<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name']);
    $skill = trim($_POST['skill']);
    $camp_id = !empty($_POST['camp_id']) ? intval($_POST['camp_id']) : null;
    
    // Validate inputs
    if (empty($name) || empty($skill)) {
        header("Location: add_volunteer.php?msg=Name+and+skill+are+required");
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // If camp_id is provided, check if camp has space
        if ($camp_id) {
            $check_sql = "SELECT 
                rc.capacity,
                (SELECT COUNT(*) FROM Volunteer WHERE camp_id = ?) as current_volunteers
            FROM Relief_Camp rc
            WHERE rc.id = ?";
            
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $camp_id, $camp_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            $camp_data = $result->fetch_assoc();
            
            if ($camp_data['current_volunteers'] >= $camp_data['capacity']) {
                throw new Exception("Selected camp is at full capacity");
            }
        }
        
        // Insert the volunteer
        $sql = "INSERT INTO Volunteer (name, skill, camp_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $skill, $camp_id);
        
        if ($stmt->execute()) {
            $conn->commit();
            header("Location: volunteers.php?msg=Volunteer+added+successfully");
            exit;
        } else {
            throw new Exception("Error adding volunteer");
        }
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: add_volunteer.php?msg=Error:+" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: volunteers.php");
    exit;
}

$conn->close();
?>
