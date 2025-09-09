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

// Truy vấn danh mục cho dropdown
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
$sql = "SELECT p.*, c.name as cat_name, 
               (SELECT COUNT(*) FROM product_variants v WHERE v.product_id = p.id) as variant_count
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Sản phẩm</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <?php include("../includes/product_item.php"); ?>

        <!-- Phân trang -->
        <?php include("../includes/pagination.php"); ?>

    </div>

    <?php include("../layout/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>