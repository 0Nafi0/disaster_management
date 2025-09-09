<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $need_id = intval($_GET['id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get need details to check if any quantity was fulfilled
        $need_sql = "SELECT fulfilled_quantity FROM needs WHERE id = ?";
        $need_stmt = $conn->prepare($need_sql);
        $need_stmt->bind_param("i", $need_id);
        $need_stmt->execute();
        $need = $need_stmt->get_result()->fetch_assoc();

        if (!$need) {
            throw new Exception("Resource need not found");
        }

        // Delete the need
        $delete_sql = "DELETE FROM needs WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $need_id);
        
        if (!$delete_stmt->execute()) {
            throw new Exception("Error deleting resource need");
        }

        $conn->commit();
        header("Location: manage_victim_needs.php?success=Resource need deleted successfully");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_victim_needs.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: manage_victim_needs.php?error=Invalid request");
exit;
