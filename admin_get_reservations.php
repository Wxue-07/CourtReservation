<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

// Menggunakan trim() untuk membuang spasi kosong (antisipasi tipe data CHAR di SSMS)
if(!isset($_SESSION['Role']) || trim($_SESSION['Role']) !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak!']);
    exit();
}

$sql = "SELECT r.ReservationID, u.Username, r.BookingDate, c.CourtNumber, r.SessionTime, r.Status 
        FROM Reservation r
        JOIN Users u ON r.UserID = u.UserID
        JOIN Court c ON r.CourtID = c.CourtID
        ORDER BY r.BookingDate ASC";

$stmt = sqlsrv_query($conn, $sql);

if($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengambil data.']);
    exit();
}

$ongoing = [];
$upcoming = [];
$history = [];

date_default_timezone_set('Asia/Jakarta');
$todayStr = date('Y-m-d'); 

while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $dateObj = $row['BookingDate']; 
    $status = trim($row['Status']); 

    $bookDateStr = is_object($dateObj) ? $dateObj->format('Y-m-d') : date('Y-m-d', strtotime($dateObj));
    $displayDate = is_object($dateObj) ? $dateObj->format('d M Y') : date('d M Y', strtotime($dateObj));

    if ($bookDateStr < $todayStr && $status !== 'Canceled') {
        $status = 'Finished';
        $updateSql = "UPDATE Reservation SET Status = 'Finished' WHERE ReservationID = ?";
        sqlsrv_query($conn, $updateSql, array($row['ReservationID']));
    }

    $item = [
        'id' => $row['ReservationID'],
        'customer' => $row['Username'],
        'date' => $displayDate,
        'court' => $row['CourtNumber'],
        'session' => $row['SessionTime'],
        'status' => $status
    ];

    if ($status === 'Canceled' || $status === 'Finished') {
        $history[] = $item; 
    } else if ($bookDateStr === $todayStr) {
        $ongoing[] = $item; 
    } else if ($bookDateStr > $todayStr) {
        $upcoming[] = $item; 
    }
}

echo json_encode([
    'status' => 'success',
    'ongoing' => $ongoing,
    'upcoming' => $upcoming,
    'history' => $history
]);
?>