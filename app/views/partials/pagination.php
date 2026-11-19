<?php
/**
 * THANH PHÂN TRANG dùng chung (Bootstrap "pagination").
 * Biến cần truyền: $page (trang hiện tại), $pages (tổng số trang), $route (đường dẫn, vd 'news'),
 *                  $params (tham số giữ nguyên trên link, mặc định rỗng).
 */
$params = $params ?? [];
if ($pages <= 1) { return; }
$link = fn(int $n) => e(url($route, $params + ['page' => $n]));
?>
<nav aria-label="Phân trang" class="mt-4">
    <ul class="pagination comic-pagination justify-content-center flex-wrap mb-0">
        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $link(max(1, $page - 1)) ?>" aria-label="Trang trước">‹ Trước</a>
        </li>
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                <a class="page-link" href="<?= $link($i) ?>"<?= $i === $page ? ' aria-current="page"' : '' ?>><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>">
            <a class="page-link" href="<?= $link(min($pages, $page + 1)) ?>" aria-label="Trang sau">Sau ›</a>
        </li>
    </ul>
</nav>
