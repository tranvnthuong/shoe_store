<?php
session_start();
include("../includes/db.php");

// Kiểm tra quyền admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Lấy doanh thu theo tháng
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total) AS revenue
        FROM orders
        WHERE status='Hoàn tất'
        GROUP BY month
        ORDER BY month";
$result = $conn->query($sql);

$months = [];
$revenues = [];
while($row = $result->fetch_assoc()) {
    $months[] = $row['month'];
    $revenues[] = $row['revenue'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thống kê Doanh thu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<div class="container my-5">
  <h3>📈 Thống kê Doanh thu theo tháng</h3>
  <canvas id="revenueChart" height="100"></canvas>

  <!-- Bảng chi tiết -->
  <table class="table table-bordered bg-white mt-4">
    <thead class="table-dark">
      <tr>
        <th>Tháng</th>
        <th>Doanh thu</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($months as $i => $m): ?>
      <tr>
        <td><?= $m ?></td>
        <td><?= number_format($revenues[$i],0,',','.') ?> VND</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
const ctx = document.getElementById('revenueChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($months) ?>,
    datasets: [{
      label: 'Doanh thu (VND)',
      data: <?= json_encode($revenues) ?>,
      backgroundColor: 'rgba(54, 162, 235, 0.6)'
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
        ticks: { callback: value => value.toLocaleString() + " VND" }
      }
    }
  }
});
</script>
</body>
</html>
