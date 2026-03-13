<?php
session_start();
include("db.php");

if(isset($_POST['email'])){

    $email = mysqli_real_escape_string($conn,$_POST['email']);

    $check = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

    if(mysqli_num_rows($check) > 0){

        $code = rand(100000,999999);
        $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        mysqli_query($conn,"UPDATE users 
                            SET reset_code='$code',
                                code_expire='$expire'
                            WHERE email='$email'");

        $_SESSION['reset_email'] = $email;

        echo "Your code is: $code";  // temporary (email later)
        echo "<br><a href='verify_code.php'>Verify Code</a>";

    } else {
        echo "Email not found";
    }
}