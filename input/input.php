
<?php

$conn = new mysqli("localhost", "root", "", "latihan");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$kecamatan = $_POST['kecamatan'];
$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
$nama = $_POST['nama'];
$pemilik = $_POST['pemilik'];

$sql = "INSERT INTO olahraga_banyumas
(kecamatan, longitude, latitude, nama, pemilik)
VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sddss",
    $kecamatan,
    $longitude,
    $latitude,
    $nama,
    $pemilik
);

if ($stmt->execute()) {
    header("Location: ../index.php");
    exit;
} else {
    echo "Data gagal ditambahkan: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>
