<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ScheduleDB";
$conn = '';

// Connect to the database
try {
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
} catch (mysqli_sql_exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>