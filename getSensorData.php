<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test12";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve latest temperature and humidity data
$sensorDataSql = "SELECT * FROM SensorData ORDER BY reg_date DESC LIMIT 1";
$sensorDataResult = $conn->query($sensorDataSql);
$sensorData = $sensorDataResult->fetch_assoc();

if ($sensorData) {
    echo json_encode([
        'temperature' => $sensorData['temperature'],
        'humidity' => $sensorData['humidity']
    ]);
} else {
    echo json_encode([
        'temperature' => null,
        'humidity' => null
    ]);
}

$conn->close();
?>
