<?php
require_once '../includes/db_connect.php';

$sql = "SELECT id, name, type, location FROM Disaster ORDER BY id DESC";
$result = $conn->query($sql);

$camp_sql = "SELECT COUNT(*) as camp_count FROM Relief_Camp";
$camp_result = $conn->query($camp_sql);
$camp_count = $camp_result->fetch_assoc()['camp_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disaster Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8 bg-white rounded-lg shadow-md">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">
                Disaster Management Dashboard
            </h1>
            <div class="space-x-4">
                <a href="add_disaster.php" 
                   class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600">
                   + Add Disaster
                </a>
                <a href="add_victim.php" 
                   class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                   + Add Victim
                </a>
                <a href="add_relief_camp.php" 
                   class="bg-purple-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-purple-600">
                   + Add Relief Camp
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6 flex space-x-6 border-b pb-2">
            <a href="index.php" class="font-semibold text-blue-600">Disasters</a>
            <a href="victims.php" class="font-semibold text-gray-600 hover:text-blue-600">Victims</a>
            <a href="volunteers.php" class="font-semibold text-gray-600 hover:text-blue-600">Volunteers</a>
            <a href="resources.php" class="font-semibold text-gray-600 hover:text-blue-600">Resources</a>
            <a href="relief_camps.php" class="font-semibold text-gray-600 hover:text-blue-600">Relief Camps (<?php echo $camp_count; ?>)</a>
        </div>

        <!-- Disasters Table -->
        <h2 class="text-xl font-bold mb-4">Recorded Disasters</h2>
        <table class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Type</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Location</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="border-t">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['type']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['location']); ?></td>
                            <td class="py-3 px-4">
                                <a href="edit_disaster.php?id=<?php echo $row['id']; ?>" 
                                   class="text-blue-500 hover:text-blue-700">Edit</a>
                                <a href="delete_disaster.php?id=<?php echo $row['id']; ?>" 
                                   class="text-red-500 hover:text-red-700 ml-4"
                                   onclick="return confirm('Are you sure you want to delete this disaster?');">
                                   Delete
                                </a>
                                <a href="relief_camps.php?disaster_id=<?php echo $row['id']; ?>"
                                   class="text-purple-500 hover:text-purple-700 ml-4">
                                   View Relief Camps
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">No disasters recorded yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
