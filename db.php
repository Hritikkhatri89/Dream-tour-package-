<?php
// Railway environment variables use karo, fallback localhost ke liye
$servername = getenv('MYSQLHOST')     ?: "localhost";
$username   = getenv('MYSQLUSER')     ?: "root";
$password   = getenv('MYSQLPASSWORD') ?: "";
$dbname     = getenv('MYSQLDATABASE') ?: "ttms";
$port       = getenv('MYSQLPORT')     ?: 3306;

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, (int)$port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
