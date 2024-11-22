<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Score Range</title>

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome for the Edit Icon (optional) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- jQuery (required for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container mt-5">
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
    <a class="dropdown-item" href="preliminarydisplay.php">View Preliminary Test</a>
    <a class="dropdown-item disabled" href="#">View Score Range</a>

  </div>
</li>


        <li class="nav-item">
        <a class="nav-link " href="blog.php">Blog Upload</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="pending.php">Pending Account</a>
        </li>
    </ul>
  </div>
</nav>
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dip209";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if (isset($_POST['delete']) && isset($_POST['scoreRangeID'])) {
            $scoreRangeID = $_POST['scoreRangeID'];
        
            // Prepare DELETE statement
            $sql = "DELETE FROM score_ranges WHERE scoreRangeID = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $scoreRangeID);
                if ($stmt->execute()) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=delete'); // Redirect with success query param
                } else {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?error=delete'); // Redirect with error query param
                }
                $stmt->close();
            } else {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?error=prepare'); // Redirect with error query
            }
        }

        // Add score range
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['parentQuestionID'])) {
                $parentQuestionID = $_POST['parentQuestionID'];
                $minScore = $_POST['minScore'];
                $maxScore = $_POST['maxScore'];
                $typelevel = $_POST['typelevel'];
                $comment = $_POST['comment'];

                $sql = "INSERT INTO score_ranges (parentQuestionID, minScore, maxScore, typelevel, comment) 
                        VALUES (?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("iiiss", $parentQuestionID, $minScore, $maxScore, $typelevel, $comment);
                    if ($stmt->execute()) {
                         header('Location: ' . $_SERVER['PHP_SELF'] . '?success=add'); // Redirect with success query param
                    } else {
                        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=add'); // Redirect with error query
                    }
                    $stmt->close();
                } else {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?error=prepare'); // Redirect with error query param
                }
            }
        }

        // Fetch score ranges and parent questions
        $sql = "SELECT sr.scoreRangeID, sr.parentQuestionID, sr.minScore, sr.maxScore, sr.comment, sr.typelevel, pq.category, pq.parentQuestion 
                FROM score_ranges sr
                JOIN parent_questions pq ON sr.parentQuestionID = pq.parentQuestionID";
        $result = $conn->query($sql);

        // Fetch parent questions for dropdown
        $sqlParentQuestions = "SELECT parentQuestionID, parentQuestion FROM parent_questions";
        $parentQuestions = $conn->query($sqlParentQuestions)->fetch_all(MYSQLI_ASSOC);

        $conn->close();
        ?>

        <!-- Display Score Ranges -->
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Parent Category</th>
                        <th>Parent Question</th>
                        <th>Min Score</th>
                        <th>Max Score</th>
                        <th>Type Level</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['category'] ?></td>
                            <td><?= $row['parentQuestion'] ?></td>
                            <td><?= $row['minScore'] ?></td>
                            <td><?= $row['maxScore'] ?></td>
                            <td><?= $row['typelevel'] ?></td>
                            <td><?= $row['comment'] ?></td>
                            <td>
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal" onclick="loadQuestionData(<?= htmlspecialchars(json_encode($row)) ?>)">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </button>
                                <form action="" method="POST" style="display:inline;">
        <input type="hidden" name="scoreRangeID" value="<?= $row['scoreRangeID'] ?>">
        <button type="submit" name="delete" class="btn btn-danger">
            <i class="fas fa-trash-alt"></i> Delete
        </button>
    </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No questions found for this parent question.</p>
        <?php endif; ?>
        <script>
        // Check if success or error message is present in URL
        $(document).ready(function() {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                var message = '';
                switch(urlParams.get('success')) {
                    case 'add':
                        message = 'Score range added successfully!';
                        break;
                    case 'delete':
                        message = 'Score range deleted successfully!';
                        break;
                }
                alert(message);  // Display success message
            }
            if (urlParams.has('error')) {
                var message = '';
                switch(urlParams.get('error')) {
                    case 'add':
                        message = 'Error adding score range.';
                        break;
                    case 'delete':
                        message = 'Error deleting score range.';
                        break;
                    case 'prepare':
                        message = 'Error preparing the SQL statement.';
                        break;
                }
                alert(message);  // Display error message
            }
        });
    </script>
        <!-- Add Comment Button -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCommentModal">Add New Comment</button>

        <!-- Add Comment Modal -->
        <div class="modal" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCommentModalLabel">Add New Comment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="parentQuestionID">Select Parent Question: </label>
                                <select id="parentQuestionID" name="parentQuestionID" class="form-control" required>
                                    <option value="">Select a Parent Question</option>
                                    <?php foreach ($parentQuestions as $parent): ?>
                                        <option value="<?= $parent['parentQuestionID'] ?>"><?= $parent['parentQuestion'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="minScore">Min Score</label>
                                <input type="number" class="form-control" id="minScore" name="minScore" required>
                            </div>

                            <div class="form-group">
                                <label for="maxScore">Max Score</label>
                                <input type="number" class="form-control" id="maxScore" name="maxScore" required>
                            </div>

                            <div class="form-group">
                                <label for="typelevel">Type Level</label>
                                <input type="text" class="form-control" id="typelevel" name="typelevel" required>
                            </div>

                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>

                            <button type="submit" name="addComment" class="btn btn-primary">Add Comment</button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        

        <!-- Edit Score Range Modal -->
        <div class="modal" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Score Range</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="scoreRangeID" name="scoreRangeID">
                            <div class="form-group">
                                <label for="minScore">Min Score</label>
                                <input type="number" class="form-control" id="minScore" name="minScore" required>
                            </div>

                            <div class="form-group">
                                <label for="maxScore">Max Score</label>
                                <input type="number" class="form-control" id="maxScore" name="maxScore" required>
                            </div>

                            <div class="form-group">
                                <label for="typelevel">Type Level</label>
                                <input type="text" class="form-control" id="typelevel" name="typelevel">
                            </div>

                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadQuestionData(data) {
            document.getElementById('scoreRangeID').value = data.scoreRangeID;
            document.getElementById('minScore').value = data.minScore;
            document.getElementById('maxScore').value = data.maxScore;
            document.getElementById('typelevel').value = data.typelevel;
            document.getElementById('comment').value = data.comment;
        }
    </script>
</body>
</html>
