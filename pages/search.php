<?php
session_start();
include("../configs/db.php");

// Lấy tham số tìm kiếm
$q      = isset($_GET['q']) ? trim($_GET['q']) : "";
$cat    = isset($_GET['category']) ? intval($_GET['category']) : 0;
$brand  = isset($_GET['brand']) ? intval($_GET['brand']) : 0;
$min    = isset($_GET['min']) ? floatval($_GET['min']) : 0;
$max    = isset($_GET['max']) ? floatval($_GET['max']) : 0;

// Câu SQL cơ bản
$sql = "SELECT p.*, c.name AS category, b.name AS brand
        FROM products p
        LEFT JOIN categories c ON p.category_id=c.id
        LEFT JOIN brands b ON p.brand_id=b.id
        WHERE 1=1";

// Điều kiện tìm kiếm
$params = [];
$types  = "";

if ($q != "") {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%$q%";
    $types .= "s";
}
if ($cat > 0) {
    $sql .= " AND p.category_id=?";
    $params[] = $cat;
    $types .= "i";
}
if ($brand > 0) {
    $sql .= " AND p.brand_id=?";
    $params[] = $brand;
    $types .= "i";
}
if ($min > 0) {
    $sql .= " AND p.price >= ?";
    $params[] = $min;
    $types .= "d";
}
if ($max > 0) {
    $sql .= " AND p.price <= ?";
    $params[] = $max;
    $types .= "d";
}

$sql .= " ORDER BY p.id DESC";
$stmt = $conn->prepare($sql);

// Bind params nếu có
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Lấy danh mục và brand cho filter
$cats = $conn->query("SELECT * FROM categories");
$brands = $conn->query("SELECT * FROM brands");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Tìm kiếm sản phẩm</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
    <?php include("../layout/header.php") ?>
    <div class="container" style="padding-top: 80px;">
        <h2 class="mb-4">🔎 Kết quả tìm kiếm</h2>

        <div class="row g-4">
            <?php if ($result->num_rows == 0): ?>
                <div class="alert alert-warning">Không tìm thấy sản phẩm phù hợp!</div>
            <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="card product-card h-100">
                            <img src="<?= $row['image'] ?>" class="card-img-top product-img">
                            <div class="card-body text-center">
                                <h6 class="card-title"><?= htmlspecialchars($row['name']) ?></h6>
                                <p class="text-muted"><?= $row['brand'] ?> - <?= $row['category'] ?></p>
                                <p class="fw-bold text-danger"><?= number_format($row['price'], 0, ',', '.') ?> VND</p>
                                <a href="product_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Chi
                                    tiết</a>
                                <a href="cart.php?add=<?= $row['id'] ?>" class="btn btn-sm btn-success">🛒 Mua ngay</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include("../layout/footer.php") ?>
    <script src="https://unpkg.com/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>