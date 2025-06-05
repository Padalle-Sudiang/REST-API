<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include file koneksi
require_once "db.php";

// Query: ambil data kendaraan yang tidak ada di tabel memberships
$query = "
    SELECT v.id, v.plate_number, v.plate_type, v.is_member, v.image_path, v.created_at
    FROM vehicles v
    ORDER BY v.created_at DESC
";

$result = $koneksi->query($query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$koneksi->close();
?>
