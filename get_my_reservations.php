<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['UserID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi habis, silakan login ulang.']);
    exit();
}

$userID = $_SESSION['UserID'];

$sql = "SELECT r.ReservationID, r.BookingDate, c.CourtNumber, r.SessionTime, r.Status 
        FROM Reservation r
        JOIN Court c ON r.CourtID = c.CourtID
        WHERE r.UserID = ?
        ORDER BY r.BookingDate ASC";

$params = array($userID);
$stmt = sqlsrv_query($conn, $sql, $params);

if($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data dari database.']);
    exit();
}

$ongoing = [];
$upcoming = [];
$history = [];

// Set zona waktu ke Jakarta dan buat format string (Misal: "2026-06-02")
date_default_timezone_set('Asia/Jakarta');
$todayStr = date('Y-m-d'); 

while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $dateObj = $row['BookingDate'];
    $status = $row['Status'];
    
    // Ubah tanggal dari database ke format string yang sama
    $bookDateStr = is_object($dateObj) ? $dateObj->format('Y-m-d') : date('Y-m-d', strtotime($dateObj));
    $displayDate = is_object($dateObj) ? $dateObj->format('d M Y') : date('d M Y', strtotime($dateObj));

    // FILTER KADALUARSA dengan String
    if ($bookDateStr < $todayStr && $status !== 'Canceled') {
        $status = 'Finished';
        $updateSql = "UPDATE Reservation SET Status = 'Finished' WHERE ReservationID = ?";
        sqlsrv_query($conn, $updateSql, array($row['ReservationID']));
    }

    $item = [
        'date' => $displayDate,
        'court' => $row['CourtNumber'],
        'session' => $row['SessionTime'],
        'status' => $status
    ];

    // PENGELOMPOKAN TAB OTOMATIS BERDASARKAN TEKS TANGGAL
    if ($status === 'Canceled' || $status === 'Finished') {
        $history[] = $item; 
    } else if ($bookDateStr === $todayStr) {
        $ongoing[] = $item; // Hari ini = Ongoing
    } else if ($bookDateStr > $todayStr) {
        $upcoming[] = $item; // Besok dan seterusnya = Upcoming
    }
}

echo json_encode([
    'status' => 'success',
    'ongoing' => $ongoing,
    'upcoming' => $upcoming,
    'history' => $history
]);
?>