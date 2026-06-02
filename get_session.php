<?php
session_start();
header('Content-Type: application/json');

if(isset($_SESSION['UserID'])) {
    echo json_encode([
        'logged_in' => true, 
        'username' => $_SESSION['Username'], 
        'role' => $_SESSION['Role']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>