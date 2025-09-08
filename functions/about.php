<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("../layout/header.php"); ?>

<div class="about-page">

  <!-- Hero Section -->
  <section class="hero text-white text-center d-flex align-items-center justify-content-center" style="background: url('../images/banner.jpg') center/cover; height: 60vh;">
    <div data-aos="fade-up">
      <h1 class="fw-bold">Chào mừng đến với <span class="text-warning">ShoeStore</span></h1>
      <p class="lead">Thời trang - Phong cách - Đẳng cấp</p>
    </div>
  </section>

  <div class="container my-5">

    <!-- About -->
    <section class="mb-5 text-center" data-aos="fade-up">
      <h2 class="mb-3">👟 Giới thiệu về chúng tôi</h2>
      <p class="text-muted">ShoeStore ra đời với sứ mệnh mang đến những đôi giày chất lượng, giúp khách hàng tự tin thể hiện phong cách cá nhân. Chúng tôi luôn đặt <b>chất lượng</b> và <b>khách hàng</b> làm trung tâm.</p>
    </section>

    <!-- Vì sao chọn -->
    <section class="mb-5">
      <h2 class="text-center mb-4" data-aos="zoom-in">🌟 Vì sao chọn ShoeStore?</h2>
      <div class="row g-4">
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
          <div class="card shadow h-100 text-center p-3">
            <i class="bi bi-bag-check-fill fs-1 text-primary"></i>
            <h5 class="mt-2">Chính hãng 100%</h5>
          </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
          <div class="card shadow h-100 text-center p-3">
            <i class="bi bi-truck fs-1 text-success"></i>
            <h5 class="mt-2">Ship nhanh toàn quốc</h5>
          </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
          <div class="card shadow h-100 text-center p-3">
            <i class="bi bi-cash-coin fs-1 text-warning"></i>
            <h5 class="mt-2">Giá hợp lý</h5>
          </div>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
          <div class="card shadow h-100 text-center p-3">
            <i class="bi bi-headset fs-1 text-danger"></i>
            <h5 class="mt-2">Hỗ trợ 24/7</h5>
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
            <img src="../images/long.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Triệu Tài Long</h6>
            <small>CEO & Founder</small>
            <div>
              <a href="#" class="text-dark me-2"><i class="bi bi-facebook"></i></a>
              <a href="#" class="text-dark me-2"><i class="bi bi-instagram"></i></a>
              <a href="#" class="text-dark"><i class="bi bi-telegram"></i></a>
            </div>
          </div>
        </div>
        <div class="col-md-3 text-center" data-aos="flip-left" data-aos-delay="100">
          <div class="card border-0">
            <img src="../images/hoai.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Nguyễn Thị Thu Hoài</h6>
            <small>Quản lý</small>
          </div>
          <a href="#" class="text-dark"><i class="bi bi-facebook"></i></a>
        </div>
        <div class="col-md-3 text-center" data-aos="flip-left" data-aos-delay="200">
          <div class="card border-0">
            <img src="../images/thuong.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Trần Văn Thượng</h6>
            <small> PM & Developer </small>
          </div>
           <a href="#" class="text-dark"><i class="bi bi-telegram"></i></a>
        </div>
        <div class="col-md-3 text-center" data-aos="flip-left" data-aos-delay="300">
          <div class="card border-0">
            <img src="../images/manh.jpg" class="rounded-circle mx-auto" width="140" height="140">
            <h6 class="mt-2">Vi Văn Mạnh</h6>
            <small>CSKH</small>
          </div>
          <a href="#" class="text-dark"><i class="bi bi-telegram"></i></a>
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
      <h2 class="text-center mb-4" data-aos="fade-up">💬 Khách hàng nói gì?</h2>
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
      <a href="contact.php" class="btn btn-lg btn-primary">📩 Liên hệ ngay</a>
    </div>

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
