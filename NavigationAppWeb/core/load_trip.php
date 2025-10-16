<?php
header('Content-Type: application/json');
session_start();
include(__DIR__ . "/connect.php");

if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}
$user_id = $_SESSION['login'];

try {
    $stmt = $conn->prepare("SELECT Origin, Destination, Mode, Route, Step FROM Trip WHERE UserId = ? ORDER BY Id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trips = [];
    while ($row = $result->fetch_assoc()) {
        $trips[] = $row;
    }
    echo json_encode($trips);
} catch(Exception $e) {
    echo json_encode(['error'=>'Error al cargar viajes: '.$e->getMessage()]);
}
