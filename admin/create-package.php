<?php
include("../db.php");
$editMode = false;
$packageData = [
    "id" => "",
    "title" => "",
    "type" => "",
    "duration" => "",
    "price" => "",
    "highlights" => "",
    "description" => "",
    "image" => ""
];

// Check if editing
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['ajax_remove'])) {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $type = mysqli_real_escape_string($conn, $_POST["type"]);
    $duration = mysqli_real_escape_string($conn, $_POST["duration"]);
    $price = mysqli_real_escape_string($conn, $_POST["price"]);
    $highlights = isset($_POST["highlights"]) ? mysqli_real_escape_string($conn, $_POST["highlights"]) : '';
    $description = mysqli_real_escape_string($conn, $_POST["description"]);

    $image_names = [];
    if ($editMode && !empty($packageData['image'])) {
        $image_names = explode(',', $packageData['image']);
    }

    // Upload new images
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            $tmp = $_FILES['images']['tmp_name'][$key];
            $newName = time() . "_" . rand(1000,9999) . "_" . $name;
            $target = "../img/" . $newName;
            if (move_uploaded_file($tmp, $target)) {
                $image_names[] = $newName;
            }
        }
    }

    $image_str = implode(',', $image_names);

    if ($editMode) {
        $sql = "UPDATE packages SET 
                title='$title', type='$type', duration='$duration', price='$price', 
                description='$description', image='$image_str'
                WHERE id = {$packageData['id']}";
        $msg = "Package Updated Successfully";
    } else {
        $sql = "INSERT INTO packages (title, type, duration, price, description, image)
                VALUES ('$title', '$type', '$duration', '$price', '$description', '$image_str')";
        $msg = "Package Created Successfully";
    }

    mysqli_query($conn, $sql);
    echo "<script>alert('$msg'); window.location.href='view_packages.php';</script>";
    exit;
}

// Handle AJAX image remove
if (isset($_POST['ajax_remove']) && $editMode) {
    $img = $_POST['img'];
    $images = explode(',', $packageData['image']);
    if (($key = array_search($img, $images)) !== false) {
        unset($images[$key]);
        $newStr = implode(',', $images);
        mysqli_query($conn, "UPDATE packages SET image='$newStr' WHERE id={$packageData['id']}");
        echo "success"; exit;
    }
    echo "error"; exit;
}
?>

