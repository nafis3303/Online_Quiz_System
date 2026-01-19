<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}


$studentId = $_SESSION['user_id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quizzers";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);
$sql = "
    SELECT r.quiz_id, q.title, r.score, r.date_taken, r.time_taken
    FROM results r
    JOIN quizzes q ON r.quiz_id = q.id
    WHERE r.student_id = $studentId
    ORDER BY r.date_taken DESC
";
$result = $conn->query($sql);
$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $quiz_id = $row['quiz_id'];
    $questions = [];
    $qRes = $conn->query("SELECT * FROM questions WHERE quiz_id = $quiz_id");

    while ($q = $qRes->fetch_assoc()) {
        $questions[$q['id']] = $q;
    }
    $answers = [];
    $aRes = $conn->query("SELECT * FROM user_answers WHERE quiz_id = $quiz_id AND student_id = $studentId");
    while ($a = $aRes->fetch_assoc()) {
        $answers[$a['question_id']] = $a['selected_option'];
    }
    $quizzes[] =
        [
            'meta' => $row,
            'questions' => $questions,
            'answers' => $answers
        ];
}
$conn->close();


?>


<!DOCTYPE html>
<html>

<head>
    <title>My Results - QuizMaster</title>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="view_quiz.php">View Quizzes</a></li>
                <li><a href="view_results.php">My Results</a></li>
            </ul>
            <form action="../logout.php" method="post">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </aside>
        <main class="content-area">
            <h1>My Quiz Results</h1>
            <?php if (count($quizzes) === 0): ?>
                <p>You haven't attempted any quizzes yet.</p>
            <?php endif; ?>
            <?php foreach ($quizzes as $quiz): ?>
                <div class="quiz-box">
                    <h2><?= htmlspecialchars($quiz['meta']['title']) ?></h2>

                    <div class="meta">
                        <strong>Score:</strong> <?= $quiz['meta']['score'] ?><br>
                        <strong>Date Taken:</strong> <?= $quiz['meta']['date_taken'] ?><br>
                        <strong>Time Taken:</strong> <?= $quiz['meta']['time_taken'] ?> seconds
                    </div>

                    <h3>Question Review</h3>

                    <?php foreach ($quiz['questions'] as $qid => $q): ?>
                        <?php
                        $selected = $quiz['answers'][$qid] ?? null;
                        $correct = $q['correct_option'];
                        $isCorrect = ($selected == $correct);
                        ?>

                        <div class="question-block">
                            <strong>Q:</strong> <?= htmlspecialchars($q['question_text']) ?><br>
                            <strong>Your Answer:</strong>
                            <?= htmlspecialchars($q["option_$selected"] ?? 'Not Answered') ?><br>

                            <?php if ($isCorrect): ?>
                                <span class="correct">Correct</span>
                            <?php else: ?>
                                <span class="wrong">Wrong</span><br>
                                <strong>Correct Answer:</strong>
                                <?= htmlspecialchars($q["option_$correct"]) ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <a href="../dashboard.php" class="back-btn">Back to Dashboard</a>



        </main>

    </div>

</body>

</html>