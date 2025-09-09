<?php
require_once '../includes/db_connect.php';

// Get all volunteers
$volunteers_sql = "SELECT v.*, rc.name as camp_name 
                  FROM Volunteer v 
                  LEFT JOIN Relief_Camp rc ON v.camp_id = rc.id 
                  ORDER BY v.name";
$volunteers_result = $conn->query($volunteers_sql);

// Get all victims
$victims_sql = "SELECT v.*, rc.name as camp_name, d.name as disaster_name 
                FROM Victim v 
                LEFT JOIN Relief_Camp rc ON v.camp_id = rc.id
                LEFT JOIN Disaster d ON v.disaster_id = d.id 
                ORDER BY v.name";
$victims_result = $conn->query($victims_sql);

// Get current help assignments
$assignments_sql = "SELECT vh.*, 
                          v.name as victim_name, 
                          vol.name as volunteer_name,
                          rc_v.name as victim_camp,
                          rc_vol.name as volunteer_camp
                   FROM Volunteer_Victim_Help vh
                   JOIN Victim v ON vh.victim_id = v.id
                   JOIN Volunteer vol ON vh.volunteer_id = vol.id
                   LEFT JOIN Relief_Camp rc_v ON v.camp_id = rc_v.id
                   LEFT JOIN Relief_Camp rc_vol ON vol.camp_id = rc_vol.id
                   ORDER BY vh.start_date DESC";
$assignments_result = $conn->query($assignments_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Volunteers to Help Victims</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="flex justify-between mb-4">
            <h1 class="text-3xl font-bold">Assign Volunteers to Help Victims</h1>
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

        <div class="grid grid-cols-2 gap-8">
            <!-- Assignment Form -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Assign Helper</h2>
                <form action="process_assign_help.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Volunteer</label>
                        <select name="volunteer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Volunteer</option>
                            <?php while($volunteer = $volunteers_result->fetch_assoc()): ?>
                                <option value="<?php echo $volunteer['id']; ?>">
                                    <?php echo htmlspecialchars($volunteer['name']); ?> 
                                    <?php if ($volunteer['camp_name']): ?>
                                        (Camp: <?php echo htmlspecialchars($volunteer['camp_name']); ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Victim</label>
                        <select name="victim_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                            <option value="">Select Victim</option>
                            <?php while($victim = $victims_result->fetch_assoc()): ?>
                                <option value="<?php echo $victim['id']; ?>">
                                    <?php echo htmlspecialchars($victim['name']); ?> 
                                    (Camp: <?php echo htmlspecialchars($victim['camp_name']); ?>,
                                    Disaster: <?php echo htmlspecialchars($victim['disaster_name']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Help Type</label>
                        <input type="text" name="help_type" required
                               placeholder="e.g., Medical Care, Food Distribution, Counseling"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" required
                               value="<?php echo date('Y-m-d'); ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                        <input type="date" name="end_date"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                        Assign Helper
                    </button>
                </form>
            </div>

            <!-- Current Assignments -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Current Help Assignments</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Volunteer</th>
                                <th class="px-4 py-2 text-left">Victim</th>
                                <th class="px-4 py-2 text-left">Help Type</th>
                                <th class="px-4 py-2 text-left">Duration</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($assignment = $assignments_result->fetch_assoc()): ?>
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <?php echo htmlspecialchars($assignment['volunteer_name']); ?>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            Camp: <?php echo htmlspecialchars($assignment['volunteer_camp'] ?? 'None'); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php echo htmlspecialchars($assignment['victim_name']); ?>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            Camp: <?php echo htmlspecialchars($assignment['victim_camp'] ?? 'None'); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($assignment['help_type']); ?></td>
                                    <td class="px-4 py-2">
                                        <?php 
                                        echo date('Y-m-d', strtotime($assignment['start_date']));
                                        if ($assignment['end_date']) {
                                            echo ' to ' . date('Y-m-d', strtotime($assignment['end_date']));
                                        } else {
                                            echo ' (Ongoing)';
                                        }
                                        ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="delete_help_assignment.php?id=<?php echo $assignment['id']; ?>" 
                                           class="text-red-500 hover:text-red-700"
                                           onclick="return confirm('Are you sure you want to remove this help assignment?');">
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
</body>
</html>
