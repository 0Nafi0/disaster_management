<?php

require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['disaster_name']);
    $type = trim($_POST['disaster_type']);
    $location = trim($_POST['disaster_location']);

    $sql = "INSERT INTO Disaster (name, type, location) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $name, $type, $location);

        if ($stmt->execute()) {
            header("Location: index.php?status=success");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close();

} else {
    header("Location: add_disaster.php");
    exit();
}