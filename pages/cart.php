<?php
session_start();
include("../configs/db.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: ../account/login.php");
  exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$user_id = $_SESSION['user_id'];
// Xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
  $id = intval($_GET['remove']);
  $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND id=?");
  $stmt->bind_param("ii", $user_id, $id);
  $stmt->execute();
  header("Location: cart.php");
  exit;
}

function addToCart($product_id, $variant_id = null, $qty = 1)
{
  global $conn, $user_id;

  // Nếu có variant → kiểm tra tồn kho theo variant
  if ($variant_id) {
    $stmt = $conn->prepare("SELECT stock FROM product_variants WHERE id=? AND product_id=?");
    $stmt->bind_param("ii", $variant_id, $product_id);
  } else {
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
  }
  $stmt->execute();
  $stockRow = $stmt->get_result()->fetch_assoc();
  $stock = $stockRow['stock'] ?? 0;

  if ($stock <= 0) {
    return false; // hết hàng
  }

  // Kiểm tra cart
  $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=? AND (variant_id <=> ?)");
  $stmt->bind_param("iii", $user_id, $product_id, $variant_id);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();

  if ($row) {
    $new_qty = min($row['quantity'] + $qty, $stock);
    $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
    $stmt->bind_param("ii", $new_qty, $row['id']);
    $stmt->execute();
  } else {
    $qty = min($qty, $stock);
    $stmt = $conn->prepare("INSERT INTO cart(user_id, product_id, variant_id, quantity) VALUES(?,?,?,?)");
    $stmt->bind_param("iiii", $user_id, $product_id, $variant_id, $qty);
    $stmt->execute();
  }
  return true;
}


// Thêm sản phẩm vào giỏ
if (!empty($_GET['add_to_cart']) || isset($_GET['add'])) {
  $product_id = intval($_GET['add']);
  $variant_id = !empty($_GET['variant']) ? intval($_GET['variant']) : null;
  $qty = !empty($_GET['qty']) ? intval($_GET['qty']) : 1;

  if (addToCart($product_id, $variant_id, $qty)) {
    header("Location: cart.php");
  } else {
    $_SESSION['error'] = "❌ Hết hàng!";
    header("Location: products.php");
  }
  exit;
}


// Cập nhật số lượng
if (isset($_POST['update'])) {
  foreach ($_POST['qty'] as $cart_id => $qty) {
    $qty = intval($qty);
    if ($qty <= 0) {
      $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND id=?");
      $stmt->bind_param("ii", $user_id, $cart_id);
      $stmt->execute();
    } else {
      $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND id=?");
      $stmt->bind_param("iii", $qty, $user_id, $cart_id);
      $stmt->execute();
    }
  }
  header("Location: cart.php");
  exit;
}


// Lấy danh sách giỏ hàng
$stmt = $conn->prepare("
  SELECT c.id as cart_id, c.quantity, 
         p.id as product_id, p.name, p.image, p.price as base_price, p.stock as product_stock,
         v.id as variant_id, v.name as variant_name, v.price as variant_price, v.stock as variant_stock
  FROM cart c
  JOIN products p ON p.id = c.product_id
  LEFT JOIN product_variants v ON v.id = c.variant_id
  WHERE c.user_id=?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];
while ($row = $result->fetch_assoc()) {
  $price = $row['variant_price'] ?? $row['base_price'];
  $row['price'] = $price;
  $row['subtotal'] = $price * $row['quantity'];
  $total += $row['subtotal'];
  $items[] = $row;
}


// Giảm giá
$discount = 0;
$coupon_code = "";

// Áp dụng coupon
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
    $msg = "<div class='alert alert-danger mt-3'>❌ Mã giảm giá không hợp lệ</div>";
  }
}

