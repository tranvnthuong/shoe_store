<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
include("../configs/db.php");

// Nếu chưa đăng nhập thì chuyển sang login
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Lấy trạng thái filter (nếu có)
$status_filter = isset($_GET['status']) ? $_GET['status'] : "";

// Truy vấn đơn hàng
if ($status_filter && in_array($status_filter, ['pending','processing','shipping','completed','returned','canceled'])) {
  $sql = "SELECT * FROM orders WHERE user_id = ? AND status = ? ORDER BY created_at DESC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $user_id, $status_filter);
} else {
  $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
}
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" />
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body { background: #f5f7fa; }
    .order-card {
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      margin-bottom: 20px;
      opacity: 0;
      transform: translateY(20px);
      animation: fadeUp 0.6s ease forwards;
    }
    @keyframes fadeUp {
      to { opacity: 1; transform: translateY(0); }
    }
    .order-header {
      padding: 15px;
      background: linear-gradient(135deg, #36d1dc, #5b86e5);
      color: white;
      font-weight: 600;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .order-body { padding: 15px; }
    .order-body .info-item { margin-bottom: 8px; font-size: 15px; }
    .order-body .info-item i { margin-right: 6px; color: #007bff; }
    .order-footer { padding: 12px 15px; background: #f9fafc; text-align: right; }
    .status-badge { font-size: 14px; padding: 6px 12px; border-radius: 12px; font-weight: 600; }
  </style>
</head>

<body>
  <?php include("../layout/header.php") ?>
  <div class="container" style="padding-top: 80px; padding-bottom:40px;">
    <h2 class="mb-4 text-center fw-bold">📦 Đơn hàng của tôi</h2>

    <!-- Bộ lọc trạng thái -->
    <form method="GET" class="mb-4 text-center">
      <div class="row justify-content-center">
        <div class="col-md-4">
          <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending" <?= $status_filter=="pending"?"selected":"" ?>>⏳ Chờ xác nhận</option>
            <option value="processing" <?= $status_filter=="processing"?"selected":"" ?>>📦 Chờ lấy hàng</option>
            <option value="shipping" <?= $status_filter=="shipping"?"selected":"" ?>>🚚 Đang giao</option>
            <option value="completed" <?= $status_filter=="completed"?"selected":"" ?>>✅ Đã giao</option>
            <option value="returned" <?= $status_filter=="returned"?"selected":"" ?>>↩️ Trả hàng</option>
            <option value="canceled" <?= $status_filter=="canceled"?"selected":"" ?>>❌ Đã hủy</option>
          </select>
        </div>
      </div>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
        $order_id = $row['id'];
        $sql_items = "SELECT SUM(price * quantity) AS total FROM order_items WHERE order_id = $order_id";
        $total_res = $conn->query($sql_items);
        $total_row = $total_res->fetch_assoc();
        $total = $total_row['total'] ?? 0;

        // Hiển thị trạng thái
        switch ($row['status']) {
          case 'pending':
            $status = '<span class="status-badge bg-warning text-dark">⏳ Chờ xác nhận</span>';
            break;
          case 'processing':
            $status = '<span class="status-badge bg-info text-white">📦 Chờ lấy hàng</span>';
            break;
          case 'shipping':
            $status = '<span class="status-badge bg-primary text-white">🚚 Đang giao</span>';
            break;
          case 'completed':
            $status = '<span class="status-badge bg-success text-white">✅ Đã giao</span>';
            break;
          case 'returned':
            $status = '<span class="status-badge bg-secondary text-white">↩️ Trả hàng</span>';
            break;
          case 'canceled':
            $status = '<span class="status-badge bg-danger text-white">❌ Đã hủy</span>';
            break;
          default:
            $status = '<span class="status-badge bg-dark text-white">Không rõ</span>';
        }
        ?>
        <div class="order-card">
          <div class="order-header">
            <span>Đơn hàng #<?= $row['id'] ?></span>
            <span><?= $status ?></span>
          </div>
          <div class="order-body">
            <div class="info-item"><i class="fa fa-calendar"></i> Ngày đặt: <?= $row['created_at'] ?></div>
            <div class="info-item"><i class="fa fa-coins"></i> Tổng tiền: <strong><?= number_format($total, 0, ',', '.') ?> VND</strong></div>
            <div class="info-item"><i class="fa fa-credit-card"></i> Thanh toán: <?= htmlspecialchars($row['payment_method']) ?></div>
          </div>
          <div class="order-footer">
            <a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
              <i class="fa fa-eye"></i> Xem chi tiết
            </a>
          </div>
        </div>
      <?php endwhile; ?>

    <?php else: ?>
      <div class="alert alert-info text-center shadow-sm">Không có đơn hàng nào trong trạng thái này.</div>
    <?php endif; ?>
  </div>
  <?php include("../layout/footer.php") ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
