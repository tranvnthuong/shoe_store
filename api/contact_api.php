 <?php
    include("../configs/db.php");
    session_start();

    header('Content-Type: application/json');

    // CSRF token check
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        echo json_encode([
            "status" => "error",
            "msg" => "CSRF token không hợp lệ!",
            "isToast" => true
        ]);
        exit;
    }

    $name    = trim($_POST['full_name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        echo json_encode([
            "status" => "warning",
            "title" => "",
            "msg" => "Vui lòng điền đầy đủ thông tin bắt buộc.",
            "isToast" => true
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "status" => "warning",
            "title" => "",
            "msg" => "Email không hợp lệ.",
            "isToast" => true
        ]);
        exit;
    }

    if ($phone && !preg_match("/^[0-9]{9,11}$/", $phone)) {
        echo json_encode([
            "status" => "warning",
            "title" => "",
            "msg" => "Số điện thoại không hợp lệ (9-11 số).",
            "isToast" => true
        ]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "msg" => "Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm.",
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "msg" => "Lỗi khi gửi liên hệ. Vui lòng thử lại!",
        ]);
    }
