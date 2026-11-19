-- =====================================================================
--  DIỄN ĐÀN BLADEKUN – 3 bảng MỚI (không đụng vào bảng của game)
-- =====================================================================
--  Chạy file này MỘT LẦN trong Navicat (chọn đúng database của game, ví dụ nro-d):
--    chuột phải database -> "Chạy tệp SQL" (Execute SQL File), hoặc mở Query rồi dán nội dung vào.
--  Chạy lại không sao: dùng IF NOT EXISTS.

CREATE TABLE IF NOT EXISTS `forum_posts` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id`    INT NOT NULL,                       -- người đăng (account.id)
  `title`         VARCHAR(150) NULL,                  -- tiêu đề (tuỳ chọn)
  `content`       TEXT NOT NULL,
  `media_path`    VARCHAR(255) NULL,                  -- đường dẫn ảnh/video đã tải lên (tương đối trong public/uploads)
  `media_type`    VARCHAR(10) NULL,                   -- 'image' hoặc 'video'
  `pinned`        TINYINT(1) NOT NULL DEFAULT 0,      -- 1 = admin ghim lên đầu
  `like_count`    INT NOT NULL DEFAULT 0,
  `comment_count` INT NOT NULL DEFAULT 0,
  `created_at`    DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_list` (`pinned`, `created_at`),
  KEY `idx_account` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `forum_comments` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id`    INT UNSIGNED NOT NULL,
  `account_id` INT NOT NULL,
  `content`    TEXT NOT NULL,
  `media_path` VARCHAR(255) NULL,
  `media_type` VARCHAR(10) NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`, `created_at`),
  KEY `idx_account` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `forum_likes` (
  `post_id`    INT UNSIGNED NOT NULL,
  `account_id` INT NOT NULL,
  PRIMARY KEY (`post_id`, `account_id`)               -- mỗi người chỉ thích 1 lần mỗi bài
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
