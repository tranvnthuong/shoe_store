-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 08, 2025 lúc 11:11 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shoestore`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`) VALUES
(1, 'GUCCI', '2025-09-05 08:19:51'),
(2, 'cr7', '2025-09-05 08:20:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Sneaker', '2025-09-03 07:18:39'),
(2, 'Giày da', '2025-09-03 07:18:39'),
(3, 'Boot', '2025-09-03 07:18:39'),
(4, 'Sandal', '2025-09-03 07:18:39'),
(5, 'Balo', '2025-09-06 13:59:34'),
(6, 'Charm', '2025-09-06 13:59:34'),
(7, 'Dây giày', '2025-09-06 13:59:34'),
(8, 'Lót đế', '2025-09-06 13:59:34'),
(9, 'Gấu bông', '2025-09-06 13:59:34'),
(10, 'Xịt khử mùi', '2025-09-06 13:59:34'),
(11, 'Giầy tây', '2025-09-06 14:44:52'),
(12, 'Giầy thể thao', '2025-09-06 14:44:52'),
(13, 'Dép', '2025-09-06 14:44:52'),
(14, 'Giầy cao gót', '2025-09-08 02:30:40'),
(15, 'Giầy thể thao', '2025-09-08 02:30:40'),
(16, 'Giầy chạy bộ & đi bộ', '2025-09-08 02:30:40'),
(17, 'Giầy búp bê', '2025-09-08 02:30:40'),
(18, 'Túi xách & ví', '2025-09-08 02:30:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `type` enum('percent','fixed') DEFAULT 'percent',
  `expiry` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount`, `type`, `expiry`) VALUES
(1, 'SALE10', 10.00, 'percent', '2025-12-31'),
(2, 'GIAM50K', 50000.00, 'fixed', '2025-12-31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total` decimal(15,2) NOT NULL,
  `status` varchar(20) DEFAULT 'Mới',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `phone`, `address`, `payment_method`, `total`, `status`, `created_at`, `total_price`) VALUES
