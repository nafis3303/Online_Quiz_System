<?php
session_start();

if (!isset($_SESSION['role']) || trim(strtolower($_SESSION['role'])) !== 'student') {
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
$quizzes = [];
$sql = "SELECT id, title, description FROM quizzes WHERE is_published = 1";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $quizzes[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Available Quizzes - QuizMaster</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/view_quiz.css">


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
            <div class="breadcrumbs">Dashboard > View Quizzes</div>
            <h1>Available Quizzes</h1>
            <div class="quiz-card-container">
                <?php if (count($quizzes) === 0): ?>
                    <p>No quizzes available right now.</p>
                <?php else: ?>
                    <?php foreach ($quizzes as $quiz): ?>
                        <div class="quiz-card">
                            <h2><?= htmlspecialchars($quiz['title']) ?></h2>
                            <p><?= htmlspecialchars($quiz['description']) ?></p>
                            <p><strong>Duration:</strong> 30 seconds</p>

                            <form method="GET" action="submit_quiz.php">
                                <input type="hidden" name="quiz_id" value="<?= (int) $quiz['id'] ?>">
                                <button type="submit" class="start-btn">Start Quiz</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>

</body>

</html>