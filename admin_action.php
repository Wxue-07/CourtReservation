<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Pastikan yang melakukan aksi benar-benar Admin
if (!isset($_SESSION['Role']) || trim($_SESSION['Role']) !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak! Anda bukan Admin.']);
    exit();
}

// Pastikan data yang dikirim dari JavaScript ada
if (!isset($_POST['reservation_id']) || !isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
    exit();
}

$reservationId = $_POST['reservation_id'];
$action = $_POST['action'];

// Tentukan status baru berdasarkan tombol yang diklik  
$newStatus = '';
if ($action === 'Approve') {
    $newStatus = 'Approved';
} else if ($action === 'Cancel') {
    $newStatus = 'Canceled';
} else {
    echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenal!']);
    exit();
}

// Update status di database
$sql = "UPDATE Reservation SET Status = ? WHERE ReservationID = ?";
$params = array($newStatus, $reservationId);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal merubah data di database.']);
    exit();
}

// Berikan jawaban sukses ke JavaScript
echo json_encode(['status' => 'success']);
?>