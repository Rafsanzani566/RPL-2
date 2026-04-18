<?php 
    include 'koneksi.php';
    session_start();

    $query = "SELECT * FROM siswa;";
    $sql = mysqli_query($connect, $query);
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

    <title>Dashboard Admin</title>
</head>

<body class="bg-light">
    <nav class="navbar navbar-light bg-white mb-4 shadow-sm">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Panel</a>
      </div>
    </nav>

    <div class="container">
        <h1>Data Siswa</h1>
        <figure>
          <blockquote class="blockquote">
            <p>Berisi data yang telah disimpan di database.</p>
          </blockquote>
          <figcaption class="blockquote-footer">
            CRUD <cite title="Source Title">Create Read Update Delete</cite>
          </figcaption>
        </figure>
        
        <a href="kelola.php" type="button" class="btn btn-primary mb-3">
            <i class="fa fa-plus" style="-webkit-text-stroke: 1px white;"></i> Tambah Data
        </a>

        <?php if(isset($_SESSION['eksekusi'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><?php echo $_SESSION['eksekusi']; ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php 
            session_destroy();
            endif; 
        ?>

        <div class="table-responsive card p-3 shadow-sm">
            <table id="dt" class="table align-middle table-bordered table-striped hover">
                <thead>
                    <tr>
                        <th><center>No.</center></th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Foto</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($result = mysqli_fetch_assoc($sql)){ ?>
                    <tr>
                        <td><center><?php echo ++$no; ?>.</center></td>
                        <td><?php echo $result['nisn']; ?></td>
                        <td><?php echo $result['nama']; ?></td>
                        <td><?php echo $result['jenis_kelamin']; ?></td>
                        <td>
                            <center><img src="img/<?php echo $result['foto_siswa']; ?>" style="width: 50px; border-radius: 5px;"></center>
                        </td>
                        <td><?php echo $result['alamat']; ?></td>
                        <td>
                            <a href="kelola.php?ubah=<?php echo $result['id_siswa']; ?>" type="button" class="btn btn-success btn-sm">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a href="proses.php?hapus=<?php echo $result['id_siswa']; ?>" type="button" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus data tersebut???')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
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