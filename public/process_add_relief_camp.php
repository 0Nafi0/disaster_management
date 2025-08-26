<?php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $capacity = $_POST['capacity'];
    $disaster_id = $_POST['disaster_id'];

    $stmt = $conn->prepare("INSERT INTO Relief_Camp (name, capacity, disaster_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $name, $capacity, $disaster_id);

    if ($stmt->execute()) {
        header("Location: relief_camps.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
