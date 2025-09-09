<?php
session_start();
include("../configs/db.php");

// Bắt buộc đăng nhập
if (!isset($_SESSION['user_id'])) {
  header("Location: ../account/login.php");
  exit;
}

$user_id    = $_SESSION['user_id'];
$email      = $_SESSION['email'] ?? '';
$balance    = floatval($_SESSION['balance'] ?? 0);
$full_name  = $_SESSION['full_name'] ?? '';
$phone      = $_SESSION['phone'] ?? '';
$address    = $_SESSION['address'] ?? '';

$msg = NULL;
$error = NULL;

// Kiểm tra giỏ hàng trong DB
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM cart WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
if ($res['total'] == 0) {
  header("Location: ../pages/cart.php");
  exit;
}

// cập nhật số lượng/variant từ giỏ
if (isset($_POST['update_cart'])) {
  foreach ($_POST['items'] as $cart_id => $row) {
    $qty = max(1, intval($row['quantity']));
    $variant_id = !empty($row['variant_id']) ? intval($row['variant_id']) : null;
    $stmt = $conn->prepare("UPDATE cart SET quantity=?, variant_id=? WHERE id=? AND user_id=?");
    $stmt->bind_param("iiii", $qty, $variant_id, $cart_id, $user_id);
    $stmt->execute();
  }
}


