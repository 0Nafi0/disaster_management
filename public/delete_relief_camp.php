<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input

    // First check if there are any victims in this camp
    $check_sql = "SELECT COUNT(*) as victim_count FROM victim WHERE camp_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['victim_count'] > 0) {
        // There are victims in this camp
        header("Location: relief_camps.php?msg=Cannot+delete+camp:+There+are+" . $row['victim_count'] . "+victims+currently+assigned+to+this+camp");
        $check_stmt->close();
        exit;
    }

    $check_stmt->close();

    // If no victims are found, proceed with deletion
    $sql = "DELETE FROM Relief_Camp WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back with success
        header("Location: relief_camps.php?msg=Camp+deleted+successfully");
        $stmt->close();
        exit;
    } else {
        header("Location: relief_camps.php?msg=Error+deleting+camp:+" . urlencode($conn->error));
        $stmt->close();
        exit;
    }
} else {
    header("Location: relief_camps.php?msg=Invalid+request");
    exit;
}

$conn->close();
?>
