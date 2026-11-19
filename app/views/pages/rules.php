<?php
/**
 * TRANG LUẬT CHƠI – trang chữ tĩnh (PageController::rules).
 * Đây là bộ luật MẪU thường gặp ở máy chủ nhỏ; hãy sửa cho đúng với cách bạn vận hành.
 */
$rules = [
    ['Tôn trọng nhau', 'Không chửi bới, xúc phạm, phân biệt đối xử hay công kích cá nhân trong chat thế giới, chat bang và tin nhắn.'],
    ['Không gian lận', 'Cấm dùng công cụ can thiệp game, khai thác lỗi (bug) để trục lợi. Gặp lỗi hãy báo cho quản trị viên, người báo lỗi sẽ được ghi nhận.'],
    ['Bảo vệ tài khoản', 'Mỗi người nên dùng một tài khoản và đặt mật khẩu riêng, không trùng với nơi khác. Quản trị viên sẽ không bao giờ hỏi mật khẩu của bạn.'],
    ['Giao dịch an toàn', 'Chỉ giao dịch qua hệ thống trong game. Máy chủ không chịu trách nhiệm khi bạn tự trao đổi vật phẩm hoặc tài khoản ở bên ngoài.'],
    ['Không spam, quảng cáo', 'Cấm đăng lặp tin nhắn, rao bán, quảng cáo máy chủ khác hoặc chèn đường link lạ.'],
    ['Nghe theo quản trị viên', 'Quyết định của quản trị viên là quyết định cuối cùng. Vi phạm có thể bị khoá chat, khoá tạm thời hoặc khoá tài khoản.'],
];
?>
<div class="page-head">
    <h1 class="display-title">Luật chơi</h1>
    <p>Giữ cho máy chủ vui và công bằng với mọi người.</p>
    <?= scene('fence', 'page-head__deco d-none d-md-block') ?>
</div>

<section class="panel" aria-label="Danh sách luật">
    <ol class="steps mb-0">
        <?php foreach ($rules as [$title, $text]): ?>
            <li><strong><?= e($title) ?></strong><?= e($text) ?></li>
        <?php endforeach; ?>
    </ol>
</section>
