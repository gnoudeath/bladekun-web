<?php
/**
 * Database – tạo MỘT kết nối PDO dùng chung cho cả website.
 *
 * Vì sao dùng PDO?
 *  - Hỗ trợ "prepared statement": dữ liệu truyền vào được tách riêng khỏi câu SQL,
 *    nhờ đó chặn được tấn công SQL injection. KHÔNG BAO GIỜ nối chuỗi người dùng nhập vào SQL.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            // Cho phép ghi đè DSN (dùng khi kiểm thử); bình thường tự ghép từ host/port/name.
            $dsn = config('db.dsn') ?: sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                config('db.host'),
                config('db.port'),
                config('db.name')
            );

            self::$pdo = new PDO($dsn, config('db.user'), config('db.pass'), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // lỗi SQL => ném Exception (dễ bắt lỗi)
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // mỗi dòng là mảng ['cột' => giá trị]
                PDO::ATTR_EMULATE_PREPARES   => false,                    // dùng prepared statement thật của MySQL
            ]);
        }
        return self::$pdo;
    }
}
