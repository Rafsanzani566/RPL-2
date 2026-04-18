<?php
include '../config/database.php'; 

function tambah_data($data, $files){
    global $pdo;

    $nisn           = $data['nisn'];
    $nama           = $data['nama'];
    $jabatan        = $data['jabatan'];
    $instagram_user = str_replace('@', '', $data['instagram_user']); // Hilangkan @ jika ada

    // Handle Foto
    $split         = explode('.', $files['foto']['name']);
    $ekstensi      = end($split);
    $foto          = $nisn.'.'.$ekstensi;

    $dir = "img/";
    if (!is_dir($dir)) mkdir($dir);
    
    move_uploaded_file($files['foto']['tmp_name'], $dir.$foto);

    // Query sesuai kolom SQL: id, nisn, nama, jabatan, instagram_user, foto
    $query = "INSERT INTO siswa (nisn, nama, jabatan, instagram_user, foto) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$nisn, $nama, $jabatan, $instagram_user, $foto]);
}

function ubah_data($data, $files){
    global $pdo;

    $id             = $data['id']; // Primary Key di SQL adalah 'id'
    $nisn           = $data['nisn'];
    $nama           = $data['nama'];
    $jabatan        = $data['jabatan'];
    $instagram_user = str_replace('@', '', $data['instagram_user']);

    $queryShow = "SELECT foto FROM siswa WHERE id = ?";
    $stmtShow = $pdo->prepare($queryShow);
    $stmtShow->execute([$id]);
    $result = $stmtShow->fetch();

    if($files['foto']['name'] == ""){
        $foto = $result['foto'];
    } else {
        $split    = explode('.', $files['foto']['name']);
        $ekstensi = end($split);
        $foto     = $nisn.'.'.$ekstensi;
        
        if(!empty($result['foto']) && file_exists("img/" . $result['foto'])){
            @unlink("img/" . $result['foto']);
        }
        move_uploaded_file($files['foto']['tmp_name'], 'img/' . $foto);
    }

    $query = "UPDATE siswa SET nisn=?, nama=?, jabatan=?, instagram_user=?, foto=? WHERE id=?";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$nisn, $nama, $jabatan, $instagram_user, $foto, $id]);
}

function hapus_data($data){
    global $pdo;
    $id = $data['hapus'];

    $queryShow = "SELECT foto FROM siswa WHERE id = ?";
    $stmtShow = $pdo->prepare($queryShow);
    $stmtShow->execute([$id]);
    $result = $stmtShow->fetch();

    if(!empty($result['foto']) && file_exists("img/" . $result['foto'])){
        @unlink("img/" . $result['foto']);
    }

    $query = "DELETE FROM siswa WHERE id = ?";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$id]);
}
?>