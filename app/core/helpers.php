<?php
/**
 * Các hàm tiện ích dùng ở mọi nơi (controller, model, view).
 * Giữ ngắn gọn, mỗi hàm làm đúng một việc.
 */

/**
 * Đọc cấu hình theo "dấu chấm":  config('db.name')  =>  $config['db']['name']
 * Mảng cấu hình được nạp một lần duy nhất từ config/config.php.
 */
function config(string $key, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = require ROOT_PATH . '/config/config.php';
    }
    $value = $config;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/**
 * Escape dữ liệu trước khi in ra HTML. QUAN TRỌNG: luôn dùng e() cho mọi dữ liệu
 * lấy từ người dùng hoặc database (tên nhân vật, tiêu đề tin...) để chống tấn công XSS.
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Thư mục gốc của website trên URL, ví dụ "/bladekun-web/public" hoặc "" (nếu đặt ở gốc).
 * Tự suy ra từ đường dẫn của public/index.php nên web chạy được ở mọi thư mục con của XAMPP.
 */
function base_url(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    return $dir === '/' || $dir === '.' ? '' : rtrim($dir, '/');
}

/**
 * Tạo địa chỉ trang:  url('rank')  /  url('news/view', ['id' => 3])
 * Chuyển giữa 2 kiểu địa chỉ bằng config app.pretty_urls, code ở nơi khác không cần đổi.
 */
function url(string $path = '', array $params = []): string
{
    $path = trim($path, '/');
    if (config('app.pretty_urls')) {
        $url = base_url() . '/' . $path;                 // .../rank
    } else {
        $url = base_url() . '/index.php';                // .../index.php?r=rank
        if ($path !== '') {
            $params = ['r' => $path] + $params;
        }
    }
    $query = http_build_query($params);
    return $url . ($query !== '' ? '?' . $query : '');
}

/** Địa chỉ file tĩnh (css/js/ảnh) trong public/assets. Thêm ?v=thời-gian-sửa để trình duyệt tải lại khi file đổi. */
function asset(string $path): string
{
    $file = ROOT_PATH . '/public/assets/' . $path;
    $version = is_file($file) ? '?v=' . filemtime($file) : '';
    return base_url() . '/assets/' . $path . $version;
}

/** Chuyển hướng sang một trang rồi dừng chương trình. */
function redirect(string $path, array $params = []): void
{
    header('Location: ' . url($path, $params));
    exit;
}

/** Trang hiện tại có phải $path không (để tô sáng menu). */
function is_current(string $path): bool
{
    return Router::currentPath() === '/' . trim($path, '/');
}

/**
 * Địa chỉ IP của người truy cập.
 * Cố ý chỉ tin REMOTE_ADDR: các header như X-Forwarded-For có thể bị giả mạo.
 * (Nếu sau này đặt web phía sau Cloudflare/Nginx thì mới cần xử lý thêm.)
 */
function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/** Số có dấu chấm ngăn cách hàng nghìn: 1234567 => "1.234.567" */
function format_number($n): string
{
    return number_format((float) $n, 0, ',', '.');
}

/** Giá trị người dùng đã nhập ở lần gửi form trước (để không phải gõ lại khi báo lỗi). */
function old(string $key, string $default = ''): string
{
    return e(Session::old($key, $default));
}

/**
 * Biến nội dung HTML trong database thành văn bản thường (giữ xuống dòng).
 * Dùng cho bài tin tức: nội dung cũ trong database là HTML, nếu in thẳng ra trang mà
 * có thẻ <script> hay thuộc tính onerror=... thì kẻ xấu chèn được mã độc (XSS).
 * Cách an toàn: bỏ HẾT thẻ HTML, rồi escape. Muốn giữ định dạng (đậm, ảnh...) sau này,
 * hãy dùng thư viện lọc HTML như HTMLPurifier thay vì tự lọc.
 */
function html_to_text(?string $html): string
{
    $html = (string) $html;
    $html = preg_replace('~<(script|style)\b.*?</\1>~is', '', $html);               // bỏ luôn cả NỘI DUNG của <script>/<style>
    $html = preg_replace('~<br\s*/?>|</(p|div|li|h[1-6]|tr)>~i', "\n", $html);   // thẻ đóng khối => xuống dòng
    $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace("~\n{3,}~", "\n\n", $text));                          // gộp dòng trống thừa
}

/** Ngày giờ từ database => "dd/mm/yyyy HH:MM". Trả về "—" nếu rỗng. */
function format_datetime(?string $value): string
{
    if (!$value) {
        return '—';
    }
    $ts = strtotime($value);
    return $ts ? date('d/m/Y H:i', $ts) : '—';
}

