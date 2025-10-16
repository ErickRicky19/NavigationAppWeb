<?php
session_start();
include(__DIR__ . "/../core/connect.php");

    if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
    }

    $userId = mysqli_real_escape_string($conn, $_GET['userId']);

    $sql = "DELETE FROM Trip WHERE UserId = '$userId'";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../user_view/mapPage.php");
        exit;
    } else {
        echo "Error deleting trips: " . mysqli_error($conn);
    }
    mysqli_close($conn);
?>
