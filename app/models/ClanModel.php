<?php
/**
 * ClanModel – làm việc với bảng bang hội của game: clan_sv1 (hoặc clan_sv2... theo config game.server_no).
 *
 * LƯU Ý QUAN TRỌNG: tên bảng được ghép từ SỐ SERVER ("clan_sv" . $n). Số này đến từ file cấu hình
 * (không bao giờ từ người dùng) và được ép về int, nên ghép vào câu SQL là an toàn.
 * Giá trị người dùng gửi lên (id bang, số dòng...) vẫn luôn đi qua tham số "?" như mọi Model khác.
 *
 * Danh sách thành viên: cột clan.members của game là chuỗi JSON khó đọc, nên web KHÔNG đọc nó.
 * Thay vào đó đếm/duyệt bằng player.clan_id_svN = clan.id – dễ hiểu và luôn khớp với bảng nhân vật.
 */
class ClanModel extends Model
{
    private function table(): string
    {
        return 'clan_sv' . max(1, (int) config('game.server_no', 1));
    }

    /** Tên cột trong bảng player trỏ tới bang: clan_id_sv1... */
    public static function playerColumn(): string
    {
        return 'clan_id_sv' . max(1, (int) config('game.server_no', 1));
    }

    /**
     * Bảng xếp hạng bang hội theo sức mạnh.
     * Subquery đếm thành viên: mỗi bang đếm số nhân vật có clan_id trùng id bang.
     */
    public function top(int $limit): array
    {
        return $this->fetchAll(
            'SELECT c.id, c.NAME AS name, c.slogan, c.img_id, c.power_point, c.max_member, c.LEVEL AS level, c.clan_point,
                    (SELECT COUNT(*) FROM player p WHERE p.' . self::playerColumn() . ' = c.id) AS member_count
             FROM ' . $this->table() . ' c
             ORDER BY c.power_point DESC, c.id ASC
             LIMIT ?',
            [$limit]
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT c.id, c.NAME AS name, c.slogan, c.img_id, c.power_point, c.max_member, c.LEVEL AS level, c.clan_point, c.create_time,
                    (SELECT COUNT(*) FROM player p WHERE p.' . self::playerColumn() . ' = c.id) AS member_count
             FROM ' . $this->table() . ' c WHERE c.id = ? LIMIT 1',
            [$id]
        );
    }

    /** Hạng của bang = số bang mạnh hơn + 1. */
    public function rankOf(int $powerPoint): int
    {
        return 1 + (int) $this->fetchValue('SELECT COUNT(*) FROM ' . $this->table() . ' WHERE power_point > ?', [$powerPoint]);
    }

    public function countAll(): int
    {
        return (int) $this->fetchValue('SELECT COUNT(*) FROM ' . $this->table());
    }

    /** Tên + id bang (để hiện ở trang nhân vật). Trả null nếu nhân vật không có bang (clan_id = -1). */
    public function brief(int $id): ?array
    {
        if ($id < 0) {
            return null;
        }
        return $this->fetchOne('SELECT id, NAME AS name FROM ' . $this->table() . ' WHERE id = ? LIMIT 1', [$id]);
    }

    /** Biểu tượng bang: game lưu img_id là mã ảnh trong client (web không có), nên dùng 1 trong 7 ngọc rồng theo id. */
    public static function emblem(int $clanId): string
    {
        return asset('img/balls/' . (($clanId % 7) + 1) . '.png');
    }
}
