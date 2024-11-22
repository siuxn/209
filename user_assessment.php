<?php
// Start the session to access session variables
session_start();

// Connect to your database
$host = 'localhost'; // Database host
$dbname = 'dip209';  // Database name
$username = 'root';  // Database username
$password = '';      // Database password

// Check if the user is logged in and get user_id from session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header('Location: signin.php');
    exit;
}

// Assign user_id from session
$user_id = $_SESSION['user_id'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Query to fetch all parent questions
$sql = "SELECT parentQuestionID, category, parentQuestion FROM parent_questions";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute();
    $parentQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching parent questions: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Questions List</title>
    <style>
        .parent-question-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .parent-question-info {
            max-width: 80%;
        }

        .start-quiz-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .start-quiz-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>List of Parent Questions</h2>

    <?php if (!empty($parentQuestions)): ?>
        <?php foreach ($parentQuestions as $question): ?>
            <div class="parent-question-item">
                <div class="parent-question-info">
                    <p><strong>Parent Question:</strong> <?php echo htmlspecialchars($question['parentQuestion']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($question['category']); ?></p>
                </div>
                <a href="start_quiz.php?parentQuestionID=<?php echo $question['parentQuestionID']; ?>&user_id=<?php echo $user_id; ?>" class="start-quiz-btn">Start Quiz</a>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No parent questions found.</p>
    <?php endif; ?>
    
</body>
</html>
