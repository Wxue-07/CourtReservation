<?php
$serverName = "LAPTOP-E3UBABNG\SQLWX"; 

$connectionOptions = array(
    "Database" => "CourtReservation", 
    "Uid" => "",
    "PWD" => "" 
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(json_encode(['status' => 'error', 'message' => 'Koneksi Database Gagal.']));
}
?>