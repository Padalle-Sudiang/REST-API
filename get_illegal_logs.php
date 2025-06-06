<?php
date_default_timezone_set('Asia/Makassar'); // Set timezone to Asia/Makassar
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include file koneksi
require_once "db.php";

// Proses GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "
        SELECT 
            pl.id AS log_id,
            v.plate_number,
            pl.entry_time,
            pl.exit_time,
            pl.img_path,
            pl.created_at AS log_created_at
        FROM parking_logs pl
        JOIN vehicles v ON v.id = pl.vehicle_id
        JOIN illegal_plates ip ON ip.plate_number = v.plate_number
        ORDER BY pl.entry_time DESC
    ";

    $result = $koneksi->query($query);

    if ($result) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode([
            "status" => "success",
            "logs" => $data
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Gagal mengambil data log."
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Metode tidak diizinkan."
    ]);
}

$koneksi->close();
?>
