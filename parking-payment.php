<?php  
date_default_timezone_set('Asia/Makassar'); // Set timezone to Asia/Makassar
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");

// Include file koneksi
require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $plate = $input['plate_number'];
    $exit_time = $input['exit_time'];
    $amount_paid = $input['amount_paid'];

    // Ambil data kendaraan & log parkir aktif
    $stmt = $koneksi->prepare("SELECT v.id AS vehicle_id, pl.id AS log_id, pl.entry_time 
        FROM vehicles v
        JOIN parking_logs pl ON v.id = pl.vehicle_id
        WHERE v.plate_number = ? AND pl.exit_time IS NULL
        ORDER BY pl.entry_time DESC LIMIT 1");
    $stmt->bind_param("s", $plate);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) {
        echo json_encode(["error" => "Tidak ada log aktif untuk kendaraan ini."]);
        exit;
    }

    $vehicle_id = $res['vehicle_id'];
    $log_id = $res['log_id'];
    $entry_time = strtotime($res['entry_time']);
    $exit = strtotime($exit_time);

    if ($exit <= $entry_time) {
        echo json_encode(["error" => "Waktu keluar tidak valid."]);
        exit;
    }

    $diff_hours = ceil(($exit - $entry_time) / 3600);
    $total_fee = $diff_hours * 5000;  // Rp 5.000 per jam
    $change = $amount_paid - $total_fee;

    // Update log parkir
    $stmt1 = $koneksi->prepare("UPDATE parking_logs SET exit_time = ?, parking_fee = ? WHERE id = ?");
    $stmt1->bind_param("sii", $exit_time, $total_fee, $log_id);
    $stmt1->execute();

    // Simpan pembayaran
    $stmt2 = $koneksi->prepare("INSERT INTO payments (parking_log_id, amount_paid, change_returned, payment_time) VALUES (?, ?, ?, NOW())");
    $stmt2->bind_param("iii", $log_id, $amount_paid, $change);
    $stmt2->execute();

    // Catat pembukaan gate otomatis karena pembayaran valid
    $stmt3 = $koneksi->prepare("INSERT INTO gate_logs (vehicle_id, action, source, timestamp) VALUES (?, 'open', 'Payment Verified', NOW())");
    $stmt3->bind_param("i", $vehicle_id);
    $stmt3->execute();

    echo json_encode([
        "message" => "Pembayaran berhasil diproses.",
        "total_fee" => $total_fee,
        "change" => $change,
        "open_gate" => true
    ]);
}

$koneksi->close();
?>
