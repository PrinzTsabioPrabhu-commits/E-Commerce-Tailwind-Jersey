<?php
// Database connection configuration
$host = "localhost";
$user = "root";
$pass = "";
$db = "ecommerce";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8");
?>
