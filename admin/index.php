<?php
session_start();
include("../includes/db.php");
include("../configs/db.php");
// Kiểm tra quyền admin
// if ($_SESSION['role'] != 'admin') { header("Location: ../login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 250px;
        background: #343a40;
        color: #fff;
    }

    .sidebar a {
        color: #fff;
        display: block;
        padding: 12px 20px;
        text-decoration: none;
    }

    .sidebar a:hover {
        background: #495057;
    }

    .content {
        flex-grow: 1;
        padding: 20px;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="p-3">⚙️ Admin</h3>
        <a href="orders.php">🛒 Quản lý đơn hàng</a>
        <a href="products.php">📦 Quản lý sản phẩm</a>
        <a href="categories.php">🏷️ Quản lý danh mục</a>
        <a href="users.php">👥 Quản lý tài khoản</a>
        <a href="stats.php">💰 Thống kê doanh thu</a>
        <a href="../index.php">↩️ Về trang chủ</a>
    </div>
    <div class="content">
        <h2>📊 Bảng điều khiển</h2>
        <p>Chào mừng Admin, chọn chức năng ở menu bên trái.</p>
    </div>
</body>

</html>