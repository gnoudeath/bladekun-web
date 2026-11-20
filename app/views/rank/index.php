<?php
/**
 * BẢNG XẾP HẠNG (nhiều tab)
 * Biến: $players (mỗi dòng có `score` = điểm theo tab đang xem), $type (tab hiện tại), $types (các tab),
 *       $limit, $limits, $topScore (điểm của người đứng đầu)
 */
$label = $types[$type]['label'];
$unit  = $types[$type]['unit'];
?>
<div class="page-head">
    <h1 class="display-title">Bảng xếp hạng</h1>
    <p>Những chiến binh nổi bật nhất máy chủ. Bấm vào tên để xem hồ sơ.</p>
    <?= scene('island-mid', 'page-head__deco d-none d-md-block') ?>
</div>

<section class="panel" aria-label="Bảng xếp hạng">
    <!-- Hàng tab: chuyển loại bảng (sức mạnh / đệ tử / ...). Bootstrap "nav-pills" + giao diện truyện tranh -->
    <ul class="nav nav-pills comic-tabs mb-3" aria-label="Loại bảng xếp hạng">
        <?php foreach ($types as $key => $t): ?>
            <li class="nav-item">
                <a class="nav-link<?= $key === $type ? ' active' : '' ?>" href="<?= e(url('rank', ['type' => $key, 'top' => $limit])) ?>"<?= $key === $type ? ' aria-current="page"' : '' ?>><?= e($t['label']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="fw-bold">Hiển thị:</span>
        <div class="btn-group" role="group" aria-label="Số dòng hiển thị">
            <?php foreach ($limits as $l): ?>
                <a class="btn btn-comic btn-sm <?= $l === $limit ? 'btn-comic--blue' : 'btn-comic--light' ?>" href="<?= e(url('rank', ['type' => $type, 'top' => $l])) ?>"<?= $l === $limit ? ' aria-current="true"' : '' ?>>Top <?= (int) $l ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($players): ?>
        <div class="table-responsive">
            <table class="table rank-table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" class="rank-col">Hạng</th>
                        <th scope="col">Chiến binh</th>
                        <th scope="col" class="col-planet d-none d-sm-table-cell">Hành tinh</th>
                        <th scope="col" class="num"><?= e($label) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($players as $i => $p):
                    $rank = $i + 1;
                    // Thanh: % so với người đứng đầu của tab này (người đầu = 100%)
                    $percent = $topScore > 0 ? max(2, (int) round($p['score'] / $topScore * 100)) : 0;
                ?>
                    <tr>
                        <td class="rank-col"><span class="rank-badge<?= $rank <= 3 ? ' rank-badge--' . $rank : '' ?>"><?= $rank ?></span></td>
                        <td class="rank-name"><a class="player-link" href="<?= e(url('player', ['id' => $p['id']])) ?>"><?= e($p['name']) ?></a></td>
                        <td class="col-planet d-none d-sm-table-cell"><span class="comic-tag"><?= e(PlayerModel::planetName($p['gender'])) ?></span></td>
                        <td class="num">
                            <?= e(format_number($p['score'])) ?>
                            <div class="progress power-bar" role="img" aria-label="<?= $percent ?> phần trăm so với người đứng đầu"><div class="progress-bar" style="width: <?= $percent ?>%"></div></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="small text-body-secondary mt-3 mb-0">Cột <strong><?= e($label) ?></strong> tính theo <?= e($unit) ?>; dữ liệu cập nhật khi game lưu nhân vật.<?php $min = PlayerModel::rankMin($type); if ($min > 0): ?> Cần đạt tối thiểu <strong><?= e(format_number($min)) ?></strong> mới được vào bảng.<?php endif; ?></p>
    <?php else: ?>
        <div class="empty">
            <span class="display-title">Chưa có ai!</span>
            Chưa có chiến binh nào đủ điều kiện vào bảng<?php $min = PlayerModel::rankMin($type); if ($min > 0): ?> (cần tối thiểu <?= e(format_number($min)) ?>)<?php endif; ?>. Hãy luyện tập và trở thành người đầu tiên!
        </div>
    <?php endif; ?>
</section>
