 <h4>⚙️ Admin</h4>
 <p>Xin chào <b><?= $_SESSION['full_name'] ?></b></p>
 <hr>
 <a class="sidebar-link <?= (in_array($current_page, ['dashboard.php'])) ? 'active' : '' ?>" href="dashboard.php">
     <i class="fa fa-gauge"></i>
     <span>Dashboard</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_users.php'])) ? 'active' : '' ?>" href="manage_users.php">
     <i class="fa fa-users"></i>
     <span>Quản lý người dùng</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_products.php'])) ? 'active' : '' ?>"
     href="manage_products.php">
     <i class="fa fa-box"></i>
     <span>Quản lý sản phẩm</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_orders.php'])) ? 'active' : '' ?>"
     href="manage_orders.php">
     <i class="fa fa-cart-shopping"></i>
     <span>Quản lý đơn hàng</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_categories.php'])) ? 'active' : '' ?>"
     href="manage_categories.php">
     <i class="fa fa-list"></i>
     <span>Quản lý danh mục</span>
 </a>
 <a class="sidebar-link <?= (in_array($current_page, ['manage_brands.php'])) ? 'active' : '' ?>"
     href="manage_brands.php">
     <i class="fa fa-tags"></i>
     <span>Quản lý thương hiệu</span>
 </a>
 <a class="sidebar-link" href="../index.php">
     <i class="fa fa-home"></i>
     <span>Về trang chủ</span>
 </a>
 <a class="sidebar-link" href="../account/logout.php">
     <i class="fa fa-door-closed"></i>
     <span>Đăng xuất</span>
 </a>