(1, NULL, '', '0965773279', 'Phe ', 'COD', 0.00, 'pending', '2025-09-06 01:10:01', 0.00),
(2, NULL, '', '0965773279', 'Phe ', 'Bank', 0.00, 'pending', '2025-09-06 01:17:12', 0.00),
(3, NULL, '', '0965773279', 'Phe ', 'COD', 1200000.00, 'pending', '2025-09-06 01:18:18', 0.00),
(4, NULL, '', '0965773279', 'Phe ', 'COD', 1200000.00, 'pending', '2025-09-06 01:23:43', 0.00),
(5, NULL, '', '0965773279', 'Phe ', 'COD', 1200000.00, 'pending', '2025-09-06 01:36:46', 0.00),
(6, NULL, '', '0965773279', 'ABC@xzy.com', 'COD', 2500000.00, 'pending', '2025-09-06 11:37:04', 0.00),
(7, NULL, '', '096532132', 'abc', 'COD', 950000.00, 'pending', '2025-09-06 11:48:16', 0.00),
(8, 2, '', '0965773279', 'ABC@xzy.com', 'Bank', 950000.00, 'processing', '2025-09-06 12:21:55', 0.00),
(9, NULL, '', '0965773279', 'abc', 'Bank', 3600000.00, 'pending', '2025-09-06 13:57:16', 0.00),
(10, NULL, '', '0965773279', 'Phe ', 'COD', 6250000.00, 'pending', '2025-09-08 02:45:55', 0.00),
(11, NULL, '', '111111', '221121', 'COD', 2100000.00, 'pending', '2025-09-08 03:08:34', 0.00),
(12, NULL, '', '1', '1', 'COD', 1120000.00, 'pending', '2025-09-08 03:34:58', 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 2, 1200000.00),
(2, 2, 1, 1, 1200000.00),
(3, 3, 1, 1, 1200000.00),
(4, 4, 1, 1, 1200000.00),
(5, 5, 1, 1, 1200000.00),
(6, 6, 5, 1, 2500000.00),
(7, 7, 6, 1, 950000.00),
(8, 8, 6, 1, 950000.00),
(9, 9, 3, 2, 1800000.00),
(10, 10, 5, 1, 2500000.00),
(11, 10, 6, 1, 950000.00),
(12, 10, 8, 1, 2800000.00),
(13, 11, 9, 1, 2100000.00),
(14, 12, 76, 1, 520000.00),
(15, 12, 81, 1, 600000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 100,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`, `stock`, `image`, `description`, `created_at`) VALUES
(1, 1, 'Giày Sneaker Trắng', 1200000.00, 50, 'https://i.pinimg.com/736x/77/6f/5d/776f5db81803121042f5f0f0e1c9fa95.jpg', 'Sneaker trắng phong cách.', '2025-09-03 07:18:39'),
(2, 1, 'Giày Sneaker Đen', 1100000.00, 60, 'https://i.pinimg.com/1200x/46/a7/a4/46a7a44fe61ce8dfd3f05e870f2a78ae.jpg', 'Sneaker đen năng động.', '2025-09-03 07:18:39'),
(3, 2, 'Giày sneaker nike jordan', 1800000.00, 40, 'https://i.pinimg.com/736x/f0/cc/52/f0cc527558e0edf98626ee69a5d91ea4.jpg', 'Da thật 100%.', '2025-09-03 07:18:39'),
(4, 3, 'Giày Boot Nữ', 1500000.00, 30, 'https://i.pinimg.com/736x/fc/0c/ec/fc0cecee8b39a830447ea3105e99fd7d.jpg', 'Boot nữ cá tính.', '2025-09-03 07:18:39'),
(5, 1, 'Giày Sneaker Adidas Ultraboost', 2500000.00, 45, 'https://i.pinimg.com/736x/36/03/5a/36035ace79aa1c228378bea7fa30f0ab.jpg', 'Thoải mái, siêu nhẹ.', '2025-09-06 10:30:13'),
(6, 1, 'Giày Sneaker Converse Classic', 950000.00, 80, 'https://i.pinimg.com/736x/e3/99/15/e399159659cb84702027e8b0138984f5.jpg', 'Phong cách cổ điển.', '2025-09-06 10:30:13'),
(7, 2, 'Giày Nike Air Max', 3200000.00, 25, 'https://i.pinimg.com/736x/b4/3d/36/b43d3619a396c505c992c9fdce65d522.jpg', 'Công nghệ Air êm ái.', '2025-09-06 10:30:13'),
(8, 3, 'Giày Boot Nam Da Bò', 2800000.00, 20, 'https://i.pinimg.com/736x/8b/a8/37/8ba837d341aaef9b179d3114d078cd45.jpg', 'Boot da bò cao cấp.', '2025-09-06 10:30:13'),
(9, 3, 'Giày Boot Nữ Cao Gót', 2100000.00, 35, 'https://i.pinimg.com/1200x/fb/6f/cd/fb6fcda37f4792c355009262429cd962.jpg', 'Boot cao gót sang trọng.', '2025-09-06 10:30:13'),
(10, 2, 'Giày Sneaker Puma RS-X', 1900000.00, 50, 'https://i.pinimg.com/736x/39/62/33/396233cef0045c42fa89facb7e49f806.jpg', 'Thiết kế hiện đại, trẻ trung.', '2025-09-06 10:30:13'),
(11, 1, 'Giày Vans Old Skool', 1200000.00, 70, 'https://i.pinimg.com/736x/76/a1/14/76a114be3b026667d7ac8b58dc747900.jpg', 'Phong cách đường phố.', '2025-09-06 10:30:13'),
(12, 2, 'Giày Sneaker Balenciaga Triple S', 12500000.00, 10, 'https://i.pinimg.com/736x/70/0f/25/700f25c8ba1a85110ff9e4128ed72614.jpg', 'Sneaker cao cấp.', '2025-09-06 10:30:13'),
(13, 3, 'Giày Boot Nữ Da Lộn', 2400000.00, 28, 'https://i.pinimg.com/736x/ba/c1/9f/bac19fc77e47d2a84b71fcf3b9bc4281.jpg', 'Boot da lộn mềm mại.', '2025-09-06 10:30:13'),
(14, 1, 'Giày Sneaker New Balance 574', 1800000.00, 40, 'https://i.pinimg.com/736x/6d/c9/d7/6dc9d7b09727c1a66a377cb124295350.jpg', 'Thoải mái, thời trang.', '2025-09-06 10:30:13'),
(15, 5, 'Balo Thời Trang Nam', 350000.00, 20, 'https://i.pinimg.com/736x/9f/d5/5f/9fd55fb0b505e5a00f9c26f3a6b97dd.jpg', 'Balo nam phong cách Hàn Quốc.', '2025-09-06 14:05:10'),
(16, 5, 'Balo Laptop Chống Nước', 420000.00, 15, 'https://i.pinimg.com/736x/f1/71/32/f17132984129c63a0e8a40863c19b5cf.jpg', 'Chống nước, phù hợp laptop 15 inch.', '2025-09-06 14:05:10'),
(17, 5, 'Balo Nữ Dễ Thương', 280000.00, 30, 'https://i.pinimg.com/736x/64/9b/0d/649b0d90c7db3a2c3b1ec9d8da7bb14a.jpg', 'Phong cách trẻ trung, dễ thương.', '2025-09-06 14:05:10'),
(18, 5, 'Balo Du Lịch Cỡ Lớn', 550000.00, 10, 'https://i.pinimg.com/736x/67/d1/6c/67d16c966c6c0d01a15de3c4baf8b25e.jpg', 'Dung tích 40L, chống thấm.', '2025-09-06 14:05:10'),
(19, 5, 'Balo Mini Nữ', 250000.00, 25, 'https://i.pinimg.com/736x/19/67/ea/1967ea0410b70b96eb72e56a90325c52.jpg', 'Balo nhỏ gọn, hợp đi chơi.', '2025-09-06 14:05:10'),
(20, 5, 'Balo Học Sinh Nữ', 300000.00, 18, 'https://i.pinimg.com/736x/84/87/38/8487384e6e515cdbdcbf7a4ebea6f50a.jpg', 'Thiết kế ngọt ngào, dễ thương.', '2025-09-06 14:05:10'),
(21, 6, 'Charm Mickey', 30000.00, 50, 'https://i.pinimg.com/736x/36/b5/bf/36b5bfbf75f1f3f63e0a4b3d13c0a68e.jpg', 'Charm giày hình Mickey dễ thương.', '2025-09-06 14:05:34'),
(22, 6, 'Charm Doraemon', 35000.00, 60, 'https://i.pinimg.com/736x/8a/f4/ff/8af4ff1b02e41bbf32a178a2c7f2ee85.jpg', 'Charm giày Doraemon ngộ nghĩnh.', '2025-09-06 14:05:34'),
(23, 6, 'Charm Ngôi Sao', 25000.00, 40, 'https://i.pinimg.com/736x/25/91/ab/2591ab0a23c8d4f8acb61bb64f4eeb3d.jpg', 'Charm hình ngôi sao lung linh.', '2025-09-06 14:05:34'),
(24, 6, 'Charm Trái Tim', 30000.00, 55, 'https://i.pinimg.com/736x/6e/26/60/6e26605ee4d07c3b0c4c73dc0eb7d4e5.jpg', 'Charm trái tim cực xinh.', '2025-09-06 14:05:34'),
(25, 6, 'Charm Pikachu', 40000.00, 35, 'https://i.pinimg.com/736x/46/f0/41/46f041a67dd3e3d6b679c3f0a52cb0f4.jpg', 'Charm giày Pikachu đáng yêu.', '2025-09-06 14:05:34'),
(26, 6, 'Charm Hoa Hồng', 35000.00, 45, 'https://i.pinimg.com/736x/38/66/0a/38660af4a0f0d5b010d2c335f3a7a8b1.jpg', 'Charm hình hoa hồng.', '2025-09-06 14:05:34'),
(27, 7, 'Dây Giày Trắng Cổ Điển', 20000.00, 100, 'https://i.pinimg.com/736x/63/86/83/638683178c48aa5e3ff749dadc1d478d.jpg', 'Dây giày trắng phù hợp mọi loại giày.', '2025-09-06 14:05:54'),
(28, 7, 'Dây Giày Phản Quang', 40000.00, 70, 'https://i.pinimg.com/736x/94/5b/71/945b71e4ac8ff9e7070f3f3f1ff153aa.jpg', 'Dây giày phát sáng buổi tối.', '2025-09-06 14:05:54'),
(29, 7, 'Dây Giày Dẹt Đen', 25000.00, 90, 'https://i.pinimg.com/736x/87/4f/ce/874fce458df49c5dcf1329a32cde5f2f.jpg', 'Phong cách streetwear.', '2025-09-06 14:05:54'),
(30, 7, 'Dây Giày Nhiều Màu', 35000.00, 80, 'https://i.pinimg.com/736x/94/9a/f6/949af6f51fa05b2992c24b3d8cc50862.jpg', 'Dây giày phối màu trẻ trung.', '2025-09-06 14:05:54'),
(31, 7, 'Dây Giày Bản To', 30000.00, 60, 'https://i.pinimg.com/736x/4b/18/9f/4b189f8b00af1e6767f37e775e78aaf3.jpg', 'Phong cách hiphop cá tính.', '2025-09-06 14:05:54'),
(32, 7, 'Dây Giày Dây Tròn', 22000.00, 75, 'https://i.pinimg.com/736x/1a/0d/bf/1a0dbf15b827c5f127dcb05e5a48d7a1.jpg', 'Bền, dễ thắt nút.', '2025-09-06 14:05:54'),
(33, 9, 'Gấu Bông Teddy Size L', 350000.00, 50, 'https://i.pinimg.com/736x/0b/6c/19/0b6c19545eaa23476f70875d59c2f15c.jpg', 'Gấu bông Teddy mềm mại, cao 1m2, quà tặng ý nghĩa.', '2025-09-06 14:36:29'),
(34, 9, 'Gấu Bông Pikachu', 250000.00, 80, 'https://i.pinimg.com/736x/29/50/19/2950197dc05d06e3e8d6f6aeb7bdf44b.jpg', 'Gấu bông Pikachu vàng dễ thương, cao 50cm.', '2025-09-06 14:36:29'),
(35, 9, 'Gấu Bông Brown Line', 280000.00, 60, 'https://i.pinimg.com/736x/f8/d6/09/f8d60968b9f1a8bbf5d173e7403817b2.jpg', 'Chú gấu Brown Line Friends cực dễ thương.', '2025-09-06 14:36:29'),
(36, 9, 'Gấu Bông Doraemon', 300000.00, 70, 'https://i.pinimg.com/736x/41/d9/f1/41d9f1bda2e59a3120c4f20ec070a1c2.jpg', 'Gấu bông Doraemon xanh truyền thống.', '2025-09-06 14:36:29'),
(37, 9, 'Gấu Bông Totoro', 320000.00, 40, 'https://i.pinimg.com/736x/36/ff/bb/36ffbbbd70b90d77a76d20e3ef1c3832.jpg', 'Gấu bông Totoro bụng bự đáng yêu, size 80cm.', '2025-09-06 14:36:29'),
(38, 9, 'Gấu Bông Stitch', 260000.00, 55, 'https://i.pinimg.com/736x/9f/9a/2d/9f9a2df9c9a67f4315b59e01e15db293.jpg', 'Gấu bông Stitch xanh dễ thương.', '2025-09-06 14:36:29'),
(39, 10, 'Xịt Khử Mùi Adidas Dynamic Pulse', 150000.00, 100, 'https://i.pinimg.com/736x/5a/0c/6e/5a0c6ef1f98af7e2f51ef80a5e8d5bfa.jpg', 'Hương thơm nam tính, giữ mùi lâu.', '2025-09-06 14:36:39'),
(40, 10, 'Xịt Khử Mùi Axe Apollo', 120000.00, 120, 'https://i.pinimg.com/736x/bc/7e/0d/bc7e0dd2d1b9c8af56953ff65a2d50f2.jpg', 'Mùi hương tươi mát, năng động.', '2025-09-06 14:36:39'),
(41, 10, 'Xịt Khử Mùi Romano Classic', 100000.00, 90, 'https://i.pinimg.com/736x/3c/68/5b/3c685b6828a9de80a4b2a5b8dcf5367f.jpg', 'Phong cách lịch lãm, cuốn hút.', '2025-09-06 14:36:39'),
(42, 10, 'Xịt Khử Mùi Nivea Men Fresh Active', 110000.00, 110, 'https://i.pinimg.com/736x/1f/1e/8b/1f1e8bcaf01f5573f5e58f4f86b3d6b1.jpg', 'Khử mùi suốt 48 giờ, hương mát lạnh.', '2025-09-06 14:36:39'),
(43, 10, 'Xịt Khử Mùi Old Spice Wolfthorn', 130000.00, 70, 'https://i.pinimg.com/736x/47/f8/f2/47f8f2f47f9d13dbf80e253cc43e6e43.jpg', 'Hương thơm độc đáo, mạnh mẽ.', '2025-09-06 14:36:39'),
(44, 10, 'Xịt Khử Mùi Dove Men Care', 140000.00, 95, 'https://i.pinimg.com/736x/0a/17/aa/0a17aa7e62a8b4a6e99a217c47c7c68f.jpg', 'Chăm sóc da, chống mùi hiệu quả.', '2025-09-06 14:36:39'),
(57, 11, 'Giày Tây Da Bóng Đen', 750000.00, 40, 'https://i.pinimg.com/736x/ae/4a/c6/ae4ac6e42b53f58dd8aaea18738a61fd.jpg', 'Giày tây da bóng sang trọng, lịch lãm.', '2025-09-06 14:45:02'),
(58, 11, 'Giày Tây Nâu Da Bò', 720000.00, 35, 'https://i.pinimg.com/736x/9e/54/29/9e5429e3d496fd07e1a05c9d0f0cf7fb.jpg', 'Chất liệu da bò cao cấp, bền đẹp.', '2025-09-06 14:45:02'),
(59, 11, 'Giày Tây Da Lỳ Khâu Tay', 690000.00, 50, 'https://i.pinimg.com/736x/3f/29/bf/3f29bf1d3f8ecfa7a34d53637c2bca4d.jpg', 'Giày da lỳ khâu tay tinh xảo.', '2025-09-06 14:45:02'),
(60, 11, 'Giày Tây Oxford Cổ Điển', 800000.00, 25, 'https://i.pinimg.com/736x/69/60/f9/6960f93b3d91f4a7c453f994d3efb91b.jpg', 'Kiểu dáng Oxford cổ điển.', '2025-09-06 14:45:02'),
(61, 11, 'Giày Tây Lười Trơn', 770000.00, 30, 'https://i.pinimg.com/736x/b2/f7/aa/b2f7aa9c52021e109fe943c75a2341fc.jpg', 'Giày tây lười tiện lợi, thời trang.', '2025-09-06 14:45:02'),
(62, 11, 'Giày Tây Monk Strap', 850000.00, 20, 'https://i.pinimg.com/736x/f4/01/1c/f4011cbbfdbb1d9f0ff6944e4f9f7966.jpg', 'Giày tây Monk Strap 2 khóa cá tính.', '2025-09-06 14:45:02'),
(63, 12, 'Giày Thể Thao Nike Air Force 1', 2200000.00, 60, 'https://i.pinimg.com/736x/f1/43/6a/f1436a2d31c2b8a26a926a8297c25a5c.jpg', 'Nike AF1 trắng huyền thoại.', '2025-09-06 14:45:14'),
(64, 12, 'Giày Adidas Ultraboost 22', 3500000.00, 45, 'https://i.pinimg.com/736x/63/2c/c7/632cc7d27f07b89b0cc16253f4b89671.jpg', 'Đệm boost êm ái, chạy bộ cực tốt.', '2025-09-06 14:45:14'),
(65, 12, 'Giày Converse Chuck Taylor', 1500000.00, 80, 'https://i.pinimg.com/736x/7d/18/b1/7d18b193df7c4058fa1f653d8146e10f.jpg', 'Converse cổ cao classic.', '2025-09-06 14:45:14'),
(66, 12, 'Giày Vans Old Skool Đen Trắng', 1400000.00, 70, 'https://i.pinimg.com/736x/41/61/cc/4161cc49d444b7a305f7a2c64764d41a.jpg', 'Vans Old Skool đen trắng cá tính.', '2025-09-06 14:45:14'),
(67, 12, 'Giày Puma RS-X Multi', 2100000.00, 50, 'https://i.pinimg.com/736x/18/8e/9b/188e9b445524ac0f26cb249fcf8a1723.jpg', 'Puma RS-X phối màu độc đáo.', '2025-09-06 14:45:14'),
(68, 12, 'Giày New Balance 574', 1800000.00, 55, 'https://i.pinimg.com/736x/f5/7a/ee/f57aee2f43a2f6ed8d4c5446b6a75821.jpg', 'New Balance 574 cổ điển.', '2025-09-06 14:45:14'),
(69, 13, 'Dép Adidas Adilette', 600000.00, 90, 'https://i.pinimg.com/736x/7d/8b/37/7d8b3735b81d3f1a9a7f51f89369a776.jpg', 'Dép Adidas sọc kinh điển.', '2025-09-06 14:45:24'),
(70, 13, 'Dép Nike Benassi Just Do It', 550000.00, 100, 'https://i.pinimg.com/736x/7b/90/cd/7b90cd7eb8d7f2288c62e00c0af2c9f7.jpg', 'Dép Nike Benassi mềm mại.', '2025-09-06 14:45:24'),
(71, 13, 'Dép Lào Xỏ Ngón Đen', 120000.00, 150, 'https://i.pinimg.com/736x/83/0b/26/830b26af07e60a9f6474d8a4ef16a58a.jpg', 'Dép xỏ ngón đen đơn giản.', '2025-09-06 14:45:24'),
(72, 13, 'Dép Crocs Clog Trắng', 900000.00, 60, 'https://i.pinimg.com/736x/4f/71/2c/4f712cd187b82dbdf9c118ecedfda2a3.jpg', 'Dép Crocs thoáng khí, êm ái.', '2025-09-06 14:45:24'),
(73, 13, 'Dép Quai Hậu Nam Thể Thao', 350000.00, 70, 'https://i.pinimg.com/736x/50/6d/46/506d46823d258348de22d4b95a25f962.jpg', 'Dép quai hậu chắc chắn, thoải mái.', '2025-09-06 14:45:24'),
(74, 13, 'Dép Da Nam Nâu Cao Cấp', 480000.00, 40, 'https://i.pinimg.com/736x/f8/f4/4f/f8f44f788e408ea465fbba1d35c3c98e.jpg', 'Dép da bò cao cấp, bền đẹp.', '2025-09-06 14:45:24'),
(75, 14, 'Giày cao gót đen basic', 450000.00, 20, 'https://images.unsplash.com/photo-1580745297110-46dba27a1c03', NULL, '2025-09-08 02:36:50'),
(76, 14, 'Giày cao gót mũi nhọn', 520000.00, 15, 'https://images.unsplash.com/photo-1596464716123-69a77c2c6c87', NULL, '2025-09-08 02:36:50'),
(77, 14, 'Giày cao gót quai mảnh', 480000.00, 18, 'https://images.unsplash.com/photo-1596464716142-c7d3dfec1e93', NULL, '2025-09-08 02:36:50'),
(78, 14, 'Giày cao gót da bóng', 550000.00, 12, 'https://images.unsplash.com/photo-1593032457869-9b54a7d81f58', NULL, '2025-09-08 02:36:50'),
(79, 14, 'Giày cao gót công sở', 500000.00, 20, 'https://images.unsplash.com/photo-1580745286939-6f28d9dc5e50', NULL, '2025-09-08 02:36:50'),
(80, 14, 'Giày cao gót nơ xinh', 530000.00, 16, 'https://images.unsplash.com/photo-1579338550910-21d2b6f0c1c7', NULL, '2025-09-08 02:36:50'),
(81, 15, 'Giày thể thao trắng classic', 600000.00, 25, 'https://images.unsplash.com/photo-1528701800489-20be7c6e1d3a', NULL, '2025-09-08 02:36:50'),
(82, 15, 'Giày sneaker đen basic', 620000.00, 22, 'https://images.unsplash.com/photo-1521334884684-d80222895322', NULL, '2025-09-08 02:36:50'),
(83, 15, 'Giày chạy bộ chuyên dụng', 750000.00, 18, 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f', NULL, '2025-09-08 02:36:50'),
(84, 15, 'Giày thể thao nữ hồng pastel', 640000.00, 15, 'https://images.unsplash.com/photo-1528701800489-20be7c6e1d3a', NULL, '2025-09-08 02:36:50'),
(85, 15, 'Giày sneaker nam hiện đại', 680000.00, 20, 'https://images.unsplash.com/photo-1582582428600-4a71d0dbf3a6', NULL, '2025-09-08 02:36:50'),
(86, 15, 'Giày thể thao cao cổ', 700000.00, 10, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff', NULL, '2025-09-08 02:36:50'),
(87, 16, 'Giày chạy bộ siêu nhẹ', 850000.00, 30, 'https://images.unsplash.com/photo-1595950658284-7a18c458d09e', NULL, '2025-09-08 02:36:50'),
(88, 16, 'Giày chạy bộ đế êm', 900000.00, 25, 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f', NULL, '2025-09-08 02:36:50'),
(89, 16, 'Giày đi bộ casual', 780000.00, 28, 'https://images.unsplash.com/photo-1582582428600-4a71d0dbf3a6', NULL, '2025-09-08 02:36:50'),
(90, 16, 'Giày running thoáng khí', 950000.00, 20, 'https://images.unsplash.com/photo-1600181958231-cf1f6a640f3a', NULL, '2025-09-08 02:36:50'),
(91, 16, 'Giày đi bộ dã ngoại', 820000.00, 15, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff', NULL, '2025-09-08 02:36:50'),
(92, 16, 'Giày chạy bộ chuyên nghiệp', 990000.00, 12, 'https://images.unsplash.com/photo-1519744792095-2f2205e87b6f', NULL, '2025-09-08 02:36:50'),
(93, 17, 'Giày búp bê đen basic', 400000.00, 20, 'https://images.unsplash.com/photo-1606813902916-5c9a4901c9a9', NULL, '2025-09-08 02:36:50'),
(94, 17, 'Giày búp bê nơ hồng', 420000.00, 18, 'https://images.unsplash.com/photo-1606813902434-7bde95c3c3f0', NULL, '2025-09-08 02:36:50'),
(95, 17, 'Giày búp bê da mềm', 450000.00, 15, 'https://images.unsplash.com/photo-1606813902930-67c9ad9c19f5', NULL, '2025-09-08 02:36:50'),
(96, 17, 'Giày búp bê công chúa', 470000.00, 12, 'https://images.unsplash.com/photo-1606813902368-8f2a6b8f8c69', NULL, '2025-09-08 02:36:50'),
(97, 17, 'Giày búp bê thời trang', 430000.00, 22, 'https://images.unsplash.com/photo-1606813902299-8cfa859504a5', NULL, '2025-09-08 02:36:50'),
(98, 17, 'Giày búp bê cao cấp', 490000.00, 10, 'https://images.unsplash.com/photo-1606813902910-7d7b4b87b84a', NULL, '2025-09-08 02:36:50'),
(99, 18, 'Túi xách công sở da PU', 650000.00, 20, 'https://images.unsplash.com/photo-1522336572468-97b06e8ef143', NULL, '2025-09-08 02:36:50'),
(100, 18, 'Túi đeo chéo mini', 480000.00, 25, 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f', NULL, '2025-09-08 02:36:50'),
(101, 18, 'Túi tote canvas basic', 350000.00, 30, 'https://images.unsplash.com/photo-1589998059171-988d887df646', NULL, '2025-09-08 02:36:50'),
(102, 18, 'Ví nữ cầm tay sang trọng', 300000.00, 22, 'https://images.unsplash.com/photo-1522336572468-97b06e8ef143', NULL, '2025-09-08 02:36:50'),
(103, 18, 'Ví nam da bò cao cấp', 550000.00, 18, 'https://images.unsplash.com/photo-1589998059171-988d887df646', NULL, '2025-09-08 02:36:50'),
(104, 18, 'Túi xách thời trang nữ', 700000.00, 15, 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f', NULL, '2025-09-08 02:36:50'),
(105, 18, 'Ví cầm tay unisex', 320000.00, 25, 'https://images.unsplash.com/photo-1589998059171-988d887df646', NULL, '2025-09-08 02:36:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `day_of_birth` date DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` int(11) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `day_of_birth`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, '', NULL, 'admin@shop.com', '123456', 0, 'admin', '2025-09-03 07:18:39'),
(2, 'Long l', NULL, 'admin@gmail.com', '1', 0, 'user', '2025-09-03 07:19:33'),
(3, 'langcoc@gmail.com', '2025-09-04', 'langcoc@gmail.com', '1', 0, 'user', '2025-09-03 09:43:15'),
(4, 'lăng cọc 1', '2025-09-13', 'langcoc1@gmail.com', '1', 0, 'user', '2025-09-03 09:45:08'),
(5, 'lăng cọc 2', '2025-09-19', 'langcoc2@gmail.com', '1', 0, 'user', '2025-09-03 09:46:49');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
