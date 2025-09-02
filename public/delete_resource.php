<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Delete the resource
        $sql = "DELETE FROM Resource WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: resources.php?msg=Resource+deleted+successfully");
            exit;
        } else {
            throw new Exception("Error deleting resource");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: resources.php?msg=Error:+" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: resources.php?msg=Invalid+request");
    exit;
}

$conn->close();
?>
