<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name']);
    $quantity = intval($_POST['quantity']);
    $unit = !empty($_POST['unit']) ? trim($_POST['unit']) : null;
    
    // Validate inputs
    if (empty($name)) {
        header("Location: add_resource.php?msg=Resource+name+is+required");
        exit;
    }
    
    if ($quantity < 0) {
        header("Location: add_resource.php?msg=Quantity+cannot+be+negative");
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert the resource
        $sql = "INSERT INTO Resource (name, quantity, unit) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $name, $quantity, $unit);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: resources.php?msg=Resource+added+successfully");
            exit;
        } else {
            throw new Exception("Error adding resource");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: add_resource.php?msg=Error:+" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: resources.php");
    exit;
}

$conn->close();
?>
