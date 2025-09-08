<?php
session_start();
include("../configs/db.php");

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: ../pages/checkout.php");
    exit;
}

// Nếu chưa đăng nhập thì chuyển qua login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id    = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$full_name  = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
$email      = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Xử lý đặt hàng
if (isset($_POST['checkout'])) {
    $address = $_POST['address'];
    $phone   = $_POST['phone'];
    $payment = $_POST['payment'];

    // Tính tổng
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $p = $stmt->get_result()->fetch_assoc();
        $subtotal = $p['price'] * $qty;
        $total += $subtotal;
    }

    // Tạo đơn hàng
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, address, phone, payment_method, status, created_at) 
                            VALUES (?,?,?,?,?,'pending',NOW())");
    $stmt->bind_param("idsss", $user_id, $total, $address, $phone, $payment);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Thêm chi tiết đơn hàng
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $p = $stmt->get_result()->fetch_assoc();
        $price = $p['price'];

        $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
        $stmt2->bind_param("iiid", $order_id, $id, $qty, $price);
        $stmt2->execute();
    }

    // Xóa giỏ hàng sau khi đặt
    unset($_SESSION['cart']);

    // Giao diện thông báo đẹp
    echo "
    <div class='container my-5'>
      <div class='card shadow-lg border-success'>
        <div class='card-body text-center'>
          <h3 class='text-success'>✅ Đặt hàng thành công!</h3>
          <p class='lead'>Cảm ơn bạn <b>" . htmlspecialchars($full_name) . "</b> đã mua sắm tại cửa hàng.</p>
          <p>Mã đơn hàng của bạn: <b>#{$order_id}</b></p>
          <a href='products.php' class='btn btn-primary mt-3'>Tiếp tục mua sắm</a>
          <a href='orders.php' class='btn btn-outline-success mt-3'>Xem đơn hàng của tôi</a>
        </div>
      </div>
    </div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thanh toán</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <h2>🛒 Thanh toán</h2>
  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Họ tên</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($full_name) ?>" readonly>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
    </div>
    <div class="col-md-6">
      <label class="form-label">Số điện thoại</label>
      <input type="text" name="phone" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Địa chỉ giao hàng</label>
      <input type="text" name="address" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Phương thức thanh toán</label>
      <select name="payment" class="form-select" required>
        <option value="COD">💵 Thanh toán khi nhận hàng</option>
        <option value="Bank">🏦 Chuyển khoản ngân hàng</option>
        <option value="Momo">📱 Ví MoMo</option>
      </select>
    </div>
    <div class="col-md-12">
      <button type="submit" name="checkout" class="btn btn-success w-100">Xác nhận đặt hàng</button>
    </div>
  </form>
</div>
</body>
</html>
