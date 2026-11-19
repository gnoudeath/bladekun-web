<?php
/**
 * AuthController – Đăng ký, đăng nhập, đăng xuất.
 *
 * Quy ước chung cho các hàm xử lý form (POST):
 *   1. requireCsrf()          – chặn form giả mạo
 *   2. đọc + kiểm tra dữ liệu – nếu sai: flash lỗi + nhớ lại ô đã nhập + quay về form
 *   3. gọi Model làm việc      – lưu/đọc database
 *   4. flash thành công + redirect – chuyển trang (tránh gửi lại form khi bấm F5)
 */
class AuthController extends Controller
{
    // ------------------------------------------------------------------
    //  ĐĂNG KÝ
    // ------------------------------------------------------------------

    public function showRegister(): void
    {
        $this->requireGuest();
        $this->view('auth/register', ['title' => 'Đăng ký chiến binh']);
    }

    public function register(): void
    {
        $this->requireGuest();
        $this->requireCsrf();

        // Tên đăng nhập luôn chuyển về chữ thường vì MySQL không phân biệt hoa/thường
        $username = strtolower($this->post('username'));
        $email    = $this->post('email');
        $password = (string) ($_POST['password'] ?? '');          // không trim mật khẩu
        $confirm  = (string) ($_POST['password_confirm'] ?? '');

        Session::flashOld(['username' => $username, 'email' => $email]);

        // Ô "website" là bẫy cho bot: người thật không thấy (ẩn bằng CSS) nên không bao giờ điền.
        if ($this->post('website') !== '') {
            redirect('register');
        }

        // Giới hạn số tài khoản tạo từ một địa chỉ IP trong một khoảng thời gian
        $limitKey = 'register:' . client_ip();
        $max      = (int) config('limits.register_max');
        $window   = (int) config('limits.register_window');
        if (RateLimiter::tooMany($limitKey, $max, $window)) {
            Session::flash('error', 'Bạn đã tạo quá nhiều tài khoản trong thời gian ngắn. Hãy thử lại sau.');
            redirect('register');
        }

        // Gom hết lỗi để người dùng sửa một lượt, thay vì báo từng lỗi một
        $errors = array_filter([
            Validator::username($username),
            Validator::email($email),
            Validator::password($password),
            $password !== $confirm ? 'Hai mật khẩu nhập không giống nhau.' : null,
        ]);
        if ($errors) {
            foreach ($errors as $message) {
                Session::flash('error', $message);
            }
            redirect('register');
        }

        $accounts = new AccountModel();
        if ($accounts->usernameExists($username)) {
            Session::flash('error', 'Tên đăng nhập này đã có người dùng, hãy chọn tên khác.');
            redirect('register');
        }

        try {
            $id = $accounts->create($username, $password, $email, client_ip(), (bool) config('game.auto_activate'));
        } catch (PDOException $e) {
            // Mã 23000 = vi phạm khoá UNIQUE: hai người bấm đăng ký cùng một tên gần như cùng lúc
            if ($e->getCode() === '23000') {
                Session::flash('error', 'Tên đăng nhập này vừa có người dùng, hãy chọn tên khác.');
                redirect('register');
            }
            throw $e;
        }

        RateLimiter::hit($limitKey, $window);
        Session::clearOld();
        Auth::login($id);
        Session::flash('success', 'Tạo tài khoản thành công! Hãy tải game và đăng nhập bằng tài khoản này.');
        redirect('account');
    }

    // ------------------------------------------------------------------
    //  ĐĂNG NHẬP
    // ------------------------------------------------------------------

    public function showLogin(): void
    {
        $this->requireGuest();
        $this->view('auth/login', ['title' => 'Đăng nhập']);
    }

    public function login(): void
    {
        $this->requireGuest();
        $this->requireCsrf();

        $username = strtolower($this->post('username'));
        $password = (string) ($_POST['password'] ?? '');
        Session::flashOld(['username' => $username]);

        $limitKey = 'login:' . client_ip();
        $max      = (int) config('limits.login_max');
        $window   = (int) config('limits.login_window');
        if (RateLimiter::tooMany($limitKey, $max, $window)) {
            Session::flash('error', 'Bạn nhập sai quá nhiều lần. Hãy thử lại sau ít phút.');
            redirect('login');
        }

        $account = (new AccountModel())->findForLogin($username);

        // Báo lỗi CHUNG cho cả "sai tên" lẫn "sai mật khẩu": nếu báo riêng, kẻ xấu sẽ dò ra tên nào tồn tại.
        if ($account === null || !AccountModel::verifyPassword($password, (string) $account['password'])) {
            RateLimiter::hit($limitKey, $window);
            Session::flash('error', 'Sai tên đăng nhập hoặc mật khẩu.');
            redirect('login');
        }

        if ((int) $account['ban'] !== 0) {
            Session::flash('error', 'Tài khoản này đã bị khoá.');
            redirect('login');
        }

        RateLimiter::clear($limitKey);
        Session::clearOld();
        Auth::login((int) $account['id']);
        redirect('account');
    }

    // ------------------------------------------------------------------
    //  ĐĂNG XUẤT (dùng POST + CSRF để không ai ép bạn đăng xuất bằng một đường link)
    // ------------------------------------------------------------------

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        Session::flash('info', 'Bạn đã đăng xuất. Hẹn gặp lại, chiến binh!');
        redirect('');
    }
}
