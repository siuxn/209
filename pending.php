<?php
include "generalConnection.php";

session_start();

// Fetch all pending users
$sql = "SELECT * FROM pendingUsers";
$result = mysqli_query($data, $sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve'], $_POST['pendingID'])) {
    $pendingID = $_POST['pendingID'];

    // Approve the user: Move from pendingUsers to userInfo
    $sql = "SELECT * FROM pendingUsers WHERE pendingID = $pendingID";
    $pendingResult = mysqli_query($data, $sql);
    if ($pendingResult && mysqli_num_rows($pendingResult) > 0) {
        $user = mysqli_fetch_assoc($pendingResult);
        $name = $user['name'];
        $email = $user['userEmail'];
        $password = $user['userPassword'];
        $userType = $user['userType'];

        // Insert into userInfo
        $sql2 = "INSERT INTO userInfo (name, userEmail, userPassword, userType) VALUES ('$name', '$email', '$password', '$userType')";
        if (mysqli_query($data, $sql2)) {
            // Remove from pendingUsers
            mysqli_query($data, "DELETE FROM pendingUsers WHERE pendingID = $pendingID");
            header("Location: pending.php?success=User approved successfully.");
        } else {
            echo "Error approving user: " . mysqli_error($data);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject'], $_POST['pendingID'])) {
    $pendingID = $_POST['pendingID'];

    // Reject the user: Remove from pendingUsers
    $sql = "DELETE FROM pendingUsers WHERE pendingID = $pendingID";
    if (mysqli_query($data, $sql)) {
        header("Location: pending.php?success=User rejected successfully.");
    } else {
        echo "Error rejecting user: " . mysqli_error($data);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pending Approvals</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class ="container">
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
    <a class="dropdown-item " href="displayt.php">View Score Range</a>

  </div>
</li>


        <li class="nav-item">
        <a class="nav-link " href="blog.php">Blog Upload</a>
        </li>
        <li class="nav-item">
        <a class="nav-link disabled" href="#">Pending Account</a>
        </li>
    </ul>
  </div>
</nav>

    <h1 class="text-center mb-4">Pending Doctor Registrations</h1>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['userEmail']); ?></td>
                        <td><?php echo htmlspecialchars($row['requestDate']); ?></td>
                        <td>
                            <!-- Approve Button -->
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="pendingID" value="<?php echo $row['pendingID']; ?>">
                                <button type="submit" name="approve" class="btn btn-success">Approve</button>
                            </form>
                            <!-- Reject Button -->
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="pendingID" value="<?php echo $row['pendingID']; ?>">
                                <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="alert alert-warning">No pending registrations at the moment.</p>
    <?php endif; ?>
</div>

    </div>
  
    

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-KyZXEJGo9v+8B+6aWTOc56f5p0pZ0hkkL2L9d9sE+KTZMEhBZP9vbdly1jxuHOwa" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-KyZXEJGo9v+8B+6aWTOc56f5p0pZ0hkkL2L9d9sE+KTZMEhBZP9vbdly1jxuHOwa" crossorigin="anonymous"></script>

</body>
</html>
