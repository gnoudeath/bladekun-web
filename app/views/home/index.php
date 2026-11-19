<?php
/**
 * TRANG CHỦ
 * Biến: $topPlayers (top 5), $latestNews (3 tin), $accountCount, $playerCount
 * Bố cục dùng lưới Bootstrap: .row / .col-*  (12 cột; col-lg-7 = chiếm 7/12 trên màn hình rộng,
 * còn trên điện thoại các cột tự xếp chồng lên nhau).
 */
$first = $topPlayers[0] ?? null;
?>

<!-- ============ HERO: lời chào + đảo nổi (ảnh nền map lấy từ game) mang thẻ "người mạnh nhất" ============ -->
<section class="hero mb-4" aria-labelledby="hero-title">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="comic-tag comic-tag--blue mb-3">Máy chủ đang mở cửa!</span>
            <h1 class="display-title" id="hero-title">Sức mạnh<br>không giới hạn!</h1>
            <p class="hero__text mt-3">
                Tạo chiến binh của riêng bạn, luyện tập, săn boss và leo lên đỉnh bảng xếp hạng.
                Đăng ký chỉ mất một phút!
            </p>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php if (Auth::check()): ?>
                    <a class="btn btn-comic btn-comic--red btn-lg" href="<?= e(url('account')) ?>">Vào tài khoản</a>
                <?php else: ?>
                    <a class="btn btn-comic btn-comic--red btn-lg" href="<?= e(url('register')) ?>">Đăng ký ngay</a>
                <?php endif; ?>
                <a class="btn btn-comic btn-comic--light btn-lg" href="<?= e(url('download')) ?>">Tải game</a>
            </div>

            <!-- 7 viên ngọc rồng nhún nhảy lần lượt (icon thật của game; --i trễ animation theo thứ tự) -->
            <div class="balls" aria-hidden="true">
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <img src="<?= e(asset('img/balls/' . $i . '.png')) ?>" alt="" width="46" height="46" style="--i:<?= $i ?>">
                <?php endfor; ?>
            </div>
        </div>

        <div class="col-lg-5 text-center">
            <div class="champion">
                <?= scene('saucer', 'champion__saucer') ?>
                <img class="champion__island" src="<?= e(asset('img/scene/86.png')) ?>" alt="" loading="lazy">
                <div class="champion__card">
                    <span class="champion__label">Mạnh nhất!</span>
                    <?php if ($first): ?>
                        <span class="champion__name"><?= e($first['name']) ?></span>
                        <span class="champion__power"><?= e(format_number($first['power'])) ?> sức mạnh</span>
                    <?php else: ?>
                        <span class="champion__name">Còn trống</span>
                        <span class="champion__power">Hãy là người đầu tiên!</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ Số liệu tổng quan ============ -->
<section class="row g-3 mb-4" aria-label="Số liệu máy chủ">
    <div class="col-12 col-sm-4">
        <div class="stat-card"><img src="<?= e(asset('img/tomato.png')) ?>" alt="" width="44" height="44">
            <div><div class="stat-card__num"><?= e(format_number($accountCount)) ?></div><div class="stat-card__label">Tài khoản</div></div></div>
    </div>
    <div class="col-6 col-sm-4">
        <div class="stat-card"><img src="<?= e(asset('img/gem.png')) ?>" alt="" width="44" height="44">
            <div><div class="stat-card__num"><?= e(format_number($playerCount)) ?></div><div class="stat-card__label">Chiến binh</div></div></div>
    </div>
    <div class="col-12 col-sm-4">
        <div class="stat-card"><img src="<?= e(asset('img/balls/4.png')) ?>" alt="" width="44" height="44">
            <div><div class="stat-card__num"><?= $first ? e(format_number($first['power'])) : '—' ?></div><div class="stat-card__label">Sức mạnh cao nhất</div></div></div>
    </div>
</section>

