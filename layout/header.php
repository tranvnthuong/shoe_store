<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include(__DIR__ . "/../configs/db.php");

$base_url = '/shoe_store';

// Hiển thị tên user
$display_name = isset($_SESSION['full_name']) && $_SESSION['full_name'] !== ''
    ? $_SESSION['full_name']
    : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Tài khoản');

// Đếm giỏ hàng
$cart_count = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $q) $cart_count += (int)$q;
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm" style="z-index:3000;">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="<?= $base_url ?>/index.php">
            <i class="fas fa-shoe-prints"></i> Shoe Store
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Menu -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link <?= (in_array($current_page, ['products.php'])) ? 'active' : '' ?>"
                        href="<?= $base_url ?>/pages/products.php">
                        <span>Tất cả sản phẩm</span>
                    </a>
                </li>

                <!-- Nam -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (in_array($current_page, ['men.php'])) ? 'active' : '' ?>"
                        href="#" role="button" data-bs-toggle="dropdown">
                        <span>Nam</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/men.php?type=giaytay">Giầy tây</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/men.php?type=giaythethao">Giầy thể
                                thao</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/men.php?type=dep">Dép</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=balo">Balo</a>
                        </li>
                    </ul>
                </li>

                <!-- Nữ -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (in_array($current_page, ['women.php'])) ? 'active' : '' ?>"
                        href="#" role="button" data-bs-toggle="dropdown">
                        <span>Nữ</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/women.php?type=giaycaogot">Giầy
                                cao gót</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/women.php?type=giaythethao">Giầy
                                thể thao</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/women.php?type=giaychaybodibo">Giầy
                                chạy bộ % đi bộ</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/women.php?type=giaybupbe">Giầy búp
                                bê</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=balo">Balo</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/women.php?type=tuixachvi">Túi xách
                                - Ví</a></li>
                    </ul>
                </li>

                <!-- Phụ kiện -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (in_array($current_page, ['phukien.php'])) ? 'active' : '' ?>"
                        href="#" role="button" data-bs-toggle="dropdown">
                        <span>Phụ kiện</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=balo">Balo</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=charm">Charm</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=lotde">Lót đế</a>
                        </li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=daygiay">Dây
                                giày</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=gaubong">Gấu
                                bông</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>/pages/phukien.php?type=xitkhumui">Xịt
                                khử mùi</a></li>
                    </ul>
                </li>

                <!-- Giới thiệu + Liên hệ -->
                <li class="nav-item">
                    <a class="nav-link <?= (in_array($current_page, ['about.php'])) ? 'active' : '' ?>"
                        href="<?= $base_url ?>/pages/about.php">
                        <span>Giới thiệu</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (in_array($current_page, ['contact.php'])) ? 'active' : '' ?>"
                        href="<?= $base_url ?>/pages/contact.php">
                        <span>Liên hệ</span>
                    </a>
                </li>
            </ul>

            <!-- Bộ lọc sản phẩm -->
            <div class="dropdown me-3">
                <button class="btn btn-sm btn-outline-warning dropdown-toggle" type="button" id="filterDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel"></i> Lọc
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3 shadow" style="min-width: 250px;">
                    <form method="GET" action="<?= $base_url ?>/pages/products.php">
                        <div class="mb-2">
                            <label class="form-label small mb-1">Danh mục</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="0">Tất cả</option>
                                <?php
                                $cate = $conn->query("SELECT * FROM categories");
                                while ($c = $cate->fetch_assoc()):
                                ?>
                                    <option value="<?= $c['id'] ?>"
                                        <?= (isset($_GET['category']) && $_GET['category'] == $c['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small mb-1">Sắp xếp</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="">Mặc định</option>
                                <option value="price_asc"
                                    <?= (isset($_GET['sort']) && $_GET['sort'] == "price_asc") ? "selected" : "" ?>>Giá
                                    ↑</option>
                                <option value="price_desc"
                                    <?= (isset($_GET['sort']) && $_GET['sort'] == "price_desc") ? "selected" : "" ?>>Giá
                                    ↓</option>
                                <option value="newest"
                                    <?= (isset($_GET['sort']) && $_GET['sort'] == "newest") ? "selected" : "" ?>>Mới
                                    nhất</option>
                                <option value="oldest"
                                    <?= (isset($_GET['sort']) && $_GET['sort'] == "oldest") ? "selected" : "" ?>>Cũ nhất
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-sm btn-warning w-100">
                            <i class="fas fa-filter"></i> Áp dụng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tìm kiếm -->
            <form class="d-flex me-3" action="<?= $base_url ?>/pages/products.php" method="get">
                <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="Bạn cần tìm gì?"
                    aria-label="Search" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
                <button class="btn btn-sm btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
            </form>

            <!-- Giỏ hàng -->
            <a href="<?= $base_url ?>/pages/cart.php" class="text-light position-relative me-3 fs-5" title="Giỏ hàng">
                <i class="fas fa-shopping-cart"></i>
                <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $cart_count ?>
                    </span>
                <?php endif; ?>
            </a>

            <!-- Dropdown tài khoản -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="accountDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-user"></i> <?= htmlspecialchars($display_name) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                        <?php if (isset($_SESSION['username'])): ?>
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>/account/profile.php">
                                    Thông tin cá nhân
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>/pages/orders.php">
                                    Đơn hàng
                                </a>
                            </li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= $base_url ?>/admin/dashboard.php">
                                        Quản trị Admin
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>/account/logout.php">
                                    Đăng xuất
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>/account/login.php">
                                    Đăng nhập
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= $base_url ?>/account/register.php">Đăng ký

                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>