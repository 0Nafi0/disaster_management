<?php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['victim_name']);
    $disaster_id = intval($_POST['disaster_id']);
    $camp_id = intval($_POST['camp_id']);

    $sql = "INSERT INTO Victim (name, disaster_id, camp_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $name, $disaster_id, $camp_id);

    if ($stmt->execute()) {
        header("Location: victims.php?status=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
