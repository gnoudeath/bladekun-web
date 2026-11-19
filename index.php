<?php
// Dành cho người mở http://localhost/bladekun-web/ trong XAMPP: chuyển sang thư mục public/.
// (Khi dùng VirtualHost trỏ thẳng vào public/ thì file này không bao giờ được gọi.)
header('Location: public/');
exit;
