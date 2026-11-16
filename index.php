
<?php
// Koneksi Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "latihan";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil Data dari Database
$sql = "SELECT * FROM olahraga_banyumas";
$result = $conn->query($sql);

$markers = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;

    if (
        is_numeric($row['longitude']) &&
        is_numeric($row['latitude']) &&
        $row['longitude'] >= -180 &&
        $row['longitude'] <= 180 &&
        $row['latitude'] >= -90 &&
        $row['latitude'] <= 90
    ) {
        $markers[] = [
            'kecamatan' => $row['kecamatan'],
            'longitude' => (float) $row['longitude'],
            'latitude' => (float) $row['latitude'],
            'nama' => $row['nama'],
            'pemilik' => $row['pemilik']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Web GIS</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet"
    href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css">

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        #map {
            width: 100%;
            height: 500px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #dddddd;
        }
    </style>
</head>

<body>

    <!-- Peta -->
    <div id="map"></div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>

    <script>
        /* Initial Map */
        var map = L.map('map').setView([-7.424, 109.2], 10);

        /* Tile Basemap */
        var basemap1 = L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri'
            }
        );

        var basemap2 = L.tileLayer(
            'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: 'Map data &copy; Google'
            }
        );

        basemap1.addTo(map);

        /* Data Marker dari Database */
        var lokasi = <?php echo json_encode(
            $markers,
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
        ); ?>;

        var markerLayer = L.layerGroup();

        lokasi.forEach(function(item) {

            var marker = L.marker([
                item.latitude,
                item.longitude
            ]);

            var popup = document.createElement('div');
            var nama = document.createElement('b');

            nama.textContent = item.nama;

            popup.appendChild(nama);
            popup.appendChild(document.createElement('br'));

            popup.appendChild(document.createTextNode(
                "Kecamatan: " + item.kecamatan
            ));

            popup.appendChild(document.createElement('br'));

            popup.appendChild(document.createTextNode(
                "Pemilik: " + item.pemilik
            ));

            marker.bindPopup(popup);
            marker.addTo(markerLayer);
        });

        markerLayer.addTo(map);

        /* Control Layer */
        var baseMaps = {
            "Esri Imagery": basemap1,
            "Google Street": basemap2
        };

        var overlayMaps = {
            "Lokasi Olahraga": markerLayer
        };

        L.control.layers(
            baseMaps,
            overlayMaps,
            {
                collapsed: false
            }
        ).addTo(map);

        /* Scale Bar */
        L.control.scale({
            maxWidth: 150,
            position: 'bottomright'
        }).addTo(map);
    </script>

    <br>

    <!-- INPUT DATA DI LUAR TABEL -->
    <a href="input/index.html" target="_blank">
        Input Data
    </a>

    <br><br>

    <!-- Tabel Database -->
    <table>

        <tr>
            <th>ID</th>
            <th>Kecamatan</th>
            <th>Longitude</th>
            <th>Latitude</th>
            <th>Nama</th>
            <th>Pemilik</th>
            <th colspan="2">Aksi</th>
        </tr>

        <?php foreach ($data as $row) { ?>

            <tr>
                <td>
                    <?= htmlspecialchars($row['id']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['kecamatan']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['longitude']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['latitude']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['nama']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($row['pemilik']) ?>
                </td>

                <!-- AKSI HANYA EDIT DAN HAPUS -->
                <td>
                    <a href="edit/index.php?id=<?= urlencode($row['id']) ?>">
                        Edit
                    </a>
                </td>

                <td>
                    <a href="delete.php?id=<?= urlencode($row['id']) ?>"
                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                        Hapus
                    </a>
                </td>
            </tr>

        <?php } ?>

    </table>

    <?php $conn->close(); ?>

</body>
</html>
