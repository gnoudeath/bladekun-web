<?php
/**
 * =====================================================================
 *  FRONT CONTROLLER – cửa vào DUY NHẤT của website
 * =====================================================================
 *  Mọi yêu cầu từ trình duyệt đều đi qua file này. Thứ tự xử lý:
 *
 *   Trình duyệt ──► public/index.php ──► Router ──► Controller ──► Model (database)
 *                                                       │
 *                                                       └──► View (HTML) ──► Trình duyệt
 *
 *  Chỉ thư mục public/ cần mở ra cho người ngoài; code (app/), cấu hình (config/)
 *  nằm ngoài public/ để người ngoài không đọc trực tiếp được.
 */

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));          // thư mục gốc của project
define('APP_PATH', ROOT_PATH . '/app');         // thư mục chứa code

require APP_PATH . '/core/helpers.php';

/**
 * Tự động nạp class: khi code viết "new RankController()", PHP sẽ tìm file
 * RankController.php trong các thư mục dưới đây. Nhờ vậy không cần require từng file.
 * (Quy ước: tên file = tên class.)
 */
spl_autoload_register(function (string $class): void {
    foreach (['core', 'controllers', 'models'] as $dir) {
        $file = APP_PATH . "/$dir/$class.php";
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

date_default_timezone_set(config('app.timezone', 'Asia/Ho_Chi_Minh'));

// Header bảo mật cơ bản
header('X-Frame-Options: DENY');                         // không cho trang khác nhúng web này vào iframe
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

/**
 * Xử lý lỗi tập trung: mọi lỗi không bắt được sẽ rơi vào đây.
 * debug = true  => hiện chi tiết lỗi (để học và sửa code)
 * debug = false => chỉ hiện trang lỗi chung, không lộ thông tin nội bộ
 */
set_exception_handler(function (Throwable $e): void {
    error_log('[bladekun-web] ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    $detail = config('app.debug') ? get_class($e) . ': ' . $e->getMessage() : null;
    try {
        View::render('errors/404', [
            'title'   => 'Có lỗi xảy ra',
            'message' => 'Máy chủ gặp trục trặc. Hãy thử lại sau ít phút.',
            'detail'  => $detail,
        ]);
    } catch (Throwable $inner) {
        echo 'Có lỗi xảy ra.' . ($detail ? ' ' . e($detail) : '');
    }
});

Session::start();

// Chạy Router với bảng đường đi trong app/routes.php
(new Router(require APP_PATH . '/routes.php'))->dispatch();
