<?php 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 

// Konfigurasi koneksi ke database
$host     = "localhost";
$user     = "tkjh7215_opengate";
$password = "opengate123";
$database = "tkjh7215_opengate";

$koneksi = new mysqli($host, $user, $password, $database);
if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Koneksi gagal: " . $koneksi->connect_error
    ]);
    exit();
}

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

     // Cek apakah kendaraan sudah terdaftar (SEBELUM simpan gambar)
    $stmt = $koneksi->prepare("SELECT id, is_member FROM vehicles WHERE plate_number = ?");
    $stmt->bind_param("s", $plate_number);
    $stmt->execute();
    $result = $stmt->get_result();

    $status = "new";
    $vehicle_id = null;
    $is_member = false;
    $filename_url = null;

    if ($row = $result->fetch_assoc()) {
        $status = "existing";
        $vehicle_id = $row['id'];
        $is_member = (bool)$row['is_member'];
    } else {
        // HANYA jika plat belum ada, simpan gambar
        $upload_folder = __DIR__ . '/uploads/';
        $timestamp = date("Ymd_His");
        $sanitized_plate = preg_replace("/[^A-Za-z0-9]/", "", $plate_number);
        $unique_name = $sanitized_plate . "_" . $timestamp . '.jpg';
        $filename_server = $upload_folder . $unique_name;
        $filename_url = 'http://tkj-3b.com/tkj-3b.com/opengate/uploads/' . $unique_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $filename_server)) {
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan gambar."
            ]);
            exit();
        }

        $stmt = $koneksi->prepare("INSERT INTO vehicles (plate_number, plate_type, is_member, image_path, created_at) VALUES (?, ?, 0, ?, NOW())");
        $stmt->bind_param("sss", $plate_number, $plate_type, $filename_url);
        $stmt->execute();
        $vehicle_id = $koneksi->insert_id;
    }

    // Cek membership aktif
    $stmt = $koneksi->prepare("SELECT owner_name FROM memberships WHERE vehicle_id = ? AND membership_expiry >= CURDATE()");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $member_info = $res->fetch_assoc();
    $is_member = $member_info ? true : false;

    // Cek apakah plat ilegal
    $stmt = $koneksi->prepare("SELECT description FROM illegal_plates WHERE plate_number = ?");
    $stmt->bind_param("s", $plate_number);
    $stmt->execute();
    $illegal = $stmt->get_result()->fetch_assoc();
    $is_illegal = $illegal ? true : false;

    // Cek riwayat masuk aktif
    $stmt = $koneksi->prepare("SELECT id, entry_time FROM parking_logs WHERE vehicle_id = ? AND exit_time IS NULL");
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $has_active_log = false;
    $entry_time = null;

    if ($log = $res->fetch_assoc()) {
        $has_active_log = true;
        $entry_time = $log['entry_time'];
    } else {
        $entry_time = date("Y-m-d H:i:s");
        $stmt = $koneksi->prepare("INSERT INTO parking_logs (vehicle_id, entry_time, img_path, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $vehicle_id, $entry_time, $filename_url);
        $stmt->execute();
    }

    // Logika buka gate
    $open_gate = false;
    $gate_source = null;

    if ($is_illegal) {
        $open_gate = false; // Plat ilegal tidak boleh masuk
    } elseif ($is_member) {
        $open_gate = true;
        $gate_source = 'member';
    } elseif (!$has_active_log) {
        $open_gate = true;
        $gate_source = 'new_vehicle';
    }

    // Simpan gate log jika gate dibuka
    if ($open_gate && $gate_source) {
        $stmt = $koneksi->prepare("INSERT INTO gate_logs (vehicle_id, action, source, timestamp) VALUES (?, 'open', ?, NOW())");
        $stmt->bind_param("is", $vehicle_id, $gate_source);
        $stmt->execute();
    }

    // Output response
    echo json_encode([
        "plate_number" => $plate_number,
        "plate_type" => $plate_type,
        "status" => $status,
        "entry_time" => $entry_time,
        "image_path" => $filename_url,
        "illegal_status" => [
            "is_illegal" => $is_illegal,
            "description" => $illegal['description'] ?? null
        ],
        "membership" => [
            "is_member" => $is_member,
            "owner_name" => $member_info['owner_name'] ?? null
        ],
        "open_gate" => $open_gate
    ]);
} else {
    echo json_encode(["error" => "Invalid request"]);
}

$koneksi->close();
?>
