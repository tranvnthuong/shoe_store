<?php
session_start();
include("./configs/db.php");

// Số sản phẩm mỗi trang
$limit = 8;

// Trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Vị trí bắt đầu
$offset = ($page - 1) * $limit;

// Lấy sản phẩm theo trang, KHÔNG lấy phụ kiện (id từ 5 đến 10)
$sql = "SELECT * FROM products 
        WHERE category_id
        ORDER BY created_at DESC
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Đếm tổng số sản phẩm (chỉ giày, không phụ kiện)
$countSql = "SELECT COUNT(*) AS total FROM products WHERE category_id NOT BETWEEN 5 AND 10";
$countResult = $conn->query($countSql);
$totalProducts = $countResult->fetch_assoc()['total'];

// Tính tổng số trang
$totalPages = ceil($totalProducts / $limit);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Shoe Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

    <?php include("layout/header.php") ?><?php include("./layout/header.php") ?>

    <!-- Banner Carousel -->
    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="4000">
                <img src="https://i.pinimg.com/736x/d1/2c/e1/d12ce1614109d74c5d5c9aed04f5630e.jpg" class="d-block w-100"
                    style="height:650px; object-fit:cover;">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h1>Chào mừng đến với Shop Giày</h1>

                    <img src="https://i.pinimg.com/736x/d1/2c/e1/d12ce1614109d74c5d5c9aed04f5630e.jpg"
                        class="d-block w-100" style="height:850px; object-fit:cover;">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-5">
                        <h1>Chào mừng đến với Shop Snaker</h1>

                        <p>Thời trang - Phong cách - Đẳng cấp</p>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="https://i.pinimg.com/736x/99/49/6a/99496a166061ccf01d701a63d5b25c8c.jpg"
                        class="d-block w-100" style="height:650px; object-fit:cover;">

                    <img src="https://i.pinimg.com/736x/99/49/6a/99496a166061ccf01d701a63d5b25c8c.jpg"
                        class="d-block w-100" style="height:850px; object-fit:cover;">

                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h1>Bộ sưu tập mới</h1>
                        <p>Giảm giá 30% hôm nay</p>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="https://i.pinimg.com/736x/9d/70/29/9d70296bd760bd879235eea7d12dcc90.jpg"
                        class="d-block w-100" style="height:650px; object-fit:cover;">

                    <img src="https://i.pinimg.com/736x/9d/70/29/9d70296bd760bd879235eea7d12dcc90.jpg"
                        class="d-block w-100" style="height:850px; object-fit:cover;">

                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h1>Phong cách trẻ trung</h1>
                        <p>Mua ngay để nhận ưu đãi</p>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <!-- Sản phẩm nổi bật -->
        <div class="container my-5">
            <h2 class="text-center mb-4">🔥 SẢN PHẨM HOT NHẤT </h2>
            <div class="row g-4">

                <div class="col-md-3 col-sm-6">
                    <div class="card product-card">
                        <img src="https://i.pinimg.com/1200x/a8/a6/22/a8a622c06e8f762050a4fe285aa985ca.jpg"
                            class="card-img-top product-img">
                        <div class="card-body text-center">
                            <h5 class="card-title">Sneaker Trắng</h5>
                            <p class="text-danger fw-bold">1,200,000 VND</p>
                            <a href="cart.php?add=1" class="btn btn-primary btn-sm w-100">🛒 Mua ngay</a>
                        </div>
                    </div>
                </div>

                <!-- Thêm sp khác tương tự -->
            </div>
        </div>


        <?php
    // Mảng các tiêu đề muốn hiển thị động
    $titles = ["DEAL NGON", "BÁN CHẠY", "MỚI VỀ", "GIÁ SỐC"];
    ?>
        <!DOCTYPE html>
        <html lang="vi">

        <head>
            <meta charset="UTF-8">
            <title>Sản phẩm nổi bật</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
            h2 {
                font-size: 32px;
                font-weight: bold;
                text-transform: uppercase;
            }

            .highlight {
                color: red;
            }

            .typing {
                display: inline-block;
                border-right: 2px solid black;
                white-space: nowrap;
                overflow: hidden;
            }
            </style>
        </head>

        <body>
            <div class="container my-5 text-center">
                <h2> SẢN PHẨM <span id="dynamic" class="highlight typing"></span></h2>
            </div>

            <script>
            // Lấy danh sách từ PHP ra JS
            let titles = <?php echo json_encode($titles); ?>;
            let index = 0;
            let element = document.getElementById("dynamic");
            let speed = 100; // tốc độ gõ
            let eraseSpeed = 50; // tốc độ xóa
            let delay = 1000; // thời gian dừng giữa các từ

            function typeText(text, i) {
                if (i < text.length) {
                    element.innerHTML = text.substring(0, i + 1);
                    setTimeout(() => typeText(text, i + 1), speed);
                } else {
                    setTimeout(() => eraseText(text, text.length - 1), delay);
                }
            }

            function eraseText(text, i) {
                if (i >= 0) {
                    element.innerHTML = text.substring(0, i);
                    setTimeout(() => eraseText(text, i - 1), eraseSpeed);
                } else {
                    index = (index + 1) % titles.length;
                    typeText(titles[index], 0);
                }
            }

            // Bắt đầu hiệu ứng
            typeText(titles[index], 0);
            </script>
        </body>

        </html>


        <link rel="stylesheet" href="../assets/css/style.css">
        <?php include("layout/footer.php") ?>


        <!-- Sản phẩm HOT -->
        <div class="container my-5">
            <div class="row g-4">
                <div class="hot-title text-center my-5">
                    <h2 class="fw-bold"> SẢN PHẨM HOT </h2>
                    <div class="decor-line">
                        <span></span>
                        <i class="bi bi-diamond-fill mx-1"></i>
                        <i class="bi bi-diamond-fill mx-0"></i>
                        <i class="bi bi-diamond-fill mx-1"></i>
                        <span></span>
                    </div>
                </div>

                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm">
                        <?php
              $imgPath = $row['image'];
              if (strpos($imgPath, 'http') !== 0) {
                $imgPath = "./uploads/" . $imgPath;
              }
              ?>
                        <img src="<?= htmlspecialchars($imgPath) ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($row['name']) ?>" style="height:200px; object-fit:cover;">

                        <div class="card-body d-flex flex-column">
                            <a href="./pages/product_detail.php?id=<?= $row['id'] ?>"
                                class="card-title"><?= htmlspecialchars($row['name']) ?></a>
                            <p class="card-text text-danger fw-bold">
                                <?= number_format($row['price'], 0, ',', '.') ?> VND
                            </p>
                            <p class="card-text text-muted small">
                                <?= htmlspecialchars(substr($row['description'], 0, 60)) ?>...
                            </p>
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="./pages/product_detail.php?id=<?= $row['id'] ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Mua ngay
                                </a>
                                <a href="./pages/cart.php?add=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-cart-plus"></i> Thêm giỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Thanh phân trang -->
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <!-- Nút Previous -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Trước</a>
                    </li>

                    <!-- Các số trang -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <!-- Nút Next -->
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Sau</a>
                    </li>
                </ul>
            </nav>
        </div>

        <?php include("./layout/footer.php") ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>