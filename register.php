<?php
include 'koneksi.php'; // file koneksi ke MySQL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $role = $_POST['role'];

  // Validasi sederhana
  if (!empty($username) && !empty($password) && !empty($role)) {
    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    $result = mysqli_query($conn, $query);

    if ($result) {
      echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='index.html';</script>";
    } else {
      echo "Gagal mendaftar: " . mysqli_error($conn);
    }
  } else {
    echo "Semua field harus diisi.";
  }
}
?>

