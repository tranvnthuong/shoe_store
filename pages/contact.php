<?php
include("../configs/db.php");
session_start();

$success = "";
$error = "";

// Khi form được submit
if (isset($_POST['submit'])) {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $message = trim($_POST['message']);

  if ($name && $email && $message) {
    $stmt = $conn->prepare("INSERT INTO contacts (name,email,phone,message) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);
    if ($stmt->execute()) {
      $success = "✅ Cảm ơn bạn! Chúng tôi sẽ liên hệ sớm.";
    } else {
      $error = "❌ Lỗi khi gửi liên hệ. Vui lòng thử lại!";
    }
  } else {
    $error = "Vui lòng điền đầy đủ thông tin bắt buộc.";
  }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Liên hệ - Shoe Store</title>
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
    <h2>📞 Liên hệ với chúng tôi</h2>
    <p>Nếu bạn có thắc mắc, hãy để lại thông tin hoặc ghé trực tiếp cửa hàng.</p>

    <div class="row">
      <!-- Form liên hệ -->
      <div class="col-md-6">
        <form method="POST" action="contact_submit.php" class="mt-4">
          <div class="mb-3">
            <label for="name" class="form-label">Họ và tên</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Nội dung</label>
            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Gửi</button>
        </form>
      </div>

      <!-- Google Map -->
      <div class="col-md-6">
        <h5>📍 Địa chỉ cửa hàng</h5>

        <p>123 Đường ABC, Quận XYZ, TP.HCM</p>

        <p>123 Đường ABC, Quận XYZ, TP.Bắc Ninh</p>

        <div class="map-responsive">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.502046947835!2d106.7004232153349!3d10.7768890621226!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3f5ad3a2fb%3A0xf48a3c9afcd0a06!2zQ2hpIMSQ4bqhaSBIb8OgbmcgVMOyYSBQaOG6p24!5e0!3m2!1svi!2s!4v1675242941234!5m2!1svi!2s"
            width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>
  </div>
  <?php include("../layout/footer.php"); ?>