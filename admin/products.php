<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Lấy danh mục + thương hiệu để hiển thị trong form
$categories = $conn->query("SELECT * FROM categories");
$brands = $conn->query("SELECT * FROM brands");

// Thêm sản phẩm
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $cat = $_POST['category_id'];
    $brand = $_POST['brand_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image']; // link ảnh, bạn có thể nâng cấp thành upload file

    $stmt = $conn->prepare("INSERT INTO products (name, category_id, brand_id, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siidis", $name, $cat, $brand, $price, $stock, $image);
    $stmt->execute();
    header("Location: products.php");
}

// Xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: products.php");
}

// Sửa sản phẩm
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $cat = $_POST['category_id'];
    $brand = $_POST['brand_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, brand_id=?, price=?, stock=?, image=? WHERE id=?");
    $stmt->bind_param("siidisi", $name, $cat, $brand, $price, $stock, $image, $id);
    $stmt->execute();
    header("Location: products.php");
}

// Lấy danh sách sản phẩm (join để hiện tên danh mục & thương hiệu)
$sql = "SELECT p.*, c.name AS category, b.name AS brand 
        FROM products p 
        LEFT JOIN categories c ON p.category_id=c.id 
        LEFT JOIN brands b ON p.brand_id=b.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Sản phẩm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
  <h3>📦 Quản lý Sản phẩm</h3>

  <!-- Form thêm sản phẩm -->
  <form method="POST" class="row g-2 mb-4">
    <div class="col-md-2"><input type="text" name="name" class="form-control" placeholder="Tên sản phẩm" required></div>
    <div class="col-md-2">
      <select name="category_id" class="form-control" required>
        <option value="">--Danh mục--</option>
        <?php while($c=$categories->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="brand_id" class="form-control" required>
        <option value="">--Thương hiệu--</option>
        <?php while($b=$brands->fetch_assoc()): ?>
        <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-1"><input type="number" step="0.01" name="price" class="form-control" placeholder="Giá" required></div>
    <div class="col-md-1"><input type="number" name="stock" class="form-control" placeholder="SL" required></div>
    <div class="col-md-2"><input type="text" name="image" class="form-control" placeholder="Link ảnh"></div>
    <div class="col-md-2"><button type="submit" name="add" class="btn btn-primary w-100">Thêm</button></div>
  </form>

  <!-- Bảng sản phẩm -->
  <table class="table table-bordered bg-white">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Ảnh</th>
        <th>Tên</th>
        <th>Danh mục</th>
        <th>Thương hiệu</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><img src="<?= $row['image'] ?>" width="60"></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['category'] ?></td>
        <td><?= $row['brand'] ?></td>
        <td><?= number_format($row['price'],0,',','.') ?> VND</td>
        <td><?= $row['stock'] ?></td>
        <td>
          <!-- Nút sửa -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Sửa</button>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
        </td>
      </tr>

      <!-- Modal sửa -->
      <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <form method="POST">
              <div class="modal-header">
                <h5 class="modal-title">Sửa sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="row g-2">
                  <div class="col-md-6">
                    <label class="form-label">Tên</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-control">
                      <?php $cats=$conn->query("SELECT * FROM categories");
                      while($c=$cats->fetch_assoc()): ?>
                      <option value="<?= $c['id'] ?>" <?= $c['id']==$row['category_id']?'selected':'' ?>><?= $c['name'] ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Thương hiệu</label>
                    <select name="brand_id" class="form-control">
                      <?php $brs=$conn->query("SELECT * FROM brands");
                      while($b=$brs->fetch_assoc()): ?>
                      <option value="<?= $b['id'] ?>" <?= $b['id']==$row['brand_id']?'selected':'' ?>><?= $b['name'] ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Giá</label>
                    <input type="number" step="0.01" name="price" value="<?= $row['price'] ?>" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Số lượng</label>
                    <input type="number" name="stock" value="<?= $row['stock'] ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Link ảnh</label>
                    <input type="text" name="image" value="<?= $row['image'] ?>" class="form-control">
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" name="edit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
