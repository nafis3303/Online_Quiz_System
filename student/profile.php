<?php
session_start();

if (!isset($_SESSION['role']) || trim(strtolower($_SESSION['role'])) !== 'student') {
    header("Location: ../login.php");
    exit();
}

$studentId = (int) ($_SESSION['user_id'] ?? 0);
if ($studentId <= 0) {
    header("Location: ../login.php");
    exit();
}
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "quizzers";
$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
//student info
$student = null;
$stmt = mysqli_prepare($conn, "SELECT id, username, email, role FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $studentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result && mysqli_num_rows($result) === 1) {
    $student = mysqli_fetch_assoc($result);
}
mysqli_stmt_close($stmt);
$attempted = 0;
$avgScore = null;
$statsSql1 = "SELECT COUNT(*) AS attempted, AVG(score) AS avg_score FROM results WHERE student_id = ?";
$stmt = mysqli_prepare($conn, $statsSql1);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $resStats = mysqli_stmt_get_result($stmt);
    if ($resStats) {
        $stats = mysqli_fetch_assoc($resStats);
        $attempted = (int) ($stats['attempted'] ?? 0);
        $avgScore = $stats['avg_score'];
    }
    mysqli_stmt_close($stmt);
} else {
    $statsSql2 = "SELECT COUNT(*) AS attempted, AVG(score) AS avg_score FROM results WHERE user_id = ?";
    $stmt2 = mysqli_prepare($conn, $statsSql2);
    if ($stmt2) {
        mysqli_stmt_bind_param($stmt2, "i", $studentId);
        mysqli_stmt_execute($stmt2);
        $resStats2 = mysqli_stmt_get_result($stmt2);
        if ($resStats2) {
            $stats2 = mysqli_fetch_assoc($resStats2);
            $attempted = (int) ($stats2['attempted'] ?? 0);
            $avgScore = $stats2['avg_score'];
        }
        mysqli_stmt_close($stmt2);
    }
}
mysqli_close($conn);

$username = $student['username'] ?? ($_SESSION['username'] ?? 'Student');
$email = $student['email'] ?? 'N/A';
$role = $student['role'] ?? 'student';

$avgScoreText = ($avgScore === null) ? "N/A" : number_format((float) $avgScore, 2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>MY Profile</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <div class="dashboard-layout">

        <aside class="sidebar">
            <div class="sidebar-top">
                <h2>Menu</h2>
                <ul>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="../dashboard.php">Dashboard</a></li>
                    <li><a href="view_quiz.php">View Quizzes</a></li>
                    <li><a href="view_results.php">My Results</a></li>
                    
                </ul>
            </div>

            <div class="sidebar-bottom">
                <form action="../logout.php" method="post">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </aside>

        <main class="content-area">
            <div class="breadcrumbs">Dashboard > My Profile</div>

            <h1>My Profile</h1>

            <div class="profile-card">
                <p><strong>Name:</strong> <?= htmlspecialchars($username) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($role) ?></p>
            </div>

            <h2>Performance Summary</h2>

            <div class="profile-card">
                <p><strong>Quizzes Attempted:</strong> <?= $attempted ?></p>
                <p><strong>Average Score:</strong> <?= htmlspecialchars($avgScoreText) ?></p>
            </div>

            <a href="../dashboard.php" class="back-btn">Back to Dashboard</a>
        </main>

    </div>

</body>
</html>