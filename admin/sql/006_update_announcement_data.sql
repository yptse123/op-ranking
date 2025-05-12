-- Set proper character set for entire table
ALTER TABLE op_ranking.announcement CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Now insert the data with proper encoding
INSERT INTO op_ranking.announcement
(id, image_url, content, starttime, endtime, `rank`, is_active, created_at, updated_at)
VALUES(1, '/op-ranking-page/images/no1.png', '最新遊戲！', '2025-05-11 10:40:00', '2025-05-29 10:40:00', 1, 1, '2025-05-12 10:38:41', NULL);