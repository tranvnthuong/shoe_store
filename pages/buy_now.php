<?php
session_start();
include("../configs/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit;
}

$user_id   = $_SESSION['user_id'];
$email     = $_SESSION['email'] ?? '';
$balance   = floatval($_SESSION['balance'] ?? 0);
$full_name = $_SESSION['full_name'] ?? '';
$phone     = $_SESSION['phone'] ?? '';
$address   = $_SESSION['address'] ?? '';

$msg = $error = null;

// Lấy product_id, variant_id, qty từ URL
$product_id = intval($_GET['id'] ?? 0);
$variant_id = !empty($_GET['variant']) ? intval($_GET['variant']) : null;
$qty        = max(1, intval($_GET['qty'] ?? 1));

if ($product_id <= 0) {
    die("❌ Thiếu ID sản phẩm.");
}

// Lấy thông tin sản phẩm
if ($variant_id) {
    $stmt = $conn->prepare("SELECT p.id as product_id, p.name, p.image,
                                 v.id as variant_id, v.name as variant_name, v.price, v.stock
                          FROM products p
                          JOIN product_variants v ON v.product_id = p.id
                          WHERE p.id=? AND v.id=? LIMIT 1");
    $stmt->bind_param("ii", $product_id, $variant_id);
} else {
    $stmt = $conn->prepare("SELECT p.id as product_id, p.name, p.image,
                                 NULL as variant_id, NULL as variant_name, p.price, p.stock
                          FROM products p
                          WHERE p.id=? LIMIT 1");
    $stmt->bind_param("i", $product_id);
}
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    die("❌ Sản phẩm không tồn tại hoặc biến thể không hợp lệ.");
}

// Xử lý áp dụng coupon
$discount = 0;
if (isset($_POST['apply_coupon'])) {
    $code = trim($_POST['coupon']);
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code=? LIMIT 1");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $coupon = $stmt->get_result()->fetch_assoc();
    if ($coupon) {
        $_SESSION['coupon'] = $coupon;
    } else {
        $_SESSION['coupon'] = null;
    }
}

// Xử lý đặt hàng
if (isset($_POST['checkout'])) {
    $qty      = max(1, intval($_POST['quantity']));
    $variant_id = !empty($_POST['variant_id']) ? intval($_POST['variant_id']) : $variant_id;
    $address  = trim($_POST['address']);
    $phone    = trim($_POST['phone']);
    $payment  = $_POST['payment'];

    // Kiểm tra tồn kho
    if ($qty > intval($product['stock'])) {
        $error = "❌ Sản phẩm không đủ hàng.";
    } else {
        $total = floatval($product['price']) * $qty;

        // Áp dụng coupon nếu có
        $discount = 0;
        if (isset($_SESSION['coupon'])) {
            $coupon = $_SESSION['coupon'];
            if (!empty($coupon['expiry']) && strtotime($coupon['expiry']) <= time()) {
                unset($_SESSION['coupon']);
            } else {
                if ($coupon['type'] === 'percent') {
                    $discount = ($total * floatval($coupon['discount'])) / 100.0;
                } else {
                    $discount = floatval($coupon['discount']);
                }
            }
        }
        $final_total = max(0.0, $total - $discount);

        $conn->begin_transaction();
        try {
            // Nếu thanh toán bằng balance: kiểm tra đủ tiền
            if ($payment === "BALANCE" && $final_total > $balance) {
                throw new Exception("Số dư không đủ.");
            }

            // Tạo order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, payment_method, total, total_price, status, created_at)
                              VALUES (?,?,?,?, 'pending', NOW())");
            $stmt->bind_param("isdd", $user_id, $payment, $total, $final_total);
            $stmt->execute();
            $order_id = $conn->insert_id;

            // Thêm order_item
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, variant_id, quantity, price)
                               VALUES (?,?,?,?,?)");
            $stmt2->bind_param("iiiid", $order_id, $product['product_id'], $variant_id, $qty, $product['price']);
            $stmt2->execute();

            // Cập nhật stock
            if ($variant_id) {
                $stmt3 = $conn->prepare("UPDATE product_variants SET stock = stock - ? WHERE id=?");
                $stmt3->bind_param("ii", $qty, $variant_id);
            } else {
                $stmt3 = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id=?");
                $stmt3->bind_param("ii", $qty, $product['product_id']);
            }
            $stmt3->execute();

            // Trừ balance nếu cần
            if ($payment === "BALANCE") {
                $stmtBal = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id=?");
                $stmtBal->bind_param("di", $final_total, $user_id);
                $stmtBal->execute();
            }

            $conn->commit();
            unset($_SESSION['coupon']);
            $msg = "<div class='alert alert-success text-center'>
                ✅ Đặt hàng thành công! Mã đơn hàng: #$order_id
              </div>";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "<div class='alert alert-danger text-center'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Mua hàng</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include("../layout/header.php"); ?>

    <div class="container" style="padding-top: 80px;">
        <h2>🛒 Xác nhận thanh toán</h2>
        <?php if (!empty($msg)) {
            echo $msg;
        } ?>
        <?php if (!empty($error)) {
            echo $error;
        } ?>
        <form method="post">
            <table class="table table-bordered text-center">
                <tr>
                    <td><img src="<?= htmlspecialchars($product['image']) ?>" width="80"></td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>
                        <?php
                        $vs = $conn->query("SELECT id,name FROM product_variants WHERE product_id=" . (int)$product['product_id']);
                        if ($vs->num_rows > 0): ?>
                            <select name="variant_id" class="form-select">
                                <?php while ($v = $vs->fetch_assoc()): ?>
                                    <option value="<?= $v['id'] ?>" <?= ($variant_id == $v['id'] ? 'selected' : '') ?>>
                                        <?= htmlspecialchars($v['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        <?php else: ?>
                            <input type="hidden" name="variant_id" value="">
                            Không có
                        <?php endif; ?>
                    </td>
                    <td><input type="number" name="quantity" value="<?= $qty ?>" min="1"
                            class="form-control w-50 mx-auto">
                    </td>
                    <td><?= number_format($product['price'], 0, ',', '.') ?> VND</td>
                </tr>
            </table>
            <div class="mb-3">
                <input type="text" name="coupon" placeholder="Mã giảm giá" class="form-control d-inline w-auto">
                <button name="apply_coupon" class="btn btn-outline-primary">Áp dụng</button>
            </div>
            <div class="mb-3">
                <label>Địa chỉ giao hàng</label>
                <input type="text" name="address" value="<?= htmlspecialchars($address) ?>" class="form-control"
                    required>
            </div>
            <div class="mb-3">
                <label>SĐT</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Thanh toán</label>
                <select name="payment" class="form-select">
                    <option value="COD">Thanh toán khi nhận hàng</option>
                    <option value="BALANCE">Số dư tài khoản</option>
                </select>
            </div>
            <button type="submit" name="checkout" class="btn btn-success w-100">Xác nhận mua hàng</button>
        </form>
    </div>
    <?php include("../layout/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>