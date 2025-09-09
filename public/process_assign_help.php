<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $volunteer_id = isset($_POST['volunteer_id']) ? intval($_POST['volunteer_id']) : 0;
    $victim_id = isset($_POST['victim_id']) ? intval($_POST['victim_id']) : 0;
    $help_type = isset($_POST['help_type']) ? trim($_POST['help_type']) : '';
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    if (!$volunteer_id || !$victim_id || empty($help_type) || empty($start_date)) {
        header("Location: assign_help.php?error=Please fill all required fields");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if volunteer and victim exist
        $check_sql = "SELECT 
            (SELECT COUNT(*) FROM Volunteer WHERE id = ?) as volunteer_exists,
            (SELECT COUNT(*) FROM Victim WHERE id = ?) as victim_exists";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $volunteer_id, $victim_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $exists = $result->fetch_assoc();

        if (!$exists['volunteer_exists'] || !$exists['victim_exists']) {
            throw new Exception("Invalid volunteer or victim selected");
        }

        // Check if this assignment already exists and is active
        $existing_sql = "SELECT id FROM Volunteer_Victim_Help 
                        WHERE volunteer_id = ? AND victim_id = ? 
                        AND (end_date IS NULL OR end_date >= CURDATE())";
        $existing_stmt = $conn->prepare($existing_sql);
        $existing_stmt->bind_param("ii", $volunteer_id, $victim_id);
        $existing_stmt->execute();
        if ($existing_stmt->get_result()->num_rows > 0) {
            throw new Exception("This volunteer is already assigned to help this victim");
        }

        // Create new assignment
        $insert_sql = "INSERT INTO Volunteer_Victim_Help 
                      (volunteer_id, victim_id, help_type, start_date, end_date) 
                      VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iisss", $volunteer_id, $victim_id, $help_type, $start_date, $end_date);
        
        if ($insert_stmt->execute()) {
            $conn->commit();
            header("Location: assign_help.php?success=Help assignment created successfully");
            exit;
        } else {
            throw new Exception("Error creating help assignment");
        }

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: assign_help.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

header("Location: assign_help.php");
exit;
