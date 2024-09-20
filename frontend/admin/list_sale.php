<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้าวมันไก่น้องนัน</title>
    <?php include_once('../layout/config/library.php') ?>
    <style>
        .content-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        .table td,
        .table th {
            padding: 0.75rem;
            text-align: center;
        }

        .table-container {
            overflow-x: auto;
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .no-data {
            text-align: center;
            color: #dc3545;
            /* Red color for "No data found" message */
            font-weight: bold;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once('../layout/header.php') ?>
        <?php include_once('./sidenav.php') ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">ลำดับการสั่งซื้อ</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <!-- Main content -->
            <sectio class="content">
                <div class="container-fluid">
                    <?php
                    $act = (isset($_GET['act']) ? $_GET['act'] : '');
                    if ($act == 'view') {

                        include('./order_detail.php');
                    } else {

                        include('./list_order.php');
                    } ?>
                </div>

            </sectio>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php include_once('../layout/footer.php') ?>
    </div>


    <?php include_once('../layout/config/script.php') ?>

</body>

</html>