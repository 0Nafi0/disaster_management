<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: volunteers.php");
    exit;
}

// Fetch volunteer details with camp info using JOIN
$sql = "SELECT v.*, rc.name as camp_name, d.name as disaster_name
FROM Volunteer v
LEFT JOIN Relief_Camp rc ON v.camp_id = rc.id
LEFT JOIN disaster d ON rc.disaster_id = d.id
WHERE v.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$volunteer = $result->fetch_assoc();

if (!$volunteer) {
    header("Location: volunteers.php");
    exit;
}

// Get available camps with their disaster names using JOIN
$camps_sql = "SELECT rc.id, rc.name, d.name as disaster_name,
    (SELECT COUNT(*) FROM Volunteer WHERE camp_id = rc.id AND id != ?) as current_volunteers,
    rc.capacity
FROM Relief_Camp rc
JOIN disaster d ON rc.disaster_id = d.id
HAVING current_volunteers < capacity  -- Only show camps that aren't full
    OR rc.id = ?  -- Always show current camp
ORDER BY rc.name";

$camps_stmt = $conn->prepare($camps_sql);
$camps_stmt->bind_param("ii", $id, $volunteer['camp_id']);
$camps_stmt->execute();
$camps_result = $camps_stmt->get_result();

// Get common skills from existing volunteers
$skills_sql = "SELECT DISTINCT skill FROM Volunteer ORDER BY skill";
$skills_result = $conn->query($skills_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Volunteer</title>
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
            <h1 class="text-3xl font-bold mb-6">Edit Volunteer</h1>
        
        <form action="process_edit_volunteer.php" method="POST" class="space-y-4">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($volunteer['id']); ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Volunteer Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($volunteer['name']); ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Skill</label>
                <input type="text" name="skill" value="<?php echo htmlspecialchars($volunteer['skill']); ?>" required list="skills"
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
                        <option value="<?php echo $camp['id']; ?>" 
                            <?php echo ($camp['id'] == $volunteer['camp_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($camp['name']); ?> 
                            (<?php echo htmlspecialchars($camp['disaster_name']); ?>) - 
                            <?php echo $camp['current_volunteers']; ?>/<?php echo $camp['capacity']; ?> volunteers
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                    Update Volunteer
                </button>
                <a href="volunteers.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>

<?php 
$stmt->close();
$camps_stmt->close();
$conn->close(); 
?>
