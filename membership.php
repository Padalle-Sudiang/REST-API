<?php 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type"); 

// Include file koneksi
require_once "db.php";

// Tambah membership kendaraan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);
  $plate = $input['plate_number'];
  $owner = $input['owner_name'];
  $expiry = $input['membership_expiry'];

  // Pastikan kendaraan ada
  $stmt = $koneksi->prepare("SELECT id FROM vehicles WHERE plate_number = ?");
  $stmt->bind_param("s", $plate);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($v = $res->fetch_assoc()) {
    $vehicle_id = $v['id'];
  } else {
    $stmt = $koneksi->prepare("INSERT INTO vehicles (plate_number, plate_type, is_member, created_at) VALUES (?, 'plat_biasa', 1, NOW())");
    $stmt->bind_param("s", $plate);
    $stmt->execute();
    $vehicle_id = $koneksi->insert_id;
  }

  $stmt = $koneksi->prepare("INSERT INTO memberships (vehicle_id, owner_name, membership_expiry, created_at) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("iss", $vehicle_id, $owner, $expiry);
  $stmt->execute();

  $koneksi->query("UPDATE vehicles SET is_member = 1 WHERE id = $vehicle_id");

  echo json_encode(["message" => "Membership berhasil ditambahkan.", "plate_number" => $plate]);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $result = $koneksi->query("
    SELECT v.plate_number, m.owner_name, m.membership_expiry 
    FROM memberships m JOIN vehicles v ON v.id = m.vehicle_id
  ");
  echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}  else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $plate = $input['plate_number'] ?? '';
    $owner = $input['owner_name'] ?? '';
    $expiry = $input['membership_expiry'] ?? '';

    if (!$plate || !$owner || !$expiry) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap."]);
        exit();
    }

    // Ambil ID kendaraan
    $stmt = $koneksi->prepare("SELECT id FROM vehicles WHERE plate_number = ?");
    $stmt->bind_param("s", $plate);
    $stmt->execute();
    $res = $stmt->get_result();

    if (!$v = $res->fetch_assoc()) {
        echo json_encode(["status" => "error", "message" => "Kendaraan tidak ditemukan."]);
        exit();
    }

    $vehicle_id = $v['id'];

    // Update membership
    $stmt = $koneksi->prepare("UPDATE memberships SET owner_name = ?, membership_expiry = ? WHERE vehicle_id = ?");
    $stmt->bind_param("ssi", $owner, $expiry, $vehicle_id);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Membership berhasil diperbarui."]);
}

$koneksi->close();
?>