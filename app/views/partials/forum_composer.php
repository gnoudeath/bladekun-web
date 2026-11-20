<?php
/**
 * PARTIAL: form gửi bài / bình luận.
 * Biến: $action ('forum/post' = đăng bài), $kind ('post'|'comment'), $postId (khi bình luận), $wait (giây còn phải chờ)
 * Giới hạn thời gian chờ được kiểm tra thật ở SERVER; JS chỉ đếm ngược cho người dùng đỡ bực.
 */
$isPost  = ($kind ?? 'post') === 'post';
$maxMb   = (int) config('forum.max_upload_mb', 4);
$maxChar = (int) config('forum.max_content', 2000);
$uid     = 'c' . ($isPost ? 'p' : $postId);
// Danh sách sticker ảnh (public/assets/emoji) để JS dựng bảng chọn: [{name, src}]
$stickerData = [];
foreach (sticker_names() as $n => $file) { $stickerData[] = ['name' => $n, 'src' => asset('emoji/' . $file)]; }
?>
<form class="composer" method="post" action="<?= e(url($isPost ? 'forum/post' : 'forum/comment')) ?>" enctype="multipart/form-data"
      data-composer data-stickers="<?= e(json_encode($stickerData)) ?>" data-wait="<?= (int) $wait ?>" data-max-bytes="<?= $maxMb * 1024 * 1024 ?>" data-max-mb="<?= $maxMb ?>">
    <?= Csrf::field() ?>
    <?php if (!$isPost): ?><input type="hidden" name="post_id" value="<?= (int) $postId ?>"><?php endif; ?>

    <?php if ($isPost): ?>
        <input class="form-control mb-2" type="text" name="title" maxlength="<?= (int) config('forum.max_title', 100) ?>" placeholder="Tiêu đề (không bắt buộc)">
    <?php endif; ?>
    <textarea class="form-control" name="content" id="<?= e($uid) ?>" rows="<?= $isPost ? 3 : 2 ?>" maxlength="<?= $maxChar ?>"
              placeholder="<?= $isPost ? 'Bạn đang nghĩ gì? Gõ :ball1: hoặc bấm 😀 để chèn biểu cảm' : 'Viết bình luận...' ?>"></textarea>

    <div class="composer__bar">
        <button type="button" class="btn btn-comic btn-comic--light btn-sm" data-emoji-toggle aria-expanded="false" aria-label="Chọn biểu cảm">😀 Biểu cảm</button>
        <label class="btn btn-comic btn-comic--light btn-sm mb-0">
            📎 Ảnh/Video
            <input type="file" name="media" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm" hidden data-file>
        </label>
        <span class="composer__file small" data-file-name></span>
        <span class="ms-auto small text-body-secondary d-none d-sm-inline">Tối đa <?= $maxMb ?>MB · <?= $isPost ? 'mỗi bài cách nhau ' . (int) (config('forum.post_cooldown_seconds', 300) / 60) . ' phút' : 'mỗi bình luận cách nhau ' . (int) config('forum.comment_cooldown_seconds', 15) . ' giây' ?></span>
        <button class="btn btn-comic btn-comic--red btn-sm" type="submit" data-submit><?= $isPost ? 'Đăng bài' : 'Gửi' ?></button>
    </div>
    <div class="emoji-panel" data-emoji-panel hidden></div>
    <p class="composer__error small" data-error role="alert" hidden></p>
</form>
