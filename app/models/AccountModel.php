<?php
/**
 * AccountModel – làm việc với bảng `account` (tài khoản đăng nhập game).
 *
 * ⚠ VỀ MẬT KHẨU (rất quan trọng, đọc kỹ):
 *   Game server kiểm tra đăng nhập bằng câu SQL  "... WHERE username = ? AND password = ?",
 *   tức là game đang lưu và so sánh mật khẩu DẠNG CHỮ THƯỜNG (không mã hoá).
 *   Vì web và game dùng chung bảng account, web BẮT BUỘC lưu cùng kiểu thì game mới đăng nhập được.
 *   Đây là điểm yếu bảo mật của game gốc, không phải của web.
 *   Mọi chỗ động tới mật khẩu đều gom về 2 hàm storedPassword() và verifyPassword() bên dưới,
 *   để sau này nâng cấp (mã hoá bằng password_hash) chỉ phải sửa đúng 2 hàm này + code game.
 */
class AccountModel extends Model
{
    /** Giá trị mặc định của last_time_login trong database = "chưa từng đăng nhập". */
    public const NEVER_LOGGED_IN = '2002-05-07 00:00:00';

    // -------- Mật khẩu: nơi DUY NHẤT cần sửa khi nâng cấp bảo mật ----------------

    /** Biến mật khẩu người dùng gõ thành giá trị lưu vào cột account.password. */
    public static function storedPassword(string $plain): string
    {
        return $plain;      // hiện tại: lưu thẳng (để khớp với game). Tương lai: password_hash($plain, PASSWORD_DEFAULT)
    }

    /** Mật khẩu người dùng gõ có khớp với giá trị đang lưu không? */
    public static function verifyPassword(string $plain, string $stored): bool
    {
        return hash_equals($stored, self::storedPassword($plain));   // tương lai: password_verify($plain, $stored)
    }

    // -------- Tìm kiếm ---------------------------------------------------------------

    /**
     * Lấy tài khoản theo tên đăng nhập (kèm mật khẩu, CHỈ dùng nội bộ để kiểm tra đăng nhập).
     * Lưu ý: MySQL so sánh tên không phân biệt hoa/thường, nên "Goku" và "goku" là cùng một tài khoản.
     */
    public function findForLogin(string $username): ?array
    {
        return $this->fetchOne(
            'SELECT id, username, password, ban FROM account WHERE username = ? LIMIT 1',
            [$username]
        );
    }

    /** Thông tin tài khoản để hiển thị. Cố ý KHÔNG lấy cột password. */
    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, username, email, create_time, last_time_login, ban, active, is_admin,
                    thoi_vang, ruby, vnd, tongnap
             FROM account WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    public function usernameExists(string $username): bool
    {
        return (bool) $this->fetchValue('SELECT COUNT(*) FROM account WHERE username = ?', [$username]);
    }

    public function countAll(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM account');
    }

    // -------- Thêm / sửa -------------------------------------------------------------

    /**
     * Tạo tài khoản mới, trả về id.
     * Chỉ điền 6 cột cần thiết; các cột còn lại (vàng, ruby, thời gian...) dùng giá trị
     * mặc định đã khai báo sẵn trong database, đúng như game mong đợi.
     * Nếu username bị trùng, database (khoá UNIQUE) sẽ ném lỗi => controller bắt và báo người dùng.
     */
    public function create(string $username, string $plainPassword, string $email, string $ip, bool $active): int
    {
        $this->execute(
            'INSERT INTO account (username, password, email, ip, ip_address, active) VALUES (?, ?, ?, ?, ?, ?)',
            [$username, self::storedPassword($plainPassword), $email, $ip, $ip, $active ? 1 : 0]
        );
        return (int) $this->db->lastInsertId();
    }

    public function updatePassword(int $id, string $newPlainPassword): void
    {
        $this->execute(
            'UPDATE account SET password = ? WHERE id = ?',
            [self::storedPassword($newPlainPassword), $id]
        );
    }

    /** Lấy mật khẩu hiện lưu của một tài khoản (chỉ để kiểm tra "mật khẩu cũ" khi đổi mật khẩu). */
    public function storedPasswordOf(int $id): ?string
    {
        $value = $this->fetchValue('SELECT password FROM account WHERE id = ?', [$id]);
        return $value === false ? null : (string) $value;
    }
}
