<?php /** DANH SÁCH TIN TỨC. Biến: $posts */ ?>
<div class="page-head">
    <h1 class="display-title">Tin tức</h1>
    <p>Thông báo, cập nhật và sự kiện mới nhất của máy chủ.</p>
    <?= scene('rock-island', 'page-head__deco d-none d-md-block') ?>
</div>

<section class="panel" aria-label="Danh sách tin tức">
    <?php if ($posts): ?>
        <div class="row g-3">
            <?php foreach ($posts as $n): ?>
                <div class="col-md-6">
                    <a class="news-card h-100" href="<?= e(url('news/view', ['id' => $n['id']])) ?>">
                        <span class="news-card__date"><?= e(format_datetime($n['created_at'])) ?> • <?= e(format_number($n['views'])) ?> lượt xem</span>
                        <span class="news-card__title"><?= e($n['title']) ?></span>
                        <?php if (!empty($n['short_content']) && $n['short_content'] !== $n['title']): ?>
                            <span class="news-card__text"><?= e(html_to_text($n['short_content'])) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty"><span class="display-title">Yên ắng...</span>Chưa có tin tức nào được đăng.</div>
    <?php endif; ?>
    <?php View::partial('pagination', ['page' => $page, 'pages' => $pages, 'route' => 'news']); ?>
</section>
