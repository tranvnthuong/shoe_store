<?php
// product_detail.php
session_start();
include("../configs/db.php");

// Lấy id sản phẩm từ URL
$id = (int)($_GET['id'] ?? 0);

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

// Lấy danh sách biến thể
$variants = $conn->query("SELECT * FROM product_variants WHERE product_id = $id");

// Lấy danh sách ảnh từ bảng product_images
$images = $conn->query("SELECT * FROM product_images WHERE product_id = $id");

$images_arr = [];
while ($img = $images->fetch_assoc()) {
  $images_arr[] = $img['url'];
}
// fallback nếu chưa có ảnh trong product_images
if (empty($images_arr)) {
  $images_arr[] = $product['image'] ?? "../uploads/default-shoe.jpg";
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
        <div id="carouselProduct" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php foreach ($images_arr as $k => $img): ?>
              <div class="carousel-item <?= $k === 0 ? 'active' : '' ?>">
                <img src="<?= htmlspecialchars($img) ?>" class="d-block w-100" alt="Ảnh sản phẩm"
                  style="max-height:50vh; object-fit:cover;"
                  onerror="this.src='../uploads/default-shoe.jpg';">
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($images_arr) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselProduct"
              data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselProduct"
              data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          <?php endif; ?>
        </div>
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
        <form method="get" action="cart.php">
          <input type="hidden" name="add" value="<?= $product['id'] ?>">
          <?php if ($variants->num_rows > 0): ?>
            <div class="mb-3">
              <label class="fw-bold">Phân loại:</label>
              <select name="variant" class="form-select w-auto d-inline-block">
                <?php while ($v = $variants->fetch_assoc()): ?>
                  <option value="<?= $v['id'] ?>">
                    <?= htmlspecialchars($v['name']) ?> -
                    <?= number_format($v['price'], 0, ',', '.') ?> VND
                    (<?= $v['stock'] ?> sp)
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
          <?php endif; ?>

          <div class="mt-auto d-flex justify-content-start gap-2">
            <a href="./buy_now.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary">
              <i class="fa-solid fa-bag-shopping"></i> Mua ngay
            </a>
            <button type="submit" name="add_to_cart" value="1" class="btn btn-outline-success">
              <i class="fas fa-cart-plus"></i> Thêm giỏ
            </button>
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