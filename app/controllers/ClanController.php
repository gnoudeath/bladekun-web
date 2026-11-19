<?php
/**
 * ClanController – Xếp hạng bang hội (/clans) và trang chi tiết một bang (/clan?id=...).
 */
class ClanController extends Controller
{
    private const ALLOWED_LIMITS = [20, 50, 100];

    public function index(): void
    {
        $limit = (int) ($_GET['top'] ?? 20);
        if (!in_array($limit, self::ALLOWED_LIMITS, true)) {
            $limit = 20;
        }
        $clans = (new ClanModel())->top($limit);

        $this->view('clan/index', [
            'title'    => 'Bang hội',
            'clans'    => $clans,
            'limit'    => $limit,
            'limits'   => self::ALLOWED_LIMITS,
            'topPower' => $clans ? (int) $clans[0]['power_point'] : 0,
        ]);
    }

    public function show(): void
    {
        $id   = (int) ($_GET['id'] ?? 0);
        $clanModel = new ClanModel();
        $clan = $id > 0 ? $clanModel->find($id) : null;

        if ($clan === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy bang hội', 'message' => 'Bang hội này không tồn tại hoặc đã giải tán.']);
            return;
        }

        $this->view('clan/show', [
            'title'   => 'Bang ' . $clan['name'],
            'clan'    => $clan,
            'rank'    => $clanModel->rankOf((int) $clan['power_point']),
            'members' => (new PlayerModel())->byClan($id, 50),
        ]);
    }
}
