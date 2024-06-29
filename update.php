<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test12";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['led']) && isset($_GET['status'])) {
  $led = $_GET['led'];
  $status = $_GET['status'];
  
  $sql = "UPDATE led_status SET $led = $status WHERE id = 1";
  
  if ($conn->query($sql) === TRUE) {
    echo json_encode(array("message" => "Record updated successfully"));
  } else {
    echo json_encode(array("message" => "Error updating record: " . $conn->error));
  }
}

if (isset($_GET['temperature']) && isset($_GET['humidity'])) {
  $temperature = $_GET['temperature'];
  $humidity = $_GET['humidity'];
  
  $sql = "INSERT INTO sensordata (temperature, humidity) VALUES ($temperature, $humidity)";
  
  if ($conn->query($sql) === TRUE) {
    echo json_encode(array("message" => "DHT data inserted successfully"));
  } else {
    echo json_encode(array("message" => "Error inserting data: " . $conn->error));
  }
}

$conn->close();
?>
