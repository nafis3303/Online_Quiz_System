<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quizzers";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Database connection failed");
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['userName'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirmPassword === '' || $role === '') {
        $errorMessage = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } else {

        
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $errorMessage = "Email already exists.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password, role)
                    VALUES ('$name', '$email', '$hashedPassword', '$role')";

            if (mysqli_query($conn, $sql)) {
                $successMessage = "Registration Successful";
            } else {
                $errorMessage = "Registration failed. Try again.";
            }
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - QuizMaster</title>
    <link rel="stylesheet" href="css/register.css">
</head>

<body>

<?php if ($successMessage): ?>
    <div class="popup-success">
        <div class="popup-box">
            <h2><?= htmlspecialchars($successMessage) ?></h2>
            <p><a href="login.php">Click here to login</a></p>
        </div>
    </div>
<?php endif; ?>

<div class="register-container">
    <h1>Create Account</h1>

    <?php if ($errorMessage): ?>
        <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm();">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="userName" id="userName">
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="text" name="email" id="email">
            <small id="emailStatus"></small>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="password">
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirmPassword" id="confirmPassword">
        </div>

        <div class="form-group">
            <label>Register As</label>
            <select name="role" id="role">
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>

        <button type="submit" class="register-btn">Register</button>
    </form>

    <div class="form-footer">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

<script src="js/validation.js"></script>

<script>
// AJAX email check
document.getElementById("email").addEventListener("blur", function () {
    const email = this.value.trim();
    const status = document.getElementById("emailStatus");

    if (email === "") {
        status.innerText = "";
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            status.innerText = this.responseText;
            status.style.color = this.responseText.includes("Available") ? "green" : "red";
        }
    };

    xhr.open("POST", "check_email.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("email=" + encodeURIComponent(email));
});
</script>

</body>
</html>
