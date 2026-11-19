<?php
/**
 * MỘT BÀI TIN. Biến: $post
 * Nội dung trong database là HTML; ta bỏ hết thẻ rồi escape (html_to_text + e) để chống XSS.
 * nl2br giữ lại các lần xuống dòng.
 */
?>
<article class="article panel mx-auto" style="max-width:820px">
    <h1 class="display-title"><?= e($post['title']) ?></h1>
    <div class="article__meta text-body-secondary">
        <?= e(format_datetime($post['created_at'])) ?> • <?= e(format_number($post['views'])) ?> lượt xem
    </div>
    <div class="article__body"><?= nl2br(e(html_to_text($post['content']))) ?></div>
    <p class="mb-0"><a class="btn btn-comic btn-comic--light btn-sm" href="<?= e(url('news')) ?>">← Tất cả tin tức</a></p>
</article>
