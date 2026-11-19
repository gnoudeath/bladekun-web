<?php
/** PARTIAL: ảnh hoặc video đính kèm. Biến: $path, $type. Đường dẫn do server tự đặt khi tải lên (xem Upload.php). */
if (empty($path)) { return; }
?>
<div class="forum-media">
    <?php if ($type === 'video'): ?>
        <video controls preload="metadata" playsinline src="<?= e(upload_url($path)) ?>"></video>
    <?php else: ?>
        <a href="<?= e(upload_url($path)) ?>" target="_blank" rel="noopener"><img src="<?= e(upload_url($path)) ?>" alt="Ảnh đính kèm" loading="lazy"></a>
    <?php endif; ?>
</div>
