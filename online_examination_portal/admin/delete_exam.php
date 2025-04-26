<?php
session_start();
require '../includes/db.php';
// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}
// Check if exam ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}
$exam_id = intval($_GET['id']);
// Delete exam query
$sql = "DELETE FROM exams WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $exam_id);
if ($stmt->execute()) {
    echo "<script>
        alert('Exam deleted successfully!');
        window.location.href = '../exams/list.php'; 
    </script>";
} else {
    echo "<script>
        alert('Error deleting exam.');
        window.location.href = '../exams/list.php';
    </script>";
}
?>
