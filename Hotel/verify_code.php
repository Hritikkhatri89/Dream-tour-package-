<?php
session_start();
include("db.php");

if(isset($_POST['code'])){

    $email = $_SESSION['reset_email'];
    $code = $_POST['code'];

    $result = mysqli_query($conn, 
        "SELECT * FROM users 
         WHERE email='$email' 
         AND reset_code='$code' 
         AND code_expire > NOW()");

    if(mysqli_num_rows($result) > 0){

        header("Location: new_password.php");
        exit;

    } else {
        echo "Invalid or Expired Code!";
    }
}
?>

<form method="post">
    <input type="text" name="code" placeholder="Enter 6-digit Code" required>
    <button type="submit">Verify</button>
</form>