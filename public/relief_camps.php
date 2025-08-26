<?php
require_once '../includes/db_connect.php';

$sql = "SELECT rc.id, rc.name, rc.capacity, d.name AS disaster_name
        FROM Relief_Camp rc
        JOIN disaster d ON rc.disaster_id = d.id
        ORDER BY rc.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relief Camps</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8 bg-white rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Relief Camps</h1>
            <a href="add_relief_camp.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600">
                + Add New Camp
            </a>
        </div>

        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 text-left">Camp Name</th>
                    <th class="py-3 px-4 text-left">Capacity</th>
                    <th class="py-3 px-4 text-left">Disaster</th>
                    <th class="py-3 px-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['capacity']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['disaster_name']); ?></td>
                            <td class="py-3 px-4">
                                <a href="#" class="text-blue-500 hover:text-blue-700">Edit</a>
                                <a href="#" class="text-red-500 hover:text-red-700 ml-4">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-4">No camps recorded yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
