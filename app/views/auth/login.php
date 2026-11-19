<?php
/** FORM ĐĂNG NHẬP – gửi POST tới /login (AuthController::login). */
?>
<div class="row justify-content-center">
<div class="col-md-8 col-lg-5">
<div class="page-head text-center"><h1 class="display-title">Đăng nhập</h1></div>

<section class="panel" aria-labelledby="login-title">
    <h2 class="panel__title" id="login-title">Chào mừng trở lại</h2>
    <form method="post" action="<?= e(url('login')) ?>">
        <?= Csrf::field() /* mã chống CSRF – bắt buộc trong mọi form POST */ ?>

        <div class="mb-3">
            <label class="form-label" for="username">Tên đăng nhập</label>
            <input class="form-control" type="text" id="username" name="username" value="<?= old('username') ?>"
                   autocomplete="username" autocapitalize="none" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Mật khẩu</label>
            <div class="input-group">
                <input class="form-control" type="password" id="password" name="password" autocomplete="current-password"  required>
                <button type="button" class="btn btn-comic btn-comic--light toggle-pw" data-toggle-password="password">Hiện</button>
            </div>
        </div>

        <button class="btn btn-comic btn-comic--red w-100 btn-lg" type="submit">Vào thôi!</button>
        <p class="text-center mt-3 mb-0">Chưa có tài khoản? <a href="<?= e(url('register')) ?>">Đăng ký ngay</a></p>
    </form>
</section>
</div></div>
<?php Session::clearOld(); ?>
