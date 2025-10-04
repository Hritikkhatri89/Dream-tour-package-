<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// User ka data fetch karna DB se
$query = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Bookings fetch karna
$bookQuery = "SELECT * FROM bookings WHERE user_id='$user_id'";
$bookings = mysqli_query($conn, $bookQuery);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
</head>
<body>
    <h2>Welcome, <?php echo $user['name']; ?> 👋</h2>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Phone: <?php echo !empty($user['phone']) ? $user['phone'] : "Not Added"; ?></p>

    <h3>Your Bookings</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Package</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($bookings)){ ?>
        <tr>
            <td><?php echo $row['package_name']; ?></td>
            <td><?php echo $row['date']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
