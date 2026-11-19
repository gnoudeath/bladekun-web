<?php
/**
 * TRANG LỖI CHUNG (404 không tìm thấy, 405 sai phương thức, 419 hết hạn form, 500 lỗi máy chủ).
 * Biến: $title, $message (tuỳ chọn), $detail (chi tiết lỗi, chỉ có khi app.debug = true)
 */
$message = $message ?? 'Trang bạn tìm không có ở đây. Có thể đường link bị sai hoặc trang đã bị xoá.';
?>
<section class="panel auth-wrap text-center mt-4">
    <h2 class="panel__title"><?= e($title ?? 'Lỗi') ?></h2>
    <div class="display-title mb-2" style="font-size:5rem" aria-hidden="true">?!</div>
    <p class="fw-semibold"><?= e($message) ?></p>
    <?php if (!empty($detail)): ?>
        <p class="small text-body-secondary" style="word-break:break-word"><code><?= e($detail) ?></code></p>
    <?php endif; ?>
    <a class="btn btn-comic btn-comic--red" href="<?= e(url('')) ?>">Về trang chủ</a>
</section>
