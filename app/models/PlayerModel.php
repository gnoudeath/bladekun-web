<?php
/**
 * PlayerModel – làm việc với bảng `player` (nhân vật trong game).
 *
 * Mỗi tài khoản có tối đa 1 nhân vật (cột player.account_id là UNIQUE).
 * Cột sức mạnh là player.power (số nguyên lớn, game tự cập nhật khi lưu nhân vật,
 * nên bảng xếp hạng cập nhật khi người chơi thoát game / game tự lưu).
 */
class PlayerModel extends Model
{
    /** Cột player.gender trong game: 0 = Trái Đất, 1 = Namếc, 2 = Xayda. */
    public const PLANETS = [0 => 'Trái Đất', 1 => 'Namếc', 2 => 'Xayda'];

    /**
     * Các loại bảng xếp hạng. KHOÁ là giá trị người dùng gửi lên (?type=...), GIÁ TRỊ là tên cột thật.
     * Chỉ cột nằm trong danh sách này mới được ghép vào SQL (whitelist) – người dùng không thể chèn cột tuỳ ý.
     */
    public const RANK_TYPES = [
        'power' => ['col' => 'p.power',      'label' => 'Sức mạnh',   'unit' => 'sức mạnh'],
        'pet'   => ['col' => 'p.pet_power',  'label' => 'Đệ tử',      'unit' => 'sức mạnh đệ tử'],
        'boss'  => ['col' => 'p.kill_boss',  'label' => 'Diệt boss',  'unit' => 'boss đã hạ'],
        'topup' => ['col' => 'p.tong_nap',   'label' => 'Top nạp',    'unit' => 'tổng nạp'],
    ];

    /** Các loại bảng đang bật (ẩn 'topup' nếu config game.rank_show_topup = false). */
    public static function rankTypes(): array
    {
        $types = self::RANK_TYPES;
        if (!config('game.rank_show_topup', true)) {
            unset($types['topup']);
        }
        return $types;
    }

    public static function planetName($gender): string
    {
        return self::PLANETS[(int) $gender] ?? '???';
    }

    /**
     * Top nhân vật theo sức mạnh.
     * JOIN với account để loại tài khoản bị khoá (ban) và (tuỳ cấu hình) tài khoản admin,
     * tránh việc nhân vật admin chiếm hết đầu bảng.
     * Thêm thứ tự phụ "p.id" để hai người bằng sức mạnh luôn xếp ổn định, không nhảy lung tung.
     */
    public function topByPower(int $limit): array
    {
        return $this->fetchAll(
            'SELECT p.id, p.name, p.power, p.gender
             FROM player p
             JOIN account a ON a.id = p.account_id
             WHERE ' . $this->visibleCondition() . '
             ORDER BY p.power DESC, p.id ASC
             LIMIT ?',
            [$limit]
        );
    }

    /**
     * Top nhân vật theo một tiêu chí bất kỳ trong RANK_TYPES ('power', 'pet', 'boss', 'topup').
     * Trả thêm cột `score` = giá trị dùng để xếp hạng, để view in ra mà không cần biết tên cột.
     */
    public function topBy(string $type, int $limit): array
    {
        $types = self::rankTypes();
        $col = ($types[$type] ?? $types['power'])['col'];   // chỉ lấy tên cột từ whitelist
        return $this->fetchAll(
            'SELECT p.id, p.name, p.power, p.gender, ' . $col . ' AS score
             FROM player p
             JOIN account a ON a.id = p.account_id
             WHERE ' . $this->visibleCondition() . '
             ORDER BY ' . $col . ' DESC, p.power DESC, p.id ASC
             LIMIT ?',
            [$limit]
        );
    }

    /**
     * Hồ sơ công khai của một nhân vật theo id (trang chi tiết). Trả null nếu không có
     * hoặc nhân vật bị ẩn (tài khoản bị khoá / admin) – giống hệt điều kiện của bảng xếp hạng.
     * Cố ý KHÔNG lấy tong_nap, thoi_vang, ruby... (thông tin riêng tư).
     */
    public function findPublic(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT p.id, p.name, p.power, p.gender, p.head, p.pet_power, p.kill_boss, p.create_time, p.' . ClanModel::playerColumn() . ' AS clan_id
             FROM player p JOIN account a ON a.id = p.account_id
             WHERE ' . $this->visibleCondition() . ' AND p.id = ? LIMIT 1',
            [$id]
        );
    }

    /** Các thành viên của một bang, mạnh nhất trước. */
    public function byClan(int $clanId, int $limit = 50): array
    {
        return $this->fetchAll(
            'SELECT p.id, p.name, p.power, p.gender
             FROM player p JOIN account a ON a.id = p.account_id
             WHERE ' . $this->visibleCondition() . ' AND p.' . ClanModel::playerColumn() . ' = ?
             ORDER BY p.power DESC, p.id ASC LIMIT ?',
            [$clanId, $limit]
        );
    }

    /** Số nhân vật theo từng hành tinh: [0 => n, 1 => n, 2 => n] (dùng cho thẻ hành tinh ở trang chủ). */
    public function countByPlanet(): array
    {
        $out = [0 => 0, 1 => 0, 2 => 0];
        foreach ($this->fetchAll('SELECT gender, COUNT(*) AS n FROM player GROUP BY gender') as $row) {
            $out[(int) $row['gender']] = (int) $row['n'];
        }
        return $out;
    }

    /** Nhân vật của một tài khoản (hoặc null nếu chưa tạo nhân vật trong game). */
    public function findByAccountId(int $accountId): ?array
    {
        return $this->fetchOne(
            'SELECT id, name, power, gender, pet_power, head FROM player WHERE account_id = ? LIMIT 1',
            [$accountId]
        );
    }

    /** Hạng của một mức sức mạnh = số người mạnh hơn + 1 (cùng điều kiện lọc với bảng xếp hạng). */
    public function rankOf(int $power): int
    {
        return 1 + (int) $this->fetchValue(
            'SELECT COUNT(*) FROM player p JOIN account a ON a.id = p.account_id
             WHERE ' . $this->visibleCondition() . ' AND p.power > ?',
            [$power]
        );
    }

    public function countAll(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM player');
    }

    /** Điều kiện "nhân vật nào được phép hiện trên bảng xếp hạng". Gom một chỗ để mọi hàm dùng chung. */
    private function visibleCondition(): string
    {
        $sql = 'a.ban = 0';
        if (config('game.rank_hide_admins')) {
            $sql .= ' AND a.is_admin = 0';
        }
        return $sql;
    }
}
