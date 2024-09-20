<?php
include_once('../../backend/config/condb.php'); // Include the database connection file

// ตรวจสอบว่ามีการส่งค่าหน้าปัจจุบันมาหรือไม่ ถ้าไม่มีให้ตั้งค่าหน้าเป็น 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// ตรวจสอบว่ามีการเลือกจำนวนแถวต่อหน้าหรือไม่ ถ้าไม่มีให้ตั้งค่าเป็น 10
$records_per_page = isset($_GET['records_per_page']) ? (int)$_GET['records_per_page'] : 10;

$offset = ($page - 1) * $records_per_page; // คำนวณจุดเริ่มต้นข้อมูล

// Query เพื่อดึงข้อมูลเฉพาะที่ต้องการแสดงในหน้าปัจจุบัน
$query = "SELECT o.*, u.u_name 
          FROM orders_table as o 
          INNER JOIN users_table as u ON o.u_id = u.u_id
          ORDER BY o.o_id DESC
          LIMIT $offset, $records_per_page";

$rs_order = mysqli_query($conn, $query) or die("Error: " . mysqli_error($conn));

// นับจำนวนแถวทั้งหมดในตารางเพื่อคำนวณจำนวนหน้า
$total_query = "SELECT COUNT(*) as total FROM orders_table";
$result_total = mysqli_query($conn, $total_query);
$row_total = mysqli_fetch_assoc($result_total);
$total_pages = ceil($row_total['total'] / $records_per_page);
?>

<form method="GET" action="">
    <label for="records_per_page">แสดงแถวต่อหน้า:</label>
    <select name="records_per_page" class="p-1" id="records_per_page" onchange="this.form.submit()">
        <option value="10" <?php if ($records_per_page == 10) echo 'selected'; ?>>10</option>
        <option value="20" <?php if ($records_per_page == 20) echo 'selected'; ?>>20</option>
        <option value="50" <?php if ($records_per_page == 50) echo 'selected'; ?>>50</option>
        <option value="100" <?php if ($records_per_page == 100) echo 'selected'; ?>>100</option>
    </select>
</form>

<div class="table-container">
    <table id="example1" class="table table-bordered table-hover">
        <thead>
            <tr class="danger">
                <th scope="col">
                    <center>No.</center>
                </th>
                <th scope="col">พนักงานขาย</th>
                <th scope="col">เวลา</th>
                <th scope="col">รายละเอียด</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rs_order as $row) { ?>
                <tr>
                    <td><?php echo $row['o_id']; ?></td>
                    <td><?php echo $row['u_name']; ?></td>
                    <td><?php echo date('d/m/y H:i:s', strtotime($row['o_date'])); ?></td>
                    <td>
                        <a href="order_detail.php?order_id=<?php echo $row['o_id']; ?>&act=view" target="_blank" class="btn btn-success btn-xs">
                            <i class="nav-icon fas fa-clipboard-list"></i> เปิดดูรายการ
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
