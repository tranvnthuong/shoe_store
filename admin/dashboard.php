<?php
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit;
}

// Giả lập dữ liệu doanh thu theo tháng (sau này thay bằng SQL query từ bảng orders)
$months = ["Th1", "Th2", "Th3", "Th4", "Th5", "Th6", "Th7", "Th8", "Th9", "Th10", "Th11", "Th12"];
$revenues = [12000000, 11000000, 19000000, 14000000, 20000000, 25000000, 23000000, 17000000, 22000000, 30000000, 28000000, 35000000];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Admin Dashboard</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <?php include("../layout/admin_header.php") ?>
      <!-- Main Content -->
      <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 content">
        <h2 class="mb-4">📊 Bảng điều khiển</h2>

        <div class="row g-4">
          <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
              <i class="bi bi-people card-icon"></i>
              <h5 class="mt-2">Người dùng</h5>
              <p class="text-muted">120</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
              <i class="bi bi-box-seam card-icon"></i>
              <h5 class="mt-2">Sản phẩm</h5>
              <p class="text-muted">340</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
              <i class="bi bi-cart-check card-icon"></i>
              <h5 class="mt-2">Đơn hàng</h5>
              <p class="text-muted">87</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm text-center p-3">
              <i class="bi bi-cash-coin card-icon"></i>
              <h5 class="mt-2">Doanh thu</h5>
              <p class="text-muted">50,000,000 VND</p>
            </div>
          </div>
        </div>

        <!-- Biểu đồ doanh thu -->
        <div class="mt-5">
          <h4>📈 Biểu đồ doanh thu theo tháng</h4>
          <canvas id="revenueChart" height="100"></canvas>
        </div>
      </main>
    </div>
  </div>

  <script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
          label: 'Doanh thu (VND)',
          data: <?= json_encode($revenues) ?>,
          borderColor: 'rgba(13,110,253,1)',
          backgroundColor: 'rgba(13,110,253,0.2)',
          borderWidth: 2,
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#0d6efd'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          },
        },
        scales: {
          y: {
            ticks: {
              callback: function(value) {
                return value.toLocaleString() + ' đ';
              }
            }
          }
        }
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>