<?php
/**
 * Router – "người chỉ đường": nhìn địa chỉ người dùng mở, quyết định Controller nào xử lý.
 *
 * Bảng đường đi nằm ở app/routes.php, dạng:
 *     'GET /rank' => ['RankController', 'index']
 * nghĩa là: ai mở trang /rank bằng GET thì gọi RankController->index().
 */
class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Đường dẫn trang hiện tại, luôn bắt đầu bằng "/", ví dụ "/rank" hoặc "/" (trang chủ).
     * Hỗ trợ 2 kiểu địa chỉ:
     *   - Mặc định:     index.php?r=rank   (chạy được ngay, không cần cấu hình Apache)
     *   - URL đẹp:      /rank              (khi bật app.pretty_urls + mod_rewrite)
     */
    public static function currentPath(): string
    {
        if (isset($_GET['r'])) {
            return '/' . trim((string) $_GET['r'], '/');
        }
        $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = base_url();
        if ($base !== '' && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        $uri = trim($uri, '/');
        if ($uri === 'index.php') {
            $uri = '';
        }
        return '/' . $uri;
    }

    /** Tìm route khớp và chạy Controller tương ứng. */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path   = self::currentPath();
        $key    = $method . ' ' . $path;

        if (isset($this->routes[$key])) {
            [$class, $action] = $this->routes[$key];
            (new $class())->$action();      // vd: (new RankController())->index()
            return;
        }

        // Có đường dẫn này nhưng sai phương thức (vd mở bằng GET trang chỉ nhận POST)
        foreach (['GET', 'POST'] as $other) {
            if (isset($this->routes[$other . ' ' . $path])) {
                http_response_code(405);
                View::render('errors/404', ['title' => 'Sai phương thức', 'message' => 'Trang này không nhận kiểu truy cập đó.']);
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Không tìm thấy trang']);
    }
}
