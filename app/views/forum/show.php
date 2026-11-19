<?php /** DIỄN ĐÀN – MỘT BÀI + BÌNH LUẬN. Biến: $post, $comments, $me, $wait */ ?>
<div class="page-head"><h1 class="display-title">Bài viết</h1></div>

<?php View::partial('forum_post', ['post' => $post, 'me' => $me, 'full' => true]); ?>

<section class="panel mt-4" id="comments" aria-label="Bình luận">
    <h2 class="panel__title">Bình luận (<?= count($comments) ?>)</h2>

    <?php foreach ($comments as $c):
        $cName = $c['char_name'] ?: $c['username'];
        $mine  = $me && ((int) $me['id'] === (int) $c['account_id'] || !empty($me['is_admin'])); ?>
        <div class="comment" id="comment-<?= (int) $c['id'] ?>">
            <img class="avatar avatar--sm" src="<?= e(head_avatar($c['char_head'] ?? 0, $c['char_gender'] ?? 0)) ?>" alt="" width="36" height="36">
            <div class="comment__main">
                <strong class="<?= $c['is_admin'] ? 'is-admin' : '' ?>"><?= e($cName) ?></strong>
                <?php if ($c['is_admin']): ?><span class="comic-tag comic-tag--red">Admin</span><?php endif; ?>
                <span class="small text-body-secondary"> · <?= e(time_ago($c['created_at'])) ?></span>
                <?php if ($c['content'] !== ''): ?><div><?= rich_text($c['content']) ?></div><?php endif; ?>
                <?php View::partial('forum_media', ['path' => $c['media_path'], 'type' => $c['media_type']]); ?>
                <?php if ($mine): ?>
                    <form method="post" action="<?= e(url('forum/delete')) ?>" onsubmit="return confirm('Xoá bình luận này?');">
                        <?= Csrf::field() ?><input type="hidden" name="kind" value="comment"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                        <button class="forum-btn forum-btn--danger forum-btn--tiny" type="submit">Xoá</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$comments): ?><p class="text-body-secondary">Chưa có bình luận nào.</p><?php endif; ?>

    <div class="mt-3">
        <?php if ($me): ?>
            <?php View::partial('forum_composer', ['kind' => 'comment', 'postId' => (int) $post['id'], 'wait' => $wait]); ?>
        <?php else: ?>
            <p class="mb-0"><a href="<?= e(url('login')) ?>">Đăng nhập</a> để bình luận.</p>
        <?php endif; ?>
    </div>
</section>
<p class="mt-3"><a class="btn btn-comic btn-comic--light btn-sm" href="<?= e(url('forum')) ?>">← Diễn đàn</a></p>
