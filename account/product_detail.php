<?php
session_start();
include("includes/db.php");

// Lấy ID sản phẩm
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}
$id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT p.*, c.name AS category, b.name AS brand
                        FROM products p
                        LEFT JOIN categories c ON p.category_id=c.id
                        LEFT JOIN brands b ON p.brand_id=b.id
                        WHERE p.id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "Sản phẩm không tồn tại!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .product-img { width:100%; max-height:450px; object-fit:cover; border-radius:8px; }
  </style>
</head>
<body>
<div class="container my-5">
  <div class="row">
    <!-- Ảnh -->
    <div class="col-md-6">
      <img src="<?= $product['image'] ?>" class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <!-- Thông tin -->
    <div class="col-md-6">
      <h2><?= htmlspecialchars($product['name']) ?></h2>
      <p class="text-muted"><?= $product['brand'] ?> | <?= $product['category'] ?></p>
      <h4 class="text-danger"><?= number_format($product['price'],0,',','.') ?> VND</h4>
      <p><strong>Còn lại:</strong> <?= $product['stock'] ?> sản phẩm</p>
      <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Chưa có mô tả')) ?></p>

      <!-- Form thêm vào giỏ -->
      <form action="cart.php" method="GET" class="d-flex">
        <input type="hidden" name="add" value="<?= $product['id'] ?>">
        <input type="number" name="qty" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control w-25 me-2">
        <button type="submit" class="btn btn-success">🛒 Thêm vào giỏ</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
