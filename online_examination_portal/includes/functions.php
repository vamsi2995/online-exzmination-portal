<?php
require 'includes/db.php';

/**
 * Securely sanitize input data
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

/**
 * Check if a user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect users if they are not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Fetch a list of exams
 */
function get_exams() {
    global $conn;
    $sql = "SELECT * FROM exams ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Fetch exam details by ID
 */
function get_exam_by_id($exam_id) {
    global $conn;
    $sql = "SELECT * FROM exams WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Fetch exam questions
 */
function get_questions_by_exam($exam_id) {
    global $conn;
    $sql = "SELECT * FROM questions WHERE exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
