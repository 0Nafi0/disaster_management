<?php
require_once '../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $volunteer_id = $_POST['volunteer_id'];
    $camp_id = $_POST['camp_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Validate dates
    if (strtotime($end_date) < strtotime($start_date)) {
        echo "<script>
                alert('End date cannot be earlier than start date');
                window.location.href='assign_volunteering.php';
              </script>";
        exit;
    }

    // Check for overlapping assignments
    $overlap_check = "SELECT * FROM volunteering 
                     WHERE volunteer_id = ? 
                     AND ((start_date BETWEEN ? AND ?) 
                          OR (end_date BETWEEN ? AND ?)
                          OR (start_date <= ? AND end_date >= ?))";
    
    $stmt = $conn->prepare($overlap_check);
    $stmt->bind_param("issssss", $volunteer_id, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('This volunteer already has an assignment during this period');
                window.location.href='assign_volunteering.php';
              </script>";
        exit;
    }

    // Insert new assignment
    $sql = "INSERT INTO volunteering (volunteer_id, camp_id, start_date, end_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $volunteer_id, $camp_id, $start_date, $end_date);

    if ($stmt->execute()) {
        echo "<script>
                alert('Volunteer assigned successfully');
                window.location.href='assign_volunteering.php';
              </script>";
    } else {
        echo "<script>
                alert('Error assigning volunteer: " . $stmt->error . "');
                window.location.href='assign_volunteering.php';
              </script>";
    }

    $stmt->close();
} else {
    header("Location: assign_volunteering.php");
    exit;
}

$conn->close();
?>
