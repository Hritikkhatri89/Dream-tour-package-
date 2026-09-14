<?php
// Auto-detect environment variables (Railway / Render / local)
$servername = getenv('MYSQLHOST')     ?: (getenv('DB_HOST')     ?: "127.0.0.1");
$username   = getenv('MYSQLUSER')     ?: (getenv('DB_USER')     ?: "root");
$password   = getenv('MYSQLPASSWORD') ?: (getenv('DB_PASSWORD') ?: "");
$dbname     = getenv('MYSQLDATABASE') ?: (getenv('DB_NAME')     ?: "ttms");
$port       = getenv('MYSQLPORT')     ?: (getenv('DB_PORT')     ?: 3306);

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, (int)$port);
if (!$conn) {
    die("<h2 style='color:red;font-family:sans-serif;padding:20px'>
    ⚠️ Database Connection Failed<br>
    <small style='font-size:14px;color:#555'>
    Host: $servername | DB: $dbname<br>
    Error: " . mysqli_connect_error() . "
    </small></h2>");
}
?>
