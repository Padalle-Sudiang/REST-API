<?php
date_default_timezone_set('Asia/Makassar'); // Set timezone to Asia/Makassar
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include file koneksi
require_once "db.php";

// Ambil data log gerbang + plat kendaraan
$sql = "
    SELECT g.id, v.plate_number, g.action, g.source, g.timestamp
    FROM gate_logs g
    JOIN vehicles v ON g.vehicle_id = v.id
    ORDER BY g.timestamp DESC
";

$result = $koneksi->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id"           => $row["id"],
        "plate_number" => $row["plate_number"],
        "action"       => $row["action"],
        "source"       => $row["source"],
        "timestamp"    => $row["timestamp"]
    ];
}

echo json_encode($data);
$koneksi->close();
?>
