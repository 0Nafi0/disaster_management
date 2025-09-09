<?php
require_once '../includes/db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete the volunteering assignment
    $sql = "DELETE FROM volunteering WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>
                alert('Volunteer assignment deleted successfully');
                window.location.href='assign_volunteering.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting volunteer assignment');
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
