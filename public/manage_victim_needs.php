<?php
require_once '../includes/db_connect.php';

// Get all victims with their camp and disaster info
$victims_sql = "SELECT v.*, rc.name as camp_name, d.name as disaster_name 
                FROM Victim v 
                LEFT JOIN Relief_Camp rc ON v.camp_id = rc.id
                LEFT JOIN Disaster d ON v.disaster_id = d.id 
                ORDER BY v.name";
$victims_result = $conn->query($victims_sql);

// Get all resources
$resources_sql = "SELECT * FROM Resource ORDER BY name";
$resources_result = $conn->query($resources_sql);

// Get current needs with fulfillment status
$needs_sql = "SELECT n.*, 
                     v.name as victim_name, 
                     r.name as resource_name,
                     r.unit,
                     rc.name as camp_name
              FROM needs n
              JOIN Victim v ON n.victim_id = v.id
              JOIN Resource r ON n.resource_id = r.id
              LEFT JOIN Relief_Camp rc ON v.camp_id = rc.id
              ORDER BY n.request_date DESC";
$needs_result = $conn->query($needs_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Victim Resource Needs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8">
        <div class="flex justify-between mb-6">
            <h1 class="text-3xl font-bold">Manage Victim Resource Needs</h1>
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
            <!-- Resource Need Request Form -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Request Resources for Victim</h2>
                <form action="process_victim_needs.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Victim</label>
                        <select name="victim_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Victim</option>
                            <?php while($victim = $victims_result->fetch_assoc()): ?>
                                <option value="<?php echo $victim['id']; ?>">
                                    <?php echo htmlspecialchars($victim['name']); ?>
                                    (Camp: <?php echo htmlspecialchars($victim['camp_name'] ?? 'None'); ?>,
                                    Disaster: <?php echo htmlspecialchars($victim['disaster_name'] ?? 'None'); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Resource</label>
                        <select name="resource_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Resource</option>
                            <?php while($resource = $resources_result->fetch_assoc()): ?>
                                <option value="<?php echo $resource['id']; ?>">
                                    <?php echo htmlspecialchars($resource['name']); ?>
                                    (Available: <?php echo $resource['quantity'] . ' ' . $resource['unit']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity Needed</label>
                        <input type="number" name="quantity" required min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                        Submit Request
                    </button>
                </form>
            </div>

            <!-- Current Resource Needs -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Current Resource Needs</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Victim</th>
                                <th class="px-4 py-2 text-left">Resource</th>
                                <th class="px-4 py-2 text-left">Quantity</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($need = $needs_result->fetch_assoc()): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <?php echo htmlspecialchars($need['victim_name']); ?>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            Camp: <?php echo htmlspecialchars($need['camp_name'] ?? 'None'); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php echo htmlspecialchars($need['resource_name']); ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php 
                                        echo htmlspecialchars($need['fulfilled_quantity'] . '/' . $need['quantity'] . ' ' . $need['unit']); 
                                        ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php
                                        $status_colors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'partially_fulfilled' => 'bg-blue-100 text-blue-800',
                                            'fulfilled' => 'bg-green-100 text-green-800'
                                        ];
                                        $status_class = $status_colors[$need['status']];
                                        ?>
                                        <span class="px-2 py-1 rounded-full text-xs <?php echo $status_class; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $need['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php if ($need['status'] !== 'fulfilled'): ?>
                                            <a href="fulfill_resource_need.php?id=<?php echo $need['id']; ?>" 
                                               class="text-blue-500 hover:text-blue-700 mr-2">
                                                Fulfill
                                            </a>
                                        <?php endif; ?>
                                        <a href="delete_resource_need.php?id=<?php echo $need['id']; ?>" 
                                           class="text-red-500 hover:text-red-700"
                                           onclick="return confirm('Are you sure you want to delete this resource need?');">
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
