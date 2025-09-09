<?php
session_start();
include("../configs/db.php");

// Chỉ admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit;
}

// Xóa danh mục
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: manage_categories.php");
  exit;
}

// Biến lưu trạng thái sửa
$edit_mode = false;
$edit_category = null;

// Chọn danh mục để sửa
if (isset($_GET['edit'])) {
  $edit_mode = true;
  $id = intval($_GET['edit']);
  $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $edit_category = $stmt->get_result()->fetch_assoc();
}

// Thêm danh mục
if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $stmt = $conn->prepare("INSERT INTO categories (name, created_at) VALUES (?, NOW())");
  $stmt->bind_param("s", $name);
  $stmt->execute();
  header("Location: manage_categories.php");
  exit;
}

// Cập nhật danh mục
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $stmt = $conn->prepare("UPDATE categories SET name=? WHERE id=?");
  $stmt->bind_param("si", $name, $id);
  $stmt->execute();
  header("Location: manage_categories.php");
  exit;
}

// Lấy danh sách danh mục
$result = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title>Quản lý danh mục</title>
  <link rel="icon" type="image/x-icon" href="../favicon.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <?php include("../layout/admin_header.php") ?>
      <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 content">
        <h2 class="mb-4">📂 Quản lý danh mục</h2>

        <!-- Form thêm / sửa danh mục -->
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-dark text-white">
            <?= $edit_mode ? "✏️ Chỉnh sửa danh mục" : "➕ Thêm danh mục" ?>
          </div>
          <div class="card-body">
            <form method="POST" class="row g-3">
              <input type="hidden" name="id" value="<?= $edit_category['id'] ?? '' ?>">
              <div class="col-md-8">
                <input type="text" name="name" class="form-control" placeholder="Tên danh mục"
                  value="<?= $edit_category['name'] ?? '' ?>" required>
              </div>
              <div class="col-md-4">
                <?php if ($edit_mode): ?>
                  <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
                  <a href="manage_categories.php" class="btn btn-secondary">Hủy</a>
                <?php else: ?>
                  <button type="submit" name="add" class="btn btn-success">Thêm</button>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>

        <!-- Danh sách danh mục -->
        <div class="table-list-manage">
          <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark sticky-top">
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
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                      <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Xóa danh mục này?')">
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>