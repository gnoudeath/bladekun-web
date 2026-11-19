-- =====================================================================
--  TUỲ CHỌN: tăng tốc bảng xếp hạng (không bắt buộc, web vẫn chạy khi không có)
-- =====================================================================
--  Bảng xếp hạng sắp xếp theo player.power. Khi số nhân vật lớn (hàng chục nghìn),
--  một chỉ mục (index) trên cột này giúp trang tải nhanh hơn.
--
--  Cách chạy: mở database của game trong Navicat → New Query → dán đoạn dưới → Run.
--  Chỉ chạy MỘT lần; chạy lại sẽ báo "Duplicate key name" (vô hại).
-- =====================================================================

ALTER TABLE `player` ADD INDEX `idx_player_power` (`power`);
