<?php
session_start();
include("db.php");
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['pid']) || empty($_GET['pid'])) {
    header("Location: tourpackage.php");
    exit;
}

$pid = intval($_GET['pid']);
$result = mysqli_query($conn, "SELECT * FROM packages WHERE id = $pid");

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<h3>Package not found!</h3>";
    exit;
}

$package = mysqli_fetch_assoc($result);

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);

// Logo as base64
$logo_tag = "";
$logo_path = 'img/logo.png';
if (extension_loaded('gd') && file_exists($logo_path)) {
    $logo_data = base64_encode(file_get_contents($logo_path));
    $logo_base64 = 'data:image/png;base64,' . $logo_data;
    $logo_tag = "<img src='$logo_base64' height='48' style='vertical-align:middle; margin-right:10px;'>";
}

// Build itinerary as simple divs (NO table - avoids dompdf page-break bugs)
$itinerary_res = mysqli_query($conn, "SELECT * FROM itineraries WHERE package_id=$pid ORDER BY id ASC");
$itinerary_html = "";
$dayNum = 1;

if ($itinerary_res && mysqli_num_rows($itinerary_res) > 0) {
    while ($row = mysqli_fetch_assoc($itinerary_res)) {
        $plan_clean = nl2br(htmlspecialchars($row['plan']));
        $bg = ($dayNum % 2 == 0) ? "#f8fafc" : "#ffffff";
        $itinerary_html .= "
        <div style='display:table; width:100%; border-bottom:1px solid #cbd5e1; page-break-inside:avoid; background:$bg;'>
            <div style='display:table-cell; width:80px; padding:12px 10px; border-right:1px solid #cbd5e1; font-weight:bold; color:#2b3a55; font-size:11px; text-align:center; vertical-align:top; white-space:nowrap;'>Day $dayNum</div>
            <div style='display:table-cell; padding:12px 14px; font-size:10px; color:#334155; line-height:1.7; vertical-align:top;'>$plan_clean</div>
        </div>";
        $dayNum++;
    }
} else {
    $itinerary_html = "<div style='padding:20px; text-align:center; color:#888; font-size:10px;'>Itinerary details will be provided upon booking.</div>";
}

$title = htmlspecialchars($package['title']);
$type  = htmlspecialchars($package['type']);
$dur   = htmlspecialchars($package['duration']);
$price = number_format($package['price']);
$date  = date('d M, Y');
$year  = date('Y');

$pdf_html = "
<html>
<head>
<meta charset='UTF-8'>
<style>
    @page { margin: 25px 30px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 0; }
    table { border-collapse: collapse; }
</style>
</head>
<body>

<!-- COMPANY HEADER -->
<table style='width:100%; margin-bottom:12px;'>
    <tr>
        <td style='vertical-align:middle;'>
            $logo_tag
            <span style='font-size:20px; font-weight:bold; color:#2b3a55; vertical-align:middle;'>Dream Tour &amp; Travel</span>
        </td>
        <td style='text-align:right; vertical-align:middle; font-size:9px; color:#666;'>
            33, Gujrat Gas Circle, Adajan, Surat<br>
            +91 8980052655 | dreamtourtravels.com
        </td>
    </tr>
</table>
<hr style='border:none; border-top:2px solid #2b3a55; margin-bottom:12px;'>

<!-- DOCUMENT TITLE -->
<div style='text-align:center; font-size:13px; font-weight:bold; text-transform:uppercase; color:#2b3a55; background:#f1f5f9; border:1px solid #cbd5e1; padding:9px; margin-bottom:0; letter-spacing:1px;'>
    Official Tour Itinerary &amp; Program
</div>

<!-- PACKAGE INFO TABLE - directly below title -->
<table style='width:100%; border:1px solid #cbd5e1; border-top:none; margin-bottom:0;'>
    <tr>
        <td style='padding:10px 12px; border-right:1px solid #cbd5e1; width:66%; vertical-align:top;'>
            <div style='font-size:8px; text-transform:uppercase; color:#64748b; font-weight:bold; margin-bottom:4px;'>Tour Destination / Package</div>
            <div style='font-size:14px; font-weight:bold; color:#1e293b;'>$title</div>
        </td>
        <td style='padding:10px 12px; vertical-align:top;'>
            <div style='font-size:8px; text-transform:uppercase; color:#64748b; font-weight:bold; margin-bottom:4px;'>Issue Date</div>
            <div style='font-size:12px; font-weight:bold; color:#1e293b;'>$date</div>
        </td>
    </tr>
    <tr>
        <td style='padding:10px 12px; border-top:1px solid #cbd5e1; border-right:1px solid #cbd5e1; vertical-align:top;'>
            <div style='font-size:8px; text-transform:uppercase; color:#64748b; font-weight:bold; margin-bottom:4px;'>Category</div>
            <div style='font-size:12px; font-weight:bold; color:#1e293b;'>$type</div>
        </td>
        <td style='padding:10px 12px; border-top:1px solid #cbd5e1; vertical-align:top;'>
            <div style='display:table; width:100%;'>
                <div style='display:table-cell; width:50%; vertical-align:top;'>
                    <div style='font-size:8px; text-transform:uppercase; color:#64748b; font-weight:bold; margin-bottom:4px;'>Duration</div>
                    <div style='font-size:12px; font-weight:bold; color:#1e293b;'>$dur</div>
                </div>
                <div style='display:table-cell; width:50%; vertical-align:top;'>
                    <div style='font-size:8px; text-transform:uppercase; color:#64748b; font-weight:bold; margin-bottom:4px;'>Package Cost</div>
                    <div style='font-size:12px; font-weight:bold; color:#1e293b;'>&#8377; $price</div>
                </div>
            </div>
        </td>
    </tr>
</table>

<!-- ITINERARY HEADER - zero margin, directly below package info -->
<div style='background:#2b3a55; color:white; font-size:10px; font-weight:bold; text-transform:uppercase; padding:10px 14px; display:table; width:100%; border-top:1px solid #2b3a55; box-sizing:border-box;'>
    <div style='display:table-cell; width:80px; color:white; letter-spacing:0.5px;'>Day</div>
    <div style='display:table-cell; color:white; letter-spacing:0.5px; padding-left:10px;'>Tour Program &amp; Sightseeing Activities</div>
</div>

<!-- ITINERARY ROWS - each row has page-break-inside:avoid -->
<div style='border:1px solid #cbd5e1; border-top:none;'>
    $itinerary_html
</div>

<!-- FOOTER -->
<div style='margin-top:20px; text-align:center; font-size:8px; color:#94a3b8; border-top:1px solid #e2e8f0; padding-top:8px;'>
    This is a computer-generated official document. &copy; $year Dream Tour &amp; Travel.
</div>

</body>
</html>";

$dompdf->loadHtml($pdf_html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$cleanTitle = preg_replace('/[^A-Za-z0-9]/', '_', $package['title']);
$filename = "Itinerary_" . $cleanTitle . ".pdf";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $dompdf->output();
exit;
?>
