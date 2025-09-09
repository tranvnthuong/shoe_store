<?php
session_start();
include("../configs/db.php");

// Chỉ admin mới được vào
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../account/login.php");
  exit;
}

// Biến lưu trạng thái sửa
$edit_mode = false;
$edit_product = null;

// Xóa sản phẩm
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: manage_products.php");
  exit;
}

// Chọn sản phẩm để sửa
if (isset($_GET['edit'])) {
  $edit_mode = true;
  $id = intval($_GET['edit']);
  $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $edit_product = $stmt->get_result()->fetch_assoc();
}

// Thêm sản phẩm
if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $desc = $_POST['description'];

  // Upload ảnh
  $target = "../layout/images/" . basename($_FILES["image"]["name"]);
  move_uploaded_file($_FILES["image"]["tmp_name"], $target);
  $imgPath = "layout/images/" . basename($_FILES["image"]["name"]);

  $stmt = $conn->prepare("INSERT INTO products (name, price, stock, description, image, created_at) VALUES (?,?,?,?,?,NOW())");
  $stmt->bind_param("sdiss", $name, $price, $stock, $desc, $imgPath);
  $stmt->execute();
  header("Location: manage_products.php");
  exit;
}

// Cập nhật sản phẩm
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $desc = $_POST['description'];

  // Nếu có upload ảnh mới
  if (!empty($_FILES["image"]["name"])) {
    $target = "../layout/images/" . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target);
    $imgPath = "layout/images/" . basename($_FILES["image"]["name"]);

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, description=?, image=? WHERE id=?");
    $stmt->bind_param("sdissi", $name, $price, $stock, $desc, $imgPath, $id);
  } else {
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, description=? WHERE id=?");
    $stmt->bind_param("sdssi", $name, $price, $stock, $desc, $id);
  }

  $stmt->execute();
  header("Location: manage_products.php");
  exit;
}

// Lấy danh sách sản phẩm
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Quản lý sản phẩm</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">

<body>
  <div class="container-fluid">
    <div class="row">
      <?php include("../layout/admin_header.php") ?>
      <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 content">
        <h2 class="mb-4">📦 Quản lý sản phẩm</h2>

        <!-- Form thêm / sửa sản phẩm -->
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-dark text-white">
            <?= $edit_mode ? "✏️ Chỉnh sửa sản phẩm" : "➕ Thêm sản phẩm" ?>
          </div>
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data" class="row g-3">
              <input type="hidden" name="id" value="<?= $edit_product['id'] ?? '' ?>">
              <div class="col-md-4">
                <input type="text" name="name" class="form-control" placeholder="Tên sản phẩm"
                  value="<?= $edit_product['name'] ?? '' ?>" required>
              </div>
              <div class="col-md-2">
                <input type="number" name="price" class="form-control" placeholder="Giá"
                  value="<?= $edit_product['price'] ?? '' ?>" required>
              </div>
              <div class="col-md-2">
                <input type="number" name="stock" class="form-control" placeholder="Số lượng"
                  value="<?= $edit_product['stock'] ?? '' ?>" required>
              </div>
              <div class="col-md-4">
                <input type="file" name="image" class="form-control"
                  <?= $edit_mode ? '' : 'required' ?>>
                <?php if ($edit_mode && !empty($edit_product['image'])): ?>
                  <img src="../<?= $edit_product['image'] ?>" width="80" class="mt-2">
                <?php endif; ?>
              </div>
              <div class="col-md-12">
                <textarea name="description" class="form-control"
                  placeholder="Mô tả sản phẩm"><?= $edit_product['description'] ?? '' ?></textarea>
              </div>
              <div class="col-md-12">
                <?php if ($edit_mode): ?>
                  <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
                  <a href="manage_products.php" class="btn btn-secondary">Hủy</a>
                <?php else: ?>
                  <button type="submit" name="add" class="btn btn-success">Thêm sản phẩm</button>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="table-list-manage" style="max-height: 45vh;">
          <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark sticky-top">
              <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Số lượng</th>
                <th>Mô tả</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><img src="../<?= $row['image'] ?>" width="80"></td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= number_format($row['price'], 0, ',', '.') ?> VND</td>
                  <td><?= $row['stock'] ?></td>
                  <td><?= htmlspecialchars($row['description']) ?></td>
                  <td><?= $row['created_at'] ?></td>
                  <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                      <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Xóa sản phẩm này?')">
                      <i class="bi bi-trash"></i> Xóa
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>