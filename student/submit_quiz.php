<?php


session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$studentId = $_SESSION['user_id'];
$quizId = intval($_GET['quiz_id'] ?? ($_POST['quiz_id'] ?? 0));


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "quizzers";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$quiz = null;
$questions = [];
$submitted = false;
$alreadyAttempted = false;
$score = 0;



$check = $conn->query("SELECT * FROM results WHERE student_id = $studentId AND quiz_id = $quizId");
if ($check->num_rows > 0) {
    $alreadyAttempted = true;
    $existingResult = $check->fetch_assoc();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyAttempted) {
    $answers = $_POST['answers'] ?? [];

    if (count($answers) > 0) {
        $questionIds = implode(',', array_map('intval', array_keys($answers)));
        $result = $conn->query("SELECT id, correct_option FROM questions WHERE quiz_id = $quizId AND id IN ($questionIds)");

        $correctAnswers = [];
        while ($row = $result->fetch_assoc()) {
            $qid = $row['id'];
            $correct = $row['correct_option'];
            $correctAnswers[$qid] = $correct;

            if (isset($answers[$qid]) && intval($answers[$qid]) === intval($correct)) {
                $score++;
            }
        }
    } else {

        $correctAnswers = [];
    }

    $duration = intval($_POST['time_taken'] ?? 0);
    $stmt = $conn->prepare("INSERT INTO results (student_id, quiz_id, score, time_taken) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $studentId, $quizId, $score, $duration);
    $stmt->execute();
    $stmt->close();


    if (count($answers) > 0) {
        $answerStmt = $conn->prepare("INSERT INTO user_answers (student_id, quiz_id, question_id, selected_option) VALUES (?, ?, ?, ?)");
        foreach ($answers as $qid => $selected) {
            $qid = intval($qid);
            $selected = intval($selected);
            $answerStmt->bind_param("iiii", $studentId, $quizId, $qid, $selected);
            $answerStmt->execute();
        }

        $answerStmt->close();
    }

    $submitted = true;
}


if (!$submitted && !$alreadyAttempted && $quizId) {
    $quizRes = $conn->query("SELECT * FROM quizzes WHERE id = $quizId AND is_published = 1");
    if ($quizRes->num_rows === 1) {
        $quiz = $quizRes->fetch_assoc();
        $qRes = $conn->query("SELECT * FROM questions WHERE quiz_id = $quizId");
        while ($row = $qRes->fetch_assoc()) {
            $questions[] = $row;
        }
    }
}
$conn->close();
?>




<!DOCTYPE html>
<html>

<head>
    <title>Take Quiz - QuizMaster</title>
    <link rel="stylesheet" href="../css/submit_quiz.css">


</head>

<body>
    <div class="quiz-container">
        <?php if ($alreadyAttempted): ?>
            <div class="info-box">

                You have already attempted this quiz.<br>
                <strong>Your Score:</strong> <?= $existingResult['score'] ?>
            </div>

            <div style="center;">
                <a href="../dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
            </div>


        <?php elseif ($submitted): ?>

            <div class="result">
                Quiz submitted successfully!<br>
                <strong>Your Score:</strong> <?= $score ?> / <?= count($answers) ?>
            </div>

            <div style="center;">
                <a href="../dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
            </div>

        <?php elseif ($quiz): ?>

            <div class="timer"> Time Left: <span id="time">00:30</span></div>

            <h2><?= htmlspecialchars($quiz['title']) ?></h2>

            <form id="quizForm" method="POST">
                <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                <input type="hidden" name="time_taken" id="time_taken" value="0">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question">
                        <h3><?= ($index + 1) ?>. <?= htmlspecialchars($q['question_text']) ?></h3>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <label>
                                <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $i ?>" required>
                                <?= htmlspecialchars($q["option_$i"]) ?>
                            </label>

                        <?php endfor; ?>
                    </div>

                <?php endforeach; ?>

                <button type="submit" class="submit-btn"> Submit Quiz</button>

            </form>

        <?php else: ?>

            <p>Quiz not found or not available.</p>
        <?php endif; ?>

    </div>

    <?php if (!$submitted && !$alreadyAttempted && $quiz): ?>
        <script>
            const QUIZ_DURATION = 30;
            let timerDisplay = document.getElementById("time");
            let timeTakenField = document.getElementById("time_taken");


            let startTime = sessionStorage.getItem('quiz_start_time_<?= $quizId ?>');
            if (!startTime) {
                startTime = Date.now();
                sessionStorage.setItem('quiz_start_time_<?= $quizId ?>', startTime);
            }
            else {
                startTime = parseInt(startTime, 10);
            }


            function updateTimer() {
                const elapsedMs = Date.now() - startTime;
                let elapsedSeconds = Math.floor(elapsedMs / 1000);
                let timeLeft = QUIZ_DURATION - elapsedSeconds;

                if (timeLeft < 0) timeLeft = 0;

                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                timeTakenField.value = elapsedSeconds;

                if (timeLeft === 0) {
                    sessionStorage.removeItem('quiz_start_time_<?= $quizId ?>');
                    clearInterval(timerInterval);
                    document.getElementById("quizForm").submit();
                }
            }



            updateTimer();
            const timerInterval = setInterval(updateTimer, 1000);


            document.getElementById('quizForm').addEventListener('submit', function () {
                sessionStorage.removeItem('quiz_start_time_<?= $quizId ?>');
            });


        </script>

    <?php endif; ?>
</body>

</html>