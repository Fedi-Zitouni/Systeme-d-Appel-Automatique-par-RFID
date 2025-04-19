<?php
require 'connectDB.php';

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $level_id = $_POST['level'];
    $specialty_id = !empty($_POST['specialty']) ? $_POST['specialty'] : NULL;
    $group_number = $_POST['group_number'];

    $stmt = $conn->prepare("INSERT INTO course_groups (level_id, specialty_id, group_number) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $level_id, $specialty_id, $group_number);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error adding class group: ' . $conn->error;
    }
} elseif ($action === 'get') {
    // Get class group data for editing
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM course_groups WHERE group_id = $id");
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo 'error';
    }
} elseif ($action === 'update') {
    // Update class group
    $id = $_POST['class_id'];
    $level_id = $_POST['level'];
    $specialty_id = !empty($_POST['specialty']) ? $_POST['specialty'] : NULL;
    $group_number = $_POST['group_number'];

    $stmt = $conn->prepare("UPDATE course_groups 
                           SET level_id = ?, specialty_id = ?, group_number = ?
                           WHERE group_id = ?");
    $stmt->bind_param("isii", $level_id, $specialty_id, $group_number, $id);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Error updating class group: ' . $conn->error;
    }
} elseif ($action === 'delete') {

    $id = $_GET['id'];
    
    $check = $conn->query("SELECT COUNT(*) as count FROM users WHERE class_group_id = $id");
    $result = $check->fetch_assoc();
    
    if ($result['count'] > 0) {
        echo 'Cannot delete - there are users assigned to this class group';
    } else {
        if ($conn->query("DELETE FROM course_groups WHERE group_id = $id")) {
            echo 'success';
        } else {
            echo 'Error deleting class group: ' . $conn->error;
        }
    }
}
?>