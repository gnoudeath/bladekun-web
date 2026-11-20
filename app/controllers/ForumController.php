<?php
/**
 * ForumController – Diễn đàn: bài đăng, bình luận, thích, ghim, xoá.
 *
 * Quy tắc:
 *  - Ai cũng XEM được. Đăng bài / bình luận / thích cần đăng nhập, tài khoản không bị khoá.
 *  - Chờ giữa hai lần gửi: bài viết forum.post_cooldown_seconds (mặc định 300 = 5 phút), bình luận forum.comment_cooldown_seconds (mặc định 15 giây).
 *    Kiểm tra ở SERVER bằng thời điểm gửi gần nhất trong database => đổi trình duyệt/F5 cũng không né được.
 *  - Đính kèm 1 ảnh hoặc video ≤ forum.max_upload_mb MB (xem Upload.php).
 *  - Chỉ chủ bài hoặc admin được xoá; chỉ admin được ghim.
 *  - Mọi hành động POST đều kiểm tra mã CSRF.
 */
class ForumController extends Controller
{
    private ForumModel $forum;

    public function __construct()
    {
        $this->forum = new ForumModel();
    }

    /** Trang chủ diễn đàn: tab lọc + form đăng bài + danh sách + phân trang. */
    public function index(): void
    {
        if (!$this->forum->ready()) { $this->notReady(); return; }

        $viewer = Auth::id() ?? 0;
        $filter = $_GET['filter'] ?? 'all';
        if (!isset(ForumModel::FILTERS[$filter])) { $filter = 'all'; }
        if ($filter === 'mine' && !$viewer) { $this->requireLogin(); }

        $per   = (int) config('forum.per_page', 10);
        $total = $this->forum->count($filter, $viewer);
        $pages = max(1, (int) ceil($total / $per));
        $page  = min(max(1, (int) ($_GET['page'] ?? 1)), $pages);

        $this->view('forum/index', [
            'title'   => 'Diễn đàn',
            'posts'   => $this->forum->page($filter, $viewer, $page, $per),
            'filter'  => $filter,
            'page'    => $page,
            'pages'   => $pages,
            'total'   => $total,
            'me'      => $this->me(),
            'wait'    => $this->waitSeconds($viewer, true),    // ô đăng bài
        ]);
    }

    /** Một bài + toàn bộ bình luận. */
    public function show(): void
    {
        if (!$this->forum->ready()) { $this->notReady(); return; }

        $viewer = Auth::id() ?? 0;
        $post = $this->forum->find((int) ($_GET['id'] ?? 0), $viewer);
        if ($post === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy bài viết', 'message' => 'Bài viết không tồn tại hoặc đã bị xoá.']);
            return;
        }
        $this->view('forum/show', [
            'title'    => $post['title'] ?: 'Bài viết của ' . ($post['char_name'] ?: $post['username']),
            'post'     => $post,
            'comments' => $this->forum->comments((int) $post['id']),
            'me'       => $this->me(),
            'wait'     => $this->waitSeconds($viewer, false),   // ô bình luận
        ]);
    }

    /** Đăng bài mới. */
    public function create(): void
    {
        $accountId = $this->requireLogin();
        $this->requireCsrf();
        $me = $this->requireCanPost($accountId);

        $title   = mb_substr($this->post('title'), 0, (int) config('forum.max_title', 100));
        $content = $this->post('content');
        [$media, $error] = $this->checkSubmission($accountId, $content, true);
        if ($error) { Session::flash('error', $error); redirect('forum'); }

        $id = $this->forum->createPost($accountId, $title !== '' ? $title : null, $content, $media);
        Session::flash('success', 'Đã đăng bài!');
        redirect('forum/post', ['id' => $id]);
    }

    /** Bình luận vào một bài. */
    public function comment(): void
    {
        $accountId = $this->requireLogin();
        $this->requireCsrf();
        $this->requireCanPost($accountId);

        $postId = (int) ($_POST['post_id'] ?? 0);
        if ($this->forum->find($postId, 0) === null) { redirect('forum'); }

        [$media, $error] = $this->checkSubmission($accountId, $this->post('content'), false);
        if ($error) { Session::flash('error', $error); redirect('forum/post', ['id' => $postId]); }

        $this->forum->createComment($postId, $accountId, $this->post('content'), $media);
        redirect('forum/post', ['id' => $postId]);
    }

    /** Thích / bỏ thích. */
    public function like(): void
    {
        $accountId = $this->requireLogin();
        $this->requireCsrf();
        $this->requireCanPost($accountId);
        $postId = (int) ($_POST['post_id'] ?? 0);
        if ($this->forum->find($postId, 0) !== null) { $this->forum->toggleLike($postId, $accountId); }
        $this->back($postId);
    }

