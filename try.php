<?php 
$servername = "localhost";
$username = "pammanet_hahalol";
$password = "yougotmedude";
$db = "pammanet_rcsef";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO setting (setname, setval, uuser)
VALUES ('datenow', date('Y-m-d H:i:s'), 'org')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>