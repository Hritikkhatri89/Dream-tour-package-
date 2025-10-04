<?php
session_start();
include("db.php");

$msg = "";
$name = "";
$phone = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $insert = mysqli_query($conn, "INSERT INTO contact (name, phone) VALUES ('$name', '$phone')");
    if ($insert) {
        $msg = " Thank you! Your details have been submitted.";
        $name = "";
        $phone = "";
    } else {
        $msg = " Something went wrong.";
    }
}
?>

<html>
<head>
  <title>Dream Tour & Travel</title>
   <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f9f9f9, #e0f7fa);
      font-family: 'Segoe UI', sans-serif;
    }
    .contact-section {
      max-width: 600px;
      margin: 100px auto;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .contact-section h2 {
      color: #181e4b;
      margin-bottom: 20px;
    }
    .btn-submit {
      background-color: #0d6efd;
      color: #fff;
    }
    .btn-submit:hover {
      background-color: #0b5ed7;
    }
  </style>
</head>
<body>

<!-- Get in Touch Section -->
<div class="contact-section">
  <h2 class="text-center">Get in Touch</h2>

  <?php if ($msg): ?>
    <div class="alert alert-info text-center"><?= $msg ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Your Name</label>
      <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($name) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Phone Number</label>
      <input type="text" name="phone" class="form-control" required pattern="[0-9]{10}" placeholder="10-digit number" value="<?= htmlspecialchars($phone) ?>">
    </div>
    <button type="submit" name="submit_contact" class="btn btn-submit w-100">Submit</button>
  </form>
</div>

</body>
</html>
