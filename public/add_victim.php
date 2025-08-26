<?php
require_once '../includes/db_connect.php';
$disasters = $conn->query("SELECT id, name FROM disaster");
$camps = $conn->query("SELECT id, name FROM Relief_Camp");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Add Victim</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="container mx-auto mt-10 p-8 bg-white max-w-md rounded-lg shadow-md">
    <h1 class="text-xl font-bold mb-4">Add New Victim</h1>
    <form action="process_add_victim.php" method="POST">
      <div class="mb-4">
        <label class="block mb-2">Victim Name</label>
        <input type="text" name="victim_name" class="w-full border px-3 py-2 rounded" required>
      </div>

      <div class="mb-4">
        <label class="block mb-2">Disaster</label>
        <select name="disaster_id" class="w-full border px-3 py-2 rounded">
          <?php while($d = $disasters->fetch_assoc()): ?>
            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-2">Relief Camp</label>
        <select name="camp_id" class="w-full border px-3 py-2 rounded">
          <?php while($c = $camps->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Victim</button>
    </form>
  </div>
</body>
</html>
