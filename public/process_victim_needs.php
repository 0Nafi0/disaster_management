<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $victim_id = isset($_POST['victim_id']) ? intval($_POST['victim_id']) : 0;
    $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if (!$victim_id || !$resource_id || $quantity <= 0) {
        header("Location: manage_victim_needs.php?error=Please fill all fields with valid values");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if victim and resource exist
        $check_sql = "SELECT 
            (SELECT COUNT(*) FROM Victim WHERE id = ?) as victim_exists,
            (SELECT COUNT(*) FROM Resource WHERE id = ?) as resource_exists,
            (SELECT quantity FROM Resource WHERE id = ?) as available_quantity";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("iii", $victim_id, $resource_id, $resource_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $check = $result->fetch_assoc();

        if (!$check['victim_exists'] || !$check['resource_exists']) {
            throw new Exception("Invalid victim or resource selected");
        }

        // Check if there's an existing unfulfilled need for this victim and resource
        $existing_sql = "SELECT id, quantity, fulfilled_quantity FROM needs 
                        WHERE victim_id = ? AND resource_id = ? AND status != 'fulfilled'";
        $existing_stmt = $conn->prepare($existing_sql);
        $existing_stmt->bind_param("ii", $victim_id, $resource_id);
        $existing_stmt->execute();
        $existing_result = $existing_stmt->get_result();

        if ($existing_result->num_rows > 0) {
            $existing = $existing_result->fetch_assoc();
            $new_quantity = $existing['quantity'] + $quantity;
            
            // Update existing need
            $update_sql = "UPDATE needs SET quantity = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $new_quantity, $existing['id']);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Error updating existing need");
            }
            
            $message = "Updated existing resource need";
        } else {
            // Create new need
            $insert_sql = "INSERT INTO needs (victim_id, resource_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iii", $victim_id, $resource_id, $quantity);
            
            if (!$insert_stmt->execute()) {
                throw new Exception("Error creating new resource need");
            }
            
            $message = "Created new resource need";
        }

        $conn->commit();
        header("Location: manage_victim_needs.php?success=" . urlencode($message));
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_victim_needs.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: manage_victim_needs.php");
exit;
