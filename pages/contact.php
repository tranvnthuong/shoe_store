<?php
include("../configs/db.php");
session_start();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Xử lý request AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['ajax'])) {
  header('Content-Type: application/json');

  if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    echo json_encode(["status" => "error", "msg" => "CSRF token không hợp lệ!"]);
    exit;
  }

  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $message = trim($_POST['message']);

  if (!$name || !$email || !$message) {
    echo json_encode(["status" => "error", "msg" => "Vui lòng điền đầy đủ thông tin bắt buộc."]);
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "msg" => "Email không hợp lệ."]);
    exit;
  }

  if ($phone && !preg_match("/^[0-9]{9,11}$/", $phone)) {
    echo json_encode(["status" => "error", "msg" => "Số điện thoại không hợp lệ (9-11 số)."]);
    exit;
  }

  $stmt = $conn->prepare("INSERT INTO contacts (name,email,phone,message,status,created_at) VALUES (?,?,?,?,?,NOW())");
  $status = "pending";
  $stmt->bind_param("sssss", $name, $email, $phone, $message, $status);

  if ($stmt->execute()) {
    echo json_encode(["status" => "success", "msg" => "✅ Cảm ơn bạn! Chúng tôi sẽ liên hệ sớm."]);
  } else {
    echo json_encode(["status" => "error", "msg" => "❌ Lỗi khi gửi liên hệ. Vui lòng thử lại!"]);
  }
  exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Liên hệ - Shoe Store</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
  <link href="../assets/css/style.css" rel="stylesheet">

  <style>
    .form-icon {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #888;
    }
    .form-control {
      padding-left: 2.2rem;
    }
    .map-responsive {
      overflow: hidden;
      padding-bottom: 56.25%;
      position: relative;
      height: 0;
    }
    .map-responsive iframe {
      left: 0;
      top: 0;
      height: 100%;
      width: 100%;
      position: absolute;
    }
  </style>
</head>

<body>
  <?php include("../layout/header.php"); ?>

  <div class="container" style="padding-top: 80px;">
    <h2 data-aos="fade-down">📞 Liên hệ với chúng tôi</h2>
    <p data-aos="fade-down" data-aos-delay="100">Nếu bạn có thắc mắc, hãy để lại thông tin hoặc ghé trực tiếp cửa hàng.</p>

    <div class="row mt-4">
      <!-- Form liên hệ -->
      <div class="col-md-6" data-aos="fade-right">
        <div class="card shadow-sm p-4">
          <form id="contactForm">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" name="ajax" value="1">

            <div class="mb-3 position-relative">
              <i class="fa fa-user form-icon"></i>
              <input type="text" class="form-control" name="name" placeholder="Họ và tên" required>
            </div>

            <div class="mb-3 position-relative">
              <i class="fa fa-envelope form-icon"></i>
              <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>

            <div class="mb-3 position-relative">
              <i class="fa fa-phone form-icon"></i>
              <input type="text" class="form-control" name="phone" placeholder="Số điện thoại (tuỳ chọn)">
            </div>

            <div class="mb-3 position-relative">
              <i class="fa fa-comment form-icon"></i>
              <textarea class="form-control" name="message" rows="4" placeholder="Nội dung liên hệ" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">
              <i class="fa fa-paper-plane"></i> Gửi liên hệ
            </button>
          </form>
        </div>
      </div>

      <!-- Google Map dạng Tab -->
      <div class="col-md-6" data-aos="fade-left">
        <ul class="nav nav-tabs" id="branchTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="hcm-tab" data-bs-toggle="tab" data-bs-target="#hcm" type="button" role="tab">
              📍 TP.HCM
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="bacninh-tab" data-bs-toggle="tab" data-bs-target="#bacninh" type="button" role="tab">
              📍 Bắc Ninh
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="danang-tab" data-bs-toggle="tab" data-bs-target="#danang" type="button" role="tab">
              📍 Đà Nẵng
            </button>
          </li>
        </ul>

        <div class="tab-content mt-3" id="branchTabsContent">
          <!-- HCM -->
          <div class="tab-pane fade show active" id="hcm" role="tabpanel" aria-labelledby="hcm-tab">
            <h5>📍 Chi nhánh TP.HCM</h5>
            <p>123 Đường ABC, Quận XYZ, TP.HCM</p>
            <div class="map-responsive">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d501714.3777334782!2d106.3555433547297!3d10.76192843195741!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752eefdb25d923%3A0x4bcf54ddca2b7214!2zSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1757660239633!5m2!1svi!2s"
                width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>

          <!-- Bắc Ninh -->
          <div class="tab-pane fade" id="bacninh" role="tabpanel" aria-labelledby="bacninh-tab">
            <h5>📍 Chi nhánh Bắc Ninh</h5>
            <p>456 Đường DEF, Quận 1, TP. Bắc Ninh</p>
            <div class="map-responsive">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59527.278591227216!2d105.99969598343276!3d21.174080186859044!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31350c5b3464ae51%3A0x1a3035b9749102f9!2zVHAuIELhuq9jIE5pbmgsIELhuq9jIE5pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1757659535214!5m2!1svi!2s"
                width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>

          <!-- Đà Nẵng -->
          <div class="tab-pane fade" id="danang" role="tabpanel" aria-labelledby="danang-tab">
            <h5>📍 Chi nhánh Đà Nẵng</h5>
            <p>789 Đường GHI, Quận Hải Châu, TP. Đà Nẵng</p>
            <div class="map-responsive">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d245374.1268620618!2d107.91381930847082!3d16.067008502048147!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314219c792252a13%3A0x1df0cb4b86727e06!2zxJDDoCBO4bq1bmcsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1757660188538!5m2!1svi!2s"
                width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="toast" class="toast text-bg-primary" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <i class="fa fa-bell me-2"></i>
        <strong class="me-auto">Thông báo</strong>
        <small>Just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body"></div>
    </div>
  </div>

  <?php include("../layout/footer.php"); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();

    const form = document.getElementById("contactForm");
    const toastEl = document.getElementById("toast");
    const toastBody = toastEl.querySelector(".toast-body");
    const toast = new bootstrap.Toast(toastEl);

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(form);

      const res = await fetch("", {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      toastBody.innerHTML = data.msg;
      toastEl.className = "toast " + (data.status === "success" ? "text-bg-success" : "text-bg-danger");
      toast.show();

      if (data.status === "success") form.reset();
    });
  </script>
</body>
</html>
