<?php
/**
 * BẢNG ĐƯỜNG ĐI – nơi duy nhất khai báo "địa chỉ nào do Controller nào xử lý".
 *
 * Muốn thêm một trang mới:
 *   1. Thêm một dòng vào đây.
 *   2. Viết hàm tương ứng trong Controller (nếu cần Controller mới, tạo file trong app/controllers).
 *   3. Tạo file view trong app/views.
 * (Hướng dẫn từng bước có trong README.md, mục "Thêm một trang mới".)
 *
 * Khoá có dạng 'PHƯƠNG-THỨC /đường-dẫn'. GET = xem trang, POST = gửi form.
 */
return [
    // Trang công khai
    'GET /'            => ['HomeController',     'index'],
    'GET /rank'        => ['RankController',     'index'],
    'GET /clans'       => ['ClanController',     'index'],
    'GET /clan'        => ['ClanController',     'show'],
    'GET /player'      => ['PlayerController',   'show'],
    'GET /about'       => ['PageController',     'about'],
    'GET /rules'       => ['PageController',     'rules'],
    'GET /guide'       => ['PageController',     'guide'],
    'GET /news'        => ['NewsController',     'index'],
    'GET /news/view'   => ['NewsController',     'show'],
    'GET /download'    => ['DownloadController', 'index'],

    // Diễn đàn (xem: ai cũng xem được; đăng/bình luận/thích: phải đăng nhập)
    'GET /forum'          => ['ForumController', 'index'],
    'GET /forum/post'     => ['ForumController', 'show'],
    'POST /forum/post'    => ['ForumController', 'create'],
    'POST /forum/comment' => ['ForumController', 'comment'],
    'POST /forum/like'    => ['ForumController', 'like'],
    'POST /forum/delete'  => ['ForumController', 'delete'],
    'POST /forum/pin'     => ['ForumController', 'pin'],

    // Đăng ký / đăng nhập / đăng xuất
    'GET /register'    => ['AuthController',     'showRegister'],
    'POST /register'   => ['AuthController',     'register'],
    'GET /login'       => ['AuthController',     'showLogin'],
    'POST /login'      => ['AuthController',     'login'],
    'POST /logout'     => ['AuthController',     'logout'],

    // Khu vực tài khoản (phải đăng nhập)
    'GET /account'            => ['AccountController', 'index'],
    'GET /account/password'   => ['AccountController', 'showPassword'],
    'POST /account/password'  => ['AccountController', 'changePassword'],
];
