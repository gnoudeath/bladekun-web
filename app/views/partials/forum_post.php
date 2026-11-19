<?php
/**
 * PARTIAL: một bài đăng dạng thẻ (dùng ở danh sách và trang chi tiết).
 * Biến: $post, $me (tài khoản đang đăng nhập hoặc null), $full (true = hiện đủ nội dung), $filter, $page (để quay lại đúng chỗ)
 * Mọi chuỗi từ người dùng đi qua e() hoặc rich_text() (đã e() bên trong).
 */
$full    = $full ?? false;
$isOwner = $me && (int) $me['id'] === (int) $post['account_id'];
$isAdmin = $me && !empty($me['is_admin']);
$name    = $post['char_name'] ?: $post['username'];
$text    = (string) $post['content'];
if (!$full && mb_strlen($text) > 400) { $text = mb_substr($text, 0, 400) . '…'; }   // cắt TRƯỚC khi rich_text để không cắt đôi thẻ ảnh
$fields  = Csrf::field() . '<input type="hidden" name="post_id" value="' . (int) $post['id'] . '">'
         . ($full ? '' : '<input type="hidden" name="from" value="list"><input type="hidden" name="filter" value="' . e($filter ?? 'all') . '"><input type="hidden" name="page" value="' . (int) ($page ?? 1) . '">');
?>
<article class="forum-post<?= $post['pinned'] ? ' forum-post--pinned' : '' ?>" id="post-<?= (int) $post['id'] ?>">
    <?php if ($post['pinned']): ?><span class="forum-post__pin">📌 Ghim bởi Admin</span><?php endif; ?>

    <header class="forum-post__head">
        <img class="avatar" src="<?= e(head_avatar($post['char_head'] ?? 0, $post['char_gender'] ?? 0)) ?>" alt="" width="48" height="48">
        <div>
            <strong class="forum-post__name<?= $post['is_admin'] ? ' is-admin' : '' ?>"><?= e($name) ?></strong>
            <?php if ($post['is_admin']): ?><span class="comic-tag comic-tag--red">Admin</span><?php endif; ?>
            <div class="small text-body-secondary"><?= e(time_ago($post['created_at'])) ?></div>
        </div>
    </header>

    <?php if (!empty($post['title'])): ?>
        <h3 class="forum-post__title"><?= $full ? e($post['title']) : '<a href="' . e(url('forum/post', ['id' => $post['id']])) . '">' . e($post['title']) . '</a>' ?></h3>
    <?php endif; ?>
    <?php if ($text !== ''): ?><div class="forum-post__body"><?= rich_text($text) ?></div><?php endif; ?>
    <?php if (!$full && mb_strlen((string) $post['content']) > 400): ?>
        <a class="small" href="<?= e(url('forum/post', ['id' => $post['id']])) ?>">Xem tiếp</a>
    <?php endif; ?>

    <?php View::partial('forum_media', ['path' => $post['media_path'], 'type' => $post['media_type']]); ?>

    <footer class="forum-post__foot">
        <?php if ($me): ?>
            <form method="post" action="<?= e(url('forum/like')) ?>" class="d-inline"><?= $fields ?>
                <button class="forum-btn<?= $post['liked'] ? ' is-on' : '' ?>" type="submit" aria-pressed="<?= $post['liked'] ? 'true' : 'false' ?>">👍 <?= $post['liked'] ? 'Đã thích' : 'Thích' ?></button>
            </form>
        <?php else: ?>
            <a class="forum-btn" href="<?= e(url('login')) ?>">👍 Thích</a>
        <?php endif; ?>
        <a class="forum-btn" href="<?= e(url('forum/post', ['id' => $post['id']])) ?>#comments">💬 Bình luận</a>

        <?php if ($isAdmin): ?>
            <form method="post" action="<?= e(url('forum/pin')) ?>" class="d-inline"><?= $fields ?>
                <button class="forum-btn" type="submit"><?= $post['pinned'] ? 'Bỏ ghim' : 'Ghim' ?></button></form>
        <?php endif; ?>
        <?php if ($isOwner || $isAdmin): ?>
            <form method="post" action="<?= e(url('forum/delete')) ?>" class="d-inline" onsubmit="return confirm('Xoá bài viết này?');">
                <?= $fields ?><input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                <button class="forum-btn forum-btn--danger" type="submit">Xoá</button></form>
        <?php endif; ?>

        <span class="forum-post__stats"><?= (int) $post['like_count'] ?> lượt thích · <?= (int) $post['comment_count'] ?> bình luận</span>
    </footer>
</article>
