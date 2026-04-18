<?php
    include 'koneksi.php';
    session_start();

    $id_siswa = ''; $nisn = ''; $nama = ''; $jenis_kelamin = ''; $alamat = '';

    if(isset($_GET['ubah'])){
        $id_siswa = $_GET['ubah'];
        $query = "SELECT * FROM siswa WHERE id_siswa = '$id_siswa';";
        $sql = mysqli_query($connect, $query);
        $result = mysqli_fetch_assoc($sql);

        $nisn = $result['nisn'];
        $nama = $result['nama'];
        $jenis_kelamin = $result['jenis_kelamin'];
        $alamat = $result['alamat'];
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Kelola Data</title>
</head>
<body>
    <nav class="navbar navbar-light bg-light mb-4 shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Panel</a>
      </div>
    </nav>

    <div class="container">
        <form method="POST" action="proses.php" enctype="multipart/form-data">
            <input type="hidden" name="id_siswa" value="<?php echo $id_siswa; ?>">
            
            <div class="mb-3 row">
                <label for="nisn" class="col-sm-2 col-form-label">NISN</label>
                <div class="col-sm-10">
                  <input required type="text" name="nisn" class="form-control" id="nisn" value="<?php echo $nisn; ?>" placeholder="Ex: 112233">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="nama" class="col-sm-2 col-form-label">Nama Siswa</label>
                <div class="col-sm-10">
                  <input required type="text" name="nama" class="form-control" id="nama" value="<?php echo $nama; ?>" placeholder="Ex: Alex">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="jkel" class="col-sm-2 col-form-label">Jenis Kelamin</label>
                <div class="col-sm-10">
                    <select required id="jkel" name="jenis_kelamin" class="form-select">
                        <option <?php if($jenis_kelamin == 'Laki-laki' || $jenis_kelamin == 'Laki Laki'){ echo "selected"; } ?> value="Laki-laki">Laki-laki</option>
                        <option <?php if($jenis_kelamin == 'Perempuan'){ echo "selected"; } ?> value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="foto" class="col-sm-2 col-form-label">Foto Siswa</label>
                <div class="col-sm-10">
                    <input <?php if(!isset($_GET['ubah'])){ echo "required"; } ?> class="form-control" type="file" name="foto" id="foto" accept="image/*">
                </div>
            </div>

            <div class="mb-3 row">
                <label for="alamat" class="col-sm-2 col-form-label">Alamat Lengkap</label>
                <div class="col-sm-10">
                    <textarea required class="form-control" name="alamat" id="alamat" rows="3"><?php echo $alamat; ?></textarea>
                </div>
            </div>

            <div class="mb-3 row mt-4">
                <div class="col">
                    <?php if(isset($_GET['ubah'])){ ?>
                        <button type="submit" name="aksi" value="edit" class="btn btn-primary">
                            <i class="fa fa-floppy-disk"></i> Simpan
                        </button>
                    <?php } else { ?>
                        <button type="submit" name="aksi" value="add" class="btn btn-primary">
                            <i class="fa fa-plus" style="-webkit-text-stroke: 1px white;"></i> Tambahkan
                        </button>
                    <?php } ?>
                    <a href="index.php" type="button" class="btn btn-danger">
                        <i class="fa fa-reply"></i> Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>