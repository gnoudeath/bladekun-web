<?php
/**
 * DIỄN ĐÀN – DANH SÁCH BÀI. Biến: $posts, $filter, $page, $pages, $total, $me, $wait
 */
?>
<div class="page-head">
    <h1 class="display-title">Diễn đàn</h1>
    <p>Trò chuyện, khoe thành tích, chia sẻ ảnh và video cùng anh em.</p>
</div>

<section class="panel mb-4" aria-label="Đăng bài mới">
    <?php if ($me): ?>
        <?php View::partial('forum_composer', ['kind' => 'post', 'wait' => $wait]); ?>
    <?php else: ?>
        <p class="mb-0">Hãy <a href="<?= e(url('login')) ?>">đăng nhập</a> hoặc <a href="<?= e(url('register')) ?>">đăng ký</a> để đăng bài và bình luận.</p>
    <?php endif; ?>
</section>

<nav class="forum-tabs" aria-label="Lọc bài đăng">
    <?php foreach (ForumModel::FILTERS as $key => $label): ?>
        <a class="forum-tab<?= $filter === $key ? ' is-active' : '' ?>" href="<?= e(url('forum', $key === 'all' ? [] : ['filter' => $key])) ?>"<?= $filter === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
    <span class="forum-tabs__count"><?= (int) $total ?> bài</span>
</nav>

<?php if ($posts): ?>
    <div class="forum-list">
        <?php foreach ($posts as $post): ?>
            <?php View::partial('forum_post', ['post' => $post, 'me' => $me, 'full' => false, 'filter' => $filter, 'page' => $page]); ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <section class="panel"><div class="empty"><span class="display-title">Chưa có bài nào</span>Hãy là người đầu tiên đăng bài!</div></section>
<?php endif; ?>

<?php View::partial('pagination', ['page' => $page, 'pages' => $pages, 'route' => 'forum', 'params' => $filter === 'all' ? [] : ['filter' => $filter]]); ?>
