<?php
session_start();
include("../db.php");

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch itineraries with package info
$itineraries = mysqli_query($conn, "
    SELECT i.id, p.title AS package_title, i.day_or_night, i.plan
    FROM itineraries i
    JOIN packages p ON i.package_id = p.id
    ORDER BY i.package_id, i.id ASC
");
?>

<html>
<head>
    <title>View Itineraries - Admin</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family:'Poppins', sans-serif; background:#87b4c4; margin:0; }
        .container { margin-top:50px; }
        table { background:#fff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
        th, td { text-align:center; vertical-align:middle !important; }
        .title { font-weight:600; color:#1d3557; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4 title">View Itineraries</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Package Name</th>
                <th>Day/Night</th>
                <th>Plan Details</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($itineraries)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['package_title']) ?></td>
                    <td><?= htmlspecialchars($row['day_or_night']) ?></td>
                    <td><?= htmlspecialchars($row['plan']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="itinerary.php" class="btn btn-secondary mt-3">Back to Manage Itineraries</a>
</div>
</body>
</html>