<!-- ============ 3 hành tinh: ảnh cảnh vật lấy từ game + số chiến binh mỗi hành tinh ============ -->
<section class="mb-4" aria-labelledby="planets-title">
    <h2 class="visually-hidden" id="planets-title">Ba hành tinh</h2>
    <div class="row g-3">
        <?php foreach ([
            0 => ['Trái Đất', 'house-earth', 'tree-earth'],
            1 => ['Namếc',    'dome-namek',  'tree-namek'],
            2 => ['Xayda',    'pod-xayda',   'rock-xayda'],
        ] as $g => [$name, $main, $side]): ?>
            <div class="col-md-4">
                <a class="planet-card planet-card--<?= $g ?> h-100" href="<?= e(url('guide')) ?>">
                    <span class="planet-card__stage"><?= scene($side, 'planet-card__side') ?><?= scene($main, 'planet-card__img') ?></span>
                    <span class="planet-card__name"><?= e($name) ?></span>
                    <span class="planet-card__count"><?= e(format_number($planetCounts[$g] ?? 0)) ?> chiến binh</span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<div class="row g-4 mb-4">
    <!-- ============ Top 5 ============ -->
    <div class="col-lg-7">
        <section class="panel h-100" aria-labelledby="top-title">
            <h2 class="panel__title" id="top-title">Top 5 chiến binh</h2>
            <?php if ($topPlayers): ?>
                <div class="table-responsive">
                    <table class="table rank-table align-middle mb-0">
                        <thead><tr><th scope="col" class="rank-col">Hạng</th><th scope="col">Tên</th><th scope="col" class="num text-end">Sức mạnh</th></tr></thead>
                        <tbody>
                        <?php foreach ($topPlayers as $i => $p): ?>
                            <tr>
                                <td class="rank-col"><span class="rank-badge rank-badge--<?= $i + 1 ?>"><?= $i + 1 ?></span></td>
                                <td class="rank-name"><a class="player-link" href="<?= e(url('player', ['id' => $p['id']])) ?>"><?= e($p['name']) ?></a></td>
                                <td class="num text-end"><?= e(format_number($p['power'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-center mt-3 mb-0"><a class="btn btn-comic btn-comic--blue btn-sm" href="<?= e(url('rank')) ?>">Xem toàn bộ bảng</a></p>
            <?php else: ?>
                <div class="empty"><span class="display-title">Chưa có ai!</span>Chưa có chiến binh nào trên bảng xếp hạng.</div>
            <?php endif; ?>
        </section>
    </div>

    <!-- ============ Bắt đầu trong 3 bước ============ -->
    <div class="col-lg-5">
        <section class="panel h-100" aria-labelledby="start-title">
            <h2 class="panel__title" id="start-title">Bắt đầu nhanh</h2>
            <ol class="steps">
                <li><strong>Tạo tài khoản</strong>Điền tên đăng nhập, email và mật khẩu ở trang đăng ký.</li>
                <li><strong>Tải game</strong>Lấy bản client ở trang <a href="<?= e(url('download')) ?>">Tải game</a>.</li>
                <li><strong>Vào chiến đấu</strong>Đăng nhập bằng đúng tài khoản vừa tạo và tạo nhân vật.</li>
            </ol>
        </section>
    </div>
</div>

<!-- ============ Tin mới ============ -->
<section class="panel mt-5" aria-labelledby="news-title">
    <h2 class="panel__title" id="news-title">Tin mới</h2>
    <?php if ($latestNews): ?>
        <div class="row g-3">
            <?php foreach ($latestNews as $n): ?>
                <div class="col-md-4">
                    <a class="news-card h-100" href="<?= e(url('news/view', ['id' => $n['id']])) ?>">
                        <span class="news-card__date"><?= e(format_datetime($n['created_at'])) ?></span>
                        <span class="news-card__title"><?= e($n['title']) ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty"><span class="display-title">Yên ắng...</span>Chưa có tin tức nào được đăng.</div>
    <?php endif; ?>
</section>
