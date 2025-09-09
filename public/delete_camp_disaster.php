<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $record_id = intval($_GET['id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete the record
        $delete_sql = "DELETE FROM records WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $record_id);
        
        if (!$delete_stmt->execute()) {
            throw new Exception("Error deleting record");
        }

        $conn->commit();
        header("Location: manage_camp_disasters.php?success=Record deleted successfully");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_camp_disasters.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: manage_camp_disasters.php?error=Invalid request");
exit;
