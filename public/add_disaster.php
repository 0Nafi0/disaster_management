<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Disaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8 bg-white max-w-md rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">
            Record a New Disaster
        </h1>
        <form action="process_add_disaster.php" method="post">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-semibold mb-2">
                    Disaster Name: 
                </label>
                <input type="text" 
                id="name" name="disaster_name" 
                class="w-full px-3 py-2 border border-gray-300 
                rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="type" class="block text-gray-700 font-semibold mb-2">
                    Type (e.g., Flood,Earthquake): 
                </label>
                <input type="text" id="type" name="disaster_type" class="w-full px-3 py-2 
                border border-gray-300 rounded-lg">
            </div>
            <div class="mb-6">
                <label for="location" class="block text-gray-700 font-semibold mb-2">
                    Location
                </label>
                <input type="text" id="location" name="disaster_location" class="w-full px-3 py-2 
                border border-gray-300 rounded-lg">
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-500 text-white font-bold 
                py-2 px-4 rounded-lg hover:bg-blue-600">
                Add Disaster
            </button>
            </div>
        </form>
    </div>
</body>
</html>