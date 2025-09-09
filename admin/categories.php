<?php
session_start();
include("../includes/db.php");

// Nếu chưa đăng nhập admin
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

// Thêm danh mục
if (isset($_POST['add'])) {
  $name = trim($_POST['name']);
  if ($name != "") {
    $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    header("Location: categories.php");
  }
}

// Xóa danh mục
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM categories WHERE id=$id");
  header("Location: categories.php");
}

// Sửa danh mục
if (isset($_POST['edit'])) {
  $id = intval($_POST['id']);
  $name = trim($_POST['name']);
  $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
  $stmt->bind_param("si", $name, $id);
  $stmt->execute();
  header("Location: categories.php");
}

// Lấy danh sách danh mục
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Quản lý Danh mục</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container-fluid">
    <div class="row">
      <?php include("../layout/admin_header.php") ?>
      <h3>📂 Quản lý Danh mục</h3>

      <!-- Form thêm danh mục -->
      <form method="POST" class="row g-2 mb-3">
        <div class="col-auto">
          <input type="text" name="name" class="form-control" placeholder="Tên danh mục" required>
        </div>
        <div class="col-auto">
          <button type="submit" name="add" class="btn btn-primary">Thêm mới</button>
        </div>
      </form>

      <!-- Bảng danh mục -->
      <table class="table table-bordered bg-white">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Tên danh mục</th>
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
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                  data-bs-target="#editModal<?= $row['id'] ?>">Sửa</button>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                  onclick="return confirm('Xóa danh mục này?')">Xóa</a>
              </td>
            </tr>

            <!-- Modal sửa -->
            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST">
                    <div class="modal-header">
                      <h5 class="modal-title">Sửa danh mục</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"
                        class="form-control" required>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" name="edit" class="btn btn-success">Lưu</button>
                      <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Đóng</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>