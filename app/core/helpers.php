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