$stmt = $conn->prepare("
  SELECT c.id AS cart_id, c.product_id, c.variant_id, c.quantity,
         p.name, p.image, p.price as base_price,
         v.name as variant_name, v.price as variant_price
  FROM cart c
  JOIN products p ON c.product_id=p.id
  LEFT JOIN product_variants v ON c.variant_id=v.id
  WHERE c.user_id=?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// XỬ LÝ ĐẶT HÀNG
if (isset($_POST['checkout'])) {
  $address = trim($_POST['address']);
  $phone   = trim($_POST['phone']);
  $payment = $_POST['payment'];

  // Lấy giỏ hàng chi tiết (1 lần)
  $cart_items = [];
  $total = 0.0;

  $stmt = $conn->prepare("
  SELECT c.id AS cart_id, c.product_id, c.variant_id, c.quantity,
         p.name, p.image, p.price as base_price,
         v.name as variant_name, v.price as variant_price
  FROM cart c
  JOIN products p ON c.product_id=p.id
  LEFT JOIN product_variants v ON c.variant_id=v.id
  WHERE c.user_id=?
");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $res = $stmt->get_result();

  while ($row = $res->fetch_assoc()) {
    // Giá và tồn kho áp theo variant nếu có
    $hasVariant = !is_null($row['variant_id']) && $row['variant_id'] !== '';
    $stock = $hasVariant ? intval($row['variant_stock']) : intval($row['product_stock']);
    $price = $hasVariant ? floatval($row['variant_price']) : floatval($row['base_price']);

    // Kiểm tra tồn kho
    if (intval($row['quantity']) > $stock) {
      $error = "<div class='alert alert-danger text-center'>❌ Sản phẩm <b>" . htmlspecialchars($row['name']) . "</b> không đủ hàng!</div>";
      break;
    }

    $subtotal = $price * intval($row['quantity']);
    $total += $subtotal;

    // lưu item tĩnh (giá cụ thể tại thời điểm mua)
    $cart_items[] = [
      'cart_id' => intval($row['cart_id']),
      'product_id' => intval($row['product_id']),
      'variant_id' => $hasVariant ? intval($row['variant_id']) : null,
      'quantity' => intval($row['quantity']),
      'price' => $price,
      'name' => $row['name'],
      'image' => $row['image'],
    ];
  }

  // Nếu đã có coupon thì tính giảm
  if (isset($_SESSION['coupon'])) {
    $coupon = $_SESSION['coupon'];

    if (!empty($coupon['expiry'])) {
      $expiry = strtotime($coupon['expiry']); // convert string "2025-09-30" → timestamp
      if ($expiry < time()) {
        unset($_SESSION['coupon']);
      }
    }

    if (isset($_SESSION['coupon'])) { // nếu vẫn còn hợp lệ
      if ($coupon['type'] === 'percent') {
        $discount = ($total * $coupon['discount']) / 100;
      } else {
        $discount = $coupon['discount'];
      }
      $coupon_code = $coupon['code'];
    }
  }

  $final_total = max(0.0, $total - $discount);

  // Bắt đầu transaction để đảm bảo atomic
  $conn->begin_transaction();

  try {
    // Nếu thanh toán bằng balance: kiểm tra đủ tiền
    if ($payment === "BALANCE") {
      if ($final_total > $balance) {
        throw new Exception("Số dư trong tài khoản không đủ để thanh toán.");
      }
      // trừ tiền ở cuối cùng cùng transaction (sau khi insert order)
    }

    // Tạo order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, payment_method, total, total_price, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    if ($stmt === false) throw new Exception("Prepare failed (insert order): " . $conn->error);
    $stmt->bind_param("isdd", $user_id, $payment, $total, $final_total);
    if (!$stmt->execute()) throw new Exception("Execute failed (insert order): " . $stmt->error);
    $order_id = $conn->insert_id;
    $stmt->close();

    // Thêm order_items & trừ stock tương ứng
    $stmtInsertItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES (?,?,?,?,?)");
    if ($stmtInsertItem === false) throw new Exception("Prepare failed (insert order_items): " . $conn->error);

    $stmtUpdateVariant = $conn->prepare("UPDATE product_variants SET stock = stock - ? WHERE id = ?");
    $stmtUpdateProduct = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    if ($stmtUpdateVariant === false || $stmtUpdateProduct === false) {
      throw new Exception("Prepare failed (update stock): " . $conn->error);
    }

    foreach ($cart_items as $item) {
      // insert order_item
      // variant_id có thể null -> bind kiểu 'i' vẫn chấp nhận null (MySQL sẽ gán NULL)
      $v = $item['variant_id'];
      $stmtInsertItem->bind_param("iiiid", $order_id, $item['product_id'], $v, $item['quantity'], $item['price']);
      if (!$stmtInsertItem->execute()) throw new Exception("Execute failed (insert order_item): " . $stmtInsertItem->error);

      // update stock
      if (!is_null($v)) {
        $stmtUpdateVariant->bind_param("ii", $item['quantity'], $v);
        if (!$stmtUpdateVariant->execute()) throw new Exception("Execute failed (update variant stock): " . $stmtUpdateVariant->error);
      } else {
        $stmtUpdateProduct->bind_param("ii", $item['quantity'], $item['product_id']);
        if (!$stmtUpdateProduct->execute()) throw new Exception("Execute failed (update product stock): " . $stmtUpdateProduct->error);
      }
    }

    // Xóa giỏ hàng của user
    $stmtDel = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    if ($stmtDel === false) throw new Exception("Prepare failed (delete cart): " . $conn->error);
    $stmtDel->bind_param("i", $user_id);
    if (!$stmtDel->execute()) throw new Exception("Execute failed (delete cart): " . $stmtDel->error);
    $stmtDel->close();

    // Nếu thanh toán bằng BALANCE thì trừ tiền tài khoản user
    if ($payment === "BALANCE") {
      $stmtBal = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
      if ($stmtBal === false) throw new Exception("Prepare failed (update balance): " . $conn->error);
      $stmtBal->bind_param("di", $final_total, $user_id);
      if (!$stmtBal->execute()) throw new Exception("Execute failed (update balance): " . $stmtBal->error);
      $stmtBal->close();
    }

    // commit transaction
    $conn->commit();

    // Xóa coupon đã dùng
    unset($_SESSION['coupon']);

    // Giao diện thông báo
    $msg = "
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
  } catch (Exception $e) {
    // rollback & hiển thị lỗi
    $conn->rollback();
    $error = "<div class='alert alert-danger text-center'>❌ Lỗi đặt hàng: " . htmlspecialchars($e->getMessage()) . "</div>";
  }
}

// Sau khi load giỏ hàng và tính $total
$discount = 0;
$coupon_code = "";

if (isset($_SESSION['coupon'])) {
  $coupon = $_SESSION['coupon'];

  // check expiry
  if (!empty($coupon['expiry']) && strtotime($coupon['expiry']) < time()) {
    unset($_SESSION['coupon']);
  } else {
    if ($coupon['type'] === 'percent') {
      $discount = ($total * $coupon['discount']) / 100;
    } else {
      $discount = $coupon['discount'];
    }
    $coupon_code = $coupon['code'];
  }
}

// khi nhấn "Áp dụng coupon"
if (isset($_POST['apply_coupon'])) {
  $coupon_code = strtoupper(trim($_POST['coupon']));
  $stmt = $conn->prepare("SELECT * FROM coupons WHERE code=? LIMIT 1");
  $stmt->bind_param("s", $coupon_code);
  $stmt->execute();
  $coupon = $stmt->get_result()->fetch_assoc();

  if ($coupon) {
    $_SESSION['coupon'] = $coupon;
  } else {
    unset($_SESSION['coupon']);
  }
}


?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh toán - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <?php include("../layout/header.php"); ?>

  <div class="container" style="padding-top: 80px;">
    <?php if (!empty($msg)): ?>
      <?= $msg ?>
    <?php else: ?>
      <?php if (!empty($error)) echo $error; ?>
      <h2>🛒 Xác nhận thanh toán</h2>
      <form method="post">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Ảnh</th>
              <th>Tên</th>
              <th>Phân loại</th>
              <th>Số lượng</th>
              <th>Giá</th>
              <th>Thành tiền</th>
            </tr>
          </thead>
          <tbody>
            <?php $total = 0;
            foreach ($cart_items as $item):
              $price = $item['variant_id'] ? $item['variant_price'] : $item['base_price'];
              $subtotal = $price * $item['quantity'];
              $total += $subtotal;
            ?>
              <tr>
                <td><img src="<?= htmlspecialchars($item['image']) ?>" width="70"></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>
                  <?php
                  // select variant nếu có
                  $vs = $conn->query("SELECT id,name FROM product_variants WHERE product_id=" . (int)$item['product_id']);
                  if ($vs->num_rows > 0): ?>
                    <select name="items[<?= $item['cart_id'] ?>][variant_id]" class="form-select">
                      <option value="">--Chọn--</option>
                      <?php while ($v = $vs->fetch_assoc()): ?>
                        <option value="<?= $v['id'] ?>"
                          <?= ($item['variant_id'] == $v['id'] ? 'selected' : '') ?>>
                          <?= htmlspecialchars($v['name']) ?>
                        </option>
                      <?php endwhile; ?>
                    </select>
                  <?php else: ?>
                    <input type="hidden" name="items[<?= $item['cart_id'] ?>][variant_id]" value="">
                    Không có
                  <?php endif; ?>
                </td>
                <td><input type="number" name="items[<?= $item['cart_id'] ?>][quantity]"
                    value="<?= $item['quantity'] ?>" min="1" class="form-control w-50 mx-auto"></td>
                <td><?= number_format($price, 0, ',', '.') ?> VND</td>
                <td><?= number_format($subtotal, 0, ',', '.') ?> VND</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <!-- Tổng tiền -->
        <div class="d-flex justify-content-between">
          <div>
            <h5>Tạm tính: <?= number_format($total, 0, ',', '.') ?> VND</h5>
            <?php if ($discount > 0): ?>
              <h5>Giảm giá: -<?= number_format($discount, 0, ',', '.') ?> VND (<?= $coupon_code ?>)</h5>
            <?php endif; ?>
            <h4>Tổng thanh toán: <span class="text-danger"><?= number_format($total - $discount, 0, ',', '.') ?>
                VND</span></h4>
          </div>
          <div>
            <input type="text" name="coupon" placeholder="Mã giảm giá" class="form-control d-inline w-auto">
            <button name="apply_coupon" class="btn btn-outline-primary">Áp dụng</button>
          </div>
          <button type="submit" name="update_cart" class="btn btn-warning">Cập nhật giỏ</button>
        </div>
      </form>
      <form method="POST" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Họ tên</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($full_name) ?>" readonly>
        </div>
        <div class="col-md-6">
          <label class="form-label">Số điện thoại</label>
          <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Địa chỉ giao hàng</label>
          <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address) ?>"
            required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phương thức thanh toán</label>
          <select name="payment" class="form-select" required>
            <option value="COD">💵 Thanh toán khi nhận hàng</option>
            <option value="BALANCE">💰 Số dư tài khoản</option>
          </select>
        </div>
        <div class="col-md-12">
          <button type="submit" name="checkout" class="btn btn-success w-100">Xác nhận đặt hàng</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
  <?php include("../layout/footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>