<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้าวมันไก่น้องนัน</title>

    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    include('../backend/config/condb.php');

    // Handle Create Operation
    if (isset($_POST['role']) && $_POST['role'] == "add") {
        $name = $_POST['r_name'];

        $query = "INSERT INTO roles_table (r_name) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $name);

        if ($stmt->execute()) {
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'เพิ่มข้อมูลเรียบร้อยแล้ว!'
            }).then(function() {
                window.location = '../frontend/admin/roles.php?role_add=role_add';
            });
            </script>";
        } else {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล!'
            }).then(function() {
                window.location = '../frontend/admin/roles.php?role_add_error=role_add_error';
            });
            </script>";
        }
    }

    // Handle Update Operation
    elseif (isset($_POST['role']) && $_POST['role'] == "edit") {
        $id = $_POST['r_id'];
        $name = $_POST['r_name'];

        $query = "UPDATE roles_table SET r_name = ? WHERE r_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $name, $id);

        if ($stmt->execute()) {
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'อัปเดตข้อมูลเรียบร้อยแล้ว!'
            }).then(function() {
                window.location = '../frontend/admin/roles.php';
            });
            </script>";
        } else {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล!'
            }).then(function() {
                window.location = '../frontend/admin/roles.php?role_edit_error=role_edit_error';
            });
            </script>";
        }
    } elseif (isset($_GET['role']) && $_GET['role'] == "del") {
        $id = $_GET['r_id'];

        $query = "DELETE FROM roles_table WHERE r_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'ลบข้อมูลเรียบร้อยแล้ว!'
            }).then(function() {
                window.location = '../frontend/admin/roles.php';
            });
            </script>";
        } else {
            error_log("Delete error: " . $stmt->error);  // Log error for debugging
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: 'เกิดข้อผิดพลาดในการลบข้อมูล!'
            }).then(function() {
                window.location = '../frontend/admin/roles.php?role_delete_error=role_delete_error';
            });
            </script>";
        }
    }
    ?>
</body>

</html>