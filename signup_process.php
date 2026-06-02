<?php
include 'db.php';

// Wajib memberitahu browser bahwa jawaban berupa JSON
header('Content-Type: application/json');

// Mengambil data dari form HTML
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone = $_POST['phonenumber'];
$role = 'Customer';

$checkSql = "SELECT * FROM Users WHERE Email = ?";
$checkStmt = sqlsrv_query($conn, $checkSql, array($email));

if ($checkStmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem database.']);
    exit();
}

if (sqlsrv_has_rows($checkStmt)) {
    echo json_encode(['status' => 'error', 'message' => 'Email ini sudah terdaftar. Silakan gunakan email lain!']);
    exit();
}

$sql = "INSERT INTO Users (Username, Email, Password, PhoneNumber, Role) VALUES (?, ?, ?, ?, ?)";
$params = array($name, $email, $password, $phone, $role);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt) {
    echo json_encode(['status' => 'success']);
} else {
    $errors = sqlsrv_errors();
    $errorMsg = $errors[0]['message'] ?? 'Gagal menyimpan data ke database.';
    echo json_encode(['status' => 'error', 'message' => $errorMsg]);
}
?>