<?php
/**
 * AvatarModel – bảng head_avatar của game: đầu nhân vật (head_id) -> id ảnh chân dung (avatar_id, nằm trong thư mục icon).
 * Nạp cả bảng (~460 dòng) MỘT lần mỗi request rồi dùng lại cho mọi bài/bình luận.
 */
class AvatarModel extends Model
{
    private static ?array $map = null;

    public static function avatarId(int $head): ?int
    {
        if (self::$map === null) {
            self::$map = [];
            try {
                foreach ((new self())->fetchAll('SELECT head_id, avatar_id FROM head_avatar') as $r) {
                    self::$map[(int) $r['head_id']] = (int) $r['avatar_id'];
                }
            } catch (PDOException $e) { /* chưa có bảng => dùng ảnh ghép sprite như trước */ }
        }
        return self::$map[$head] ?? null;
    }
}
