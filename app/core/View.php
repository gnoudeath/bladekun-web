<?php
/**
 * View – ghép dữ liệu vào file giao diện (V trong MVC).
 *
 * Cách hoạt động:
 *   1. Chạy file view của trang (vd views/rank/index.php) và "bắt" kết quả HTML vào biến $content.
 *   2. Đưa $content vào khung chung views/layouts/main.php (có menu, chân trang...).
 * Nhờ vậy mỗi trang chỉ cần viết phần nội dung riêng, khung chung sửa một lần là đủ.
 */
class View
{
    /**
     * @param string $view   tên view, ví dụ 'rank/index'  =>  app/views/rank/index.php
     * @param array  $data   biến truyền cho view, ví dụ ['players' => [...]]
     */
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $content = self::capture($view, $data);
        echo self::capture($layout, $data + ['content' => $content]);
    }

    /** Chạy một file view và trả về HTML dạng chuỗi thay vì in ngay ra màn hình. */
    private static function capture(string $view, array $data): string
    {
        extract($data, EXTR_SKIP);      // biến mảng ['players' => ...] thành biến $players trong view
        ob_start();
        require APP_PATH . '/views/' . $view . '.php';
        return (string) ob_get_clean();
    }

    /** Chèn một mảnh giao diện dùng chung (partial), ví dụ thanh thông báo. */
    public static function partial(string $name, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require APP_PATH . '/views/partials/' . $name . '.php';
    }
}
