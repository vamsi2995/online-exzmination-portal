<?php
session_start();
require '../includes/db.php';

// Check if the user is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

// Fetch available exams
$exam_query = "SELECT id, title FROM exams";
$exam_result = $conn->query($exam_query);

// If an exam is selected, fetch its questions
$selected_exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : null;
$questions = null;

if ($selected_exam_id) {
    $question_query = "SELECT id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE exam_id = ?";
    $stmt = $conn->prepare($question_query);
    $stmt->bind_param("i", $selected_exam_id);
    $stmt->execute();
    $questions = $stmt->get_result();
}

// Fetch the selected exam title separately
$exam_title = "";
if ($selected_exam_id) {
    $exam_title_query = "SELECT title FROM exams WHERE id = ?";
    $stmt_title = $conn->prepare($exam_title_query);
    $stmt_title->bind_param("i", $selected_exam_id);
    $stmt_title->execute();
    $title_result = $stmt_title->get_result();
    $exam_title_row = $title_result->fetch_assoc();
    $exam_title = $exam_title_row['title'] ?? 'Unknown Exam';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Select an Exam</h2>

        <form method="GET">
            <label for="exam_id">Choose an exam:</label>
            <select name="exam_id" id="exam_id" onchange="this.form.submit()">
                <option value="">-- Select Exam --</option>
                <?php while ($row = $exam_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo ($selected_exam_id == $row['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['title']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($selected_exam_id && $questions && $questions->num_rows > 0): ?>
            <h2>Exam: <?php echo htmlspecialchars($exam_title); ?></h2>

            <form action="submit_exam.php" method="POST">
                <input type="hidden" name="exam_id" value="<?php echo $selected_exam_id; ?>">

                <?php while ($row = $questions->fetch_assoc()): ?>
                    <div class="question-box">
                        <p><strong><?php echo htmlspecialchars($row['question_text']); ?></strong></p>
                        <label><input type="radio" name="answer[<?php echo $row['id']; ?>]" value="A"> <?php echo htmlspecialchars($row['option_a']); ?></label><br>
                        <label><input type="radio" name="answer[<?php echo $row['id']; ?>]" value="B"> <?php echo htmlspecialchars($row['option_b']); ?></label><br>
                        <label><input type="radio" name="answer[<?php echo $row['id']; ?>]" value="C"> <?php echo htmlspecialchars($row['option_c']); ?></label><br>
                        <label><input type="radio" name="answer[<?php echo $row['id']; ?>]" value="D"> <?php echo htmlspecialchars($row['option_d']); ?></label><br>
                    </div>
                <?php endwhile; ?>

                <button type="submit">Submit Exam</button>
            </form>
            <br>
            <a href="view_results.php?exam_id=<?php echo $selected_exam_id; ?>">Check Results</a>
        <?php elseif ($selected_exam_id): ?>
            <p>No questions found for this exam.</p>
        <?php endif; ?>

        <br>
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
