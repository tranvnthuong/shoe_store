<?php
session_start();

include("../includes/db.php");

include("../configs/db.php");


// Kiểm tra quyền admin
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

// Thay đổi role (quyền)
if (isset($_POST['change_role'])) {
  $id = intval($_POST['id']);
  $role = $_POST['role'];
  $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
  $stmt->bind_param("si", $role, $id);
  $stmt->execute();
  header("Location: users.php");
}

// Xóa user
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM users WHERE id=$id");
  header("Location: users.php");
}

// Lấy danh sách user
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Quản lý Tài khoản</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container my-4">
    <h3>👥 Quản lý Tài khoản</h3>

    <table class="table table-bordered bg-white">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Email</th>
          <th>SĐT</th>
          <th>Địa chỉ</th>
          <th>Vai trò</th>
          <th>Ngày tạo</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['phone'] ?? '-' ?></td>
            <td><?= $row['address'] ?? '-' ?></td>
            <td>
              <form method="POST" class="d-flex">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <select name="role" class="form-select form-select-sm">
                  <option value="user" <?= $row['role'] == 'user' ? 'selected' : '' ?>>User</option>
                  <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
                <button type="submit" name="change_role" class="btn btn-sm btn-primary ms-2">Lưu</button>
              </form>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>