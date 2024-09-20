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
    session_start();
    include './config/condb.php';

    // Check if user is logged in
    $u_id = $_SESSION['u_id'] ?? '';
    if (empty($u_id)) {
        session_destroy();
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด!',
            text: 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล!'
        }).then(function() {
            window.location='../../../frontend/user/index.php';
        });
        </script>";
        exit();
    }

    // Retrieve request parameters
    $od_status = $_REQUEST['od_status'] ?? '';
    $q_order = $_REQUEST['q_order'] ?? '';
    $table_number = $_REQUEST['table_number'] ?? 0;
    $pay_amount1 = $_REQUEST['pay_amount1'] ?? 0;
    $pay_amount2 = $_REQUEST['pay_amount2'] ?? 0;
    $order_date = date("Y-m-d G:i:s");

    // Begin transaction
    mysqli_query($conn, "BEGIN");

    // Insert into orders_table
    $sql1 = "INSERT INTO orders_table (u_id, od_status, q_order, table_number, pay_amount1, pay_amount2, o_date) 
              VALUES ('$u_id', '$od_status', '$q_order', '$table_number', '$pay_amount1', '$pay_amount2', '$order_date')";
    $query1 = mysqli_query($conn, $sql1);
    if (!$query1) {
        die("Error inserting into orders_table: " . mysqli_error($conn));
    }

    // Retrieve the last inserted order_id
    $sql2 = "SELECT MAX(o_id) as order_id FROM orders_table WHERE u_id = '$u_id'";
    $query2 = mysqli_query($conn, $sql2);
    if (!$query2) {
        die("Error retrieving order_id: " . mysqli_error($conn));
    }
    $row = mysqli_fetch_array($query2);
    $order_id = $row['order_id'];

    // Check if the cart exists and is not empty
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด!',
            text: 'ไม่มีสินค้าในตะกร้า'
        }).then(function() {
            window.location='../frontend/user/index.php';
        });
        </script>";
        mysqli_query($conn, "ROLLBACK");
        exit();
    }

    // Process the cart items and calculate total
    $total_order_amount = 0; // Initialize total amount
    foreach ($_SESSION['cart'] as $p_id => $qty) {
        $sql3 = "SELECT * FROM products_table WHERE p_id=$p_id";
        $query3 = mysqli_query($conn, $sql3);
        if (!$query3) {
            die("Error retrieving product details: " . mysqli_error($conn));
        }
        $row3 = mysqli_fetch_array($query3);
        $productPrice = $row3['p_price'];
        $total = $productPrice * $qty;
        $total_order_amount += $total; 

        $sql4 = "INSERT INTO order_details_table (o_id, p_id, qty, od_date, total) 
                 VALUES ('$order_id', '$p_id', '$qty', '$order_date', '$total')";
        $query4 = mysqli_query($conn, $sql4);
        if (!$query4) {
            die("Error inserting into order_details_table: " . mysqli_error($conn));
        }
    }

    // Fetch the employee name based on u_id
    $sqlEmp = "SELECT u_name FROM users_table WHERE u_id = '$u_id'";
    $queryEmp = mysqli_query($conn, $sqlEmp);
    $rowEmp = mysqli_fetch_array($queryEmp);
    $u_name = $rowEmp['u_name'] ?? 'ไม่ระบุ';

    // Finalize transaction
    if ($query1) {
        mysqli_query($conn, "COMMIT");
        unset($_SESSION['cart']); // Clear the cart

        // Success message
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'เพิ่มข้อมูลเรียบร้อยแล้ว!'
            }).then(function() {
                window.location = '../frontend/user/list_sale.php'; // Redirect after success
            });
        </script>";
    } else {
        mysqli_query($conn, "ROLLBACK");
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด!',
            text: 'บันทึกข้อมูลไม่สำเร็จ กรุณาติดต่อเจ้าหน้าที่'
        }).then(function() {
            window.location = '../../../frontend/user/index.php';
        });
        </script>";
    }
    ?>
</body>

</html>
