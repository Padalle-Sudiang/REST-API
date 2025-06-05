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
