<?php
require_once '../includes/db_connect.php';

$sql = "SELECT id, name, type, location FROM Disaster ORDER BY id DESC";
$result = $conn->query($sql);

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
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">
                Disaster Dashboard
            </h1>
            <a href="add_disaster.php" class="bg-green-500 font-bold 
            py-2 px-4 rounded-lg hover::bg-green-600">+ Add New Disaster</a>
        </div>


        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Type</th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Location</th>
                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['type']); ?></td>
                            <td class="text-left py-3 px-4"><?php echo htmlspecialchars($row['location']); ?></td>
                            <td class="text-left py-3 px-4">
                                <a href="#" class="text-blue-500 hover:text-blue-700">Edit</a>
                                <a href="#" class="text-red-500 hover:text-red-700 ml-4">Delete</a>
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