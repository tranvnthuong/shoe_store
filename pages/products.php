<?php
session_start();
include("../configs/db.php");

// Nhận tham số lọc
$keyword     = $_GET['q'] ?? '';
$category_id = $_GET['category_id'] ?? 0;

// Nhận tham số trang
$limit = 8; // số sản phẩm / trang
$page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Truy vấn danh mục
$cats = $conn->query("SELECT * FROM categories");

// Điều kiện WHERE
$where = "WHERE 1";
if ($category_id) {
    $where .= " AND p.category_id = " . intval($category_id);
}
if ($keyword) {
    $where .= " AND p.name LIKE '%" . $conn->real_escape_string($keyword) . "%'";
}

// Đếm tổng số sản phẩm
$count_sql = "SELECT COUNT(*) as total 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              $where";
$count_result = $conn->query($count_sql);
$total = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// Truy vấn sản phẩm có phân trang
$sql = "SELECT p.*, c.name as cat_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        $where
        ORDER BY p.created_at DESC 
        LIMIT $limit OFFSET $offset";

$products = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sản phẩm</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include("../layout/header.php"); ?>

    <div class="container" style="padding-top: 80px;">
        <h2 class="mb-4">Danh sách sản phẩm</h2>

        <!-- Form tìm kiếm -->
        <form class="row mb-4" method="GET">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Tên sản phẩm..."
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
                <button class="btn btn-primary w-100">
                    <i class="fas fa-search"></i>
                    Tìm kiếm
                </button>
            </div>
        </form>

        <!-- Danh sách sản phẩm -->
        <div class="row">
            <?php if ($products && $products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <a href="./product_detail.php?id=<?= $row['id'] ?>">
                                <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($row['name']) ?>" style="height:200px; object-fit:cover;"
                                    onerror="this.src='../uploads/default-shoe.jpg';">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <a href="./product_detail.php?id=<?= $row['id'] ?>"
                                    class="card-title product-title"><?= htmlspecialchars($row['name']) ?></a>
                                <p class="text-muted small"><?= htmlspecialchars($row['cat_name']) ?></p>
                                <?php if (isset($row['description'])): ?>
                                    <p class="card-text product-description">
                                        <?= htmlspecialchars($row['description']) ?>
                                    </p>
                                <?php endif; ?>
                                <p class="card-text text-danger fw-bold">
                                    <?= number_format($row['price'], 0, ',', '.') ?> VND
                                </p>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="./product_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary">
                                        <i class="fa-solid fa-bag-shopping"></i> Mua ngay
                                    </a>
                                    <a href="./cart.php?add=<?= $row['id'] ?>" class="btn btn-outline-success">
                                        <i class="fas fa-cart-plus"></i> Thêm giỏ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info">Không tìm thấy sản phẩm nào.</div>
            <?php endif; ?>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <nav style="overflow: auto;">
                <ul class="pagination justify-content-center">
                    <!-- Nút First page -->
                    <?php if (isset($_GET['page']) && $_GET['page'] != 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?q=<?= urlencode($keyword) ?>&category_id=<?= $category_id ?>&page=1">
                                <i class="fa-solid fa-angles-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                    <!-- Nút Previous -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?q=<?= urlencode($keyword) ?>&category_id=<?= $category_id ?>&page=<?= max(1, $page - 1) ?>">
                            <i class="fa-solid fa-angle-left"></i>
                        </a>
                    </li>
                    <!-- Các số trang -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?q=<?= urlencode($keyword) ?>&category_id=<?= $category_id ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Nút Next -->
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?q=<?= urlencode($keyword) ?>&category_id=<?= $category_id ?>&page=<?= min($total_pages, $page + 1) ?>">
                            <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </li>
                    <!-- Nút Last page -->
                    <?php if (isset($_GET['page']) && $_GET['page'] != $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?q=<?= urlencode($keyword) ?>&category_id=<?= $category_id ?>&page=<?= $total_pages ?>">
                                <i class="fa-solid fa-angles-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>

    <?php include("../layout/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>