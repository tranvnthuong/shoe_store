<?php
session_start();
include("../configs/db.php");

// Nhận tham số lọc
$keyword     = $_GET['q'] ?? '';
$category_id = $_GET['category_id'] ?? 0;

// Truy vấn danh mục
$cats = $conn->query("SELECT * FROM categories");

// Truy vấn sản phẩm
$sql = "SELECT p.*, c.name as cat_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE 1";

if ($category_id) {
  $sql .= " AND p.category_id = " . intval($category_id);
}

if ($keyword) {
  $sql .= " AND p.name LIKE '%" . $conn->real_escape_string($keyword) . "%'";
}

$sql .= " ORDER BY p.created_at DESC"; // mới nhất trước

$products = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include("../layout/header.php"); ?>

    <div class="container my-4">
        <h2 class="mb-4">🛍️ Danh sách sản phẩm</h2>

        <!-- Form tìm kiếm -->
        <form class="row mb-4" method="GET">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Nhập tên sản phẩm..."
                    value="<?= htmlspecialchars($keyword) ?>">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php while ($cat = $cats->fetch_assoc()): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Lọc</button>
            </div>
        </form>

        <!-- Danh sách sản phẩm -->
        <div class="row">
            <?php if ($products && $products->num_rows > 0): ?>
            <?php while ($p = $products->fetch_assoc()): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <!-- FIX: Đường dẫn ảnh -->
                    <img src="/shoe_store/uploads/<?= htmlspecialchars($p['image']) ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($p['name']) ?>" style="height:200px;object-fit:cover;"
                        onerror="this.src='/shoe_store/assets/no-image.png';">

                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
                        <p class="text-muted small"><?= htmlspecialchars($p['cat_name']) ?></p>
                        <p class="fw-bold text-danger mb-3">
                            <?= number_format($p['price'], 0, ',', '.') ?> VND
                        </p>
                        <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary mt-auto">Xem chi
                            tiết</a>
                        <a href="cart_add.php?id=<?= $p['id'] ?>" class="btn btn-success mt-2">+ Thêm giỏ hàng</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="alert alert-info">Không tìm thấy sản phẩm nào.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("../layout/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>