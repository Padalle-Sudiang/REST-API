<?php 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");

// Include file koneksi
require_once "db.php";

// Cek parking log
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['plate_number'])) {
    $plate = $_GET['plate_number'];
    $stmt = $koneksi->prepare("
      SELECT v.plate_number, pl.entry_time, pl.exit_time, pl.parking_fee
      FROM vehicles v
      JOIN parking_logs pl ON pl.vehicle_id = v.id
      WHERE v.plate_number = ? ORDER BY pl.entry_time DESC LIMIT 1
    ");
    $stmt->bind_param("s", $plate);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_assoc());
  } else {
    $result = $koneksi->query("
      SELECT v.plate_number, pl.entry_time, pl.exit_time, pl.parking_fee, pl.img_path, pl.img_path_exit
      FROM parking_logs pl JOIN vehicles v ON v.id = pl.vehicle_id
    ");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
  }
}

$koneksi->close();
?>