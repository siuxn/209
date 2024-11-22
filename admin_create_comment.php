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

// Fetch available parent questions from the database
$sql = "SELECT parentQuestionID, parentQuestion FROM parent_questions";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$parentQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get selected parent question and number of score ranges
    $parentQuestionID = $_POST['parentQuestionID'];  // Selected parent question ID
    $numRanges = $_POST['numRanges'];

    // Insert each score range and comment into the score_ranges table
    for ($i = 1; $i <= $numRanges; $i++) {
        $minScore = $_POST["minScore_$i"];
        $maxScore = $_POST["maxScore_$i"];
        $comment = $_POST["comment_$i"];
        $typeLevel = $_POST["typeLevel_$i"];  // Get the typeLevel for each score range

        // Insert score range into the score_ranges table
        $sqlScoreRange = "INSERT INTO score_ranges (parentQuestionID, minScore, maxScore, typelevel, comment) VALUES (?, ?, ?, ?, ?)";
        $stmtScoreRange = $pdo->prepare($sqlScoreRange);

        try {
            $stmtScoreRange->execute([$parentQuestionID, $minScore, $maxScore, $typeLevel, $comment]);
            echo "Score Range $i added successfully!<br>";
        } catch (PDOException $e) {
            echo "Error inserting score range: " . $e->getMessage() . "<br>";
        }
    }

    echo "All score ranges and comments added successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Score Ranges for Parent Question</title>
</head>
<body>
    <h2>Add Score Ranges for a Parent Question</h2>

    <form method="POST">
        <!-- Dropdown to select Parent Question -->
        <label for="parentQuestionID">Select Parent Question: </label>
        <select id="parentQuestionID" name="parentQuestionID" required>
            <option value="">Select a Parent Question</option>
            <?php foreach ($parentQuestions as $parent) { ?>
                <option value="<?php echo $parent['parentQuestionID']; ?>"><?php echo $parent['parentQuestion']; ?></option>
            <?php } ?>
        </select><br><br>

        <!-- Number of Score Ranges -->
        <label for="numRanges">Number of Score Ranges: </label>
        <input type="number" id="numRanges" name="numRanges" min="1" required><br><br>

        <!-- Dynamic fields for score ranges -->
        <div id="score-ranges-container"></div><br><br>

        <input type="submit" value="Submit">
    </form>

    <script>
        document.getElementById('numRanges').addEventListener('input', function() {
            var numRanges = parseInt(this.value);
            var container = document.getElementById('score-ranges-container');
            container.innerHTML = '';  // Clear previous fields

            for (var i = 1; i <= numRanges; i++) {
                var rangeHTML = `
                    <h3>Score Range ${i}</h3>
                    <label for="minScore_${i}">Min Score: </label><input type="number" id="minScore_${i}" name="minScore_${i}" required><br>
                    <label for="maxScore_${i}">Max Score: </label><input type="number" id="maxScore_${i}" name="maxScore_${i}" required><br>
                    <label for="typeLevel_${i}">Type Level: </label><input type="text" id="typeLevel_${i}" name="typeLevel_${i}" required><br>
                    <label for="comment_${i}">Comment: </label><textarea id="comment_${i}" name="comment_${i}" rows="4" cols="50" required></textarea><br><br>
                `;
                container.innerHTML += rangeHTML;
            }
        });
    </script>
</body>
</html>
