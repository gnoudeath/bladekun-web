<?php
/**
 * Csrf – chống tấn công CSRF (kẻ xấu lừa bạn bấm vào một trang lạ, trang đó âm thầm gửi
 * form "đổi mật khẩu" sang web của mình bằng phiên đăng nhập của bạn).
 *
 * Cách chống: mỗi form chứa một mã bí mật ngẫu nhiên; chỉ form do chính web này tạo ra mới có mã đúng.
 * Mọi form POST đều phải có  <?= Csrf::field() ?>  và controller gọi requireCsrf().
 */
class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    /** Ô ẩn để dán vào mọi form POST. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        // hash_equals so sánh an toàn, không để lộ thông tin qua thời gian xử lý
        return is_string($token) && isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }
}
