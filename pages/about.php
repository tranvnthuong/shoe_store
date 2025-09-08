<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Giới thiệu - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <?php include("../layout/header.php"); ?>

  <div class="container" style="padding-top: 80px;">
    <!-- Hero Section -->
    <section class="hero text-white text-center d-flex align-items-center justify-content-center"
      style="background: url('../assets/images/banner.jpg') center/cover; height: 60vh; margin-top: 60px;">
      <div data-aos="fade-up">
        <h1 class="text-warning">Chào mừng đến với Shoe Store</h1>
        <p class="lead">Thời trang - Phong cách - Đẳng cấp</p>
      </div>
    </section>



    <!-- About -->
    <section class="my-5 text-center" data-aos="fade-up">
      <h2 class="mb-3"> <i class="fas fa-shoe-prints"></i>Giới thiệu về chúng tôi</h2>
      <p class="text-muted">Shoe Store ra đời với sứ mệnh mang đến những đôi giày chất lượng, giúp khách hàng
        tự tin thể hiện phong cách cá nhân. Chúng tôi luôn đặt <b>chất lượng</b> và <b>khách hàng</b> làm
        trung tâm.</p>
    </section>

    <!-- Vì sao chọn -->
    <section class="mb-5">
      <h2 class="text-center mb-4" data-aos="zoom-in"><i class="fa-solid fa-star text-warning"></i>Vì sao chọn
        Shoe Store?
      </h2>
      <div class="row g-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
          <div class="card shadow d-flex align-items-center justify-content-center gap-2 p-3">
            <i class="fas fa-circle-check fs-1 text-primary"></i>
            <h5>Chính hãng 100%</h5>
          </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
          <div class="card shadow d-flex align-items-center justify-content-center gap-2 p-3">
            <i class="fas fa-truck-fast fs-1 text-success"></i>
            <h5>Ship nhanh toàn quốc</h5>
          </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
          <div class="card shadow d-flex align-items-center justify-content-center gap-2 p-3">
            <i class="fas fa-coins fs-1 text-warning"></i>
            <h5>Giá hợp lý</h5>
          </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
          <div class="card shadow d-flex align-items-center justify-content-center gap-2 p-3">
            <i class="fas fa-headset fs-1 text-danger"></i>
            <h5>Hỗ trợ 24/7</h5>
          </div>
        </div>
      </div>
    </section>

    <!-- Team -->
    <section class="mb-5">
      <h2 class="text-center mb-4" data-aos="zoom-in">👨‍👩‍👦 Đội ngũ của chúng tôi</h2>
      <div class="row g-4">
        <div class="col-md-3 text-center" data-aos="flip-left">
          <div class="card border-0">
            <img src="../assets/images/long.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Triệu Tài Long</h6>
            <small>CEO & Founder</small>
            <div class="d-flex justify-content-center mt-2">
              <a href="#" class="text-primary fs-4"><i class="fa-brands fa-facebook"></i></a>
              <a href="#" class="text-danger fs-4"><i class="fa-brands fa-square-instagram"></i></a>
              <a href="#" class="text-info fs-4"><i class="fa-brands fa-telegram"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-3 text-center" data-aos="flip-left" data-aos-delay="100">
          <div class="card border-0">
            <img src="../assets/images/hoai.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Nguyễn Thị Thu Hoài</h6>
            <small>Quản lý</small>
          </div>
          <div class="d-flex justify-content-center mt-2">
            <a href="#" class="text-primary fs-4"><i class="fa-brands fa-facebook"></i></a>
            <a href="#" class="text-danger fs-4"><i class="fa-brands fa-square-instagram"></i></a>
            <a href="#" class="text-info fs-4"><i class="fa-brands fa-telegram"></i></a>
          </div>
        </div>
        <div class="col-md-3 text-center" data-aos="flip-left" data-aos-delay="200">
          <div class="card border-0">
            <img src="../assets/images/thuong.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Trần Văn Thượng</h6>
            <small> PM & Developer </small>
          </div>
          <div class="d-flex justify-content-center mt-2">
            <a href="https://facebook.com/thuongwbw/" class="text-primary fs-4"><i
                class="fa-brands fa-facebook"></i></a>
            <a href="https://github.com/tranvnthuong/" class="text-dark fs-4"><i
                class="fa-brands fa-square-github"></i></a>
            <a href="https://www.linkedin.com/in/tran-vn-thuong/" class="text-primary fs-4"><i
                class="fa-brands fa-linkedin"></i></a>
          </div>
        </div>
        <div class="col-md-3 text-center" data-aos="flip-left" data-aos-delay="300">
          <div class="card border-0">
            <img src="../assets/images/manh.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Vi Văn Mạnh</h6>
            <small>CSKH</small>
          </div>
          <div class="d-flex justify-content-center mt-2">
            <a href="#" class="text-primary fs-4"><i class="fa-brands fa-facebook"></i></a>
            <a href="#" class="text-danger fs-4"><i class="fa-brands fa-square-instagram"></i></a>
            <a href="#" class="text-info fs-4"><i class="fa-brands fa-telegram"></i></a>
          </div>
        </div>
      </div>
    </section>

    <!-- Counter -->
    <section class="bg-light py-5 text-center rounded mb-5">
      <div class="row">
        <div class="col-md-3">
          <h2 class="counter text-primary" data-target="10000">0</h2>
          <p>Khách hàng</p>
        </div>
        <div class="col-md-3">
          <h2 class="counter text-success" data-target="5000">0</h2>
          <p>Đơn hàng</p>
        </div>
        <div class="col-md-3">
          <h2 class="counter text-warning" data-target="50">0</h2>
          <p>Đối tác</p>
        </div>
        <div class="col-md-3">
          <h2 class="counter text-danger" data-target="5">0</h2>
          <p>Năm kinh nghiệm</p>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="mb-5">
      <h2 class="text-center mb-4" data-aos="fade-up"><i class="fa-solid fa-comment"></i>Khách hàng nói gì?
      </h2>
      <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner text-center">
          <div class="carousel-item active">
            <blockquote class="blockquote">
              <p>"Giày đẹp, chất lượng và giao hàng nhanh!"</p>
              <footer class="blockquote-footer">Anh Hùng</footer>
            </blockquote>
          </div>
          <div class="carousel-item">
            <blockquote class="blockquote">
              <p>"Shop tư vấn nhiệt tình, sản phẩm y hình."</p>
              <footer class="blockquote-footer">Chị Lan</footer>
            </blockquote>
          </div>
          <div class="carousel-item">
            <blockquote class="blockquote">
              <p>"Giá cả hợp lý, sẽ ủng hộ dài dài."</p>
              <footer class="blockquote-footer">Bạn Minh</footer>
            </blockquote>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <div class="text-center" data-aos="zoom-in">
      <a href="contact.php" class="btn btn-lg btn-primary"><i class="fa-solid fa-envelope"></i>Liên hệ
        ngay</a>
    </div>
  </div>
  <?php include("../layout/footer.php"); ?>
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();

    // Counter animation
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
      counter.innerText = '0';
      const updateCounter = () => {
        const target = +counter.getAttribute('data-target');
        const c = +counter.innerText;
        const increment = target / 200;
        if (c < target) {
          counter.innerText = Math.ceil(c + increment);
          setTimeout(updateCounter, 10);
        } else {
          counter.innerText = target;
        }
      };
      updateCounter();
    });
  </script>
</body>

</html>