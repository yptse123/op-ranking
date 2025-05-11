-- Description: Update data in the ranking table
--
UPDATE op_ranking.ranking SET redeem_code='234' WHERE id=2;
UPDATE op_ranking.ranking SET redeem_code='123' WHERE id=1;

-- Description: Insert data into the banner table
--
INSERT INTO op_ranking.banner
(id, title, url, image_url, `rank`, impression, click, created_at, updated_at)
VALUES(1, 'Ur1', './op-ranking-page/images/test-banner.png', './op-ranking-page/images/test-banner.png', 1, 0, 0, '2025-05-11 15:13:26', NULL);