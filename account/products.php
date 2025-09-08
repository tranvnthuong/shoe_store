<?php
session_start();
include("includes/db.php");

// Lấy sản phẩm (có join danh mục và thương hiệu)
$limit = 8; // số sản phẩm mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$sql = "SELECT p.*, c.name AS category, b.name AS brand
        FROM products p
        LEFT JOIN categories c ON p.category_id=c.id
        LEFT JOIN brands b ON p.brand_id=b.id
        ORDER BY p.id DESC
        LIMIT $start, $limit";
$result = $conn->query($sql);

// Đếm tổng sản phẩm để phân trang
$total_res = $conn->query("SELECT COUNT(*) AS total FROM products");
$total = $total_res->fetch_assoc()['total'];
$pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sản phẩm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .product-card { transition: all 0.3s; }
    .product-card:hover { transform: translateY(-6px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .product-img { height: 220px; object-fit: cover; }
  </style>
</head>
<body>
<div class="container my-5">
  <h2 class="mb-4">🛍️ Danh sách sản phẩm</h2>
  <div class="row g-4">

    <?php while($row = $result->fetch_assoc()): ?>
    <div class="col-md-3 col-sm-6">
      <div class="card product-card h-100">
        <img src="<?= $row['image'] ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($row['name']) ?>">
        <div class="card-body text-center">
          <h6 class="card-title"><?= htmlspecialchars($row['name']) ?></h6>
          <p class="text-muted"><?= $row['brand'] ?> - <?= $row['category'] ?></p>
          <p class="fw-bold text-danger"><?= number_format($row['price'],0,',','.') ?> VND</p>
          <a href="product_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
          <a href="cart.php?add=<?= $row['id'] ?>" class="btn btn-sm btn-success">🛒 Mua ngay</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>

  </div>

  <!-- Phân trang -->
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for($i=1;$i<=$pages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'' ?>">
          <a class="page-link" href="products.php?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>
</body>
</html>
