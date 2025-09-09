<?php
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $need_id = isset($_POST['need_id']) ? intval($_POST['need_id']) : 0;
    $fulfill_quantity = isset($_POST['fulfill_quantity']) ? intval($_POST['fulfill_quantity']) : 0;

    if (!$need_id || $fulfill_quantity <= 0) {
        header("Location: manage_victim_needs.php?error=Invalid input");
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get need details
        $need_sql = "SELECT n.*, r.quantity as available_quantity 
                    FROM needs n
                    JOIN Resource r ON n.resource_id = r.id
                    WHERE n.id = ?";
        $need_stmt = $conn->prepare($need_sql);
        $need_stmt->bind_param("i", $need_id);
        $need_stmt->execute();
        $need = $need_stmt->get_result()->fetch_assoc();

        if (!$need) {
            throw new Exception("Resource need not found");
        }

        // Check if fulfillment quantity is valid
        $remaining_need = $need['quantity'] - $need['fulfilled_quantity'];
        if ($fulfill_quantity > $remaining_need) {
            throw new Exception("Fulfillment quantity cannot exceed remaining need");
        }

        if ($fulfill_quantity > $need['available_quantity']) {
            throw new Exception("Not enough resources available");
        }

        // Update the need
        $new_fulfilled_quantity = $need['fulfilled_quantity'] + $fulfill_quantity;
        $status = $new_fulfilled_quantity >= $need['quantity'] ? 'fulfilled' : 'partially_fulfilled';

        $update_need_sql = "UPDATE needs 
                           SET fulfilled_quantity = ?, 
                               status = ?
                           WHERE id = ?";
        $update_need_stmt = $conn->prepare($update_need_sql);
        $update_need_stmt->bind_param("isi", $new_fulfilled_quantity, $status, $need_id);
        
        if (!$update_need_stmt->execute()) {
            throw new Exception("Error updating need status");
        }

        // Update resource quantity
        $new_resource_quantity = $need['available_quantity'] - $fulfill_quantity;
        $update_resource_sql = "UPDATE Resource 
                              SET quantity = ? 
                              WHERE id = ?";
        $update_resource_stmt = $conn->prepare($update_resource_sql);
        $update_resource_stmt->bind_param("ii", $new_resource_quantity, $need['resource_id']);
        
        if (!$update_resource_stmt->execute()) {
            throw new Exception("Error updating resource quantity");
        }

        $conn->commit();
        header("Location: manage_victim_needs.php?success=Resource need fulfilled successfully");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_victim_needs.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}

// Display form for fulfilling need
$need_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$need_id) {
    header("Location: manage_victim_needs.php?error=Invalid request");
    exit;
}

$need_sql = "SELECT n.*, 
                    v.name as victim_name,
                    r.name as resource_name,
                    r.quantity as available_quantity,
                    r.unit
             FROM needs n
             JOIN Victim v ON n.victim_id = v.id
             JOIN Resource r ON n.resource_id = r.id
             WHERE n.id = ?";
$need_stmt = $conn->prepare($need_sql);
$need_stmt->bind_param("i", $need_id);
$need_stmt->execute();
$need = $need_stmt->get_result()->fetch_assoc();

if (!$need) {
    header("Location: manage_victim_needs.php?error=Resource need not found");
    exit;
}

$remaining_need = $need['quantity'] - $need['fulfilled_quantity'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fulfill Resource Need</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Fulfill Resource Need</h1>
                <a href="manage_victim_needs.php" class="text-gray-600 hover:text-gray-800">Back</a>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded">
                <p><strong>Victim:</strong> <?php echo htmlspecialchars($need['victim_name']); ?></p>
                <p><strong>Resource:</strong> <?php echo htmlspecialchars($need['resource_name']); ?></p>
                <p><strong>Total Needed:</strong> <?php echo $need['quantity'] . ' ' . $need['unit']; ?></p>
                <p><strong>Already Fulfilled:</strong> <?php echo $need['fulfilled_quantity'] . ' ' . $need['unit']; ?></p>
                <p><strong>Remaining Need:</strong> <?php echo $remaining_need . ' ' . $need['unit']; ?></p>
                <p><strong>Available Quantity:</strong> <?php echo $need['available_quantity'] . ' ' . $need['unit']; ?></p>
            </div>

            <form action="fulfill_resource_need.php" method="POST" class="space-y-4">
                <input type="hidden" name="need_id" value="<?php echo $need_id; ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity to Fulfill</label>
                    <input type="number" name="fulfill_quantity" required
                           min="1" max="<?php echo min($remaining_need, $need['available_quantity']); ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border">
                    <p class="mt-1 text-sm text-gray-500">
                        Maximum: <?php echo min($remaining_need, $need['available_quantity']) . ' ' . $need['unit']; ?>
                    </p>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600">
                    Fulfill Need
                </button>
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
