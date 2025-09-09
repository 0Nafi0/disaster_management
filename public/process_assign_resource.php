<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $camp_id = isset($_POST['camp_id']) ? intval($_POST['camp_id']) : 0;
    $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if (!$camp_id || !$resource_id || $quantity <= 0) {
        header("Location: assign_resource.php?error=Invalid input data");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if resource has enough quantity available
        $check_sql = "SELECT quantity FROM Resource WHERE id = ? FOR UPDATE";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $resource_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $resource = $result->fetch_assoc();

        if (!$resource || $resource['quantity'] < $quantity) {
            throw new Exception("Insufficient resource quantity available");
        }

        // Check if assignment already exists
        $existing_sql = "SELECT quantity FROM Camp_Resource WHERE camp_id = ? AND resource_id = ?";
        $existing_stmt = $conn->prepare($existing_sql);
        $existing_stmt->bind_param("ii", $camp_id, $resource_id);
        $existing_stmt->execute();
        $existing_result = $existing_stmt->get_result();

        if ($existing_result->num_rows > 0) {
            // Update existing assignment
            $update_sql = "UPDATE Camp_Resource SET quantity = quantity + ? 
                          WHERE camp_id = ? AND resource_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("iii", $quantity, $camp_id, $resource_id);
            $update_stmt->execute();
        } else {
            // Create new assignment
            $insert_sql = "INSERT INTO Camp_Resource (camp_id, resource_id, quantity) 
                          VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iii", $camp_id, $resource_id, $quantity);
            $insert_stmt->execute();
        }

        // Update resource quantity
        $update_resource_sql = "UPDATE Resource SET quantity = quantity - ? WHERE id = ?";
        $update_resource_stmt = $conn->prepare($update_resource_sql);
        $update_resource_stmt->bind_param("ii", $quantity, $resource_id);
        $update_resource_stmt->execute();

        // Commit transaction
        $conn->commit();
        header("Location: assign_resource.php?success=Resource assigned successfully");
        exit;

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        header("Location: assign_resource.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: assign_resource.php");
exit;
