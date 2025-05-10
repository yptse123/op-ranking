<?php include('admin/PM/PM.php'); ?>

<?php

$max = 6;

$condition = array();
$rankingData = array();
$rankingData = PM::getSingleton("Database")->getCollection("ranking", $condition, "rank DESC", 1, $max);

if(count($rankingData) < $max)
{
	$loop =  $max-count($rankingData);

	for ($i=0; $i < $loop; $i++) 
	{ 
		$rankingData[$i+count($rankingData)] = array(
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
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/custom.css">

</head>

<body class="index">

	<div class="page">

		<div class="top-container">

			<img src="images/logo.png" class="top-container-logo">
			
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

						<img src="images/no<?php echo $count ?>_.png">

					</div>

					<?php endif; ?>

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

	</div>

</body>

</html>