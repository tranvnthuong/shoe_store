<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include("../configs/db.php");

// Nếu chưa đăng nhập thì chuyển sang login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Đơn hàng - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
  <?php include("../layout/header.php") ?>
  <div class="container my-5">
    <h2 class="mb-4 text-center">📦 Đơn hàng của tôi</h2>

    <?php if ($result && $result->num_rows > 0): ?>
      <table class="table table-bordered table-striped text-center align-middle shadow">
        <thead class="table-dark">
          <tr>
            <th>Mã đơn</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td>#<?= $row['id'] ?></td>
              <td><?= $row['created_at'] ?></td>
              <?php
              $order_id = $row['id'];
              $sql_items = "SELECT SUM(price * quantity) AS total FROM order_items WHERE order_id = $order_id";
              $total_res = $conn->query($sql_items);
              $total_row = $total_res->fetch_assoc();
              $total = $total_row['total'] ?? 0;
              ?>
              <td><?= number_format($total, 0, ',', '.') ?> VND</td>

              <td>
                <?php
                switch ($row['status']) {
                  case 'pending':
                    echo '<span class="badge bg-warning">⏳ Chờ xác nhận</span>';
                    break;
                  case 'processing':
                    echo '<span class="badge bg-info">📦 Chờ lấy hàng</span>';
                    break;
                  case 'shipping':
                    echo '<span class="badge bg-primary">🚚 Đang giao</span>';
                    break;
                  case 'completed':
                    echo '<span class="badge bg-success">✅ Đã giao</span>';
                    break;
                  case 'returned':
                    echo '<span class="badge bg-secondary">↩️ Trả hàng</span>';
                    break;
                  case 'canceled':
                    echo '<span class="badge bg-danger">❌ Đã hủy</span>';
                    break;
                  default:
                    echo '<span class="badge bg-dark">Không rõ</span>';
                }
                ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info text-center">Bạn chưa có đơn hàng nào.</div>
    <?php endif; ?>
  </div>
  <?php include("../layout/footer.php") ?>
  <?php include("../layout/header.php") ?>
</body>

</html>