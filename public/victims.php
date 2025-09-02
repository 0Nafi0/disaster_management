<?php
require_once '../includes/db_connect.php';

$sql = "SELECT v.id, v.name, d.name AS disaster_name, c.name AS camp_name
        FROM Victim v
        JOIN disaster d ON v.disaster_id = d.id
        JOIN Relief_Camp c ON v.camp_id = c.id
        ORDER BY v.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Victims</title>
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
        <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Victims</h1>
      <a href="add_victim.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">+ Add Victim</a>
    </div>

    <table class="min-w-full bg-white border">
      <thead>
        <tr>
          <th class="py-2 px-4 border">Name</th>
          <th class="py-2 px-4 border">Disaster</th>
          <th class="py-2 px-4 border">Relief Camp</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['disaster_name']) ?></td>
            <td class="border px-4 py-2"><?= htmlspecialchars($row['camp_name']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
