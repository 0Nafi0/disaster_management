<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $camp_id = isset($_POST['camp_id']) ? intval($_POST['camp_id']) : 0;
    $disaster_id = isset($_POST['disaster_id']) ? intval($_POST['disaster_id']) : 0;
    $available_resources = isset($_POST['available_resources']) ? trim($_POST['available_resources']) : '';

    if (!$camp_id || !$disaster_id || empty($available_resources)) {
        header("Location: manage_camp_disasters.php?error=Please fill all fields");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if camp and disaster exist
        $check_sql = "SELECT 
            (SELECT COUNT(*) FROM Relief_Camp WHERE id = ?) as camp_exists,
            (SELECT COUNT(*) FROM Disaster WHERE id = ?) as disaster_exists";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $camp_id, $disaster_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $exists = $result->fetch_assoc();

        if (!$exists['camp_exists'] || !$exists['disaster_exists']) {
            throw new Exception("Invalid camp or disaster selected");
        }

        // Check if record already exists
        $existing_sql = "SELECT id FROM records WHERE camp_id = ? AND disaster_id = ?";
        $existing_stmt = $conn->prepare($existing_sql);
        $existing_stmt->bind_param("ii", $camp_id, $disaster_id);
        $existing_stmt->execute();
        
        if ($existing_stmt->get_result()->num_rows > 0) {
            throw new Exception("This camp is already assigned to this disaster");
        }

        // Insert new record
        $insert_sql = "INSERT INTO records (camp_id, disaster_id, available_resources) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iis", $camp_id, $disaster_id, $available_resources);
        
        if (!$insert_stmt->execute()) {
            throw new Exception("Error creating record");
        }

        $conn->commit();
        header("Location: manage_camp_disasters.php?success=Record created successfully");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_camp_disasters.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: manage_camp_disasters.php");
exit;
