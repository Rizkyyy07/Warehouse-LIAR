<!-- login.php -->
<?php
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($user) {
  $_SESSION['username'] = $username;
  $_SESSION['role'] = $user['role'];
  if ($user['role'] == 'admin') {
    header('Location: admin_dashboard.php');
  } else {
    header('Location: staff_dashboard.php');
  }
} else {
  echo "Login gagal. <a href='index.html'>Coba lagi</a>";
}
?>
