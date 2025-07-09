<!-- transaksi.php -->
<?php
session_start();
if ($_SESSION['role'] != 'staff') {
  header('Location: login.php');
  exit();
}
include 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id_barang = $_POST['id_barang'];
  $jenis = $_POST['jenis'];
  $jumlah = $_POST['jumlah'];
  $tanggal = date('Y-m-d');
  mysqli_query($conn, "INSERT INTO transaksi (id_barang, jenis, jumlah, tanggal) VALUES ($id_barang, '$jenis', $jumlah, '$tanggal')");
  if ($jenis == 'masuk') {
    mysqli_query($conn, "UPDATE barang SET stok = stok + $jumlah WHERE id_barang = $id_barang");
  } else {
    mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = $id_barang");
  }
  header('Location: staff_dashboard.php');
}
$barang = mysqli_query($conn, "SELECT * FROM barang");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transaksi Barang</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    :root {
      --primary: #10b981;
      --primary-dark: #059669;
      --secondary: #84cc16;
      --accent: #22c55e;
      --dark: #1e293b;
      --light: #f8fafc;
      --gray: #94a3b8;
      --warehouse-bg: #0f172a;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: var(--warehouse-bg);
      color: white;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      position: relative;
      overflow: hidden;
    }

    .warehouse-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: 
        linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.95)),
        url('wareh9.jpeg') center/cover;
      filter: blur(4px);
      transform: scale(1.02);
    }

    .transaction-container {
      background: rgba(30, 41, 59, 0.8);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      width: 100%;
      max-width: 500px;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
      animation: animate__fadeIn;
    }

    .transaction-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      animation: warehouseFlow 8s linear infinite;
      background-size: 200% 100%;
    }

    h2 {
      color: white;
      margin-bottom: 30px;
      text-align: center;
      font-weight: 600;
      font-size: 28px;
      position: relative;
      display: inline-block;
      width: 100%;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      border-radius: 3px;
    }

    .staff-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 500;
      background-color: rgba(34, 197, 94, 0.2);
      color: #dcfce7;
      border: 1px solid rgba(34, 197, 94, 0.3);
      margin-left: 10px;
      vertical-align: middle;
      backdrop-filter: blur(5px);
    }

    .form-group {
      margin-bottom: 25px;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 10px;
      color: rgba(255, 255, 255, 0.8);
      font-weight: 500;
    }

    select, input {
      width: 100%;
      padding: 15px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      background-color: rgba(15, 23, 42, 0.5);
      color: white;
      appearance: none;
    }

    select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2310b981' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      background-size: 12px;
    }

    select:focus, input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
      background-color: rgba(15, 23, 42, 0.7);
    }

    input::placeholder {
      color: rgba(255, 255, 255, 0.5);
    }

    button {
      width: 100%;
      padding: 16px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      margin-top: 20px;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 25px;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .back-link:hover {
      color: white;
      text-decoration: underline;
    }

    /* Warehouse decorative elements */
    .warehouse-decoration {
      position: fixed;
      z-index: -1;
      pointer-events: none;
    }

    .pallet {
      width: 100px;
      height: 100px;
      border-radius: 6px;
      position: absolute;
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 28px;
      opacity: 0.8;
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.3);
      backdrop-filter: blur(5px);
    }

    .pallet-1 {
      top: 10%;
      left: 5%;
      transform: rotate(-10deg);
      animation: float 8s ease-in-out infinite;
    }

    .pallet-2 {
      bottom: 10%;
      right: 5%;
      transform: rotate(15deg);
      animation: float 9s ease-in-out infinite 1s;
      background: rgba(132, 204, 22, 0.15);
      border: 1px solid rgba(132, 204, 22, 0.3);
    }

    @keyframes warehouseFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    @keyframes float {
      0% { transform: translateY(0) rotate(-10deg); }
      50% { transform: translateY(-15px) rotate(10deg); }
      100% { transform: translateY(0) rotate(-10deg); }
    }

    @media (max-width: 600px) {
      .transaction-container {
        padding: 30px 20px;
      }
      
      h2 {
        font-size: 24px;
      }

      .pallet {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="warehouse-bg"></div>
  
  <!-- Warehouse decorative elements -->
  <div class="warehouse-decoration pallet pallet-1">
    <i class="fas fa-box"></i>
  </div>
  <div class="warehouse-decoration pallet pallet-2">
    <i class="fas fa-pallet"></i>
  </div>

  <div class="transaction-container animate__animated animate__fadeIn">
    <h2>Transaksi Barang <span class="staff-badge">STAFF</span></h2>
    <form method="POST">
      <div class="form-group">
        <label for="id_barang"><i class="fas fa-box-open"></i> Pilih Barang</label>
        <select id="id_barang" name="id_barang" required>
          <option value="">-- Pilih Barang --</option>
          <?php while ($row = mysqli_fetch_assoc($barang)) { ?>
            <option value="<?= $row['id_barang'] ?>"><?= $row['nama'] ?></option>
          <?php } ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="jenis"><i class="fas fa-exchange-alt"></i> Jenis Transaksi</label>
        <select id="jenis" name="jenis" required>
          <option value="masuk">Barang Masuk</option>
          <option value="keluar">Barang Keluar</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="jumlah"><i class="fas fa-calculator"></i> Jumlah</label>
        <input type="number" id="jumlah" name="jumlah" placeholder="Masukkan jumlah" required>
      </div>
      
      <button type="submit">
        <i class="fas fa-save"></i> Simpan Transaksi
      </button>
    </form>
    <a href="staff_dashboard.php" class="back-link">
      <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
    </a>
  </div>
</body>
</html>