<?php
require_once('../includes/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $disaster_id = mysqli_real_escape_string($conn, $_POST['disaster_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update report
    $query = "UPDATE report SET type = ?, location = ?, disaster_id = ?, status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $type, $location, $disaster_id, $status, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: reports.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

header("Location: reports.php");
exit();
