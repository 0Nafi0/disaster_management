<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete the volunteer
        $sql = "DELETE FROM Volunteer WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: volunteers.php?msg=Volunteer+deleted+successfully");
            exit;
        } else {
            throw new Exception("Error deleting volunteer");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: volunteers.php?msg=Error:+" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: volunteers.php?msg=Invalid+request");
    exit;
}

$conn->close();
?>
