THƯ MỤC ẢNH BỔ SUNG (những thứ server game KHÔNG có)
=====================================================
Web ưu tiên lấy ảnh thẳng từ thư mục game (config 'assets' => 'game_path').
Nếu game không có ảnh đó, web sẽ tìm ở đây. Cứ chép file vào, không cần sửa code:

  icon/<số>.png     Icon dùng cho trang bị / avatar có id <số>  (vd: icon/32361.png)

Tên file phải là SỐ ĐÚNG ID trong database (item_template.icon_id, head_avatar.avatar_id...).
