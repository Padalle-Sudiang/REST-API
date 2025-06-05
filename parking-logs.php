<?php 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 

// Konfigurasi koneksi ke database
$host     = "localhost";
$user     = "tkjh7215_opengate";
$password = "opengate123";
$database = "tkjh7215_opengate";

// Membuat koneksi
$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi gagal: " . $koneksi->connect_error
    ]);
    exit();
}

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
      SELECT v.plate_number, pl.entry_time, pl.exit_time, pl.parking_fee, pl.img_path
      FROM parking_logs pl JOIN vehicles v ON v.id = pl.vehicle_id
    ");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
  }
}

$koneksi->close();
?>