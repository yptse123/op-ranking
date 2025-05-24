<?php include('admin/PM/PM.php'); ?>

<?php

$rankingMax = 6;
$bannerMax = 6;
$preOrderMax = 3;

$condition = array("pre_order" => 0);
$rankingData = array();
$rankingData = PM::getSingleton("Database")->getCollection("ranking", $condition, "rank DESC", 1, $rankingMax);

$condition = array();
$bannerData = PM::getSingleton("Database")->getCollection("banner", $condition, "rank DESC", 1, $bannerMax);

$condition = array("pre_order" => 1);
$preOrderData = array();
$preOrderData = PM::getSingleton("Database")->getCollection("ranking", $condition, "rank DESC", 1, $preOrderMax);

$condition = array(
	"is_active" => array(
		"oper" => "=",
		"val" => "1",
	),
	"starttime" => array(
		"oper" => "<=",
		"val" => date("Y-m-d H:i:s"),
	),
	"endtime" => array(
		"oper" => ">=",
		"val" => date("Y-m-d H:i:s"),
	),
);
$announcementData = PM::getSingleton("Database")->getCollection("announcement", $condition, "rank DESC", 1, 999);

if(count($rankingData) < $rankingMax)
{
	$loop =  $rankingMax-count($rankingData);

	for ($i=0; $i < $loop; $i++) 
	{ 
		$rankingData[$i+count($rankingData)] = array(
			"title" => "尚未推出 敬請期待",
			"url" => "",
			"thumbnail_url" => "images/coming.png",
		);
	}
}

if(count($preOrderData) < $preOrderMax)
{
	$loop =  $preOrderMax-count($preOrderData);

	for ($i=0; $i < $loop; $i++) 
	{ 
		$preOrderData[$i+count($preOrderData)] = array(
			"title" => "尚未推出 敬請期待",
			"url" => "",
			"thumbnail_url" => "images/coming.png",
		);
	}
}

?>

<!DOCTYPE html>
<html class="wide" lang="en">

<head>
    <title>&#8734; GAMES 遊戲排名</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="title" content="&#8734; GAMES 遊戲排名">
    <meta name="keywords" content="&#8734; GAMES 遊戲排名">
    <meta name="description" content="&#8734; GAMES 遊戲排名">
	<meta name="csrf-token" content="<?php echo htmlspecialchars(PM::getSingleton('Common')->generateCsrfToken()); ?>">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap-grid.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/custom.css">

</head>

<body class="index">

	<div class="page">

		<div class="top-container">

			<img src="images/logo.png" class="top-container-logo">
			
		</div>

		<div class="banner-container">

			<div class="owl-carousel owl-theme">

				<?php foreach($bannerData as $row): ?>

				<div class="banner-item" data-banner-id="<?php echo $row["id"] ?>">
					<a href="<?php echo $row["url"] ?>" class="banner" target="_blank" >
						<img src="<?php echo $row["image_url"] ?>" alt="<?php echo $row["title"] ?>" title="<?php echo $row["title"] ?>" /> 
					</a> 
				</div>

				<?php endforeach; ?>
				
			</div>

		</div>

		<div class="mid-container">

			<div class="container">

				<div class="container-title">遊戲排名</div>

				<?php

				$count = 1;

				foreach($rankingData as $row):

				?>

				<?php if($count % 3 == 1): ?>

				<div class="row">

				<?php endif; ?>

				<div class="item">

					<?php if($count <= 3 && !empty($row["url"])): ?>

					<div class="item-badge">

						<img src="images/no<?php echo $count ?>.png">

					</div>

					<?php endif; ?>

					<div class="item-container">

						<a href="<?php echo $row["url"] ?>" target="_blank">

							<img src="<?php echo $row["thumbnail_url"] ?>" class="item-thumb" alt="<?php echo $row["title"] ?>" title="<?php echo $row["title"] ?>">

						</a>

					</div>

					<div class="item-redeem">

						<?php if(!empty($row["redeem_code"])): ?>

						<img src="images/redeem_code.png" class="item-redeem-img" alt="" title="" data-toggle="modal" data-target="#redeem-<?php echo $row["id"] ?>">

						<div class="modal fade" id="redeem-<?php echo $row["id"] ?>" tabindex="-1" role="dialog" aria-labelledby="redeem-<?php echo $row["id"] ?>" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content">
									<div class="modal-body">
										<div class="item-redeem-code-title">
											<?php echo $row["title"] ?>兌換碼
										</div>
										<div class="item-redeem-code">
											<?php echo $row["redeem_code"] ?>
										</div>
										<img src="images/close.png" class="item-redeem-close" alt="" title="" data-dismiss="modal">
									</div>
								</div>
							</div>
						</div>

						<?php endif; ?>

					</div>

				</div>

				<?php if($count % 3 == 0): ?>

				</div>

				<?php endif; ?>

				<?php

					$count++;

				endforeach;

				?>

				<div class="container-title">即將推出</div>

				<?php

				$count = 1;

				foreach($preOrderData as $row):

				?>

				<?php if($count % 3 == 1): ?>

				<div class="row">

				<?php endif; ?>

				<div class="item">

					<div class="item-container">

						<a href="<?php echo $row["url"] ?>" target="_blank">

							<img src="<?php echo $row["thumbnail_url"] ?>" class="item-thumb" alt="<?php echo $row["title"] ?>" title="<?php echo $row["title"] ?>">

						</a>

					</div>

				</div>

				<?php if($count % 3 == 0): ?>

				</div>

				<?php endif; ?>

				<?php

					$count++;

				endforeach;

				?>

			</div>
			
		</div>

		<div class="modal fade announcement-modal" id="announcementModal" tabindex="-1" role="dialog" aria-labelledby="announcementModal" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body">
						<div>
							<img id="announcementImage" class="announcement-image" src="" alt="">
						</div>
						<div id="announcementContent" class="announcement-content"></div>
						<div class="item-announcement-close-container">
							<img src="images/close.png" class="item-announcement-close" alt="" title="" data-dismiss="modal">
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

</body>

<!-- Mainly scripts -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

<script src="js/owl.carousel.min.js"></script>

<script>
const announcements = <?php echo json_encode($announcementData); ?>;
</script>

<script src="js/custom.js"></script>

</html>