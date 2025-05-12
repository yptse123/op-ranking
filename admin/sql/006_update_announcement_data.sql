-- Description: Update data in the ranking table
--
INSERT INTO op_ranking.announcement
(id, image_url, content, starttime, endtime, `rank`, is_active, created_at, updated_at)
VALUES(1, '/op-ranking-page/images/no1.png', '最新遊戲！', '2025-05-11 10:40:00', '2025-05-29 10:40:00', 1, 1, '2025-05-12 10:38:41', NULL);