<?php
/**
 * PARTIAL: hình nhân vật + danh sách trang bị đang mặc.
 * Biến: $look = kết quả CharacterModel::forPlayer() (layers, approx, equipment)
 * Dùng chung cho trang "Tài khoản của tôi" và hồ sơ công khai.
 * Mọi chuỗi từ database đều đi qua e() để tránh chèn mã HTML/JS.
 */
$iconDir = ROOT_PATH . '/public/assets/img/icons/';
?>
<div class="char-card">
    <div class="char-stage" style="width:<?= CharacterModel::STAGE_W ?>px;height:<?= CharacterModel::STAGE_H ?>px" aria-label="Hình nhân vật">
        <?php foreach ($look['layers'] as $l): ?>
            <img class="char-stage__layer" src="<?= e($l['src']) ?>" alt="" width="<?= (int) $l['w'] ?>" height="<?= (int) $l['h'] ?>"
                 style="left:<?= (int) $l['left'] ?>px;top:<?= (int) $l['top'] ?>px;z-index:<?= (int) $l['z'] ?>">
        <?php endforeach; ?>
        <?php if (!$look['layers']): ?><span class="char-stage__empty">Chưa có ảnh</span><?php endif; ?>
    </div>
    <?php if ($look['approx'] && $look['layers']): ?>
        <p class="char-note">Một số bộ phận chưa có ảnh trong file game nên hiển thị bản cơ bản.</p>
    <?php endif; ?>

    <h3 class="char-eq-title">Trang bị đang mặc</h3>
    <?php if ($look['equipment']): ?>
        <ul class="eq-grid">
            <?php foreach ($look['equipment'] as $it):
                $hasIcon = is_file($iconDir . (int) $it['icon'] . '.png');
                $tip = $it['name'] . ($it['options'] ? "\n" . implode("\n", $it['options']) : ''); ?>
                <li class="eq-item" title="<?= e($tip) ?>" tabindex="0">
                    <?php if ($hasIcon): ?>
                        <img src="<?= e(asset('img/icons/' . (int) $it['icon'] . '.png')) ?>" alt="" width="40" height="40">
                    <?php else: ?><span class="eq-item__noicon">?</span><?php endif; ?>
                    <span class="eq-item__name"><?= e($it['name']) ?></span>
                    <span class="eq-item__type"><?= e($it['type_label']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-body-secondary mb-0">Chưa mặc trang bị nào.</p>
    <?php endif; ?>
</div>
