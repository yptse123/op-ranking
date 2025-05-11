<?php ob_start(); include "include/header.php"; ?>

<?php

$condition = array();
$bannerData = PM::getSingleton("Database")->getCollection("banner", $condition, "rank DESC");

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
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>輪播廣告</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="">
                                <a href="banner_edit.php" class="btn btn-primary btn-new">新增輪播廣告</a>
                            </div>
                            <table class="table table-striped table-bordered table-hover dataTables" >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>輪播廣告</th>
                                        <th>網址</th>
                                        <th>圖片</th>
                                        <th>顯示次數</th>
                                        <th>點擊次數</th>
                                        <th>排序(愈大優先 前台只顯示前6個)</th>
                                        <th>新增時間</th>
                                        <th>最後修改時間</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($bannerData as $row): ?>
                                    <tr>
                                        <td><?php echo $row["id"] ?></td>
                                        <td><?php echo $row["title"] ?></td>
                                        <td><?php echo $row["url"] ?></td>
                                        <td><img src="<?php echo $row["image_url"] ?>" style="max-width: 100px;"></td>
                                        <td><?php echo $row["impression"] ?></td>
                                        <td><?php echo $row["click"] ?></td>
                                        <td><?php echo $row["rank"] ?></td>
                                        <td><?php echo $row["created_at"] ?></td>
                                        <td><?php echo $row["updated_at"] ?></td>
                                        <td>
                                            <a href="banner_edit.php?id=<?php echo $row["id"] ?>" class="btn btn-primary btn-edit">修改</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include "include/footer.php"; ?>

    <script>
        $(document).ready(function () {

            $('.dataTables').dataTable({
                responsive: false,
                pageLength: 20,
                searching: false,
                "lengthMenu": [ 20, 50, 100 ],
                "order": [[ 4, "desc" ]],
                language: {
                    url: './config/zh_Hant.json',
                },
                scrollCollapse: true,
            });
        
        });

    </script>

</body>

</html>
