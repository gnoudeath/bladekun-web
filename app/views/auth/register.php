<?php
/**
 * FORM ĐĂNG KÝ – gửi POST tới /register (AuthController::register).
 * Các thuộc tính minlength/maxlength/pattern chỉ giúp người dùng sửa lỗi sớm;
 * việc kiểm tra thật sự luôn do Validator làm ở phía server.
 */
$uMin = (int) config('game.username_min');
$uMax = (int) config('game.username_max');
$pMin = (int) config('game.password_min');
$pMax = (int) config('game.password_max');
?>
<div class="row justify-content-center">
<div class="col-md-8 col-lg-5">
<div class="page-head text-center">
    <h1 class="display-title">Đăng ký chiến binh</h1>
    <p>Tài khoản tạo ở đây dùng để đăng nhập trực tiếp vào game.</p>
</div>

<section class="panel" aria-labelledby="reg-title">
    <h2 class="panel__title" id="reg-title">Tạo tài khoản</h2>
    <form method="post" action="<?= e(url('register')) ?>">
        <?= Csrf::field() ?>

        <!-- Ô bẫy bot: ẩn khỏi người thật. Bot tự điền mọi ô sẽ bị chặn trong AuthController. -->
        <div class="hp-field" aria-hidden="true">
            <label class="form-label" for="website">Để trống ô này</label>
            <input class="form-control" type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="mb-3">
            <label class="form-label" for="username">Tên đăng nhập</label>
            <input class="form-control" type="text" id="username" name="username" value="<?= old('username') ?>"
                   minlength="<?= $uMin ?>" maxlength="<?= $uMax ?>" pattern="[A-Za-z0-9_]+"
                   autocomplete="username" autocapitalize="none" required autofocus>
            <div class="form-text"><?= $uMin ?>–<?= $uMax ?> ký tự: chữ, số, gạch dưới. Sẽ được đổi thành chữ thường.</div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input class="form-control" type="email" id="email" name="email" value="<?= old('email') ?>" maxlength="255" autocomplete="email" required>
            <div class="form-text">Dùng để liên hệ khi cần hỗ trợ tài khoản.</div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Mật khẩu</label>
            <div class="input-group">
                <input class="form-control" type="password" id="password" name="password" minlength="<?= $pMin ?>" maxlength="<?= $pMax ?>" autocomplete="new-password" required>
                <button type="button" class="btn btn-comic btn-comic--light toggle-pw" data-toggle-password="password">Hiện</button>
            </div>
            <div class="form-text"><?= $pMin ?>–<?= $pMax ?> ký tự, không dấu cách, không chữ có dấu (để gõ được trong game).</div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password_confirm">Nhập lại mật khẩu</label>
            <input class="form-control" type="password" id="password_confirm" name="password_confirm" minlength="<?= $pMin ?>" maxlength="<?= $pMax ?>" autocomplete="new-password" required>
        </div>

        <button class="btn btn-comic btn-comic--red w-100 btn-lg" type="submit">Tạo tài khoản</button>
        <p class="text-center mt-3 mb-0">Đã có tài khoản? <a href="<?= e(url('login')) ?>">Đăng nhập</a></p>
    </form>
</section>
</div></div>
<?php Session::clearOld(); ?>
