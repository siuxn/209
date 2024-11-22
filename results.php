<?php
// Start the session to access session variables
session_start();

// Check if the quiz result is stored in the session
if (!isset($_SESSION['quiz_result'])) {
    echo "No results available. Please complete the quiz first.";
    exit;
}

// Retrieve the quiz result from the session
$quizResult = $_SESSION['quiz_result'];
$totalScore = $quizResult['score'];
$comment = $quizResult['comment'];

// Clear the session quiz result after displaying it
unset($_SESSION['quiz_result']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
</head>
<body>
    <h2>Quiz Results</h2>

    <h3>Total Score: <?php echo $totalScore; ?></h3>
    <h3>Comment:</h3>
    <p><?php echo htmlspecialchars($comment); ?></p>

    <a href="quiz.php">Go back to quiz</a>
</body>
</html>
