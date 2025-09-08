<?php
session_start();
include("../configs/db.php");

// Chỉ admin mới được vào
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit;
}

// Xóa đơn hàng
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_orders.php");
    exit;
}

// Cập nhật trạng thái đơn hàng
if (isset($_POST['update_status'])) {
    $id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    header("Location: manage_orders.php");
    exit;
}

// Lấy danh sách đơn hàng
$sql = "SELECT o.*, u.full_name, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý đơn hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <h2 class="mb-4">📦 Quản lý đơn hàng</h2>
  <a href="dashboard.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Quay lại Dashboard</a>

  <table class="table table-bordered table-hover align-middle text-center">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Khách hàng</th>
        <th>Email</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Ngày đặt</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= number_format($row['total'],0,',','.') ?> VND</td>
          <td>
            <form method="POST" class="d-flex justify-content-center align-items-center">
              <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
              <select name="status" class="form-select form-select-sm me-2">
                <option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>⏳ Chờ xử lý</option>
                <option value="processing" <?= $row['status']=='processing'?'selected':'' ?>>🔄 Đang xử lý</option>
                <option value="shipped" <?= $row['status']=='shipped'?'selected':'' ?>>🚚 Đã giao</option>
                <option value="cancelled" <?= $row['status']=='cancelled'?'selected':'' ?>>❌ Đã hủy</option>
              </select>
              <button type="submit" name="update_status" class="btn btn-sm btn-primary">Cập nhật</button>
            </form>
          </td>
          <td><?= $row['created_at'] ?></td>
          <td>
            <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
              <i class="bi bi-eye"></i> Xem
            </a>
            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa đơn hàng này?')">
              <i class="bi bi-trash"></i> Xóa
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
