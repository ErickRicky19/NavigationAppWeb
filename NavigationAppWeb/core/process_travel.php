<?php
include("connect.php");

if (isset($_POST['createTravel'])) {

$userId =  mysqli_real_escape_string($conn, $_POST['userId']);
$route=  mysqli_real_escape_string($conn, $_POST['route']);
$mode = mysqli_real_escape_string($conn, $_POST['mode']);
$origin = mysqli_real_escape_string($conn, $_POST['origin']);
$destination = mysqli_real_escape_string($conn, $_POST['destination']);
$distance = mysqli_real_escape_string($conn, $_POST['distance']);
$duration = mysqli_real_escape_string($conn, $_POST['duration']);
$createdOn =  date("Y-m-d H:i:s");
$sql = "INSERT INTO Travel (UserId, Route, Mode, Origin, Destination, Distance, Duration, CreatedOn) 
VALUES ('$userId', '$route', '$mode', '$origin', '$destination', '$distance', '$duration', '$createdOn')";

if (mysqli_query($conn, $sql))
{
        session_start();
        $_SESSION['createTravel'] = "New Travel record created successfully";
        
}
else {
    die("Error: " . $sql . "<br>" . mysqli_error($conn));
}
}

?>
