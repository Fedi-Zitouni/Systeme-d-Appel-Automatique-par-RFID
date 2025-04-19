<?php
require 'connectDB.php';

if (isset($_POST['Add'])) {
    $user_id = $_POST['user_id'];
    $Uname = $_POST['name'];
    $Number = $_POST['number'];
    $Email = $_POST['email'];
    $dev_uid = $_POST['dev_uid'];
    $Gender = $_POST['gender'];
    $class_group = $_POST['class_group'] ?? null;

    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error";
        exit();
    }
    
    mysqli_stmt_bind_param($result, "i", $user_id);
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);
    
    if ($row = mysqli_fetch_assoc($resultl)) {
        if ($row['add_card'] == 0) {
            if (!empty($Uname) && !empty($Number) && !empty($Email)) {
                $sql = "SELECT serialnumber FROM users WHERE serialnumber=? AND id NOT like ?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error";
                    exit();
                }
                
                mysqli_stmt_bind_param($result, "di", $Number, $user_id);
                mysqli_stmt_execute($result);
                $resultl = mysqli_stmt_get_result($result);
                
                if (!$row = mysqli_fetch_assoc($resultl)) {
                    $dev_name = "All";
                    if (!empty($dev_uid)) {
                        $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                        $result = mysqli_stmt_init($conn);
                        if (mysqli_stmt_prepare($result, $sql)) {
                            mysqli_stmt_bind_param($result, "s", $dev_uid);
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            if ($row = mysqli_fetch_assoc($resultl)) {
                                $dev_name = $row['device_dep'];
                            }
                        }
                    }

                    // Update user with class group
                    $sql = "UPDATE users SET username=?, serialnumber=?, gender=?, email=?, 
                            user_date=CURDATE(), device_uid=?, device_dep=?, class_group_id=?, add_card=1 
                            WHERE id=?";
                    $result = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($result, $sql)) {
                        echo "SQL_Error_select_Fingerprint";
                        exit();
                    }
                    
                    mysqli_stmt_bind_param($result, "ssssssii", $Uname, $Number, $Gender, $Email, 
                                          $dev_uid, $dev_name, $class_group, $user_id);
                    mysqli_stmt_execute($result);
                    echo 1;
                } else {
                    echo "The serial number is already taken!";
                }
            } else {
                echo "Empty Fields";
            }
        } else {
            echo "This User already exists";
        }
    } else {
        echo "No selected Card!";
    }
    exit();
}


if (isset($_POST['Update'])) {
    $user_id = $_POST['user_id'];
    $Uname = $_POST['name'];
    $Number = $_POST['number'];
    $Email = $_POST['email'];
    $dev_uid = $_POST['dev_uid'];
    $Gender = $_POST['gender'];
    $class_group = $_POST['class_group'] ?? null;


    $sql = "SELECT add_card FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error";
        exit();
    }
    
    mysqli_stmt_bind_param($result, "i", $user_id);
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);
    
    if ($row = mysqli_fetch_assoc($resultl)) {
        if ($row['add_card'] == 0) {
            echo "First, You need to add the User!";
            exit();
        }

        if (empty($Uname) || empty($Number) || empty($Email)) {
            echo "Empty Fields";
            exit();
        }

        $sql = "SELECT serialnumber FROM users WHERE serialnumber=? AND id NOT like ?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error";
            exit();
        }
        
        mysqli_stmt_bind_param($result, "di", $Number, $user_id);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        
        if (!$row = mysqli_fetch_assoc($resultl)) {
            $dev_name = "All";
            if (!empty($dev_uid)) {
                $sql = "SELECT device_dep FROM devices WHERE device_uid=?";
                $result = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($result, $sql)) {
                    mysqli_stmt_bind_param($result, "s", $dev_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    if ($row = mysqli_fetch_assoc($resultl)) {
                        $dev_name = $row['device_dep'];
                    }
                }
            }

            $sql = "UPDATE users SET username=?, serialnumber=?, gender=?, email=?, 
                    device_uid=?, device_dep=?, class_group_id=? WHERE id=?";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo "SQL_Error_select_Card";
                exit();
            }
            
            mysqli_stmt_bind_param($result, "ssssssi", $Uname, $Number, $Gender, $Email, 
                                  $dev_uid, $dev_name, $class_group, $user_id);
            mysqli_stmt_execute($result);
            echo 1;
        } else {
            echo "The serial number is already taken!";
        }
    } else {
        echo "No selected User to update!";
    }
    exit();
}

if (isset($_GET['select'])) {
    $card_uid = $_GET['card_uid'];
    
    $sql = "SELECT * FROM users WHERE card_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select";
        exit();
    }
    
    mysqli_stmt_bind_param($result, "s", $card_uid);
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);
    
    header('Content-Type: application/json');
    $data = array();
    while ($row = mysqli_fetch_assoc($resultl)) {
        $data[] = $row;
    }
    
    echo json_encode($data);
    exit();
}

if (isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    
    if (empty($user_id)) {
        echo "No selected user to remove";
        exit();
    }
    
    $sql = "DELETE FROM users WHERE id=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_delete";
        exit();
    }
    
    mysqli_stmt_bind_param($result, "i", $user_id);
    mysqli_stmt_execute($result);
    echo 1;
    exit();
}