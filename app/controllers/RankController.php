<?php
/**
 * RankController – Bảng xếp hạng sức mạnh.
 *
 * Có nhiều bảng (tab): sức mạnh, đệ tử, diệt boss, top nạp – chọn bằng ?type=... (xem PlayerModel::RANK_TYPES).
 *
 * Đây là tính năng đơn giản nhất của web (chỉ đọc, không cần đăng nhập),
 * nên rất hợp để đọc đầu tiên khi tìm hiểu cách MVC hoạt động:
 *   Router gọi index() -> Model lấy top -> View vẽ bảng.
 */
class RankController extends Controller
{
    /** Chỉ cho phép vài giá trị "số dòng" để người dùng không thể xin 1 triệu dòng làm nặng server. */
    private const ALLOWED_LIMITS = [20, 50, 100];

    public function index(): void
    {
        $limit = (int) ($_GET['top'] ?? 20);
        if (!in_array($limit, self::ALLOWED_LIMITS, true)) {
            $limit = 20;
        }

        // Loại bảng: chỉ nhận giá trị có trong danh sách cho phép, sai thì dùng 'power'.
        $types = PlayerModel::rankTypes();
        $type  = (string) ($_GET['type'] ?? 'power');
        if (!isset($types[$type])) {
            $type = 'power';
        }

        $players = (new PlayerModel())->topBy($type, $limit);

        $this->view('rank/index', [
            'title'   => 'Bảng xếp hạng',
            'players' => $players,
            'limit'   => $limit,
            'limits'  => self::ALLOWED_LIMITS,
            'types'   => $types,
            'type'    => $type,
            // Điểm của người đứng đầu, dùng để vẽ thanh (% so với top 1)
            'topScore' => $players ? (int) $players[0]['score'] : 0,
        ]);
    }
}
