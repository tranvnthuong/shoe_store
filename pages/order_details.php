<?php
session_start();
include("../configs/db.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);
$error = "";

// Nếu không có order_id
if ($order_id <= 0) {
    header("Location: orders.php");
    exit;
}

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT o.*, u.email, u.full_name, u.phone, u.address
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        WHERE o.id=? AND o.user_id=?
                        LIMIT 1");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $error = "❌ Không tìm thấy đơn hàng";
} else {
    // Lấy chi tiết sản phẩm
    $stmt2 = $conn->prepare("SELECT oi.*, p.name, p.image, v.name as variant_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_variants v ON oi.variant_id = v.id
        WHERE oi.order_id=?");
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();
    $items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi tiết đơn hàng #<?= $order_id ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
  <style>
    body { background: #f8f9fa; }
    .card { border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .order-progress .step { flex: 1; text-align: center; position: relative; }
    .order-progress .step:before {
        content: ""; position: absolute; top: 12px; left: 50%; height: 4px; width: 100%;
        background: #dee2e6; z-index: -1;
    }
    .order-progress .step:first-child:before { display: none; }
    .order-progress .circle {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 8px; font-size: 14px; font-weight: bold;
        background: #dee2e6; color: #6c757d;
    }
    .order-progress .active .circle { background: #0d6efd; color: #fff; }
    .order-progress .done .circle { background: #198754; color: #fff; }
  </style>
</head>
<body>
<?php include("../layout/header.php"); ?>

<div class="container" style="padding-top: 80px; padding-bottom:40px;">

  <a href="orders.php" class="btn btn-outline-secondary mb-3">
    ⬅ Quay lại danh sách đơn hàng
  </a>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php else: ?>

  <!-- Card thông tin đơn -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">📦 Đơn hàng #<?= $order['id'] ?></h4>
    </div>
    <div class="card-body">
      <p><i class="fa fa-calendar"></i> <b>Ngày đặt:</b> <?= $order['created_at'] ?></p>
      <p><i class="fa fa-user"></i> <b>Người nhận:</b> <?= htmlspecialchars($order['full_name']) ?></p>
      <p><i class="fa fa-phone"></i> <b>SĐT:</b> <?= htmlspecialchars($order['phone']) ?></p>
      <p><i class="fa fa-map-marker-alt"></i> <b>Địa chỉ:</b> <?= htmlspecialchars($order['address']) ?></p>
      <p><i class="fa fa-credit-card"></i> <b>Thanh toán:</b> <?= htmlspecialchars($order['payment_method']) ?></p>
      <p><i class="fa fa-info-circle"></i> <b>Trạng thái:</b> 
        <span class="badge bg-info text-dark"><?= ucfirst($order['status']) ?></span>
      </p>
    </div>
  </div>

  <!-- Tiến trình trạng thái -->
  <div class="card mb-4">
    <div class="card-body order-progress d-flex">
      <?php
        $steps = ["pending" => "Xác nhận", "processing" => "Chuẩn bị", "shipping" => "Đang giao", "completed" => "Hoàn tất"];
        $done = true;
        foreach ($steps as $key => $label):
            $class = "";
            if ($order['status'] == $key) { $class = "active"; $done = false; }
            elseif ($done) { $class = "done"; }
      ?>
        <div class="step <?= $class ?>">
          <div class="circle"><?= substr($label,0,1) ?></div>
          <div><?= $label ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Sản phẩm -->
  <div class="card mb-4">
    <div class="card-header bg-dark text-white">🛒 Sản phẩm</div>
    <div class="card-body p-0">
      <table class="table table-bordered text-center align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Ảnh</th>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>SL</th>
            <th>Thành tiền</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><img src="<?= $item['image'] ?>" width="70"></td>
            <td><?= htmlspecialchars($item['name']) ?> 
                <?php if ($item['variant_name']) echo "<br><small>".htmlspecialchars($item['variant_name'])."</small>"; ?>
            </td>
            <td><?= number_format($item['price'], 0, ',', '.') ?> VND</td>
            <td><?= $item['quantity'] ?></td>
            <td><b><?= number_format($item['price']*$item['quantity'], 0, ',', '.') ?> VND</b></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tổng tiền -->
  <div class="card">
    <div class="card-body text-end">
      <h5>Tạm tính: <?= number_format($order['total'], 0, ',', '.') ?> VND</h5>
      <?php if (!empty($order['shipping_fee'])): ?>
        <h6>Phí vận chuyển: <?= number_format($order['shipping_fee'], 0, ',', '.') ?> VND</h6>
      <?php endif; ?>
      <h4 class="text-danger">Tổng cộng: <?= number_format($order['total_price'], 0, ',', '.') ?> VND</h4>
    </div>
  </div>

  <!-- Nút hành động -->
  <div class="mt-3 text-end">
    <?php if ($order['status'] == 'pending'): ?>
      <a href="cancel_order.php?id=<?= $order['id'] ?>" class="btn btn-danger"
         onclick="return confirm('Bạn chắc chắn muốn hủy đơn?')">
        ❌ Hủy đơn hàng
      </a>
    <?php elseif ($order['status'] == 'shipping'): ?>
      <a href="confirm_received.php?id=<?= $order['id'] ?>" class="btn btn-success"
         onclick="return confirm('Xác nhận đã nhận hàng?')">
        ✅ Đã nhận hàng
      </a>
    <?php endif; ?>
    <button onclick="window.print()" class="btn btn-outline-dark">
      🖨 In hóa đơn
    </button>
  </div>

  <?php endif; ?>
</div>

<?php include("../layout/footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
