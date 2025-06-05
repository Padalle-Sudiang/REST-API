<?php
$host     = "localhost";
$user     = "tkjh7215_opengate";
$password = "opengate123";
$database = "tkjh7215_opengate";

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Koneksi gagal: " . $koneksi->connect_error]);
    exit();
}
?>
