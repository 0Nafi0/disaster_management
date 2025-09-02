<?php
require_once '../includes/db_connect.php';

// volunteer count per camp
$sql = "SELECT 
    v.*, 
    rc.name AS camp_name,
    d.name AS disaster_name,
    (SELECT COUNT(*) FROM Volunteer WHERE camp_id = v.camp_id) as volunteers_in_camp
FROM Volunteer v
LEFT JOIN Relief_Camp rc ON v.camp_id = rc.id
LEFT JOIN disaster d ON rc.disaster_id = d.id
ORDER BY v.id DESC";

$result = $conn->query($sql);

// stats
$stats_sql = "SELECT 
    COUNT(*) as total_volunteers,
    COUNT(DISTINCT camp_id) as camps_with_volunteers,
    COUNT(DISTINCT skill) as unique_skills
FROM Volunteer";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteers</title>
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
        <?php if (isset($_GET['msg'])): ?>
            <?php 
            $isError = strpos(strtolower($_GET['msg']), 'error') !== false || 
                      strpos(strtolower($_GET['msg']), 'cannot') !== false;
            $class = $isError ? 
                "bg-red-100 border border-red-400 text-red-700" : 
                "bg-green-100 border border-green-400 text-green-700";
            ?>
            <div class="<?php echo $class; ?> px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold">Total Volunteers</h3>
                <p class="text-2xl font-bold text-blue-800"><?php echo $stats['total_volunteers']; ?></p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold">Camps with Volunteers</h3>
                <p class="text-2xl font-bold text-green-800"><?php echo $stats['camps_with_volunteers']; ?></p>
            </div>
            <div class="bg-purple-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold">Unique Skills</h3>
                <p class="text-2xl font-bold text-purple-800"><?php echo $stats['unique_skills']; ?></p>
            </div>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Volunteers</h1>
            <a href="add_volunteer.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600">
                + Add New Volunteer
            </a>
        </div>

        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-3 px-4 text-left">Name</th>
                    <th class="py-3 px-4 text-left">Skill</th>
                    <th class="py-3 px-4 text-left">Assigned Camp</th>
                    <th class="py-3 px-4 text-left">Disaster</th>
                    <th class="py-3 px-4 text-left">Camp Volunteers</th>
                    <th class="py-3 px-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['skill']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['camp_name'] ?? 'Not Assigned'); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['disaster_name'] ?? 'N/A'); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['volunteers_in_camp']); ?></td>
                            <td class="py-3 px-4">
                                <a href="edit_volunteer.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                                <a href="delete_volunteer.php?id=<?php echo $row['id']; ?>" 
                                   class="text-red-500 hover:text-red-700 ml-4"
                                   onclick="return confirm('Are you sure you want to delete this volunteer?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">No volunteers recorded yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
