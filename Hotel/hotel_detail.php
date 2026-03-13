<?php
session_start();
include("db.php");

// ✅ Package ID check
if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
$package_sql = mysqli_query($conn, "SELECT title, duration, price FROM packages WHERE id=$pid");
  $package = mysqli_fetch_assoc($package_sql);
    if (!$package) die("Package not found for booking.");
} else {
    die("Invalid Package ID for booking.");
}

// ✅ Hotel ID check
if (isset($_GET['hid'])) {
    $hid = intval($_GET['hid']);
    $sql = mysqli_query($conn, "SELECT * FROM hotels WHERE id='$hid'");
    $hotel = mysqli_fetch_assoc($sql);
    if (!$hotel) die("<h3>Hotel not found!</h3>");
} else {
    die("<h3>Hotel not found!</h3>");
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['uid'])) {
        echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
        exit;
    }

    $uid = $_SESSION['uid'];
    $pid_post = intval($_POST['pid']); 
    $hid_post = intval($_POST['hid']);
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);
    $rooms = intval($_POST['rooms']);

    $package_name = mysqli_real_escape_string($conn, $package['title']);
    $hotel_name = mysqli_real_escape_string($conn, $hotel['name']);
    $hotel_star = mysqli_real_escape_string($conn, $hotel['category']); 
    $hotel_image = mysqli_real_escape_string($conn, $hotel['images']);
    $hotel_price = $_POST['final_price'];
$insert = "INSERT INTO bookings 
    (user_id, package_id, adults, hotel_id, package_name, hotel_name, hotel_star, image, children, rooms, price, checkin, checkout, status, booked_on)
    VALUES 
    ('$uid', '$pid_post', '$adults', '$hid_post', '$package_name', '$hotel_name', '$hotel_star', '$hotel_image', '$children', '$rooms', '$hotel_price', '$checkin', '$checkout', 'Pending', NOW())";

    if (mysqli_query($conn, $insert)) {
        $booking_id = mysqli_insert_id($conn);
        header("Location: booking_summary.php?bid=" . $booking_id);
        exit;
    } else {
        $error = "Booking failed. MySQL Error: " . mysqli_error($conn);
    }
}

$minDate = date('Y-m-d'); 
?>

<html>
<head>
<title><?php echo $hotel['name']; ?> - Details</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="instyle.css">

