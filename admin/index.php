<?php ob_start(); include "include/header.php"; ?>

<?php

$condition = array();
$rankingData = PM::getSingleton("Database")->getCollection("ranking", $condition, "rank DESC");

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
                            <h5>&#8734; GAMES 遊戲排名</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="">
                                <a href="rank_edit.php" class="btn btn-primary btn-new">新增排名</a>
                            </div>
                            <table class="table table-striped table-bordered table-hover dataTables" >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>遊戲</th>
                                        <th>網址</th>
                                        <th>圖片</th>
                                        <th>兌換碼</th>
                                        <th>即將推出</th>
                                        <th>排序(愈大優先 前台只顯示前6個)</th>
                                        <th>新增時間</th>
                                        <th>最後修改時間</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($rankingData as $row): ?>
                                    <tr>
                                        <td><?php echo $row["id"] ?></td>
                                        <td><?php echo $row["title"] ?></td>
                                        <td><?php echo $row["url"] ?></td>
                                        <td><img src="<?php echo $row["thumbnail_url"] ?>" style="max-width: 100px;"></td>
                                        <td><?php echo $row["redeem_code"] ?></td>
                                        <td><?php echo $row["pre_order"] ?></td>
                                        <td><?php echo $row["rank"] ?></td>
                                        <td><?php echo $row["created_at"] ?></td>
                                        <td><?php echo $row["updated_at"] ?></td>
                                        <td>
                                            <a href="rank_edit.php?id=<?php echo $row["id"] ?>" class="btn btn-primary btn-edit">修改</a>
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
                "order": [[ 5, "desc" ]],
                language: {
                    url: './config/zh_Hant.json',
                },
                scrollCollapse: true,
            });
        
        });

    </script>

</body>

</html>
