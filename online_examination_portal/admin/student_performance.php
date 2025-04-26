<?php
session_start();
require '../includes/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch student scores
$query = "
    SELECT u.name, sa.student_id, sa.exam_id, e.title AS exam_title, 
           SUM(CASE WHEN sa.selected_option = q.correct_option THEN 1 ELSE 0 END) AS score,
           COUNT(q.id) AS total_questions,
           (SUM(CASE WHEN sa.selected_option = q.correct_option THEN 1 ELSE 0 END) * 100 / COUNT(q.id)) AS percentage
    FROM student_answers sa
    JOIN questions q ON sa.question_id = q.id
    JOIN exams e ON sa.exam_id = e.id
    JOIN users u ON sa.student_id = u.id
    GROUP BY sa.student_id, sa.exam_id
    ORDER BY percentage DESC"; // Sort by highest percentage

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Student Performance and Rankings</h2>

        <table>
            <tr>
                <th>Rank</th>
                <th>Student Name</th>
                <th>Exam Title</th>
                <th>Score</th>
                <th>Total Questions</th>
                <th>Percentage</th>
            </tr>

            <?php 
            $rank = 1;
            while ($row = $result->fetch_assoc()): 
            ?>
                <tr>
                    <td><?php echo $rank++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['exam_title']); ?></td>
                    <td><?php echo $row['score']; ?></td>
                    <td><?php echo $row['total_questions']; ?></td>
                    <td><?php echo round($row['percentage'], 2) . '%'; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
