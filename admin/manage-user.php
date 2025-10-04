<?php
session_start();
include("../db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$res = mysqli_query($conn,
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
        }
        .status-confirmed {
            color: green;
            font-weight: 500;
        }
        .status-pending {
            color: #d39e00;
            font-weight: 500;
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
                        <th style="width: 60px;">ID</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res) > 0): ?>
                        <?php while($b = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['name']) ?></td>
                            <td><?= htmlspecialchars($b['title']) ?></td>
                            <td class="<?= ($b['status'] == 'Confirmed') ? 'status-confirmed' : 'status-pending'; ?>">
                                <?= htmlspecialchars($b['status']) ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No bookings found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
