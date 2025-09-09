<?php
require_once('../includes/db_connect.php');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Delete volunteer-report relationships first
        $delete_reports = "DELETE FROM reports WHERE report_id = ?";
        $stmt = mysqli_prepare($conn, $delete_reports);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        // Delete the report
        $delete_report = "DELETE FROM report WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_report);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

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
