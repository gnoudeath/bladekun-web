<?php
/**
 * GameAssets – tìm ảnh gốc của game cho web dùng.
 *
 * Thứ tự tìm một icon:
 *   1. Thư mục game:   <game_path>/resources/normal/image/<zoom>/icon/<id>.png   (nguồn chính – dùng chung với server)
 *   2. Thư mục bổ sung: public/assets/extra/icon/<id>.png                        (ảnh bạn tự thêm cho thứ game không có)
 * Cả hai không có => trả về null và view tự hiện ô trống.
 *
 * AN TOÀN: id luôn được ép thành số nguyên trước khi ghép đường dẫn => không ai đọc được file lạ bằng "../".
 */
class GameAssets
{
    private static ?string $root = null;
    private static bool $rootResolved = false;

    /** Thư mục gốc project game (đã chuẩn hoá) hoặc null nếu không tìm thấy. */
    public static function gameRoot(): ?string
    {
        if (self::$rootResolved) {
            return self::$root;
        }
        self::$rootResolved = true;
        $cfg = config('assets.game_path', null);
        // Không cấu hình => thử thư mục cùng cấp với thư mục web (…/htdocs/Nro-Online hoặc …/nro-online)
        $candidates = $cfg ? [$cfg] : ['../Nro-Online', '../nro-online'];
        foreach ($candidates as $c) {
            $abs = preg_match('~^([A-Za-z]:[\\\\/]|/)~', $c) ? $c : ROOT_PATH . '/' . $c;   // đường dẫn tuyệt đối hay tương đối
            $real = realpath($abs);
            if ($real !== false && is_dir($real . '/resources/normal/image')) {
                self::$root = $real;
                break;
            }
        }
        return self::$root;
    }

    /** Đường dẫn file icon trên đĩa, hoặc null nếu không có ở cả hai nơi. */
    public static function iconPath(int $id): ?string
    {
        if ($id < 0) {
            return null;
        }
        $zoom = max(1, min(4, (int) config('assets.zoom', 4)));
        $root = self::gameRoot();
        if ($root !== null) {
            $f = $root . '/resources/normal/image/' . $zoom . '/icon/' . $id . '.png';
            if (is_file($f)) {
                return $f;
            }
        }
        $extra = ROOT_PATH . '/public/assets/extra/icon/' . $id . '.png';
        return is_file($extra) ? $extra : null;
    }
}
