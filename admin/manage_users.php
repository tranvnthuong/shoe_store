<?php
session_start();
include("../configs/db.php");

// Chỉ admin mới được vào
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../account/login.php");
    exit;
}

// Xóa user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Đổi role
if (isset($_GET['toggle_role'])) {
    $id = intval($_GET['toggle_role']);
    $stmt = $conn->prepare("SELECT role FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $new_role = ($result['role'] === 'admin') ? 'user' : 'admin';

    $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->bind_param("si", $new_role, $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Lấy danh sách user
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Quản lý tài khoản</title>
    <link rel="icon" type="image/x-icon" href="../favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <h2 class="mb-4">👥 Quản lý tài khoản</h2>

                <div class="table-list-manage" style="max-height: 80vh;">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Ngày tạo</th>
                                <th>Quyền</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <?php if ($row['role'] === 'admin'): ?>
                                            <span class="badge bg-danger">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">User</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['id'] != $_SESSION['user_id']): // Không cho admin xóa chính mình 
                                        ?>
                                            <a href="?toggle_role=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-shield-lock"></i> Đổi quyền
                                            </a>
                                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Xóa tài khoản này?')">
                                                <i class="bi bi-trash"></i> Xóa
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tài khoản của bạn</span>
                                        <?php endif; ?>
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