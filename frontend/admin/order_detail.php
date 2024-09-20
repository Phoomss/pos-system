<?php
include_once('../../backend/config/condb.php'); // Include the database connection file
$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

// แก้ไข SQL query ให้ถูกต้อง
$sql = "SELECT d.*, p.*, u.u_name, o.o_date, o.pay_amount2,od_status
        FROM order_details_table AS d
        INNER JOIN products_table AS p ON d.p_id = p.p_id
        INNER JOIN orders_table AS o ON d.o_id = o.o_id
        INNER JOIN users_table AS u ON o.u_id = u.u_id
        WHERE d.o_id = $order_id";

$querypay = mysqli_query($conn, $sql) or die("Error : " . mysqli_error($conn));
$row = mysqli_fetch_assoc($querypay); // ดึงข้อมูลของสมาชิกสำหรับใช้ในส่วนอื่น ๆ
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

        .card-header {
            background-color: #007bff;
            color: white;
        }

        .table thead th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }

        .form-control {
            margin-bottom: 1rem;
        }

        .custom-file-label::after {
            content: "Browse";
        }

        #blah {
            display: none;
            margin-top: 1rem;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once('../layout/header.php') ?>
        <?php include_once('./sidenav.php') ?>
        <div class="content-wrapper center">
            <section class="content">
                <div class="container-fluid">
                    <center>
                        <h4>รายการสั่งซื้อ<br>
                            Order Id : <?php echo $order_id; ?> </br>
                            วัน/เดือน/ปี : <?php echo date('d/m/y', strtotime($row['od_date'])); ?></br>
                            ผู้ทำรายการ : <?php echo $row['u_name']; ?> <br />
                            สถานะ :<?php echo $row['od_status']; ?>
                        </h4>
                    </center>

                    <table border="0" align="center" class="table table-hover table-bordered table-striped">
                        <tr>
                            <td width="5%" align="center">ลำดับสินค้า</td>
                            <td width="35%" align="center">สินค้า</td>
                            <td width="10%" align="center">ราคา/หน่วย</td>
                            <td width="10%" align="center">จำนวน</td>
                            <td width="15%" align="center">รวม(บาท)</td>
                        </tr>
                        <?php
                        $total = 0;
                        $i = 0; // กำหนดค่าเริ่มต้นของตัวแปร $i
                        foreach ($querypay as $rspay) {
                            $total += $rspay['total']; //ราคารวม ทั้ง ตระกร้า
                            echo "<tr>";
                            echo "<td>" . ++$i . "</td>";
                            echo "<td>" . $rspay["p_name"] . "</td>";
                            echo "<td align='right'>" . number_format($rspay["p_price"], 2) . "</td>";
                            echo "<td align='right'>" . $rspay["qty"] . "</td>"; // เพิ่มการแสดงจำนวนสินค้า
                            echo "<td align='right'>" . number_format($rspay['total'], 2) . "</td>";
                            echo "</tr>";
                        }
                        include('../../convertnumtothai.php');
                        ?>
                        <tr>
                            <td></td>
                            <td align='right' colspan="3">
                                <b>ราคารวม
                                    ( <?php echo Convert($total); ?> )
                                </b>
                                <br>
                                <b>ยอดเงินที่รับชำระ
                                    ( <?php echo Convert($row['pay_amount2']); ?> )
                                </b>
                                <br>
                                <?php
                                $pay_amount3 = $row['pay_amount2'] - $total;
                                ?>
                                <b>เงินทอน
                                    ( <?php echo Convert($pay_amount3); ?> )
                                </b>
                            </td>
                            <td align='right' colspan='2'>
                                <b><?php echo number_format($total, 2); ?> บาท</b>
                                <br>
                                <b><?php echo number_format($row['pay_amount2'], 2); ?> บาท</b>
                                <br>
                                <b><?php echo number_format($pay_amount3, 2); ?> บาท</b>
                            </td>
                        </tr>
                    </table>
                </div>
            </section>
        </div>

        <?php include_once('../layout/footer.php') ?>
        <?php include_once('../layout/config/script.php') ?>
    </div>
</body>

</html>