<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $assignment_id = intval($_GET['id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete the assignment
        $delete_sql = "DELETE FROM Volunteer_Victim_Help WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $assignment_id);
        
        if ($delete_stmt->execute()) {
            $conn->commit();
            header("Location: assign_help.php?success=Help assignment removed successfully");
            exit;
        } else {
            throw new Exception("Error removing help assignment");
        }

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: assign_help.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: assign_help.php?error=Invalid request");
exit;
