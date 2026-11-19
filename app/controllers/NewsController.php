<?php
/**
 * NewsController – Tin tức và thông báo của máy chủ.
 */
class NewsController extends Controller
{
    public function index(): void
    {
        $model   = new NewsModel();
        $perPage = max(1, (int) config('game.news_per_page', 6));
        $total   = $model->countPublished();
        $pages   = max(1, (int) ceil($total / $perPage));
        // Số trang lấy từ ?page=, ép về khoảng hợp lệ 1..$pages (nhập bậy cũng không lỗi)
        $page    = min($pages, max(1, (int) ($_GET['page'] ?? 1)));

        $this->view('news/index', [
            'title' => 'Tin tức',
            'posts' => $model->page($page, $perPage),
            'page'  => $page,
            'pages' => $pages,
            'total' => $total,
        ]);
    }

    public function show(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $model = new NewsModel();
        $post = $id > 0 ? $model->find($id) : null;

        if ($post === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy bài viết', 'message' => 'Bài viết không tồn tại hoặc chưa được đăng.']);
            return;
        }

        $model->incrementViews($id);

        $this->view('news/show', [
            'title' => $post['title'],
            'post'  => $post,
        ]);
    }
}
