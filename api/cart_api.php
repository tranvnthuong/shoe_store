<?php
include("../configs/db.php");
session_start();

header('Content-Type: application/json');

// CSRF token check
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    echo json_encode([
        "status" => "error",
        "msg" => "CSRF token không hợp lệ!",
        "isToast" => true
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'];

if (!$action) {
    echo json_encode([
        "status" => "error",
        "msg" => "Hành động không hợp lệ",
    ]);
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

// Thêm sản phẩm vào giỏ hàng
if ($action == "add") {
    $product_id = intval($_POST['id']);
    $variant_id = !empty($_POST['variant']) ? intval($_POST['variant']) : null;
    $qty = !empty($_POST['qty']) ? intval($_POST['qty']) : 1;

    if (addToCart($product_id, $variant_id, $qty)) {
        $stmt = $conn->prepare("SELECT sum(quantity) as cart_count FROM cart WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "msg" => "Đã thêm vào rỏ hàng",
            "cartCount" => $row['cart_count'],
            "isToast" => true
        ]);
    } else {
        echo json_encode([
            "status" => "warning",
            "msg" => "Sản phẩm này đã hết hàng",
            "isToast" => true
        ]);
    }
    exit;
}

$total = 0;
$items = [];
$discount = 0;
$coupon_code = "";

function updateCart()
{
    global $conn, $user_id, $total, $items, $discount, $coupon_code;
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
    while ($row = $result->fetch_assoc()) {
        $price = $row['variant_price'] ?? $row['base_price'];
        $row['price'] = $price;
        $row['subtotal'] = $price * $row['quantity'];
        $total += $row['subtotal'];
        $items[] = $row;
    }
    // Nếu đã có coupon thì tính giảm
    if (isset($_SESSION['coupon'])) {
        $coupon = $_SESSION['coupon'];

        if (!empty($coupon['expiry'])) {
            $expiry = strtotime($coupon['expiry']);
            if ($expiry < time()) {
                unset($_SESSION['coupon']);
                echo json_encode([
                    "status" => "error",
                    "msg" => "Mã giảm giá này đã hết hạn",
                    "isToast" => true
                ]);
                exit;
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
}

updateCart();

// Áp dụng coupon
if ($action == 'apply_coupon') {
    $coupon_code = trim($_POST['coupon_code']);
    if (empty($coupon_code)) {
        echo json_encode([
            "status" => "error",
            "msg" => "Vui lòng nhập mã giảm giá",
            "isToast" => true
        ]);
        exit;
    }
    $coupon_code = strtoupper($coupon_code);
    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code=? LIMIT 1");
    $stmt->bind_param("s", $coupon_code);
    $stmt->execute();
    $coupon = $stmt->get_result()->fetch_assoc();
    if ($coupon) {
        $_SESSION['coupon'] = $coupon;
        if (!empty($coupon['expiry'])) {
            $expiry = strtotime($coupon['expiry']);
            if ($expiry < time()) {
                unset($_SESSION['coupon']);
                echo json_encode([
                    "status" => "error",
                    "msg" => "Mã giảm giá này đã hết hạn",
                    "isToast" => true
                ]);
                exit;
            }
        }

        if (isset($_SESSION['coupon'])) { // nếu vẫn còn hợp lệ
            if ($coupon['type'] === 'percent') {
                $discount = ($total * $coupon['discount']) / 100;
            } else {
                $discount = $coupon['discount'];
            }
            $coupon_code = $coupon['code'];
            echo json_encode([
                "status" => "success",
                "msg" => "Nhập thành công mã giảm giá $coupon_code",
                "total" => $total,
                "discount" => $discount,
                "coupon_code" => $coupon_code
            ]);
            exit;
        }
    } else {
        unset($_SESSION['coupon']);
        echo json_encode([
            "status" => "error",
            "msg" => "Mã giảm giá không hợp lệ",
            "isToast" => true
        ]);
        exit;
    }
}

// Xóa sản phẩm khỏi giỏ
if ($action == 'remove') {
    $id = intval($_POST['cart_id']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=? AND id=?");
    $stmt->bind_param("ii", $user_id, $id);
    $stmt->execute();
    $items = [];
    $total = 0;
    $discount = 0;
    updateCart();
    echo json_encode([
        "status" => "success",
        "msg" => "Cập nhật thành công",
        "items" => $items,
        "total" => $total,
        "discount" => $discount,
        "coupon_code" => $coupon_code
    ]);
    exit;
}

// Cập nhật số lượng
if ($action == "update") {
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
    $items = [];
    $total = 0;
    $discount = 0;
    updateCart();
    echo json_encode([
        "status" => "success",
        "msg" => "Cập nhật thành công",
        "items" => $items,      // danh sách sản phẩm
        "total" => $total,
        "discount" => $discount,
        "coupon_code" => $coupon_code
    ]);
    exit;
}
