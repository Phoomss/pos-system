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

    if (isset($_POST['user']) && $_POST['user'] == 'add') {
        $fullname = $_POST['u_name'];
        $username = $_POST['u_username'];
        $password = $_POST['u_password'];
        $phone = $_POST['u_phone'];
        $role = $_POST['r_id'];

        // Hash password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users_table (u_name, u_username, u_password, u_phone, r_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $username, $hashedPassword, $phone, $role);

        if ($stmt->execute()) {
            echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: 'เพิ่มข้อมูลผู้ใช้งานเรียบร้อยแล้ว',
                confirmButtonText: 'ตกลง'
            }).then(() => {
               window.location = '../frontend/admin/users.php?role_add=role_add';
            });
        </script>";
        } else {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเพิ่มข้อมูลได้',
                confirmButtonText: 'ตกลง'
           }).then(function() {
                window.location = '../frontend/admin/users.php?user_add_error=user_add_error';
            });
        </script>";
        }
    } elseif (isset($_POST['user']) && $_POST['user'] == 'update') {
        $userId = $_POST['u_id'];
        $fullname = $_POST['u_name'];
        $username = $_POST['u_username'];
        $phone = $_POST['u_phone'];
        $role = $_POST['r_id'];
        $password = $_POST['u_password'];

        // Prepare SQL query
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users_table SET u_name = ?, u_username = ?, u_password = ?, u_phone = ?, r_id = ? WHERE u_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssii", $fullname, $username, $hashedPassword, $phone, $role, $userId);
        } else {
            $sql = "UPDATE users_table SET u_name = ?, u_username = ?, u_phone = ?, r_id = ? WHERE u_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssiii", $fullname, $username, $phone, $role, $userId);
        }

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'ข้อมูลผู้ใช้งานถูกอัปเดตเรียบร้อยแล้ว',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location = '../frontend/admin/users.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถอัปเดตข้อมูลได้',
                    confirmButtonText: 'ตกลง'
               }).then(function() {
                window.location = '../frontend/admin/users.php?user_edit_error=user_edit_error';
            });
            </script>";
        }
    } elseif (isset($_GET['user']) && $_GET['user'] == 'del') {
        $userId = $_GET['u_id'];

        $sql = "DELETE FROM users_table WHERE u_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'ข้อมูลผู้ใช้งานถูกลบเรียบร้อยแล้ว',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    window.location = '../frontend/admin/users.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถลบข้อมูลได้',
                    confirmButtonText: 'ตกลง'
                 }).then(function() {
                window.location = '../frontend/admin/users.php?user_delete_error=user_delete_error';
            });
            </script>";
        }
    } elseif (isset($_POST['user']) && $_POST['user'] == "edit_profile") {
        $userId = $_POST['u_id'];  // Ensure u_id is passed securely
        $fullname = $_POST['u_name'];
        $username = $_POST['u_username'];
        $phone = $_POST['u_phone'];
        $password = $_POST['u_password'];  // Get the password input

        // Prepare SQL query depending on whether the user entered a new password
        if (!empty($password)) {
            // Hash the new password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // SQL query to update user profile with password change
            $sql = "UPDATE users_table SET u_name = ?, u_username = ?, u_password = ?, u_phone = ? WHERE u_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $fullname, $username, $hashedPassword, $phone, $userId);
        } else {
            // SQL query to update user profile without changing the password
            $sql = "UPDATE users_table SET u_name = ?, u_username = ?, u_phone = ? WHERE u_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $fullname, $username, $phone, $userId);
        }

        // Execute the query
        if ($stmt->execute()) {
            // Success message with SweetAlert
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'ข้อมูลโปรไฟล์ของคุณถูกอัปเดตเรียบร้อยแล้ว',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                     window.location = '../frontend/admin/user_info.php';
                });
            </script>";
        } else {
            // Error message if the query fails
            error_log("MySQL Error: " . $stmt->error); // Log the error for debugging
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถอัปเดตโปรไฟล์ได้',
                    confirmButtonText: 'ตกลง'
               }).then(() => {
                    window.location = '../frontend/user_info.php?profile_edit_error=error';
                });
            </script>";
        }
    } elseif (isset($_POST['user']) && $_POST['user'] == "edit_profile_user") {
        $userId = $_POST['u_id'];  // Ensure u_id is passed securely
        $fullname = $_POST['u_name'];
        $username = $_POST['u_username'];
        $phone = $_POST['u_phone'];
        $password = $_POST['u_password'];  // Get the password input

        // Prepare SQL query depending on whether the user entered a new password
        if (!empty($password)) {
            // Hash the new password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // SQL query to update user profile with password change
            $sql = "UPDATE users_table SET u_name = ?, u_username = ?, u_password = ?, u_phone = ? WHERE u_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $fullname, $username, $hashedPassword, $phone, $userId);
        } else {
            // SQL query to update user profile without changing the password
            $sql = "UPDATE users_table SET u_name = ?, u_username = ?, u_phone = ? WHERE u_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $fullname, $username, $phone, $userId);
        }

        // Execute the query
        if ($stmt->execute()) {
            // Success message with SweetAlert
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'ข้อมูลโปรไฟล์ของคุณถูกอัปเดตเรียบร้อยแล้ว',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                     window.location = '../frontend/user/user_info.php';
                });
            </script>";
        } else {
            // Error message if the query fails
            error_log("MySQL Error: " . $stmt->error); // Log the error for debugging
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถอัปเดตโปรไฟล์ได้',
                    confirmButtonText: 'ตกลง'
               }).then(() => {
                    window.location = '../frontend/user_info.php?profile_edit_error=error';
                });
            </script>";
        }
    }
    ?>


</body>

</html>