<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $skill = trim($_POST['skill']);
    $camp_id = !empty($_POST['camp_id']) ? intval($_POST['camp_id']) : null;
    
    // Validate inputs
    if (empty($name) || empty($skill)) {
        header("Location: edit_volunteer.php?id=$id&msg=Name+and+skill+are+required");
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // If camp_id is provided and different from current, check if camp has space
        if ($camp_id) {
            $check_sql = "SELECT 
                rc.capacity,
                (SELECT COUNT(*) FROM Volunteer WHERE camp_id = ? AND id != ?) as current_volunteers
            FROM Relief_Camp rc
            WHERE rc.id = ?";
            
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("iii", $camp_id, $id, $camp_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $camp_data = $result->fetch_assoc();
            
            if ($camp_data['current_volunteers'] >= $camp_data['capacity']) {
                throw new Exception("Selected camp is at full capacity");
            }
        }
        
        // Update the volunteer
        $sql = "UPDATE Volunteer SET name = ?, skill = ?, camp_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $name, $skill, $camp_id, $id);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: volunteers.php?msg=Volunteer+updated+successfully");
            exit;
        } else {
            throw new Exception("Error updating volunteer");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: edit_volunteer.php?id=$id&msg=Error:+" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: volunteers.php");
    exit;
}

$conn->close();
?>
