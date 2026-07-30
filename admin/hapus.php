<?php 
include '../includes/koneksi.php';
session_start();

$id = $_GET['id'];

$produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_produk FROM produk WHERE id='$id'"));
$nama = $produk['nama_produk'];

mysqli_query($conn, "DELETE FROM produk WHERE id = '$id'");

mysqli_query($conn, "INSERT INTO log_aktivitas (admin, aksi) VALUES ('{$_SESSION['admin']}', 'Menghapus produk: $nama')");

header('Location: index.php?success=hapus');
?>