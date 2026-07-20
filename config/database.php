<?php
// Koneksi Database Menggunakan PDO
$host = '127.0.0.1';
$db = 'db_bansos';
$user = 'root'; // Pastikan sesuai dengan XAMPP
$pass = '';     // Secara default XAMPP menggunakan password kosong

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    // Atur atribut agar PDO melempar Exception jika terjadi Error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage() . ". <br>Pastikan XAMPP sudah menyala dan database `db_bansos` sudah dibuat.");
}
?>