<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<link rel="stylesheet" href="css/index1.css">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Online Quiz System </title>


</head>

<body>
    <div class="container">
        <h1>Welcome to QuizMaster</h1>
        <p>Start creating quizzes, take tests, and track your performance!</p>
        <div class="button-group">
            <a href="login.php" class="btn">Login</a>
            <a href="register.php" class="btn">Create Account</a>
        </div>
    </div>



</body>

</html>