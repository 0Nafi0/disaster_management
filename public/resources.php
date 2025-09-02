<?php
require_once '../includes/db_connect.php';

// Get resources with advanced aggregation
$sql = "SELECT 
    r.*,
    CASE 
        WHEN quantity = 0 THEN 'Out of Stock'
        WHEN quantity < 10 THEN 'Low Stock'
        ELSE 'In Stock'
    END as stock_status
FROM Resource r
ORDER BY 
    CASE 
        WHEN quantity = 0 THEN 1
        WHEN quantity < 10 THEN 2
        ELSE 3
    END,
    name ASC";
$result = $conn->query($sql);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(*) as total_resources,
    COUNT(DISTINCT unit) as unique_units,
    SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
    SUM(CASE WHEN quantity < 10 AND quantity > 0 THEN 1 ELSE 0 END) as low_stock
FROM Resource";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources Management</title>
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
                $isError = strpos(strtolower($_GET['msg']), 'error') !== false;
                $class = $isError ? 
                    "bg-red-100 border border-red-400 text-red-700" : 
                    "bg-green-100 border border-green-400 text-green-700";
                ?>
                <div class="<?php echo $class; ?> px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <!-- Resource Statistics -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-blue-700">Total Resources</h3>
                    <p class="text-2xl font-bold text-blue-800"><?php echo $stats['total_resources']; ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-green-700">Unique Units</h3>
                    <p class="text-2xl font-bold text-green-800"><?php echo $stats['unique_units']; ?></p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-red-700">Out of Stock</h3>
                    <p class="text-2xl font-bold text-red-800"><?php echo $stats['out_of_stock']; ?></p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg">
                    <h3 class="text-sm font-semibold text-yellow-700">Low Stock</h3>
                    <p class="text-2xl font-bold text-yellow-800"><?php echo $stats['low_stock']; ?></p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Resources</h1>
                <a href="add_resource.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600">
                    + Add Resource
                </a>
            </div>

            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 text-left">Resource Name</th>
                        <th class="py-3 px-4 text-left">Quantity</th>
                        <th class="py-3 px-4 text-left">Unit</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($row['unit']); ?></td>
                                <td class="py-3 px-4">
                                    <?php
                                    $status_color = match($row['stock_status']) {
                                        'Out of Stock' => 'text-red-600',
                                        'Low Stock' => 'text-yellow-600',
                                        default => 'text-green-600'
                                    };
                                    ?>
                                    <span class="<?php echo $status_color; ?> font-semibold">
                                        <?php echo htmlspecialchars($row['stock_status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="edit_resource.php?id=<?php echo $row['id']; ?>" 
                                       class="text-blue-500 hover:text-blue-700">Edit</a>
                                    <a href="delete_resource.php?id=<?php echo $row['id']; ?>" 
                                       class="text-red-500 hover:text-red-700 ml-4"
                                       onclick="return confirm('Are you sure you want to delete this resource?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No resources recorded yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
