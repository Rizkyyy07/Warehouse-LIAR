<!-- edit_barang.php -->
<?php
session_start();
if ($_SESSION['role'] != 'admin') {
  header('Location: login.php');
  exit();
}
include 'koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM barang WHERE id_barang=$id"));
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = $_POST['nama'];
  $stok = $_POST['stok'];
  $satuan = $_POST['satuan'];
  mysqli_query($conn, "UPDATE barang SET nama='$nama', stok=$stok, satuan='$satuan' WHERE id_barang=$id");
  header('Location: admin_dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Barang | Warehouse System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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
      --warehouse-bg: #e2e8f0;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      perspective: 1000px;
      overflow: hidden;
      background-color: #0f172a;
    }

    .warehouse-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: 
        linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)),
        url('https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80') center/cover;
      filter: blur(4px);
      transform: scale(1.02);
    }

    .warehouse-container {
      position: relative;
      width: 100%;
      max-width: 500px;
      padding: 0 20px;
    }

    .form-container {
      background: rgba(30, 41, 59, 0.8);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      width: 100%;
      padding: 50px 40px;
      border-radius: 16px;
      box-shadow: 
        0 10px 25px -5px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.05);
      position: relative;
      overflow: hidden;
      transform-style: preserve-3d;
      transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      animation: fadeInUp 0.8s ease-out;
      border: 1px solid rgba(255, 255, 255, 0.1);
      z-index: 2;
      color: white;
    }

    .form-container::before {
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

    .warehouse-decoration {
      position: absolute;
      z-index: 1;
      pointer-events: none;
    }

    .pallet {
      width: 80px;
      height: 80px;
      border-radius: 4px;
      position: absolute;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      opacity: 0.9;
      transform-style: preserve-3d;
      background: rgba(59, 130, 246, 0.2);
      border: 1px solid rgba(59, 130, 246, 0.3);
      backdrop-filter: blur(5px);
    }

    .pallet::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 15px;
      background: rgba(59, 130, 246, 0.1);
      bottom: -15px;
      transform: skewX(45deg);
      transform-origin: top;
      opacity: 0.6;
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-top: none;
    }

    .pallet-1 {
      top: -40px;
      left: -20px;
      transform: rotate(-5deg);
      animation: float 6s ease-in-out infinite;
    }

    .pallet-2 {
      bottom: -30px;
      right: -30px;
      transform: rotate(8deg);
      animation: float 7s ease-in-out infinite 1s;
      background: rgba(249, 115, 22, 0.2);
      border: 1px solid rgba(249, 115, 22, 0.3);
    }

    .pallet-2::after {
      background: rgba(249, 115, 22, 0.1);
      border: 1px solid rgba(249, 115, 22, 0.2);
      border-top: none;
    }

    .barcode {
      position: absolute;
      right: 30px;
      top: 30px;
      display: flex;
      gap: 2px;
      height: 50px;
    }

    .barcode-line {
      background-color: rgba(255, 255, 255, 0.7);
      width: 4px;
      height: 100%;
      border-radius: 2px;
    }

    h2 {
      color: white;
      margin-bottom: 30px;
      text-align: center;
      font-weight: 700;
      font-size: 28px;
      position: relative;
      display: inline-block;
      width: 100%;
      letter-spacing: -0.5px;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
      border-radius: 4px;
    }

    .form-group {
      margin-bottom: 25px;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 10px;
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
      font-size: 14px;
      transition: all 0.3s;
      letter-spacing: -0.2px;
    }

    .input-wrapper {
      position: relative;
    }

    input {
      width: 100%;
      padding: 15px 20px 15px 50px;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 8px;
      font-size: 15px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      background-color: rgba(15, 23, 42, 0.5);
      color: white;
      font-family: 'Inter', sans-serif;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
    }

    input::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
      background-color: rgba(15, 23, 42, 0.7);
    }

    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray);
      font-size: 18px;
    }

    button {
      width: 100%;
      padding: 16px;
      background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      margin-top: 10px;
      letter-spacing: 0.2px;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
      background: linear-gradient(90deg, var(--primary-dark) 0%, var(--primary) 100%);
    }

    button:active {
      transform: translateY(0);
    }

    button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: 0.5s;
    }

    button:hover::before {
      left: 100%;
    }

    .back-link {
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      margin-top: 25px;
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
      gap: 8px;
      font-size: 14px;
      color: rgba(255, 255, 255, 0.7);
    }

    .back-link:hover {
      color: white;
      transform: translateX(-3px);
    }

    .form-footer {
      margin-top: 30px;
      text-align: center;
      font-size: 12px;
      color: rgba(255, 255, 255, 0.5);
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      padding-top: 20px;
    }

    .scanner {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.7), transparent);
      animation: scan 2s linear infinite;
      opacity: 0;
      transition: opacity 0.3s;
    }

    .form-container:hover .scanner {
      opacity: 1;
    }

    .forklift {
      position: absolute;
      bottom: -50px;
      right: -100px;
      font-size: 100px;
      color: rgba(255, 255, 255, 0.1);
      transform: rotate(-20deg);
      z-index: 1;
      animation: moveForklift 20s linear infinite;
    }

    @keyframes warehouseFlow {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    @keyframes float {
      0% { transform: translateY(0) rotate(-5deg); }
      50% { transform: translateY(-10px) rotate(5deg); }
      100% { transform: translateY(0) rotate(-5deg); }
    }

    @keyframes scan {
      0% { transform: translateY(0); }
      50% { transform: translateY(100%); }
      100% { transform: translateY(0); }
    }

    @keyframes moveForklift {
      0% { transform: translateX(0) rotate(-20deg); }
      100% { transform: translateX(-100vw) rotate(-20deg); }
    }

    @media (max-width: 600px) {
      .form-container {
        padding: 40px 25px;
        margin: 20px;
        width: calc(100% - 40px);
      }
      
      h2 {
        font-size: 24px;
      }
      
      input {
        padding: 14px 20px 14px 45px;
      }
      
      .input-icon {
        font-size: 16px;
      }

      .pallet, .forklift {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="warehouse-bg"></div>
  
  <div class="warehouse-container">
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
    
    <!-- Main form container -->
    <div class="form-container animate__animated animate__fadeInUp">
      <div class="scanner"></div>
      <div class="barcode">
      </div>
      
      <h2 class="animate__animated animate__fadeIn">Edit Barang</h2>
      <form method="POST">
        <div class="form-group animate__animated animate__fadeIn animate__delay-1s">
          <label for="nama">Nama Barang</label>
          <div class="input-wrapper">
            <i class="fas fa-box-open input-icon"></i>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" placeholder="Masukkan nama barang" required>
          </div>
        </div>
        
        <div class="form-group animate__animated animate__fadeIn animate__delay-1s">
          <label for="stok">Stok</label>
          <div class="input-wrapper">
            <i class="fas fa-boxes input-icon"></i>
            <input type="number" id="stok" name="stok" value="<?= htmlspecialchars($data['stok']) ?>" placeholder="Masukkan jumlah stok" required>
          </div>
        </div>
        
        <div class="form-group animate__animated animate__fadeIn animate__delay-1s">
          <label for="satuan">Satuan</label>
          <div class="input-wrapper">
            <i class="fas fa-weight input-icon"></i>
            <input type="text" id="satuan" name="satuan" value="<?= htmlspecialchars($data['satuan']) ?>" placeholder="Contoh: kg, pcs, liter" required>
          </div>
        </div>
        
        <button type="submit" class="animate__animated animate__fadeIn animate__delay-2s">
          <i class="fas fa-save"></i> Simpan Perubahan
        </button>
      </form>
      
      <a href="admin_dashboard.php" class="back-link animate__animated animate__fadeIn animate__delay-2s">
        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
      </a>
      
      <div class="form-footer">
        Warehouse LIAR
      </div>
    </div>
  </div>

  <script>
    // 3D tilt effect
    const formContainer = document.querySelector('.form-container');
    
    formContainer.addEventListener('mousemove', (e) => {
      const xAxis = (window.innerWidth / 2 - e.pageX) / 20;
      const yAxis = (window.innerHeight / 2 - e.pageY) / 20;
      formContainer.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
    });

    formContainer.addEventListener('mouseleave', () => {
      formContainer.style.transform = 'rotateY(0deg) rotateX(0deg)';
    });
    
    // Generate random barcode lines
    const barcode = document.querySelector('.barcode');
    for (let i = 0; i < 12; i++) {
      const line = document.createElement('div');
      line.className = 'barcode-line';
      line.style.height = `${Math.random() * 30 + 50}%`;
      barcode.appendChild(line);
    }
  </script>
</body>
</html>