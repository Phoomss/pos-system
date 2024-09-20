<?php
error_reporting(error_reporting() & ~E_NOTICE);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check database connection
include '../../backend/config/condb.php';

// Sanitize input
$p_id = isset($_GET['p_id']) ? mysqli_real_escape_string($conn, $_GET['p_id']) : '';
$act = isset($_GET['act']) ? mysqli_real_escape_string($conn, $_GET['act']) : '';

// Add product to cart
if ($act == 'add' && !empty($p_id)) {
    if (isset($_SESSION['cart'][$p_id])) {
        $_SESSION['cart'][$p_id]++; // Increase quantity
    } else {
        $_SESSION['cart'][$p_id] = 1; // Set quantity to 1 if new item
    }
}

// Remove product from cart
if ($act == 'remove' && !empty($p_id)) {
    unset($_SESSION['cart'][$p_id]);
}

// Update cart quantities
if ($act == 'update' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['amount'])) {
        $amount_array = $_POST['amount'];
        foreach ($amount_array as $p_id => $amount) {
            // Check if quantity is at least 1
            if ($amount >= 1) {
                $_SESSION['cart'][$p_id] = $amount;
            } else {
                // Remove item if quantity is less than 1
                unset($_SESSION['cart'][$p_id]);
            }
        }
    }
}
?>

<form id="frmcart" name="frmcart" method="post" action="?act=update">
    <h4>รายการสั่งซื้อ</h4>
    <br>
    <table border="0" align="center" class="table table-hover table-bordered table-striped">
        <tr>
            <td>#</td>
            <td>สินค้า</td>
            <td>ราคา</td>
            <td>จำนวน</td>
            <td>รวม(บาท)</td>
            <td>ลบ</td>
        </tr>

        <?php
        $total = 0;
        $ii = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $p_id => $qty) {
                $stmt = $conn->prepare("SELECT * FROM products_table WHERE p_id = ?");
                $stmt->bind_param("i", $p_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $sum = $row['p_price'] * $qty;
                    $total += $sum;

                    echo "<tr>";
                    echo "<td>" . ++$ii . "</td>";
                    echo "<td>" . htmlspecialchars($row['p_name']) . "</td>";
                    echo "<td align='right'>" . number_format($row['p_price'], 2) . "</td>";
                    echo "<td><input type='number' name='amount[$p_id]' value='$qty' class='form-control' min='1' onchange='this.form.submit()' /></td>";
                    echo "<td align='right'>" . number_format($sum, 2) . "</td>";
                    echo "<td><a href='?p_id=$p_id&act=remove' class='btn btn-danger btn-xs'>ลบ</a></td>";
                    echo "</tr>";
                }
                $stmt->close();
            }

            echo "<tr>";
            echo "<td colspan='5' align='right'><b>ราคารวม</b></td>";
            echo "<td align='right'>" . number_format($total, 2) . "</td>";
            echo "<td></td>";
            echo "</tr>";
        } else {
            echo "<tr><td colspan='6' align='center'>ไม่มีสินค้าในตะกร้า</td></tr>";
        }
        ?>
    </table>

    <p align="right">
        <input type="submit" name="button" id="button" value="อัพเดตรายการ" class="btn btn-warning" />
        <input type="button" name="Submit2" value="ทำรายการต่อไป" onclick="window.location='confirm.php';" class="btn btn-primary" />
    </p>
</form>