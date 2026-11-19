<?php
/**
 * Controller – lớp cha của mọi Controller (C trong MVC).
 *
 * Controller là "người điều phối": nhận yêu cầu từ trình duyệt, hỏi Model lấy dữ liệu,
 * rồi chọn View để hiển thị. Controller KHÔNG viết SQL và KHÔNG viết HTML.
 */
abstract class Controller
{
    /** Hiển thị một view. Ví dụ: $this->view('rank/index', ['players' => $players]); */
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /** Lấy một ô trong form POST, đã cắt khoảng trắng thừa hai đầu. */
    protected function post(string $key): string
    {
        return trim((string) ($_POST[$key] ?? ''));
    }

    /** Dừng nếu form không có mã CSRF hợp lệ. Gọi ở ĐẦU mọi hàm xử lý POST. */
    protected function requireCsrf(): void
    {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            View::render('errors/404', [
                'title'   => 'Phiên làm việc hết hạn',
                'message' => 'Form đã hết hạn hoặc không hợp lệ. Hãy quay lại, tải lại trang và thử lại.',
            ]);
            exit;
        }
    }

    /** Bắt buộc đăng nhập; chưa đăng nhập thì chuyển sang trang đăng nhập. */
    protected function requireLogin(): int
    {
        $id = Auth::id();
        if ($id === null) {
            Session::flash('info', 'Bạn cần đăng nhập để vào trang này.');
            redirect('login');
        }
        return $id;
    }

    /** Dành cho trang chỉ người CHƯA đăng nhập mới cần (đăng nhập, đăng ký). */
    protected function requireGuest(): void
    {
        if (Auth::check()) {
            redirect('account');
        }
    }
}
