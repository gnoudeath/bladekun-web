<?php
/**
 * =====================================================================
 *  CẤU HÌNH CHUNG CỦA WEBSITE
 * =====================================================================
 *  Đây là file DUY NHẤT bạn cần sửa để web chạy được trên máy mình.
 *  File trả về một mảng; trong code, đọc giá trị bằng hàm config('db.name').
 *
 *  ⚠ File này chứa mật khẩu database. Không đưa lên GitHub công khai.
 */
return [

    // ---------------------------------------------------------------
    // Thông tin hiển thị của website
    // ---------------------------------------------------------------
    'app' => [
        'name'        => 'BLADEKUN',
        'tagline'     => 'Máy chủ Ngọc Rồng tự làm – chiến đấu, luyện tập, vươn lên!',
        'timezone'    => 'Asia/Ho_Chi_Minh',

        // true  = hiện chi tiết lỗi (chỉ nên bật khi đang học / sửa code)
        // false = chỉ hiện trang lỗi thân thiện (luôn dùng false khi mở ra internet)
        'debug'       => true,

        // false = địa chỉ dạng  .../index.php?r=rank   (chạy ngay, không cần cấu hình Apache)
        // true  = địa chỉ đẹp dạng  .../rank          (cần bật mod_rewrite, xem README)
        'pretty_urls' => false,
    ],

    // ---------------------------------------------------------------
    // Database: PHẢI TRÙNG với database mà game server đang dùng
    // (xem server.db.* trong config/server.properties của game)
    // ---------------------------------------------------------------
    'db' => [
        'host' => '127.0.0.1',   // dùng 127.0.0.1 thay vì "localhost" để tránh lỗi trên Windows
        'port' => 3306,
        'name' => 'nro-d',       // <-- đổi cho đúng với server.db.name của game
        'user' => 'root',
        'pass' => '',            // XAMPP mặc định để trống
        'dsn'  => null,          // để null. (Chỉ dùng khi kiểm thử với database khác)
    ],

    // ---------------------------------------------------------------
    // Quy tắc liên quan tới game
    // ---------------------------------------------------------------
    'game' => [
        // Tài khoản mới tạo trên web có được kích hoạt ngay không?
        // Trong game, tài khoản active = 0 bị khoá chat thế giới, giao dịch, thách đấu...
        // true  = web tự đặt active = 1 khi đăng ký (hợp với server chơi cho vui)
        // false = để 0, bạn tự kích hoạt trong database
        'auto_activate' => true,

        // Độ dài mật khẩu. Ô nhập mật khẩu của client game thường bị giới hạn,
        // nên để vừa phải để tài khoản tạo trên web gõ vào game được.
        'username_min' => 4,
        'username_max' => 20,    // cột account.username là varchar(20)
        'password_min' => 6,
        'password_max' => 20,

        // Bảng xếp hạng có ẩn nhân vật của tài khoản admin / bị khoá không?
        'rank_hide_admins' => true,

        // Sức mạnh tối thiểu để được vào bảng xếp hạng (0 = không giới hạn).
        // Người chưa đạt mức này sẽ không hiện trong Top sức mạnh / Top đệ tử (và trang chủ).
        'rank_min_power'     => 10000000000,   // 10 tỉ – Top sức mạnh
        'rank_min_pet_power' => 10000000000,   // 10 tỉ – Top đệ tử (sức mạnh đệ tử)
        'rank_min_boss'      => 6,             // Top diệt boss: phải hạ HƠN 5 boss (>= 6)
        'rank_min_topup'     => 50000,         // Top nạp: tổng nạp từ 50.000 VNĐ trở lên

        // news_posts.status nào được coi là "đã đăng"? (kiểm tra trong database của bạn)
        'news_published_status' => 1,

        // Game của bạn chạy "server số mấy"? Game đặt tên bảng bang hội theo số này:
        // server số 1 => bảng clan_sv1 và cột player.clan_id_sv1 (xem hằng Manager.SERVER trong code Java).
        // Chỉ nhận số nguyên (được ép kiểu rồi mới ghép vào câu SQL nên an toàn).
        'server_no' => 1,

        // Có hiện bảng "Top nạp" công khai không? Đây là số tiền người chơi đã nạp (player.tong_nap):
        // nhiều server coi đó là thông tin riêng tư, nếu không muốn hiện thì đặt false.
        'rank_show_topup' => true,

        // Số bài tin mỗi trang ở trang Tin tức
        'news_per_page' => 6,
    ],

    // ---------------------------------------------------------------
    // Ảnh gốc của game: web đọc THẲNG từ thư mục server game (dùng chung database nên id khớp nhau)
    'assets' => [
        // Đường dẫn tới thư mục project game (có thư mục resources/ bên trong).
        // null = tự tìm thư mục cùng cấp: ../Nro-Online hoặc ../nro-online. Hoặc ghi rõ, ví dụ 'D:/Nro-Online'.
        'game_path' => 'D:/Nro Blade/bladekun-server',   // thư mục server game (nơi có resources/normal/image)
        // Mức zoom của ảnh (1-4). 4 = nét nhất.
        'zoom'      => 4,
        // Game không có ảnh nào thì web tìm ở public/assets/extra/icon/<id>.png (bạn tự thêm).
    ],

    // Diễn đàn (cần chạy database/forum.sql một lần)
    // ---------------------------------------------------------------
    'forum' => [
        'cooldown_seconds' => 30,     // mỗi tài khoản phải chờ bấy nhiêu giây giữa hai lần gửi (bài hoặc bình luận)
        'max_upload_mb'    => 4,      // dung lượng tối đa ảnh/video đính kèm
        'max_content'      => 2000,   // số ký tự tối đa của một bài/bình luận
        'max_title'        => 100,
        'per_page'         => 10,     // số bài mỗi trang
    ],

    // ---------------------------------------------------------------
    // Trang "Tải game" (chỉ để hiển thị, web không kết nối tới game server)
    // ---------------------------------------------------------------
    'download' => [
        'client_url'  => '',                 // dán link tải client (Google Drive, Mega...) vào đây
        'server_host' => '127.0.0.1',        // địa chỉ người chơi sẽ thấy trên trang
        'server_port' => 14445,
    ],

    // ---------------------------------------------------------------
    // Chống spam: tối đa N lần trong T giây, tính theo địa chỉ IP
    // ---------------------------------------------------------------
    'limits' => [
        'login_max'      => 5,    'login_window'    => 600,    // 5 lần sai / 10 phút
        'register_max'   => 3,    'register_window' => 3600,   // 3 tài khoản / 1 giờ
    ],
];