    /** Xoá bài hoặc bình luận (chủ nhân hoặc admin). */
    public function delete(): void
    {
        $accountId = $this->requireLogin();
        $this->requireCsrf();
        $me = $this->me();

        if (($_POST['kind'] ?? '') === 'comment') {
            $c = $this->forum->findComment((int) ($_POST['id'] ?? 0));
            if ($c && ((int) $c['account_id'] === $accountId || !empty($me['is_admin']))) {
                $this->forum->deleteComment($c);
                Session::flash('success', 'Đã xoá bình luận.');
            }
            redirect('forum/post', ['id' => (int) ($c['post_id'] ?? 0)]);
        }

        $p = $this->forum->find((int) ($_POST['id'] ?? 0), 0);
        if ($p && ((int) $p['account_id'] === $accountId || !empty($me['is_admin']))) {
            $this->forum->deletePost($p);
            Session::flash('success', 'Đã xoá bài viết.');
        }
        redirect('forum');
    }

    /** Ghim / bỏ ghim (chỉ admin). */
    public function pin(): void
    {
        $this->requireLogin();
        $this->requireCsrf();
        $me = $this->me();
        $postId = (int) ($_POST['post_id'] ?? 0);
        if (!empty($me['is_admin']) && $this->forum->find($postId, 0) !== null) { $this->forum->togglePin($postId); }
        $this->back($postId);
    }

    // ---- nội bộ ---------------------------------------------------------

    /** Tài khoản đang đăng nhập (hoặc null). */
    private function me(): ?array
    {
        $id = Auth::id();
        return $id ? (new AccountModel())->findById($id) : null;
    }

    /** Tài khoản bị khoá thì không được tương tác. */
    private function requireCanPost(int $accountId): array
    {
        $me = (new AccountModel())->findById($accountId);
        if ($me === null || (int) $me['ban'] !== 0) {
            Session::flash('error', 'Tài khoản của bạn không thể đăng bài.');
            redirect('forum');
        }
        return $me;
    }

    /**
     * Kiểm tra nội dung + giới hạn thời gian + xử lý file đính kèm. Trả [media|null, lỗi|null].
     * Thứ tự: kiểm tra rẻ trước (nội dung, thời gian), lưu file sau cùng để không để lại file rác khi bị từ chối.
     */
    private function checkSubmission(int $accountId, string $content, bool $isPost): array
    {
        $max = (int) config('forum.max_content', 2000);
        $hasFile = isset($_FILES['media']) && ($_FILES['media']['error'] ?? 4) !== UPLOAD_ERR_NO_FILE;
        if ($content === '' && !$hasFile) { return [null, 'Hãy nhập nội dung hoặc đính kèm ảnh/video.']; }
        if (mb_strlen($content) > $max)   { return [null, "Nội dung tối đa $max ký tự."]; }

        $wait = $this->waitSeconds($accountId, $isPost);
        if ($wait > 0) { return [null, 'Bạn gửi quá nhanh. Vui lòng chờ thêm ' . self::formatWait($wait) . ' nữa.']; }

        return Upload::handle('media');
    }

    /** Còn phải chờ bao nhiêu giây nữa mới được gửi tiếp (0 = gửi được ngay). View dùng số này để đếm ngược. */
    private function waitSeconds(int $accountId, bool $isPost): int
    {
        if ($accountId <= 0) { return 0; }
        // Bài và bình luận có thời gian chờ RIÊNG, tính từ lần gửi gần nhất CÙNG LOẠI.
        $cooldown = (int) config($isPost ? 'forum.post_cooldown_seconds' : 'forum.comment_cooldown_seconds', $isPost ? 300 : 15);
        return max(0, $cooldown - (time() - $this->forum->lastActivity($accountId, $isPost)));
    }

    /** 125 => "2 phút 5 giây", 15 => "15 giây". */
    private static function formatWait(int $s): string
    {
        return $s >= 60 ? intdiv($s, 60) . ' phút' . ($s % 60 ? ' ' . ($s % 60) . ' giây' : '') : $s . ' giây';
    }

    private function back(int $postId): void
    {
        if (($_POST['from'] ?? '') === 'list') {
            redirect('forum', array_filter(['filter' => $_POST['filter'] ?? null, 'page' => $_POST['page'] ?? null]));
        }
        redirect('forum/post', ['id' => $postId]);
    }

    private function notReady(): void
    {
        $this->view('forum/not_ready', ['title' => 'Diễn đàn']);
    }
}
