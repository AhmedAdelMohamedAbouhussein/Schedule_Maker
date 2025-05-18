<?php
include("../Connect_DataBase.php");

function hashPin($pin) {
    return password_hash((string)$pin, PASSWORD_DEFAULT);
}

$admins = [
    [999000001, '123456'],
    [999000002, '654321']
];

foreach ($admins as $admin) 
{
    $admin_id = $admin[0];
    $pin = $admin[1];
    $hashed_pin = hashPin($pin);

    $sql = "INSERT INTO Admin (Admin_ID, PIN) VALUES ('$admin_id', '$hashed_pin')";

    if (mysqli_query($conn, $sql)) {
        echo "Inserted admin with ID $admin_id successfully.<br>";
    } else {
        echo "Error inserting admin $admin_id: " . mysqli_error($conn) . "<br>";
    }
}

mysqli_close($conn);
?>
