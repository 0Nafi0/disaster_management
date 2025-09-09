<?php
require_once '../includes/db_connect.php';

// Get all relief camps
$camps_sql = "SELECT * FROM Relief_Camp ORDER BY name";
$camps_result = $conn->query($camps_sql);

// Get all disasters
$disasters_sql = "SELECT * FROM Disaster ORDER BY name";
$disasters_result = $conn->query($disasters_sql);

// Get current camp-disaster records
$records_sql = "SELECT r.*, rc.name as camp_name, d.name as disaster_name, 
                       d.type as disaster_type, d.location as disaster_location
                FROM records r
                JOIN Relief_Camp rc ON r.camp_id = rc.id
                JOIN Disaster d ON r.disaster_id = d.id
                ORDER BY r.assignment_date DESC";
$records_result = $conn->query($records_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Camp Disaster Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Manage Camp Disaster Records</h1>
            <a href="index.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                🏠 Home
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Assignment Form -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Assign Camp to Disaster</h2>
                <form action="process_camp_disaster.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Relief Camp</label>
                        <select name="camp_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Relief Camp</option>
                            <?php while($camp = $camps_result->fetch_assoc()): ?>
                                <option value="<?php echo $camp['id']; ?>">
                                    <?php echo htmlspecialchars($camp['name']); ?>
                                    (Capacity: <?php echo $camp['capacity']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Disaster</label>
                        <select name="disaster_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Disaster</option>
                            <?php while($disaster = $disasters_result->fetch_assoc()): ?>
                                <option value="<?php echo $disaster['id']; ?>">
                                    <?php echo htmlspecialchars($disaster['name']); ?>
                                    (<?php echo htmlspecialchars($disaster['type']); ?> - 
                                    <?php echo htmlspecialchars($disaster['location']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Available Resources</label>
                        <textarea name="available_resources" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border"
                                rows="4"
                                placeholder="List available resources (e.g., Water: 1000L, Food: 500 packets, Medical supplies: 200 units)"></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                        Create Record
                    </button>
                </form>
            </div>

            <!-- Current Records -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Current Camp-Disaster Records</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Relief Camp</th>
                                <th class="px-4 py-2 text-left">Disaster</th>
                                <th class="px-4 py-2 text-left">Resources</th>
                                <th class="px-4 py-2 text-left">Assignment Date</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($record = $records_result->fetch_assoc()): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($record['camp_name']); ?></td>
                                    <td class="px-4 py-2">
                                        <?php echo htmlspecialchars($record['disaster_name']); ?>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            <?php echo htmlspecialchars($record['disaster_type']); ?> - 
                                            <?php echo htmlspecialchars($record['disaster_location']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="max-h-20 overflow-y-auto">
                                            <?php echo nl2br(htmlspecialchars($record['available_resources'])); ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php echo date('Y-m-d', strtotime($record['assignment_date'])); ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="edit_camp_disaster.php?id=<?php echo $record['id']; ?>" 
                                           class="text-blue-500 hover:text-blue-700 mr-2">
                                            Edit
                                        </a>
                                        <a href="delete_camp_disaster.php?id=<?php echo $record['id']; ?>" 
                                           class="text-red-500 hover:text-red-700"
                                           onclick="return confirm('Are you sure you want to delete this record?');">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
