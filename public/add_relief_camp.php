<?php
require_once '../includes/db_connect.php';

$disasters = $conn->query("SELECT id, name FROM disaster");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Relief Camp</title>
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
            <h1 class="text-2xl font-bold mb-6">Add Relief Camp</h1>
        <form action="process_add_relief_camp.php" method="POST" class="space-y-4">
            <div>
                <label class="block">Camp Name</label>
                <input type="text" name="name" required class="w-full border px-4 py-2 rounded">
            </div>
            <div>
                <label class="block">Capacity</label>
                <input type="number" name="capacity" required class="w-full border px-4 py-2 rounded">
            </div>
            <div>
                <label class="block">Disaster</label>
                <select name="disaster_id" required class="w-full border px-4 py-2 rounded">
                    <option value="">-- Select Disaster --</option>
                    <?php while($d = $disasters->fetch_assoc()): ?>
                        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg">Save</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
