<?php
session_start();
include("../configs/db.php");

// Lấy danh mục
$categories = $conn->query("SELECT * FROM categories");

// Lấy filter & sort từ URL
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;

// ORDER BY
$orderBy = "ORDER BY created_at DESC";
switch ($sort) {
    case 'price_asc': $orderBy = "ORDER BY price ASC"; break;
    case 'price_desc': $orderBy = "ORDER BY price DESC"; break;
    case 'newest': $orderBy = "ORDER BY created_at DESC"; break;
    case 'oldest': $orderBy = "ORDER BY created_at ASC"; break;
}

// WHERE điều kiện
$where = "1";
if ($category > 0) $where .= " AND category_id = $category";
switch ($price_range) {
    case '0-1000': $where .= " AND price BETWEEN 0 AND 1000000"; break;
    case '1000-2000': $where .= " AND price BETWEEN 1000000 AND 2000000"; break;
    case '2000+': $where .= " AND price >= 2000000"; break;
}

// Query sản phẩm
$sql = "SELECT * FROM products WHERE $where $orderBy";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Sản phẩm - Shoe Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include("../layout/header.php") ?>

<div class="container my-5">
  <h2 class="text-center mb-4">Danh sách sản phẩm</h2>

  <!-- Bộ lọc -->
  <form method="GET" class="row mb-4 g-3 align-items-end">
    <!-- Danh mục -->
    <div class="col-md-3">
      <label class="form-label">Danh mục</label>
      <select name="category" class="form-select">
        <option value="0">-- Tất cả danh mục --</option>
        <?php while($cat = $categories->fetch_assoc()): ?>
          <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Khoảng giá -->
    <div class="col-md-3">
      <label class="form-label">Khoảng giá</label>
      <select name="price_range" class="form-select">
        <option value="">-- Tất cả --</option>
        <option value="0-1000" <?= ($price_range == '0-1000') ? 'selected' : '' ?>>Dưới 1,000,000</option>
        <option value="1000-2000" <?= ($price_range == '1000-2000') ? 'selected' : '' ?>>1,000,000 - 2,000,000</option>
        <option value="2000+" <?= ($price_range == '2000+') ? 'selected' : '' ?>>Trên 2,000,000</option>
      </select>
    </div>

    <!-- Sắp xếp -->
    <div class="col-md-3">
      <label class="form-label">Sắp xếp</label>
      <select name="sort" class="form-select">
        <option value="">-- Mặc định --</option>
        <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
        <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
        <option value="newest" <?= ($sort == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
        <option value="oldest" <?= ($sort == 'oldest') ? 'selected' : '' ?>>Cũ nhất</option>
      </select>
    </div>

    <!-- Nút lọc -->
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-funnel"></i> Lọc
      </button>
    </div>
  </form>

  <!-- Danh sách sản phẩm -->
  <div class="row g-4">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-3">
          <div class="card h-100 shadow-sm">
            <?php 
              $imgPath = $row['image'];
              if (strpos($imgPath, 'http') !== 0) {
                  $imgPath = "../uploads/" . $imgPath;
              }
            ?>
            <img src="<?= htmlspecialchars($imgPath) ?>" 
                 class="card-img-top" 
                 alt="<?= htmlspecialchars($row['name']) ?>" 
                 style="height:200px; object-fit:cover;">

            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
              <p class="card-text text-danger fw-bold">
                <?= number_format($row['price'], 0, ',', '.') ?> VND
              </p>
              <p class="card-text text-muted small">
                <?= htmlspecialchars(substr($row['description'],0,60)) ?>...
              </p>
              <div class="mt-auto d-flex justify-content-between">
                <a href="product_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-eye"></i> Xem
                </a>
                <a href="cart.php?add=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                  <i class="bi bi-cart-plus"></i> Thêm giỏ
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info">Không tìm thấy sản phẩm phù hợp.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
