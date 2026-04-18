<?php
/**
 * index.php — Public Landing Page
 * X-RPL 2 Class Portal | SMKN 1 Majalengka
 */

date_default_timezone_set('Asia/Jakarta');
$nama_kelas = "XI-RPL 2";
$jurusan = "Rekayasa Perangkat Lunak";
$kelas = "XI";
require_once __DIR__ . '/config/database.php';

// ── Fetch data ────────────────────────────────────────────────────────────────

// Officials (non-Anggota)
$stmtOfficials = $pdo->query(
    "SELECT * FROM siswa WHERE jabatan != 'Anggota' ORDER BY FIELD(jabatan,'Ketua Kelas','Sekretaris','Bendahara') LIMIT 10"
);
$officials = $stmtOfficials->fetchAll();

// All students
$stmtSiswa = $pdo->query("SELECT * FROM siswa ORDER BY nama");
$allSiswa  = $stmtSiswa->fetchAll();

// Gallery (latest 9)
$stmtGaleri = $pdo->query("SELECT * FROM galeri ORDER BY tgl_upload DESC LIMIT 9");
$galeri     = $stmtGaleri->fetchAll();

// Schedule (all)
$stmtJadwal = $pdo->query("SELECT * FROM jadwal ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), jam_mulai");
$jadwalAll  = $stmtJadwal->fetchAll();

// Group schedule by day
$hariList    = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$jadwalByDay = [];
foreach ($hariList as $h) { $jadwalByDay[$h] = []; }
foreach ($jadwalAll as $j) { $jadwalByDay[$j['hari']][] = $j; }

// Stats
$totalSiswa = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$totalGaleri = $pdo->query("SELECT COUNT(*) FROM galeri")->fetchColumn();
$totalJadwal = $pdo->query("SELECT COUNT(DISTINCT hari) FROM jadwal")->fetchColumn();

// Today's day in Indonesian
$hariIndo = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa',
             'Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$todayEng = date('l');
$todayHari = $hariIndo[$todayEng] ?? '';
$currentTime = date('H:i');

// Helper: get photo URL or null
function getPhotoUrl(?string $foto): ?string {
    if ($foto && file_exists(__DIR__ . '/assets/img/siswa/' . $foto)) {
        return 'assets/img/siswa/' . htmlspecialchars($foto);
    }
    return null;
}

function getGaleriUrl(string $file): string {
    return 'assets/img/galeri/' . htmlspecialchars($file);
}

// Helper: initials from name
function initials(string $name): string {
    $parts = explode(' ', trim($name));
    $init  = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $init .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $init;
}

