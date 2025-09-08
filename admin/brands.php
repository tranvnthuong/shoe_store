<?php
session_start();
include("../includes/db.php");

// Kiểm tra admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Thêm thương hiệu
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    if ($name != "") {
        $stmt = $conn->prepare("INSERT INTO brands (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: brands.php");
    }
}

// Xóa thương hiệu
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM brands WHERE id=$id");
    header("Location: brands.php");
}

// Sửa thương hiệu
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $stmt = $conn->prepare("UPDATE brands SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    header("Location: brands.php");
}

// Lấy danh sách thương hiệu
$result = $conn->query("SELECT * FROM brands ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Quản lý Thương hiệu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
  <h3>🏷️ Quản lý Thương hiệu</h3>

  <!-- Form thêm -->
  <form method="POST" class="row g-2 mb-3">
    <div class="col-auto">
      <input type="text" name="name" class="form-control" placeholder="Tên thương hiệu" required>
    </div>
    <div class="col-auto">
      <button type="submit" name="add" class="btn btn-primary">Thêm mới</button>
    </div>
  </form>

  <!-- Bảng -->
  <table class="table table-bordered bg-white">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Tên thương hiệu</th>
        <th>Ngày tạo</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <!-- Nút sửa -->
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Sửa</button>
          <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa thương hiệu này?')">Xóa</a>
        </td>
      </tr>

      <!-- Modal sửa -->
      <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="POST">
              <div class="modal-header">
                <h5 class="modal-title">Sửa thương hiệu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" class="form-control" required>
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
