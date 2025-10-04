<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}
?>

<html>
<head>
<title>3 Star Hotels</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
.hotel-card {
    border:2px solid #eee;
    border-radius:15px;
    padding:20px;
    margin-bottom:30px;
    background:#fff;
    box-shadow:0 6px 18px rgba(0,0,0,0.12);
    transition: all 0.3s ease-in-out;
}
.hotel-card:hover {
    transform: translateY(-5px);
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}
.carousel-inner img {
    height:280px;
    object-fit:cover;
    border-radius:12px;
}
h5 {
    color:#2c3e50;
    font-weight:600;
}
#travel-options {
    background:#f4f9ff;
}
</style>
</head>
<body style="background:#f8f9fa;">
<div class="container py-5">

    <h2 class="mb-4 text-center text-primary"> Luxury 4 Star Hotels</h2>

    <!-- Hotel Box -->
    <div class="hotel-card">
        <div class="row">
            <!-- Left Side: Image Slider -->
            <div class="col-md-6">
                <div id="hotelCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="Hotels/3s1.jpg" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img src="Hotels/3s2.jpg" class="d-block w-100">
                        </div>
                        <div class="carousel-item">
                            <img src="Hotels/3s3.jpg" class="d-block w-100">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#hotelCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#hotelCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>

            <!-- Right Side: Details -->
            <div class="col-md-6">
                <h5 class="mb-3"> Premium 3 * Hotel</h5>
                <p><b>Room Facilities:</b><br>
                   • Basic AC / Non-AC Rooms<br>
                   • Clean & Comfortable Bed<br>
                   • TV, Telephone, WiFi (basic speed)<br>
                   • Attached bathroom with hot/cold water
                </p>
                <p><b>Services:</b><br>
                   • 24/7 Room Service<br>
                   • In-house Restaurant (limited menu)<br>
                   • Daily Housekeeping
                </p>
                <p><b>Amenities:</b><br>
                   • Small Gym / Fitness Area (optional)<br>
                   • Parking Facility<br>
                   • Reception Desk
                </p>

                <!-- Buttons -->
                <button class="btn btn-success show-options">View Travel Options</button>
            </div>
        </div>

        <!-- Travel Options (hidden by default) -->
        <div id="travel-options" class="mt-4 p-3 border rounded" style="display:none;">
            <h6 class="text-primary">🚖 Select Travel Mode:</h6>
            <button id="flight-btn" class="btn btn-outline-primary me-2">✈️ Flight</button>
            <button id="train-btn" class="btn btn-outline-success">🚆 Train</button>

       <div id="flight-form" style="display:block; margin-top:15px;">
			<h6>Flight Booking</h6>
			<?php $today = date('Y-m-d'); ?>
			<label><input type="radio" name="flight_type" value="round" checked onchange="toggleReturnDate(this)"> Round Trip</label>
			<label><input type="radio" name="flight_type" value="oneway" onchange="toggleReturnDate(this)"> One Way</label>
			<div class="mt-2"><input type="text" class="form-control mb-2" placeholder="From"></div>
			<div><input type="text" class="form-control mb-2" placeholder="To"></div>
    
		<div class="mt-2">
        <label>Departure:</label>
        <input type="date" class="form-control" id="departure_date" name="departure_date" min="<?php echo $today; ?>">
		</div>
    
		<div class="mt-2" id="return_date_div">
        <label>Return:</label>
        <input type="date" class="form-control" id="return_date" name="return_date" min="<?php echo $today; ?>">
		</div>
         <button class="btn btn-primary mt-2">Book Flight</button>
		</div>

            <!-- Train Form -->
            <div id="train-form" style="display:none; margin-top:15px;">
                <h6>Train Booking</h6>
                <div class="mt-2"><input type="text" class="form-control mb-2" placeholder="From"></div>
                <div><input type="text" class="form-control mb-2" placeholder="To"></div>
                 <input type="date" class="form-control" id="departure_date" name="departure_date" min="<?php echo $today; ?>">
                <button class="btn btn-success mt-2">Book Train</button>
            </div>
			
        </div>
    </div>

</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $(".show-options").click(function(){
        $("#travel-options").slideToggle();
        $('html, body').animate({
            scrollTop: $("#travel-options").offset().top - 100
        }, 600);
    });

    $("#flight-btn").click(function(){
        $("#flight-form").slideToggle();
        $("#train-form").slideUp();
    });

    $("#train-btn").click(function(){
        $("#train-form").slideToggle();
        $("#flight-form").slideUp();
    });

    $(document).on("change","input[name='flight_type']",function(){
        if($(this).val()=="oneway") $("#return_date_div").hide();
        else $("#return_date_div").show();
    });
});
</script>

</body>
</html>
