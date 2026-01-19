
<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "quizzers"; 

$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);


if (!$conn) 
{
    die("Connection failed: " . mysqli_connect_error());
}

$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST')
 {
   
    $name = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? '');

   
    if ($name && $email && $password && $confirmPassword && $role && $password === $confirmPassword) 
    
    {
        
        $checkQuery = "SELECT * FROM users WHERE email = '$email'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) 
        
        {
            $error = "Email already exists!";
        } 
        else 
        {
          
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
           
            $sql = "INSERT INTO users (username, email, password, role) VALUES ('$name', '$email', '$hashedPassword', '$role')";
            if (mysqli_query($conn, $sql)) 
            {
                $successMessage = "Registration Successful";
            } else 
            {
                $error = "Registration failed.  try again.";
            }
        }
    } 
    else 
    
    {
        $error = "Please fill  all fields correctly.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - QuizMaster</title>

    
    <link rel="stylesheet" href="css/style.css">
 
</head>
<body>



<?php if (!empty($successMessage)): ?>
    
    <div class="popup-success">
        <div class="popup-box">
            <h2><?php echo $successMessage; ?></h2>
            <p><a href="login.php">Click here to login</a></p>
        </div>
    </div>

<?php endif; ?>

<div class="register-container">
    <h1>Create Your Account</h1>

    <?php if (!empty($error)): ?>
       
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="error-box" id="errorBox"></div>

    <form id="registerForm" action="" method="POST" onsubmit="return validateForm();">
        
    <div class="form-group">
            <label for="username">Full Name</label>
            <input type="text" id="username" name="username" placeholder="Enter your full name">
        </div>

        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="text" id="email" name="email" placeholder="example@email.com">
        </div>

        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a strong password">
        </div>

        <div class="form-group">
            
        <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password">
        </div>

        <div class="form-group">
            
        <label for="role">Account Type</label>
            <select id="role" name="role">
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>

        <button type="submit" class="register-btn">Register</button>
    </form>

    
    <div class="form-footer">
        Already registered? <a href="login.php">Login from  here</a>
    </div>

</div>


<script src="js/validation.js"></script>
</body>
</html>
