<?php
// Connect to your database
$host = 'localhost'; // Database host
$dbname = 'dip209';  // Database name
$username = 'root';  // Database username
$password = '';      // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the inputs
    $category = $_POST['category'] ?? '';
    $numQuestions = $_POST['numQuestions'] ?? '';
    $parentQuestion = $_POST['parentQuestion'] ?? '';

    if (empty($category) || empty($parentQuestion) || empty($numQuestions)) {
        echo "All fields are required.";
        exit;
    }

    // Insert the parent question into the parent_questions table
    $sqlParent = "INSERT INTO parent_questions (category, parentQuestion) VALUES (?, ?)";
    $stmtParent = $pdo->prepare($sqlParent);

    try {
        $stmtParent->execute([$category, $parentQuestion]);
        $parentQuestionID = $pdo->lastInsertId();
        echo "Parent Question added successfully with ID: $parentQuestionID <br>";
    } catch (PDOException $e) {
        echo "Error inserting parent question: " . $e->getMessage() . "<br>";
        exit;
    }

    // Loop through each question to insert it into the questions table
    for ($i = 1; $i <= $numQuestions; $i++) {
        // Get each question and its respective answers and marks
        $question = $_POST["question_$i"] ?? '';
        $answerA = $_POST["answerA_$i"] ?? '';
        $marksA = $_POST["marksA_$i"] ?? '';
        $answerB = $_POST["answerB_$i"] ?? '';
        $marksB = $_POST["marksB_$i"] ?? '';
        $answerC = $_POST["answerC_$i"] ?? '';
        $marksC = $_POST["marksC_$i"] ?? '';
        $answerD = $_POST["answerD_$i"] ?? '';
        $marksD = $_POST["marksD_$i"] ?? '';

        // Validate if question and answers are not empty
        if (empty($question) || empty($answerA) || empty($answerB) || empty($answerC) || empty($answerD)) {
            echo "Question $i and all its answers must be filled.";
            exit;
        }

        // Prepare and execute the SQL statement to insert the question and answers
        $sql = "INSERT INTO questions (parent_question_id, category, question, answerA, marksA, answerB, marksB, answerC, marksC, answerD, marksD) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$parentQuestionID, $category, $question, $answerA, $marksA, $answerB, $marksB, $answerC, $marksC, $answerD, $marksD]);
            echo "Question $i added successfully!<br>";
        } catch (PDOException $e) {
            echo "Error executing query for Question $i: " . $e->getMessage() . "<br>";
        }
    }

    echo "All questions added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Questions</title>
</head>
<body>
    <h2>Add Multiple Questions</h2>

    <form method="POST">
        <label for="parentQuestion">Parent Question: </label>
        <input type="text" id="parentQuestion" name="parentQuestion" required><br><br>

        <label for="category">Category: </label>
        <input type="text" id="category" name="category" required><br><br>

        <label for="numQuestions">Number of Questions: </label>
        <input type="number" id="numQuestions" name="numQuestions" min="1" required><br><br>

        <div id="questions-container"></div>

        <input type="submit" value="Submit">
    </form>

    <script>
        document.getElementById('numQuestions').addEventListener('input', function() {
            var numQuestions = parseInt(this.value);
            var container = document.getElementById('questions-container');
            container.innerHTML = '';  // Clear previous fields

            for (var i = 1; i <= numQuestions; i++) {
                var questionHTML = `
                    <h3>Question ${i}</h3>
                    <label for="question_${i}">Question: </label><textarea id="question_${i}" name="question_${i}" rows="4" cols="50" required></textarea><br>

                    <label for="answerA_${i}">Answer A: </label><input type="text" id="answerA_${i}" name="answerA_${i}" required><br>
                    <label for="marksA_${i}">Marks for A: </label><input type="number" id="marksA_${i}" name="marksA_${i}" required><br>

                    <label for="answerB_${i}">Answer B: </label><input type="text" id="answerB_${i}" name="answerB_${i}" required><br>
                    <label for="marksB_${i}">Marks for B: </label><input type="number" id="marksB_${i}" name="marksB_${i}" required><br>

                    <label for="answerC_${i}">Answer C: </label><input type="text" id="answerC_${i}" name="answerC_${i}" required><br>
                    <label for="marksC_${i}">Marks for C: </label><input type="number" id="marksC_${i}" name="marksC_${i}" required><br>

                    <label for="answerD_${i}">Answer D: </label><input type="text" id="answerD_${i}" name="answerD_${i}" required><br>
                    <label for="marksD_${i}">Marks for D: </label><input type="number" id="marksD_${i}" name="marksD_${i}" required><br><br>
                `;
                container.innerHTML += questionHTML;
            }
        });
    </script>
</body>
</html>
