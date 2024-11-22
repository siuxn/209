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

if (isset($_GET['parentQuestionID'])) {
    $parentQuestionID = $_GET['parentQuestionID'];

    // First, delete all associated questions for the parent question
    $sqlDeleteQuestions = "DELETE FROM questions WHERE parent_question_id = ?";
    $stmt = $conn->prepare($sqlDeleteQuestions);
    $stmt->bind_param('i', $parentQuestionID);

    if ($stmt->execute()) {
        echo "Associated questions deleted successfully.<br>";
    } else {
        echo "Error deleting associated questions: " . $stmt->error . "<br>";
    }

    // Then, delete the parent question
    $sqlDeleteParent = "DELETE FROM parent_questions WHERE parentQuestionID = ?";
    $stmtParent = $conn->prepare($sqlDeleteParent);
    $stmtParent->bind_param('i', $parentQuestionID);

    if ($stmtParent->execute()) {
        echo "Parent question deleted successfully!";
    } else {
        echo "Error deleting parent question: " . $stmtParent->error . "<br>";
    }
} else {
    echo "Parent Question ID not specified.";
}

$conn->close();
?>
