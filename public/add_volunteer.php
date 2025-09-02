<?php
require_once '../includes/db_connect.php';

// Get available camps with their disaster names using JOIN
$camps_sql = "SELECT rc.id, rc.name, d.name as disaster_name,
    (SELECT COUNT(*) FROM Volunteer WHERE camp_id = rc.id) as current_volunteers,
    rc.capacity
FROM Relief_Camp rc
JOIN disaster d ON rc.disaster_id = d.id
HAVING current_volunteers < capacity  -- Only show camps that aren't full
ORDER BY rc.name";

$camps_result = $conn->query($camps_sql);

// Get common skills from existing volunteers for suggestions
$skills_sql = "SELECT DISTINCT skill FROM Volunteer ORDER BY skill";
$skills_result = $conn->query($skills_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Volunteer</title>
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
            <h1 class="text-3xl font-bold mb-6">Add New Volunteer</h1>
        
        <form action="process_add_volunteer.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Volunteer Name</label>
                <input type="text" name="name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Skill</label>
                <input type="text" name="skill" required list="skills"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                <datalist id="skills">
                    <?php while($skill = $skills_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($skill['skill']); ?>">
                    <?php endwhile; ?>
                </datalist>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Assign to Camp</label>
                <select name="camp_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    <option value="">-- Select Camp --</option>
                    <?php while($camp = $camps_result->fetch_assoc()): ?>
                        <option value="<?php echo $camp['id']; ?>">
                            <?php echo htmlspecialchars($camp['name']); ?> 
                            (<?php echo htmlspecialchars($camp['disaster_name']); ?>) - 
                            <?php echo $camp['current_volunteers']; ?>/<?php echo $camp['capacity']; ?> volunteers
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                    Add Volunteer
                </button>
                <a href="volunteers.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