<html>
<head>
    <title><?= $editMode ? 'Edit Package' : 'Create Package'; ?></title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
         body { 
            font-family: 'Outfit', 'Poppins', sans-serif; 
            background: #f4f7fa; 
            margin: 0; 
            color: #1e293b;
            overflow-x: hidden;
         }

         /* Sidebar */

     .sidebar {
        height: 100vh;
        width: 260px;
        position: fixed;
        top: 0;
        left: 0;
        background: #0F172A; 
        padding: 20px 15px;
        color: white;
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        z-index: 1000;
        overflow-y: auto;
     }
     .sidebar h4 {
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #0fb9b1;
        font-size: 1.4rem;
        font-family: 'Outfit';
     }
     .sidebar a {
        font-family: 'Outfit', sans-serif;
        display: flex;
        align-items: center;
        padding: 10px 15px;
        margin-bottom: 10px;
        color: #94a3b8;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 0.85rem;
     }
     .sidebar a i { margin-right: 15px; font-size: 1.2rem; }
     .sidebar a:hover, .sidebar a.active {
        background: rgba(255, 255, 255, 0.05);
        color: white;
        transform: translateX(5px);
     }
     .sidebar a.active { background: #0fb9b1; color: white; }
     .sidebar a.logout { margin-top: 30px; color: #ef4444; }

         .main { margin-left: 260px; padding: 40px 50px; }
         
         .form-container {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
         }
         
         .form-title { font-weight: 700; color: #1e293b; margin-bottom: 30px; }
         
         .form-control {
            padding: 12px 18px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #1e293b;
            font-size: 0.85rem;
            transition: all 0.3s ease;
         }
         .form-control:focus {
            background: #fff;
            border-color: #0fb9b1;
            box-shadow: 0 0 0 4px rgba(15, 185, 177, 0.1);
         }
         
         .btn-submit {
            background: #0fb9b1;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(15, 185, 177, 0.2);
         }
         .btn-submit:hover { background: #0da59e; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(15, 185, 177, 0.3); }

         .thumb-container { position: relative; display:inline-block; margin:8px; }
         .thumb { width:110px; height:80px; object-fit:cover; border-radius:10px; border:2px solid #f1f5f9; transition: all 0.3s ease; }
         .thumb-container:hover .thumb { border-color: #0fb9b1; }
         .remove-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            text-align: center;
            line-height: 22px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-size: 14px;
            transition: all 0.2s ease;
         }
         .remove-btn:hover { transform: scale(1.1); background: #dc2626; }

         .btn-back {
            background: #fff;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 20px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
         }
         .btn-back:hover { background: #f8fafc; color: #1e293b; transform: translateY(-2px); }
    </style>
</head>

<body>




<!-- Sidebar -->
<div class="sidebar">
  <h4>Admin Panel</h4>
  <a href="dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i> Dashboard</a>
  <a href="create-package.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'create-package.php') ? 'active' : '' ?>"><i class="bi bi-plus-square-fill"></i> Create Package</a>
  <a href="view_packages.php" class="<?= (in_array(basename($_SERVER['PHP_SELF']), ['view_packages.php', 'edit_package.php'])) ? 'active' : '' ?>"><i class="bi bi-stack"></i> All Packages</a>
  <a href="add_itinerary.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add_itinerary.php') ? 'active' : '' ?>"><i class="bi bi-calendar-plus"></i> Add Itinerary</a>
  <a href="view_itinerary.php" class="<?= (in_array(basename($_SERVER['PHP_SELF']), ['view_itinerary.php', 'edit_itinerary.php'])) ? 'active' : '' ?>"><i class="bi bi-calendar2-week"></i> View Itineraries</a>
  <a href="add_hotel.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add_hotel.php') ? 'active' : '' ?>"><i class="bi bi-building-add"></i> Add Hotel</a>
  <a href="view_hotel.php" class="<?= (in_array(basename($_SERVER['PHP_SELF']), ['view_hotel.php', 'edit_hotel.php'])) ? 'active' : '' ?>"><i class="bi bi-buildings"></i> View Hotels</a>
  <a href="manage-user.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage-user.php') ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> Manage Users</a>
  <a href="manage-booking.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage-booking.php') ? 'active' : '' ?>"><i class="bi bi-journal-check"></i> Manage Bookings</a>
  <a href="view-contact.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'view-contact.php') ? 'active' : '' ?>"><i class="bi bi-envelope-fill"></i> Messages</a>
  <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <a href="view_packages.php" class="btn-back">
        <i class="bi bi-arrow-left me-2"></i> Back to Packages
    </a>

    <div class="col-lg-8 mx-auto form-container">
        <h3 class="form-title text-center"><?= $editMode ? "Edit Tour Package" : "Create New Package"; ?></h3>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-bold small">Package Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Magical Mauritius" required value="<?= $packageData['title']; ?>">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small">Category / Type</label>
                    <input type="text" name="type" class="form-control" placeholder="e.g. Honeymoon Special" required value="<?= $packageData['type']; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small">Duration</label>
                    <input type="text" name="duration" class="form-control" placeholder="e.g. 5 Nights / 6 Days" required value="<?= $packageData['duration']; ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">Package Price (₹)</label>
                <input type="number" name="price" class="form-control" placeholder="0.00" required value="<?= $packageData['price']; ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">Detailed Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Tell travelers about this amazing experience..." required><?= $packageData['description']; ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">Upload Images</label>
                <input type="file" name="images[]" class="form-control" multiple>
            </div>

            <?php if ($editMode && !empty($packageData['image'])): ?>
                <div class="mb-4">
                    <label class="form-label fw-bold small">Current Images (Click × to delete)</label><br>
                    <div id="existing-images" class="p-2 border rounded bg-light">
                        <?php foreach (explode(',', $packageData['image']) as $img): ?>
                            <?php if(!empty(trim($img))): ?>
                                <div class="thumb-container" data-img="<?= $img ?>">
                                    <img src="../img/<?= $img ?>" class="thumb">
                                    <div class="remove-btn" onclick="removeImage('<?= $img ?>', this)">×</div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit w-100 mt-2">
                <i class="bi <?= $editMode ? 'bi-check-circle-fill' : 'bi-plus-circle-fill'; ?> me-2"></i>
                <?= $editMode ? "Save Changes" : "Publish Package"; ?>
            </button>
        </form>
    </div>
</div>

<script>
function removeImage(imgName, el) {
    if(confirm('Are you sure you want to remove this image from this package?')) {
        var formData = new FormData();
        formData.append('ajax_remove', 1);
        formData.append('img', imgName);

        fetch('', { method:'POST', body: formData })
        .then(res => res.text())
        .then(res => {
            if(res.trim()=='success') {
                el.parentElement.remove();
            } else {
                alert('Failed to remove image');
            }
        });
    }
}
</script>

</body>
</html>



