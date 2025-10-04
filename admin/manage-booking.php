<?php
session_start();
include("../db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM bookings WHERE id = $id");
    header("Location: manage-booking.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = intval($_POST['edit_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE bookings SET status='$new_status' WHERE id=$edit_id");
    header("Location: manage-booking.php");
    exit;
}

$rs = mysqli_query($conn,
    "SELECT b.id, u.name, p.title, b.status 
     FROM bookings b
     JOIN users u ON b.user_id = u.id
     JOIN packages p ON b.package_id = p.id"
);
?>
<html>
<head>
    <title>Manage Bookings</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(to right, #f0f9ff, #e0f7fa);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .table thead {
            background-color: #e9ecef;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(13,110,253,0.05);
        }
        .page-title {
            color: #0d6efd;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
        }
        .btn-delete {
            background: linear-gradient(45deg, #ff4b2b, #ff416c);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: 0.3s;
        }
        .btn-delete:hover {
            background: linear-gradient(45deg, #e03e25, #e0355c);
            transform: scale(1.05);
        }
        .btn-edit {
            background: linear-gradient(45deg, #00b09b, #96c93d);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: 0.3s;
        }
        .btn-edit:hover {
            background: linear-gradient(45deg, #00997e, #7dbb2f);
            transform: scale(1.05);
        }
        .badge {
            font-size: 0.85rem;
            padding: 6px 10px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card p-4">
        <h4 class="page-title mb-4"><i class="bi bi-journal-check"></i> Manage Bookings</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($rs) > 0): ?>
                        <?php while($b = mysqli_fetch_assoc($rs)): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['name']) ?></td>
                            <td><?= htmlspecialchars($b['title']) ?></td>
                            <td>
                                <div class="badge <?= ($b['status'] == 'Confirmed') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                    <?= $b['status'] ?>
                                </div>
                            </td>
                            <td>
                                <button class="btn-edit" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editModal" 
                                        data-id="<?= $b['id'] ?>" 
                                        data-status="<?= $b['status'] ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <a href="?del=<?= $b['id'] ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this booking?')">
                                   <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No bookings found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Booking Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="mb-3">
            <label>Status:</label>
            <select name="status" id="edit_status" class="form-control">
                <option value="Pending">Pending</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_status').value = this.dataset.status;
    });
});
</script>
</body>
</html>