// Nếu đã có coupon thì tính giảm
if (isset($_SESSION['coupon'])) {
  $coupon = $_SESSION['coupon'];

  if (!empty($coupon['expiry'])) {
    $expiry = strtotime($coupon['expiry']); // convert string "2025-09-30" → timestamp
    if ($expiry < time()) {
      unset($_SESSION['coupon']);
      $msg = "<div class='alert alert-danger mt-3'>❌ Mã giảm giá đã hết hạn</div>";
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

// CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Giỏ hàng - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <?php include("../layout/header.php") ?>
  <div class="container" style="padding-top: 80px;">
    <h2 id="cartTitle">
      <span><i class="fa-solid fa-cart-shopping"></i></span>
      Giỏ hàng của tôi
    </h2>
    <?php if (empty($items)): ?>
      <div class="alert alert-info">Giỏ hàng trống. <a href="products.php">Mua sắm ngay</a></div>
    <?php else: ?>
      <form id="cartForm" method="POST">
        <inpit type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
          <table class="table table-bordered text-center">
            <thead class="table-dark">
              <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $p): ?>
                <tr>
                  <td><img src="<?= $p['image'] ?>" width="80"></td>
                  <td><?= htmlspecialchars($p['name']) ?>
                    <?php if ($p['variant_name']) echo " [" . htmlspecialchars($p['variant_name']) . "]"; ?>
                  </td>
                  <td><?= number_format($p['price'], 0, ',', '.') ?> VND</td>
                  <td>
                    <input type="number" name="qty[<?= $p['cart_id'] ?>]" value="<?= $p['quantity'] ?>"
                      min="1" max="<?= $p['product_stock'] ?>"
                      class="form-control w-50 mx-auto input-qty">
                  </td>
                  <td><?= number_format($p['subtotal'], 0, ',', '.') ?> VND</td>
                  <td><a href="cart.php?remove=<?= $p['cart_id'] ?>" class="btn btn-sm btn-danger">Xóa</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <!-- Coupon -->
          <div class="row mb-3">
            <div class="col-md-6">
              <input type="text" name="coupon" class="form-control" placeholder="Nhập mã giảm giá"
                value="<?= $coupon_code ?>">
            </div>
            <div class="col-md-2">
              <button type="submit" name="apply_coupon" class="btn btn-warning w-100">Áp dụng</button>
            </div>
          </div>
          <?= $msg ?? "" ?>

          <!-- Tổng tiền -->
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5>Tạm tính: <span id="total"><?= number_format($total, 0, ',', '.') ?> VND</span></h5>
              <?php if ($discount > 0): ?>
                <h5>
                  Giảm giá: -<span id="discount"><?= number_format($discount, 0, ',', '.') ?> VND</span>
                  (<?= $coupon_code ?>)
                </h5>
              <?php endif; ?>
              <h4>
                Tổng thanh toán: <span id="final_total"
                  class="text-danger"><?= number_format($total - $discount, 0, ',', '.') ?>
                  VND</span>
              </h4>
            </div>

            <div class="text-end">
              <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
              <a href="checkout.php" class="btn btn-success">Thanh toán</a>
            </div>
          </div>
      </form>
    <?php endif; ?>
  </div>
  <?php include("../layout/footer.php"); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/script.js"></script>
  <script>
    $(document).ready(function() {
      const btnLoader = makeButtonLoader($("#cartTitle"));

      $("#cartForm").on("submit", function(e) {
        e.preventDefault();

        $.ajax({
          url: "../api/cart_api.php",
          type: "POST",
          data: $(this).serialize(),
          dataType: "json",
          beforeSend: () => {
            btnLoader.showLoading();
          },
          complete: () => {
            btnLoader.showDefault();
          },
          success: (data) => {
            showMessage(data);
          },
          error: (xhr, status, error) => {
            Swal.fire({
              icon: "error",
              title: "Lỗi server",
              text: "Không thể gửi yêu cầu. Vui lòng thử lại!",
            });
            console.error(error);
          }
        });
      });
    });
  </script>
</body>

</html>