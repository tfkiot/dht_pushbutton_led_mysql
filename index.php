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

// Handle AJAX request to get LED status
if (isset($_GET['getLedStatus'])) {
    $sql = "SELECT * FROM led_status WHERE id=1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo json_encode($row);
    exit;
}

// Handle AJAX request
if (isset($_POST['led']) && isset($_POST['status'])) {
    $led = $_POST['led'];
    $status = $_POST['status'];

    $sql = "UPDATE led_status SET $led=$status WHERE id=1";
    $conn->query($sql);
    exit;
}

// Retrieve LED status
$sql = "SELECT * FROM led_status WHERE id=1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Page Title</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='main.css'>
    <script src='main.js'></script>
</head>

<body>
    <div class="cardSection">
        <h1 class="cardlable1">ESP_MySQL Dashboard</h1>
        <div class="container">
            <div class="cardItems">
                <h1 class="cardlable2">LED Control</h1>
                <!-- toggle-switch -->
                <label class="toggle-switch">
                    <input type="checkbox" name="led1" <?php if ($row['led1']) echo 'checked'; ?> onchange="handleToggle(event)">
                    <span class="slider"></span>
                    <span class="label-text">LED&nbsp1</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="led2" <?php if ($row['led2']) echo 'checked'; ?> onchange="handleToggle(event)">
                    <span class="slider"></span>
                    <span class="label-text">LED&nbsp2</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="led3" <?php if ($row['led3']) echo 'checked'; ?> onchange="handleToggle(event)">
                    <span class="slider"></span>
                    <span class="label-text">LED&nbsp3</span>
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" name="led4" <?php if ($row['led4']) echo 'checked'; ?> onchange="handleToggle(event)">
                    <span class="slider"></span>
                    <span class="label-text">LED&nbsp4</span>
                </label>
            </div>
            <div class="cardItems">
                <h3 style="padding: 20px 0px;">DHT11 Data</h3>
                <div class="gaugeItems">
                    <div class="gauge">
                        <div class="gauge_body">
                            <div class="gauge_fill"></div>
                            <div class="gauge_cover"></div>
                        </div>
                        <div class="gauge_text">TEMPERATURE</div>
                    </div>
                    <div class="gauge">
                        <div class="gauge_body">
                            <div class="gauge_fill"></div>
                            <div class="gauge_cover"></div>
                        </div>
                        <div class="gauge_text">HUMIDITY</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>

</body>


</html>
