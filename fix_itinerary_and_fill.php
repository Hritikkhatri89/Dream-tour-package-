<?php
include "db.php";

echo "<h3>Fixing Itineraries Table & Filling Data</h3><hr>";

// 1. Fix Table Schema (Add Auto Increment if missing)
$res = mysqli_query($conn, "SHOW KEYS FROM itineraries WHERE Key_name = 'PRIMARY'");
if (mysqli_num_rows($res) == 0) {
    echo "🔧 Adding Primary Key and Auto-Increment to 'itineraries' table...<br>";
    // Check if ID 0 exists and fix it first to avoid collision
    mysqli_query($conn, "UPDATE itineraries SET id = 100 WHERE id = 0"); 
    $fixSql = "ALTER TABLE `itineraries` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)";
    if (mysqli_query($conn, $fixSql)) {
        echo "✅ Schema fixed successfully!<br>";
    } else {
        echo "❌ Error fixing schema: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "⏭️ Schema already has Primary Key.<br>";
}

// 2. Define Itineraries
$destinationPlans = [
    'kerala' => [
        ['day' => 'Day 1: Arrival in Kochi & Transfer to Munnar', 'plan' => "• Arrive at Kochi Airport/Railway Station\n• Meet & greet, then drive to Munnar (scenic hill station)\n• Enroute visit Cheeyappara and Valara Waterfalls\n• Check-in at hotel and relax\n• Overnight stay in Munnar"],
        ['day' => 'Day 2: Munnar Local Sightseeing', 'plan' => "• Breakfast at hotel\n• Visit Eravikulam National Park (Rajamalai)\n• Visit Mattupetty Dam, Echo Point, and Kundala Lake\n• Visit Tea Museum and Flower Garden\n• Overnight stay in Munnar"],
        ['day' => 'Day 3: Munnar to Thekkady', 'plan' => "• After breakfast, drive to Thekkady (Periyar Wildlife Sanctuary)\n• Visit spice plantations (Cardamom, Pepper, Vanilla)\n• Optional: Boat safari in Periyar Lake (extra cost)\n• Visit Elephant Junction for interactions\n• Overnight stay in Thekkady"],
        ['day' => 'Day 4: Thekkady to Alleppey (Houseboat)', 'plan' => "• Drive to Alleppey, the Venice of the East\n• Board a traditional houseboat at noon\n• Cruise through the beautiful backwaters and lagoons\n• Enjoy Kerala meals on board\n• Overnight stay in the Houseboat"],
        ['day' => 'Day 5: Alleppey to Kochi & Departure', 'plan' => "• Breakfast on houseboat, then check-out\n• Drive to Kochi for sightseeing: Fort Kochi, Chinese Fishing Nets, Jewish Synagogue\n• Shopping at LuLu Mall or local markets\n• Evening transfer to Airport/Railway Station\n• Tour ends with wonderful memories"]
    ],
    'gujrat' => [
        ['day' => 'Day 1: Arrival in Ahmedabad', 'plan' => "• Arrive at Ahmedabad Airport/Railway Station\n• Transfer to hotel and check-in\n• Visit Sabarmati Ashram (Gandhi Ashram)\n• Evening stroll at Sabarmati Riverfront\n• Dinner at a traditional Gujarati restaurant\n• Overnight stay in Ahmedabad"],
        ['day' => 'Day 2: Ahmedabad Sightseeing', 'plan' => "• Breakfast at hotel\n• Visit Adalaj Stepwell (Architectural Marvel)\n• Visit Akshardham Temple in Gandhinagar\n• Visit Hutheesing Jain Temple and Sidi Saiyyed Mosque\n• Evening shopping at Law Garden Market\n• Overnight stay in Ahmedabad"],
        ['day' => 'Day 3: Ahmedabad to Dwarka', 'plan' => "• Early breakfast and drive to Dwarka (Sacred City)\n• Enroute visit Jamnagar: Lakhota Lake and Bala Hanuman Temple\n• Reach Dwarka in the evening and check-in\n• Attend evening Aarti at Dwarkadhish Temple\n• Overnight stay in Dwarka"],
        ['day' => 'Day 4: Dwarka Sightseeing', 'plan' => "• Early visit to Dwarkadhish Temple\n• Boat ride to Bet Dwarka (Island Temple)\n• Visit Nageshwar Jyotirlinga and Gopi Talav\n• Visit Rukmini Devi Temple\n• Evening leisure at Dwarka Beach\n• Overnight stay in Dwarka"],
        ['day' => 'Day 5: Dwarka to Somnath via Porbandar', 'plan' => "• After breakfast, drive to Somnath\n• Enroute stop at Porbandar: Visit Kirti Mandir (Gandhi's Birthplace)\n• Visit Sudama Temple\n• Reach Somnath, check-in at hotel\n• Visit Somnath Temple and witness Light & Sound Show\n• Overnight stay in Somnath"],
        ['day' => 'Day 6: Somnath to Ahmedabad & Departure', 'plan' => "• Breakfast at hotel, then check-out\n• Drive back to Ahmedabad (approx. 7-8 hrs)\n• Late evening drop-off at Airport/Railway Station\n• Tour ends with spiritual blessings"]
    ],
    'goa' => [
        ['day' => 'Day 1: Arrival in Goa', 'plan' => "• Arrive at Goa Airport or Railway Station\n• Transfer to hotel and check-in\n• Welcome drink on arrival (Non-Alcoholic)\n• Free time to relax or visit nearby beach\n• Overnight stay at hotel"],
        ['day' => 'Day 2: North Goa Sightseeing', 'plan' => "• Breakfast at hotel\n• Visit Calangute Beach, Baga Beach, and vagator Beach\n• Visit Fort Aguada and Chapora Fort\n• Overnight stay at hotel"],
        ['day' => 'Day 3: South Goa Sightseeing', 'plan' => "• Breakfast at hotel\n• Visit Basilica of Bom Jesus and Se Cathedral\n• Visit Dona Paula Viewpoint and Miramar Beach\n• Overnight stay at hotel"],
        ['day' => 'Day 4: Mandovi River Cruise', 'plan' => "• Breakfast at hotel\n• Evening Boat Cruise at Mandovi River with music\n• Overnight stay at hotel"],
        ['day' => 'Day 5: Departure', 'plan' => "• Breakfast at hotel and Check-out\n• Transfer to Goa Airport/Railway Station"]
    ],
    'dehradun' => [
        ['day' => 'Day 1: Arrival in Dehradun', 'plan' => "• Arrive at Dehradun Airport / Railway Station\n• Transfer to hotel and check-in\n• Visit Forest Research Institute and Tapkeshwar Temple\n• Evening free for shopping at Paltan Bazaar\n• Overnight stay in Dehradun"],
        ['day' => 'Day 2: Dehradun to Mussoorie', 'plan' => "• Drive to Mussoorie (Queen of Hills)\n• Visit Kempty Falls and Company Garden\n• Evening stroll at Mall Road\n• Overnight stay in Mussoorie"],
        ['day' => 'Day 3: Mussoorie Sightseeing', 'plan' => "• Visit Gun Hill Point and Camel's Back Road\n• Visit Lal Tibba and Mussoorie Lake\n• Overnight stay in Mussoorie"],
        ['day' => 'Day 4: Mussoorie to Rishikesh', 'plan' => "• Drive to Rishikesh, the Yoga Capital\n• Visit Lakshman Jhula and Ram Jhula\n• Evening Ganga Aarti at Triveni Ghat\n• Overnight stay in Rishikesh"],
        ['day' => 'Day 5: Haridwar Sightseeing & Return', 'plan' => "• Visit Har Ki Pauri and Mansa Devi Temple\n• Drive back to Dehradun\n• Tour ends with pleasant memories"]
    ],
    'ooty' => [
        ['day' => 'Day 1: Arrival in Bangalore & Transfer to Mysore', 'plan' => "• Arrive at Bangalore, meet representative\n• Drive to Mysore, enroute visit Srirangapatna\n• Visit Mysore Palace and Brindavan Gardens\n• Overnight stay in Mysore"],
        ['day' => 'Day 2: Mysore to Ooty', 'plan' => "• Drive to Ooty through Bandipur National Park\n• Check-in at hotel and relax\n• Evening at Ooty Lake\n• Overnight stay in Ooty"],
        ['day' => 'Day 3: Ooty Sightseeing', 'plan' => "• Visit Botanical Garden and Doddabetta Peak\n• Visit Tea Factory and Rose Garden\n• Overnight stay in Ooty"],
        ['day' => 'Day 4: Coonoor Excursion', 'plan' => "• Visit Sim's Park and Dolphin's Nose in Coonoor\n• Optional: Toy Train ride (subject to availability)\n• Overnight stay in Ooty"],
        ['day' => 'Day 5: Departure', 'plan' => "• Breakfast at hotel and Check-out\n• Drive back to Bangalore for departure"]
    ]
];

function getGenericPlan($dayNum, $totalDays, $title) {
    if ($dayNum == 1) return ['day' => "Day 1: Arrival in $title", 'plan' => "• Arrive at destination and meet representative\n• Transfer to hotel and check-in\n• Rest and local sightseeing in the evening\n• Overnight stay at hotel"];
    if ($dayNum == $totalDays) return ['day' => "Day $dayNum: Departure", 'plan' => "• Breakfast and check-out\n• Last-minute shopping\n• Transfer to Airport/Railway Station\n• Tour ends with pleasant memories"];
    return ['day' => "Day $dayNum: Local Exploration", 'plan' => "• Full day sightseeing of major attractions\n• Experience local culture and food\n• Visit scenic viewpoints and markets\n• Overnight stay at hotel"];
}

// 3. Process Packages
$pkgs = mysqli_query($conn, "SELECT id, title, duration FROM packages");
$totalAdded = 0;

while ($row = mysqli_fetch_assoc($pkgs)) {
    $pid = $row['id'];
    $title = $row['title'];
    $dur = $row['duration'];
    $titleLower = strtolower($title);
    
    // Check if itinerary is missing (less than 2 days usually means incomplete)
    $checkCount = mysqli_query($conn, "SELECT COUNT(*) as count FROM itineraries WHERE package_id = $pid");
    $rowCount = mysqli_fetch_assoc($checkCount)['count'];
    
    if ($rowCount >= 3) {
        echo "⏭️ Skipping '$title' (Already has $rowCount days).<br>";
        continue;
    }
    
    echo "🔨 Filling itinerary for '$title' ($pid)...";
    
    // Clear potentially broken ones
    mysqli_query($conn, "DELETE FROM itineraries WHERE package_id = $pid");

    $itPlan = null;
    foreach ($destinationPlans as $key => $plan) {
        if (strpos($titleLower, $key) !== false) {
            $itPlan = $plan;
            break;
        }
    }
    
    if ($itPlan) {
        foreach ($itPlan as $it) {
            $day = mysqli_real_escape_string($conn, $it['day']);
            $plan = mysqli_real_escape_string($conn, $it['plan']);
            mysqli_query($conn, "INSERT INTO itineraries (package_id, day_or_night, plan) VALUES ('$pid', '$day', '$plan')");
            $totalAdded++;
        }
        echo " <span style='color:green;'>SUCCESS (Matched)</span><br>";
    } else {
        preg_match('/(\d+)/', $dur, $matches);
        $daysCount = isset($matches[1]) ? intval($matches[1]) : 5;
        if ($daysCount < 2) $daysCount = 5;
        
        for ($i = 1; $i <= $daysCount; $i++) {
            $it = getGenericPlan($i, $daysCount, $title);
            $day = mysqli_real_escape_string($conn, $it['day']);
            $plan = mysqli_real_escape_string($conn, $it['plan']);
            mysqli_query($conn, "INSERT INTO itineraries (package_id, day_or_night, plan) VALUES ('$pid', '$day', '$plan')");
            $totalAdded++;
        }
        echo " <span style='color:orange;'>SUCCESS (Generic)</span><br>";
    }
}

echo "<hr><h4>Migration Complete! Total $totalAdded itinerary days added across packages.</h4>";
?>
