<?php
/**
 * KHUNG TRANG CHUNG – mọi trang đều được đặt vào đây.
 * Biến có sẵn: $content (HTML của trang con), $title (tiêu đề trang, có thể null).
 *
 * Giao diện = Bootstrap 5 (khung lưới, thanh menu, form, bảng, nút) + public/assets/css/style.css
 * (lớp "da truyện tranh" phủ lên trên Bootstrap: viền đen, bóng đổ cứng, tia sáng, chấm bán tông).
 */
$pageTitle = !empty($title) ? $title . ' – ' . config('app.name') : config('app.name') . ' – ' . config('app.tagline');

// Các mục menu: 'đường-dẫn' => 'chữ hiển thị'
$menu = [
    ''         => 'Trang chủ',
    'rank'     => 'Xếp hạng',
    'clans'    => 'Bang hội',
    'news'     => 'Tin tức',
    'forum'    => 'Diễn đàn',
    'download' => 'Tải game',
];
// Nhóm "Thông tin" (menu thả xuống): các trang chữ tĩnh
$infoMenu = [
    'about' => 'Giới thiệu',
    'guide' => 'Hướng dẫn',
    'rules' => 'Luật chơi',
];
$infoActive = false;
foreach ($infoMenu as $p => $_) { $infoActive = $infoActive || is_current($p); }
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e(config('app.tagline')) ?>">
    <meta name="theme-color" content="#1d3fa8">

    <!-- Bootstrap 5.3 (CSS) từ CDN. Cần mạng; nếu muốn chạy offline, tải file về public/assets/vendor/ rồi đổi đường dẫn. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" crossorigin="anonymous">
    <!-- Font: Bangers (chữ truyện tranh) + Be Vietnam Pro (chữ nội dung, hỗ trợ tiếng Việt). -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Be+Vietnam+Pro:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Giao diện truyện tranh của riêng web: PHẢI nằm SAU Bootstrap để ghi đè được -->
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
    <a class="skip-link visually-hidden-focusable" href="#main">Bỏ qua menu, tới nội dung</a>

    <!-- ============ THANH MENU (Bootstrap navbar) ============ -->
    <nav class="navbar navbar-expand-lg site-nav" aria-label="Menu chính">
        <div class="container">
            <a class="navbar-brand logo" href="<?= e(url('')) ?>" aria-label="<?= e(config('app.name')) ?> – về trang chủ">
                <img class="logo__ball" src="<?= e(asset('img/balls/4.png')) ?>" alt="" width="40" height="40">
                <span>
                    <span class="logo__name"><?= e(config('app.name')) ?></span>
                    <span class="logo__sub d-none d-sm-block">Ngọc Rồng • Máy chủ tự làm</span>
                </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#site-nav"
                    aria-controls="site-nav" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="site-nav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <?php foreach ($menu as $path => $label): ?>
                        <li class="nav-item">
                            <a class="nav-link<?= is_current($path) ? ' active' : '' ?>" href="<?= e(url($path)) ?>"<?= is_current($path) ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
                        </li>
                    <?php endforeach; ?>

                    <!-- Menu thả xuống (Bootstrap dropdown) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?= $infoActive ? ' active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Thông tin</a>
                        <ul class="dropdown-menu comic-dropdown">
                            <?php foreach ($infoMenu as $path => $label): ?>
                                <li><a class="dropdown-item<?= is_current($path) ? ' active' : '' ?>" href="<?= e(url($path)) ?>"><?= e($label) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>

                    <?php if (Auth::check()): ?>
                        <li class="nav-item">
                            <a class="nav-link<?= is_current('account') ? ' active' : '' ?>" href="<?= e(url('account')) ?>">Tài khoản</a>
                        </li>
                        <li class="nav-item">
                            <!-- Đăng xuất dùng POST + mã CSRF, không phải đường link thường -->
                            <form method="post" action="<?= e(url('logout')) ?>" class="m-0">
                                <?= Csrf::field() ?>
                                <button class="btn btn-comic btn-comic--light btn-sm ms-lg-2 mt-2 mt-lg-0" type="submit">Thoát</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link<?= is_current('login') ? ' active' : '' ?>" href="<?= e(url('login')) ?>">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-comic btn-comic--red btn-sm ms-lg-2 mt-2 mt-lg-0" href="<?= e(url('register')) ?>">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main id="main" class="py-4">
        <div class="container">
            <?php View::partial('flash'); ?>
            <?= $content /* HTML của trang con (đã được escape ở bên trong từng view) */ ?>
        </div>
    </main>

    <!-- ============ DẢI CẢNH: cây, nhà, phi thuyền, toà tháp... (ảnh nền map của game) đứng trên mặt đất ============ -->
    <div class="scenery" aria-hidden="true">
        <?php foreach (['tree-earth', 'house-earth', 'palm', 'dome-namek', 'tree-namek', 'tower-b', 'pagoda', 'pod-xayda', 'tree-round'] as $i => $n): ?>
            <?= scene($n, 'scenery__item' . ($i > 3 && $i % 2 ? ' d-none d-md-block' : '')) ?>
        <?php endforeach; ?>
    </div>

    <!-- ============ CHÂN TRANG: dải đồi cỏ lấy từ ảnh nền của game ============ -->
    <footer class="site-footer">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-md-5">
                    <h2 class="fs-3"><?= e(config('app.name')) ?></h2>
                    <p class="mb-2"><?= e(config('app.tagline')) ?></p>
                    <div class="balls" aria-hidden="true">
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <img src="<?= e(asset('img/balls/' . $i . '.png')) ?>" alt="" width="28" height="28" style="--i:<?= $i ?>">
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <h2 class="fs-6 text-uppercase">Liên kết</h2>
                    <ul class="list-unstyled mb-0">
                        <li><a href="<?= e(url('rank')) ?>">Bảng xếp hạng</a></li>
                        <li><a href="<?= e(url('clans')) ?>">Bang hội</a></li>
                        <li><a href="<?= e(url('guide')) ?>">Hướng dẫn tân thủ</a></li>
                        <li><a href="<?= e(url('rules')) ?>">Luật chơi</a></li>
                        <li><a href="<?= e(url('news')) ?>">Tin tức</a></li>
                        <li><a href="<?= e(url('download')) ?>">Tải game</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <h2 class="fs-6 text-uppercase">Tài khoản</h2>
                    <ul class="list-unstyled mb-0">
                        <?php if (Auth::check()): ?>
                            <li><a href="<?= e(url('account')) ?>">Tài khoản của tôi</a></li>
                            <li><a href="<?= e(url('account/password')) ?>">Đổi mật khẩu</a></li>
                        <?php else: ?>
                            <li><a href="<?= e(url('login')) ?>">Đăng nhập</a></li>
                            <li><a href="<?= e(url('register')) ?>">Đăng ký</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <p class="legal mt-4 mb-0">
                Dự án học tập, phi thương mại, làm cho vui. Website không liên kết với chủ sở hữu bản quyền
                của loạt truyện gốc. Hình ảnh trang trí (ngọc rồng, đảo, icon) lấy từ tài nguyên game của chủ dự án.
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS (menu thu gọn trên điện thoại, đóng thông báo...) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
