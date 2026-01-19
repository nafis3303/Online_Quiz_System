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
$questions = [];
$quiz_id = intval($_GET['quiz_id'] ?? 0);
$user_id = intval($_SESSION['user_id'] ?? 0);

 if ($_SERVER['REQUEST_METHOD'] === 'POST')
 
{
    $question_id = intval($_POST['question_id'] ?? 0);
    $question = mysqli_real_escape_string($conn, $_POST['question'] ?? '');
    $option1 = mysqli_real_escape_string($conn, $_POST['option1'] ?? '');
    $option2 = mysqli_real_escape_string($conn, $_POST['option2'] ?? '');
    $option3 = mysqli_real_escape_string($conn, $_POST['option3'] ?? '');
    $option4 = mysqli_real_escape_string($conn, $_POST['option4'] ?? '');
    $correct_option = intval($_POST['correct_option'] ?? 0);

if ($question_id && $question && $option1 && $option2 && $option3 && $option4 && $correct_option >= 1 && $correct_option <= 4) 
{
        $sql = "UPDATE questions SET 
            question_text='$question', 
            option_1='$option1', 
            option_2='$option2', 
            option_3='$option3', 
            option_4='$option4', 
            correct_option=$correct_option 
            WHERE id=$question_id";

if (mysqli_query($conn, $sql)) 
{
            $successMessage = "Question updated successfully.";
        } 
        else 
        
        {
            $error = "Failed to update question.";
        }
    } 
    else 
    
    {
        $error = "Please fill all fields correctly.";
}
}
$quizzes = [];
$quizSql = "SELECT id, title FROM quizzes WHERE created_by = $user_id";
$quizResult = mysqli_query($conn, $quizSql);

if ($quizResult) 
{
    while ($row = mysqli_fetch_assoc($quizResult)) 
    {
        $quizzes[] = $row;
    }
}

if ($quiz_id > 0)
 {
    $questionSql = "SELECT * FROM questions WHERE quiz_id = $quiz_id";
    $questionsResult = mysqli_query($conn, $questionSql);
    if ($questionsResult)
     {
        while ($row = mysqli_fetch_assoc($questionsResult)) 
        
    {
        $questions[] = $row;
     }
}
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Questions - QuizMaster</title>
    <link rel="stylesheet" href="../css/editqs.css" />

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
        <div class="breadcrumbs">Dashboard &gt; Edit Questions</div>

        <h1>Edit Questions</h1>


    <?php if ($successMessage): ?>

    <div class="success-message"><?= htmlspecialchars($successMessage) ?></div>

    <?php endif; ?>

        <?php if ($error): ?>

            <div class="error-message"><?= htmlspecialchars($error) ?></div>

        <?php endif; ?>


        <form method="GET" action="">

            <label for="quiz_id">Select Quiz to Edit:</label>

            <select id="quiz_id" name="quiz_id" onchange="this.form.submit()">

            <option value="">-- Select Quiz --</option>

                <?php foreach ($quizzes as $qz): ?>
                <option value="<?= $qz['id'] ?>" <?= ($quiz_id === (int)$qz['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($qz['title']) ?>
                    </option>

             <?php endforeach; ?>
           
         </select>
        </form>
       
     <?php if ($quiz_id && count($questions) > 0): ?>

         <h2>Questions for Selected Quiz</h2>

            <?php foreach ($questions as $q): ?>
                
                
            <form method="POST" action="" style="border:1px solid #ccc; padding:15px; margin-bottom:10px;">
                    
            <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                   
            <div class="form-group">
                        <label>Question Text:</label>
                        <textarea name="question" required><?= htmlspecialchars($q['question_text']) ?></textarea>
                    </div>
                    
            <div class="form-group">
                        <label>Option 1:</label>
                        <input type="text" name="option1" value="<?= htmlspecialchars($q['option_1']) ?>" required>
                    </div>

                    <div class="form-group">

                        <label>Option 2:</label>
                        <input type="text" name="option2" value="<?= htmlspecialchars($q['option_2']) ?>" required>
                    </div>


                    <div class="form-group">
                        <label>Option 3:</label>
                        <input type="text" name="option3" value="<?= htmlspecialchars($q['option_3']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Option 4:</label>
                        <input type="text" name="option4" value="<?= htmlspecialchars($q['option_4']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Correct Option (1-4):</label>
                        <input type="number" name="correct_option" min="1" max="4" value="<?= $q['correct_option'] ?>" required>
                    </div>

                    <button type="submit" class="quick-btn">Update Question</button>
                </form>

         <?php endforeach; ?>

    <?php elseif ($quiz_id): ?>

    <p>No questions found for this quiz.</p>
    <?php endif; ?>

<a href="../dashboard.php" class="quick-btn" style="background:#777; margin-top:10px;">Back to Dashboard</a>
</main>
</div>
</body>
</html>