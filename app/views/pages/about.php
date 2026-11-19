<?php
/** TRANG GIỚI THIỆU – trang chữ tĩnh (PageController::about). Sửa nội dung trực tiếp ở file này. */
?>
<div class="page-head">
    <h1 class="display-title">Giới thiệu</h1>
    <p>Bladekun là gì và vì sao nó ra đời?</p>
    <?= scene('saucer', 'page-head__deco d-none d-md-block') ?>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <section class="panel h-100" aria-labelledby="ab1">
            <h2 class="panel__title" id="ab1">Về máy chủ</h2>
            <p><strong><?= e(config('app.name')) ?></strong> là máy chủ Ngọc Rồng <strong>tự làm để học tập và giải trí</strong>:
            chạy trên máy cá nhân, không thu phí, không vì lợi nhuận. Người chơi tạo tài khoản ngay trên website này rồi
            đăng nhập vào game bằng cùng tài khoản đó.</p>
            <p>Website và game <strong>dùng chung một database</strong>, vì vậy bảng xếp hạng, bang hội và hồ sơ nhân vật
            bạn thấy ở đây chính là dữ liệu thật trong game.</p>
            <h3 class="h5 mt-4">Website có gì?</h3>
            <ul class="mb-0">
                <li><a href="<?= e(url('rank')) ?>">Bảng xếp hạng</a>: sức mạnh, đệ tử, diệt boss, top nạp.</li>
                <li><a href="<?= e(url('clans')) ?>">Bang hội</a>: bang mạnh nhất và danh sách thành viên.</li>
                <li>Hồ sơ từng chiến binh: bấm tên trong bảng xếp hạng.</li>
                <li><a href="<?= e(url('news')) ?>">Tin tức</a>, <a href="<?= e(url('guide')) ?>">hướng dẫn tân thủ</a> và <a href="<?= e(url('rules')) ?>">luật chơi</a>.</li>
            </ul>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="panel h-100" aria-labelledby="ab2">
            <h2 class="panel__title" id="ab2">Lưu ý</h2>
            <p class="mb-2">Đây là dự án học tập, <strong>phi thương mại</strong>.</p>
            <p class="mb-2">Website không liên kết với chủ sở hữu bản quyền của loạt truyện gốc.</p>
            <p class="mb-3">Máy chủ có thể tạm đóng bất cứ lúc nào để bảo trì hoặc để người làm học thêm.</p>
            <div class="balls" aria-hidden="true">
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <img src="<?= e(asset('img/balls/' . $i . '.png')) ?>" alt="" width="34" height="34" style="--i:<?= $i ?>">
                <?php endfor; ?>
            </div>
        </section>
    </div>
</div>
