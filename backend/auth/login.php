<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
</head>

<body>

    <?php
    session_start(); // เริ่มต้นเซสชัน

    include("../config/condb.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // รับข้อมูลจากฟอร์ม
        $username = $_POST['u_username'];

        // ตรวจสอบชื่อผู้ใช้ในฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM users_table WHERE u_username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // เก็บข้อมูลผู้ใช้ในเซสชัน
            $_SESSION["u_id"] = $user["u_id"];
            $_SESSION["u_username"] = $user["u_username"];
            $_SESSION["u_name"] = $user["u_name"];
            $_SESSION["r_id"] = $user["r_id"]; // เก็บ r_id ในเซสชัน

            // Redirect based on user role
            echo "<script>
                var r_id = " . $user['r_id'] . ";
                if (r_id === 1) {
                  window.location = '../../frontend/admin/index.php';
                } else if (r_id === 2) {
                  window.location = '../../frontend/user/index.php';
                }
            </script>";
        } else {
            // แสดงข้อความเมื่อชื่อผู้ใช้งานไม่ถูกต้อง
            echo "<script>
                alert('เข้าสู่ระบบไม่สำเร็จ! ไม่พบชื่อผู้ใช้งาน');
                window.location = '../../index.php';
            </script>";
        }

        $stmt->close();
    }

    $conn->close();
    ?>

</body>

</html>