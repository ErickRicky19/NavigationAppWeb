<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

session_start();
include(__DIR__ . "/connect.php"); 

if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}
$user_id = $_SESSION['login'];

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['origin'], $input['destination'], $input['mode'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$origin = trim($input['origin']);
$destination = trim($input['destination']);
$mode = trim($input['mode']); // driving-car, foot-walking, cycling-regular

function geocode($place){
    $url = "https://nominatim.openstreetmap.org/search?q=".urlencode($place)."&format=json&limit=1";
    $opts = ["http"=>["header"=>"User-Agent: NavigationApp/1.0 (test@example.com)\r\n"]];
    $context = stream_context_create($opts);
    $resp = @file_get_contents($url, false, $context);
    if (!$resp) return [0,0];
    $data = json_decode($resp, true);
    if (!$data || count($data) == 0) return [0,0];
    return [(float)$data[0]['lat'], (float)$data[0]['lon']];
}

function nearest_routable($coord, $mode, $apiKey){
    $url = "https://api.openrouteservice.org/nearest/$mode";
    $postData = ["locations" => [ [$coord[1], $coord[0]] ]]; // lon, lat
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $resp = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($resp, true);
    if(isset($res['features'][0]['geometry']['coordinates'])){
        return [
            $res['features'][0]['geometry']['coordinates'][1], // lat
            $res['features'][0]['geometry']['coordinates'][0]  // lon
        ];
    }
    return $coord; // fallback
}

$start = geocode($origin);
$end = geocode($destination);

if ($start === [0,0] || $end === [0,0]) {
    http_response_code(400);
    echo json_encode(['error'=>'No se pudo geocodificar origen o destino']);
    exit;
}

$apiKey = 'eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImFhNTI5MzQ1MTM0ZDQwODBiNjdjYmMwNWFmM2VjMzJiIiwiaCI6Im11cm11cjY0In0='; // Reemplaza por tu clave

$start = nearest_routable($start, 'foot-walking', $apiKey);
$end   = nearest_routable($end, 'foot-walking', $apiKey);

function getRoute($start, $end, $mode, $apiKey){
    $url = "https://api.openrouteservice.org/v2/directions/$mode/geojson";
    $postData = [
        "coordinates" => [
            [$start[1], $start[0]],
            [$end[1], $end[0]]
        ]
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: $apiKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $resp = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($http_code === 200) return json_decode($resp, true);
    return false;
}

$response = getRoute($start, $end, $mode, $apiKey);

if (!$response && $mode !== 'foot-walking') {
    $response = getRoute($start, $end, 'foot-walking', $apiKey);
    $mode = 'foot-walking';
}

if (!$response) {
    echo json_encode(['error'=>'No se pudo calcular la ruta con ningún modo']);
    exit;
}

$segment = $response['features'][0]['properties']['segments'][0] ?? null;
if (!$segment) { echo json_encode(['error'=>'No se pudo calcular la ruta']); exit; }

$distance = $segment['distance']/1000; // km
$duration = round($segment['duration']/60); // min
$route = $response['features'][0]['geometry'];
$steps = $segment['steps'] ?? [];

$stmt = $conn->prepare("INSERT INTO Trip (UserId, Origin, Destination, Mode, Distance, Duration, Route, Step) VALUES (?,?,?,?,?,?,?,?)");
$route_json = json_encode($route);
$steps_json = json_encode($steps, JSON_UNESCAPED_UNICODE);
error_log("Steps JSON: " . $steps_json);

$stmt->bind_param("isssddss", $user_id, $origin, $destination, $mode, $distance, $duration, $route_json, $steps_json);
if (!$stmt->execute()) {
    echo json_encode(['error'=>'Error MySQL: '.$stmt->error]);
    exit;
}

$stmt->close();
$conn->close();

echo json_encode([
    'Distance' => $distance,
    'Duration' => $duration,
    'Route' => $route,
    'Steps' => $steps,
    'OriginCoords' => $start,
    'DestinationCoords' => $end,
    'ModeUsed' => $mode
]);
?>
