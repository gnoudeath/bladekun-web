<?php
/**
 * PlayerController – Trang chi tiết (hồ sơ công khai) của một nhân vật: /player?id=...
 * Người chơi bấm vào tên trong bảng xếp hạng để vào đây.
 */
class PlayerController extends Controller
{
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $players = new PlayerModel();
        $p = $id > 0 ? $players->findPublic($id) : null;

        // Không có, hoặc bị ẩn (admin / tài khoản bị khoá) => 404, không tiết lộ là "có nhưng bị ẩn".
        if ($p === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy nhân vật', 'message' => 'Nhân vật này không tồn tại.']);
            return;
        }

        $clans = new ClanModel();
        $this->view('player/show', [
            'title'  => $p['name'],
            'p'      => $p,
            'look'   => (new CharacterModel())->forPlayer((int) $p['id'], (int) $p['gender'], (int) $p['head']),
            'rank'   => $players->rankOf((int) $p['power']),
            'clan'   => $clans->brief((int) $p['clan_id']),   // null nếu chưa vào bang (clan_id = -1)
        ]);
    }
}
