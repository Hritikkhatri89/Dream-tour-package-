<?php
include("../db.php");
$editMode = false;
$packageData = [
    "title" => "",
    "type" => "",
    "duration" => "",
    "price" => "",
    "highlights" => "",
    "description" => "",
    "image" => ""
];

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM packages WHERE id = $id");
    if (mysqli_num_rows($result) > 0) {
        $packageData = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Package not found!'); window.location='view_packages.php';</script>";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $type = mysqli_real_escape_string($conn, $_POST["type"]);
    $duration = mysqli_real_escape_string($conn, $_POST["duration"]);
    $price = mysqli_real_escape_string($conn, $_POST["price"]);
    $highlights = mysqli_real_escape_string($conn, $_POST["highlights"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);

    $img_name = $packageData['image']; // old image by default
    if (!empty($_FILES["image"]["name"])) {
        $img_name = $_FILES["image"]["name"];
        $tmp_name = $_FILES["image"]["tmp_name"];
        move_uploaded_file($tmp_name, "../img/" . $img_name);
    }

    if ($editMode) {
        $sql = "UPDATE packages SET 
                title='$title', type='$type', duration='$duration', price='$price', 
                highlights='$highlights', description='$description', image='$img_name'
                WHERE id = {$packageData['id']}";
    } else {
        $sql = "INSERT INTO packages (title, type, duration, price, highlights, description, image)
                VALUES ('$title', '$type', '$duration', '$price', '$highlights', '$description', '$img_name')";
    }

    if (mysqli_query($conn, $sql)) {
        $msg = $editMode ? 'Package Updated Successfully' : 'Package Created Successfully';
        echo "<script>alert('$msg'); window.location.href='view_packages.php';</script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


<html>
<head>
<title><?= $editMode ? 'Edit Package' : 'Create Package'; ?></title>
<link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">

<style>
    body { background:#87b4c4; }
    .form-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); padding: 30px; transition: transform 0.2s ease-in-out; }
    .form-card:hover { transform: translateY(-2px); }
    .form-label { font-weight: 600; color: #333; }
    .form-control, .form-select, textarea { border-radius: 10px; border: 1px solid #ccc; padding: 10px 12px; }
    .btn-primary { border-radius: 10px; padding: 10px 20px; font-size: 1.05rem; font-weight: 600; }
    h2 { font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 1.8rem; letter-spacing: 1px; }
</style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-card">
                <h2 class="mb-4 text-primary text-center"><?= $editMode ? 'Edit Package' : 'Create Package'; ?></h2>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Package Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($packageData['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Package Type</label>
                        <input type="text" name="type" class="form-control" value="<?= htmlspecialchars($packageData['type']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control" value="<?= htmlspecialchars($packageData['duration']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (₹)</label>
                        <input type="text" name="price" class="form-control" value="<?= htmlspecialchars($packageData['price']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Highlights</label>
                        <textarea name="highlights" class="form-control" rows="3" required><?= htmlspecialchars($packageData['highlights']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($packageData['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control">
                        <?php if ($editMode && !empty($packageData['image'])): ?>
                            <div class="mt-2">
                                <img src="../img/<?= $packageData['image']; ?>" alt="Current Image" style="width:100px;border-radius:8px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary w-50">
                            <?= $editMode ? 'Update Package' : 'Create Package'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
