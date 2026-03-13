<?php
session_start();
include("db.php");

if(!isset($_SESSION['otp_verified'])){
    die("Access Denied");
}

$email = $_SESSION['reset_email'];

if(isset($_POST['password'])){

    $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn,
        "UPDATE users 
         SET password='$newpass',
             reset_code=NULL,
             code_expire=NULL
         WHERE email='$email'");

    session_destroy();

    echo "Password Updated Successfully";
}
?>

<form method="post">
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit">Update Password</button>
</form>