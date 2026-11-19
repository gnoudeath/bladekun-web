<?php
/**
 * HomeController – Trang chủ.
 *
 * Gom vài mảnh dữ liệu từ nhiều Model để dựng trang giới thiệu:
 * số liệu tổng quan, top 5 chiến binh mạnh nhất, vài tin mới.
 */
class HomeController extends Controller
{
    public function index(): void
    {
        $players = new PlayerModel();
        $news    = new NewsModel();
        $accounts = new AccountModel();

        $this->view('home/index', [
            'title'        => null,                                  // null = dùng tên website làm tiêu đề
            'topPlayers'   => $players->topByPower(5),
            'latestNews'   => $news->latest(3),
            'accountCount' => $accounts->countAll(),
            'playerCount'  => $players->countAll(),
            'planetCounts' => $players->countByPlanet(),          // số chiến binh mỗi hành tinh
        ]);
    }
}
