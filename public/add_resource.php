<?php
require_once '../includes/db_connect.php';

// Get existing units for suggestions
$units_sql = "SELECT DISTINCT unit FROM Resource WHERE unit IS NOT NULL ORDER BY unit";
$units_result = $conn->query($units_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resource</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <div class="flex justify-end mb-4">
            <a href="index.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                🏠 Home
            </a>
        </div>
        <div class="max-w-md mx-auto p-8 bg-white rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6">Add New Resource</h1>
            
            <form action="process_add_resource.php" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Resource Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                </div>
                
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           min="0"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                </div>
                
                <div>
                    <label for="unit" class="block text-sm font-medium text-gray-700">Unit</label>
                    <input type="text" 
                           id="unit" 
                           name="unit" 
                           list="units"
                           placeholder="e.g., kg, liters, pieces"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    <datalist id="units">
                        <?php while($unit = $units_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($unit['unit']); ?>">
                        <?php endwhile; ?>
                    </datalist>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600">
                        Add Resource
                    </button>
                    <a href="resources.php" class="bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
