<?php
$host = 'localhost'; // Database host
$dbname = 'dip209';  // Database name
$username = 'root';  // Database username
$password = '';      // Database password
$user_id = $_SESSION['userID'];
session_start(); // Ensure session is started
$user_id = isset($_SESSION['userID']) ? $_SESSION['userID'] : null; // Use session variable consistently

if (isset($_SESSION['user_id'])) {
    $_SESSION['userID'] = $_SESSION['user_id']; // Normalize the key
    unset($_SESSION['user_id']); // Remove the old key
}



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Get parent question ID from URL or elsewhere
$parentQuestionID = isset($_GET['parentQuestionID']) ? $_GET['parentQuestionID'] : null;

if (!$parentQuestionID) {
    echo "Invalid Parent Question ID.";
    exit;
}

// Query to fetch the parent question name based on the parentQuestionID
$sql = "SELECT parentQuestion FROM parent_questions WHERE parentQuestionID = :parentQuestionID";
$stmt = $pdo->prepare($sql);
$stmt->execute(['parentQuestionID' => $parentQuestionID]);

// Fetch the result
$parentQuestion = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the parent question exists


// Query to fetch questions based on the parent question ID
$sql = "SELECT * FROM questions WHERE parent_question_id = :parentQuestionID";
$stmt = $pdo->prepare($sql);
$stmt->execute(['parentQuestionID' => $parentQuestionID]);

$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle session to store the current question index
session_start();

// Initialize question index
if (!isset($_SESSION['questionIndex'])) {
    $_SESSION['questionIndex'] = 0;
}

// Handle next and previous buttons
if (isset($_POST['next'])) {
    $_SESSION['questionIndex'] = min($_SESSION['questionIndex'] + 1, count($questions) - 1); // Increment index
} elseif (isset($_POST['previous'])) {
    $_SESSION['questionIndex'] = max($_SESSION['questionIndex'] - 1, 0); // Decrement index
}

// Get the current question to display
$currentQuestionIndex = $_SESSION['questionIndex'];
$currentQuestion = $questions[$currentQuestionIndex];

