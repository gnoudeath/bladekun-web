# BLADEKUN WEB – Website đi kèm máy chủ game

Website nhỏ viết bằng **PHP thuần theo mô hình MVC**, dùng **chung database với game server**, giao diện phong cách **truyện tranh** (tia sáng, chấm bán tông, viền đen dày, bong bóng thoại).

Mục tiêu của project vừa là một trang web chạy được, vừa là **tài liệu học**: code ngắn, có chú thích tiếng Việt ở những đoạn quan trọng, tách lớp rõ ràng để bạn đọc từng phần và tự mở rộng.

---

## Mục lục

1. [Web làm được gì](#1-web-làm-được-gì)
2. [Cài đặt nhanh trên XAMPP](#2-cài-đặt-nhanh-trên-xampp)
3. [Cấu trúc thư mục](#3-cấu-trúc-thư-mục)
4. [MVC là gì và nó chạy thế nào trong project này](#4-mvc-là-gì-và-nó-chạy-thế-nào-trong-project-này)
5. [Giải thích từng chức năng](#5-giải-thích-từng-chức-năng)
6. [Giao diện truyện tranh được vẽ thế nào](#6-giao-diện-truyện-tranh-được-vẽ-thế-nào)
7. [Bảo mật: đã làm gì, còn thiếu gì](#7-bảo-mật-đã-làm-gì-còn-thiếu-gì)
8. [Thêm một trang mới (hướng dẫn từng bước)](#8-thêm-một-trang-mới-hướng-dẫn-từng-bước)
9. [Lộ trình: nên phát triển tính năng nào tiếp theo](#9-lộ-trình-nên-phát-triển-tính-năng-nào-tiếp-theo)
10. [Xử lý lỗi thường gặp](#10-xử-lý-lỗi-thường-gặp)
11. [Đã kiểm thử những gì](#11-đã-kiểm-thử-những-gì)

---

## 1. Web làm được gì

| Trang | Ai dùng | Mô tả ngắn |
|---|---|---|
| **Trang chủ** | Mọi người | Giới thiệu, số liệu máy chủ, top 5 chiến binh, tin mới, 3 bước bắt đầu |
| **Bảng xếp hạng** | Mọi người | 4 tab: sức mạnh, đệ tử, diệt boss, top nạp; Top 20/50/100, có thanh so với người đứng đầu |
| **Tin tức** | Mọi người | Danh sách (phân trang 6 bài/trang) và đọc bài thông báo |
| **Tải game** | Mọi người | Hướng dẫn 4 bước, nút tải client, địa chỉ máy chủ |
| **Bang hội** (`/clans`, `/clan?id=`) | Mọi người | Xếp hạng bang theo sức mạnh, xem thành viên từng bang |
| **Hồ sơ nhân vật** (`/player?id=`) | Mọi người | Bấm tên trong bảng xếp hạng: hành tinh, sức mạnh, hạng, bang, boss đã hạ |
| **Giới thiệu / Hướng dẫn / Luật chơi** | Mọi người | 3 trang chữ tĩnh (menu "Thông tin") |
| **Đăng ký** | Khách | Tạo tài khoản dùng để đăng nhập **thẳng vào game** |
| **Đăng nhập / Đăng xuất** | Người chơi | Phiên đăng nhập của web (độc lập với việc đang online trong game) |
| **Tài khoản của tôi** | Đã đăng nhập | Xem thông tin, thỏi vàng, ruby, nhân vật, hạng |
| **Đổi mật khẩu** | Đã đăng nhập | Đổi mật khẩu đăng nhập game |

---

## 2. Cài đặt nhanh trên XAMPP

**Điều kiện:** XAMPP đã cài (có Apache + PHP 8.0 trở lên + MySQL), game server đã import database.

1. **Bật Apache và MySQL** trong XAMPP Control Panel.
2. **Chép cả thư mục `bladekun-web`** vào `C:\xampp\htdocs\`.
3. **Mở `config/config.php`** và sửa mục `db`:
   - `name` phải **trùng với `server.db.name`** trong `config/server.properties` của game (ví dụ `nro-d`).
   - `user`, `pass` giống như game đang dùng (XAMPP mặc định là `root` và mật khẩu trống).
4. **Mở trình duyệt:** `http://localhost/bladekun-web/` (tự chuyển sang thư mục `public/`).
5. Bấm **Đăng ký**, tạo một tài khoản, rồi thử đăng nhập vào game bằng tài khoản đó.

> **Bạn cần mạng internet** khi xem web để tải 2 font (Bangers, Be Vietnam Pro) và Bootstrap. Không có mạng web vẫn chạy bình thường, chỉ dùng font dự phòng.

### Tuỳ chọn: tăng tốc bảng xếp hạng
Mở `database/optional-indexes.sql` trong Navicat và chạy một lần để thêm chỉ mục cho cột `player.power`. Không bắt buộc.

### Tuỳ chọn: địa chỉ đẹp (`/rank` thay vì `index.php?r=rank`)
1. Trong `config/config.php` đổi `'pretty_urls' => true`.
2. Đảm bảo Apache bật `mod_rewrite` (XAMPP bật sẵn) và cho phép `.htaccess` (`AllowOverride All` – XAMPP mặc định đã cho phép).
3. Truy cập trực tiếp `http://localhost/bladekun-web/public/`.

### Tuỳ chọn tốt hơn: VirtualHost trỏ thẳng vào `public/`
Cách chạy chuẩn khi đưa web ra ngoài: đặt `DocumentRoot` của Apache là thư mục `bladekun-web/public`. Khi đó người ngoài **không thể** chạm vào `app/`, `config/`, `storage/` dù chỉ qua một lỗi cấu hình.

---

## 3. Cấu trúc thư mục

```
bladekun-web/
├── index.php                  Chỉ để chuyển hướng vào public/ (tiện khi dùng XAMPP)
├── README.md                  Tài liệu này
│
├── config/
│   └── config.php             ★ File cấu hình DUY NHẤT cần sửa: database, quy tắc, link tải game
│
├── public/                    ★ Thư mục duy nhất mở ra cho trình duyệt
│   ├── index.php              Front controller: cửa vào của mọi yêu cầu
│   ├── .htaccess              Luật "URL đẹp" cho Apache
│   └── assets/
│       ├── css/style.css      Toàn bộ giao diện truyện tranh (có chú thích từng khối)
│       └── js/app.js          Menu điện thoại + nút Hiện/Ẩn mật khẩu
│
├── app/                       ★ Code của web, chia theo MVC
│   ├── routes.php             Bảng đường đi: địa chỉ nào → Controller nào
│   ├── core/                  "Bộ khung" dùng chung (ít khi phải sửa)
│   │   ├── Router.php           Chọn Controller theo địa chỉ
│   │   ├── Controller.php       Lớp cha của mọi Controller
│   │   ├── Model.php            Lớp cha của mọi Model (hàm truy vấn database)
│   │   ├── View.php             Ghép dữ liệu vào giao diện
│   │   ├── Database.php         Kết nối PDO tới MySQL
│   │   ├── Session.php          Phiên làm việc + thông báo một lần + nhớ ô đã nhập
│   │   ├── Auth.php             "Ai đang đăng nhập?"
│   │   ├── Csrf.php             Chống giả mạo form
│   │   ├── RateLimiter.php      Giới hạn số lần thử (chống dò mật khẩu)
│   │   ├── Validator.php        Kiểm tra tên, mật khẩu, email
│   │   └── helpers.php          Hàm tiện ích: e(), url(), asset(), config()...
│   ├── models/                M – nói chuyện với database
│   │   ├── AccountModel.php     Bảng account
│   │   ├── PlayerModel.php      Bảng player (xếp hạng nhiều tiêu chí, hồ sơ, thành viên bang)
│   │   ├── ClanModel.php        Bảng clan_sv1 (bang hội)
│   │   └── NewsModel.php        Bảng news_posts
│   ├── controllers/           C – điều phối: nhận yêu cầu → hỏi Model → chọn View
│   │   ├── HomeController.php
│   │   ├── RankController.php
│   │   ├── NewsController.php
│   │   ├── ClanController.php     Xếp hạng bang + chi tiết bang
│   │   ├── PlayerController.php   Hồ sơ công khai của nhân vật
│   │   ├── PageController.php     Trang chữ: giới thiệu, hướng dẫn, luật
│   │   ├── DownloadController.php
│   │   ├── AuthController.php     Đăng ký / đăng nhập / đăng xuất
│   │   └── AccountController.php  Tài khoản + đổi mật khẩu
│   └── views/                 V – file giao diện (HTML + một chút PHP)
│       ├── layouts/main.php     Khung chung: đầu trang, menu, chân trang
│       ├── partials/flash.php   Thanh thông báo
│       ├── home/  rank/  clan/  player/  pages/  news/  download/  auth/  account/  errors/
│
├── database/
│   └── optional-indexes.sql   Tuỳ chọn: thêm chỉ mục cho bảng xếp hạng
└── storage/                   Dữ liệu tạm do web ghi (đếm số lần thử đăng nhập)
```

> Các file `.htaccess` trong `app/`, `config/`, `database/`, `storage/` chặn trình duyệt đọc trực tiếp những thư mục này (khi chạy trên Apache).

**Thứ tự nên đọc code để dễ hiểu nhất:**
`public/index.php` → `app/routes.php` → `RankController` → `PlayerModel` → `views/rank/index.php` → `views/layouts/main.php` → rồi tới `AuthController`.

---

## 4. MVC là gì và nó chạy thế nào trong project này

**MVC** chia code thành 3 vai để mỗi file làm đúng một việc:

| Vai | Làm gì | Không được làm gì |
|---|---|---|
| **M**odel | Đọc/ghi database, trả dữ liệu sạch | Không biết gì về HTML hay trang web |
| **V**iew | Biến dữ liệu thành HTML để người dùng xem | Không truy vấn database |
| **C**ontroller | Nhận yêu cầu, hỏi Model, chọn View, kiểm tra dữ liệu nhập | Không viết SQL, không viết HTML |

### Hành trình của một yêu cầu: mở trang "Bảng xếp hạng"

Người dùng mở `index.php?r=rank&top=50`:

```
1. public/index.php        Cửa vào. Nạp helper, bật autoload, bật session, gọi Router.
        ↓
2. app/core/Router.php     Đọc "r=rank" → tìm trong app/routes.php dòng 'GET /rank'.
        ↓
3. RankController::index() Kiểm tra top=50 hợp lệ (chỉ 20/50/100), rồi gọi Model.
        ↓
4. PlayerModel::topByPower(50)   Chạy SQL (JOIN account, bỏ admin/bị khoá, ORDER BY power DESC).
        ↓                         Trả về một mảng các nhân vật.
5. views/rank/index.php    Duyệt mảng, vẽ từng hàng bảng (mọi dữ liệu đều qua e() để chống XSS).
        ↓
6. views/layouts/main.php  Đặt phần nội dung vào khung chung (menu, footer) → gửi HTML về trình duyệt.
```

### Hành trình của một form: bấm "Tạo tài khoản"

```
POST index.php?r=register
   → Router → AuthController::register()
        1. requireCsrf()          mã bí mật của form có đúng không?
        2. ô "website" bị điền?   → là bot, bỏ qua
        3. RateLimiter            IP này tạo quá 3 tài khoản/giờ chưa?
        4. Validator              tên / email / mật khẩu có hợp lệ không? (báo hết lỗi một lượt)
        5. AccountModel           tên đã tồn tại chưa? → INSERT vào bảng account
        6. Auth::login()          đăng nhập luôn cho người dùng
        7. flash + redirect       chuyển sang trang tài khoản, kèm thông báo "Tạo tài khoản thành công!"
```
Bước 7 dùng **redirect sau khi POST** để nếu người dùng bấm F5 thì không bị gửi lại form lần nữa.

---

## 5. Giải thích từng chức năng

### 5.1 Trang chủ — `HomeController::index`
Gom dữ liệu từ 3 Model: `PlayerModel::topByPower(5)`, `NewsModel::latest(3)`, và số đếm từ `AccountModel::countAll()` / `PlayerModel::countAll()`. View `home/index.php` vẽ hero, dải số liệu, top 5 và tin mới. Thẻ "Mạnh nhất!" trong hình nổ lấy người đứng đầu danh sách top.

### 5.2 Bảng xếp hạng — `RankController::index`
- Chỉ chấp nhận `top` bằng 20, 50 hoặc 100 (tránh bị xin cả triệu dòng làm nặng server).
- SQL `JOIN account` để **loại tài khoản bị khoá và (tuỳ cấu hình `rank_hide_admins`) tài khoản admin**.
- Thanh sức mạnh = % so với người đứng đầu, tính trong view.
- Hành tinh lấy từ `player.gender`: 0 = Trái Đất, 1 = Namếc, 2 = Xayda.
- Số liệu lấy từ cột `player.power`, game cập nhật khi lưu nhân vật nên bảng **không "trực tiếp từng giây"**.

### 5.3 Đăng ký — `AuthController::register`
- Tên đăng nhập: chữ thường, số, gạch dưới, 4–20 ký tự (khớp cột `varchar(20)`). Tự đổi sang chữ thường vì MySQL không phân biệt hoa/thường (`Goku` và `goku` là một).
- Mật khẩu 6–20 ký tự, chỉ ASCII không dấu cách (người chơi phải gõ được trong ô nhập của client game).
- Chống lạm dụng: mã CSRF, ô bẫy bot, giới hạn 3 tài khoản/IP/giờ.
- Chỉ INSERT 6 cột (`username`, `password`, `email`, `ip`, `ip_address`, `active`); các cột còn lại (vàng, ruby, thời gian...) dùng giá trị mặc định có sẵn trong database, **đúng như game mong đợi**.
- `active`: theo `game.auto_activate`. Trong game, tài khoản `active = 0` bị khoá chat thế giới, giao dịch, thách đấu.

### 5.4 Đăng nhập / đăng xuất — `AuthController::login`, `logout`
- Báo lỗi **chung** "Sai tên đăng nhập hoặc mật khẩu" cho cả hai trường hợp, để kẻ xấu không dò ra tên nào tồn tại.
- Sai 5 lần/10 phút từ một IP thì bị khoá tạm.
- Tài khoản bị khoá (`ban`) không vào được.
- Khi đăng nhập thành công: `session_regenerate_id` (chống session fixation).
- Đăng xuất dùng **POST + CSRF**, không phải link thường (không ai ép bạn đăng xuất bằng một đường link).
- Đăng nhập web **không liên quan** tới việc đang online trong game; hai bên chỉ dùng chung tài khoản.

### 5.5 Tài khoản của tôi — `AccountController::index`
Đọc tài khoản bằng `AccountModel::findById` (**không lấy cột password**) và nhân vật bằng `PlayerModel::findByAccountId`. Hạng của bạn = số người mạnh hơn + 1, tính bằng `PlayerModel::rankOf`. Hiện cảnh báo nếu `active = 0`. Thỏi vàng đọc từ `account.thoi_vang`, ruby từ `account.ruby`.

### 5.6 Đổi mật khẩu — `AccountController::changePassword`
Bắt nhập lại **mật khẩu hiện tại** (phòng khi ai đó mượn máy đang đăng nhập), kiểm tra mật khẩu mới theo cùng luật với đăng ký, mật khẩu mới phải khác mật khẩu cũ.

### 5.7 Tin tức — `NewsController`
Đọc bảng `news_posts`, chỉ lấy bài có `status = game.news_published_status`. Nội dung trong database là **HTML**, nên web bỏ hết thẻ rồi escape (`html_to_text()` + `e()`), chặn kẻ xấu chèn mã độc vào bài viết. Mỗi lần xem tăng `views` lên 1. Hiện bạn **đăng tin bằng cách thêm dòng vào bảng `news_posts` trong Navicat**; trang quản trị nằm trong lộ trình bên dưới.

### 5.9 Bảng xếp hạng nhiều tab — `RankController` + `PlayerModel::topBy`
Chọn tab bằng `?type=power|pet|boss|topup`. **Cách chống SQL injection khi cột thay đổi:** tên cột không thể dùng tham số `?`, nên web chỉ chấp nhận khoá có trong danh sách trắng `PlayerModel::RANK_TYPES` (`power` → `p.power`, `pet` → `p.pet_power`, `boss` → `p.kill_boss`, `topup` → `p.tong_nap`); giá trị lạ tự quay về `power`. Mỗi dòng trả về cột `score` để view in ra mà không cần biết đang xếp theo cột nào.
Tab **Top nạp** công khai số tiền người chơi đã nạp; nếu không muốn hiện, đặt `game.rank_show_topup` = `false` trong `config.php` (tab biến mất).

### 5.10 Bang hội — `ClanController` + `ClanModel`
- Bảng bang của game tên `clan_sv1` (số `1` là `Manager.SERVER` trong code Java). Nếu server của bạn là số khác, đổi `game.server_no` trong `config.php`; web sẽ tự dùng `clan_sv<N>` và cột `player.clan_id_sv<N>`. Số này chỉ đến từ file cấu hình và được ép về số nguyên trước khi ghép vào SQL.
- **Số thành viên** đếm bằng subquery `SELECT COUNT(*) FROM player WHERE clan_id_sv1 = clan.id`, **không** đọc cột `members` (chuỗi JSON nội bộ của game) nên không phụ thuộc định dạng của nó.
- Biểu tượng bang: cột `img_id` là mã ảnh bên trong client (web không có ảnh đó) nên web dùng 1 trong 7 viên ngọc rồng theo id bang.
- Trang chi tiết (`/clan?id=`) liệt kê tối đa 50 thành viên mạnh nhất; không có bang → 404.

### 5.11 Hồ sơ nhân vật — `PlayerController::show`
Từ bảng xếp hạng/bang bấm vào tên. Hiện hành tinh (kèm ảnh cảnh vật), sức mạnh, sức mạnh đệ tử, hạng, bang, số boss đã hạ, ngày tạo. **Cố ý không hiện** vàng, ruby, tổng nạp, email. Nhân vật của admin hoặc tài khoản bị khoá trả về 404 (giống như bảng xếp hạng ẩn họ, và không tiết lộ là "có nhưng bị ẩn").

### 5.12 Phân trang tin tức — `NewsModel::page`
`LIMIT <số bài mỗi trang> OFFSET (trang-1)*<số bài mỗi trang>`; số bài mỗi trang là `game.news_per_page` (mặc định 6). `?page=` nhập bậy (âm, chữ, quá lớn) đều được ép về khoảng hợp lệ. Thanh trang nằm trong mảnh giao diện dùng chung `views/partials/pagination.php`, trang nào cũng dùng lại được: `View::partial('pagination', ['page'=>$page,'pages'=>$pages,'route'=>'news'])`.

### 5.13 Trang chữ — `PageController`
Giới thiệu, Hướng dẫn tân thủ, Luật chơi. Không có Model (không cần database): mỗi hàm chỉ chọn một View trong `views/pages/`. Sửa chữ trực tiếp trong file view. Phần **Luật chơi là bộ luật mẫu**, hãy sửa cho đúng cách bạn vận hành; phần Hướng dẫn chỉ nêu kiến thức chung, nên bổ sung chi tiết riêng của máy chủ bạn.

### 5.8 Tải game — `DownloadController`
Chỉ hiển thị thông tin từ `config/config.php` (`download.client_url`, `server_host`, `server_port`). Chưa điền link thì nút tải xám đi kèm lời nhắc.

---

## 6. Giao diện: Bootstrap 5 + "lớp áo" truyện tranh

Giao diện gồm 2 tầng, **thứ tự nạp rất quan trọng** (xem `app/views/layouts/main.php`):

1. **Bootstrap 5.3** (nạp từ CDN cdnjs): lo **bố cục** – lưới `row/col-*`, thanh menu thu gọn trên điện thoại `navbar`, form `form-control`, bảng `table-responsive`, nhóm nút `btn-group`, thông báo `alert`.
2. **`public/assets/css/style.css`** (nạp SAU Bootstrap để ghi đè): lo **diện mạo** truyện tranh.

| Hiệu ứng | Cách làm |
|---|---|
| Tia sáng toả ra phía sau | `repeating-conic-gradient` xen kẽ cam/vàng |
| Chấm bán tông (halftone) | `radial-gradient` lặp thành lưới chấm |
| Viền đen dày + bóng đổ cứng | `border: 4px solid` + `box-shadow: 6px 6px 0` (không làm mờ) |
| Chữ tiêu đề nổi có viền | `-webkit-text-stroke` + `text-shadow`, font **Bangers** |
| Tiêu đề khung nghiêng như nhãn dán | `.panel__title` + `skewX` |
| Thông báo dạng bong bóng thoại | `.alert` của Bootstrap + `.comic-alert` (cái đuôi bằng `::after`) |
| Nút bấm nhấn xuống như in offset | `.btn-comic` đổi `transform` + `box-shadow` khi `:active` |

**Đổi màu cả website:** sửa các biến ở đầu `style.css` (`--orange`, `--blue`, `--red`, `--yellow`...). Biến Bootstrap (`--bs-body-color`, `--bs-link-color`...) cũng được ghi đè ở đó.

**Hình ảnh** nằm trong `public/assets/img/`, đều lấy từ tài nguyên của game bạn (`resources/normal/image/4/`):
- `balls/1..7.png`: 7 viên ngọc rồng (icon 419–425), `gem.png`, `tomato.png`: icon vật phẩm.
- `scene/*.png`: 24 ảnh nền map của game đã được đặt tên: `tree-earth`, `house-earth`, `palm`, `dome-namek`, `tree-namek`, `pod-xayda`, `rock-xayda`, `island-big`, `island-mid`, `rock-island`, `saucer`, `pagoda`, `tower-a..d`, `building`, `fence`... Dùng trong view bằng hàm `scene('ten-anh', 'lop-css')` (xem `helpers.php`): trang chủ (đảo nổi, phi thuyền, thẻ 3 hành tinh), dải cảnh phía trên chân trang, ảnh góc phải tiêu đề mỗi trang, sân khấu hành tinh ở hồ sơ nhân vật.
- Muốn thêm ảnh khác: lấy trong `resources/normal/image/4/bg/` của game (ảnh nền map, hợp để trang trí) rồi chép vào `public/assets/img/scene/`. Các ảnh trong thư mục `monster/` và `imgbyname/` là sprite sheet cắt rời từng bộ phận nên không ghép thành hình đẹp được.
Ảnh pixel được hiển thị bằng `image-rendering: pixelated` để không bị nhoè. Muốn thêm ảnh khác: chép vào thư mục này và chèn bằng `<img src="<?= e(asset('img/ten-file.png')) ?>">`.

**Chạy offline:** tải `bootstrap.min.css` và `bootstrap.bundle.min.js` về `public/assets/vendor/` rồi sửa 2 đường link trong `main.php`.

> ⚠ Các ảnh trong game là tài nguyên có bản quyền của loạt truyện gốc. Dùng cho bản học tập/riêng tư thì ổn, **không nên dùng để kinh doanh hay đăng công khai** mà chưa có phép.

---

## 7. Bảo mật: đã làm gì, còn thiếu gì

### Đã có
- **SQL injection:** mọi truy vấn dùng prepared statement (PDO), không nối chuỗi.
- **XSS:** mọi dữ liệu in ra HTML đều qua `e()`; nội dung tin tức bị bỏ thẻ.
- **CSRF:** mọi form POST có mã bí mật, kể cả đăng xuất.
- **Session:** cookie `HttpOnly` + `SameSite=Lax`, đổi mã phiên khi đăng nhập.
- **Chống dò mật khẩu / spam:** giới hạn số lần theo IP, ô bẫy bot.
- **Không lộ thông tin:** báo lỗi đăng nhập chung; trang lỗi chỉ hiện chi tiết khi `debug = true`; cột password không bao giờ được đọc ra view.
- **Cách ly code:** `app/`, `config/`, `storage/` có `.htaccess` chặn truy cập trực tiếp.

### ⚠ Điểm yếu lớn nhất: mật khẩu lưu dạng chữ thường
Game server so sánh bằng `WHERE username = ? AND password = ?`, nên **web buộc phải lưu cùng kiểu** thì tài khoản mới đăng nhập được vào game. Đây là hạn chế của game gốc, không phải lỗi của web.
- Nếu ai đọc được database thì thấy toàn bộ mật khẩu.
- **Không mở database ra internet**, và nhắc người chơi **không dùng mật khẩu giống chỗ khác**.
- Mọi xử lý mật khẩu của web nằm ở **2 hàm duy nhất**: `AccountModel::storedPassword()` và `AccountModel::verifyPassword()`. Muốn nâng cấp, sửa 2 hàm này (dùng `password_hash`/`password_verify`) **và** sửa câu đăng nhập trong game (Java) cho khớp.

### Còn thiếu (khi định mở ra internet)
- HTTPS (chứng chỉ SSL). Không có HTTPS thì mật khẩu đi qua mạng dạng chữ rõ.
- CAPTCHA cho form đăng ký / đăng nhập (hiện chỉ có giới hạn theo IP và ô bẫy bot).
- Tạo **user MySQL riêng cho web**, chỉ cấp quyền trên các bảng cần dùng (đừng dùng `root`).
- Ghi nhật ký hành động quan trọng (đổi mật khẩu, đăng nhập sai...).

---

## 8. Thêm một trang mới (hướng dẫn từng bước)

**Ví dụ:** thêm trang `/about` ("Giới thiệu"), chưa cần database.

**Bước 1 – Khai báo đường đi** trong `app/routes.php`, thêm một dòng:
```php
'GET /about' => ['AboutController', 'index'],
```

**Bước 2 – Tạo Controller** `app/controllers/AboutController.php` (tên file = tên class):
```php
<?php
class AboutController extends Controller
{
    public function index(): void
    {
        $this->view('about/index', ['title' => 'Giới thiệu']);
    }
}
```

**Bước 3 – Tạo View** `app/views/about/index.php`:
```php
<div class="page-head"><h1 class="display">Giới thiệu</h1></div>
<section class="panel">
    <h2 class="panel__title">Về máy chủ</h2>
    <p>Nội dung của bạn ở đây.</p>
</section>
```

**Bước 4 – Thêm vào menu** (tuỳ chọn) trong mảng `$menu` ở đầu `app/views/layouts/main.php`:
```php
'about' => 'Giới thiệu',
```

Mở `index.php?r=about` là thấy trang mới, đã có sẵn menu, chân trang và giao diện truyện tranh.

**Nếu trang cần dữ liệu từ database:** thêm một hàm vào Model (hoặc tạo Model mới trong `app/models/`), Controller gọi hàm đó rồi truyền kết quả vào view qua mảng thứ hai của `$this->view(...)`. Nhớ luôn in dữ liệu bằng `e()`.

---

## 9. Lộ trình: nên phát triển tính năng nào tiếp theo

Thứ tự gợi ý đi từ dễ → khó, mỗi mục ghi rõ dữ liệu cần dùng. Các tên bảng/cột dưới đây có trong file SQL của game; **riêng ý nghĩa chính xác của từng cột nên xác nhận lại bằng cách đọc code game** trước khi làm.

### 🟢 Dễ (vài giờ, chỉ đọc dữ liệu)

| Tính năng | Gợi ý cách làm / trạng thái | Dữ liệu |
|---|---|---|
| ✅ **Trang Giới thiệu / Luật chơi / Hướng dẫn** | Đã làm: mục 5.13 |
| ✅ **Bảng xếp hạng khác** (top nạp, top đệ tử, diệt boss) | Đã làm: mục 5.9 |
| ✅ **Xếp hạng bang hội** | Đã làm: mục 5.10 |
| ✅ **Phân trang tin tức** | Đã làm: mục 5.12 |
| ✅ **Trang chi tiết nhân vật** | Đã làm: mục 5.11 |
| ✅ **Hình minh hoạ từ ảnh game** | Đã làm: mục 6 |
| **Việc nhỏ nên làm tiếp** | Ô tìm kiếm nhân vật theo tên (`WHERE name LIKE ?`), lọc bảng xếp hạng theo hành tinh, trang bang hiển thị người sáng lập, thêm ảnh banner riêng của bạn | `player.name`, `player.gender` |

### 🟡 Vừa (vài ngày, có ghi dữ liệu)

| Tính năng | Gợi ý cách làm | Lưu ý |
|---|---|---|
| **Trang quản trị đăng tin** | `AdminController`, chỉ cho `account.is_admin = 1`; form thêm/sửa/xoá bài, đặt `status` | Kiểm tra quyền ở **mọi** hàm admin, không chỉ ẩn nút |
| **Quên mật khẩu qua email** | Bảng mới lưu token hết hạn, gửi mail bằng PHPMailer | Cần dịch vụ gửi mail (SMTP) |
| **Giftcode** | Trang nhập mã, ghi `gift_code_histories` | Phải đọc code game xử lý giftcode để **thưởng đúng cách**; web ghi sai sẽ lệch dữ liệu |
| **Hệ thống mời bạn** | Trang hiển thị mã giới thiệu | Bảng `account` có `ref`, `gioithieu`, `diemgioithieu`; cần hiểu cách game dùng |
| **Quản lý email / số điện thoại** | Cho người chơi cập nhật liên hệ | `account.email`, `account.phone` |
| **Lịch sử giao dịch của tôi** | Đọc lịch sử, chỉ hiện của chính người đó | `history_transaction`, `history_receive_goldbar` |

### 🔴 Khó / đổi hạ tầng

| Tính năng | Vì sao khó | Gợi ý |
|---|---|---|
| **Mã hoá mật khẩu** | Phải sửa **cả game (Java) lẫn web** cùng lúc, và chuyển dữ liệu cũ | Sửa 2 hàm trong `AccountModel` + `LocalLogin`/`GodGK` của game; làm trên bản sao database trước |
| **Hiển thị số người đang online / trạng thái máy chủ** | **Đừng "ping" cổng game từ web**: game giới hạn 5 kết nối mỗi IP nên có thể chặn nhầm chính người chơi | Cho game tự ghi số người online vào một bảng nhỏ định kỳ, web chỉ đọc bảng đó |
| **Đưa web ra internet an toàn** | HTTPS, VirtualHost, user MySQL riêng, tường lửa | Xem mục 7 |
| **Nạp tiền / thanh toán** | Dính pháp lý, thanh toán, gian lận; game là bản sao của sản phẩm thương mại | Khuyên **không làm**. Nếu cần quà cho người chơi, làm trang admin cộng thủ công |

### 🛠 Cải thiện chất lượng code (khi project lớn dần)
- **`.env` thay cho `config.php`:** để mật khẩu database không nằm trong code (và không lỡ đưa lên GitHub).
- **Composer + PSR-4 + namespace:** thay cho autoload tự viết, chuẩn công nghiệp, dễ dùng thư viện ngoài.
- **Kiểm thử tự động (PHPUnit):** các `Validator`, `AccountModel` rất dễ viết test.
- **Cache bảng xếp hạng:** lưu kết quả 30–60 giây thay vì truy vấn mỗi lần xem (khi nhiều người truy cập).
- **API JSON:** để làm ứng dụng điện thoại hoặc bot Discord đọc xếp hạng.
- **Đa ngôn ngữ:** tách chữ ra file riêng thay vì viết thẳng trong view.

**Gợi ý thứ tự tiếp theo cho người học lại:** ô tìm kiếm nhân vật → trang quản trị đăng tin → quên mật khẩu → (đọc code game để) giftcode.

---

## 10. Xử lý lỗi thường gặp

| Hiện tượng | Nguyên nhân thường gặp | Cách xử lý |
|---|---|---|
| Trang báo **"Có lỗi xảy ra"** (kèm `SQLSTATE[HY000] [1049] Unknown database`) | Tên database sai | Sửa `db.name` trong `config/config.php` cho trùng `server.db.name` của game |
| `SQLSTATE[HY000] [2002]` / `Connection refused` | MySQL chưa bật | Bấm Start MySQL trong XAMPP |
| `SQLSTATE[HY000] [1045] Access denied` | Sai user/mật khẩu MySQL | Sửa `db.user`, `db.pass` |
| `Base table or view not found: 1146 Table '...news_posts' doesn't exist` | Database của bạn thiếu bảng đó | Import đủ file SQL của game, hoặc tạm thời bỏ trang tin tức |
| Đăng ký báo lỗi cột không tồn tại | Database khác phiên bản với file SQL mẫu | Gửi thông báo lỗi để đối chiếu cột |
| Web hiện nhưng **mất hết màu, không có giao diện** | Địa chỉ file CSS sai | Mở web qua `.../bladekun-web/public/` hoặc `.../bladekun-web/` (đừng mở file `.php` trực tiếp từ ổ đĩa) |
| `/rank` báo **404 của Apache** | Chưa bật URL đẹp đúng cách | Để `pretty_urls => false`, hoặc bật `mod_rewrite` + `AllowOverride All` |
| Đăng ký xong, vào game **không đăng nhập được** | Mật khẩu có ký tự lạ, hoặc game đang dùng login server khác | Dùng mật khẩu chỉ gồm chữ/số; kiểm tra game có đăng nhập được bằng tài khoản mẫu không |
| Bị báo "đăng nhập sai quá nhiều lần" | Chống dò mật khẩu | Chờ 10 phút, hoặc xoá file trong `storage/ratelimit/` |
| Muốn xem chi tiết lỗi | — | Đặt `'debug' => true` trong `config.php` (nhớ tắt khi mở ra internet) |

---

## 11. Đã kiểm thử những gì

Web được kiểm thử bằng **72 kiểm tra tự động** chạy trên PHP 8.3 với một database giả lập **SQLite có cùng tên bảng và cột** như database game (kể cả `clan_sv1`, `kill_boss`, `tong_nap`). Gồm: xếp hạng đúng thứ tự và loại admin/tài khoản bị khoá, 4 tab xếp hạng và giá trị `type` bậy, hồ sơ nhân vật (admin/khoá/id sai → 404, không lộ thông tin riêng tư), bang hội, phân trang (trang âm / quá lớn), 3 trang chữ, đăng ký/đăng nhập/đổi mật khẩu/đăng xuất, trùng tên (kể cả khác hoa-thường), CSRF giả mạo, ô bẫy bot, giới hạn đăng nhập sai, tin tức (bản nháp, chống XSS), 404/405. Bộ kiểm tra được chạy ở **2 kiểu địa chỉ**: mặc định (`?r=`) và URL đẹp. Giao diện được chụp và kiểm tra ở màn hình máy tính (1280px) và điện thoại (390px), không có trang nào bị tràn ngang. Bản chụp dùng Bootstrap có sẵn trong máy kiểm thử (khác màu mặc định một chút), nên màu thực tế trên máy bạn có thể lệch nhẹ.

**Chưa kiểm thử:**
- Chạy với **MySQL/MariaDB thật** và **Apache của XAMPP** (môi trường kiểm thử không có hai thứ này). Các câu SQL đều là SQL chuẩn và tên cột đã đối chiếu với file SQL của game, nhưng lần chạy đầu trên máy bạn có thể phát sinh khác biệt nhỏ, nhất là nếu database của bạn khác file SQL mẫu.
- Đăng nhập **vào game** bằng tài khoản tạo từ web (cần chạy game server và client thật).
- Hiển thị với font **Bangers** thật (môi trường kiểm thử không có mạng nên chỉ thấy font dự phòng).

Nếu gặp lỗi khi chạy trên máy bạn, gửi mình thông báo lỗi (bật `debug` để thấy chi tiết) mình sẽ sửa tiếp.

## Hình nhân vật + trang bị (đọc từ `player.items_body`)

- Hiện ở panel **Nhân vật** (trang Tài khoản) và trang hồ sơ công khai `?r=player&id=…`.
- Code: `app/models/CharacterModel.php` (đọc JSON items_body, tra `item_template`, `item_option_template`, `cai_trang`, ghép đầu/thân/chân),
  `app/views/partials/character.php` (giao diện), CSS cuối `style.css` (`.char-stage`, `.eq-grid`).
- Ảnh được cắt sẵn từ file game bằng `tools/build_sprites.py` (cần Python + Pillow):
  `python tools/build_sprites.py --game "D:/duong/dan/Nro-Online"`
  Chạy lại khi thêm trang phục/item mới. Ảnh nằm ở `public/assets/img/parts/` (nhân vật) và `public/assets/img/icons/` (icon trang bị).
- Giới hạn: repo game chỉ chứa ảnh cho khoảng 37 đầu, 25 thân, 25 chân (bộ đồ cơ bản và vài cải trang). Trang phục khác do client
  tải từ máy chủ ảnh nên không có trong repo → hiển thị bộ cơ bản theo hành tinh (có ghi chú), nhưng **danh sách trang bị + icon + chỉ số vẫn đủ**.
  Nếu client của bạn (thư mục Hashirama_Data) có sẵn các tờ ảnh `Big*.png` đầy đủ hơn, thay vào `resources/normal/data/4/img/` rồi chạy lại script.
- Icon trang bị: script chỉ copy icon của item trong file SQL mẫu. Item mới → copy thêm `resources/normal/image/4/icon/<icon_id>.png` vào `public/assets/img/icons/`.
