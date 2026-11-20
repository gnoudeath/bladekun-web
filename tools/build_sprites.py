#!/usr/bin/env python3
"""
build_sprites.py – cắt ảnh nhân vật & icon trang bị từ file game gốc để website dùng.

Chạy MỘT LẦN (hoặc khi bạn thêm trang phục mới), cần Python 3 + Pillow (pip install pillow):

    python tools/build_sprites.py --game "D:/duong/dan/Nro-Online"

Nó làm gì:
 1. Đọc  data/part/part                       -> danh sách "bộ phận" (đầu/thân/chân) và id ảnh nhỏ của từng bộ phận.
 2. Đọc  resources/data/nro/update_data/image -> bảng "id ảnh nhỏ -> (tờ ảnh, x, y, rộng, cao)".
 3. Cắt từ resources/normal/data/4/img/BigN.png (bản zoom x4) và lưu từng mảnh vào public/assets/img/parts/.
 4. Ghi public/assets/data/parts.json (vị trí lệch dx, dy của mảnh) để PHP xếp chồng đầu + thân + chân.
 5. Copy icon trang bị (resources/normal/image/4/icon/<icon_id>.png) cho mọi item trong Sql/Sql Nro.sql (hoặc --sql).

Ảnh nhỏ có id lớn hơn bảng ảnh (trang phục mới) được đọc thẳng từ resources/normal/image/4/icon/<id>.png.
Lưu ý: mảnh nào KHÔNG có ảnh trong thư mục game (ví dụ trang phục mới được client tải từ máy chủ ảnh)
sẽ bị bỏ qua; website tự lùi về bộ đồ cơ bản theo hành tinh.
"""
import argparse, json, os, re, shutil, struct
from PIL import Image

ZOOM = 4  # thư mục data/4 là bản phóng to x4 so với toạ độ trong bảng ảnh


def load_image_table(path):
    d = open(path, 'rb').read()
    n = struct.unpack('>H', d[:2])[0]
    # mỗi dòng 9 byte: tờ ảnh(1) x(2) y(2) rộng(2) cao(2)
    return [struct.unpack('>BHHHH', d[2 + 9 * i: 11 + 9 * i]) for i in range(n)]


def load_parts(path):
    d = open(path, 'rb').read()
    n = struct.unpack('>h', d[:2])[0]
    cnt = {0: 3, 1: 17, 2: 14}  # số khung ảnh của đầu / thân / chân
    p, parts = 2, []
    for _ in range(n):
        t = d[p]; p += 1
        e = []
        for _ in range(cnt[t]):
            e.append(struct.unpack('>hbb', d[p:p + 4])); p += 4
        parts.append((t, e))
    return parts


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--game', required=True, help='thư mục gốc project game (Nro-Online)')
    ap.add_argument('--sql', help='file .sql chứa bảng item_template (mặc định Sql/Sql Nro.sql trong game)')
    a = ap.parse_args()
    root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    out_img = os.path.join(root, 'public/assets/img')
    os.makedirs(out_img + '/parts', exist_ok=True)
    os.makedirs(root + '/public/assets/extra/icon', exist_ok=True)

    g = a.game
    ents = load_image_table(f'{g}/resources/data/nro/update_data/image')
    parts = load_parts(f'{g}/data/part/part')
    atlas = {}

    def sprite(i):
        if i < 0: return None
        if i >= len(ents):
            # Id lớn hơn bảng ảnh = ảnh rời, game lưu nguyên mảnh ở resources/normal/image/4/icon/<id>.png
            # (ví dụ áo/quần Thánh Tôn: id 24002, 24017...). Cắt sẵn thì không cần cắt từ tờ ảnh.
            f = f'{g}/resources/normal/image/4/icon/{i}.png'
            return Image.open(f).convert('RGBA') if os.path.exists(f) else None   # chỉ để lấy kích thước, KHÔNG lưu lại
        t, x, y, w, h = ents[i]
        if t not in atlas:
            f = f'{g}/resources/normal/data/4/img/Big{t}.png'
            if not os.path.exists(f): return None
            atlas[t] = Image.open(f).convert('RGBA')
        im = atlas[t].crop((x * ZOOM, y * ZOOM, (x + w) * ZOOM, (y + h) * ZOOM))
        a_ch = im.getchannel('A').point(lambda v: 255 if v > 20 else 0)
        return im if a_ch.getbbox() else None   # ảnh rỗng = chưa có trong thư mục game

    kinds = {0: ('head', 'h', 0), 1: ('body', 'b', 1), 2: ('leg', 'l', 1)}  # (tên, tiền tố, khung dùng khi đứng yên)
    meta = {'head': {}, 'body': {}, 'leg': {}}
    for pid, (t, e) in enumerate(parts):
        name, pre, k = kinds[t]
        ic, dx, dy = e[k]
        im = sprite(ic)
        if im is None: continue
        if ic >= len(ents):
            # ảnh rời: không copy, web đọc thẳng từ thư mục game theo id icon (khoá 'i')
            meta[name][str(pid)] = {'i': ic, 'w': im.width, 'h': im.height, 'dx': dx * ZOOM, 'dy': dy * ZOOM}
            continue
        fn = f'parts/{pre}{pid}.png'
        im.save(f'{out_img}/{fn}', optimize=True)
        meta[name][str(pid)] = {'f': fn, 'w': im.width, 'h': im.height, 'dx': dx * ZOOM, 'dy': dy * ZOOM}
    json.dump(meta, open(f'{root}/public/assets/data/parts.json', 'w'), separators=(',', ':'))
    print('Đã cắt:', {k: len(v) for k, v in meta.items()})

    # --- icon trang bị ---
    sql = a.sql or f'{g}/Sql/Sql Nro.sql'
    s = open(sql, encoding='utf8', errors='ignore').read()
    i = s.index('INSERT INTO `item_template`'); j = s.index(';\n', i)
    ids = {int(m) for m in re.findall(r"^\(\d+, \d+, -?\d+, '(?:[^'\\]|\\.|'')*', '(?:[^'\\]|\\.|'')*', (\d+),", s[i:j], re.M)}
    n = 0
    for icon in sorted(ids):
        src = f'{g}/resources/normal/image/4/icon/{icon}.png'
        if os.path.exists(src):
            shutil.copy(src, f'{root}/public/assets/extra/icon/{icon}.png'); n += 1
    print(f'Đã copy {n}/{len(ids)} icon')


if __name__ == '__main__':
    main()
