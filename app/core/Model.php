<?php
/**
 * Model – lớp cha của mọi Model (M trong MVC).
 *
 * Nhiệm vụ của Model: nói chuyện với database và trả về dữ liệu sạch cho Controller.
 * Model KHÔNG biết gì về HTML hay về trang web đang hiển thị.
 * Lớp cha này chỉ cung cấp 3 hàm nhỏ để các Model con viết SQL ngắn gọn, an toàn.
 */
abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    /** Lấy nhiều dòng. $params là mảng giá trị gắn vào các dấu ? trong câu SQL. */
    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /** Lấy đúng một dòng (hoặc null nếu không có). */
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Lấy một giá trị đơn, ví dụ COUNT(*). */
    protected function fetchValue(string $sql, array $params = [])
    {
        return $this->run($sql, $params)->fetchColumn();
    }

    /** Chạy INSERT/UPDATE/DELETE, trả về số dòng bị ảnh hưởng. */
    protected function execute(string $sql, array $params = []): int
    {
        return $this->run($sql, $params)->rowCount();
    }

    /**
     * Chuẩn bị + chạy câu lệnh. Số nguyên được gắn đúng kiểu INT
     * (bắt buộc với mệnh đề LIMIT ?, nếu gắn thành chuỗi MySQL sẽ báo lỗi cú pháp).
     */
    private function run(string $sql, array $params): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        foreach (array_values($params) as $i => $value) {
            $stmt->bindValue($i + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt;
    }
}
