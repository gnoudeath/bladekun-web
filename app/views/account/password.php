<?php
/** FORM ĐỔI MẬT KHẨU – gửi POST tới /account/password (AccountController::changePassword). */
$pMin = (int) config('game.password_min');
$pMax = (int) config('game.password_max');
?>
<div class="row justify-content-center">
<div class="col-md-8 col-lg-5">
<div class="page-head text-center"><h1 class="display-title">Đổi mật khẩu</h1></div>

<section class="panel" aria-labelledby="pw-title">
    <h2 class="panel__title" id="pw-title">Mật khẩu mới</h2>
    <form method="post" action="<?= e(url('account/password')) ?>">
        <?= Csrf::field() ?>

        <div class="mb-3">
            <label class="form-label" for="current_password">Mật khẩu hiện tại</label>
            <div class="input-group">
                <input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" autofocus required>
                <button type="button" class="btn btn-comic btn-comic--light toggle-pw" data-toggle-password="current_password">Hiện</button>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="new_password">Mật khẩu mới</label>
            <div class="input-group">
                <input class="form-control" type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="<?= $pMin ?>" maxlength="<?= $pMax ?>" required>
                <button type="button" class="btn btn-comic btn-comic--light toggle-pw" data-toggle-password="new_password">Hiện</button>
            </div>
            <div class="form-text"><?= $pMin ?>–<?= $pMax ?> ký tự, không dấu cách, không chữ có dấu.</div>
        </div>

        <div class="mb-3">
            <label class="form-label" for="new_password_confirm">Nhập lại mật khẩu mới</label>
            <input class="form-control" type="password" id="new_password_confirm" name="new_password_confirm" minlength="<?= $pMin ?>" maxlength="<?= $pMax ?>" autocomplete="new-password" required>
        </div>

        <button class="btn btn-comic btn-comic--red w-100 btn-lg" type="submit">Lưu mật khẩu</button>
        <p class="text-center mt-3 mb-0"><a href="<?= e(url('account')) ?>">← Quay lại tài khoản</a></p>
    </form>
</section>
</div></div>
