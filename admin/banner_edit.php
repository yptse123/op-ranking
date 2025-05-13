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
                "image_url" => $_POST['image_url'],
                "rank" => $_POST['rank'],
            );

            $sql = "SELECT * FROM banner 
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
                    PM::getSingleton("Database")->updateRow("banner", $sqlData, array("id" => $id));
                    $urlRebuild = true;
                    $message[] = "Update Success";
                }
                else
                {
                    $sqlData["created_at"] = date("Y-m-d H:i:s");

                    // Add 
                    $id = PM::getSingleton("Database")->insertRow("banner", $sqlData);
                    $currentUrl = $_SERVER["PHP_SELF"]."?id=".$id;
                    $urlRebuild = true;
                    $message[] = "Add Success";
                }

                header("Location: banner.php");
                exit;
            }
        }
    }
    else if($submit == "remove")
    {
        $sqlData = array(
            "id" => $id,
        );

        $sql = "DELETE FROM `banner` WHERE id = :id";
        $writeCon->prepare($sql)->execute($sqlData);

        header("Location: banner.php");
        exit;
    }
    else
    {
        
    }

    $_POST = array();
}

// user

$bannerData = array();
if($id)
{
    $dbData = PM::getSingleton("Database")->getCollection("banner", array("id" => $id));
    if(count($dbData))
    {
        $bannerData = reset($dbData);

        $condition = array("banner_id" => $id);
        $bannerImpressionData = PM::getSingleton("Database")->getCollection("banner_impression_log", $condition, "id DESC");
        $bannerClickData = PM::getSingleton("Database")->getCollection("banner_click_log", $condition, "id DESC");
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
                                    <a href="banner.php" class="btn btn-default btn-back" >返回</a>
                                </div>
                                
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-primary btn-save" name="submit" value="save" form="banner-update-form"> 儲存 </button>

                                    <?php if($id): ?>

                                    <button type="submit" class="btn btn-danger btn-remove" name="submit" value="remove" form="banner-update-form"> 刪除 </button>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="panel-options">

                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#banner-data-content">廣告資料</a></li>
                                </ul>
                            </div>
                        </div>

                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal" id="banner-update-form">
                            <input type="hidden" name="id" value="<?php echo $id ?>" />

                            <div class="panel-body container">

                                <div class="tab-content">
                                    <div id="banner-data-content" class="tab-pane active">

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 輪播廣告 <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="title" value="<?php echo !empty($bannerData["title"]) ? htmlentities($bannerData["title"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 網址 <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="url" value="<?php echo !empty($bannerData["url"]) ? htmlentities($bannerData["url"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 圖片 <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="image_url" value="<?php echo !empty($bannerData["image_url"]) ? htmlentities($bannerData["image_url"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 排序(愈大優先 前台只顯示前6個) <span style="color:red;">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="number" name="rank" value="<?php echo !empty($bannerData["rank"]) ? htmlentities($bannerData["rank"]) : '' ?>" class="form-control" required/>
                                            </div>
                                        </div>

                                        <?php if($id) : ?>
                                        
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 顯示次數 : </label>
                                            <div class="col-sm-10">
                                                <input type="text" name="" value="<?php echo !empty($bannerData["impression"]) ? htmlentities($bannerData["impression"]) : '' ?>" class="form-control" readonly/>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 點擊次數 : </label>
                                            <div class="col-sm-10">
                                                <input type="text" name="" value="<?php echo !empty($bannerData["click"]) ? htmlentities($bannerData["click"]) : '' ?>" class="form-control" readonly/>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 建立時間 : </label>
                                            <div class="col-sm-10">
                                                <input type="text" name="" value="<?php echo !empty($bannerData["created_at"]) ? htmlentities($bannerData["created_at"]) : '' ?>" class="form-control" readonly/>
                                            </div>
                                            
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"> 最後修改時間 : </label>
                                            <div class="col-sm-10">
                                                <input type="text" name="" value="<?php echo !empty($bannerData["updated_at"]) ? htmlentities($bannerData["updated_at"]) : '' ?>" class="form-control" readonly/>
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
                window.history.pushState('輪播廣告', '輪播廣告', "<?php echo $currentUrl ?>");
            <?php } ?>

            $('.dataTables').dataTable({
                responsive: false,
                pageLength: 20,
                searching: false,
                "lengthMenu": [ 20, 50, 100 ],
                "order": [[ 2, "desc" ]],
                language: {
                    url: './config/zh_Hant.json',
                },
                scrollCollapse: true,
            });
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
