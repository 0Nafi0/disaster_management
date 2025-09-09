<?php
require_once '../includes/db_connect.php';

// Get all relief camps
$camps_sql = "SELECT rc.id, rc.name, d.name as disaster_name 
              FROM Relief_Camp rc 
              JOIN disaster d ON rc.disaster_id = d.id 
              ORDER BY rc.name";
$camps_result = $conn->query($camps_sql);

// Get all resources
$resources_sql = "SELECT id, name, quantity, unit FROM Resource WHERE quantity > 0 ORDER BY name";
$resources_result = $conn->query($resources_sql);

// Get current assignments for reference
$assignments_sql = "SELECT cr.camp_id, cr.resource_id, cr.quantity, 
                          r.name as resource_name, r.unit,
                          rc.name as camp_name
                   FROM Camp_Resource cr
                   JOIN Resource r ON cr.resource_id = r.id
                   JOIN Relief_Camp rc ON cr.camp_id = rc.id
                   ORDER BY rc.name, r.name";
$assignments_result = $conn->query($assignments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Resources to Camps</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="flex justify-between mb-4">
            <h1 class="text-3xl font-bold">Assign Resources to Relief Camps</h1>
            <a href="index.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                🏠 Home
            </a>
        </div>

        <div class="grid grid-cols-2 gap-8">
            <!-- Assignment Form -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Assign Resource</h2>
                <form action="process_assign_resource.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Relief Camp</label>
                        <select name="camp_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Relief Camp</option>
                            <?php while($camp = $camps_result->fetch_assoc()): ?>
                                <option value="<?php echo $camp['id']; ?>">
                                    <?php echo htmlspecialchars($camp['name']); ?> 
                                    (<?php echo htmlspecialchars($camp['disaster_name']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Resource</label>
                        <select name="resource_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Resource</option>
                            <?php while($resource = $resources_result->fetch_assoc()): ?>
                                <option value="<?php echo $resource['id']; ?>" 
                                        data-available="<?php echo $resource['quantity']; ?>"
                                        data-unit="<?php echo htmlspecialchars($resource['unit']); ?>">
                                    <?php echo htmlspecialchars($resource['name']); ?> 
                                    (Available: <?php echo $resource['quantity'] . ' ' . $resource['unit']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" name="quantity" required min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                        Assign Resource
                    </button>
                </form>
            </div>

            <!-- Current Assignments -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Current Resource Assignments</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Camp</th>
                                <th class="px-4 py-2 text-left">Resource</th>
                                <th class="px-4 py-2 text-left">Quantity</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($assignment = $assignments_result->fetch_assoc()): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($assignment['camp_name']); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($assignment['resource_name']); ?></td>
                                    <td class="px-4 py-2">
                                        <?php echo $assignment['quantity'] . ' ' . $assignment['unit']; ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="delete_camp_resource.php?camp_id=<?php echo $assignment['camp_id']; ?>&resource_id=<?php echo $assignment['resource_id']; ?>" 
                                           class="text-red-500 hover:text-red-700"
                                           onclick="return confirm('Are you sure you want to remove this assignment?');">
                                            Remove
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

    <script>
        // Add client-side validation for quantity
        document.querySelector('select[name="resource_id"]').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const availableQty = parseInt(selectedOption.getAttribute('data-available'));
            const qtyInput = document.querySelector('input[name="quantity"]');
            qtyInput.max = availableQty;
            qtyInput.placeholder = `Max: ${availableQty}`;
        });
    </script>
</body>
</html>
