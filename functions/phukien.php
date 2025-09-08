<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include("../configs/db.php");

// Lấy type từ URL
$type = $_GET['type'] ?? '';

$categoryMap = [
    'balo'      => ['id'=>5, 'name'=>'Balo'],
    'charm'     => ['id'=>6, 'name'=>'Charm'],
    'daygiay'   => ['id'=>7, 'name'=>'Dây giày'],
    'lotde'     => ['id'=>8, 'name'=>'Lót đế'],
    'gaubong'   => ['id'=>9, 'name'=>'Gấu bông'],
    'xitkhumui' => ['id'=>10, 'name'=>'Xịt khử mùi']
];

if (!array_key_exists($type, $categoryMap)) {
    echo "❌ Danh mục không hợp lệ!";
    exit;
}

$category_id = $categoryMap[$type];
$category_id = $categoryMap[$type]['id'];

// Lấy tên danh mục
$catName = ucfirst($type);
$catName     = $categoryMap[$type]['name'];

// Query sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Phụ kiện - <?= htmlspecialchars($catName) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include("../layout/header.php"); ?>

<div class="container my-5">
  <h2 class="mb-4 text-center">
    🛍️ Phụ kiện: <span class="text-primary"><?= htmlspecialchars($catName) ?></span>
  </h2>

  <div class="row g-4">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($p = $result->fetch_assoc()): ?>
        <div class="col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm border-0">
            <img src="<?= $p['image'] ?>" 
                 class="card-img-top" 
                 style="height:200px;object-fit:cover;" 
                 alt="<?= htmlspecialchars($p['name']) ?>">
            <div class="card-body d-flex flex-column">
              <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
              <p class="fw-bold text-danger mb-2">
                <?= number_format($p['price'], 0, ',', '.') ?> VND
              </p>
              <p class="text-muted small mb-3"><?= htmlspecialchars($p['description']) ?></p>
              <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm mb-2">
                <i class="bi bi-eye"></i> Xem chi tiết
              </a>
              <a href="cart_add.php?id=<?= $p['id'] ?>" class="btn btn-success btn-sm mt-auto">
                <i class="bi bi-cart-plus"></i> Thêm giỏ hàng
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center">Không có sản phẩm nào trong danh mục này.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include("../layout/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
