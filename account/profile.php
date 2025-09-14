<?php
session_start();
include("../configs/db.php");

// Nếu chưa đăng nhập thì quay về login
if (!$_SESSION['user_id']) {
  header("Location: login.php");
  exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error = "";
$success = "";


?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Thông tin cá nhân</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    crossorigin="anonymous" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <?php include("../layout/header.php"); ?>

  <div class="container" style="padding-top: 80px;">
    <h2><i class="fa-solid fa-address-card"></i>Thông tin cá nhân</h2>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm p-3 mb-4">
      <p>
        <strong>Họ tên:</strong>
        <span class="full_name"> <?= $_SESSION['full_name']; ?></span>
      </p>
      <p>
        <strong>Ngày sinh:</strong>
        <span class="day_of_birth"> <?= $_SESSION['day_of_birth'] ?? "Chưa cập nhật"; ?></span>
      </p>
      <p>
        <strong>Email:</strong>
        <span> <?= $_SESSION['email']; ?></span>
      </p>
      <p>
        <strong>Số dư:</strong>
        <span> <?= number_format($_SESSION['balance'], 0, ',', '.') ?><span> VND
      </p>
      <p>
        <strong>Số điện thoại:</strong>
        <span class="phone"> <?= $_SESSION['phone'] ?? "Chưa cập nhật"; ?></span>
      </p>
      <p>
        <strong>Địa chỉ:</strong>
        <span class="address"> <?= $_SESSION['address'] ?? "Chưa cập nhật"; ?></span>
      </p>
      <p>
        <strong>Ngày tham gia:</strong>
        <span><?= $_SESSION['created_at'] ?? "Chưa cập nhật"; ?></span>
      </p>
      <p>
        <strong>Vai trò:</strong>
        <span> <?= $_SESSION['role']; ?></span>
      </p>

      <div class="d-flex justify-content-center gap-2 flex-wrap">
        <button id="updateProfileBtn" class="btn btn-primary" data-bs-toggle="modal"
          data-bs-target="#updateInfoModal">
          <span><i class="fa-solid fa-square-pen"></i></span>
          Cập nhật thông tin
        </button>
        <button id="changePasswordBtn" class="btn btn-warning" data-bs-toggle="modal"
          data-bs-target="#changePasswordModal">
          </span><i class="fa-solid fa-lock"></i></span>
          Đổi mật khẩu
        </button>
        <button id="depositBtn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#depositModal">
          <span><i class="fa-solid fa-wallet"></i></span>
          Nạp tiền
        </button>
        <a href="recharge_history.php" class="btn btn-info"><i class="fa-solid fa-clock-rotate-left"></i>Lịch sử
          nạp</a>
        <a href="logout.php" class="btn btn-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i>Đăng
          xuất</a>
      </div>
    </div>
  </div>

  <!-- Modal 1: Cập nhật thông tin -->
  <div class="modal fade" id="updateInfoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form class="profile-form" method="post">
          <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
          <input type="hidden" name="action" value="update_profile">
          <div class="modal-header">
            <h5 class="modal-title">Cập nhật thông tin cá nhân</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3"><label>Họ tên</label>
              <input type="text" class="form-control full_name" name="full_name"
                value="<?= $_SESSION['full_name'] ?>" required>
            </div>
            <div class="mb-3"><label>Ngày sinh</label>
              <input type="date" class="form-control day_of_birth" name="day_of_birth"
                value="<?= $_SESSION['day_of_birth'] ?>">
            </div>
            <div class="mb-3"><label>Số điện thoại</label>
              <input type="text" class="form-control phone" name="phone"
                value="<?= $_SESSION['phone'] ?>">
            </div>
            <div class="mb-3"><label>Địa chỉ</label>
              <input type="text" class="form-control address" name="address"
                value="<?= $_SESSION['address'] ?>">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal 2: Đổi mật khẩu -->
  <div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form class="profile-form" method="post">
          <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
          <input type="hidden" name="action" value="change_password">
          <div class="modal-header">
            <h5 class="modal-title">Đổi mật khẩu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3"><label>Mật khẩu hiện tại</label>
              <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3"><label>Mật khẩu mới</label>
              <input type="password" class="form-control" name="new_password" required>
            </div>
            <div class="mb-3"><label>Xác nhận mật khẩu mới</label>
              <input type="password" class="form-control" name="confirm_password" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-warning">Đổi mật khẩu</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal 3: Nạp tiền qua ngân hàng -->
  <div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form class="profile-form" method="post">
          <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
          <input type="hidden" name="action" value="deposit_money">
          <div class="modal-header">
            <h5 class="modal-title">💰 Nạp tiền qua ngân hàng</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info">
              <p><strong>Thông tin chuyển khoản:</strong></p>
              <p>Ngân hàng: <b>Vietcombank</b></p>
              <p>Số tài khoản: <b>0123456789</b></p>
              <p>Chủ tài khoản: <b>Nguyen Van A</b></p>
              <p>Nội dung: <b>naptien_<?= $_SESSION['email'] ?></b></p>
            </div>
            <div class="mb-3">
              <label for="amount">Số tiền muốn nạp (VND)</label>
              <input type="text" id="amount" class="form-control" name="amount" min="10000" step="1000"
                max="100000000" required autocomplete="off">
              <datalist id="suggestions">
                <option value="10,000 VND">
                <option value="100,000 VND">
                <option value="1,000,000 VND">
                <option value="10,000,000 VND">
                <option value="100,000,000 VND">
                <option value="20,000 VND">
                <option value="200,000 VND">
                <option value="2,000,000 VND">
                <option value="20,000,000 VND">
                <option value="30,000 VND">
                <option value="300,000 VND">
                <option value="3,000,000 VND">
                <option value="30,000,000 VND">
                <option value="40,000 VND">
                <option value="400,000 VND">
                <option value="4,000,000 VND">
                <option value="40,000,000 VND">
                <option value="50,000 VND">
                <option value="500,000 VND">
                <option value="5,000,000 VND">
                <option value="50,000,000 VND">
                <option value="60,000 VND">
                <option value="600,000 VND">
                <option value="6,000,000 VND">
                <option value="60,000,000 VND">
                <option value="70,000 VND">
                <option value="700,000 VND">
                <option value="7,000,000 VND">
                <option value="70,000,000 VND">
                <option value="80,000 VND">
                <option value="800,000 VND">
                <option value="8,000,000 VND">
                <option value="80,000,000 VND">
                <option value="90,000 VND">
                <option value="900,000 VND">
                <option value="9,000,000 VND">
                <option value="90,000,000 VND">
              </datalist>
              <script>
                const input = document.getElementById('amount');
                const options = Array.from(document.getElementById('suggestions').options).map(o => o
                  .value);

                function parseMoney(str) {
                  if (!str) return '';
                  return str.replace(/[^\d]/g, ''); // bỏ hết ký tự không phải số
                }


                let raw = 0;
                input.addEventListener('input', () => {
                  if (!input.hasAttribute("list")) {
                    input.setAttribute('list', 'suggestions');
                  }
                  if (input.value.trim() === '') {
                    input.removeAttribute('list');
                  }
                  if (options.includes(input.value)) {
                    raw = parseMoney(input.value).substring(0, 8);
                    input.value = raw;
                  } else {
                    input.value = parseMoney(input.value).substring(0, 8);
                  }
                });
                input.addEventListener('blur', () => {
                  if (raw != 0)
                    input.value = raw;
                  setTimeout(() => {
                    input.removeAttribute('list');
                    raw = 0;
                  }, 1000);
                })
              </script>
            </div>
            <p class="text-muted"><i>Sau khi chuyển khoản, admin sẽ duyệt và cộng tiền vào tài khoản của
                bạn.</i></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success">Tạo yêu cầu nạp</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php include("../layout/footer.php"); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/script.js"></script>
  <script>
    $(document).ready(function() {
      function updateInformation(full_name, day_of_birth, phone, address) {
        const data = {
          full_name,
          day_of_birth,
          phone,
          address
        };

        $.each(data, function(key, value) {
          $("span." + key).html(value); // cập nhật span
          $("input." + key).val(value); // cập nhật input
        });
      }

      const btnLoaderUpdateProfile = makeButtonLoader($("#updateProfileBtn"));
      const btnLoaderChangePassword = makeButtonLoader($("#changePasswordBtn"));
      const btnLoaderDeposit = makeButtonLoader($("#depositBtn"));

      $(".profile-form").on("submit", function(e) {
        e.preventDefault();

        $.ajax({
          url: "../api/profile_api.php",
          type: "POST",
          data: $(this).serialize(),
          dataType: "json",
          beforeSend: () => {
            let action = $(this).find("input[name=action]").val();
            if (action == "update_profile") {
              btnLoaderUpdateProfile.showLoading();
            } else if (action == "change_password") {
              btnLoaderChangePassword.showLoading();
            } else if (action == "deposit_money") {
              btnLoaderDeposit.showLoading();
            }
            $(".modal").modal("hide");
          },
          complete: () => {
            let action = $(this).find("input[name=action]").val();
            if (action == "update_profile") {
              btnLoaderUpdateProfile.showDefault();
            } else if (action == "change_password") {
              btnLoaderChangePassword.showDefault();
            } else if (action == "deposit_money") {
              btnLoaderDeposit.showDefault();
            }
          },
          success: (data) => {
            showMessage(data);
            if (data.status === "success") {
              let action = $(this).find("input[name=action]").val();
              $(this)[0].reset();
              $(this).find("input[name=action]").val(action);
              if (action == "update_profile") {
                updateInformation(
                  data.full_name,
                  data.day_of_birth,
                  data.phone,
                  data.address
                );
              }
            }
          },
          error: (xhr, status, error) => {
            Swal.fire({
              icon: "error",
              title: "Lỗi server",
              text: "Không thể gửi yêu cầu. Vui lòng thử lại!",
            });
            console.error(error);
          }
        });
      });
    });
  </script>
</body>

</html>