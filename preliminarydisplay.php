<?php
$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "dip209";         

// Create MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['delete'])) {
    $parentQuestionID = $_GET['delete'];

    // Delete questions associated with the parent question
    $sqlDeleteQuestions = "DELETE FROM questions WHERE parent_question_id = ?";
    $stmt = $conn->prepare($sqlDeleteQuestions);
    $stmt->bind_param('i', $parentQuestionID);
    $stmt->execute();

    // Delete the parent question
    $sqlDeleteParent = "DELETE FROM parent_questions WHERE parentQuestionID = ?";
    $stmtParent = $conn->prepare($sqlDeleteParent);
    $stmtParent->bind_param('i', $parentQuestionID);
    $stmtParent->execute();

    echo "<script>alert('Parent question and its associated questions have been deleted.');</script>";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $numQuestions = $_POST['numQuestions'] ?? '';
    $parentQuestion = $_POST['parentQuestion'] ?? '';

    if (empty($category) || empty($parentQuestion) || empty($numQuestions)) {
        echo "All fields are required.";
        exit;
    }

    // Insert parent question
    $sqlParent = "INSERT INTO parent_questions (category, parentQuestion) VALUES (?, ?)";
    $stmtParent = $conn->prepare($sqlParent);
    $stmtParent->bind_param('ss', $category, $parentQuestion);
    
    if ($stmtParent->execute()) {
        $parentQuestionID = $conn->insert_id;
        echo "Parent Question added successfully with ID: $parentQuestionID <br>";
    } else {
        echo "Error inserting parent question: " . $conn->error . "<br>";
        exit;
    }

    // Loop through each question and insert
// Loop through each question and insert
for ($i = 1; $i <= $numQuestions; $i++) {
  $question = $_POST["question_$i"] ?? '';
  $answerA = $_POST["answerA_$i"] ?? '';
  $marksA = $_POST["marksA_$i"] ?? '';
  $answerB = $_POST["answerB_$i"] ?? '';
  $marksB = $_POST["marksB_$i"] ?? '';
  $answerC = $_POST["answerC_$i"] ?? '';
  $marksC = $_POST["marksC_$i"] ?? '';
  $answerD = $_POST["answerD_$i"] ?? '';
  $marksD = $_POST["marksD_$i"] ?? '';

  // Validate answers and marks
  if (empty($question) || empty($answerA) || empty($answerB) || empty($answerC) || empty($answerD)) {
      echo "Question $i and all its answers must be filled.";
      exit;
  }

  // Insert question
  $sql = "INSERT INTO questions (parent_question_id, category, question, answerA, marksA, answerB, marksB, answerC, marksC, answerD, marksD) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('issssssssss', $parentQuestionID, $category, $question, $answerA, $marksA, $answerB, $marksB, $answerC, $marksC, $answerD, $marksD);

  if ($stmt->execute()) {
      echo "Question $i added successfully.<br>";
  } else {
      echo "Error inserting question $i: " . $conn->error . "<br>";
  }
}

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Questions</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- jQuery -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Bootstrap JS -->
</head>
<body>

<div class="container">
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">Admin Page</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" href="adminTheraphistCRUD.php">Therapist Page</a>
        </li>
        <li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Preliminary
  </a>
  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
    <!-- Ensure the correct file extension for the 'href' attribute -->
    <a class="dropdown-item disabled" href="#">View Preliminary Test</a>
    <a class="dropdown-item " href="displayt.php">View Score Range</a>

  </div>
</li>


        <li class="nav-item">
        <a class="nav-link" href="blog.php">Blog Upload</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="pending.php">Pending Account</a>
        </li>
    </ul>
  </div>
</nav>
<br>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">
      Add Preliminary Test
    </button>
<br>
<br>
    <?php
    include('db_connect.php');  // Include the database connection

    // Fetch Parent Questions from the database
    $sql = "SELECT * FROM parent_questions";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Displaying the table with Bootstrap styling
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Category</th>';
        echo '<th>Parent Question</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Display each parent question and category
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['category'] . '</td>';
            echo '<td>' . $row['parentQuestion'] . '</td>';
            echo '<td><a href="load_questions.php?parentQuestionID=' . $row['parentQuestionID'] . '" class="btn btn-primary">See More</a></td>';
            echo '<td>
            <a href="preliminarydisplay.php?delete=' . $row['parentQuestionID'] . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this question?\');">Delete</a>
            </td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo "No parent questions found.";
    }
    ?>
    <a href ="logout.php">
            <i class="fa fa-sign-out"  style="font-size:30px; color:red; padding-bottom:20px;"></i>
        </a>
</body>
</div>
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Parent question and its associated questions have been deleted.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
      <div class="modal-dialog" style ="max-width:50%;"role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addUserModalLabel">Add Preliminary Test</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form method="POST">
              <div class="form-group">
                <label for="parentQuestion">Parent Question: </label>
                <input type="text" class="form-control" id="parentQuestion" name="parentQuestion" required>
              </div>
              <div class="form-group">
                <label for="category">Category: </label>
                <input type="text" class="form-control" id="category" name="category" required>
              </div>
              <div class="form-group">
                <label for="numQuestions">Number of Questions: </label>
                <input type="number" class="form-control" id="numQuestions" name="numQuestions" min="1" required>
              </div>
              <div id="questions-container"></div>
              <input type="submit" class="btn btn-primary" value="Submit">
            </form>
          </div>
          
        </div>
      </div>
    </div>

<!-- Bootstrap JS and Popper.js (optional for modal functionality, etc.) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- Important: Ensure you're using the correct jQuery version -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script> <!-- Popper.js -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

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
    
</html>
