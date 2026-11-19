<?php
/**
 * Hiển thị các thông báo "một lần" (thành công / lỗi / thông tin) do controller đặt bằng Session::flash().
 * pullFlash() lấy ra rồi xoá luôn, nên tải lại trang là thông báo biến mất.
 * Dùng thành phần "alert" của Bootstrap, phủ kiểu bong bóng thoại bằng CSS (.comic-alert).
 */
$flashes = Session::pullFlash();
$map = [
    'success' => ['alert-success', '✓'],
    'error'   => ['alert-danger',  '!'],
    'info'    => ['alert-info',    'i'],
];
?>
<?php if ($flashes): ?>
    <div class="flash-list" role="status" aria-live="polite">
        <?php foreach ($flashes as $f): [$cls, $icon] = $map[$f['type']] ?? $map['info']; ?>
            <div class="alert <?= $cls ?> comic-alert alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <span class="comic-alert__icon" aria-hidden="true"><?= e($icon) ?></span>
                <span><?= e($f['message']) ?></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
