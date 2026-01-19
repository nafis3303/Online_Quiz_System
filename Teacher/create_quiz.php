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

if ($_SERVER['REQUEST_METHOD'] === 'POST')
 {
    $title = mysqli_real_escape_string($conn, $_POST['quizTitle'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['quizDescription'] ?? '');

    if ($title)
     {
        $sql = "INSERT INTO quizzes (title, description, created_by) VALUES ('$title', '$description', '{$_SESSION['user_id']}')";
        if (mysqli_query($conn, $sql)) {
            $successMessage = "Quiz created successfully.";
        } 
        else
         {
            $error = "Failed to create quiz. Please try again.";
        }
    } 
    else 
    {
        $error = "Quiz title is required.";
}
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create Quiz - QuizMaster</title>

    <link rel="stylesheet" href= "../css/creatqs.css"/>
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
    <li><a href="teacher_result.php"> View Results</a></li>
    </ul>

        <form action="../logout.php" method="post">
        <button type="submit" class="logout-btn">Logout</button>

        </form>

    </aside>

    <main class="content-area">

        <div class="breadcrumbs">Dashboard &gt; Create Quiz</div>
        <h1>Create New Quiz</h1>
        <?php if ($successMessage): ?>
        <div class="success-message"><?= htmlspecialchars($successMessage) ?></div>

        <?php endif; ?>

        <?php if ($error): ?>

        <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

    <form method="POST" action="">
         <div class="form-group">
              <label for="quizTitle">Quiz Title:</label>
             <input type="text" id="quizTitle" name="quizTitle" placeholder="Enter quiz title" required>

            </div>

         <div class="form-group">
                <label for="quizDescription">Description:</label>

                <textarea id="quizDescription" name="quizDescription" rows="4" placeholder="Quiz description"></textarea>
            </div>

            <button type="submit" class="quick-btn">Create Quiz</button>

            <a href="../dashboard.php" class="quick-btn" style="background:#777; margin-left:10px;">Back to Dashboard</a>
     </form>

</main>
</div>
</body>
</html>