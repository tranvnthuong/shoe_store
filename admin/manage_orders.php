<?php
session_start();
include("../configs/db.php");

// Chỉ admin mới được vào
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Xóa đơn hàng
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_orders.php");
    exit;
}

// Cập nhật trạng thái đơn hàng
if (isset($_POST['update_status'])) {
    $id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    header("Location: manage_orders.php");
    exit;
}

// Lấy danh sách đơn hàng
$sql = "SELECT o.*, u.full_name, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Quản lý đơn hàng</title>
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
            <main class="col-12 col-md-10 ms-sm-auto px-md-4 dashboard-content">
                <h2 class="mb-4"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</h2>

                <div class="table-list-manage" style="max-height: 80vh;">
                    <table class="table table-bordered table-hover align-middle text-center">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Email</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <?php
                                    $order_id = $row['id'];
                                    $sql_items = "SELECT SUM(price * quantity) AS total FROM order_items WHERE order_id = $order_id";
                                    $total_res = $conn->query($sql_items);
                                    $total_row = $total_res->fetch_assoc();
                                    $total = $total_row['total'] ?? 0;
                                    ?>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= number_format($total, 0, ',', '.') ?> VND</td>
                                    <td>
                                        <form method="POST" class="d-flex justify-content-center align-items-center">
                                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                            <select name="status" class="form-select form-select-sm me-2">
                                                <option value="pending"
                                                    <?= $row['status'] == 'pending' ? 'selected' : '' ?>>⏳
                                                    Chờ xử lý
                                                </option>
                                                <option value="processing"
                                                    <?= $row['status'] == 'processing' ? 'selected' : '' ?>>🔄
                                                    Đang xử lý</option>
                                                <option value="shipping"
                                                    <?= $row['status'] == 'shipping' ? 'selected' : '' ?>>🚚
                                                    Đang giao</option>
                                                <option value="completed"
                                                    <?= $row['status'] == 'completed' ? 'selected' : '' ?>>✅
                                                    Đã giao
                                                </option>
                                                <option value="returned"
                                                    <?= $row['status'] == 'returned' ? 'selected' : '' ?>>🔁
                                                    Trả hàng
                                                </option>
                                                <option value="cancelled"
                                                    <?= $row['status'] == 'cancelled' ? 'selected' : '' ?>>❌ Đã
                                                    hủy</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-sm btn-primary">Cập
                                                nhật</button>
                                        </form>
                                    </td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td>
                                        <a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Xóa đơn hàng này?')">
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