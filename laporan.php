<!-- laporan.php -->
<?php
session_start();
if ($_SESSION['role'] != 'admin') {
  header('Location: login.php');
  exit();
}
include 'koneksi.php';
$laporan = mysqli_query($conn, "SELECT t.*, b.nama FROM transaksi t JOIN barang b ON t.id_barang = b.id_barang ORDER BY t.tanggal DESC");
?>
<form action="cetak_laporan.php" method="POST" target="_blank" style="margin-top: 20px;">
  <input type="hidden" name="tanggal" value="<?= $filter ?>">
  <button type="submit" class="print-btn">
    🖨️ Cetak PDF
  </button>
</form>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Stok | Warehouse System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    :root {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --secondary: #f59e0b;
      --accent: #10b981;
      --dark: #1e293b;
      --light: #f8fafc;
      --gray: #94a3b8;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      background: 
        linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.95)),
        url('wareh6.jpg') center/cover fixed;
      color: white;
      padding: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
      z-index: 2;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
    }

    header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100px;
      height: 3px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      border-radius: 3px;
    }

    h2 {
      font-size: 28px;
      font-weight: 700;
      letter-spacing: -0.5px;
      background: linear-gradient(90deg, white 0%, var(--gray) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .action-buttons {
      display: flex;
      gap: 15px;
    }

    .back-link, .print-btn {
      display: inline-flex;
      align-items: center;
      padding: 12px 20px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      gap: 8px;
      border: none;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .back-link:hover, .print-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
      background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary) 100%);
    }

    .report-card {
      background: rgba(30, 41, 59, 0.8);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 
        0 10px 25px -5px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      position: relative;
    }

    .report-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      animation: warehouseFlow 6s linear infinite;
      background-size: 200% 100%;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    thead {
      background: linear-gradient(90deg, rgba(59, 130, 246, 0.3) 0%, rgba(245, 158, 11, 0.2) 100%);
    }

    th {
      padding: 18px 24px;
      text-align: left;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.9);
      position: relative;
      letter-spacing: 0.5px;
    }

    th:not(:last-child)::after {
      content: '';
      position: absolute;
      right: 0;
      top: 20%;
      height: 60%;
      width: 1px;
      background-color: rgba(255, 255, 255, 0.1);
    }

    td {
      padding: 16px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      color: rgba(255, 255, 255, 0.8);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover {
      background-color: rgba(59, 130, 246, 0.1);
    }

    .date-col {
      white-space: nowrap;
      color: var(--gray);
    }

    .name-col {
      font-weight: 500;
      color: white;
    }

    .type-col {
      text-transform: capitalize;
    }

    .qty-col {
      font-weight: 600;
      color: var(--accent);
      text-align: right;
    }

    .empty-state {
      padding: 60px;
      text-align: center;
      color: var(--gray);
      font-size: 16px;
    }

    /* Warehouse decorative elements */
    .warehouse-decoration {
      position: fixed;
      z-index: 1;
      pointer-events: none;
    }

    .pallet {
      width: 80px;
      height: 80px;
      border-radius: 4px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      opacity: 0.9;
      background: rgba(59, 130, 246, 0.2);
      border: 1px solid rgba(59, 130, 246, 0.3);
      backdrop-filter: blur(5px);
    }

    .pallet-1 {
      top: 50px;
      right: 50px;
      animation: float 6s ease-in-out infinite;
    }

    .pallet-2 {
      bottom: 30px;
      left: 30px;
      animation: float 7s ease-in-out infinite 1s;
      background: rgba(245, 158, 11, 0.2);
      border: 1px solid rgba(245, 158, 11, 0.3);
    }

    @keyframes warehouseFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    @keyframes float {
      0% { transform: translateY(0); }
      50% { transform: translateY(-15px); }
      100% { transform: translateY(0); }
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }

      .action-buttons {
        width: 100%;
        flex-direction: column;
      }

      .back-link, .print-btn {
        width: 100%;
        justify-content: center;
      }

      .report-card {
        overflow-x: auto;
      }

      table {
        min-width: 600px;
      }

      th, td {
        padding: 14px 16px;
      }
    }
  </style>
</head>
<body>
  <!-- Warehouse decorative elements -->
  <div class="warehouse-decoration pallet pallet-1">
    <i class="fas fa-box"></i>
  </div>
  <div class="warehouse-decoration pallet pallet-2">
    <i class="fas fa-clipboard-list"></i>
  </div>

  <div class="container">
    <header>
      <h2>Laporan Stok Barang</h2>
      <div class="action-buttons">
        <button class="print-btn" onclick="window.print()">
          <i class="fas fa-print"></i> Cetak
        </button>
        <a href="admin_dashboard.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Kembali
        </a>
      </div>
    </header>

    <div class="report-card">
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
          <?php if(mysqli_num_rows($laporan) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($laporan)) { ?>
            <tr>
              <td class="date-col"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
              <td class="name-col"><?= $row['nama'] ?></td>
              <td class="type-col"><?= $row['jenis'] ?></td>
              <td class="qty-col"><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
            </tr>
            <?php } ?>
          <?php } else { ?>
            <tr>
              <td colspan="4" class="empty-state">
                <i class="fas fa-box-open" style="font-size: 24px; margin-bottom: 10px;"></i><br>
                Tidak ada data laporan stok
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // Add subtle animation to table rows
    document.querySelectorAll('tbody tr').forEach((row, index) => {
      row.style.opacity = '0';
      row.style.transform = 'translateY(10px)';
      row.style.transition = `all 0.3s ease ${index * 0.05}s`;
      
      setTimeout(() => {
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
      }, 100);
    });
  </script>
</body>
</html>