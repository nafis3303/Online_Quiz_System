<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quizzers";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    echo "error";
    exit();
}

$email = $_POST['email'] ?? '';

if ($email === '') {
    echo "invalid";
    exit();
}

$email = mysqli_real_escape_string($conn, $email);
$result = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");

if (mysqli_num_rows($result) > 0) {
    echo "Email Already Exists";
} else {
    echo "This Email is Available";
}
