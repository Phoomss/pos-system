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

<?php
include '../../backend/config/condb.php';

$result = $conn->query(
    "SELECT u.u_id, u.u_name, u.u_username, u.u_phone, r.r_name 
FROM users_table u 
INNER JOIN roles_table r ON u.r_id = r.r_id
WHERE r.r_name = 'user'"
);

$conn->close();
?>

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
                            <h1 class="m-0">ผู้ใช้งาน</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#exampleModal">
                        <i class="fa fa-plus"></i> เพิ่มข้อมูล ผู้ใช้งาน
                    </button>

                    <div class="table-container">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">ชื่อ-นามสกุล</th>
                                    <th scope="col">ชื่อผู้ใช้งาน</th>
                                    <th scope="col">เบอร์โทร</th>
                                    <th scope="col">สถานะผู้ใช้งาน</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0) { ?>
                                    <?php foreach ($result as $row) { ?>
                                        <tr>
                                            <td>
                                                <?php echo @$l += 1; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['u_name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['u_username']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['u_phone']; ?>
                                            </td>
                                            <td>
                                                <?php echo $row['r_name']; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center align-content-center">
                                                    <p class="mx-2">
                                                        <a href="./user_edit.php?u_id=<?php echo $row['u_id']; ?>"
                                                            class="btn btn-warning"><i class="fas fa-pencil-alt"></i> แก้ไข</a>
                                                    </p>
                                                    <p>
                                                        <a href="../../backend/user_db.php?u_id=<?php echo $row['u_id']; ?>&user=del" class="del-btn btn btn-danger"><i class="fas fa-trash"></i> ลบ</a>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6" class="no-data">ไม่พบข้อมูล</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if (isset($_GET['d'])) { ?>
                            <div class="flash-data" data-flashdata="<?php echo $_GET['d']; ?>"></div>
                        <?php } ?>
                        <script>
                            $('.del-btn').on('click', function(e) {
                                e.preventDefault();
                                const href = $(this).attr('href');
                                Swal.fire({
                                    title: 'ต้องการลบข้อมูลนี้ใช่ไหม?',
                                    text: "คุณจะไม่สามารถกู้ได้หลังจากลบไปแล้ว",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes'
                                }).then((result) => {
                                    if (result.value) {
                                        document.location.href = href;
                                    }
                                });
                            });
                            const flashdata = $('.flash-data').data('flashdata');
                            if (flashdata) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: 'ข้อมูลถูกลบเรียบร้อยแล้ว'
                                });
                            }
                        </script>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php include_once('../layout/footer.php') ?>
    </div>

    <!-- Modal for Adding Users -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="../../backend/user_db.php" method="POST">
                <input type="hidden" name="user" value="add">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h5 class="modal-title" id="exampleModalLabel">เพิ่มข้อมูล ผู้ใช้งาน</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="u_name" class="col-sm-3 col-form-label">ชื่อ-นามสกุล</label>
                            <div class="col-sm-9">
                                <input id="u_name" name="u_name" type="text" required class="form-control" minlength="3" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="u_username" class="col-sm-3 col-form-label">ชื่อผู้ใช้งาน</label>
                            <div class="col-sm-9">
                                <input id="u_username" name="u_username" type="text" required class="form-control" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="u_phone" class="col-sm-3 col-form-label">เบอร์โทร</label>
                            <div class="col-sm-9">
                                <input id="u_phone" name="u_phone" type="text" required class="form-control" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="r_id" class="col-sm-3 col-form-label">สถานะผู้ใช้งาน</label>
                            <div class="col-sm-9">
                                <select id="r_id" name="r_id" class="form-control" required>
                                    <!-- Populate with role options -->
                                    <?php
                                    include '../../backend/config/condb.php';
                                    $roles = $conn->query("SELECT r_id, r_name FROM roles_table");
                                    while ($role = $roles->fetch_assoc()) {
                                        echo "<option value='{$role['r_id']}'>{$role['r_name']}</option>";
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="u_password" class="col-sm-3 col-form-label">รหัสผ่าน</label>
                            <div class="col-sm-9">
                                <input id="u_password" name="u_password" type="password" required class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> ยืนยัน</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include_once('../layout/config/script.php') ?>

</body>

</html>