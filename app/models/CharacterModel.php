<?php
/**
 * CharacterModel – dựng "diện mạo" + danh sách trang bị của một nhân vật từ cột player.items_body.
 *
 * ── Cách game lưu trang bị ─────────────────────────────────────────────────────────
 * player.items_body là một chuỗi JSON, mỗi phần tử là một ô trang bị:
 *     {"temp_id": 1641, "quantity": 1, "create_time": 0, "option": [[47, 1500], [6, 20000], ...]}
 *   - temp_id  : id trong bảng item_template (tên, icon, loại, "part" = bộ phận hình ảnh khi mặc)
 *   - option   : danh sách [id_chỉ_số, giá_trị]; tên chỉ số nằm ở bảng item_option_template
 *                (ví dụ 'Tấn công+#' -> thay dấu # bằng giá trị)
 *   - ô trống  : temp_id = -1 (hoặc quantity = 0)
 *
 * ── Cách ghép hình nhân vật (giống Player.getHead/getBody/getLeg của game) ────────
 *   đầu  = đầu của cải trang (bảng cai_trang) nếu đang mặc cải trang, ngược lại là cột player.head
 *   thân = thân của cải trang, ngược lại item_template.part của món "Áo" (type 0), ngược lại bộ mặc định
 *   chân = chân của cải trang, ngược lại item_template.part của món "Quần" (type 1), ngược lại bộ mặc định
 * Mỗi "part" là một id; ảnh được cắt sẵn bằng tools/build_sprites.py và liệt kê trong public/assets/data/parts.json.
 * Part nào chưa có ảnh trong file game thì lùi về bộ đồ cơ bản của hành tinh (và đánh dấu approx = true).
 */
class CharacterModel extends Model
{
    /** Nhãn theo item_template.TYPE (chỉ liệt kê các loại hay gặp trong ô trang bị). */
    private const TYPE_LABELS = [
        0 => 'Áo', 1 => 'Quần', 2 => 'Găng', 3 => 'Giày', 4 => 'Rada',
        5 => 'Cải trang', 11 => 'Cờ / Ba lô', 23 => 'Thú cưỡi', 24 => 'Thú cưỡi',
        27 => 'Phụ kiện', 32 => 'Phụ kiện',
    ];

    /** Bộ đồ cơ bản theo hành tinh (đã có ảnh trong file game): [đầu, thân, chân]. */
    private const FALLBACK = [0 => [64, 14, 15], 1 => [9, 10, 11], 2 => [6, 16, 17]];

    /** Kích thước khung hiển thị (px) – CSS .char-stage phải khớp. */
    public const STAGE_W = 240;
    public const STAGE_H = 250;
    // ===== CHỈNH VỊ TRÍ CẢ NHÂN VẬT TRONG KHUNG: chỉ cần sửa 2 số này (đầu/thân/chân dịch chuyển cùng nhau) =====
    /** Dịch ngang: số CÀNG LỚN nhân vật càng sang TRÁI; càng nhỏ càng sang PHẢI (px). */
    private const ANCHOR_X = 46;
    /** Khoảng cách từ chân nhân vật tới đáy khung: số CÀNG LỚN nhân vật càng LÊN CAO (px). */
    private const BOTTOM_GAP = 18;
    /** Độ cao đầu so với thân (px): số CÀNG LỚN đầu càng LÊN CAO, càng nhỏ đầu càng lún xuống thân. */
    private const HEAD_RISE = 73;   // 73 = giữ đúng độ cao bạn đã chỉnh tay (đầu chuẩn như cũ)

    private static ?array $partsMeta = null;

