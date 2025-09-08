<?php
session_start();
include("../configs/db.php");

// Thêm vào đầu file sau session_start()
$discount = 0;
$coupon_code = "";

// Áp dụng mã giảm giá
if (isset($_POST['apply_coupon'])) {
    $coupon_code = strtoupper(trim($_POST['coupon']));
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code=? AND (expiry IS NULL OR expiry >= CURDATE()) LIMIT 1");
    $stmt->bind_param("s", $coupon_code);
    $stmt->execute();
    $coupon = $stmt->get_result()->fetch_assoc();

    if ($coupon) {
        // Tính giảm
        if ($coupon['type'] == 'percent') {
            $discount = ($total * $coupon['discount']) / 100;
        } else {
            $discount = $coupon['discount'];
        }
        $_SESSION['coupon'] = [
          'code' => $coupon['code'],
          'discount' => $discount
        ];
    } else {
        unset($_SESSION['coupon']);
        echo "<div class='alert alert-danger'>Mã giảm giá không hợp lệ hoặc đã hết hạn.</div>";
    }
}

// Nếu đã áp dụng coupon trước đó
if (isset($_SESSION['coupon'])) {
    $discount = $_SESSION['coupon']['discount'];
    $coupon_code = $_SESSION['coupon']['code'];
}

include("../configs/db.php");

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Thêm vào giỏ
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = $qty;
    } else {
        $_SESSION['cart'][$id] += $qty;
        
    }
    header("Location: cart.php");
    exit;
}


// Xóa sản phẩm
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

// Cập nhật số lượng
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Giỏ hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <h2>🛒 Giỏ hàng của bạn</h2>

  <?php if (empty($_SESSION['cart'])): ?>
    <div class="alert alert-info">Giỏ hàng trống. <a href="products.php">Mua sắm ngay</a></div>
  <?php else: ?>
    <form method="POST">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $id => $qty):
            $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();
            $subtotal = $product['price'] * $qty;
            $total += $subtotal;
        ?>
          <tr>
            <td><img src="<?= $product['image'] ?>" width="80"></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= number_format($product['price'],0,',','.') ?> VND</td>
            <td><input type="number" name="qty[<?= $id ?>]" value="<?= $qty ?>" min="1" class="form-control w-50 mx-auto"></td>
            <td><?= number_format($subtotal,0,',','.') ?> VND</td>
            <td><a href="cart.php?remove=<?= $id ?>" class="btn btn-sm btn-danger">Xóa</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <div class="d-flex justify-content-between">
        <h4>Tổng: <span class="text-danger"><?= number_format($total,0,',','.') ?> VND</span></h4>
        <div>
          <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
          <a href="checkout.php" class="btn btn-success">Thanh toán</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
