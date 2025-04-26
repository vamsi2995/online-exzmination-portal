<?php
session_start();
require '../includes/db.php';

// Ensure user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get exam ID from URL
if (!isset($_GET['exam_id']) || empty($_GET['exam_id'])) {
    die("Invalid exam ID.");
}
$exam_id = intval($_GET['exam_id']);

// Handle deleting a question
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM questions WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Question deleted!'); window.location.href='manage_questions.php?exam_id=$exam_id';</script>";
    } else {
        echo "<script>alert('Error deleting question.');</script>";
    }
}

// Handle adding a question
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_option = $_POST['correct_option'];

    $sql = "INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $exam_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_option);

    if ($stmt->execute()) {
        echo "<script>alert('Question added!'); window.location.href='manage_questions.php?exam_id=$exam_id';</script>";
    } else {
        echo "<script>alert('Error adding question.');</script>";
    }
}

// Fetch existing questions
$sql = "SELECT * FROM questions WHERE exam_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Manage Questions for Exam ID: <?php echo $exam_id; ?></h2>

        <h3>Add a New Question</h3>
        <form method="POST">
            <label>Question:</label>
            <textarea name="question_text" required></textarea>

            <label>Option A:</label>
            <input type="text" name="option_a" required>
            <label>Option B:</label>
            <input type="text" name="option_b" required>
            <label>Option C:</label>
            <input type="text" name="option_c" required>
            <label>Option D:</label>
            <input type="text" name="option_d" required>

            <label>Correct Answer:</label>
            <select name="correct_option">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>

            <button type="submit">Add Question</button>
        </form>

        <h3>Existing Questions</h3>
        <table class="exam-list">
            <tr>
                <th>Question</th>
                <th>Options</th>
                <th>Correct Answer</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['question_text']; ?></td>
                    <td>
                        A) <?php echo $row['option_a']; ?><br>
                        B) <?php echo $row['option_b']; ?><br>
                        C) <?php echo $row['option_c']; ?><br>
                        D) <?php echo $row['option_d']; ?>
                    </td>
                    <td><?php echo $row['correct_option']; ?></td>
                    <td>
                    <a href="manage_questions.php?exam_id=<?php echo $exam_id; ?>&delete_id=<?php echo $row['id']; ?>" 
                    class="delete-btn" 
                    onclick="return confirm('Delete this question?');">
                    Delete
                    </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <br>
        <a href="../admin/dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
