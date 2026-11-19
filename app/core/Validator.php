<?php
/**
 * Validator – kiểm tra dữ liệu người dùng nhập.
 *
 * Mỗi hàm trả về  null  nếu hợp lệ, hoặc  một câu báo lỗi tiếng Việt  nếu sai.
 * Luôn kiểm tra ở phía server (kể cả khi form HTML đã có kiểm tra), vì người dùng
 * có thể bỏ qua trình duyệt và gửi dữ liệu trực tiếp.
 */
class Validator
{
    /** Tên đăng nhập: chữ thường, số, gạch dưới; độ dài theo cấu hình (cột database là varchar(20)). */
    public static function username(string $value): ?string
    {
        $min = (int) config('game.username_min');
        $max = (int) config('game.username_max');
        if (strlen($value) < $min || strlen($value) > $max) {
            return "Tên đăng nhập phải dài từ $min đến $max ký tự.";
        }
        if (!preg_match('/^[a-z0-9_]+$/', $value)) {
            return 'Tên đăng nhập chỉ gồm chữ thường (a-z), số (0-9) và dấu gạch dưới (_).';
        }
        return null;
    }

    /**
     * Mật khẩu: chỉ ký tự ASCII hiển thị được, không dấu cách.
     * Lý do: ô nhập của client game thường không gõ được tiếng Việt có dấu hay ký tự lạ,
     * nếu cho phép thì người chơi tạo tài khoản trên web xong lại không đăng nhập nổi vào game.
     */
    public static function password(string $value): ?string
    {
        $min = (int) config('game.password_min');
        $max = (int) config('game.password_max');
        if (strlen($value) < $min || strlen($value) > $max) {
            return "Mật khẩu phải dài từ $min đến $max ký tự.";
        }
        if (!preg_match('/^[\x21-\x7E]+$/', $value)) {
            return 'Mật khẩu chỉ gồm chữ, số, ký hiệu thường dùng; không dùng dấu cách hay chữ có dấu.';
        }
        return null;
    }

    public static function email(string $value): ?string
    {
        if ($value === '' || strlen($value) > 255 || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'Email không hợp lệ.';
        }
        return null;
    }
}
