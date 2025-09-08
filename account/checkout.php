<?php
session_start();
include("includes/db.php");

// Nếu chưa đăng nhập → yêu cầu login
if (!isset($_SESSION['username'])) {
    header("Location: account/login.php");
    exit;
}

// Nếu giỏ hàng trống
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Lấy thông tin user
$email = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $phone   = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $payment = $_POST['payment']; // COD hoặc Online

    // Tính tổng tiền
    $total = 0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $total += $product['price'] * $qty;
    }

    // Thêm vào bảng orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, 'Chờ duyệt')");
    $stmt->bind_param("id", $user['id'], $total);
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        // Thêm chi tiết sản phẩm
        foreach ($_SESSION['cart'] as $id => $qty) {
            $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            $price = $product['price'];
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)");
            $stmt2->bind_param("iiid", $order_id, $id, $qty, $price);
            $stmt2->execute();

            // Trừ số lượng trong kho
            $conn->query("UPDATE products SET stock = stock - $qty WHERE id=$id");
        }

        // Xóa giỏ hàng
        unset($_SESSION['cart']);
        $success = "Đặt hàng thành công! Mã đơn hàng: #$order_id";
    } else {
        $error = "Lỗi khi đặt hàng!";
    }
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
<div class="container my-5" style="max-width:700px;">
  <h2>💳 Thanh toán</h2>

  <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
  <?php if($success): ?><div class="alert alert-success"><?= $success ?></div>
    <a href="account/orders.php" class="btn btn-primary mt-3">Xem đơn hàng</a>
  <?php else: ?>

  <form method="POST">
    <div class="mb-3">
      <label>Email</label>
      <input type="text" class="form-control" value="<?= $user['email'] ?>" disabled>
    </div>
    <div class="mb-3">
      <label>Số điện thoại</label>
      <input type="text" name="phone" value="<?= $user['phone'] ?? '' ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Địa chỉ nhận hàng</label>
      <textarea name="address" class="form-control" required><?= $user['address'] ?? '' ?></textarea>
    </div>
    <div class="mb-3">
      <label>Phương thức thanh toán</label>
      <select name="payment" class="form-select" required>
        <option value="COD">Thanh toán khi nhận hàng (COD)</option>
        <option value="Online">Thanh toán Online</option>
      </select>
    </div>

    <h4 class="text-danger">Tổng tiền: 
      <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
          $stmt = $conn->prepare("SELECT price FROM products WHERE id=?");
          $stmt->bind_param("i", $id);
          $stmt->execute();
          $product = $stmt->get_result()->fetch_assoc();
          $total += $product['price'] * $qty;
        }
        echo number_format($total,0,',','.') . " VND";
      ?>
    </h4>
    

    <button class="btn btn-success w-100 mt-3">Xác nhận đặt hàng</button>
  </form>
  <?php endif; ?>
</div>
</body>
</html>
