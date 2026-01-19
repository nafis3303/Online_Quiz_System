<?php
session_start();

if (!isset($_SESSION['role']) || trim(strtolower($_SESSION['role'])) !== 'teacher') {
    header("Location: ../dashboard.php");
    exit();
}

$teacherId = (int) ($_SESSION['user_id'] ?? 0);

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "quizzers";

$conn = mysqli_connect($servername, $db_username, $db_password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$quizzesResult = mysqli_query($conn, "SELECT id, title FROM quizzes WHERE created_by = $teacherId ORDER BY id DESC");

$quiz_id = (int) ($_GET['quiz_id'] ?? 0);
$results = [];

if ($quiz_id > 0) {
    $sql = "
        SELECT r.id, u.username, r.score, r.date_taken
        FROM results r
        JOIN users u ON r.student_id = u.id
        WHERE r.quiz_id = $quiz_id
        ORDER BY r.date_taken DESC
    ";
    $resultsResult = mysqli_query($conn, $sql);

    if ($resultsResult) {
        while ($row = mysqli_fetch_assoc($resultsResult)) {
            $results[] = $row;
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>View Results - QuizMaster</title>
    <link rel="stylesheet" href="../css/dashboard.css" />
    <link rel="stylesheet" href="../css/viewrs.css" />
</head>

<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="create_quiz.php">Create Quiz</a></li>
                <li><a href="add_question.php">Add Questions</a></li>
                <li><a href="edit_question.php">Edit Questions</a></li>
                <li><a href="delete_question.php">Delete Questions</a></li>
                <li><a href="teacher_result.php">View Results</a></li>
            </ul>

            <form action="../logout.php" method="post">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </aside>

        <main class="content-area">
            <div class="breadcrumbs">Dashboard &gt; View Results</div>
            <h1>View Results</h1>

            <form method="GET" action="" class="select-quiz-form">
                <label for="quiz_id"><strong>Select Quiz:</strong></label>
                <select id="quiz_id" name="quiz_id" onchange="this.form.submit()">
                    <option value="">-- Select Quiz --</option>
                    <?php if ($quizzesResult): ?>
                        <?php while ($row = mysqli_fetch_assoc($quizzesResult)): ?>
                            <option value="<?= (int) $row['id'] ?>" <?= $quiz_id === (int) $row['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['title']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </form>

            <?php if ($quiz_id > 0 && count($results) > 0): ?>
                <div class="table-wrap">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Score</th>
                                <th>Taken At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $res): ?>
                                <tr>
                                    <td><?= htmlspecialchars($res['username']) ?></td>
                                    <td><?= (int) $res['score'] ?></td>
                                    <td><?= htmlspecialchars($res['date_taken']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($quiz_id > 0): ?>
                <p>No results found for this quiz.</p>
            <?php endif; ?>

            <a href="../dashboard.php" class="back-btn">Back to Dashboard</a>
        </main>
    </div>
</body>

</html>