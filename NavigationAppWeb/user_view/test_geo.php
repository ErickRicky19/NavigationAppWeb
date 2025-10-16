<?php
$apiKey = "eyJvcmciOiI1YjNjZTM1OTc4NTExMTAwMDFjZjYyNDgiLCJpZCI6ImFhNTI5MzQ1MTM0ZDQwODBiNjdjYmMwNWFmM2VjMzJiIiwiaCI6Im11cm11cjY0In0=";
$mode = "driving-car";
$start = [ -86.9679698, 12.3021275 ]; // lon, lat
$end   = [ -86.2500000, 12.1328000 ]; // lon, lat

$ors_url = "https://api.openrouteservice.org/v2/directions/$mode/geojson";

$data = [
    "coordinates" => [$start, $end]
];

$ch = curl_init($ors_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $apiKey",
    "Content-Type: application/json",
    "Accept: application/geo+json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response_json = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo "❌ Error: ORS HTTP code: $http_code<br>";
    echo $response_json;
} else {
    echo "✅ Ruta calculada correctamente:<br>";
    echo $response_json;
}
?>

