<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/database.php';

// Ambil statistik ringkas
$totalSiswa = $pdo->query("SELECT COUNT(*) FROM siswa")->fetchColumn();
$totalGaleri = $pdo->query("SELECT COUNT(*) FROM galeri")->fetchColumn();
$totalAdmin = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | X-RPL 2</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --bg-dark: #0a0a0c;
            --card-bg: #16161a;
            --accent-color: #FFD700; /* Gold/Warning */
            --neon-glow: rgba(255, 215, 0, 0.3);
            --text-muted: #a0a0a0;
        }

        body {
            background-color: var(--bg-dark);
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            background: var(--card-bg);
            border-right: 1px solid rgba(255, 215, 0, 0.1);
            transition: all 0.3s ease;
        }

        .nav-link {
            color: var(--text-muted);
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 215, 0, 0.1);
            color: var(--accent-color);
            box-shadow: inset 4px 0 0 var(--accent-color);
        }

        .nav-link i {
            width: 25px;
            font-size: 1.1rem;
        }

        /* Card Styling */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 25px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent-color);
            box-shadow: 0 10px 30px var(--neon-glow);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            background: var(--accent-color);
            filter: blur(80px);
            opacity: 0.1;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
            margin-bottom: 20px;
        }

        .display-number {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            letter-spacing: -2px;
        }

        /* Welcome Section */
        .welcome-banner {
            background: linear-gradient(90deg, #16161a 0%, #1c1c22 100%);
            padding: 30px;
            border-radius: 20px;
            border-left: 5px solid var(--accent-color);
            margin-bottom: 40px;
        }

        .glass-alert {
            background: rgba(13, 202, 240, 0.05);
            border: 1px solid rgba(13, 202, 240, 0.2);
            color: #0dcaf0;
            backdrop-filter: blur(10px);
            border-radius: 15px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-up {
            animation: fadeIn 0.6s ease forwards;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar vh-100 p-4 fixed-top">
            <div class="d-flex align-items-center mb-5">
                <div class="bg-warning rounded-circle p-2 me-2">
                    <i class="fas fa-code text-dark"></i>
                </div>
                <h5 class="text-warning fw-bold m-0" style="letter-spacing: 1px;">X-RPL 2</h5>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fas fa-th-large"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-user-graduate"></i> Data Siswa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-camera-retro"></i> Galeri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-cog"></i> Settings</a>
                </li>
                <li class="mt-5">
                    <p class="small fw-bold px-3" style="color: #e0e0e0;">System</p>
                    <a class="nav-link text-danger" href="logout.php"><i class="fas fa-power-off"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-5 py-4">
            
            <div class="welcome-banner animate-up">
                <span class="badge bg-warning text-dark mb-2">Verified Admin</span>
                <h1 class="fw-bold">Hi, <?= htmlspecialchars($_SESSION['admin_name']) ?>! 👋</h1>
                <p class="m-0" style="color: #e0e0e0;">Inilah statistik portal kelas kamu hari ini.</p>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4 animate-up" style="animation-delay: 0.1s;">
                    <div class="stat-card">
                        <div class="icon-box">
                            <i class="fas fa-user-friends fa-lg"></i>
                        </div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #e0e0e0;">Total Siswa</p>
                        <h2 class="display-number m-0"><?= number_format($totalSiswa) ?></h2>
                    </div>
                </div>

                <div class="col-md-4 mb-4 animate-up" style="animation-delay: 0.2s;">
                    <div class="stat-card">
                        <div class="icon-box">
                            <i class="fas fa-images fa-lg"></i>
                        </div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #e0e0e0;">Foto Galeri</p>
                        <h2 class="display-number m-0"><?= number_format($totalGaleri) ?></h2>
                    </div>
                </div>

                <div class="col-md-4 mb-4 animate-up" style="animation-delay: 0.3s;">
                    <div class="stat-card">
                        <div class="icon-box">
                            <i class="fas fa-shield-alt fa-lg"></i>
                        </div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #e0e0e0;">Administrator</p>
                        <h2 class="display-number m-0"><?= number_format($totalAdmin) ?></h2>
                    </div>
                </div>
            </div>

            <div class="alert glass-alert mt-4 d-flex align-items-center animate-up" style="animation-delay: 0.4s;">
                <i class="fas fa-server me-3"></i>
                <div>
                    <strong>System Status:</strong> Terhubung ke database <code>webdb_rpl2</code> | SMKN 1 Majalengka
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>