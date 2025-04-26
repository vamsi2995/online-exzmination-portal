<?php
session_start();
require '../includes/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch available exams
$sql = "SELECT id, title, total_marks FROM exams";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin'; ?></h2>

        <div class="dashboard-links">
            <a href="add_exam.php">Add New Exam</a>
            <a href="student_performance.php">View Student Performance</a>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>

        <h3>Available Exams</h3>
        <ul class="exam-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <?php echo $row['title']; ?> - Total Marks: <?php echo $row['total_marks']; ?>
                    <div class="exam-actions">
                        <a href="edit_exam.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="delete_exam.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        <a href="manage_questions.php?exam_id=<?php echo $row['id']; ?>">Manage Questions</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
