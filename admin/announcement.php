<?php ob_start(); include "include/header.php"; ?>

<?php

$condition = array();
$announcementData = PM::getSingleton("Database")->getCollection("announcement", $condition, "is_active, rank DESC");

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
                            <h5>公告</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="">
                                <a href="announcement_edit.php" class="btn btn-primary btn-new">新增公告</a>
                            </div>
                            <table class="table table-striped table-bordered table-hover dataTables" >
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>圖片</th>
                                        <th>開始時間 (UTC)</th>
                                        <th>結束時間 (UTC)</th>
                                        <th>是否啟用</th>
                                        <th>排序(愈大優先顯示)</th>
                                        <th>新增時間</th>
                                        <th>最後修改時間</th>
                                        <th>動作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($announcementData as $row): ?>
                                    <tr>
                                        <td><?php echo $row["id"] ?></td>
                                        <td><img src="<?php echo $row["image_url"] ?>" style="max-width: 100px;"></td>
                                        <td><?php echo $row["starttime"] ?></td>
                                        <td><?php echo $row["endtime"] ?></td>
                                        <td><?php echo $row["is_active"] ?></td>
                                        <td><?php echo $row["rank"] ?></td>
                                        <td><?php echo $row["created_at"] ?></td>
                                        <td><?php echo $row["updated_at"] ?></td>
                                        <td>
                                            <a href="announcement_edit.php?id=<?php echo $row["id"] ?>" class="btn btn-primary btn-edit">修改</a>
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
                "order": [[ 0, "desc" ]],
                language: {
                    url: './config/zh_Hant.json',
                },
                scrollCollapse: true,
            });
        
        });

    </script>

</body>

</html>
