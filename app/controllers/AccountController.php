<?php
/**
 * AccountController – Khu vực riêng của người chơi (phải đăng nhập).
 *   - index():           xem thông tin tài khoản và nhân vật
 *   - showPassword():    form đổi mật khẩu
 *   - changePassword():  xử lý đổi mật khẩu
 */
class AccountController extends Controller
{
    public function index(): void
    {
        $accountId = $this->requireLogin();

        $account = (new AccountModel())->findById($accountId);
        if ($account === null) {
            // Tài khoản đã bị xoá khỏi database trong lúc đang đăng nhập
            Auth::logout();
            redirect('login');
        }

        $players = new PlayerModel();
        $player  = $players->findByAccountId($accountId);

        $this->view('account/index', [
            'title'   => 'Tài khoản của tôi',
            'account' => $account,
            'player'  => $player,
            'rank'    => $player ? $players->rankOf((int) $player['power']) : null,
            'look'    => $player ? (new CharacterModel())->forPlayer((int) $player['id'], (int) $player['gender'], (int) $player['head']) : null,
        ]);
    }

    public function showPassword(): void
    {
        $this->requireLogin();
        $this->view('account/password', ['title' => 'Đổi mật khẩu']);
    }

    public function changePassword(): void
    {
        $accountId = $this->requireLogin();
        $this->requireCsrf();

        $current = (string) ($_POST['current_password'] ?? '');
        $new     = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');

        $accounts = new AccountModel();
        $stored   = $accounts->storedPasswordOf($accountId);

        // Bắt người dùng nhập lại mật khẩu cũ: nếu ai đó mượn máy đang đăng nhập thì cũng không đổi được
        if ($stored === null || !AccountModel::verifyPassword($current, $stored)) {
            Session::flash('error', 'Mật khẩu hiện tại không đúng.');
            redirect('account/password');
        }

        $error = Validator::password($new)
            ?? ($new !== $confirm ? 'Hai mật khẩu mới nhập không giống nhau.' : null)
            ?? ($new === $current ? 'Mật khẩu mới phải khác mật khẩu hiện tại.' : null);
        if ($error !== null) {
            Session::flash('error', $error);
            redirect('account/password');
        }

        $accounts->updatePassword($accountId, $new);
        Session::flash('success', 'Đổi mật khẩu thành công. Lần đăng nhập game tới hãy dùng mật khẩu mới.');
        redirect('account');
    }
}
