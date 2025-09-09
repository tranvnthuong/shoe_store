 <h4>⚙️ Admin</h4>
 <p>Xin chào <b><?= $_SESSION['full_name'] ?></b></p>
 <hr>
 <a class="sidebar-link <?= (in_array($current_page, ['dashboard.php'])) ? 'active' : '' ?>" href="dashboard.php">
     <i class="fa fa-gauge"></i>
     <span>Dashboard</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_users.php'])) ? 'active' : '' ?>" href="manage_users.php">
     <i class="fa fa-users"></i>
     <span>Người dùng</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_products.php'])) ? 'active' : '' ?>"
     href="manage_products.php">
     <i class="fa fa-box"></i>
     <span>Sản phẩm</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_orders.php'])) ? 'active' : '' ?>"
     href="manage_orders.php">
     <i class="fa fa-cart-shopping"></i>
     <span>Đơn hàng</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_categories.php'])) ? 'active' : '' ?>"
     href="manage_categories.php">
     <i class="fa fa-list"></i>
     <span>Danh mục</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_carousel.php'])) ? 'active' : '' ?>"
     href="manage_carousel.php">
     <i class="fa-solid fa-panorama"></i>
     <span>Slide Banner</span>
 </a>
 <a class="sidebar-link" href="../index.php">
     <i class="fa fa-home"></i>
     <span class="text-primary">Trang chủ</span>
 </a>
 <a class="sidebar-link" href="../account/logout.php">
     <i class="fa fa-door-closed"></i>
     <span class="text-danger">Đăng xuất</span>
 </a>