<?php
/**
 * NewsModel – làm việc với bảng `news_posts` (tin tức / thông báo).
 *
 * Hiện web chỉ ĐỌC tin. Việc viết tin mới hiện làm trực tiếp trong database (Navicat);
 * trang quản trị để đăng tin nằm trong lộ trình phát triển (xem README).
 */
class NewsModel extends Model
{
    /** Danh sách tin đã đăng, mới nhất trước. */
    public function latest(int $limit): array
    {
        return $this->fetchAll(
            'SELECT id, title, short_content, views, created_at
             FROM news_posts
             WHERE status = ?
             ORDER BY created_at DESC, id DESC
             LIMIT ?',
            [(int) config('game.news_published_status', 1), $limit]
        );
    }

    /**
     * Một trang tin (phân trang). Công thức: bỏ qua (trang-1)*số_bài_mỗi_trang bài đầu rồi lấy tiếp N bài:
     *   LIMIT <số bài mỗi trang> OFFSET <số bài bỏ qua>
     */
    public function page(int $page, int $perPage): array
    {
        return $this->fetchAll(
            'SELECT id, title, short_content, views, created_at
             FROM news_posts
             WHERE status = ?
             ORDER BY created_at DESC, id DESC
             LIMIT ? OFFSET ?',
            [(int) config('game.news_published_status', 1), $perPage, max(0, ($page - 1) * $perPage)]
        );
    }

    /** Tổng số bài đã đăng (để biết có bao nhiêu trang). */
    public function countPublished(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM news_posts WHERE status = ?', [(int) config('game.news_published_status', 1)]);
    }

    /** Một bài tin đầy đủ (chỉ bài đã đăng). */
    public function find(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, title, short_content, content, views, created_at
             FROM news_posts WHERE id = ? AND status = ? LIMIT 1',
            [$id, (int) config('game.news_published_status', 1)]
        );
    }

    public function incrementViews(int $id): void
    {
        $this->execute('UPDATE news_posts SET views = views + 1 WHERE id = ?', [$id]);
    }
}
