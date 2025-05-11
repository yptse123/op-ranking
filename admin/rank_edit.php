<?php ob_start(); include "include/header.php"; ?>

<?php

$id = 0;
if(!empty($_GET["id"]))
{
    $id = $_GET["id"];
}elseif(!empty($_POST["id"]))
{
    $id = $_POST["id"];
}
$currentUrl = $_SERVER["PHP_SELF"]."?id=".$id;

$error = array();
$message = array();

$readCon = PM::getSingleton("Database")->getReadCon();
$writeCon = PM::getSingleton("Database")->getWriteCon();

$urlRebuild = false;

if(!empty($_POST))
{
    $submit = !empty($_POST["submit"]) ? $_POST["submit"] : '';

    if($submit == "save")
    {
        if(count($error) == 0)
        {
            $sqlData = array(
                "title" => $_POST['title'],
                "url" => $_POST['url'],
                "thumbnail_url" => $_POST['thumbnail_url'],
                "rank" => $_POST['rank'],
                "redeem_code" => $_POST['redeem_code'],
            );

            $sql = "SELECT * FROM ranking 
            WHERE rank = :rank AND id != :id
            ";
            $checkBind = array(
                "rank" => $_POST['rank'],
                "id" => $id,
            );

            $stmt = $readCon->prepare($sql);
            $stmt->execute($checkBind);
            $checkData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(count($checkData) > 0)
            {
                $error[] = "排序 已存在";
            }

            if(count($error) == 0)
            {
                if($id)
                {
                    $sqlData["updated_at"] = date("Y-m-d H:i:s");

                    // Update 
                    PM::getSingleton("Database")->updateRow("ranking", $sqlData, array("id" => $id));
                    $urlRebuild = true;
                    $message[] = "Update Success";
                }
                else
                {
                    $sqlData["created_at"] = date("Y-m-d H:i:s");

                    // Add 
                    $id = PM::getSingleton("Database")->insertRow("ranking", $sqlData);
                    $currentUrl = $_SERVER["PHP_SELF"]."?id=".$id;
                    $urlRebuild = true;
                    $message[] = "Add Success";
                }

                header("Location: index.php");
                exit;
            }
        }
    }
    else if($submit == "remove")
    {
        $sqlData = array(
            "id" => $id,
        );

        $sql = "DELETE FROM `ranking` WHERE id = :id";
        $writeCon->prepare($sql)->execute($sqlData);

        header("Location: index.php");
        exit;
    }
    else
    {
        
    }

    $_POST = array();
}

// user

$rankingData = array();
if($id)
{
    $dbData = PM::getSingleton("Database")->getCollection("ranking", array("id" => $id));
    if(count($dbData))
    {
        $rankingData = reset($dbData);
    }
}

?>

<body class="">

    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <?php include "include/nav.php"; ?>
            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <?php include "include/top.php"; ?>
            </div>
        
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel blank-panel">

                        <?php if(!empty($message) || !empty($error)): ?>

                        <div class="ibox-content">
                            <?php if(!empty($message)): ?>

                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo implode("<br />", $message) ?>
                            </div>

                            <?php endif; ?>

                            <?php if(!empty($error)): ?>

                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo implode("<br />", $error) ?>
                            </div>

                            <?php endif; ?>

                        </div>

                        <?php endif; ?>

                        <div class="panel-heading">
                            <div class="panel-title m-b-md clearfix">
                                <div class="pull-left">
                                    <a href="index.php" class="btn btn-default btn-back" >返回</a>
                                </div>
                                
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-primary btn-save" name="submit" value="save" form="ranking-update-form"> 儲存 </button>

                                    <?php if($id): ?>

                                    <button type="submit" class="btn btn-danger btn-remove" name="submit" value="remove" form="ranking-update-form"> 刪除 </button>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="panel-options">

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#ranking-data-content">遊戲資料</a></li>
                                </ul>
                            </div>
                        </div>

                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal" id="ranking-update-form">
                            <input type="hidden" name="id" value="<?php echo $id ?>" />

                            <div class="panel-body container">

                                <div class="tab-content">
                                    <div id="ranking-data-content" class="tab-pane active">

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 遊戲 <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="title" value="<?php echo !empty($rankingData["title"]) ? htmlentities($rankingData["title"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 網址 <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="url" value="<?php echo !empty($rankingData["url"]) ? htmlentities($rankingData["url"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 圖片 <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="thumbnail_url" value="<?php echo !empty($rankingData["thumbnail_url"]) ? htmlentities($rankingData["thumbnail_url"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 排序(愈大優先 前台只顯示前6個) <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="number" name="rank" value="<?php echo !empty($rankingData["rank"]) ? htmlentities($rankingData["rank"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 兌換碼 <span style="color:red;"></span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="redeem_code" value="<?php echo !empty($rankingData["redeem_code"]) ? htmlentities($rankingData["redeem_code"]) : '' ?>" class="form-control"/>
                                            </div>
                                        </div>

                                        <?php if($id) : ?>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 建立時間 : </label>
                                            <div class="col-sm-10">
                                                <input type="text" name="" value="<?php echo !empty($rankingData["created_at"]) ? htmlentities($rankingData["created_at"]) : '' ?>" class="form-control" readonly/>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 最後修改時間 : </label>
                                            <div class="col-sm-10">
                                                <input type="text" name="" value="<?php echo !empty($rankingData["updated_at"]) ? htmlentities($rankingData["updated_at"]) : '' ?>" class="form-control" readonly/>
                                            </div>
                                            
                                        </div>
                                        
                                        <?php endif; ?>
                                        
                                    </div>
                                   
                                </div>

                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include "include/footer.php"; ?>

    <script>
        $(document).ready(function () {

            <?php if($urlRebuild) { ?>
                window.history.pushState('&#8734; GAMES 遊戲排名', '&#8734; GAMES 遊戲排名', "<?php echo $currentUrl ?>");
            <?php } ?>
        });

    </script>

    <style type="text/css">
        
        table.dataTable th
        {
            white-space: nowrap;
        }

        table.dataTable thead .sorting:after
        {
            margin-left: 3px;
            float: unset;
        }

        .dataTables_wrapper
        {
            overflow: auto;
        }

    </style>

</body>

</html>
