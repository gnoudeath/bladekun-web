<?php
/**
 * TRANG HƯỚNG DẪN TÂN THỦ – trang chữ tĩnh (PageController::guide).
 * Chỉ nêu kiến thức chung; chi tiết (chỉ số, vật phẩm, sự kiện) tuỳ máy chủ nên hãy bổ sung theo bản của bạn.
 */
$planets = [
    ['Trái Đất', 'house-earth', 'Hành tinh quê hương của loài người, nhiều nhân vật cân bằng, dễ làm quen cho người mới.'],
    ['Namếc',    'tree-namek',  'Hành tinh xanh ngọc, chiến binh có sức bền tốt và nhiều chiêu hỗ trợ, hồi phục.'],
    ['Xayda',    'pod-xayda',   'Hành tinh của những chiến binh hiếu chiến, thiên về sức tấn công mạnh.'],
];
?>
<div class="page-head">
    <h1 class="display-title">Hướng dẫn tân thủ</h1>
    <p>Những điều cần biết trước khi vào chiến đấu.</p>
    <?= scene('tower-c', 'page-head__deco d-none d-md-block') ?>
</div>

<section class="panel mb-4" aria-labelledby="g1">
    <h2 class="panel__title" id="g1">3 bước đầu tiên</h2>
    <ol class="steps mb-0">
        <li><strong>Đăng ký tài khoản</strong>Tạo tài khoản ở <a href="<?= e(url('register')) ?>">trang đăng ký</a>, nhớ kỹ tên đăng nhập và mật khẩu.</li>
        <li><strong>Tải và cài game</strong>Lấy bản client ở trang <a href="<?= e(url('download')) ?>">Tải game</a>, giải nén rồi mở lên.</li>
        <li><strong>Tạo nhân vật</strong>Lần đầu đăng nhập, game sẽ cho bạn chọn tên và hành tinh. Mỗi tài khoản chỉ có MỘT nhân vật.</li>
    </ol>
</section>

<section class="panel mb-4" aria-labelledby="g2">
    <h2 class="panel__title" id="g2">Chọn hành tinh</h2>
    <div class="row g-3">
        <?php foreach ($planets as [$name, $img, $text]): ?>
            <div class="col-md-4">
                <div class="planet-card h-100">
                    <?= scene($img, 'planet-card__img') ?>
                    <h3><?= e($name) ?></h3>
                    <p class="mb-0"><?= e($text) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="row g-4">
    <div class="col-lg-6">
        <section class="panel h-100" aria-labelledby="g3">
            <h2 class="panel__title" id="g3">Lối chơi cơ bản</h2>
            <ul class="mb-0">
                <li><strong>Luyện tập</strong>: đánh quái, tăng sức mạnh để leo <a href="<?= e(url('rank')) ?>">bảng xếp hạng</a>.</li>
                <li><strong>Đệ tử</strong>: có đệ tử đồng hành chiến đấu, sức mạnh đệ tử có bảng xếp hạng riêng.</li>
                <li><strong>Săn boss</strong>: hạ boss để nhận phần thưởng và tăng thành tích.</li>
                <li><strong>Bang hội</strong>: lập hoặc gia nhập <a href="<?= e(url('clans')) ?>">bang</a> để cùng nhau đi săn, cùng lên hạng.</li>
            </ul>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel h-100" aria-labelledby="g4">
            <h2 class="panel__title" id="g4">7 viên ngọc rồng</h2>
            <p>Thu thập đủ <strong>7 viên ngọc rồng</strong> rồi triệu hồi rồng thần để ước. Phần thưởng cụ thể tuỳ máy chủ.</p>
            <div class="balls mb-3" aria-hidden="true">
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <img src="<?= e(asset('img/balls/' . $i . '.png')) ?>" alt="" width="42" height="42" style="--i:<?= $i ?>">
                <?php endfor; ?>
            </div>
            <p class="small text-body-secondary mb-0">Mẹo: đọc <a href="<?= e(url('rules')) ?>">luật chơi</a> và theo dõi <a href="<?= e(url('news')) ?>">tin tức</a> để không bỏ lỡ sự kiện.</p>
        </section>
    </div>
</div>
