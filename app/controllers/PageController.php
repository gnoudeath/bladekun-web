<?php
/**
 * PageController – các trang chữ tĩnh: Giới thiệu, Luật chơi, Hướng dẫn.
 *
 * Mẫu đơn giản nhất của MVC: không có Model (không cần database), mỗi hàm chỉ chọn một View.
 * Muốn sửa nội dung, mở file view tương ứng trong app/views/pages/ và sửa chữ trực tiếp.
 */
class PageController extends Controller
{
    public function about(): void
    {
        $this->view('pages/about', ['title' => 'Giới thiệu']);
    }

    public function rules(): void
    {
        $this->view('pages/rules', ['title' => 'Luật chơi']);
    }

    public function guide(): void
    {
        $this->view('pages/guide', ['title' => 'Hướng dẫn tân thủ']);
    }
}
