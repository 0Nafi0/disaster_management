<?php
require_once '../includes/db_connect.php';

if (isset($_GET['camp_id']) && isset($_GET['resource_id'])) {
    $camp_id = intval($_GET['camp_id']);
    $resource_id = intval($_GET['resource_id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get the quantity that was assigned
        $get_qty_sql = "SELECT quantity FROM Camp_Resource 
                        WHERE camp_id = ? AND resource_id = ?";
        $get_qty_stmt = $conn->prepare($get_qty_sql);
        $get_qty_stmt->bind_param("ii", $camp_id, $resource_id);
        $get_qty_stmt->execute();
        $result = $get_qty_stmt->get_result();
        $assignment = $result->fetch_assoc();

        if ($assignment) {
            // Return the quantity to the resource
            $return_sql = "UPDATE Resource 
                          SET quantity = quantity + ? 
                          WHERE id = ?";
            $return_stmt = $conn->prepare($return_sql);
            $return_stmt->bind_param("ii", $assignment['quantity'], $resource_id);
            $return_stmt->execute();

            // Delete the assignment
            $delete_sql = "DELETE FROM Camp_Resource 
                          WHERE camp_id = ? AND resource_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("ii", $camp_id, $resource_id);
            $delete_stmt->execute();

            $conn->commit();
            header("Location: assign_resource.php?success=Resource assignment removed successfully");
            exit;
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: assign_resource.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: assign_resource.php?error=Invalid request");
exit;
