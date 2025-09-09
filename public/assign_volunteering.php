<?php
require_once '../includes/db_connect.php';

// Get all volunteers
$volunteer_sql = "SELECT id, name FROM Volunteer ORDER BY name";
$volunteer_result = $conn->query($volunteer_sql);

// Get all relief camps
$camp_sql = "SELECT id, name FROM Relief_Camp ORDER BY name";
$camp_result = $conn->query($camp_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Volunteer to Camp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8 bg-white rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Assign Volunteer to Relief Camp</h1>
            <a href="index.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                🏠Home
            </a>
        </div>
        
        <form action="process_assign_volunteering.php" method="POST" class="space-y-4">
            <div>
                <label for="volunteer_id" class="block text-sm font-medium text-gray-700">Select Volunteer</label>
                <select name="volunteer_id" id="volunteer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select a volunteer</option>
                    <?php while($volunteer = $volunteer_result->fetch_assoc()): ?>
                        <option value="<?php echo $volunteer['id']; ?>">
                            <?php echo htmlspecialchars($volunteer['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label for="camp_id" class="block text-sm font-medium text-gray-700">Select Relief Camp</label>
                <select name="camp_id" id="camp_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select a relief camp</option>
                    <?php while($camp = $camp_result->fetch_assoc()): ?>
                        <option value="<?php echo $camp['id']; ?>">
                            <?php echo htmlspecialchars($camp['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                    Assign Volunteer
                </button>
                <a href="index.php" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>

        <!-- Display Current Assignments -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Current Volunteer Assignments</h2>
            <?php
            $assignments_sql = "SELECT v.name as volunteer_name, rc.name as camp_name, 
                                     vol.start_date, vol.end_date, vol.id
                              FROM volunteering vol
                              JOIN Volunteer v ON vol.volunteer_id = v.id
                              JOIN Relief_Camp rc ON vol.camp_id = rc.id
                              ORDER BY vol.start_date DESC";
            $assignments_result = $conn->query($assignments_sql);
            ?>
            
            <table class="min-w-full bg-white border">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Volunteer</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Relief Camp</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Start Date</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">End Date</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($assignments_result->num_rows > 0): ?>
                        <?php while($assignment = $assignments_result->fetch_assoc()): ?>
                            <tr class="border-t">
                                <td class="py-3 px-4"><?php echo htmlspecialchars($assignment['volunteer_name']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($assignment['camp_name']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($assignment['start_date']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($assignment['end_date']); ?></td>
                                <td class="py-3 px-4">
                                    <a href="delete_volunteering.php?id=<?php echo $assignment['id']; ?>" 
                                       class="text-red-500 hover:text-red-700"
                                       onclick="return confirm('Are you sure you want to delete this assignment?');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No volunteer assignments found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