// Check if schedule row is currently active
function isCurrentSlot(string $hari, string $todayHari, string $mulai, string $selesai, string $now): bool {
    return $hari === $todayHari && $now >= $mulai && $now <= $selesai;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Website Class <?= $nama_kelas ?> SMKN 1 Majalengka — Informasi siswa, jadwal, dan galeri kelas.">
  <title><?= $nama_kelas ?> | SMKN 1 Majalengka</title>

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- FontAwesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">

  <style>
    /* #mainNav {
      background: rgba(18, 18, 18, 0.7) !important; 
      backdrop-filter: blur(12px) saturate(150%); 
      -webkit-backdrop-filter: blur(12px) saturate(150%);
      border-bottom: 1px solid rgba(255, 215, 0, 0.2) !important; 
    } */

    /* 2. FIX TEKS HARI AKTIF JADI PUTIH */
    .day-tab.active, .day-tab.today-tab {
      color: #ffffff !important; 
      background: var(--accent) !important;
    }
    
    #hero { background-attachment: fixed; }
  </style>

</head>

<body>
<!-- ──────────────────────────────────────────────────────────────────────────
     NAVBAR
────────────────────────────────────────────────────────────────────────── -->
<nav id="mainNav" class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand nav-brand" href="#hero">
      <span>XI-RPL<span class="brand-accent"> 2</span></span>
      <span class="brand-badge">SMKN 1 MJL</span>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            style="color:var(--text-primary);">
      <i class="fas fa-bars"></i>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto me-3 align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="#hero">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="#organisasi">Organisasi</a></li>
        <li class="nav-item"><a class="nav-link" href="#siswa">Siswa</a></li>
        <li class="nav-item"><a class="nav-link" href="#galeri">Galeri</a></li>
        <li class="nav-item"><a class="nav-link" href="#jadwal">Jadwal</a></li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <button id="themeToggle" aria-label="Toggle theme">
          <i class="fas fa-sun"></i>
        </button>
        <a href="login.php" class="btn-accent" style="padding:.5rem 1.2rem; font-size:.85rem;">
          <i class="fas fa-lock"></i> Admin
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- ──────────────────────────────────────────────────────────────────────────
     HERO
────────────────────────────────────────────────────────────────────────── -->
<section id="hero">
  <div class="hero-grid-bg"></div>
  <div class="hero-glow"></div>

  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <div class="hero-tag">
          <span>Kelas Aktif</span>
          <span>Tahun Ajaran 2025/2026</span>
        </div>

        <h1 class="hero-title animate-fade-up">
          Kelas <span class="line-accent"><?= $nama_kelas ?></span>
          SMKN 1 Majalengka
        </h1>

        <p class="hero-desc animate-fade-up delay-1">
          Website class <?= $kelas ?> <?= $jurusan ?> 2.
          Temukan informasi siswa, jadwal pelajaran, galeri kegiatan,
          dan struktur organisasi kelas kami.
        </p>

        <div class="d-flex flex-wrap gap-3 animate-fade-up delay-2">
          <a href="#siswa" class="btn-accent">
            <i class="fas fa-users"></i> Data Siswa
          </a>
          <a href="#jadwal" class="btn-outline">
            <i class="fas fa-calendar-alt"></i> Lihat Jadwal
          </a>
        </div>

        <div class="hero-stats animate-fade-up delay-3">
          <div class="hero-stat-item">
            <div class="hero-stat-num"><?= $totalSiswa ?></div>
            <div class="hero-stat-label">Total Siswa</div>
          </div>
          <div class="hero-stat-item">
            <div class="hero-stat-num"><?= $totalJadwal ?></div>
            <div class="hero-stat-label">Hari Belajar</div>
          </div>
          <div class="hero-stat-item">
            <div class="hero-stat-num"><?= $totalGaleri ?></div>
            <div class="hero-stat-label">Foto Galeri</div>
          </div>
        </div>
      </div>

      <div class="col-lg-5 animate-fade-up delay-2">
        <div class="hero-visual">
          <div class="hero-card-box">
            <div class="hero-card-title">Information</div>

            <div class="info-row">
              <span class="info-label">Program Keahlian</span>
              <span class="info-value accent"><?= $jurusan ?></span>
            </div>
            <div class="info-row">
              <span class="info-label">Tingkat</span>
              <span class="info-value">Kelas <?= $kelas ?></span>
            </div>
            <div class="info-row">
              <span class="info-label">Sekolah</span>
              <span class="info-value">SMKN 1 Majalengka</span>
            </div>
            <div class="info-row">
              <span class="info-label">Tahun Ajaran</span>
              <span class="info-value">2025 / 2026</span>
            </div>
            <div class="info-row">
              <span class="info-label">Hari Ini</span>
              <span class="info-value accent">
                <?= $todayHari ?: 'Libur' ?>, <?= date('d M Y') ?>
              </span>
            </div>
            <div class="info-row">
              <span class="info-label">Waktu Server</span>
              <span class="info-value mono"><?= date('H:i:s') ?> WIB</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ──────────────────────────────────────────────────────────────────────────
     ORGANISASI (Officials)
────────────────────────────────────────────────────────────────────────── -->
<section id="organisasi" class="section-pad">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Struktur Organisasi</span>
      <h2 class="section-title">Pengurus <span>Kelas</span></h2>
      <div class="divider-accent"></div>
      <p class="section-subtitle">
        Jajaran pengurus kelas <?= $nama_kelas ?> yang bertanggung jawab dalam
        pengelolaan dan koordinasi kegiatan kelas.
      </p>
    </div>

    <?php if (empty($officials)): ?>
      <div class="text-center" style="color:var(--text-muted); padding:3rem 0;">
        <i class="fas fa-users fa-3x mb-3" style="color:var(--border);"></i>
        <p>Data pengurus belum tersedia.</p>
      </div>
    <?php else: ?>
      <div class="row g-4 justify-content-center">
        <?php foreach ($officials as $idx => $off):
          $photoUrl = getPhotoUrl($off['foto']);
          $isPrimary = $off['jabatan'] === 'Ketua Kelas';
        ?>
          <div class="col-lg-<?= $isPrimary ? '4' : '3' ?> col-md-6 col-sm-6 reveal" style="animation-delay:<?= $idx * 0.1 ?>s">
            <div class="official-card <?= $isPrimary ? 'primary-card' : '' ?>">
              <?php if ($photoUrl): ?>
                <img src="<?= $photoUrl ?>" alt="<?= htmlspecialchars($off['nama']) ?>"
                     class="official-avatar" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid var(--border);margin:0 auto 1.25rem;">
              <?php else: ?>
                <div class="official-avatar-placeholder">
                  <?= initials($off['nama']) ?>
                </div>
              <?php endif; ?>

              <span class="official-jabatan"><?= htmlspecialchars($off['jabatan']) ?></span>
              <div class="official-name"><?= htmlspecialchars($off['nama']) ?></div>
              <div class="official-nisn mono">NISN: <?= htmlspecialchars($off['nisn']) ?></div>

              <?php if ($off['instagram_user']): ?>
                <a href="https://instagram.com/<?= htmlspecialchars($off['instagram_user']) ?>"
                   target="_blank" rel="noopener" class="official-instagram">
                  <i class="fab fa-instagram"></i>
                  @<?= htmlspecialchars($off['instagram_user']) ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ──────────────────────────────────────────────────────────────────────────
     SISWA (All Students)
────────────────────────────────────────────────────────────────────────── -->
<section id="siswa" class="section-pad">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Data Siswa</span>
      <h2 class="section-title">Seluruh <span>Anggota Kelas</span></h2>
      <div class="divider-accent"></div>
      <p class="section-subtitle">
        Daftar lengkap seluruh siswa kelas <?= $nama_kelas ?>. Gunakan pencarian
        atau filter jabatan untuk menemukan siswa tertentu.
      </p>
    </div>

    <!-- Search & Filter -->
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-start align-items-md-center mb-4 reveal">
      <div class="search-box-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" id="studentSearch" class="search-box" placeholder="Cari nama siswa...">
      </div>
      <div class="filter-btn-group">
        <button class="filter-btn active" data-filter="all">Semua</button>
        <button class="filter-btn" data-filter="Ketua Kelas">Ketua Kelas</button>
        <button class="filter-btn" data-filter="Sekretaris">Sekretaris</button>
        <button class="filter-btn" data-filter="Bendahara">Bendahara</button>
        <button class="filter-btn" data-filter="Anggota">Anggota</button>
      </div>
    </div>

    <?php if (empty($allSiswa)): ?>
      <div class="text-center" style="color:var(--text-muted); padding:3rem 0;">
        <i class="fas fa-user-slash fa-3x mb-3" style="color:var(--border);"></i>
        <p>Data siswa belum tersedia.</p>
      </div>
    <?php else: ?>
      <div class="row g-3" id="studentGrid">
        <?php foreach ($allSiswa as $s):
          $photoUrl = getPhotoUrl($s['foto']);
        ?>
          <div class="col-xl-3 col-lg-4 col-md-6 student-card-wrap reveal"
               data-name="<?= strtolower($s['nama']) ?>"
               data-jabatan="<?= htmlspecialchars($s['jabatan']) ?>">
            <div class="student-card">
              <!-- Photo or placeholder -->
              <?php if ($photoUrl): ?>
                <img src="<?= $photoUrl ?>" alt="<?= htmlspecialchars($s['nama']) ?>" class="student-card-img" style="height:180px;object-fit:cover;width:100%;">
              <?php else: ?>
                <div class="student-card-img-placeholder">
                  <?= initials($s['nama']) ?>
                </div>
              <?php endif; ?>

              <div class="student-card-body">
                <span class="student-badge"><?= htmlspecialchars($s['jabatan']) ?></span>
                <div class="student-name"><?= htmlspecialchars($s['nama']) ?></div>
                <div class="student-nisn">NISN: <?= htmlspecialchars($s['nisn']) ?></div>
              </div>

              <?php if ($s['instagram_user']): ?>
                <div class="student-card-footer">
                  <a href="https://instagram.com/<?= htmlspecialchars($s['instagram_user']) ?>"
                     target="_blank" rel="noopener" class="ig-link">
                    <i class="fab fa-instagram"></i>
                    @<?= htmlspecialchars($s['instagram_user']) ?>
                  </a>
                </div>
              <?php else: ?>
                <div class="student-card-footer">
                  <span style="font-size:.75rem;color:var(--text-muted);">
                    <i class="fas fa-minus-circle me-1"></i>No Instagram
                  </span>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ──────────────────────────────────────────────────────────────────────────
     GALERI
────────────────────────────────────────────────────────────────────────── -->
<section id="galeri" class="section-pad">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Galeri Kelas</span>
      <h2 class="section-title">Dokumentasi <span>Kegiatan</span></h2>
      <div class="divider-accent"></div>
      <p class="section-subtitle">
        Koleksi foto kegiatan dan momen bersama kelas <?= $nama_kelas ?>.
        Klik foto untuk melihat ukuran penuh.
      </p>
    </div>

    <?php if (empty($galeri)): ?>
      <div class="text-center" style="color:var(--text-muted); padding:3rem 0;">
        <i class="fas fa-images fa-3x mb-3" style="color:var(--border);"></i>
        <p>Galeri foto belum tersedia.</p>
      </div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($galeri as $idx => $g):
          $tglFormatted = date('d M Y', strtotime($g['tgl_upload']));
          $fileUrl = getGaleriUrl($g['file_foto']);
        ?>
          <div class="col-lg-4 col-md-6 reveal" style="animation-delay:<?= ($idx % 3) * 0.1 ?>s">
            <div class="gallery-item"
                 data-src="<?= $fileUrl ?>"
                 data-title="<?= htmlspecialchars($g['judul']) ?>"
                 data-date="<?= $tglFormatted ?>">
              <img src="<?= $fileUrl ?>" alt="<?= htmlspecialchars($g['judul']) ?>" loading="lazy">
              <div class="gallery-overlay">
                <div class="gallery-overlay-title"><?= htmlspecialchars($g['judul']) ?></div>
                <div class="gallery-overlay-date"><?= $tglFormatted ?></div>
              </div>
              <div class="gallery-zoom-icon">
                <i class="fas fa-expand-alt"></i>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="lightbox-overlay" role="dialog" aria-modal="true">
  <div class="lightbox-content">
    <button id="lightboxClose" class="lightbox-close" aria-label="Tutup">
      <i class="fas fa-times"></i>
    </button>
    <img id="lightboxImg" src="" alt="Galeri">
    <div class="lightbox-caption" id="lightboxTitle"></div>
    <div class="lightbox-date mono" id="lightboxDate"></div>
  </div>
</div>

<!-- ──────────────────────────────────────────────────────────────────────────
     JADWAL
────────────────────────────────────────────────────────────────────────── -->
<section id="jadwal" class="section-pad">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Jadwal Pelajaran</span>
      <h2 class="section-title">Jadwal <span>Harian</span></h2>
      <div class="divider-accent"></div>
      <p class="section-subtitle">
        Jadwal pelajaran kelas <?= $nama_kelas ?> per hari.
        Jadwal hari ini ditampilkan otomatis dengan highlight mata pelajaran yang sedang berlangsung.
      </p>
    </div>

    <div class="reveal">
      <!-- Day Tabs -->
      <div class="day-tabs">
        <?php foreach ($hariList as $hari):
          $isToday = ($hari === $todayHari);
        ?>
          <button class="day-tab <?= $isToday ? 'today-tab' : '' ?>" data-hari="<?= $hari ?>">
            <?= $hari ?>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Day Panels -->
      <?php foreach ($hariList as $hari): ?>
        <div class="day-panel" data-hari="<?= $hari ?>">
          <?php if (empty($jadwalByDay[$hari])): ?>
            <div style="color:var(--text-muted); text-align:center; padding:3rem 0;">
              <i class="fas fa-coffee fa-2x mb-2" style="color:var(--border);"></i>
              <p>Tidak ada jadwal untuk hari <?= $hari ?>.</p>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="schedule-table">
                <thead>
                  <tr>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:var(--text-muted); letter-spacing:.1em; text-transform:uppercase; padding-bottom:.75rem; padding-left:1.25rem;">Waktu</td>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:var(--text-muted); letter-spacing:.1em; text-transform:uppercase; padding-bottom:.75rem;">Mata Pelajaran</td>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:var(--text-muted); letter-spacing:.1em; text-transform:uppercase; padding-bottom:.75rem;">Guru</td>
                    <td style="font-family:'Share Tech Mono',monospace; font-size:.7rem; color:var(--text-muted); letter-spacing:.1em; text-transform:uppercase; padding-bottom:.75rem; text-align:center;">Status</td>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($jadwalByDay[$hari] as $jadwal):
                    $isActive = isCurrentSlot($hari, $todayHari, $jadwal['jam_mulai'], $jadwal['jam_selesai'], $currentTime);
                    $mulaiFormatted   = date('H:i', strtotime($jadwal['jam_mulai']));
                    $selesaiFormatted = date('H:i', strtotime($jadwal['jam_selesai']));
                  ?>
                    <tr class="schedule-row <?= $isActive ? 'active-now' : '' ?>">
                      <td>
                        <span class="time-badge">
                          <?= $mulaiFormatted ?> - <?= $selesaiFormatted ?>
                        </span>
                      </td>
                      <td>
                        <div class="mapel-name"><?= htmlspecialchars($jadwal['mapel']) ?></div>
                      </td>
                      <td>
                        <div class="guru-name"><?= htmlspecialchars($jadwal['guru']) ?></div>
                      </td>
                      <td style="text-align:center;">
                        <?php if ($isActive): ?>
                          <span class="now-badge">
                            <span style="width:6px;height:6px;background:var(--text-on-accent);border-radius:50%;animation:blink 1s infinite;"></span>
                            Sekarang
                          </span>
                        <?php else: ?>
                          <span style="font-size:.75rem; color:var(--text-muted);">—</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ──────────────────────────────────────────────────────────────────────────
     FOOTER
────────────────────────────────────────────────────────────────────────── -->
<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand"><?= $kelas ?>-RPL<span> 2</span></div>
        <p class="footer-text">
          Website class <?= $kelas ?>  <?= $jurusan ?> 2.<br>
          SMKN 1 Majalengka — Tahun Ajaran 2025/2026.
        </p>
      </div>

      <div class="col-lg-2 col-md-4">
        <div style="font-family:'Share Tech Mono',monospace; font-size:.7rem; letter-spacing:.15em; color:var(--text-muted); text-transform:uppercase; margin-bottom:1rem;">Navigasi</div>
        <ul class="footer-link-list">
          <li><a href="#hero"><i class="fas fa-chevron-right fa-xs"></i> Beranda</a></li>
          <li><a href="#organisasi"><i class="fas fa-chevron-right fa-xs"></i> Organisasi</a></li>
          <li><a href="#siswa"><i class="fas fa-chevron-right fa-xs"></i> Siswa</a></li>
          <li><a href="#galeri"><i class="fas fa-chevron-right fa-xs"></i> Galeri</a></li>
          <li><a href="#jadwal"><i class="fas fa-chevron-right fa-xs"></i> Jadwal</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-4">
        <div style="font-family:'Share Tech Mono',monospace; font-size:.7rem; letter-spacing:.15em; color:var(--text-muted); text-transform:uppercase; margin-bottom:1rem;">Informasi</div>
        <ul class="footer-link-list">
          <li><a href="https://www.google.com/maps/search/SMKN+1+Majalengka" target="_blank"><i class="fas fa-map-marker-alt fa-xs"></i> Majalengka, Jawa Barat</a></li>
          <li><a href="https://smkn1majalengka.sch.id" target="_blank" rel="noopener"><i class="fas fa-globe fa-xs"></i> smkn1majalengka.sch.id</a></li>
          <li><a href="login.php"><i class="fas fa-lock fa-xs"></i> Admin Login</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-4">
        <div style="font-family:'Share Tech Mono',monospace; font-size:.7rem; letter-spacing:.15em; color:var(--text-muted); text-transform:uppercase; margin-bottom:1rem;">Program Keahlian</div>
        <p class="footer-text" style="font-size:.85rem;">
          Rekayasa Perangkat Lunak (RPL) adalah program keahlian yang
          berfokus pada pengembangan perangkat lunak, basis data, dan
          kinerja sebuah program website.
        </p>
      </div>
    </div>

    <div class="footer-bottom">
      <span class="footer-copy">
        &copy; <?= date('Y') ?> <?= $nama_kelas ?> SMKN 1 Majalengka. All rights reserved.
      </span>
      <span class="footer-copy mono">
        <?= date('d/m/Y H:i') ?> WIB
      </span>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>
</body>
</html>
