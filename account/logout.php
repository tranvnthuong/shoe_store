<?php
session_start();
unset($_SESSION['username']); // xóa session username
header("Location: ../account/login.php");
?>