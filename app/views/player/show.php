<?php
/**
 * HỒ SƠ CÔNG KHAI CỦA MỘT NHÂN VẬT
 * Biến: $p (nhân vật), $rank (hạng sức mạnh), $clan (['id','name'] hoặc null nếu chưa vào bang)
 * Cố ý không hiện thông tin riêng tư (vàng, ruby, tổng nạp, email...).
 */
$planet = PlayerModel::planetName($p['gender']);
?>
<div class="page-head">
    <h1 class="display-title"><?= e($p['name']) ?></h1>
    <p>Hồ sơ chiến binh</p>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <section class="panel h-100 text-center" aria-labelledby="pl-planet">
            <h2 class="panel__title" id="pl-planet">Hành tinh <?= e($planet) ?></h2>
            <div class="planet-stage planet-stage--<?= (int) $p['gender'] ?>">
                <?= scene(planet_scene($p['gender']), 'planet-stage__img') ?>
                <span class="rank-stamp" title="Hạng sức mạnh"><small>Hạng</small><?= $rank > 0 ? (int) $rank : '–' ?></span>
            </div>
            <?php View::partial('character', ['look' => $look]); ?>
        </section>
    </div>

    <div class="col-lg-7">
        <section class="panel h-100" aria-labelledby="pl-info">
            <h2 class="panel__title" id="pl-info">Thông tin</h2>
            <div class="row g-2 mb-3">
                <div class="col-sm-6"><div class="stat-card"><img src="<?= e(asset('img/balls/4.png')) ?>" alt="" width="40" height="40"><div><div class="stat-card__num"><?= e(format_number($p['power'])) ?></div><div class="stat-card__label">Sức mạnh</div></div></div></div>
                <div class="col-sm-6"><div class="stat-card"><img src="<?= e(asset('img/gem.png')) ?>" alt="" width="40" height="40"><div><div class="stat-card__num"><?= e(format_number($p['pet_power'])) ?></div><div class="stat-card__label">Sức mạnh đệ tử</div></div></div></div>
            </div>
            <dl class="kv">
                <dt>Hành tinh</dt><dd><span class="comic-tag comic-tag--blue"><?= e($planet) ?></span></dd>
                <dt>Hạng sức mạnh</dt><dd><?= $rank > 0 ? '#' . (int) $rank : 'Chưa vào bảng' ?> <a href="<?= e(url('rank')) ?>">(xem bảng xếp hạng)</a></dd>
                <dt>Bang hội</dt>
                <dd>
                    <?php if ($clan): ?>
                        <a class="player-link" href="<?= e(url('clan', ['id' => $clan['id']])) ?>"><?= e($clan['name']) ?></a>
                    <?php else: ?>
                        <span class="text-body-secondary">Chưa vào bang</span>
                    <?php endif; ?>
                </dd>
                <dt>Boss đã hạ</dt><dd><?= e(format_number($p['kill_boss'])) ?></dd>
                <dt>Ngày tạo nhân vật</dt><dd><?= e(format_datetime($p['create_time'])) ?></dd>
            </dl>
            <a class="btn btn-comic btn-comic--light btn-sm" href="<?= e(url('rank')) ?>">← Bảng xếp hạng</a>
        </section>
    </div>
</div>
