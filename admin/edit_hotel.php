<?php
include("../db.php");

// ====== GET HOTEL ID ======
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Hotel ID");
}

$hotel_id = intval($_GET['id']);

// ====== FETCH HOTEL DATA ======
$stmt = $conn->prepare("SELECT * FROM hotels WHERE id=?");
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$hotel = $result->fetch_assoc();

if (!$hotel) {
    die("Hotel not found");
}

// ====== FORM SUBMIT ======
if (isset($_POST['submit'])) {

    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $rating = $_POST['rating'];
    $price = $_POST['price'];
    $food = $_POST['food'];
    $policy = $_POST['policy'];

    $facilities = isset($_POST['facilities']) 
        ? implode(", ", $_POST['facilities']) 
        : "";

    // ====== HANDLE IMAGES ======
    $imageNames = !empty($hotel['images']) 
        ? explode(",", $hotel['images']) 
        : [];

    // Remove selected images
    if (isset($_POST['remove_images'])) {
        foreach ($_POST['remove_images'] as $rmImg) {
            if (($key = array_search($rmImg, $imageNames)) !== false) {
                unset($imageNames[$key]);
                if (file_exists("../Hotel/" . $rmImg)) {
                    unlink("../Hotel/" . $rmImg);
                }
            }
        }
    }

    // Add new images
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $filename) {
            $newName = time() . "_" . basename($filename);
            $targetPath = "../Hotel/" . $newName;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetPath)) {
                $imageNames[] = $newName;
            }
        }
    }

    $imageList = implode(",", $imageNames);

    // ====== UPDATE HOTEL ======
    $stmt = $conn->prepare("UPDATE hotels SET 
        name=?, 
        category=?, 
        description=?, 
        location=?, 
        rating=?, 
        price=?, 
        images=?, 
        facilities=?, 
        food=?, 
        policy=? 
        WHERE id=?");

    $stmt->bind_param(
        "sssssdssssi",
        $name,
        $category,
        $description,
        $location,
        $rating,
        $price,
        $imageList,
        $facilities,
        $food,
        $policy,
        $hotel_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Hotel updated successfully!'); window.location.href='view_hotel.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

$hotel_facilities = !empty($hotel['facilities']) 
    ? explode(", ", $hotel['facilities']) 
    : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $hotel_id ? "Edit" : "Add" ?> Hotel</title>
<link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
     
     .form-label { font-weight: 500; color: #64748b; font-size: 0.85rem; margin-bottom: 8px; }

     .form-control, .form-select {
        padding: 12px 18px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #1e293b;
        font-size: 0.85rem;
        transition: all 0.3s ease;
     }
     .form-control:focus, .form-select:focus {
        background: #fff;
        border-color: #0fb9b1;
        box-shadow: 0 0 0 4px rgba(15, 185, 177, 0.1);
     }
     
     .btn-submit {
        background: #0fb9b1;
        color: white;
        border: none;
        padding: 14px 40px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(15, 185, 177, 0.2);
     }
     .btn-submit:hover { background: #0da59e; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(15, 185, 177, 0.3); }

     .preview-container { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px; }
     .preview-item { position: relative; }
     .preview-container img { width: 120px; height: 90px; object-fit: cover; border-radius: 12px; border: 2px solid #f1f5f9; transition: 0.3s; }

     .facility-group { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 15px; padding: 20px; margin-bottom: 25px; }
     .facility-group h5 { color: #1e293b; font-weight: 700; margin-bottom: 15px; font-size: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
     .facility-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
     .facility-list div { font-size: 0.9rem; color: #64748b; display: flex; align-items: center; }
     .facility-list input { margin-right: 10px; width: 18px; height: 18px; accent-color: #0fb9b1; }

     .remove-btn { 
        position: absolute; 
        top: -8px; 
        right: -8px; 
        background: #ef4444; 
        color: white; 
        border: none; 
        border-radius: 50%; 
        width: 24px; 
        height: 24px; 
        cursor: pointer; 
        font-weight: bold; 
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
     }

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
    <a href="view_hotel.php" class="btn-back">
        <i class="bi bi-arrow-left me-2"></i> Back to Hotels
    </a>

    <div class="form-container col-lg-10 mx-auto">
        <h2 class="form-title text-center">✏️ Update Hotel Details</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">HOTEL NAME</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($hotel['name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">CATEGORY / STAR RATING</label>
                    <select name="category" class="form-select" required>
                        <?php
                        $categories = ["3 Star","4 Star","5 Star"];
                        foreach($categories as $cat){
                            $selected = ($hotel['category'] ?? '')==$cat ? 'selected' : '';
                            echo "<option value='$cat' $selected>$cat</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">ABOUT THE HOTEL</label>
                    <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($hotel['description'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">DINING & CUISINE</label>
                    <textarea name="food" class="form-control" rows="3"><?= htmlspecialchars($hotel['food'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">CHECK-IN / CHECK-OUT POLICIES</label>
                    <textarea name="policy" class="form-control" rows="3"><?= htmlspecialchars($hotel['policy'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">LOCATION ADDRESS</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($hotel['location'] ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">USER RATING (1-5)</label>
                    <input type="number" name="rating" step="0.1" min="1" max="5" class="form-control" value="<?= htmlspecialchars($hotel['rating'] ?? '') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">PRICE PER NIGHT (₹)</label>
                    <input type="number" name="price" class="form-control" value="<?= htmlspecialchars($hotel['price'] ?? '') ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">HOTEL GALLERY (Existing & New)</label>
                    <input type="file" name="images[]" id="images" class="form-control" multiple>
                    <div id="preview" class="preview-container">
                        <?php
                        if(!empty($hotel['images'])){
                            foreach(explode(",", $hotel['images']) as $img){
                                echo "<div class='preview-item'>
                                        <img src='../Hotel/$img'>
                                        <button type='button' class='remove-btn' onclick='removeExistingImage(this, \"$img\")'>&times;</button>
                                      </div>";
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Facilities Section -->
                <div class="col-12 mt-5">
                    <h5 class="fw-bold mb-4 text-dark border-start border-4 border-primary ps-3">Update Facilities</h5>

                    <?php
                    $facilities_group = [
                        "3 Star Essentials" => [
                            "Clean and Comfortable Rooms", "Air Conditioning", "Free WiFi", 
                            "Complimentary Breakfast", "Room Service (Limited Hours)", "24-hour Front Desk", 
                            "Daily Housekeeping", "In-house Restaurant or Café", "Free Parking", 
                            "Television with Cable Channels", "Laundry Service (Paid)"
                        ],
                        "4 Star Premiums" => [
                            "Spacious and Modern Rooms", "Air Conditioning with Climate Control", "High-speed WiFi", 
                            "24-hour Room Service", "Multi-cuisine Restaurant and Bar", "Swimming Pool", 
                            "Fitness Centre / Gym", "Banquet Hall / Meeting Rooms", "Laundry and Dry-cleaning Service", 
                            "Free On-site Parking", "Concierge Service"
                        ],
                        "5 Star Luxuries" => [
                            "Luxury Rooms and Suites", "24-hour Butler Service", "Fine Dining Restaurants and Bars", 
                            "Spa and Wellness Centre", "Indoor or Outdoor Swimming Pool", "Fitness Centre / Yoga Room", 
                            "Valet Parking", "High-speed WiFi and Smart TVs", "Conference Halls / Event Venues", 
                            "Complimentary Breakfast Buffet", "24-hour Concierge", "Daily Housekeeping and Turndown Service", 
                            "Mini Bar and Coffee Machine in Room"
                        ]
                    ];

                    foreach ($facilities_group as $group_name => $facilities_list) {
                        echo "<div class='facility-group'>";
                        echo "<h5>$group_name</h5>";
                        echo "<div class='facility-list'>";
                        foreach ($facilities_list as $fac) {
                            $checked = in_array($fac, $hotel_facilities) ? 'checked' : '';
                            echo "<div><input type='checkbox' name='facilities[]' value='$fac' $checked> $fac</div>";
                        }
                        echo "</div></div>";
                    }
                    ?>
                </div>

                <div class="col-12 text-center mt-5">
                    <button type="submit" name="submit" class="btn-submit">
                        <i class="bi bi-save me-2"></i> Update Hotel Information
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
<script>
function removeExistingImage(btn, filename){
    const parent = btn.parentElement;
    parent.remove();

    // create hidden input to remove image from server on submit
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'remove_images[]';
    input.value = filename;
    document.querySelector('form').appendChild(input);
}

// Preview newly selected images
document.getElementById('images').addEventListener('change', function(event) {
    const preview = document.getElementById('preview');
    Array.from(event.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.innerHTML = `<img src="${e.target.result}">
                             <button type="button" class="remove-btn" onclick="this.parentElement.remove()">&times;</button>`;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
});
</script>
</body>
</html>



