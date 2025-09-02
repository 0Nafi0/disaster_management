<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $type = !empty($_POST['type']) ? trim($_POST['type']) : null;
    $location = !empty($_POST['location']) ? trim($_POST['location']) : null;
    
    // Validate required fields
    if (empty($name)) {
        header("Location: edit_disaster.php?id=$id&msg=Disaster+name+is+required");
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update the disaster
        $sql = "UPDATE Disaster SET name = ?, type = ?, location = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $type, $location, $id);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: index.php?msg=Disaster+updated+successfully");
            exit;
        } else {
            throw new Exception("Error updating disaster");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: edit_disaster.php?id=$id&msg=Error:+" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

$stmt->close();
$conn->close();
?>