<style>
.tabs { display:flex; margin-top:20px; border-bottom:2px solid #ddd; }
.tab { padding:10px 18px; cursor:pointer; border:none; border-bottom:2px solid transparent; color:#555; font-weight:500; }
.tab.active { border-bottom:2px solid #0d6efd; color:#0d6efd; font-weight:600; }
.tab-content { margin-top:15px; }
.smooth-box {
    max-height: 120px;
    overflow: hidden;
    transition: max-height 0.5s ease-in-out;
}
.smooth-box.open {
    max-height: 600px;
}
</style>
</head>

<body style="background:#fff;">

<?php
$logged_user_name = '';
if(isset($_SESSION['uid'])) {
    $u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
    if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];
}
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
  <div class="container">
    
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo.png" alt="Logo">
      <span class="fs-4">Dream Tour & Travel</span>
    </a>

    <!-- Mobile Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- CENTER NAVIGATION -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link active" href="tourpackage.php">Destination</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
      </ul>

      <!-- RIGHT LOGIN -->
      <div class="d-flex align-items-center">
        <?php if(isset($_SESSION['uid'])): ?>
          <div class="dropdown">
            <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown" style="color:#2b3a55 !important;">
                <i class="bi bi-person-circle fs-5"></i> <?php echo htmlspecialchars($logged_user_name ?: 'My Account'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="mybookings.php">My Bookings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-login-premium">
             <i class="bi bi-person-circle fs-5"></i> Log In
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</nav>

<div class="container my-5">

<?php if($success): ?>
<div class="alert alert-success text-center fw-semibold rounded-3">
    ✅ Your Hotel & Package booking has been successfully placed!
</div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger text-center fw-semibold rounded-3">
    <?= $error; ?>
</div>
<?php endif; ?>

<div class="card shadow border-0 rounded-4 p-3">
    <div class="row g-4">

        <!-- LEFT COLUMN (Images + Hotel Info) -->
        <div class="col-md-6">

            <?php $images = explode(",", $hotel['images']); ?>

            <img id="mainImage" src="Hotel/<?= trim($images[0]); ?>" 
                 class="w-100 mb-3 rounded-4"
                 style="height:400px; object-fit:cover;">

            <div class="d-flex flex-wrap gap-2">
            <?php foreach($images as $img): ?>
                <img src="Hotel/<?= trim($img); ?>" 
                     onclick="document.getElementById('mainImage').src=this.src;" 
                     style="width:90px;height:70px;object-fit:cover;border-radius:8px;cursor:pointer;">
            <?php endforeach; ?>
            </div>

            <div class="text-center mt-3">
                <p class="fw-semibold text-info mb-1">
                    📅 <?= $package['duration']; ?>
                </p>
                <h4 class="fw-bold text-primary"><?= $hotel['name']; ?></h4>
                <p class="text-muted"><?= $hotel['location']; ?></p>
                <p>⭐ <?= $hotel['rating']; ?> / 5</p>
                <p class="fw-bold text-success">₹<?= $hotel['price']; ?> <small>per package</small></p>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-md-6">
<!-- FACILITIES SECTION -->
<?php if(!empty($hotel['facilities'])): ?>
<div class="mt-3 p-3 border rounded-4 bg-light">

    <h5 class="fw-bold mb-3 text-primary">Select Facilities</h5>

    <div id="facilitiesContainer" class="smooth-box">
        <div class="d-flex flex-column gap-2">

        <?php 
        $facility_costs = [
            'Wi-Fi' => 200,
            'Breakfast' => 300,
            'Parking' => 150,
            'Swimming Pool' => 400,
            'Gym' => 250
        ];

        $facilities = explode(",", $hotel['facilities']);
        foreach ($facilities as $f): 
            $f = trim($f);
            $cost = $facility_costs[$f] ?? 100;
        ?>
            <div class="form-check">
                <input class="form-check-input facility-check" type="checkbox"
                       value="<?= $f ?>" data-cost="<?= $cost ?>" checked>
                <label class="form-check-label">
                    <?= $f ?> (+₹<?= $cost ?>)
                </label>
            </div>
        <?php endforeach; ?>

        </div>
    </div>

    <div class="text-center mt-2">
        <button type="button" id="toggleFacilities" class="btn btn-outline-primary btn-sm rounded-pill">
            + See More
        </button>
    </div>

</div>
<?php endif; ?>


<!-- TOTAL PRICE -->
<div class="mt-3 text-end">
    <h5>💰 <strong>Total Price: ₹<span id="totalPrice"><?= $hotel['price']; ?></span></strong></h5>
</div>

<!-- TABS -->
<div class="tabs">
    <button class="tab active" id="tabDetails">Hotel Details</button>
    <button class="tab" id="tabItinerary">Itinerary</button>
    <button class="tab" id="tabBooking">Booking Details</button>
</div>


<!-- DETAILS TAB -->
<div class="tab-content" id="detailsTab">
    <div id="hotelText" style="max-height:250px; overflow:hidden;">
        <h5 class="text-primary">Description</h5>
        <p><?= $hotel['description']; ?></p>
        <hr>
        <h5 class="text-primary mt-3">Food & Dining</h5>
        <p><?= $hotel['food'] ?: 'Food & dining details will be available soon.'; ?></p>
        <hr>
        <h5 class="text-primary mt-3">Property Policy</h5>
        <p><?= $hotel['policy'] ?: 'Property policies will be updated soon.'; ?></p>
    </div>

    <div class="text-center mt-3">
        <button type="button" id="toggleMore" class="btn btn-outline-primary btn-sm rounded-pill">
            + See More
        </button>
    </div>
</div>
<!-- ITINERARY TAB -->
<div class="tab-content" id="itineraryTab" style="display:none;">

    <h4 class="mt-3">🗓️ Day Wise Itinerary</h4>

    <div id="itineraryBox" style="max-height:300px; overflow:hidden; transition:0.4s;">
        
        <?php
        $itinerary_res = mysqli_query($conn, "SELECT * FROM itineraries WHERE package_id=$pid ORDER BY id ASC");

        if (mysqli_num_rows($itinerary_res) > 0) {
            while ($row = mysqli_fetch_assoc($itinerary_res)) {
                echo "
                    <div class='timeline-content p-3 mb-3 border rounded'>
                        <p>".nl2br(htmlspecialchars($row['plan']))."</p>
                    </div>
                ";
            }
        } else {
            echo "<p>No itinerary available.</p>";
        }
        ?>
    </div>

    <div class="text-center mt-2">
        <button type="button" id="toggleItinerary" class="btn btn-outline-primary btn-sm rounded-pill">
            + See More
        </button>
    </div>
</div>

<!-- BOOKING TAB -->
<div class="tab-content" id="bookingTab" style="display:none;">
    <form method="POST" class="mt-3">
        <input type="hidden" name="hid" value="<?= $hotel['id']; ?>">
        <input type="hidden" name="pid" value="<?= $pid; ?>">
        <input type="hidden" name="final_price" id="finalPriceInput" value="<?= $hotel['price']; ?>">

        <label class="form-label fw-semibold">Check-in Date</label>
        <input type="date" name="checkin" class="form-control mb-3" required min="<?= $minDate; ?>">

        <label class="form-label fw-semibold">Check-out Date</label>
        <input type="date" name="checkout" class="form-control mb-3" required min="<?= $minDate; ?>">

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Adults</label>
                <input type="number" name="adults" class="form-control" min="1" value="1" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Children</label>
                <input type="number" name="children" class="form-control" min="0" value="0">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Rooms</label>
                <input type="number" id="rooms" name="rooms" class="form-control" min="1" max="100" value="1" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100 rounded-pill">
            Book This Hotel & Package
        </button>
    </form>
</div>

        </div>
    </div>
</div>
</div>

<script>
// TAB SWITCH
document.getElementById('tabDetails').onclick = () => {
    detailsTab.style.display = "block";
    itineraryTab.style.display = "none";
    bookingTab.style.display = "none";

    tabDetails.classList.add('active');
    tabItinerary.classList.remove('active');
    tabBooking.classList.remove('active');
};

document.getElementById('tabItinerary').onclick = () => {
    detailsTab.style.display = "none";
    itineraryTab.style.display = "block";
    bookingTab.style.display = "none";

    tabDetails.classList.remove('active');
    tabItinerary.classList.add('active');
    tabBooking.classList.remove('active');
};

document.getElementById('tabBooking').onclick = () => {
    detailsTab.style.display = "none";
    itineraryTab.style.display = "none";
    bookingTab.style.display = "block";

    tabDetails.classList.remove('active');
    tabItinerary.classList.remove('active');
    tabBooking.classList.add('active');
};


// PRICE SYSTEM
let hotelPrice = <?= $hotel['price']; ?>;
let packagePrice = <?= $package['price']; ?>;

let roomsInput = document.getElementById('rooms');
let checkinInput = document.querySelector("input[name='checkin']");
let checkoutInput = document.querySelector("input[name='checkout']");

// Nights count
function getNights() {
    let checkin = new Date(checkinInput.value);
    let checkout = new Date(checkoutInput.value);

    if (!checkinInput.value || !checkoutInput.value) return 1;

    let diff = checkout - checkin;
    let nights = diff / (1000 * 60 * 60 * 24);

    return nights > 0 ? nights : 1;
}

// MAIN PRICE CALCULATION 
function updatePrice() {
    let rooms = Number(roomsInput.value);
    let nights = getNights();

    let total = hotelPrice * rooms * nights;

    document.querySelectorAll('.facility-check').forEach(chk => {
        if (!chk.checked) {
            total -= Number(chk.dataset.cost);
        }
    });

    total += packagePrice;

    if (total < 0) total = 0;

    document.getElementById('totalPrice').textContent = total;
    document.getElementById('finalPriceInput').value = total;
}

roomsInput.oninput = function () {
    if (this.value > 100) {
        alert("Maximum 100 rooms allowed!");
        this.value = 100;
    }
    if (this.value < 1) this.value = 1;
    updatePrice();
};

checkinInput.onchange = updatePrice;
checkoutInput.onchange = updatePrice;

// Facilities change
document.querySelectorAll('.facility-check').forEach(chk => {
    chk.onchange = updatePrice;
});

updatePrice();



// UNIVERSAL SMOOTH TOGGLE FUNCTION 
function universalSmoothToggle(box, btn, minHeight) {
    if (box.classList.contains("open")) {
        box.style.maxHeight = minHeight + "px";
        box.classList.remove("open");
        btn.textContent = "+ See More";
    } else {
        box.style.maxHeight = box.scrollHeight + "px";
        box.classList.add("open");
        btn.textContent = "− See Less";
    }
}



//  HOTEL DETAILS SMOOTH
const detailsBox = document.getElementById("hotelText");
const detailsBtn = document.getElementById("toggleMore");

detailsBox.style.maxHeight = "250px";
detailsBox.style.overflow = "hidden";
detailsBox.style.transition = "max-height 0.4s ease";

detailsBtn.onclick = () => {
    universalSmoothToggle(detailsBox, detailsBtn, 250);
};



// ⭐ FACILITIES SMOOTH
const facilitiesBox = document.getElementById("facilitiesContainer");
const facilitiesBtn = document.getElementById("toggleFacilities");

facilitiesBox.style.maxHeight = "120px";
facilitiesBox.style.overflow = "hidden";
facilitiesBox.style.transition = "max-height 0.4s ease";

facilitiesBtn.onclick = () => {
    universalSmoothToggle(facilitiesBox, facilitiesBtn, 120);
};



// ⭐ ITINERARY SMOOTH
const itineraryBox = document.getElementById("itineraryBox");
const itineraryBtn = document.getElementById("toggleItinerary");

itineraryBox.style.maxHeight = "300px";
itineraryBox.style.overflow = "hidden";
itineraryBox.style.transition = "max-height 0.4s ease";

itineraryBtn.onclick = () => {
    universalSmoothToggle(itineraryBox, itineraryBtn, 300);
};


// ⭐ FIX HEIGHT ON WINDOW RESIZE
window.addEventListener("resize", () => {
    if (detailsBox.classList.contains("open"))
        detailsBox.style.maxHeight = detailsBox.scrollHeight + "px";

    if (facilitiesBox.classList.contains("open"))
        facilitiesBox.style.maxHeight = facilitiesBox.scrollHeight + "px";

    if (itineraryBox.classList.contains("open"))
        itineraryBox.style.maxHeight = itineraryBox.scrollHeight + "px";
});

</script>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
