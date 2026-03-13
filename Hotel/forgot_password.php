<?php
session_start();
include("db.php");

if(!isset($_SESSION['reset_email'])){
    die("Unauthorized Access");
}

$email = $_SESSION['reset_email'];

if(isset($_POST['code'])){

    $code = mysqli_real_escape_string($conn, $_POST['code']);

    $stmt = mysqli_prepare($conn,
        "SELECT id FROM users 
         WHERE email=? 
         AND reset_code=? 
         AND code_expire > NOW()"
    );

    mysqli_stmt_bind_param($stmt, "ss", $email, $code);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) > 0){

        // optional but better security
        $_SESSION['code_verified'] = true;

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