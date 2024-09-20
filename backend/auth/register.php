<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>

    <!-- โหลด SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
    include("../config/condb.php");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // รับข้อมูลจากฟอร์ม
        $fullname = $_POST['u_name'];
        $username = $_POST['u_username'];
        $password = $_POST['u_password'];
        $phone = $_POST['u_phone'];
        $role = 2; // กำหนดค่า r_id เป็น 2

        // ตรวจสอบว่าชื่อผู้ใช้งานซ้ำหรือไม่
        $checkUser = $conn->prepare("SELECT * FROM users_table WHERE u_username = ?");
        $checkUser->bind_param("s", $username);
        $checkUser->execute();
        $result = $checkUser->get_result();

        if ($result->num_rows > 0) {
            // แสดง SweetAlert กรณีชื่อผู้ใช้งานซ้ำ
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ชื่อผู้ใช้งานซ้ำ',
                text: 'ชื่อผู้ใช้งานนี้ถูกใช้แล้ว กรุณาใช้ชื่อผู้ใช้งานอื่น!'
            }).then(function(){
                    window.location = '../../register.php';
                });
            </script>";
        } else {
            // เข้ารหัสรหัสผ่านก่อนบันทึก
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // เตรียมคำสั่ง SQL สำหรับบันทึกข้อมูลผู้ใช้ใหม่
            $stmt = $conn->prepare("INSERT INTO users_table (u_name, u_username, u_password, u_phone, r_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $fullname, $username, $hashedPassword, $phone, $role);

            if ($stmt->execute()) {
                // แสดง SweetAlert และเปลี่ยนหน้าไปยัง index.php
                echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สมัครเข้าใช้งานสำเร็จ!',
                    text: 'คุณได้ทำการสมัครสมาชิกเรียบร้อยแล้ว'
                }).then(function() {
                    window.location = '../../index.php'; // redirect ไปยัง index.php
                });
            </script>";
            } else {
                // แสดงข้อผิดพลาดกรณีบันทึกข้อมูลไม่สำเร็จ
                echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error . "'
                }).then(function(){
                    window.location = '../../register.php';
                });
            </script>";
            }

            $stmt->close();
        }

        $checkUser->close();
    }

    $conn->close();
    ?>

</body>

</html>
