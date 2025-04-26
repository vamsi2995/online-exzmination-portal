<?php
session_start();
require '../includes/db.php';

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $total_marks = intval($_POST['total_marks']);

    if (!empty($title) && $total_marks > 0) {
        $stmt = $conn->prepare("INSERT INTO exams (title, total_marks) VALUES (?, ?)");
        $stmt->bind_param("si", $title, $total_marks);
        
        if ($stmt->execute()) {
            $success = "Exam added successfully!";
        } else {
            $error = "Error adding exam.";
        }

        $stmt->close();
    } else {
        $error = "Please enter valid details!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Exam</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Add New Exam</h2>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form action="add_exam.php" method="POST">
            <label>Exam Title:</label>
            <input type="text" name="title" required>

            <label>Total Marks:</label>
            <input type="number" name="total_marks" required>

            <button type="submit">Add Exam</button>
        </form>

        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
