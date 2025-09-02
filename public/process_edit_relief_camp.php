<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $capacity = intval($_POST['capacity']);
    $disaster_id = intval($_POST['disaster_id']);
    
    // Validate inputs
    if (empty($name) || $capacity <= 0 || $disaster_id <= 0) {
        header("Location: edit_relief_camp.php?id=$id&error=Invalid input");
        exit;
    }
    
    // Prepare SQL update query
    $sql = "UPDATE Relief_Camp SET name = ?, capacity = ?, disaster_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $name, $capacity, $disaster_id, $id);
    
    if ($stmt->execute()) {
        header("Location: relief_camps.php?msg=Camp+updated+successfully");
        exit;
    } else {
        header("Location: edit_relief_camp.php?id=$id&error=Update failed");
        exit;
    }
} else {
    header("Location: relief_camps.php");
    exit;
}

$stmt->close();
$conn->close();
?>