    /**
     * @return array{layers: array, approx: bool, equipment: array}
     *   layers    : danh sách ảnh xếp chồng [['src','left','top','w','h','z']]
     *   approx    : true nếu có bộ phận phải thay bằng bản cơ bản vì thiếu ảnh
     *   equipment : danh sách trang bị [['name','icon','type_label','options'=>[string...]]]
     */
    public function forPlayer(int $playerId, int $gender, int $head): array
    {
        $row = $this->fetchOne('SELECT items_body FROM player WHERE id = ? LIMIT 1', [$playerId]);
        $slots = self::parseItems($row['items_body'] ?? '');
        $templates = $this->templates(array_column($slots, 'temp_id'));
        $optionNames = $this->optionNames();

        $equipment = [];
        $bodyPart = $legPart = null;
        $costume = null;
        foreach ($slots as $s) {
            $t = $templates[$s['temp_id']] ?? null;
            if ($t === null) {
                continue;   // id không còn trong item_template => bỏ qua cho an toàn
            }
            $type = (int) $t['type'];
            if ($type === 0) { $bodyPart = (int) $t['part']; }
            if ($type === 1) { $legPart  = (int) $t['part']; }
            if ($type === 5) { $costume  = $this->costume((int) $s['temp_id']); }

            $opts = [];
            foreach ($s['option'] as $o) {
                $name = $optionNames[(int) $o[0]] ?? null;
                if ($name !== null) {
                    $opts[] = str_replace('#', number_format((int) $o[1], 0, ',', '.'), $name);
                }
            }
            $equipment[] = [
                'name'       => $t['name'],
                'icon'       => (int) $t['icon_id'],
                'type'       => $type,
                'type_label' => self::TYPE_LABELS[$type] ?? 'Trang bị',
                'options'    => $opts,
            ];
        }

        // Cải trang ghi đè từng bộ phận (giá trị -1 = giữ nguyên bộ phận thường)
        $headId = $head;
        if ($costume !== null) {
            if ($costume['head'] >= 0) { $headId = $costume['head']; }
            if ($costume['body'] >= 0) { $bodyPart = $costume['body']; }
            if ($costume['leg']  >= 0) { $legPart  = $costume['leg']; }
        }

        [$layers, $approx] = $this->compose($gender, $headId, $bodyPart, $legPart);
        return ['layers' => $layers, 'approx' => $approx, 'equipment' => $equipment];
    }

    /** Đọc chuỗi JSON items_body thành mảng ô còn trang bị. Dữ liệu hỏng => mảng rỗng (không làm sập trang). */
    public static function parseItems(?string $json): array
    {
        $data = json_decode((string) $json, true);
        if (!is_array($data)) {
            return [];
        }
        $out = [];
        foreach ($data as $it) {
            if (!is_array($it)) { continue; }
            $id = (int) ($it['temp_id'] ?? -1);
            if ($id < 0 || (int) ($it['quantity'] ?? 1) <= 0) { continue; }   // ô trống
            $opts = [];
            foreach ((array) ($it['option'] ?? []) as $o) {
                if (is_array($o) && count($o) >= 2) { $opts[] = [(int) $o[0], (int) $o[1]]; }
            }
            $out[] = ['temp_id' => $id, 'option' => $opts];
        }
        return $out;
    }

