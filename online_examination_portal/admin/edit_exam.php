<?php
session_start();
require '../includes/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get exam ID from URL
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$exam_id = $_GET['id'];

// Fetch existing exam data
$sql = "SELECT * FROM exams WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();
$exam = $result->fetch_assoc();

if (!$exam) {
    die("Exam not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $total_marks = $_POST['total_marks'];

    $update_sql = "UPDATE exams SET title = ?, total_marks = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sii", $title, $total_marks, $exam_id);

    if ($update_stmt->execute()) {
        $success = "Exam updated successfully!";
    } else {
        $error = "Error updating exam.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exam</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Exam</h2>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label for="title">Exam Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($exam['title']); ?>" required>

            <label for="total_marks">Total Marks:</label>
            <input type="number" id="total_marks" name="total_marks" value="<?php echo htmlspecialchars($exam['total_marks']); ?>" required>

            <button type="submit">Update Exam</button>
        </form>

        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
