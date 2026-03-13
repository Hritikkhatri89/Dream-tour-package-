<?php
session_start();

include("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if(isset($_POST['email'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);

    $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) == 0){
        die("Email not registered");
    }

    $code = rand(100000,999999);
   $expire = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');

    mysqli_query($conn,"UPDATE users 
                        SET reset_code='$code',
                            code_expire='$expire'
                        WHERE email='$email'");

    $_SESSION['reset_email'] = $email;

    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yourgmail@gmail.com';
        $mail->Password = 'your_app_password';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('yourgmail@gmail.com','TTMS');
        $mail->addAddress($email);

        $mail->Subject = 'Password Reset OTP';
        $mail->Body = "Your OTP is: $code (Valid for 5 minutes)";

        $mail->send();

        header("Location: verify_code.php");
        exit;

    }catch(Exception $e){
        echo "Mailer Error: ".$mail->ErrorInfo;
    }
}