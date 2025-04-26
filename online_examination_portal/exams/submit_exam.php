<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $exam_id = $_POST['exam_id'];
    $user_id = $_SESSION['user_id'];
    $answers = $_POST['answer'];

    // Store answers in the database
    foreach ($answers as $question_id => $selected_option) {
        $stmt = $conn->prepare("INSERT INTO student_answers (student_id, exam_id, question_id, selected_option) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $exam_id, $question_id, $selected_option);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Submission</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <p class="success">Exam submitted successfully!</p>
        <a href="take_exam.php" class="back-btn">Take Another Exam</a>
        <a href="view_results.php?exam_id=<?php echo $exam_id; ?>" class="delete-btn">Check Results</a>
    </div>
</body>
</html>
