<?php
session_start();
include("../configs/db.php");

// Bắt buộc đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['id'] ?? 0);
$error = "";
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

    // Lấy chi tiết sản phẩm trong đơn
    $stmt2 = $conn->prepare("SELECT oi.*, p.name, p.image, v.name as variant_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_variants v ON oi.variant_id = v.id
        WHERE oi.order_id=?
    ");

    $stmt2->bind_param("i", $order_id);
    if (!$stmt2->execute()) {
        die("SQL error: " . $stmt2->error);
    }
    $res = $stmt2->get_result();
    $items = $res->fetch_all(MYSQLI_ASSOC);

    if (empty($items)) {
        $error = "Không có sản phẩm trong đơn hàng #{$order_id}";
    }
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
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include("../layout/header.php"); ?>

    <div class="container" style="padding-top: 80px;">
        <a href="orders.php" class="btn btn-outline-secondary my-3">⬅ Quay lại đơn hàng</a>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($order): ?>
        <h2>📦 Chi tiết đơn hàng #<?= $order['id'] ?></h2>
        <p><b>Ngày đặt:</b> <?= $order['created_at'] ?></p>
        <p><b>Người nhận:</b> <?= htmlspecialchars($order['full_name']) ?></p>
        <p><b>SĐT:</b> <?= htmlspecialchars($order['phone']) ?></p>
        <p><b>Địa chỉ:</b> <?= htmlspecialchars($order['address']) ?></p>
        <p><b>Thanh toán:</b> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><b>Trạng thái:</b> <span class="badge bg-info"><?= $order['status'] ?></span></p>

        <h4 class="mt-4">🛒 Sản phẩm</h4>
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><img src="<?= $item['image'] ?>" width="80"></td>
                    <td><?= htmlspecialchars($item['name']) ?>
                        <?php if ($item['variant_name']) echo "(" . htmlspecialchars($item['variant_name']) . ")"; ?>
                    </td>

                    <td><?= number_format($item['price'], 0, ',', '.') ?> VND</td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> VND</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end">
            <h5>Tổng tiền gốc: <?= number_format($order['total'], 0, ',', '.') ?> VND</h5>
            <h4>Thành tiền: <span class="text-danger"><?= number_format($order['total_price'], 0, ',', '.') ?>
                    VND</span></h4>
        </div>
        <?php endif; ?>
    </div>
    <?php include("../layout/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>