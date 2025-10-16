<?php
session_start();
include(__DIR__ . "/connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT Id, Password FROM User WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['Password'])) {
            $_SESSION['login'] = $row['Id'];

            $userId = $row['Id'];
            $delete = $conn->prepare("DELETE FROM Trip WHERE UserId = ? AND DATE(CreatedOn) < CURDATE()");
            $delete->bind_param("i", $userId);
            $delete->execute();

            header("Location: ../user_view/mapPage.php");
            exit;
        } else {
            $_SESSION['error'] = "Usuario o contraseña incorrectos.";
            header("Location: ../user_view/loginPage.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Usuario o contraseña incorrectos.";
        header("Location: ../user_view/loginPage.php");
        exit;
    }
}
?>
