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

    if (isset($_POST['product']) && $_POST['product'] == "add") {
        // Product add code
        $p_name = mysqli_real_escape_string($conn, $_POST["p_name"]);
        $p_detail = mysqli_real_escape_string($conn, $_POST["p_detail"]);
        $p_price = mysqli_real_escape_string($conn, $_POST["p_price"]);

        $date1 = date("Ymd_His");
        $numrand = (mt_rand());
        $upload = $_FILES['p_image']['name'];
        if ($upload != '') {
            $path = "../uploads/";
            $type = strrchr($_FILES['p_image']['name'], ".");
            $newname = $numrand . $date1 . $type;
            $path_copy = $path . $newname;
            move_uploaded_file($_FILES['p_image']['tmp_name'], $path_copy);
        } else {
            $newname = '';
        }

        $sql = "INSERT INTO products_table (p_name, p_detail, p_price, p_image)
                VALUES ('$p_name', '$p_detail', '$p_price', '$newname')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'เพิ่มสินค้าเรียบร้อยแล้ว!'
                }).then(function() {
                    window.location = '../frontend/admin/products.php?product_add=product_add';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: 'เกิดข้อผิดพลาดในการเพิ่มสินค้า!'
                }).then(function() {
                    window.location = '../frontend/admin/products.php?product_add_error=product_add_error';
                });
            </script>";
        }
    } elseif (isset($_POST['product']) && $_POST['product'] == "edit") {
        // Product edit code
        $p_id = mysqli_real_escape_string($conn, $_POST["p_id"]);
        $p_name = mysqli_real_escape_string($conn, $_POST["p_name"]);
        $p_detail = mysqli_real_escape_string($conn, $_POST["p_detail"]);
        $p_price = mysqli_real_escape_string($conn, $_POST["p_price"]);
        $file1 = $_POST['file1'];

        $date1 = date("Ymd_His");
        $numrand = (mt_rand());
        $upload = $_FILES['p_image']['name'];
        if ($upload != '') {
            $path = "../uploads/";
            $type = strrchr($_FILES['p_image']['name'], ".");
            $newname = $numrand . $date1 . $type;
            $path_copy = $path . $newname;
            move_uploaded_file($_FILES['p_image']['tmp_name'], $path_copy);
        } else {
            $newname = $file1;
        }

        $sql = "UPDATE products_table SET 
                p_name = '$p_name', 
                p_detail = '$p_detail', 
                p_price = '$p_price', 
                p_image = '$newname' 
                WHERE p_id = $p_id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'แก้ไขข้อมูลเรียบร้อยแล้ว!'
                }).then(function() {
                    window.location = '../frontend/admin/products.php';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: 'เกิดข้อผิดพลาดในการแก้ไขสินค้า!'
                }).then(function() {
                    window.location = '../frontend/admin/product_edit.php?p_id=$p_id&&product_edit_error=product_edit_error';
                });
            </script>";
        }
    } elseif (isset($_GET['product']) && $_GET['product'] == "del") {
        // Product delete code
        $p_id = mysqli_real_escape_string($conn, $_GET["p_id"]);
        $sql_del = "DELETE FROM products_table WHERE p_id = $p_id";
        $result_del = mysqli_query($conn, $sql_del);

        if ($result_del) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: 'ลบสินค้าเรียบร้อยแล้ว!'
                }).then(function() {
                    window.location = '../frontend/admin/products.php?product_del=product_del';
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด!',
                    text: 'เกิดข้อผิดพลาดในการลบสินค้า!'
                }).then(function() {
                    window.location = '../frontend/admin/products.php?product_del_error=product_del_error';
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด!',
                text: 'ไม่มีการดำเนินการ!'
            }).then(function() {
                window.location = '../frontend/admin/products.php?product_no=product_no';
            });
        </script>";
    }
    ?>
</body>