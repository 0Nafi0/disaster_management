<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: relief_camps.php");
    exit;
}

// Fetch current camp details
$sql = "SELECT * FROM Relief_Camp WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$camp = $result->fetch_assoc();

if (!$camp) {
    header("Location: relief_camps.php");
    exit;
}

// Fetch all disasters for the dropdown
$disasters_sql = "SELECT id, name FROM disaster ORDER BY name";
$disasters_result = $conn->query($disasters_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Relief Camp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="flex justify-end mb-4">
            <a href="index.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                🏠 Home
            </a>
        </div>
        <div class="p-8 bg-white rounded-lg shadow-md">
            <h1 class="text-3xl font-bold mb-6">Edit Relief Camp</h1>
        
        <form action="process_edit_relief_camp.php" method="POST" class="space-y-4">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($camp['id']); ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Camp Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($camp['name']); ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Capacity</label>
                <input type="number" name="capacity" value="<?php echo htmlspecialchars($camp['capacity']); ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Disaster</label>
                <select name="disaster_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    <?php while($disaster = $disasters_result->fetch_assoc()): ?>
                        <option value="<?php echo $disaster['id']; ?>" 
                            <?php echo ($disaster['id'] == $camp['disaster_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($disaster['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                    Update Camp
                </button>
                <a href="relief_camps.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>
