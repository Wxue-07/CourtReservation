<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

$email = $_POST['email']; 
$password = $_POST['password'];

$sql = "SELECT * FROM Users WHERE Email = ?";
$stmt = sqlsrv_query($conn, $sql, array($email));

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem database.']);
    exit();
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($user && $password == $user['Password']) {
    // Simpan data penting ke Session
    $_SESSION['UserID'] = $user['UserID'];
    $_SESSION['Username'] = $user['Username'];
    $_SESSION['Role'] = $user['Role'];
    
    // Cek Role untuk menentukan halaman tujuan
    $redirect = ($user['Role'] === 'Admin') ? 'admin_dashboard.html' : 'dashboard.html';
    
    echo json_encode(['status' => 'success', 'redirect' => $redirect]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email atau Password salah!']);
}
?>