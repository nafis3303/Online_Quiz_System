<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') 

{
    header("Location: ../dashboard.php");
    exit();
}
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
$error = "";

$quiz_id = intval($_GET['quiz_id'] ?? 0);
$question_id = intval($_GET['question_id'] ?? 0);

if ($question_id && $_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) 

{
    $sql = "DELETE FROM questions WHERE id = $question_id";
    if (mysqli_query($conn, $sql)) 
    
    {
        $successMessage = "Question deleted successfully.";
    } 
    else 
    {
        $error = "Failed to delete question.";
    }
}
$quizzesResult = mysqli_query($conn, "SELECT id, title FROM quizzes WHERE created_by = '{$_SESSION['user_id']}'");

$questions = [];
if ($quiz_id) 

{
    $questionsResult = mysqli_query($conn, "SELECT * FROM questions WHERE quiz_id = $quiz_id");
    while ($row = mysqli_fetch_assoc($questionsResult)) 
    {
        $questions[] = $row;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Delete Questions - QuizMaster</title>
    <link rel="stylesheet" href="../css/deleteqs.css" />
</head>
<body>
<div class="dashboard-layout">
       <aside class="sidebar">

        <h2>Menu</h2>
        <ul>
            <li><a href="../dashboard.php"> Dashboard</a></li>
            <li><a href="create_quiz.php"> Create Quiz</a></li>
            <li><a href="add_question.php"> Add Questions</a></li>
            <li><a href="edit_question.php"> Edit Questions</a></li>
            <li><a href="delete_question.php"> Delete Questions</a></li>
            <li><a href="teacher_results.php"> View Results</a></li>
        </ul>

        <form action="../logout.php" method="post">
        <button type="submit" class="logout-btn">Logout</button>
        </form>
    </aside>
        <main class="content-area">
    <div class="breadcrumbs">Dashboard &gt; Delete Questions</div>

     <h1>Delete Questions</h1>

        <form method="GET" action="">
            <label for="quiz_id">Select Quiz:</label>
            <select id="quiz_id" name="quiz_id" onchange="this.form.submit()">
            <option value="">-- Select Quiz --</option>

            <?php while ($row = mysqli_fetch_assoc($quizzesResult)): ?>
            <option value="<?= $row['id'] ?>" <?= $quiz_id === (int)$row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['title']) ?></option>
             <?php endwhile; ?>
           
            </select>
        </form>

        <?php if ($quiz_id && count($questions) > 0): ?>
            <h2>Questions for Selected Quiz</h2>
            <ul>
                <?php foreach ($questions as $q): ?>
                    <li>
                        <?= htmlspecialchars($q['question_text']) ?>
                        <a href="?quiz_id=<?= $quiz_id ?>&question_id=<?= $q['id'] ?>&delete=1">Delete</a>
                    </li>

    <?php endforeach; ?>
    </ul>

    <?php elseif ($quiz_id): ?>
        <p>No questions found for this quiz.</p>
        <?php endif; ?>

      <a href="../dashboard.php" class="quick-btn" style="background:#777; margin-top:10px;">Back to Dashboard</a>
    </main>
</div>

<?php if ($successMessage || $error): ?>
    <div class="popup-overlay" id="popupMessage">
    <div class="popup-box <?= $successMessage ? 'success' : 'error' ?>">
        <p><?= htmlspecialchars($successMessage ?: $error) ?></p>
        <button onclick="document.getElementById('popupMessage').style.display='none'">OK</button>
        </div>
    </div>
    <?php endif; ?>
<script>
    setTimeout(() => 
    {
    const popup = document.getElementById('popupMessage');
    if (popup) popup.style.display = 'none';
    }, 4000);
 
</script>
</body>
</html>