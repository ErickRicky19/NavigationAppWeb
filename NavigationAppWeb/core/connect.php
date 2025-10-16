<?php
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "TravelAppWeb";

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong: " . mysqli_connect_error());
}



