<form method="POST" action="submit_quiz.php">
    <?php if ($questions): ?>
        <?php foreach ($questions as $index => $question): ?>
            <div class="question-item">
                <h3><?php echo ($index + 1) . ". " . htmlspecialchars($question['question']); ?></h3>

                <label>
                    <input type="radio" name="question_<?php echo $index; ?>" value="1" required>
                    1. <?php echo htmlspecialchars($question['answerA']); ?> 
                </label><br>

                <label>
                    <input type="radio" name="question_<?php echo $index; ?>" value="2">
                    2. <?php echo htmlspecialchars($question['answerB']); ?> 
                </label><br>

                <label>
                    <input type="radio" name="question_<?php echo $index; ?>" value="3">
                    3. <?php echo htmlspecialchars($question['answerC']); ?> 
                </label><br>

                <label>
                    <input type="radio" name="question_<?php echo $index; ?>" value="4">
                    4. <?php echo htmlspecialchars($question['answerD']); ?> 
                </label><br><br>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No questions available.</p>
    <?php endif; ?>

    <input type="submit" value="Submit Answers">
</form>
