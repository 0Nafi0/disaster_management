<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record_id = isset($_POST['record_id']) ? intval($_POST['record_id']) : 0;
    $available_resources = isset($_POST['available_resources']) ? trim($_POST['available_resources']) : '';

    if (!$record_id || empty($available_resources)) {
        header("Location: manage_camp_disasters.php?error=Invalid input");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update the record
        $update_sql = "UPDATE records SET available_resources = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $available_resources, $record_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Error updating record");
        }

        $conn->commit();
        header("Location: manage_camp_disasters.php?success=Record updated successfully");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_camp_disasters.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

// Get record details for editing
$record_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$record_id) {
    header("Location: manage_camp_disasters.php?error=Invalid request");
    exit;
}

$record_sql = "SELECT r.*, rc.name as camp_name, d.name as disaster_name,
                      d.type as disaster_type, d.location as disaster_location
               FROM records r
               JOIN Relief_Camp rc ON r.camp_id = rc.id
               JOIN Disaster d ON r.disaster_id = d.id
               WHERE r.id = ?";
$record_stmt = $conn->prepare($record_sql);
$record_stmt->bind_param("i", $record_id);
$record_stmt->execute();
$record = $record_stmt->get_result()->fetch_assoc();

if (!$record) {
    header("Location: manage_camp_disasters.php?error=Record not found");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Camp-Disaster Record</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Edit Camp-Disaster Record</h1>
                <a href="manage_camp_disasters.php" class="text-gray-600 hover:text-gray-800">Back</a>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded">
                <p><strong>Relief Camp:</strong> <?php echo htmlspecialchars($record['camp_name']); ?></p>
                <p><strong>Disaster:</strong> <?php echo htmlspecialchars($record['disaster_name']); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($record['disaster_type']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($record['disaster_location']); ?></p>
            </div>

            <form action="edit_camp_disaster.php" method="POST" class="space-y-4">
                <input type="hidden" name="record_id" value="<?php echo $record_id; ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Available Resources</label>
                    <textarea name="available_resources" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border"
                              rows="6"><?php echo htmlspecialchars($record['available_resources']); ?></textarea>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="manage_camp_disasters.php" 
                       class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                        Update Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
