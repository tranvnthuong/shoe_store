<?php
session_start();
include("includes/db.php");

// Khởi tạo giỏ
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Thêm sản phẩm
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $q  = $conn->query("SELECT * FROM products WHERE id=$id");
    if ($q->num_rows > 0) {
        $p = $q->fetch_assoc();
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                "name"=>$p['name'],
                "price"=>$p['price'],
                "quantity"=>1,
                "image"=>$p['image']
            ];
        }
    }
    echo "<script>alert('Đã thêm vào giỏ hàng'); window.location='cart.php';</script>";
}

// Xóa 1 sp
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    echo "<script>alert('Đã xóa sản phẩm'); window.location='cart.php';</script>";
}

// Xóa tất cả
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    echo "<script>alert('Đã xóa giỏ hàng'); window.location='cart.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Giỏ hàng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <h2>🛒 Giỏ hàng</h2>
  <?php if (!empty($_SESSION['cart'])): ?>
  <table class="table table-bordered">
    <thead><tr>
      <th>Ảnh</th><th>Sản phẩm</th><th>Giá</th><th>Số lượng</th><th>Thành tiền</th><th></th>
    </tr></thead>
    <tbody>
      <?php $total=0; foreach ($_SESSION['cart'] as $id=>$item): 
        $sub=$item['price']*$item['quantity']; $total+=$sub; ?>
        <tr>
          <td><img src="<?= $item['image'] ?>" width="70" height="70" style="object-fit:cover;"></td>
          <td><?= $item['name'] ?></td>
          <td><?= number_format($item['price'],0,",",".") ?> VND</td>
          <td><?= $item['quantity'] ?></td>
          <td><?= number_format($sub,0,",",".") ?> VND</td>
          <td><a href="cart.php?remove=<?= $id ?>" class="btn btn-sm btn-danger">Xóa</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <h4>Tổng: <span class="text-danger"><?= number_format($total,0,",",".") ?> VND</span></h4>
  <a href="checkout.php" class="btn btn-success">Thanh toán</a>
  <a href="cart.php?clear=1" class="btn btn-secondary">Xóa giỏ</a>
  <?php else: ?>
    <div class="alert alert-info">Giỏ hàng trống.</div>
  <?php endif; ?>
</div>
</body>
</html>
