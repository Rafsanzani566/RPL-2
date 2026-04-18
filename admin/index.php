<?php 
    require_once '../config/database.php'; // Pastikan path-nya benar
    session_start();

    $pdo = getPDO(); 

    // --- KODE STATISTIK MULAI DI SINI ---
    // 1. Ambil total semua siswa
    $stmtTotal = $pdo->query("SELECT COUNT(*) as total FROM siswa");
    $totalSiswa = $stmtTotal->fetch()['total'];

    // 2. Ambil jumlah berdasarkan jabatan
    $stmtJabatan = $pdo->query("SELECT jabatan, COUNT(*) as jumlah FROM siswa GROUP BY jabatan");
    $dataJabatan = $stmtJabatan->fetchAll();
    // --- KODE STATISTIK SELESAI ---

    $query = "SELECT * FROM siswa";
    $stmt = $pdo->query($query);
    $no = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">

    <title>Dashboard Admin - XI RPL 2</title>
</head>

<body class="bg-light">
    <nav class="navbar navbar-light bg-white mb-4 shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Panel</a>
      </div>
    </nav>

    <div class="container">
    <h1 class="mt-4">Data Siswa</h1>
    <figure class="mb-4">
      <blockquote class="blockquote">
        <p style="color: #b0b0b0; font-size: 0.9rem;">Sistem monitoring aktivitas XI RPL 2 sedang aktif.</p>
      </blockquote>
      <figcaption class="blockquote-footer">
        Grimoire Digital <cite title="Source Title">XI RPL 2</cite>
      </figcaption>
    </figure>

    <div class="row mb-2">
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white border-0 shadow-sm" style="border-left: 4px solid #00d4ff; background: #1a1a1a;">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1" style="font-size: 0.7rem; color: #888; letter-spacing: 1px;">Total Siswa/Siswi</h6>
                    <h3 class="mb-0 fw-bold">
                        <?= $totalSiswa ?> <small style="font-size: 0.8rem; color: #00d4ff;">Siswa</small>
                    </h3>
                </div>
            </div>
        </div>

        <?php foreach($dataJabatan as $jbt): ?>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-white border-0 shadow-sm" style="border-left: 4px solid #9d50bb; background: #1a1a1a;">
                <div class="card-body">
                    <h6 class="text-uppercase mb-1" style="font-size: 0.7rem; color: #888; letter-spacing: 1px;">
                        <?= $jbt['jabatan'] ?>
                    </h6>
                    <h3 class="mb-0 fw-bold"><?= $jbt['jumlah'] ?></h3>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <a href="kelola.php" type="button" class="btn btn-primary mb-3">
        <i class="fa fa-plus"></i> Tambah Data
    </a>

        <?php if(isset($_SESSION['eksekusi'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><?php echo $_SESSION['eksekusi']; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php 
            // Hapus session setelah ditampilkan agar tidak muncul terus saat refresh
            unset($_SESSION['eksekusi']);
            endif; 
        ?>

        <div class="table-responsive card p-3 shadow-sm">
            <table id="dt" class="table align-middle table-bordered table-striped hover">
                <thead>
                    <tr>
                        <th><center>No.</center></th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Jabatan</th>
                        <th>Foto</th>
                        <th>Instagram</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Looping data menggunakan PDO fetch
                    while($result = $stmt->fetch(PDO::FETCH_ASSOC)): 
                    ?>
                    <tr>
                        <td><center><?php echo ++$no; ?>.</center></td>
                        <td><?php echo $result['nisn']; ?></td>
                        <td><?php echo $result['nama']; ?></td>
                        <td><?php echo $result['jabatan']; ?></td>
                        <td>
                            <center>
                                <?php if($result['foto']): ?>
                                    <img src="img/<?php echo $result['foto']; ?>" style="width: 50px; border-radius: 5px;">
                                <?php else: ?>
                                    <span class="badge bg-secondary">No Photo</span>
                                <?php endif; ?>
                            </center>
                        </td>
                        <td>
                            <?php if($result['instagram_user']): ?>
                                <a href="https://instagram.com/<?php echo $result['instagram_user']; ?>" target="_blank" class="text-decoration-none">
                                    @<?php echo $result['instagram_user']; ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="kelola.php?ubah=<?php echo $result['id']; ?>" type="button" class="btn btn-success btn-sm">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="proses.php?hapus=<?php echo $result['id']; ?>" type="button" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus data tersebut?')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#dt').DataTable({
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
            });
        });
    </script>
</body>
</html>