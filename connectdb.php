<?php
$servername = "localhost";
$username = "root";
$password = ""; // Ganti dengan password Anda jika ada

$conn = new mysqli($servername, $username, $password); // Koneksi tanpa memilih database dulu

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pilih database
$dbname = "miksu"; // Ganti dengan nama database Anda
if (!mysqli_select_db($conn, $dbname)) {
    die("Error selecting database: " . mysqli_error($conn));
}

