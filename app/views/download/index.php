<?php
/** TRANG TẢI GAME. Biến: $clientUrl (có thể rỗng), $serverHost, $serverPort. Giá trị lấy từ config/config.php. */
?>
<div class="page-head">
    <h1 class="display-title">Tải game</h1>
    <p>Làm theo 4 bước để vào chiến đấu.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <section class="panel h-100" aria-labelledby="dl-steps">
            <h2 class="panel__title" id="dl-steps">Hướng dẫn</h2>
            <ol class="steps">
                <li>
                    <strong>Tạo tài khoản</strong>
                    <?php if (Auth::check()): ?>Bạn đã có tài khoản và đang đăng nhập.
                    <?php else: ?><a href="<?= e(url('register')) ?>">Đăng ký ở đây</a> nếu chưa có.<?php endif; ?>
                </li>
                <li><strong>Tải bản game (client)</strong>Dùng nút tải bên dưới, giải nén vào một thư mục riêng.</li>
                <li><strong>Mở game</strong>Chạy file chương trình trong thư mục vừa giải nén.</li>
                <li><strong>Đăng nhập</strong>Dùng đúng tên đăng nhập và mật khẩu đã tạo trên web.</li>
            </ol>

            <div class="mt-4">
                <?php if ($clientUrl !== ''): ?>
                    <a class="btn btn-comic btn-comic--red btn-lg" href="<?= e($clientUrl) ?>" rel="noopener">Tải bản Windows</a>
                <?php else: ?>
                    <span class="btn btn-comic btn-comic--light btn-lg disabled" aria-disabled="true">Chưa có link tải</span>
                    <div class="small text-body-secondary mt-2">Quản trị viên: dán link tải vào mục <code>download.client_url</code> trong <code>config/config.php</code>.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="col-lg-4">
        <section class="panel h-100" aria-labelledby="dl-server">
            <h2 class="panel__title" id="dl-server">Máy chủ</h2>
            <dl class="kv">
                <dt>Địa chỉ</dt><dd><code><?= e($serverHost) ?></code></dd>
                <dt>Cổng</dt><dd><code><?= (int) $serverPort ?></code></dd>
            </dl>
            <p class="small text-body-secondary mb-0">
                Thông tin này chỉ để tham khảo; bản client đã được cài sẵn địa chỉ máy chủ.
            </p>
        </section>
    </div>
</div>
