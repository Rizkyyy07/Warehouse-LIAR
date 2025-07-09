<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
use Dompdf\Dompdf;

include 'koneksi.php';

$query = "SELECT t.*, b.nama FROM transaksi t JOIN barang b ON t.id_barang = b.id_barang ORDER BY t.tanggal DESC";
$result = mysqli_query($conn, $query);

// Cek jika tidak ada data
if (mysqli_num_rows($result) == 0) {
    die("❌ Tidak ada data transaksi ditemukan.");
}

// Buat HTML + CSS
$html = '
<style>
  body { font-family: Arial, sans-serif; }
  h2 { text-align: center; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th, td { border: 1px solid #000; padding: 8px; text-align: center; }
  th { background-color: #e0f0ff; }
</style>
<h2>Laporan Transaksi Barang</h2>
<table>
  <thead>
    <tr>
      <th>Tanggal</th>
      <th>Nama Barang</th>
      <th>Jenis</th>
      <th>Jumlah</th>
    </tr>
  </thead>
  <tbody>
';

// Tambahkan data ke tabel
while ($row = mysqli_fetch_assoc($result)) {
    $html .= "<tr>
        <td>{$row['tanggal']}</td>
        <td>{$row['nama']}</td>
        <td>{$row['jenis']}</td>
        <td>{$row['jumlah']}</td>
    </tr>";
}

$html .= '</tbody></table>';

// Inisialisasi dan render PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Transaksi_Barang.pdf", array("Attachment" => true));
?>
