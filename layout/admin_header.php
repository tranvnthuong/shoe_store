<?php
$current_page = basename($_SERVER['PHP_SELF']);

$stmt = $conn->prepare("SELECT COUNT(status) AS pending_status_count FROM orders WHERE status = 'pending'");
$stmt->execute();
$orders_result = $stmt->get_result()->fetch_assoc();

$stmt2 = $conn->prepare("SELECT COUNT(trang_thai) AS trang_thai_count FROM nap_tien WHERE trang_thai = 'choduyet'");
$stmt2->execute();
$naptien_result = $stmt2->get_result()->fetch_assoc();
?>
<!-- Nút mở sidebar chỉ hiện trên mobile -->
<button class="btn btn-primary d-md-none m-2" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
    <i class="fa fa-bars"></i> Menu
</button>

<!-- Sidebar cho desktop -->
<nav class="col-md-2 d-none d-md-block bg-dark sidebar">
    <div class="p-3 text-white">
        <?php include("../includes/sidebar_item_admin.php") ?>
    </div>
</nav>

<!-- Sidebar mobile dạng Offcanvas -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMobile">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">⚙️ Admin</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <?php include("../includes/sidebar_item_admin.php") ?>
    </div>
</div>