<?php
/**
 * Upload – nhận ảnh / video người dùng tải lên một cách AN TOÀN.
 *
 * Những điều KHÔNG bao giờ tin từ người dùng: tên file, đuôi file và Content-Type do trình duyệt gửi.
 * Vì vậy ta:
 *   1. kiểm tra dung lượng theo config (forum.max_upload_mb, mặc định 4MB);
 *   2. đọc loại file THẬT bằng finfo (nhìn vào nội dung file) và chỉ cho phép danh sách trắng bên dưới;
 *   3. với ảnh, mở thử bằng getimagesize() để chắc chắn là ảnh thật;
 *   4. lưu dưới TÊN NGẪU NHIÊN do server đặt, đuôi do server chọn (không dùng tên gốc) => không thể tải file .php lên;
 *   5. thư mục public/uploads có .htaccess tắt thực thi PHP (phòng thủ thêm).
 */
class Upload
{
    /** loại file thật (MIME) => [đuôi lưu, loại hiển thị] */
    private const ALLOWED = [
        'image/jpeg' => ['jpg',  'image'],
        'image/png'  => ['png',  'image'],
        'image/gif'  => ['gif',  'image'],
        'image/webp' => ['webp', 'image'],
        'video/mp4'  => ['mp4',  'video'],
        'video/webm' => ['webm', 'video'],
    ];

    /**
     * Xử lý ô <input type="file" name="$field">.
     * @return array{0: ?array{path:string,type:string}, 1: ?string}  [thông tin file | null, thông báo lỗi | null]
     *         Không chọn file => [null, null] (không phải lỗi).
     */
    public static function handle(string $field = 'media'): array
    {
        $f = $_FILES[$field] ?? null;
        if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }
        $maxMb = (int) config('forum.max_upload_mb', 4);
        $max = $maxMb * 1024 * 1024;

        if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE || $f['size'] > $max) {
            return [null, "File quá lớn (tối đa {$maxMb}MB)."];
        }
        if ($f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
            return [null, 'Tải file lên thất bại, hãy thử lại.'];
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        if (!isset(self::ALLOWED[$mime])) {
            return [null, 'Chỉ cho phép ảnh (jpg, png, gif, webp) hoặc video (mp4, webm).'];
        }
        [$ext, $type] = self::ALLOWED[$mime];
        if ($type === 'image' && @getimagesize($f['tmp_name']) === false) {
            return [null, 'File ảnh không hợp lệ.'];
        }

        $sub = date('Ym');
        $dir = ROOT_PATH . '/public/uploads/forum/' . $sub;
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return [null, 'Không tạo được thư mục lưu file (kiểm tra quyền ghi public/uploads).'];
        }
        $name = bin2hex(random_bytes(12)) . '.' . $ext;
        if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) {
            return [null, 'Không lưu được file.'];
        }
        return [['path' => "forum/$sub/$name", 'type' => $type], null];
    }

    /** Xoá file đã tải lên (khi xoá bài). Chỉ xoá trong public/uploads/forum, chặn "../". */
    public static function delete(?string $path): void
    {
        if ($path && preg_match('~^forum/\d{6}/[a-f0-9]{24}\.[a-z0-9]+$~', $path)) {
            @unlink(ROOT_PATH . '/public/uploads/' . $path);
        }
    }
}
