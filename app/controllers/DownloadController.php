<?php
/**
 * DownloadController – Trang hướng dẫn tải game và địa chỉ máy chủ.
 * Chỉ hiển thị thông tin lấy từ config/config.php, không đụng tới database.
 */
class DownloadController extends Controller
{
    public function index(): void
    {
        $this->view('download/index', [
            'title'      => 'Tải game',
            'clientUrl'  => (string) config('download.client_url', ''),
            'serverHost' => (string) config('download.server_host'),
            'serverPort' => (int) config('download.server_port'),
        ]);
    }
}
