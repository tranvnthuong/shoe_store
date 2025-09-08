<?php
session_start();
include("../includes/db.php");

// Nếu chưa đăng nhập thì chuyển về login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Lấy thông tin user
$email = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Lấy danh sách đơn hàng của user
$sql = "SELECT * FROM orders WHERE user_id=? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đơn hàng của tôi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <h2>📦 Đơn hàng của tôi</h2>

  <?php if($orders->num_rows == 0): ?>
    <div class="alert alert-info">Bạn chưa có đơn hàng nào. <a href="../products.php">Mua sắm ngay</a></div>
  <?php else: ?>
    <table class="table table-bordered bg-white">
      <thead class="table-dark">
        <tr>
          <th>Mã đơn</th>
          <th>Ngày đặt</th>
          <th>Tổng tiền</th>
          <th>Trạng thái</th>
          <th>Chi tiết</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $orders->fetch_assoc()): ?>
        <tr>
          <td>#<?= $row['id'] ?></td>
          <td><?= $row['created_at'] ?></td>
          <td><?= number_format($row['total'],0,',','.') ?> VND</td>
          <td>
            <?php
              $badge = "secondary";
              if ($row['status']=="Chờ duyệt") $badge="warning";
              if ($row['status']=="Đang giao") $badge="info";
              if ($row['status']=="Hoàn tất") $badge="success";
              if ($row['status']=="Hủy") $badge="danger";
            ?>
            <span class="badge bg-<?= $badge ?>"><?= $row['status'] ?></span>
          </td>
          <td>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detail<?= $row['id'] ?>">Xem</button>
          </td>
        </tr>

        <!-- Modal chi tiết đơn hàng -->
        <div class="modal fade" id="detail<?= $row['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng #<?= $row['id'] ?></h5>
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
  <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
