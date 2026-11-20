<?php
/**
 * ForumModel – bảng forum_posts / forum_comments / forum_likes (tạo bằng database/forum.sql).
 * Thông tin người đăng lấy từ account (tên đăng nhập, quyền admin) và player (tên nhân vật, đầu để làm avatar).
 */
class ForumModel extends Model
{
    public const FILTERS = ['all' => 'Tất cả bài đăng', 'mine' => 'Bài của tôi', 'admin' => 'Bài của Admin'];

    /** Các bảng diễn đàn đã được tạo chưa? (để báo hướng dẫn thân thiện thay vì lỗi SQL) */
    public function ready(): bool
    {
        try { $this->fetchValue('SELECT COUNT(*) FROM forum_posts'); return true; }
        catch (PDOException $e) { return false; }
    }

    /** Phần SELECT/JOIN dùng chung: kèm người đăng và "tôi đã thích chưa". $viewer = id tài khoản đang xem (0 nếu khách). */
    private const AUTHOR_SQL = 'a.username, a.is_admin, pl.name AS char_name, pl.head AS char_head, pl.gender AS char_gender';
    private const AUTHOR_JOIN = 'JOIN account a ON a.id = %s.account_id LEFT JOIN player pl ON pl.account_id = %s.account_id';

    /** Điều kiện lọc theo tab. Trả [sql, params]. */
    private function filterWhere(string $filter, int $viewer): array
    {
        if ($filter === 'mine')  { return ['p.account_id = ?', [$viewer]]; }
        if ($filter === 'admin') { return ['a.is_admin = 1', []]; }
        return ['1 = 1', []];
    }

    /** Một trang bài đăng: bài ghim trước, sau đó mới nhất trước. */
    public function page(string $filter, int $viewer, int $page, int $perPage): array
    {
        [$where, $params] = $this->filterWhere($filter, $viewer);
        $params = array_merge([$viewer], $params, [$perPage, max(0, ($page - 1) * $perPage)]);
        return $this->fetchAll(
            'SELECT p.*, ' . self::AUTHOR_SQL . ',
                    (SELECT COUNT(*) FROM forum_likes l WHERE l.post_id = p.id AND l.account_id = ?) AS liked
             FROM forum_posts p ' . sprintf(self::AUTHOR_JOIN, 'p', 'p') . '
             WHERE ' . $where . '
             ORDER BY p.pinned DESC, p.created_at DESC, p.id DESC
             LIMIT ? OFFSET ?',
            $params
        );
    }

    public function count(string $filter, int $viewer): int
    {
        [$where, $params] = $this->filterWhere($filter, $viewer);
        return (int) $this->fetchValue(
            'SELECT COUNT(*) FROM forum_posts p ' . sprintf(self::AUTHOR_JOIN, 'p', 'p') . ' WHERE ' . $where,
            $params
        );
    }

    public function find(int $id, int $viewer): ?array
    {
        return $this->fetchOne(
            'SELECT p.*, ' . self::AUTHOR_SQL . ',
                    (SELECT COUNT(*) FROM forum_likes l WHERE l.post_id = p.id AND l.account_id = ?) AS liked
             FROM forum_posts p ' . sprintf(self::AUTHOR_JOIN, 'p', 'p') . ' WHERE p.id = ? LIMIT 1',
            [$viewer, $id]
        );
    }

    /** Bình luận của một bài, cũ trước (đọc từ trên xuống như hội thoại). */
    public function comments(int $postId): array
    {
        return $this->fetchAll(
            'SELECT c.*, ' . self::AUTHOR_SQL . '
             FROM forum_comments c ' . sprintf(self::AUTHOR_JOIN, 'c', 'c') . '
             WHERE c.post_id = ? ORDER BY c.created_at ASC, c.id ASC LIMIT 300',
            [$postId]
        );
    }

    public function findComment(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM forum_comments WHERE id = ?', [$id]);
    }

    /** Thời điểm (unix) người này gửi bài/bình luận gần nhất – dùng cho giới hạn thời gian chờ (riêng bài viết / bình luận). 0 nếu chưa từng. */
    /** Thời điểm (timestamp) lần gửi gần nhất của tài khoản, riêng cho bài viết hoặc bình luận. */
    public function lastActivity(int $accountId, bool $isPost): int
    {
        $table = $isPost ? 'forum_posts' : 'forum_comments';   // chỉ 2 giá trị cố định, không lấy từ người dùng
        $t = $this->fetchValue('SELECT MAX(created_at) FROM ' . $table . ' WHERE account_id = ?', [$accountId]);
        return $t ? (int) strtotime($t) : 0;
    }

    public function createPost(int $accountId, ?string $title, string $content, ?array $media): int
    {
        $this->execute(
            'INSERT INTO forum_posts (account_id, title, content, media_path, media_type, created_at) VALUES (?,?,?,?,?,?)',
            [$accountId, $title, $content, $media['path'] ?? null, $media['type'] ?? null, date('Y-m-d H:i:s')]
        );
        return (int) $this->db->lastInsertId();
    }

    public function createComment(int $postId, int $accountId, string $content, ?array $media): void
    {
        $this->execute(
            'INSERT INTO forum_comments (post_id, account_id, content, media_path, media_type, created_at) VALUES (?,?,?,?,?,?)',
            [$postId, $accountId, $content, $media['path'] ?? null, $media['type'] ?? null, date('Y-m-d H:i:s')]
        );
        $this->recount($postId);
    }

    /** Bấm "Thích": chưa thích => thêm; đã thích => bỏ. */
    public function toggleLike(int $postId, int $accountId): void
    {
        $has = (int) $this->fetchValue('SELECT COUNT(*) FROM forum_likes WHERE post_id = ? AND account_id = ?', [$postId, $accountId]);
        if ($has) {
            $this->execute('DELETE FROM forum_likes WHERE post_id = ? AND account_id = ?', [$postId, $accountId]);
        } else {
            $this->execute('INSERT INTO forum_likes (post_id, account_id) VALUES (?,?)', [$postId, $accountId]);
        }
        $this->recount($postId);
    }

    public function togglePin(int $postId): void
    {
        $this->execute('UPDATE forum_posts SET pinned = 1 - pinned WHERE id = ?', [$postId]);
    }

    /** Xoá bài kèm bình luận, lượt thích và file đính kèm. */
    public function deletePost(array $post): void
    {
        foreach ($this->fetchAll('SELECT media_path FROM forum_comments WHERE post_id = ?', [(int) $post['id']]) as $c) {
            Upload::delete($c['media_path']);
        }
        Upload::delete($post['media_path']);
        $this->execute('DELETE FROM forum_comments WHERE post_id = ?', [(int) $post['id']]);
        $this->execute('DELETE FROM forum_likes WHERE post_id = ?', [(int) $post['id']]);
        $this->execute('DELETE FROM forum_posts WHERE id = ?', [(int) $post['id']]);
    }

    public function deleteComment(array $comment): void
    {
        Upload::delete($comment['media_path']);
        $this->execute('DELETE FROM forum_comments WHERE id = ?', [(int) $comment['id']]);
        $this->recount((int) $comment['post_id']);
    }

    /** Tính lại số thích / số bình luận từ dữ liệu thật (tránh lệch số khi có lỗi giữa chừng). */
    private function recount(int $postId): void
    {
        $this->execute(
            'UPDATE forum_posts SET
               like_count    = (SELECT COUNT(*) FROM forum_likes WHERE post_id = ?),
               comment_count = (SELECT COUNT(*) FROM forum_comments WHERE post_id = ?)
             WHERE id = ?',
            [$postId, $postId, $postId]
        );
    }
}