/**
 * Thẻ <img> trang trí lấy từ ảnh game trong public/assets/img/scene/ :  scene('tree-earth', 'page-head__deco')
 * Ảnh chỉ để làm đẹp nên alt="" và aria-hidden (trình đọc màn hình bỏ qua); loading="lazy" để trang nhẹ hơn.
 */
function scene(string $name, string $class = ''): string
{
    return '<img class="scene ' . e($class) . '" src="' . e(asset('img/scene/' . $name . '.png')) . '" alt="" aria-hidden="true" loading="lazy">';
}

/** Ảnh cảnh đại diện cho từng hành tinh (gender 0/1/2): nhà Trái Đất, cây Namếc, phi thuyền Xayda. */
function planet_scene($gender): string
{
    return ['tree-earth', 'tree-namek', 'pod-xayda'][(int) $gender] ?? 'tree-earth';
}

// =====================================================================
//  DIỄN ĐÀN: văn bản + biểu cảm + avatar + file đính kèm
// =====================================================================

/** URL công khai của file người dùng tải lên (đường dẫn lưu trong DB tính từ public/uploads). */
function upload_url(string $path): string
{
    return base_url() . '/uploads/' . $path;
}

/**
 * Biểu cảm dạng ảnh ("sticker"): mọi file .png/.gif/.webp trong public/assets/emoji/ đều dùng được.
 * Muốn thêm biểu cảm mới: chép file vào thư mục đó (tên chỉ gồm a-z, 0-9, _ hoặc -), ví dụ "haha.png"
 * rồi người dùng gõ :haha: là ra ảnh. Không cần sửa code.
 * @return string[] danh sách tên (không có đuôi)
 */
function sticker_names(): array
{
    static $names = null;
    if ($names === null) {
        $names = [];
        foreach (glob(ROOT_PATH . '/public/assets/emoji/*.{png,gif,webp}', GLOB_BRACE) ?: [] as $f) {
            $n = pathinfo($f, PATHINFO_FILENAME);
            if (preg_match('/^[a-z0-9_-]+$/', $n)) { $names[$n] = basename($f); }
        }
        ksort($names);
    }
    return $names;
}

/**
 * Biến văn bản người dùng gõ thành HTML an toàn:
 *   1. e()       : vô hiệu hoá mọi thẻ HTML/JS (chống XSS) – LUÔN làm đầu tiên;
 *   2. :tên:     : thay bằng ảnh biểu cảm nếu tên có trong thư mục emoji (chỉ tên hợp lệ trong whitelist);
 *   3. nl2br     : xuống dòng.
 * Emoji Unicode (😀) là ký tự thường nên hiện luôn, không cần xử lý.
 */
function rich_text(?string $text): string
{
    $html = e($text);
    $stickers = sticker_names();
    if ($stickers) {
        $html = preg_replace_callback('/:([a-z0-9_-]{1,30}):/', function ($m) use ($stickers) {
            return isset($stickers[$m[1]])
                ? '<img class="sticker" src="' . e(asset('emoji/' . $stickers[$m[1]])) . '" alt=":' . $m[1] . ':" title=":' . $m[1] . ':" width="28" height="28">'
                : $m[0];
        }, $html);
    }
    return nl2br($html, false);
}

/** "15 giờ trước", "19 ngày trước"... từ chuỗi ngày giờ trong DB. */
function time_ago(?string $value): string
{
    $ts = $value ? strtotime($value) : 0;
    if (!$ts) { return '—'; }
    $d = max(0, time() - $ts);
    if ($d < 60)       { return 'vừa xong'; }
    if ($d < 3600)     { return floor($d / 60) . ' phút trước'; }
    if ($d < 86400)    { return floor($d / 3600) . ' giờ trước'; }
    if ($d < 86400 * 30) { return floor($d / 86400) . ' ngày trước'; }
    return date('d/m/Y', $ts);
}

/** Địa chỉ ảnh icon của game (do MediaController trả về). Dùng icon_exists() để biết có ảnh hay không. */
function icon_url(int $id): string
{
    return url('media', ['id' => $id]);
}

function icon_exists(int $id): bool
{
    return GameAssets::iconPath($id) !== null;
}

/**
 * Avatar của người đăng. Thứ tự: chân dung chính thức trong game (bảng head_avatar + thư mục icon của game)
 * -> đầu ghép từ sprite (nếu có) -> ảnh mặc định.
 */
function head_avatar($head, $gender): string
{
    $avatar = AvatarModel::avatarId((int) $head);
    if ($avatar !== null && icon_exists($avatar)) {
        return icon_url($avatar);
    }
    $meta = CharacterModel::headMeta((int) $head, (int) $gender);
    return $meta ? CharacterModel::partSrc($meta) : asset('img/gem.png');
}
