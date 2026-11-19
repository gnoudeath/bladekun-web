<?php
/**
 * Auth – trả lời câu hỏi "ai đang đăng nhập?".
 *
 * Trong session chỉ lưu id tài khoản. Thông tin khác (vàng, ruby...) luôn đọc mới từ
 * database mỗi lần cần, nên số liệu luôn đúng và không bị "cũ" khi người chơi vừa nhận thưởng trong game.
 */
class Auth
{
    public static function login(int $accountId): void
    {
        // Đổi mã phiên ngay khi đăng nhập để chặn tấn công "session fixation"
        session_regenerate_id(true);
        $_SESSION['account_id'] = $accountId;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION['account_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['account_id']) ? (int) $_SESSION['account_id'] : null;
    }
}
