<?php
include 'db.php';
header('Content-Type: application/json');

$date = $_GET['date'] ?? '';
$courtID = $_GET['courtID'] ?? '';

if(empty($date) || empty($courtID)) {
    echo json_encode([]);
    exit();
}

// Cari sesi yang sudah dipesan (Status selain 'Canceled' berarti jadwal terisi)
$sql = "SELECT SessionTime FROM Reservation WHERE BookingDate = ? AND CourtID = ? AND Status != 'Canceled'";
$params = array($date, $courtID);
$stmt = sqlsrv_query($conn, $sql, $params);

$bookedSessions = [];
if ($stmt !== false) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $bookedSessions[] = $row['SessionTime'];
    }
}

// Kembalikan daftar sesi yang sudah terisi (misal: ["10:00 - 12:00", "14:00 - 16:00"])
echo json_encode($bookedSessions);
?>