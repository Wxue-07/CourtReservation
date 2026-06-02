<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

if(!isset($_SESSION['UserID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesi habis, silakan login ulang.']);
    exit();
}

$userID = $_SESSION['UserID'];
$courtID = $_POST['courtSelect']; 
$bookingDate = $_POST['bookingDate'];
$sesiArray = $_POST['sesi']; 
$status = 'Pending';
$pricePerSession = 150000; 

// Loop sebanyak sesi yang dicentang
foreach($sesiArray as $sesi) {
    // Perhatikan: SessionTime ditulis tanpa spasi dan tanpa kurung siku
    $sql = "INSERT INTO Reservation (UserID, CourtID, BookingDate, SessionTime, TotalToPaid, Status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $params = array($userID, $courtID, $bookingDate, $sesi, $pricePerSession, $status);
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if($stmt === false) {
        // Jika gagal, ambil pesan error asli dari SQL Server untuk mempermudah perbaikan
        $errors = sqlsrv_errors();
        echo json_encode(['status' => 'error', 'message' => $errors[0]['message']]);
        exit();
    }
}

echo json_encode(['status' => 'success']);
?>