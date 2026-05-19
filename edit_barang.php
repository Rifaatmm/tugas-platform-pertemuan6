<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Hanya izinkan metode PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode tidak diizinkan"]);
    exit();
}

// Ambil koneksi database (sesuaikan dengan file koneksi kamu)
include 'koneksi.php'; // pastikan file koneksi.php ada

// Ambil data dari body request (JSON)
$input = json_decode(file_get_contents("php://input"), true);

// Validasi input
if (
    empty($input['id']) ||
    empty($input['nama_barang']) ||
    empty($input['kategori']) ||
    !isset($input['harga']) ||
    !isset($input['stok'])
) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit();
}

$id           = intval($input['id']);
$nama_barang  = mysqli_real_escape_string($conn, $input['nama_barang']);
$kategori     = mysqli_real_escape_string($conn, $input['kategori']);
$harga        = floatval($input['harga']);
$stok         = intval($input['stok']);

// Query UPDATE
$sql = "UPDATE barang 
        SET nama_barang='$nama_barang', kategori='$kategori', harga='$harga', stok='$stok'
        WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        http_response_code(200);
        echo json_encode([
            "status"  => "success",
            "message" => "Data barang berhasil diperbarui"
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "status"  => "error",
            "message" => "Data tidak ditemukan atau tidak ada perubahan"
        ]);
    }
} else {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal memperbarui data: " . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
