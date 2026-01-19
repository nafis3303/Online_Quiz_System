<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = trim(strtolower($_SESSION['role']));
if ($role !== 'student' && $role !== 'teacher') {
    header("Location: login.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

if ($username === 'User') {
    $conn = mysqli_connect("localhost", "root", "", "quizzers");
    if ($conn) {
        $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $fetchedUsername);

            if (mysqli_stmt_fetch($stmt) && $fetchedUsername) {
                $username = $fetchedUsername;
                $_SESSION['username'] = $username;
            }

            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <h2>Menu</h2>
        <ul>
            <?php if ($role === 'student'): ?>
                <!-- Student menu  -->
                <li><a href="student/profile.php">My Profile</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="student/view_quiz.php">Available Quizzes</a></li>
                <li><a href="student/view_results.php">My Results</a></li>

            <?php elseif ($role === 'teacher'): ?>
                <!-- Teacher menu -->
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="teacher/create_quiz.php">Create Quiz</a></li>
                <li><a href="teacher/add_question.php">Add Questions</a></li>
                <li><a href="teacher/edit_question.php">Edit Questions</a></li>
                <li><a href="teacher/delete_question.php">Delete Questions</a></li>
                <li><a href="teacher/teacher_results.php">View Results</a></li>
            <?php endif; ?>
        </ul>

        <form action="logout.php" method="POST" style="margin-top: 20px;">
            <button type="submit" class="btn logout-btn">Logout</button>
        </form>
    </aside>

    <main class="content-area">
        <?php if ($role === 'student'): ?>
            
            <h1>Welcome, <?= htmlspecialchars($username) ?>(Student)</h1>
            <p>Select an option to continue:</p>
            <div class="quick-actions">
                <a href="student/view_quiz.php" class="quick-btn">View Quizes</a>
                <a href="student/view_results.php" class="quick-btn">View My Results</a>
            </div>

        <?php elseif ($role === 'teacher'): ?>
            
            <h1>Welcome, <?= htmlspecialchars($username) ?>(Teacher)</h1>
            <p>Select an option to continue:</p>
            <div class="quick-actions">
                <a href="teacher/create_quiz.php" class="quick-btn">Create Quiz</a>
                <a href="teacher/add_question.php" class="quick-btn">Add Questions</a>
                <a href="teacher/teacher_result.php" class="quick-btn">View Results</a>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
