<!-- admin_dashboard.php -->
<?php
session_start();
if ($_SESSION['role'] != 'admin') {
  header('Location: login.php');
  exit();
}
include 'koneksi.php';
$barang = mysqli_query($conn, "SELECT * FROM barang");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    :root {
      --primary: #3b82f6;
      --primary-dark: #2563eb;
      --secondary: #f59e0b;
      --accent: #10b981;
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
      overflow-x: hidden;
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
        url('wareh3.jpeg') center/cover;
      filter: blur(4px);
      transform: scale(1.02);
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    header {
      background: rgba(30, 41, 59, 0.8);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      color: white;
      padding: 20px 0;
      margin-bottom: 30px;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
      position: relative;
      overflow: hidden;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    header::before {
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

    header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    h2 {
      font-weight: 600;
      font-size: 24px;
      position: relative;
      display: inline-block;
      color: white;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 0;
      width: 50px;
      height: 3px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      border-radius: 3px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      padding: 10px 20px;
      border-radius: 6px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .nav-links a:hover {
      background-color: rgba(59, 130, 246, 0.3);
      transform: translateY(-2px);
      border-color: rgba(59, 130, 246, 0.5);
    }

    .nav-links a.logout {
      background-color: rgba(231, 76, 60, 0.3);
      border-color: rgba(231, 76, 60, 0.5);
      box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
    }

    .nav-links a.logout:hover {
      background-color: rgba(192, 57, 43, 0.4);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
    }

    .card {
      background: rgba(30, 41, 59, 0.7);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      padding: 30px;
      margin-bottom: 30px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
      border-color: rgba(59, 130, 246, 0.3);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(to bottom, var(--primary), var(--secondary));
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 15px 20px;
      text-align: left;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    th {
      background-color: rgba(42, 82, 152, 0.7);
      color: white;
      font-weight: 500;
      position: sticky;
      top: 0;
      backdrop-filter: blur(5px);
    }

    tr:hover {
      background-color: rgba(255, 255, 255, 0.05);
    }

    tr:nth-child(even) {
      background-color: rgba(255, 255, 255, 0.03);
    }

    tr:nth-child(even):hover {
      background-color: rgba(255, 255, 255, 0.08);
    }

    .action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 16px;
      background-color: rgba(52, 152, 219, 0.3);
      color: white;
      text-decoration: none;
      border-radius: 6px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      font-size: 14px;
      font-weight: 500;
      gap: 8px;
      box-shadow: 0 2px 10px rgba(52, 152, 219, 0.2);
      border: 1px solid rgba(52, 152, 219, 0.3);
    }

    .action-btn:hover {
      background-color: rgba(41, 128, 185, 0.4);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
    }

    .action-btn.edit {
      background-color: rgba(46, 204, 113, 0.3);
      box-shadow: 0 2px 10px rgba(46, 204, 113, 0.2);
      border: 1px solid rgba(46, 204, 113, 0.3);
    }

    .action-btn.edit:hover {
      background-color: rgba(39, 174, 96, 0.4);
      box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
    }

    .action-btn i {
      font-size: 14px;
    }

    .status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      backdrop-filter: blur(5px);
    }

    .status-in {
      background-color: rgba(40, 167, 69, 0.2);
      color: #d4edda;
      border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .status-out {
      background-color: rgba(220, 53, 69, 0.2);
      color: #f8d7da;
      border: 1px solid rgba(220, 53, 69, 0.3);
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
      background: rgba(59, 130, 246, 0.15);
      border: 1px solid rgba(59, 130, 246, 0.3);
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
      background: rgba(249, 115, 22, 0.15);
      border: 1px solid rgba(249, 115, 22, 0.3);
    }

    .forklift {
      position: fixed;
      bottom: -50px;
      right: -100px;
      font-size: 120px;
      color: rgba(255, 255, 255, 0.1);
      transform: rotate(-20deg);
      z-index: -1;
      animation: moveForklift 25s linear infinite;
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

    @keyframes moveForklift {
      0% { transform: translateX(0) rotate(-20deg); }
      100% { transform: translateX(-100vw) rotate(-20deg); }
    }

    @media (max-width: 768px) {
      header .container {
        flex-direction: column;
        text-align: center;
      }

      h2::after {
        left: 50%;
        transform: translateX(-50%);
      }

      .nav-links {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
      }

      .nav-links a {
        margin: 0;
      }

      table {
        display: block;
        overflow-x: auto;
      }
      
      .card {
        padding: 20px 15px;
      }
      
      th, td {
        padding: 12px 15px;
      }

      .pallet, .forklift {
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
  <div class="warehouse-decoration forklift">
    <i class="fas fa-forklift"></i>
  </div>

  <header class="animate__animated animate__fadeInDown">
    <div class="container">
      <h2 class="animate__animated animate__fadeIn">Dashboard Admin</h2>
      <div class="nav-links">
        <a href="tambah_barang.php" class="animate__animated animate__fadeIn animate__delay-1s">
          <i class="fas fa-plus-circle"></i> Tambah Barang
        </a>
        <a href="laporan.php" class="animate__animated animate__fadeIn animate__delay-1s">
          <i class="fas fa-file-alt"></i> Laporan Stok
        </a>
        <a href="beranda.html" class="logout animate__animated animate__fadeIn animate__delay-2s">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="card animate__animated animate__fadeIn animate__delay-1s">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Barang</th>
            <th>Stok</th>
            <th>Satuan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($barang)) { ?>
          <tr class="animate__animated animate__fadeIn">
            <td><?= $row['id_barang'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td>
              <span class="status-badge <?= $row['stok'] > 0 ? 'status-in' : 'status-out' ?>">
                <?= $row['stok'] ?>
              </span>
            </td>
            <td><?= $row['satuan'] ?></td>
            <td>
              <a href="edit_barang.php?id=<?= $row['id_barang'] ?>" class="action-btn edit">
                <i class="fas fa-edit"></i> Edit
              </a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // Add animation to table rows as they appear
    document.addEventListener('DOMContentLoaded', () => {
      const rows = document.querySelectorAll('tbody tr');
      rows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
      });
    });
  </script>
</body>
</html>