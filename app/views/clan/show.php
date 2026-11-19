<?php
/** CHI TIẾT MỘT BANG. Biến: $clan, $rank (hạng bang), $members (thành viên, mạnh nhất trước) */
?>
<div class="page-head">
    <h1 class="display-title d-flex align-items-center gap-3">
        <img class="emblem emblem--lg" src="<?= e(ClanModel::emblem((int) $clan['id'])) ?>" alt="" width="56" height="56">
        <span><?= e($clan['name']) ?></span>
    </h1>
    <?php if ($clan['slogan'] !== ''): ?><p>“<?= e($clan['slogan']) ?>”</p><?php endif; ?>
    <?= scene('pagoda', 'page-head__deco d-none d-md-block') ?>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <section class="panel h-100" aria-labelledby="clan-info">
            <h2 class="panel__title" id="clan-info">Thông tin</h2>
            <dl class="kv">
                <dt>Hạng bang</dt><dd><span class="rank-badge<?= $rank <= 3 ? ' rank-badge--' . $rank : '' ?>"><?= (int) $rank ?></span></dd>
                <dt>Sức mạnh</dt><dd><strong><?= e(format_number($clan['power_point'])) ?></strong></dd>
                <dt>Cấp bang</dt><dd><?= (int) $clan['level'] ?></dd>
                <dt>Thành viên</dt><dd><?= (int) $clan['member_count'] ?>/<?= (int) $clan['max_member'] ?></dd>
                <dt>Điểm bang</dt><dd><?= e(format_number($clan['clan_point'])) ?></dd>
                <dt>Thành lập</dt><dd><?= e(format_datetime($clan['create_time'])) ?></dd>
            </dl>
            <a class="btn btn-comic btn-comic--light btn-sm" href="<?= e(url('clans')) ?>">← Tất cả bang hội</a>
        </section>
    </div>

    <div class="col-lg-8">
        <section class="panel h-100" aria-labelledby="clan-members">
            <h2 class="panel__title" id="clan-members">Thành viên</h2>
            <?php if ($members): ?>
                <div class="table-responsive">
                    <table class="table rank-table align-middle mb-0">
                        <thead><tr><th scope="col" class="rank-col">#</th><th scope="col">Chiến binh</th><th scope="col" class="d-none d-sm-table-cell">Hành tinh</th><th scope="col" class="num">Sức mạnh</th></tr></thead>
                        <tbody>
                        <?php foreach ($members as $i => $m): ?>
                            <tr>
                                <td class="rank-col"><span class="rank-badge"><?= $i + 1 ?></span></td>
                                <td class="rank-name"><a class="player-link" href="<?= e(url('player', ['id' => $m['id']])) ?>"><?= e($m['name']) ?></a></td>
                                <td class="d-none d-sm-table-cell"><span class="comic-tag"><?= e(PlayerModel::planetName($m['gender'])) ?></span></td>
                                <td class="num"><?= e(format_number($m['power'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty"><span class="display-title">Vắng vẻ...</span>Chưa có thành viên nào hiển thị.</div>
            <?php endif; ?>
        </section>
    </div>
</div>
