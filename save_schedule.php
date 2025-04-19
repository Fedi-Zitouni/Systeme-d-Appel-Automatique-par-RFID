<?php
include 'connectDB.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_id = $conn->real_escape_string($_POST['class_group']);
    $course_id = $conn->real_escape_string($_POST['course']);
    $class_type_id = $conn->real_escape_string($_POST['class_type']);
    $professor_id = $conn->real_escape_string($_POST['professor']);
    $classroom_id = $conn->real_escape_string($_POST['classroom']);
    $day = $conn->real_escape_string($_POST['day']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $frequency = $conn->real_escape_string($_POST['frequency']);
    $academic_year = $conn->real_escape_string($_POST['academic_year']);

    $sql = "INSERT INTO class_sessions (
        course_id, class_type_id, group_id, professor_id, classroom_id,
        start_time, end_time, day_of_week, frequency, academic_year
    ) VALUES (
        '$course_id', '$class_type_id', '$group_id', '$professor_id', '$classroom_id',
        '$start_time', '$end_time', '$day', '$frequency', '$academic_year'
    )";

    if ($conn->query($sql)) {
        $_SESSION['message'] = "Schedule added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }

    header("Location: admin_dashboard.php");
    exit();
}
?>