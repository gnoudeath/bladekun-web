<?php
/**
 * Session – quản lý "phiên làm việc" của người dùng (ghi nhớ họ đã đăng nhập).
 *
 * Ngoài ra có 2 tiện ích dùng một lần rồi mất (flash):
 *   - flash message: thông báo "Đăng ký thành công!" hiện đúng một lần sau khi chuyển trang.
 *   - old input: nhớ các ô đã nhập để điền lại khi form báo lỗi.
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        session_name('BLADEKUN_SID');
        session_set_cookie_params([
            'lifetime' => 0,                 // hết hạn khi đóng trình duyệt
            'path'     => '/',
            'httponly' => true,              // JavaScript không đọc được cookie => khó bị đánh cắp phiên
            'samesite' => 'Lax',             // hạn chế gửi cookie từ trang web lạ (chống CSRF)
            'secure'   => !empty($_SERVER['HTTPS']),
        ]);
        ini_set('session.use_strict_mode', '1');   // không chấp nhận mã phiên do người dùng tự đặt
        session_start();
    }

    // ---- Thông báo một lần -------------------------------------------------

    /** $type: 'success' | 'error' | 'info' */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /** Lấy toàn bộ thông báo đang chờ rồi xoá luôn (chỉ hiện một lần). */
    public static function pullFlash(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $messages;
    }

    // ---- Nhớ lại dữ liệu form ----------------------------------------------

    /** Lưu các ô đã nhập (KHÔNG bao giờ lưu mật khẩu). */
    public static function flashOld(array $input): void
    {
        unset($input['password'], $input['password_confirm'], $input['current_password'], $input['new_password']);
        $_SESSION['_old'] = $input;
    }

    public static function old(string $key, string $default = ''): string
    {
        return (string) ($_SESSION['_old'][$key] ?? $default);
    }

    public static function clearOld(): void
    {
        unset($_SESSION['_old']);
    }
}
