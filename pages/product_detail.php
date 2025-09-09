<?php
// product_detail.php
session_start();
include("../configs/db.php"); // file kết nối DB

// Lấy id sản phẩm từ URL
$id = $_GET['id'] ?? 0;
$id = (int)$id;

// Truy vấn sản phẩm
$sql = "SELECT p.*, c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = $id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) {
  die("Sản phẩm không tồn tại!");
}

// Truy vấn sản phẩm liên quan
$related_sql = "SELECT * FROM products 
                WHERE category_id = " . (int)$product['category_id'] . " 
                  AND id <> $id 
                ORDER BY created_at DESC 
                LIMIT 4";
$related_result = $conn->query($related_sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title><?= htmlspecialchars($product['name']); ?></title>
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
    <div class="row">
      <!-- Ảnh sản phẩm -->
      <div class="col-md-5">
        <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
          alt="<?= htmlspecialchars($row['name']) ?>" style="max-height:50vh; object-fit:cover;"
          onerror="this.src='../uploads/default-shoe.jpg';">
      </div>

      <!-- Thông tin sản phẩm -->
      <div class="col-md-7">
        <h2><?= htmlspecialchars($product['name']); ?></h2>
        <a href="products.php?category_id=<?= $product['category_id']; ?>" class="text-link">
          <p class="text-muted">
            Danh mục: <?= htmlspecialchars($product['category_name'] ?? "Chưa phân loại"); ?>
          </p>
        </a>
        <h4 class="text-danger mb-3">
          <?= number_format($product['price'], 0, ',', '.'); ?> VND
        </h4>
        <p>
          <?= nl2br(htmlspecialchars($product['description'])); ?>
        </p>
        <p>
          <strong>Tồn kho:</strong> <?= $product['stock']; ?>
        </p>
        <form method="post" action="cart_add.php">
          <input type="hidden" name="id" value="<?= $product['id']; ?>">
          <div class="input-group mb-3" style="max-width:200px;">
            <input type="number" name="qty" value="1" min="1" max="<?= $product['stock']; ?>"
              class="form-control">
            <button type="submit" class="btn btn-primary">Thêm vào giỏ</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <div class="mt-5">
      <h4>Sản phẩm liên quan</h4>
      <div class="row">
        <?php while ($row = $related_result->fetch_assoc()): ?>
          <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
              <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
                alt="<?= htmlspecialchars($row['name']) ?>" style="max-height:200px; object-fit:cover;"
                onerror="this.src='../uploads/default-shoe.jpg';">
              <div class="card-body">
                <h6 class="card-title">
                  <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                    <?php echo htmlspecialchars($row['name']); ?>
                  </a>
                </h6>
                <p class="text-danger mb-0">
                  <?php echo number_format($row['price'], 0, ',', '.'); ?> VND
                </p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
  <?php include("../layout/footer.php") ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>