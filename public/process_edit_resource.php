<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $unit = !empty($_POST['unit']) ? trim($_POST['unit']) : null;
    
    // Validate inputs
    if (empty($name)) {
        header("Location: edit_resource.php?id=$id&msg=Resource+name+is+required");
        exit;
    }
    
    if ($quantity < 0) {
        header("Location: edit_resource.php?id=$id&msg=Quantity+cannot+be+negative");
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update the resource
        $sql = "UPDATE Resource SET name = ?, quantity = ?, unit = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $name, $quantity, $unit, $id);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: resources.php?msg=Resource+updated+successfully");
            exit;
        } else {
            throw new Exception("Error updating resource");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: edit_resource.php?id=$id&msg=Error:+" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: resources.php");
    exit;
}

$conn->close();
?>
