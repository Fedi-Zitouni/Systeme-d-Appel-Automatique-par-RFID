<?php
require 'connectDB.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No user ID provided']);
    exit();
}

$user_id = intval($_GET['id']);

$sql = "SELECT u.*, 
        cg.group_id AS class_group_id,
        CONCAT(al.abbreviation, 
              IF(s.name IS NULL, '', CONCAT('-', s.name)), 
              '-TD', cg.group_number, '-TP', cg.group_number) AS class_name
        FROM users u
        LEFT JOIN course_groups cg ON u.class_group_id = cg.group_id
        LEFT JOIN academic_levels al ON cg.level_id = al.level_id
        LEFT JOIN specialties s ON cg.specialty_id = s.specialty_id
        WHERE u.id = ?";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo json_encode(['error' => 'SQL prepare error: ' . mysqli_error($conn)]);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
    // Format the response data
    $response = [
        'id' => $user['id'],
        'card_uid' => $user['card_uid'],
        'username' => $user['username'],
        'serialnumber' => $user['serialnumber'],
        'gender' => $user['gender'],
        'email' => $user['email'],
        'device_dep' => $user['device_dep'],
        'class_group_id' => $user['class_group_id'],
        'class_name' => $user['class_name'] ?? 'Not assigned'
    ];
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'User not found']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>