// Check if the form is submitted
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_answers'])) {
    $totalScore = 0;
    $answerSum = [];

    // Define the score for each answer option (A=1, B=2, C=3, D=4)
    $answerScores = [
        'A' => 1,
        'B' => 2,
        'C' => 3,
        'D' => 4
    ];

    // Calculate the total score
    foreach ($questions as $index => $question) {
        $selectedAnswer = isset($_POST["question_$index"]) ? $_POST["question_$index"] : null;

        if ($selectedAnswer && isset($answerScores[$selectedAnswer])) {
            $answerSum[$index] = $answerScores[$selectedAnswer];
            $totalScore += $answerScores[$selectedAnswer];
        }
    }

    // Fetch the corresponding comment
    $sql = "SELECT comment FROM score_ranges WHERE parentQuestionID = :parentQuestionID 
            AND minScore <= :totalScore AND maxScore >= :totalScore";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['parentQuestionID' => $parentQuestionID, 'totalScore' => $totalScore]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $comment = $result ? $result['comment'] : "No comment available for this score.";

    // Check if a record for this user and parentQuestionID already exists in testHistory
    $checkSql = "SELECT testHistoryID FROM testHistory WHERE userID = :userID AND parentQuestionID = :parentQuestionID";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute(['userID' => $user_id, 'parentQuestionID' => $parentQuestionID]);

    // If a record exists, update it; otherwise, insert a new one
    if ($checkStmt->rowCount() > 0) {
        // Record exists, update it
        $updateSql = "UPDATE testHistory SET score = :score, comment = :comment WHERE userID = :userID AND parentQuestionID = :parentQuestionID";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'score' => $totalScore,
            'comment' => $comment,
            'userID' => $user_id,
            'parentQuestionID' => $parentQuestionID
        ]);
    } else {
        // No record found, insert a new one
        $insertSql = "INSERT INTO testHistory (userID, parentQuestionID, score, comment) 
                      VALUES (:userID, :parentQuestionID, :score, :comment)";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            'userID' => $user_id,
            'parentQuestionID' => $parentQuestionID,
            'score' => $totalScore,
            'comment' => $comment
        ]);
    }

    // Notify the user and display the modal
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('resultModal');
            const modalContent = document.getElementById('modalContent');
            modalContent.innerHTML = `<p><b>Result</b>: $totalScore</p><p>$comment</p>`;
            setTimeout(() => {
                modal.style.display = 'block';
            }, 300); // Delay of 300ms
        });
    </script>";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #bcc1b3;
        }
        .container {
            color: black;
           border-radius:10px;
            padding: 20px;
            margin-top: 20px;
            margin-left: 30px;
            margin-right: 30px;
            background-color: white;
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .btn, .delete-btn {
            display: inline-block;
            margin: 10px 10px;
            padding: 10px 20px;
            border: 1px solid black;
            width:100px;
            text-decoration: none;
            background-color: white;
            color: black;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover, .delete-btn:hover {
            background-color: #7d8479;
            color:white;
        }
        .btn-submit, .delete-btn {
            display: inline-block;
            margin: 10px 10px;
            padding: 10px 20px;
            border: 1px solid black;
            width:150px;
            text-decoration: none;
            background-color: white;
            color: black;
            border-radius: 5px;
            margin-left:90px;
            transition: background-color 0.3s;
        }
        .btn-submit:hover, .delete-btn:hover {
            background-color: #73878b;
            color:white;
        }
        .btn-history, .delete-btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            border: 1px solid black;
            width:150px;
            text-decoration: none;
            background-color: white;
            color: black;
            border-radius: 5px;
            margin-left:90px;
            transition: background-color 0.3s;
        }
        .btn-history:hover, .delete-btn:hover {
            background-color: #4d5d42;
            color:white;
        }
        
    </style>
</head>
<body>
<a style ="margin-top:30px;margin-left:30px;"href="user_assessment.php?user_id=<?php echo $user_id; ?>">Back</a>
    <h1 style="margin-left:30px; color: #595e4d;">  
        <?php
            // Assuming $parentQuestion is already set and fetched from the database
            echo "<p>" . htmlspecialchars($parentQuestion['parentQuestion']) . "</p>";
        ?>
    </h1>
    <div id="resultModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div id="modalContent"></div>
            <a href="history.php?user_id=<?php echo $user_id; ?>">

                    <button class="btn-history">View History</button>
                </a>
        </div>
    </div>
    <div class="container">
        <!-- Display the current question number and question text -->
        <h2 style="font-size:25px; max-width:70%;"><?php echo ($currentQuestionIndex + 1) . ". " . htmlspecialchars($currentQuestion['question']); ?></h2>

        <form method="POST" action="">
            <label>
                <input type="radio" name="question_<?php echo $currentQuestionIndex; ?>" value="A">
                <?php echo htmlspecialchars($currentQuestion['answerA']); ?>
            </label><br><br>

            <label>
                <input type="radio" name="question_<?php echo $currentQuestionIndex; ?>" value="B">
                <?php echo htmlspecialchars($currentQuestion['answerB']); ?>
            </label><br><br>

            <label>
                <input type="radio" name="question_<?php echo $currentQuestionIndex; ?>" value="C">
                <?php echo htmlspecialchars($currentQuestion['answerC']); ?>
            </label><br><br>

            <label>
                <input type="radio" name="question_<?php echo $currentQuestionIndex; ?>" value="D">
                <?php echo htmlspecialchars($currentQuestion['answerD']); ?>
            </label><br><br>

            <h5 style="color:grey;">Question <?php echo ($currentQuestionIndex + 1) . " out of " . count($questions); ?></h5>

            <!-- Navigation buttons -->

            <!-- Hide 'Previous' button on the first question -->
            <?php if ($currentQuestionIndex > 0): ?>
                <button type="submit" name="previous" value="previous"  class="btn">Previous</button>
            <?php endif; ?>

            <!-- Hide 'Next' button on the last question -->
            <?php if ($currentQuestionIndex < count($questions) - 1): ?>
                 <button type="submit" name="next" value="next" class="btn">Next</button>

            <?php endif; ?>

            <!-- Show the submit button only on the last question -->
            <?php if ($currentQuestionIndex == count($questions) - 1): ?>
                <input type="submit" name="submit_answers" class="btn-submit" value="Submit Answers">
            <?php endif; ?>
        </form>
    </div>
</body>
<script>
        // Close the modal
        function closeModal() {
            document.getElementById('resultModal').style.display = 'none';
        }
    </script>
</html>
