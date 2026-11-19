<?php /** Hiện khi chưa tạo bảng diễn đàn trong database. */ ?>
<div class="page-head"><h1 class="display-title">Diễn đàn</h1></div>
<section class="panel">
    <h2 class="panel__title">Cần tạo bảng dữ liệu một lần</h2>
    <p>Diễn đàn dùng 3 bảng mới (<code>forum_posts</code>, <code>forum_comments</code>, <code>forum_likes</code>) và không đụng vào bảng của game.</p>
    <ol>
        <li>Mở Navicat, chọn đúng database của game (ví dụ <code>nro-d</code>).</li>
        <li>Chuột phải → <strong>Chạy tệp SQL</strong> → chọn file <code>database/forum.sql</code> trong thư mục web.</li>
        <li>Tải lại trang này.</li>
    </ol>
</section>
