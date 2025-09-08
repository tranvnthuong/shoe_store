<?php
session_start();
include("../includes/db.php");

// Kiểm tra quyền admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Cập nhật trạng thái đơn hàng
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    header("Location: orders.php");
}

// Lấy danh sách đơn hàng
$sql = "SELECT o.*, u.email 
        FROM orders o
        LEFT JOIN users u ON o.user_id=u.id
        ORDER BY o.id DESC";
$orders = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Đơn hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
  <h3>🛒 Quản lý Đơn hàng</h3>

  <table class="table table-bordered bg-white">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Khách hàng</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Ngày đặt</th>
        <th>Chi tiết</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $orders->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= number_format($row['total'],0,',','.') ?> VND</td>
        <td>
          <form method="POST" class="d-flex">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <select name="status" class="form-select form-select-sm">
              <option <?= $row['status']=='Chờ duyệt'?'selected':'' ?>>Chờ duyệt</option>
              <option <?= $row['status']=='Đang giao'?'selected':'' ?>>Đang giao</option>
              <option <?= $row['status']=='Hoàn tất'?'selected':'' ?>>Hoàn tất</option>
              <option <?= $row['status']=='Hủy'?'selected':'' ?>>Hủy</option>
            </select>
            <button type="submit" name="update_status" class="btn btn-sm btn-primary ms-2">Lưu</button>
          </form>
        </td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detail<?= $row['id'] ?>">Xem</button>
        </td>
      </tr>

      <!-- Modal chi tiết đơn hàng -->
      <div class="modal fade" id="detail<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Chi tiết đơn #<?= $row['id'] ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <table class="table">
                <thead>
                  <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $items = $conn->query("SELECT oi.*, p.name FROM order_items oi 
                                         LEFT JOIN products p ON oi.product_id=p.id 
                                         WHERE order_id=".$row['id']);
                  while($it=$items->fetch_assoc()):
                ?>
                  <tr>
                    <td><?= htmlspecialchars($it['name']) ?></td>
                    <td><?= $it['quantity'] ?></td>
                    <td><?= number_format($it['price'],0,',','.') ?> VND</td>
                  </tr>
                <?php endwhile; ?>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
          </div>
        </div>
      </div>

      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
