<?php
/**
 * RateLimiter – giới hạn số lần thử, chống đoán mật khẩu và tạo tài khoản hàng loạt.
 *
 * Ghi số lần thử vào file nhỏ trong thư mục storage/ratelimit (không cần thêm bảng database).
 * Đây là bản đơn giản cho server nhỏ. Khi cần mạnh hơn, đổi sang Redis hoặc bảng database
 * (chỉ cần sửa file này, code ở nơi khác giữ nguyên).
 */
class RateLimiter
{
    /** Đã vượt quá $max lần trong $window giây chưa? */
    public static function tooMany(string $key, int $max, int $window): bool
    {
        return count(self::attempts($key, $window)) >= $max;
    }

    /** Ghi nhận thêm một lần thử. */
    public static function hit(string $key, int $window): void
    {
        $attempts   = self::attempts($key, $window);
        $attempts[] = time();
        self::save($key, $attempts);
    }

    /** Xoá lịch sử (vd sau khi đăng nhập đúng). */
    public static function clear(string $key): void
    {
        $file = self::file($key);
        if (is_file($file)) {
            @unlink($file);
        }
    }

    // ---- nội bộ ---------------------------------------------------------

    private static function attempts(string $key, int $window): array
    {
        $file = self::file($key);
        if (!is_file($file)) {
            return [];
        }
        $data = json_decode((string) @file_get_contents($file), true);
        if (!is_array($data)) {
            return [];
        }
        $since = time() - $window;
        return array_values(array_filter($data, fn ($t) => $t >= $since));   // bỏ các lần đã quá cũ
    }

    private static function save(string $key, array $attempts): void
    {
        $file = self::file($key);
        // Nếu không ghi được file (thiếu quyền), bỏ qua: web vẫn chạy, chỉ mất tính năng giới hạn
        @file_put_contents($file, json_encode($attempts), LOCK_EX);
    }

    private static function file(string $key): string
    {
        $dir = ROOT_PATH . '/storage/ratelimit';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir . '/' . sha1($key) . '.json';
    }
}
