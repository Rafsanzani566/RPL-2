<?php
    // Pastikan path ini benar. Jika database.php di folder yang sama, hapus '../config/'
    require_once '../config/database.php';
    session_start();

    $pdo = getPDO(); 
    $id_siswa = ''; $nisn = ''; $nama = ''; $jabatan = ''; $instagram_user = '';

    if(isset($_GET['ubah'])){
        $id_siswa = $_GET['ubah'];
        
        $query = "SELECT * FROM siswa WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_siswa]);
        $result = $stmt->fetch();

        if($result){
            $nisn = $result['nisn'];
            $nama = $result['nama'];
            $jabatan = $result['jabatan'];
            $instagram_user = $result['instagram_user'];
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Kelola Data - XI RPL 2</title>
</head>
<body>
    <nav class="navbar navbar-light bg-light mb-4 shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Admin Panel</a>
      </div>
    </nav>

    <div class="container">
        <form method="POST" action="proses.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id_siswa; ?>">
            
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">NISN</label>
                <div class="col-sm-10">
                  <input required type="text" name="nisn" class="form-control" value="<?php echo $nisn; ?>" placeholder="Contoh: 00123456">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                  <input required type="text" name="nama" class="form-control" value="<?php echo $nama; ?>" placeholder="Nama Lengkap">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Jabatan</label>
                <div class="col-sm-10">
                    <select required name="jabatan" class="form-select">
                        <option <?php if($jabatan == 'Ketua Kelas') echo "selected"; ?> value="Ketua Kelas">Ketua Kelas</option>
                        <option <?php if($jabatan == 'Sekretaris') echo "selected"; ?> value="Sekretaris">Sekretaris</option>
                        <option <?php if($jabatan == 'Bendahara') echo "selected"; ?> value="Bendahara">Bendahara</option>
                        <option <?php if($jabatan == 'Anggota' || $jabatan == '') echo "selected"; ?> value="Anggota">Anggota</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Instagram</label>
                <div class="col-sm-10">
                  <input type="text" name="instagram_user" class="form-control" value="<?php echo $instagram_user; ?>" placeholder="Username tanpa @">
                </div>
            </div>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Foto</label>
                <div class="col-sm-10">
                    <input <?php if(!isset($_GET['ubah'])) echo "required"; ?> class="form-control" type="file" name="foto" accept="image/*">
                    <?php if(isset($_GET['ubah'])): ?>
                        <div class="form-text text-danger">*Kosongkan jika tidak ingin mengubah foto.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3 row mt-4">
                <div class="col">
                    <?php if(isset($_GET['ubah'])): ?>
                        <button type="submit" name="aksi" value="edit" class="btn btn-primary">
                            <i class="fa fa-floppy-disk"></i> Simpan Perubahan
                        </button>
                    <?php else: ?>
                        <button type="submit" name="aksi" value="add" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Tambahkan Data
                        </button>
                    <?php endif; ?>
                    
                    <a href="index.php" class="btn btn-danger">
                        <i class="fa fa-reply"></i> Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>