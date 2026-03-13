<?php
session_start();
include("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

require 'vendor/autoload.php';

if (isset($_GET['payment_id']) && isset($_GET['bid'])) {
    $payment_id = mysqli_real_escape_string($conn, $_GET['payment_id']);
    $bid = intval($_GET['bid']);

    // Update booking status to Paid
    $sql = "UPDATE bookings SET status='Paid' WHERE id='$bid'";
    
    if (mysqli_query($conn, $sql)) {
        
        // Fetch booking and user details for email
        $details_query = "SELECT b.*, u.name as user_name, u.email as user_email, p.image as tour_image 
                         FROM bookings b 
                         JOIN users u ON b.user_id = u.id 
                         JOIN packages p ON b.package_id = p.id
                         WHERE b.id = '$bid'";
        $details_result = mysqli_query($conn, $details_query);
        $booking = mysqli_fetch_assoc($details_result);

        if ($booking && $booking['user_email']) {
            $user_email = $booking['user_email'];
            $user_name = $booking['user_name'];
            $package_name = $booking['package_name'];
            $amount = $booking['price'];
            $checkin = $booking['checkin'];
            $checkout = $booking['checkout'];
            $hotel_name = $booking['hotel_name'];
            $hotel_star = $booking['hotel_star'] ?? '';

            // Prepare variables for email
            $images = explode(',', $booking['image'] ?? '');
            $first_image = trim($images[0] ?? '');
            $image_path = "Hotel/" . $first_image;
            
            $tour_images = explode(',', $booking['tour_image'] ?? '');
            $first_tour_image = trim($tour_images[0] ?? '');
            $tour_image_path = "img/" . $first_tour_image;

            $hotel_id = $booking['hotel_id'];
            $rooms = $booking['rooms'];
            $adults = $booking['adults'];
            $children = $booking['children'];

            // Send Confirmation Email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'dreamtour955@gmail.com';   
                $mail->Password   = 'fgfznrkqjrousypr';       
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('dreamtour955@gmail.com', 'Dream Tour & Travel');
                $mail->addAddress($user_email);
                $mail->Subject = 'Booking Confirmed - Dream Tour & Travel';
                $mail->isHTML(true);



                
                $mail->Body = "
                <div style='background-color: #f0f7f9; padding: 30px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                        <tr>
                            <td align='center'>
                                <!-- Main Card -->
                                <table width='600' border='0' cellspacing='0' cellpadding='0' style='background: #ffffff; border-radius: 25px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05);'>
                                    
                                    <!-- Dark Blue Header -->
                                    <tr>
                                        <td style='background: #2b3a55; padding: 40px 20px; text-align: center; border-radius: 25px 25px 0 0;'>
                                            <img src='http://localhost/ttms_final1/img/logo.png' width='60' style='margin-bottom: 15px; border-radius: 50%;'>
                                            <h2 style='color: #ffffff; margin: 0; font-size: 22px; letter-spacing: 0.5px;'>Dream Tour & Travel Management System</h2>
                                            <p style='color: #cbd5e1; margin: 5px 0 0; font-size: 13px;'>33, Gujrat Gas Circle, Adajan | +91 8980052655</p>
                                        </td>
                                    </tr>

                                    <!-- Success Message -->
                                    <tr>
                                        <td style='padding: 30px 0 10px; text-align: center;'>
                                            <h1 style='color: #28a745; margin: 0; font-size: 28px; display: flex; align-items: center; justify-content: center;'>
                                                <span style='margin-right: 10px;'>✅</span> Booking Confirmed!
                                            </h1>
                                        </td>
                                    </tr>

                                    <!-- Rounded Detail Rows -->
                                    <tr>
                                        <td style='padding: 20px 30px 40px;'>
                                            
                                            <!-- Row Template -->
                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px; display: block;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>👤</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>User Name:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$user_name</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>🎁</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>Package:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$package_name</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>🏨</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>Hotel:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$hotel_name ($hotel_star ⭐)</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>📅</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>Check-in:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$checkin</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>📅</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>Check-out:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$checkout</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>👥</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>Adults:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$adults</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>👶</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>Children:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$children</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-bottom: 12px;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>🚪</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 14px;'>Rooms:</td>
                                                        <td align='right' style='color: #1e293b; font-size: 14px;'>$rooms</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <div style='border: 1px solid #e2e8f0; border-radius: 50px; padding: 12px 25px; margin-top: 20px; background: #f8fafc;'>
                                                <table width='100%'>
                                                    <tr>
                                                        <td style='width: 30px; font-size: 18px;'>💰</td>
                                                        <td style='font-weight: 700; color: #1e293b; font-size: 16px;'>Total Price:</td>
                                                        <td align='right' style='color: #28a745; font-size: 20px; font-weight: 800;'>₹$amount</td>
                                                    </tr>
                                                </table>
                                            </div>

                                            <!-- Footer Button -->
                                            <div style='text-align: center; margin-top: 40px;'>
                                                <a href='http://localhost/ttms_final1/booking_summary.php?bid=$bid' style='background: #2b3a55; color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 50px; font-weight: bold; font-size: 14px;'>View & Print Official Bill</a>
                                            </div>

                                        </td>
                                    </tr>
                                </table>
                                
                                <p style='color: #64748b; font-size: 12px; margin-top: 20px;'>
                                    © 2025 Dream Tour & Travel. All rights reserved.<br>
                                    This is an auto-generated email. Please do not reply.
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>";

                // --- Generate PDF for attachment ---
                $options = new Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);
                $options->set('defaultFont', 'DejaVu Sans'); // Support for more characters
                $dompdf = new Dompdf($options);
                
                // Get logo only if GD extension is available (to avoid Dompdf crash)
                $logo_path = 'img/logo.png';
                $logo_tag = "";
                if (extension_loaded('gd') && file_exists($logo_path)) {
                    $logo_data = base64_encode(file_get_contents($logo_path));
                    $logo_base64 = 'data:image/png;base64,' . $logo_data;
                    $logo_tag = "<img src='$logo_base64' width='65'><br>";
                } else {
                    // Fallback to text logo if GD is missing
                    $logo_tag = "<div style='font-size: 24px; font-weight: bold; margin-bottom: 5px;'>Dream Tour</div>";
                }

                $pdf_html = "
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <style>
                        body { font-family: 'DejaVu Sans', sans-serif; background: #f0f7f9; margin: 0; padding: 20px; }
                        .container { width: 100%; max-width: 650px; margin: auto; background: #ffffff; border-radius: 25px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
                        .header { background: #2b3a55; padding: 40px 20px; text-align: center; color: white; }
                        .header img { border-radius: 50%; margin-bottom: 10px; border: 2px solid #fff; }
                        .header h2 { margin: 0; font-size: 20px; letter-spacing: 0.5px; }
                        .header p { margin: 5px 0 0; font-size: 12px; color: #cbd5e1; }
                        .confirmed { text-align: center; color: #28a745; font-size: 24px; padding: 30px 0; font-weight: bold; }
                        .details { padding: 0 35px 40px; }
                        .row { border: 1px solid #e2e8f0; border-radius: 40px; padding: 10px 25px; margin-bottom: 10px; background: #fff; }
                        .row .label { float: left; font-weight: bold; color: #1e293b; font-size: 13px; line-height: 20px; }
                        .row .value { float: right; color: #1e293b; font-size: 13px; line-height: 20px; font-weight: 500; }
                        .row.total { background: #f8fafc; margin-top: 15px; border: 1.5px solid #28a745; padding: 15px 25px; }
                        .row.total .label { font-size: 15px; color: #1e293b; line-height: 25px; }
                        .row.total .value { color: #28a745; font-size: 18px; font-weight: 800; line-height: 25px; }
                        .clr { clear: both; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            $logo_tag
                            <h2>Dream Tour & Travel Management System</h2>
                            <p>33, Gujrat Gas Circle, Adajan | +91 8980052655</p>
                        </div>
                        <div class='confirmed'>✔ Booking Confirmed!</div>
                        <div class='details'>
                            <div class='row'><span class='label'>User Name:</span> <span class='value'>$user_name</span><div class='clr'></div></div>
                            <div class='row'><span class='label'>Package:</span> <span class='value'>$package_name</span><div class='clr'></div></div>
                            <div class='row'><span class='label'>Hotel:</span> <span class='value'>$hotel_name</span><div class='clr'></div></div>
                            <div class='row'><span class='label'>Check-in:</span> <span class='value'>$checkin</span><div class='clr'></div></div>
                            <div class='row'><span class='label'>Check-out:</span> <span class='value'>$checkout</span><div class='clr'></div></div>
                            <div class='row'><span class='label'>Adults:</span> <span class='value'>$adults</span><div class='clr'></div></div>
                            <div class='row'><span class='label'>Children:</span> <span class='value'>$children</span><div class='clr'></div></div>
                            <div class='row'><span class='label'>Rooms:</span> <span class='value'>$rooms</span><div class='clr'></div></div>
                            
                            <div class='row total'>
                                <span class='label'>Total Price:</span> 
                                <span class='value'>₹$amount</span>
                                <div class='clr'></div>
                            </div>
                        </div>
                        <div style='text-align:center; padding-bottom: 30px; font-size: 10px; color: #94a3b8;'>
                            This is an officially confirmed computer-generated bill.
                        </div>
                    </div>
                </body>
                </html>";

                $dompdf->loadHtml($pdf_html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $pdf_output = $dompdf->output();

                // Attach the PDF
                $mail->addStringAttachment($pdf_output, "BookingReceipt_#$bid.pdf");

                $mail->send();
            } catch (Exception $e) {
                // Silently log or handle error - we don't want to break the success flow if email fails
                // but for debugging we could: error_log("Mail Error: " . $mail->ErrorInfo);
            }
        }

        // Redirect back to summary with success
        $_SESSION['payment_success'] = true;
        header("Location: booking_summary.php?bid=" . $bid);
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit;
}
?>

