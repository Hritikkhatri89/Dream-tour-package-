<?php
session_start();
include("../db.php");
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Delete message if requested
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM contact WHERE id=$delete_id");
    header("Location: manage-contact.php?msg=deleted");
    exit;
}

// Search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $result = mysqli_query($conn, "SELECT * FROM contact 
                                   WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR message LIKE '%$search%'
                                   ORDER BY id DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM contact ORDER BY id DESC");
}
?>

<html>
<head>
    <title>Manage Contact Messages</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 1100px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: white;
            border-radius: 15px 15px 0 0;
            font-size: 20px;
            font-weight: 600;
            text-align: center;
        }
        table {
            border-radius: 10px;
            overflow: hidden;
        }
        thead {
            background: #343a40;
            color: #fff;
        }
        tbody tr:hover {
            background: #e9f2ff;
            transition: 0.3s;
        }
        .search-box {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
        }
        .icon {
            color: #4e73df;
            font-size: 22px;
            margin-right: 8px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-envelope-fill me-2"></i> Manage Contact Messages
        </div>
        <div class="card-body">
            <!-- Search Box -->
            <form method="GET" class="search-box">
                <input type="text" name="search" value="<?= $search ?>" class="form-control" placeholder="Search by name, email or message...">
                <button type="submit" class="btn btn-primary ms-2"><i class="bi bi-search"></i> Search</button>
                <a href="manage-contact.php" class="btn btn-secondary ms-2"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </form>

            <!-- Table -->
            <table class="table table-bordered table-hover text-center">
                <thead>
                    <tr>
                        <th><i class="bi bi-hash icon"></i>ID</th>
                        <th><i class="bi bi-person-fill icon"></i>Name</th>
                        <th><i class="bi bi-envelope-open-fill icon"></i>Email</th>
                        <th><i class="bi bi-chat-square-text-fill icon"></i>Message</th>
                        <th><i class="bi bi-calendar-event-fill icon"></i>Date</th>
                        <th><i class="bi bi-gear-fill icon"></i>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
