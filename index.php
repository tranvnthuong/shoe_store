<?php
session_start();
include("./configs/db.php");

$sql = "SELECT * FROM products 
        WHERE category_id
        ORDER BY created_at DESC
        LIMIT 12";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Shoe Store</title>
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .typing {
            border-right: 2px solid black;
            white-space: nowrap;
            overflow: hidden;
            vertical-align: bottom;
        }

        .carousel-item img {
            display: block;
            width: 100%;
            max-height: 450px;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <?php include("layout/header.php") ?>
    <div class="container my-5">
        <!-- Banner Carousel -->
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
            </div>

            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="4000">
                    <img src="https://i.pinimg.com/736x/d1/2c/e1/d12ce1614109d74c5d5c9aed04f5630e.jpg">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h1>Chào mừng đến với Shop Giày</h1>
                        <p>Thời trang - Phong cách - Đẳng cấp</p>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="https://i.pinimg.com/736x/99/49/6a/99496a166061ccf01d701a63d5b25c8c.jpg">
                    <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                        <h1>Bộ sưu tập mới</h1>
                        <p>Giảm giá 30% hôm nay</p>
                    </div>
                </div>
                <div class="carousel-item" data-bs-interval="4000">
                    <img src="https://i.pinimg.com/736x/9d/70/29/9d70296bd760bd879235eea7d12dcc90.jpg">
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

        <!-- Sản phẩm HOT -->
        <div class="row g-4">
            <div class="text-center mt-5 mb-2">
                <h2>
                    <span> SẢN PHẨM </span><span id="dynamic" class="text-danger typing"></span>
                </h2>
                <div class="decor-line">
                    <i class="fas fa-diamond mx-1"></i>
                    <i class="fas fa-diamond mx-0"></i>
                    <i class="fas fa-diamond mx-1"></i>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="./pages/products.php" class="btn btn-outline-primary">
                    Xem tất cả
                </a>
            </div>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm">
                        <a href="./product_detail.php?id=<?= $row['id'] ?>">
                            <img src="<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($row['name']) ?>" style="height:200px; object-fit:cover;"
                                onerror="this.src='./uploads/default-shoe.jpg';">
                        </a>

                        <div class=" card-body d-flex flex-column">
                            <a href="./pages/product_detail.php?id=<?= $row['id'] ?>"
                                class="card-title product-title"><?= htmlspecialchars($row['name']) ?></a>
                            <?php if (isset($row['description'])): ?>
                                <p class="card-text product-description">
                                    <?= htmlspecialchars($row['description']) ?>
                                </p>
                            <?php endif; ?>

                            <p class="card-text text-danger fw-bold">
                                <?= number_format($row['price'], 0, ',', '.') ?> VND
                            </p>
                            <div class="mt-auto d-flex justify-content-between">
                                <a href="./pages/product_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-bag-shopping"></i> Mua ngay
                                </a>
                                <a href="./pages/cart.php?add=<?= $row['id'] ?>" class="btn btn-outline-success">
                                    <i class="fas fa-cart-plus"></i> Thêm giỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include("./layout/footer.php") ?>

    <script>
        // Lấy danh sách từ PHP ra JS
        let titles = ["DEAL NGON", "BÁN CHẠY", "MỚI VỀ", "GIÁ SỐC"];
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>