<?php
require_once('../includes/db_connect.php');

// Fetch all reports with related information
$query = "SELECT r.*, d.name as disaster_name, GROUP_CONCAT(v.name) as volunteer_names 
          FROM report r 
          LEFT JOIN disaster d ON r.disaster_id = d.id 
          LEFT JOIN reports rp ON r.id = rp.report_id 
          LEFT JOIN volunteer v ON rp.volunteer_id = v.id 
          GROUP BY r.id";
$result = mysqli_query($conn, $query);

// Fetch all disasters for the dropdown
$disaster_query = "SELECT * FROM disaster";
$disaster_result = mysqli_query($conn, $disaster_query);

// Fetch all volunteers for the dropdown
$volunteer_query = "SELECT * FROM volunteer";
$volunteer_result = mysqli_query($conn, $volunteer_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Reports Management</h2>
        
        <!-- Add Report Button -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addReportModal">
            Add New Report
        </button>

        <!-- Reports Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Disaster</th>
                    <th>Volunteers</th>
                    <th>Status</th>
                    <th>Report Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><?php echo $row['disaster_name']; ?></td>
                        <td><?php echo $row['volunteer_names']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['report_date']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editReportModal"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-type="<?php echo $row['type']; ?>"
                                    data-location="<?php echo $row['location']; ?>"
                                    data-disaster="<?php echo $row['disaster_id']; ?>"
                                    data-status="<?php echo $row['status']; ?>">
                                Edit
                            </button>
                            <a href="delete_report.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this report?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Add Report Modal -->
        <div class="modal fade" id="addReportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="process_add_report.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <input type="text" class="form-control" id="type" name="type" required>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            <div class="mb-3">
                                <label for="disaster_id" class="form-label">Disaster</label>
                                <select class="form-control" id="disaster_id" name="disaster_id" required>
                                    <?php 
                                    mysqli_data_seek($disaster_result, 0);
                                    while($disaster = mysqli_fetch_assoc($disaster_result)) { 
                                    ?>
                                        <option value="<?php echo $disaster['id']; ?>">
                                            <?php echo $disaster['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="volunteers" class="form-label">Volunteers</label>
                                <select class="form-control" id="volunteers" name="volunteers[]" multiple required>
                                    <?php 
                                    mysqli_data_seek($volunteer_result, 0);
                                    while($volunteer = mysqli_fetch_assoc($volunteer_result)) { 
                                    ?>
                                        <option value="<?php echo $volunteer['id']; ?>">
                                            <?php echo $volunteer['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Report Modal -->
        <div class="modal fade" id="editReportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="process_edit_report.php" method="POST">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_type" class="form-label">Type</label>
                                <input type="text" class="form-control" id="edit_type" name="type" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="edit_location" name="location" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_disaster_id" class="form-label">Disaster</label>
                                <select class="form-control" id="edit_disaster_id" name="disaster_id" required>
                                    <?php 
                                    mysqli_data_seek($disaster_result, 0);
                                    while($disaster = mysqli_fetch_assoc($disaster_result)) { 
                                    ?>
                                        <option value="<?php echo $disaster['id']; ?>">
                                            <?php echo $disaster['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate edit modal with report data
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_type').value = this.dataset.type;
                document.getElementById('edit_location').value = this.dataset.location;
                document.getElementById('edit_disaster_id').value = this.dataset.disaster;
                document.getElementById('edit_status').value = this.dataset.status;
            });
        });
    </script>
</body>
</html>
