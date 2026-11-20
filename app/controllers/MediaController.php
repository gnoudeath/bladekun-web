<?php
/**
 * MediaController – trả ảnh gốc của game cho trình duyệt:   ?r=media&id=1641
 * (Web và server game chạy cùng máy nên đọc thẳng file; không cần copy hàng nghìn ảnh vào web.)
 */
class MediaController extends Controller
{
    public function icon(): void
    {
        $id   = (int) ($_GET['id'] ?? -1);          // ép số nguyên: chặn mọi kiểu "../../"
        $file = GameAssets::iconPath($id);
        if ($file === null) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Không có ảnh';
            return;
        }
        $etag = '"' . md5($file . filemtime($file)) . '"';
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=86400');
        header('ETag: ' . $etag);
        if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {   // trình duyệt đã có bản này
            http_response_code(304);
            return;
        }
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }
}
