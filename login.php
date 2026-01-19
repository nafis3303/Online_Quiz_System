<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "quizzers";

$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$loginError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['loginEmail'] ?? '');
    $password = $_POST['loginPassword'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    if ($email && $password) {
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {

                
                $role = trim(strtolower($user['role'] ?? ''));

                if ($role !== 'student' && $role !== 'teacher') {
                    $loginError = "Access denied.";
                } else {

                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['role'] = $role;
                    $_SESSION['username'] = $user['username']; 

                    
                    if ($rememberMe) {
                        setcookie("remembered_email", $email, time() + (86400 * 30), "/");
                        setcookie("remembered_password", $password, time() + (86400 * 30), "/");
                    } else {
                        setcookie("remembered_email", "", time() - 3600, "/");
                        setcookie("remembered_password", "", time() - 3600, "/");
                    }

                    
                    header("Location: dashboard.php");
                    exit();
                }

            } else {
                $loginError = "Invalid password.";
            }

        } else {
            $loginError = "User not found.";
        }

    } else {
        $loginError = "Please fill in all fields.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Quiz Platform</title>
    <link rel="stylesheet" href="css/login.css">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body>
    <div class="login-container">
        <h1>Login to your Account</h1>

        <?php if (!empty($loginError)): ?>
            <p style="color:red; text-align:center;"><?= htmlspecialchars($loginError) ?></p>
        <?php endif; ?>

        <form id="loginForm" action="" method="POST" onsubmit="return validateLogin();">

            <div class="form-group">
                <label for="loginEmail">Email Address</label>
                <input type="text" id="loginEmail" name="loginEmail" placeholder="example@email.com"
                    value="<?= isset($_COOKIE['remembered_email']) ? htmlspecialchars($_COOKIE['remembered_email']) : '' ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="loginPassword" placeholder="Your password"
                    value="<?= isset($_COOKIE['remembered_password']) ? htmlspecialchars($_COOKIE['remembered_password']) : '' ?>"
                    required>
            </div>

            <div class="form-group remember-me">
                <input type="checkbox" name="remember_me" <?= isset($_COOKIE['remembered_email']) ? 'checked' : '' ?>>
                Remember Me
            </div>

            <button type="submit" class="btn">Login</button>
        </form>

        <div class="form-footer">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>

    <script>
        function validateLogin() {
            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value.trim();

            if (!email || !password) {
                alert("Please fill all fields.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
