<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("connect.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['createUser'])) {
    $userName = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO `User` (Username, Password) VALUES (?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $userName, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['create'] = "New user created successfully";
        header("Location: ../user_view/loginPage.php");
        exit;
    } else {
        die("Error creating user: " . $stmt->error);
    }
}
?>
