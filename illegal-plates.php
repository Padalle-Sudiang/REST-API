<?php 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include file koneksi
require_once "db.php";

// POST: Tambah data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['plate_number'], $input['nama_pelapor'], $input['no_wa'], $input['description'])) {
        http_response_code(400);
        echo json_encode(["error" => "Data tidak lengkap."]);
        exit();
    }

    $plate   = $input['plate_number'];
    $pelapor = $input['nama_pelapor'];
    $wa      = $input['no_wa'];
    $desc    = $input['description'];

    $stmt = $koneksi->prepare("INSERT INTO illegal_plates (plate_number, nama_pelapor, no_wa, description, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $plate, $pelapor, $wa, $desc);

    if ($stmt->execute()) {
        echo json_encode([
            "message" => "Plat ilegal berhasil ditambahkan.",
            "plate_number" => $plate
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Gagal menambahkan plat ilegal.", "details" => $stmt->error]);
    }

// GET: Ambil semua data
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $koneksi->query("SELECT plate_number, nama_pelapor, no_wa, description, created_at FROM illegal_plates");

    if ($result) {
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Gagal mengambil data."]);
    }

// PUT: Update data berdasarkan plate_number
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['plate_number'], $input['nama_pelapor'], $input['no_wa'], $input['description'])) {
        http_response_code(400);
        echo json_encode(["error" => "Data tidak lengkap."]);
        exit();
    }

    $plate   = $input['plate_number'];
    $pelapor = $input['nama_pelapor'];
    $wa      = $input['no_wa'];
    $desc    = $input['description'];

    $stmt = $koneksi->prepare("UPDATE illegal_plates SET nama_pelapor = ?, no_wa = ?, description = ? WHERE plate_number = ?");
    $stmt->bind_param("ssss", $pelapor, $wa, $desc, $plate);

    if ($stmt->execute()) {
        echo json_encode([
            "message" => "Data plat ilegal berhasil diperbarui.",
            "plate_number" => $plate
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Gagal memperbarui data.", "details" => $stmt->error]);
    }

// DELETE: Hapus data berdasarkan plate_number
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $koneksi->prepare("DELETE FROM illegal_plates WHERE plate_number = ?");
    $stmt->bind_param("s", $plate);

    if ($stmt->execute()) {
        echo json_encode([
            "message" => "Data plat ilegal berhasil dihapus.",
            "plate_number" => $plate
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Gagal menghapus data.", "details" => $stmt->error]);
    }
}

$koneksi->close();
?>
