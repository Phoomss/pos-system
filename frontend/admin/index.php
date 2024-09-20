<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once('../../backend/config/condb.php');

// ดึงข้อมูลยอดขายจากฐานข้อมูล
$query = "SELECT DATE(od.od_date) AS sales_date, SUM(o.pay_amount2) AS total_pay_amount
          FROM order_details_table od
          JOIN orders_table o ON od.o_id = o.o_id
          GROUP BY sales_date
          ORDER BY sales_date;";
$result = $conn->query($query);

$dates = [];
$sales = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['sales_date'];
        $sales[] = $row['total_pay_amount'];
    }
}

// ดึงข้อมูลยอดขายรายเดือน
$query_monthly = "SELECT DATE_FORMAT(od.od_date, '%Y-%m') AS sales_month, SUM(o.pay_amount2) AS total_pay_amount
                  FROM order_details_table od
                  JOIN orders_table o ON od.o_id = o.o_id
                  GROUP BY sales_month
                  ORDER BY sales_month;";
$result_monthly = $conn->query($query_monthly);

$months = [];
$sales_monthly = [];

if ($result_monthly->num_rows > 0) {
    while ($row = $result_monthly->fetch_assoc()) {
        $months[] = $row['sales_month'];
        $sales_monthly[] = $row['total_pay_amount'];
    }
}

// ดึงข้อมูลยอดขายตามประเภท
$query_types = "SELECT 
    od_status, 
    COUNT(*) AS count,
    SUM(pay_amount2) AS total_sales
FROM 
    orders_table
GROUP BY 
    od_status;";
$result_types = $conn->query($query_types);

$order_types = [];
$order_counts = [];
$order_totals = [];

if ($result_types->num_rows > 0) {
    while ($row = $result_types->fetch_assoc()) {
        $order_types[] = $row['od_status'];
        $order_counts[] = $row['count'];
    }
}

// ดึงข้อมูลยอดขายรายปี
$query_yearly = "SELECT YEAR(od.od_date) AS sales_year, SUM(o.pay_amount2) AS total_pay_amount
                 FROM order_details_table od
                 JOIN orders_table o ON od.o_id = o.o_id
                 GROUP BY sales_year
                 ORDER BY sales_year;";
$result_yearly = $conn->query($query_yearly);

$years = [];
$sales_yearly = [];

if ($result_yearly->num_rows > 0) {
    while ($row = $result_yearly->fetch_assoc()) {
        $years[] = $row['sales_year'];
        $sales_yearly[] = $row['total_pay_amount'];
    }
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้าวมันไก่น้องนัน</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <?php include_once('../layout/config/library.php') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        h1,
        h2 {
            color: #343a40;
        }

        .container-fluid {
            margin-top: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once('../layout/header.php') ?>
        <?php include_once('./sidenav.php') ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-3">Dashboard</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">ยอดขายต่อวัน</h2>
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">ยอดขายต่อเดือน</h2>
                                    <canvas id="monthlySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">ยอดขายต่อปี</h2>
                                    <canvas id="yearlySalesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">อัตราการซื้อกลับบ้านและทานที่ร้าน</h2>
                                    <canvas id="orderTypeChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include_once('../layout/footer.php') ?>
    </div>

    <script>
        // JavaScript สำหรับ Chart.js
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'ยอดขายต่อวัน',
                    data: <?php echo json_encode($sales); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthlySalesChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'ยอดขายต่อเดือน',
                    data: <?php echo json_encode($sales_monthly); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const orderTypeCtx = document.getElementById('orderTypeChart').getContext('2d');
        const orderTypeChart = new Chart(orderTypeCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($order_types); ?>,
                datasets: [{
                    label: 'อัตราการซื้อกลับบ้านและทานที่ร้าน',
                    data: <?php echo json_encode($order_counts); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'อัตราการซื้อกลับบ้านและทานที่ร้าน'
                    }
                }
            }
        });

        const yearlyCtx = document.getElementById('yearlySalesChart').getContext('2d');
        const yearlySalesChart = new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($years); ?>,
                datasets: [{
                    label: 'ยอดขายต่อปี',
                    data: <?php echo json_encode($sales_yearly); ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <?php include_once('../layout/config/script.php') ?>
</body>

</html>
>