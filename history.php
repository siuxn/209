<?php
// Start the session
// Example: After successful login
$_SESSION['userID'] = $fetchedUserID; // Assign the user's ID to the session
session_start();

$userID = $_SESSION['userID'];
// Database connection details
$host = 'localhost'; // Database host
$dbname = 'dip209';  // Database name
$username = 'root';  // Database username
$password = '';      // Database password

// Fetch user ID from session or query string
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $_SESSION['user_id'] = $user_id; // Store in session for future use
} else {
    echo "Error: User ID not found in session or query string.<br>";
    header('Location: signin.php');
    exit;
}

try {
    // Establish database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// SQL query to fetch test history for the given user
$sql = "SELECT 
    th.testHistoryID, 
    pq.parentQuestion AS parentQuestion, 
    th.score, 
    th.comment, 
    th.timestamp, 
    sr.typelevel 
FROM TestHistory th
JOIN parent_questions pq ON th.parentQuestionID = pq.parentQuestionID
JOIN score_ranges sr ON th.score BETWEEN sr.minScore AND sr.maxScore AND sr.parentQuestionID = th.parentQuestionID
WHERE th.userID = :userID
ORDER BY th.timestamp DESC;
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['userID' => $user_id]); // Execute the query with the user ID

// Fetch results
$testHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Test History</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #bcc1b3;
        }
        /* Basic styling for the cards */
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            margin: 10px;
            padding: 15px;
            width: 300px;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .card h3 {
            margin: 0;
            font-size: 1.2em;
        }
        
        .card .score {
            color: #4caf50;
            font-weight: bold;
        }
        .card .timestamp {
            font-size: 0.9em;
            color: #888;
        }
        .card button {
            display: inline-block;
            margin: 10px 10px;
            padding: 10px 20px;
            text-decoration: none;
            background-color: #7d8479;
            color: white;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .card button:hover {
            background-color: #7d8479;
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
            padding-top: 60px;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        /* Close button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Styling for the end message */
        .end-message {
            font-size: 1.2em;
            color: #888;
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script>
        // Function to open the modal and display the comment
        function openModal(comment, questionTitle) {
            var modal = document.getElementById("myModal");
            var modalContent = document.getElementById("modalContent");
            var modalQuestion = document.getElementById("modalQuestion");

            // Set the question and comment in the modal
            modalQuestion.innerText = questionTitle;
            modalContent.innerHTML = "<p>" + comment + "</p>";

            modal.style.display = "block";
        }

        // Function to close the modal
        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</head>
<body>
    <div class ="container" style ="margin-top:10px;">
        <h2>Your Quiz History</h2>

        <?php if (!empty($testHistory)): ?>
            <div class="cards-container">
                <?php $lastIndex = count($testHistory) - 1; // Get the index of the last item ?>
                <?php foreach ($testHistory as $index => $history): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($history['parentQuestion']); ?></h3>
                        <p class="score">Score: <?php echo htmlspecialchars($history['score']); ?></p>
                        <p class="timestamp">Completed on: <?php echo htmlspecialchars($history['timestamp']); ?></p>
                        <p class="level">Level: <?php echo htmlspecialchars($history['typelevel']); ?></p>
                        <!-- Button to open modal and show comment -->
                        <button onclick="openModal('<?php echo htmlspecialchars($history['comment']); ?>', '<?php echo htmlspecialchars($history['parentQuestion']); ?>', '<?php echo htmlspecialchars($history['typelevel']); ?>')">See More</button>

                    </div>

                    <!-- Display "You've reached the end" message only for the last item -->
                    <?php if ($index === $lastIndex): ?>
                        <div class="end-message">
                            You've reached the end.
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You have not completed any preliminary tests. <a href="user_assessment.php?user_id=<?php echo $user_id; ?>"><button class="btn btn-warning">Start now!</button><a></p>
        <?php endif; ?>

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalQuestion">Question</h2>
                <div id="modalContent">
                    <!-- The comment will be displayed here -->
                </div>
            </div>
        </div>
     </div>
</body>
</html>
