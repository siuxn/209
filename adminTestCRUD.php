<?php
// Include database connection
include "generalConnection.php";

// Fetch all questions from the database
$query = "SELECT * FROM questions";
$questions = $conn->query($query);

// Check if there are any questions to display
if ($questions && $questions->num_rows > 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Questions Management</title>
</head>
<body>

<table border="1">
    <tr>
        <th>Question</th>
        <th>Option 1</th>
        <th>Option 2</th>
        <th>Option 3</th>
        <th>Option 4</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $questions->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['question_text']); ?></td>
            <td><?php echo htmlspecialchars($row['option1']) . " (" . $row['option1_marks'] . " marks)"; ?></td>
            <td><?php echo htmlspecialchars($row['option2']) . " (" . $row['option2_marks'] . " marks)"; ?></td>
            <td><?php echo htmlspecialchars($row['option3']) . " (" . $row['option3_marks'] . " marks)"; ?></td>
            <td><?php echo htmlspecialchars($row['option4']) . " (" . $row['option4_marks'] . " marks)"; ?></td>
            <td>
                <a href="edit_question.php?id=<?php echo $row['question_id']; ?>">Edit</a> |
                <a href="delete_question.php?id=<?php echo $row['question_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<a href="add_question.php">Add New Question</a>

<?php
} else {
    echo "No questions found in the database.";
}

// Close the database connection
$conn->close();
?>

</body>
</html>
