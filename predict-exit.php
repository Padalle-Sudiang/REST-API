<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $plate_number = $_POST['plate_number'] ?? null;  
    $plate_type   = $_POST['plate_type'] ?? null;

    if (!$plate_number || !$plate_type) {
        echo json_encode([
            "status" => "error",
            "message" => "Plate number dan plate type wajib diisi."
        ]);
        exit();
    }

    // Cari vehicle ID
    $stmt = $koneksi->prepare("SELECT id FROM vehicles WHERE plate_number = ?");
    $stmt->bind_param("s", $plate_number);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) {
        echo json_encode(["status" => "error", "message" => "Kendaraan tidak ditemukan."]);
        exit();
    }

    $vehicle_id = $res['id'];

    // Cari log aktif terakhir yang belum punya gambar keluar
    $stmt = $koneksi->prepare("
    SELECT id FROM parking_logs 
    WHERE vehicle_id = ? AND img_path_exit IS NULL 
    ORDER BY entry_time DESC LIMIT 1
    ");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $log = $stmt->get_result()->fetch_assoc();

    if (!$log) {
        echo json_encode(["status" => "error", "message" => "Log parkir keluar tidak ditemukan atau sudah punya gambar."]);
        exit();
    }

    $log_id = $log['id'];

    // Simpan gambar keluar
    $upload_folder = __DIR__ . '/uploads/';
    $timestamp = date("Ymd_His");
    $sanitized_plate = preg_replace("/[^A-Za-z0-9]/", "", $plate_number);
    $unique_name = $sanitized_plate . "_exit_" . $timestamp . '.jpg';
    $filename_server = $upload_folder . $unique_name;
    $filename_url = 'http://tkj-3b.com/tkj-3b.com/opengate/uploads/' . $unique_name;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filename_server)) {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan gambar keluar."]);
        exit();
    }

    // Update log
    $stmt = $koneksi->prepare("UPDATE parking_logs SET img_path_exit = ? WHERE id = ?");
    $stmt->bind_param("si", $filename_url, $log_id);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Gambar kendaraan keluar berhasil disimpan.",
        "image_exit_path" => $filename_url
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Request tidak valid."]);
}

$koneksi->close();
?>
