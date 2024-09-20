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
        $total_order_amount += $total; // Add to total amount

        $sql4 = "INSERT INTO order_details_table (o_id, p_id, qty, od_date, total) 
                 VALUES ('$order_id', '$p_id', '$qty', '$order_date', '$total')";
        $query4 = mysqli_query($conn, $sql4);
        if (!$query4) {
            die("Error inserting into order_details_table: " . mysqli_error($conn));
        }
    }


    foreach ($_SESSION['cart'] ?? [] as $p_id => $qty) {
        $sql3 = "SELECT * FROM products_table WHERE p_id=$p_id";
        $query3 = mysqli_query($conn, $sql3);

        // ตรวจสอบว่าการ query สำเร็จหรือไม่
        if (!$query3) {
            die("Error retrieving product details: " . mysqli_error($conn));
        }

        $row3 = mysqli_fetch_array($query3);

        // แสดงข้อมูลสินค้าในรูปแบบ array
        echo '<pre>';
        print_r($row3);
        echo '</pre>';

        $productName = $row3['p_name'];
        $productPrice = $row3['p_price'];
        $total = $productPrice * $qty;
    }

    print_r($_SESSION['cart']); // ตรวจสอบเนื้อหาของตะกร้า
    if (!isset($_SESSION['cart'])) {
        echo "Cart is not set!";
    } else if (!is_array($_SESSION['cart'])) {
        echo "Cart is not an array!";
    }

    // Fetch the employee name based on u_id
    $sqlEmp = "SELECT u_name FROM users_table WHERE u_id = '$u_id'";
    $queryEmp = mysqli_query($conn, $sqlEmp);
    $rowEmp = mysqli_fetch_array($queryEmp);
    $u_name = $rowEmp['u_name'] ?? 'ไม่ระบุ';

    // Begin creating PDF after successful order creation
    require('../fpdf/fpdf.php');

    $pdfDirectory = '../pdf/';
    if (!file_exists($pdfDirectory)) {
        mkdir($pdfDirectory, 0777, true);  // Create directory if it doesn't exist
    }

    if ($query1) {
        mysqli_query($conn, "COMMIT");
        unset($_SESSION['cart']); // Clear the cart

        // Create PDF
        $pdf = new FPDF('P', 'cm', array(10.2, 29.7));
        $pdf->AddPage();

        // Add Thai font
        $pdf->AddFont('Sarabun-Bold', '', 'Sarabun-Bold.php');
        $pdf->AddFont('Sarabun-Thin', '', 'Sarabun-Thin.php');
        $width = $pdf->GetPageWidth();

        $pdf->SetFont('Sarabun-Bold', '', 16);
        $pdf->Cell($width - 2, 0, iconv('UTF-8', 'TIS-620', 'ข้าวมันไก่น้องนัน'), 0, 1, 'C');

        // Order details
        $pdf->SetFont('Sarabun-Thin', '', 12);
        $pdf->Cell(0, 2, iconv('UTF-8', 'TIS-620', 'รายละเอียดคำสั่งซื้อ'), 0, 1, 'C');
        $pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', $order_date), 0, 1, 'C');
        $pdf->Cell(0, 1, 'Order ID: ' . $order_id, 0, 1);
        $pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', 'ชื่อพนักงานขาย: ') . iconv('UTF-8', 'TIS-620', $u_name), 0, 1);
        $pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', 'สถานะ: ') . iconv('UTF-8', 'TIS-620', $od_status), 0, 1);
        $pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', 'ลำดับคิว: ') . $q_order, 0, 1);
        $pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', 'ยอดเงินรวม: ') . $total_order_amount . iconv('UTF-8', 'TIS-620', ' บาท'), 0, 1);

        $pdf->Cell(0, 2, iconv('UTF-8', 'TIS-620', 'รายการสินค้า'), 0, 1, 'C');
        foreach ($_SESSION['cart'] ?? [] as $p_id => $qty) {
            $sql3 = "SELECT * FROM products_table WHERE p_id=$p_id";
            $query3 = mysqli_query($conn, $sql3);

            // ตรวจสอบว่าการ query สำเร็จหรือไม่
            if (!$query3) {
                die("Error retrieving product details: " . mysqli_error($conn));
            }

            $row3 = mysqli_fetch_array($query3);

            // ตรวจสอบว่า $row3 มีข้อมูลหรือไม่
            if ($row3) {
                $productName = $row3['p_name'];
                $productPrice = $row3['p_price'];
                $total = $productPrice * $qty;

                $pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', "สินค้า: $productName | จำนวน: $qty | ราคา: $total บาท"), 0, 1);
            } else {
                // ถ้าไม่พบสินค้า ให้แสดงข้อความ
                $pdf->Cell(0, 1, iconv('UTF-8', 'TIS-620', "ไม่พบสินค้า ID: $p_id"), 0, 1);
            }
        }

        // Save the PDF file
        $pdfFile = $pdfDirectory . $order_id . '.pdf';
        $pdf->Output('F', $pdfFile);

        // SweetAlert2 to download the PDF
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: 'เพิ่มข้อมูลเรียบร้อยแล้ว! กำลังดาวน์โหลดไฟล์ PDF...'
            }).then(function() {
                window.location = '$pdfFile';
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