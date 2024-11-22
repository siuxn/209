<?php
$servername = "localhost";  // or your database host
$username = "root";         // your database username
$password = "";             // your database password
$dbname = "dip209";         // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $id = $_POST['id'];
    $question = $_POST['question'];
    $answerA = $_POST['answerA'];
    $marksA = $_POST['marksA'];
    $answerB = $_POST['answerB'];
    $marksB = $_POST['marksB'];
    $answerC = $_POST['answerC'];
    $marksC = $_POST['marksC'];
    $answerD = $_POST['answerD'];
    $marksD = $_POST['marksD'];
    $comment = $_POST['comment'];

    // Create the SQL query to update the question
    $sql = "UPDATE questions 
            SET question = '$question', 
                answerA = '$answerA', marksA = '$marksA', 
                answerB = '$answerB', marksB = '$marksB', 
                answerC = '$answerC', marksC = '$marksC', 
                answerD = '$answerD', marksD = '$marksD'
            WHERE id = $id";
    
    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Update the score_ranges table
    $sqlScoreRange = "UPDATE score_ranges
                      SET comment = '$comment'
                      WHERE parentQuestionID = $id";
    
    if ($conn->query($sqlScoreRange) === TRUE) {
        echo "Score range updated successfully";
    } else {
        echo "Error updating score range: " . $conn->error;
    }
}
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parentQuestionID from the AJAX request
$parentQuestionID = $_GET['parentQuestionID'];

// Fetch questions and related score range data (including comment)
$sql = "SELECT q.*, 
       (SELECT comment FROM score_ranges WHERE parentQuestionID = q.parent_question_id ORDER BY maxScore DESC LIMIT 1) AS comment
FROM questions q
WHERE q.parent_question_id = $parentQuestionID
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>

    <!-- Bootstrap CSS CDN link -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Questions and Score Range</h2>
<a href ="preliminarydisplay.php"><i class="fa fa-angle-left" style="font-size:36px"></i></a>
    <?php
    if ($result->num_rows > 0) {
        echo '<table class="table table-striped table-bordered table-hover">';
        echo '<thead class="thead-dark">';
        echo '<tr>';
        echo '<th>Question</th>';
        echo '<th>Answer A</th>';
        echo '<th>Answer B</th>';
        echo '<th>Answer C</th>';
        echo '<th>Answer D</th>';
        echo '<th>Action</th>'; // Added Action column for editing
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Display each question, answer, marks, score range, and comment related to the parent question
        // Inside the while loop, print the data for each row
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['question'] . '</td>';
            echo '<td>' . $row['answerA'] . ' (Marks: ' . $row['marksA'] . ')</td>';
            echo '<td>' . $row['answerB'] . ' (Marks: ' . $row['marksB'] . ')</td>';
            echo '<td>' . $row['answerC'] . ' (Marks: ' . $row['marksC'] . ')</td>';
            echo '<td>' . $row['answerD'] . ' (Marks: ' . $row['marksD'] . ')</td>';
            echo '<td>
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" 
                            onclick="loadQuestionData(' . htmlspecialchars(json_encode($row)) . ')">
                       <i class="fas fa-pencil-alt"></i> Edit
                    </button>
                  </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo "No questions found for this parent question.";
    }
    $conn->close();
    ?>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" action="load_questions.php?parent_id=<?php echo $parentQuestionID; ?>">
          <input type="hidden" id="editQuestionID" name="id">
          <div class="form-group">
            <label for="editQuestion">Question</label>
            <input type="text" class="form-control" id="editQuestion" name="question" required>
          </div>
          <div class="form-group">
            <label for="editAnswerA">Answer A</label>
            <input type="text" class="form-control" id="editAnswerA" name="answerA" required>
            <label for="editMarksA">Marks A</label>
            <input type="number" class="form-control" id="editMarksA" name="marksA" required>
            <br>
          </div>
          <div class="form-group">
            <label for="editAnswerB">Answer B</label>
            <input type="text" class="form-control" id="editAnswerB" name="answerB" required>
            <label for="editMarksB">Marks B</label>
            <input type="number" class="form-control" id="editMarksB" name="marksB" required>
          </div>
          <br>

          <div class="form-group">
            <label for="editAnswerC">Answer C</label>
            <input type="text" class="form-control" id="editAnswerC" name="answerC" required>
            <label for="editMarksC">Marks C</label>
            <input type="number" class="form-control" id="editMarksC" name="marksC" required>
          </div>
          <br>
          <div class="form-group">
            <label for="editAnswerD">Answer D</label>
            <input type="text" class="form-control" id="editAnswerD" name="answerD" required>
            <label for="editMarksD">Marks D</label>
            <input type="number" class="form-control" id="editMarksD" name="marksD" required>
          </div>
          
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
  <!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Update Successful</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        The question and score range were successfully updated.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

</div>
<?php if (isset($updateSuccess) && $updateSuccess): ?>
    <script>
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
    </script>
<?php endif; ?>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Custom JavaScript for loading question data into modal -->
<script>
function loadQuestionData(question) {
    document.getElementById('editQuestionID').value = question.id;
    document.getElementById('editQuestion').value = question.question;
    document.getElementById('editAnswerA').value = question.answerA;
    document.getElementById('editMarksA').value = question.marksA;
    document.getElementById('editAnswerB').value = question.answerB;
    document.getElementById('editMarksB').value = question.marksB;
    document.getElementById('editAnswerC').value = question.answerC;
    document.getElementById('editMarksC').value = question.marksC;
    document.getElementById('editAnswerD').value = question.answerD;
    document.getElementById('editMarksD').value = question.marksD;
    document.getElementById('editComment').value = question.comment;
}
</script>

</body>
</html>
