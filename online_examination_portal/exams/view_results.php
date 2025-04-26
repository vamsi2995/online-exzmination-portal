<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$exam_id = $_GET['exam_id'];
$user_id = $_SESSION['user_id'];

// Fetch student's answers and correct answers
$query = "
    SELECT q.question_text, q.correct_option, sa.selected_option 
    FROM student_answers sa
    JOIN questions q ON sa.question_id = q.id
    WHERE sa.exam_id = ? AND sa.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $exam_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate Score
$total_questions = $result->num_rows;
$correct_answers = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Exam Results</h2>

        <table>
            <tr>
                <th>Question</th>
                <th>Your Answer</th>
                <th>Correct Answer</th>
                <th>Result</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                    <td><?php echo htmlspecialchars($row['selected_option']); ?></td>
                    <td><?php echo htmlspecialchars($row['correct_option']); ?></td>
                    <td>
                        <?php
                        if ($row['selected_option'] == $row['correct_option']) {
                            echo "<span class='success'>✔ Correct</span>";
                            $correct_answers++;
                        } else {
                            echo "<span class='error'>✘ Incorrect</span>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3 class="success">Your Score: <?php echo $correct_answers; ?> / <?php echo $total_questions; ?></h3>

        <a href="take_exam.php" class="back-btn">Take Another Exam</a>
        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
