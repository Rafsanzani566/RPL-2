<?php
require_once 'config/database.php';
$pass = 'Admin@2025'; // Balikin ke sandi awal yang kamu mau
$hash = password_hash($pass, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
if($stmt->execute([$hash])) {
    echo "BERHASIL! Sekarang password di database sudah disinkronkan dengan PHP kamu.";
}