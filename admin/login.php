<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}



$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role']; // lưu quyền


?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Shoe Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f5f6fa;
    }

    .sidebar {
      height: 100vh;
      background: #343a40;
      color: #fff;
    }

    .sidebar a {
      color: #ccc;
      text-decoration: none;
      display: block;
      padding: 12px;
    }

    .sidebar a:hover {
      background: #495057;
      color: #fff;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar">
        <h4 class="p-3 border-bottom">👑 Admin</h4>
        <a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="categories.php"><i class="bi bi-list-ul"></i> Quản lý danh mục</a>
        <a href="brands.php"><i class="bi bi-tags"></i> Quản lý thương hiệu</a>
        <a href="products.php"><i class="bi bi-box-seam"></i> Quản lý sản phẩm</a>
        <a href="orders.php"><i class="bi bi-cart-check"></i> Quản lý đơn hàng</a>
        <a href="users.php"><i class="bi bi-people"></i> Quản lý tài khoản</a>
        <a href="revenue.php"><i class="bi bi-graph-up"></i> Thống kê doanh thu</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
      </div>

      <!-- Main -->
      <div class="col-md-10 p-4">
        <h2>📊 Bảng điều khiển</h2>
        <p>Chào mừng Admin, bạn có thể quản lý toàn bộ hệ thống tại đây.</p>
      </div>
    </div>
  </div>
</body>

</html>