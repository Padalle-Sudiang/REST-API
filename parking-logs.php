<?php 
date_default_timezone_set('Asia/Makassar');
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Include file koneksi
require_once "db.php";

// Cek parking log
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['plate_number'])) {
    $plate = $_GET['plate_number'];

    // Ambil log terakhir yang BELUM keluar
    $stmt = $koneksi->prepare("
      SELECT 
        v.plate_number,
        v.is_member,
        pl.entry_time,
        pl.exit_time,
        pl.parking_fee,
        pl.img_path,
        pl.img_path_exit
      FROM 
        vehicles v
      JOIN 
        parking_logs pl ON pl.vehicle_id = v.id
      WHERE 
        v.plate_number = ? 
        AND pl.exit_time IS NULL
      ORDER BY 
        pl.entry_time DESC 
      LIMIT 1
    ");
    $stmt->bind_param("s", $plate);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Fallback: Jika tidak ada log aktif (semua sudah keluar), ambil log terakhir apapun
    if (!$data) {
      $stmt = $koneksi->prepare("
        SELECT 
          v.plate_number,
          v.is_member,
          pl.entry_time,
          pl.exit_time,
          pl.parking_fee,
          pl.img_path,
          pl.img_path_exit
        FROM 
          vehicles v
        JOIN 
          parking_logs pl ON pl.vehicle_id = v.id
        WHERE 
          v.plate_number = ?
        ORDER BY 
          pl.entry_time DESC 
        LIMIT 1
      ");
      $stmt->bind_param("s", $plate);
      $stmt->execute();
      $data = $stmt->get_result()->fetch_assoc();
    }

    echo json_encode($data);
  } else {
    // Jika tidak ada parameter plate_number, tampilkan semua log
    $result = $koneksi->query("
      SELECT 
        v.plate_number,
        v.is_member,
        pl.entry_time,
        pl.exit_time,
        pl.parking_fee,
        pl.img_path,
        pl.img_path_exit
      FROM 
        parking_logs pl 
      JOIN 
        vehicles v ON v.id = pl.vehicle_id
      ORDER BY 
        pl.entry_time DESC
    ");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
  }
}

$koneksi->close();
?>
