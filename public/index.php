<?php
require_once '../includes/db_connect.php';

// get disasters
$sql = "SELECT id, name, type, location FROM Disaster ORDER BY id DESC";
$result = $conn->query($sql);

// summary
$stats_sql = "SELECT 
    (SELECT COUNT(*) FROM Relief_Camp) as camp_count,
    (SELECT COUNT(*) FROM Volunteer) as volunteer_count,
    (SELECT COUNT(*) FROM victim) as victim_count,
    (SELECT COUNT(DISTINCT skill) FROM Volunteer) as unique_skills,
    (SELECT COUNT(*) FROM Disaster) as disaster_count";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// get camp occupancy stats using subquery and aggregate functions
$occupancy_sql = "SELECT 
    rc.id,
    rc.name,
    rc.capacity,
    COUNT(v.id) as victim_count,
    COUNT(vol.id) as volunteer_count
FROM Relief_Camp rc
LEFT JOIN victim v ON rc.id = v.camp_id
LEFT JOIN Volunteer vol ON rc.id = vol.camp_id
GROUP BY rc.id
ORDER BY (COUNT(v.id)/rc.capacity) DESC
LIMIT 5";
$occupancy_result = $conn->query($occupancy_sql);

// skills distribution
$skills_sql = "SELECT 
    skill, 
    COUNT(*) as count 
FROM Volunteer 
GROUP BY skill 
ORDER BY count DESC 
LIMIT 5";
$skills_result = $conn->query($skills_sql);
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
                <a href="add_volunteer.php" 
                   class="bg-yellow-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-yellow-600">
                   + Add Volunteer
                </a>
                <a href="add_relief_camp.php" 
                   class="bg-purple-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-purple-600">
                   + Add Relief Camp
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-5 gap-4 mb-8">
            <div class="bg-red-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-red-700">Active Disasters</h3>
                <p class="text-2xl font-bold text-red-800"><?php echo $stats['disaster_count']; ?></p>
            </div>
            <div class="bg-blue-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-700">Total Victims</h3>
                <p class="text-2xl font-bold text-blue-800"><?php echo $stats['victim_count']; ?></p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-700">Volunteers</h3>
                <p class="text-2xl font-bold text-yellow-800"><?php echo $stats['volunteer_count']; ?></p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-purple-700">Relief Camps</h3>
                <p class="text-2xl font-bold text-purple-800"><?php echo $stats['camp_count']; ?></p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-green-700">Unique Skills</h3>
                <p class="text-2xl font-bold text-green-800"><?php echo $stats['unique_skills']; ?></p>
            </div>
        </div>

        <!-- Camp Occupancy and Skills Distribution -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <!-- Camp Occupancy -->
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Camp Occupancy Overview</h3>
                <div class="space-y-2">
                    <?php while($camp = $occupancy_result->fetch_assoc()): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm"><?php echo htmlspecialchars($camp['name']); ?></span>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs">
                                    Victims: <?php echo $camp['victim_count']; ?>/<?php echo $camp['capacity']; ?>
                                </span>
                                <span class="text-xs text-yellow-600">
                                    (<?php echo $camp['volunteer_count']; ?> volunteers)
                                </span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: <?php echo ($camp['victim_count']/$camp['capacity']*100); ?>%">
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Skills Distribution -->
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Top Volunteer Skills</h3>
                <div class="space-y-4">
                    <?php while($skill = $skills_result->fetch_assoc()): ?>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span><?php echo htmlspecialchars($skill['skill']); ?></span>
                                <span class="font-semibold"><?php echo $skill['count']; ?> volunteers</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" 
                                     style="width: <?php echo ($skill['count']/$stats['volunteer_count']*100); ?>%">
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6 flex space-x-6 border-b pb-2">
            <a href="index.php" class="font-semibold text-blue-600">Disasters</a>
            <a href="victims.php" class="font-semibold text-gray-600 hover:text-blue-600">Victims</a>
            <a href="volunteers.php" class="font-semibold text-gray-600 hover:text-blue-600">Volunteers</a>
            <a href="relief_camps.php" class="font-semibold text-gray-600 hover:text-blue-600">Relief Camps</a>
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