    /** Xếp đầu / thân / chân thành các lớp ảnh có toạ độ tuyệt đối trong khung STAGE_W x STAGE_H. */
    private function compose(int $gender, int $headId, ?int $bodyId, ?int $legId): array
    {
        $meta = self::partsMeta();
        $fb = self::FALLBACK[$gender] ?? self::FALLBACK[0];
        $approx = false;

        $pick = function (string $kind, ?int $id, int $fallback) use ($meta, &$approx) {
            if ($id !== null && isset($meta[$kind][(string) $id])) { return $meta[$kind][(string) $id]; }
            $approx = true;
            return $meta[$kind][(string) $fallback] ?? null;
        };
        $h = $pick('head', $headId, $fb[0]);
        $b = $pick('body', $bodyId, $fb[1]);
        $l = $pick('leg',  $legId,  $fb[2]);
        if ($h === null || $b === null || $l === null) {
            return [[], true];    // chưa chạy tools/build_sprites.py
        }

        // Xếp từ dưới lên. Cả 3 mảnh dùng CHUNG một mốc ngang (cx - ANCHOR_X) rồi cộng độ lệch dx riêng của từng mảnh
        // (đúng như game: dx/dy là vị trí góc trên-trái của mảnh so với mốc chung, KHÔNG căn giữa theo bề rộng ảnh –
        // căn giữa làm đầu lệch khỏi thân). Theo chiều dọc: thân đè lên chân 24px, đầu đè lên thân 24px (ảnh zoom x4).
        $left = (int) (self::STAGE_W / 2 - self::ANCHOR_X);
        $legTop  = self::STAGE_H - self::BOTTOM_GAP - $l['h'] + $l['dy'];
        $bodyTop = $legTop + 24 - $b['h'] + $b['dy'];
        // Đầu: game căn theo MỐC CHUNG (dy là vị trí mép trên so với mốc; cằm luôn ở dy + cao = 80 với phần lớn đầu),
        // nên KHÔNG căn theo đáy ảnh – các đầu cao thấp khác nhau (Namếc, Krillin...) mới cùng khớp với thân.
        // Số HEAD_RISE = độ cao mốc đầu so với mép trên thân: TĂNG => đầu nhô CAO lên, GIẢM => đầu hạ THẤP xuống.
        // Thân là ảnh rời (trang phục mới) thường cao hơn/có cánh, áo choàng: neo đầu theo chiều cao thân chuẩn 48 để đầu không bị nhô lên.
        $bodyRef = isset($b['i']) ? $legTop + 24 - 48 + $b['dy'] : $bodyTop;
        $headTop = $bodyRef - self::HEAD_RISE + $h['dy'];

        $mk = fn(array $p, int $top, int $z) => [
            'src' => self::partSrc($p), 'w' => $p['w'], 'h' => $p['h'],
            'left' => $left + $p['dx'] + ($z === 3 ? -13 : 2), 'top' => $top, 'z' => $z,
        ];
        return [[$mk($l, $legTop, 1), $mk($b, $bodyTop, 2), $mk($h, $headTop, 3)], $approx];
    }

    /** Đường dẫn ảnh của một mảnh: ảnh đã cắt sẵn ('f') hoặc ảnh rời trong thư mục game ('i' = id icon). */
    public static function partSrc(array $p): string
    {
        return isset($p['i']) ? icon_url((int) $p['i']) : asset('img/' . $p['f']);
    }

    /** Ảnh đầu (cho avatar diễn đàn): đầu thật nếu có ảnh, không thì đầu mặc định theo hành tinh. */
    public static function headMeta(int $head, int $gender): ?array
    {
        $m = self::partsMeta()['head'] ?? [];
        return $m[(string) $head] ?? $m[(string) (self::FALLBACK[$gender][0] ?? 64)] ?? null;
    }

    /** parts.json: danh sách bộ phận đã cắt ảnh. Đọc một lần mỗi request. */
    private static function partsMeta(): array
    {
        if (self::$partsMeta === null) {
            $file = ROOT_PATH . '/public/assets/data/parts.json';
            $data = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
            self::$partsMeta = is_array($data) ? $data : ['head' => [], 'body' => [], 'leg' => []];
        }
        return self::$partsMeta;
    }

    /** Lấy thông tin các item_template cần dùng bằng MỘT câu SQL (id đã ép int nên an toàn). */
    private function templates(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) { return []; }
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $out = [];
        try {
            foreach ($this->fetchAll("SELECT id, TYPE AS type, NAME AS name, icon_id, part FROM item_template WHERE id IN ($marks)", $ids) as $r) {
                $out[(int) $r['id']] = $r;
            }
        } catch (PDOException $e) { /* bảng khác tên => hiển thị trống thay vì lỗi */ }
        return $out;
    }

    private function optionNames(): array
    {
        $out = [];
        try {
            foreach ($this->fetchAll('SELECT id, NAME AS name FROM item_option_template') as $r) {
                $out[(int) $r['id']] = (string) $r['name'];
            }
        } catch (PDOException $e) { }
        return $out;
    }

    /** Hình ảnh của một cải trang: ['head'=>int,'body'=>int,'leg'=>int] (-1 = không đổi). */
    private function costume(int $itemId): ?array
    {
        try {
            $r = $this->fetchOne('SELECT head, body, leg FROM cai_trang WHERE id_temp = ? LIMIT 1', [$itemId]);
        } catch (PDOException $e) { return null; }
        return $r ? ['head' => (int) $r['head'], 'body' => (int) $r['body'], 'leg' => (int) $r['leg']] : null;
    }
}
