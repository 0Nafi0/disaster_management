<?php
require_once('../includes/db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $disaster_id = mysqli_real_escape_string($conn, $_POST['disaster_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $volunteers = $_POST['volunteers'];

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert into report table
        $query = "INSERT INTO report (type, location, disaster_id, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssss", $type, $location, $disaster_id, $status);
        mysqli_stmt_execute($stmt);

        $report_id = mysqli_insert_id($conn);

        // Insert volunteer assignments
        $volunteer_query = "INSERT INTO reports (report_id, volunteer_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $volunteer_query);

        foreach ($volunteers as $volunteer_id) {
            mysqli_stmt_bind_param($stmt, "ii", $report_id, $volunteer_id);
            mysqli_stmt_execute($stmt);
        }

        // Commit transaction
        mysqli_commit($conn);
        header("Location: reports.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}

header("Location: reports.php");
exit();
