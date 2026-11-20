<?php
/**
 * TRANG TÀI KHOẢN CỦA TÔI
 * Biến: $account (thông tin tài khoản, KHÔNG có mật khẩu), $player (nhân vật hoặc null), $rank (hạng hoặc null)
 */
$neverLoggedIn = ($account['last_time_login'] === AccountModel::NEVER_LOGGED_IN);
?>
<div class="page-head"><h1 class="display-title">Xin chào, <?= e($account['username']) ?>!</h1></div>

<?php if ((int) $account['active'] === 0): ?>
    <div class="alert alert-warning comic-alert" role="note">
        <strong>Tài khoản chưa được kích hoạt.</strong>
        Trong game, tài khoản chưa kích hoạt bị hạn chế một số tính năng (chat thế giới, giao dịch, thách đấu).
        Hãy liên hệ quản trị viên để được kích hoạt.
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- ============ Thông tin tài khoản ============ -->
    <div class="col-lg-6">
        <section class="panel h-100" aria-labelledby="acc-title">
            <h2 class="panel__title" id="acc-title">Tài khoản</h2>
            <dl class="kv">
                <dt>Tên đăng nhập</dt><dd><?= e($account['username']) ?></dd>
                <dt>Email</dt><dd><?= e($account['email'] ?: '—') ?></dd>
                <dt>Ngày tạo</dt><dd><?= e(format_datetime($account['create_time'])) ?></dd>
                <dt>Đăng nhập game gần nhất</dt><dd><?= $neverLoggedIn ? 'Chưa từng' : e(format_datetime($account['last_time_login'])) ?></dd>
                <dt>Trạng thái</dt>
                <dd>
                    <?php if ((int) $account['active'] === 1): ?><span class="comic-tag comic-tag--green">Đã kích hoạt</span>
                    <?php else: ?><span class="comic-tag comic-tag--red">Chưa kích hoạt</span><?php endif; ?>
                </dd>
            </dl>

            <!-- Số dư trong game: thoi_vang = thỏi vàng, ruby = ruby (cột trong bảng account). Icon lấy từ ảnh game. -->
            <div class="row g-2 mb-3">
                <div class="col-6"><div class="stat-card"><img src="<?= e(asset('img/balls/7.png')) ?>" alt="" width="40" height="40"><div><div class="stat-card__num"><?= e(format_number($account['thoi_vang'])) ?></div><div class="stat-card__label">Thỏi vàng</div></div></div></div>
                <div class="col-6"><div class="stat-card"><img src="<?= e(asset('img/gem.png')) ?>" alt="" width="40" height="40"><div><div class="stat-card__num"><?= e(format_number($account['ruby'])) ?></div><div class="stat-card__label">Ruby</div></div></div></div>
            </div>

            <a class="btn btn-comic btn-comic--blue btn-sm" href="<?= e(url('account/password')) ?>">Đổi mật khẩu</a>
        </section>
    </div>

    <!-- ============ Nhân vật ============ -->
    <div class="col-lg-6">
        <section class="panel h-100" aria-labelledby="char-title">
            <h2 class="panel__title" id="char-title">Nhân vật</h2>
            <?php if ($player): ?>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="display-title"><?= e($player['name']) ?></span>
                    <span class="comic-tag comic-tag--blue"><?= e(PlayerModel::planetName($player['gender'])) ?></span>
                </div>
                <dl class="kv mt-3">
                    <dt>Sức mạnh</dt><dd><strong><?= e(format_number($player['power'])) ?></strong></dd>
                    <dt>Hạng</dt><dd><?= $rank > 0 ? '#' . (int) $rank : 'Chưa vào bảng' ?> <a href="<?= e(url('rank')) ?>">(xem bảng xếp hạng)</a></dd>
                    <dt>Sức mạnh đệ tử</dt><dd><?= e(format_number($player['pet_power'])) ?></dd>
                </dl>
                <?php View::partial('character', ['look' => $look]); ?>
            <?php else: ?>
                <div class="empty">
                    <span class="display-title">Chưa có nhân vật</span>
                    Đăng nhập vào game bằng tài khoản này để tạo nhân vật đầu tiên.
                    <p class="mt-3 mb-0"><a class="btn btn-comic btn-comic--red btn-sm" href="<?= e(url('download')) ?>">Tải game</a></p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
