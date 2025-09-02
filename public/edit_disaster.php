<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: index.php?msg=Invalid+disaster+ID");
    exit;
}

// Fetch disaster details
$sql = "SELECT d.*,
    (SELECT COUNT(*) FROM victim WHERE disaster_id = d.id) as victim_count,
    (SELECT COUNT(*) FROM Relief_Camp WHERE disaster_id = d.id) as camp_count
FROM Disaster d
WHERE d.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$disaster = $result->fetch_assoc();

if (!$disaster) {
    header("Location: index.php?msg=Disaster+not+found");
    exit;
}

// Get common disaster types from existing records for suggestions
$types_sql = "SELECT DISTINCT type FROM Disaster WHERE type IS NOT NULL ORDER BY type";
$types_result = $conn->query($types_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Disaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="flex justify-end mb-4">
            <a href="index.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                🏠 Home
            </a>
        </div>
        <div class="max-w-2xl mx-auto p-8 bg-white rounded-lg shadow-md">
            <h1 class="text-3xl font-bold mb-6">Edit Disaster</h1>

            <?php if ($disaster['victim_count'] > 0 || $disaster['camp_count'] > 0): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-6">
                <p>This disaster has:</p>
                <ul class="list-disc ml-5 mt-2">
                    <?php if ($disaster['victim_count'] > 0): ?>
                        <li><?php echo $disaster['victim_count']; ?> registered victims</li>
                    <?php endif; ?>
                    <?php if ($disaster['camp_count'] > 0): ?>
                        <li><?php echo $disaster['camp_count']; ?> relief camps</li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <form action="process_edit_disaster.php" method="POST" class="space-y-6">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($disaster['id']); ?>">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Disaster Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?php echo htmlspecialchars($disaster['name']); ?>" 
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                    <input type="text" 
                           id="type" 
                           name="type" 
                           value="<?php echo htmlspecialchars($disaster['type']); ?>" 
                           list="disaster-types"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    <datalist id="disaster-types">
                        <?php while($type = $types_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($type['type']); ?>">
                        <?php endwhile; ?>
                    </datalist>
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="<?php echo htmlspecialchars($disaster['location']); ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                        Update Disaster
                    </button>
                    <a href="index.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
