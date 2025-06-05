<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Koneksi database
$host     = "localhost";
$user     = "tkjh7215_opengate";
$password = "opengate123";
$database = "tkjh7215_opengate";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]);
    exit();
}

// Ambil data log gerbang + plat kendaraan
$sql = "
    SELECT g.id, v.plate_number, g.action, g.source, g.timestamp
    FROM gate_logs g
    JOIN vehicles v ON g.vehicle_id = v.id
    ORDER BY g.timestamp DESC
";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        "id"         => $row["id"],
        "plate_number" => $row["plate_number"],
        "action"     => $row["action"],
        "source"     => $row["source"],
        "timestamp"  => $row["timestamp"]
    ];
}

echo json_encode($data);

$conn->close();
?>
