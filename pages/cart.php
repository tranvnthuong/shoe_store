<?php
session_start();
include("../configs/db.php");

// Nếu người dùng bấm "Mua ngay" thì chuyển tới checkout
if (isset($_GET['buy']) && $_GET['buy'] == 1) {
    header("Location: ../pages/checkout.php");
    exit;
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xóa sản phẩm
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

// Thêm vào giỏ
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $qty = isset($_GET['qty']) ? intval($_GET['qty']) : 1;
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty; // cộng dồn số lượng
    header("Location: cart.php");
    exit;
}

// Cập nhật số lượng
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $qty = intval($qty);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

// Tính tổng tiền trước khi áp dụng coupon
$total = 0;
$items = [];

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        if ($product) {
            $price = (int)$product['price'];
            $subtotal = $price * $qty;
            $total += $subtotal;
            $product['qty'] = $qty;
            $product['subtotal'] = $subtotal;
            $items[] = $product;
        }
    }
}

// Giảm giá
$discount = 0;
$coupon_code = "";

// Áp dụng coupon
if (isset($_POST['apply_coupon'])) {
    $coupon_code = strtoupper(trim($_POST['coupon']));
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code=? AND (expiry IS NULL OR expiry >= CURDATE()) LIMIT 1");
    $stmt->bind_param("s", $coupon_code);
    $stmt->execute();
    $coupon = $stmt->get_result()->fetch_assoc();

    if ($coupon) {
        $_SESSION['coupon'] = $coupon;
    } else {
        unset($_SESSION['coupon']);
        $msg = "<div class='alert alert-danger mt-3'>❌ Mã giảm giá không hợp lệ hoặc đã hết hạn.</div>";
    }
}

// Nếu đã có coupon thì tính giảm
if (isset($_SESSION['coupon'])) {
    $coupon = $_SESSION['coupon'];
    if ($coupon['type'] === 'percent') {
        $discount = ($total * $coupon['discount']) / 100;
    } else {
        $discount = $coupon['discount'];
    }
    $coupon_code = $coupon['code'];
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

  <?php if (empty($items)): ?>
    <div class="alert alert-info">Giỏ hàng trống. <a href="../pages/products.php">Mua sắm ngay</a></div>
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
        <?php foreach ($items as $p): ?>
          <tr>
            <td><img src="../<?= $p['image'] ?>" width="80"></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= number_format($p['price'],0,',','.') ?> VND</td>
            <td><input type="number" name="qty[<?= $p['id'] ?>]" value="<?= $p['qty'] ?>" min="1" class="form-control w-50 mx-auto"></td>
            <td><?= number_format($p['subtotal'],0,',','.') ?> VND</td>
            <td><a href="cart.php?remove=<?= $p['id'] ?>" class="btn btn-sm btn-danger">Xóa</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Coupon -->
      <div class="row mb-3">
        <div class="col-md-6">
          <input type="text" name="coupon" class="form-control" placeholder="Nhập mã giảm giá" value="<?= $coupon_code ?>">
        </div>
        <div class="col-md-2">
          <button type="submit" name="apply_coupon" class="btn btn-warning w-100">Áp dụng</button>
        </div>
      </div>
      <?= $msg ?? "" ?>

      <!-- Tổng tiền -->
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h5>Tạm tính: <?= number_format($total,0,',','.') ?> VND</h5>
          <?php if ($discount > 0): ?>
            <h5>Giảm giá: -<?= number_format($discount,0,',','.') ?> VND (<?= $coupon_code ?>)</h5>
          <?php endif; ?>
          <h4>Tổng thanh toán: <span class="text-danger"><?= number_format($total - $discount,0,',','.') ?> VND</span></h4>
        </div>
        <div>
          <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
          <a href="../pages/checkout.php" class="btn btn-success">Thanh toán</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
