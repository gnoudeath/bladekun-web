<?php
/** XẾP HẠNG BANG HỘI. Biến: $clans, $limit, $limits, $topPower */
?>
<div class="page-head">
    <h1 class="display-title">Bang hội</h1>
    <p>Những bang hội mạnh nhất máy chủ. Bấm vào tên bang để xem thành viên.</p>
    <?= scene('pagoda', 'page-head__deco d-none d-md-block') ?>
</div>

<section class="panel" aria-label="Bảng xếp hạng bang hội">
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="fw-bold">Hiển thị:</span>
        <div class="btn-group" role="group" aria-label="Số dòng hiển thị">
            <?php foreach ($limits as $l): ?>
                <a class="btn btn-comic btn-sm <?= $l === $limit ? 'btn-comic--blue' : 'btn-comic--light' ?>" href="<?= e(url('clans', ['top' => $l])) ?>"<?= $l === $limit ? ' aria-current="true"' : '' ?>>Top <?= (int) $l ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($clans): ?>
        <div class="table-responsive">
            <table class="table rank-table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="rank-col">Hạng</th>
                        <th scope="col">Bang hội</th>
                        <th scope="col" class="d-none d-sm-table-cell">Cấp</th>
                        <th scope="col" class="d-none d-sm-table-cell">Thành viên</th>
                        <th scope="col" class="num">Sức mạnh</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($clans as $i => $c):
                    $rank = $i + 1;
                    $percent = $topPower > 0 ? max(2, (int) round($c['power_point'] / $topPower * 100)) : 0;
                ?>
                    <tr>
                        <td class="rank-col"><span class="rank-badge<?= $rank <= 3 ? ' rank-badge--' . $rank : '' ?>"><?= $rank ?></span></td>
                        <td class="rank-name">
                            <a class="player-link d-inline-flex align-items-center gap-2" href="<?= e(url('clan', ['id' => $c['id']])) ?>">
                                <img class="emblem" src="<?= e(ClanModel::emblem((int) $c['id'])) ?>" alt="" width="28" height="28">
                                <span><?= e($c['name']) ?></span>
                            </a>
                            <?php if ($c['slogan'] !== ''): ?><div class="small text-body-secondary fw-normal"><?= e($c['slogan']) ?></div><?php endif; ?>
                        </td>
                        <td class="d-none d-sm-table-cell"><?= (int) $c['level'] ?></td>
                        <td class="d-none d-sm-table-cell"><?= (int) $c['member_count'] ?>/<?= (int) $c['max_member'] ?></td>
                        <td class="num">
                            <?= e(format_number($c['power_point'])) ?>
                            <div class="progress power-bar" role="img" aria-label="<?= $percent ?> phần trăm so với bang đứng đầu"><div class="progress-bar" style="width: <?= $percent ?>%"></div></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty"><span class="display-title">Chưa có bang nào!</span>Vào game và lập bang hội đầu tiên của máy chủ.</div>
    <?php endif; ?>
</section>
