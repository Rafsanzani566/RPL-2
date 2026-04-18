<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE BINARY username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id']   = $user['id'];
                    $_SESSION['admin_name'] = $user['full_name'];
                    header('Location: admin/dashboard.php');
                    exit;
                } else {
                    $error = 'Password salah, cek huruf besar kecilnya!';
                }
            } else {
                $error = 'Username tidak ditemukan!';
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = 'Isi dulu username dan passwordnya!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | X-RPL 2</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background-color: var(--bg-root);
            font-family: 'Barlow', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Efek Ornamen Background */
        body::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--accent);
            filter: blur(150px);
            opacity: 0.05;
            top: -100px;
            right: -100px;
            z-index: -1;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            background: var(--bg-card);
            border: 1px solid rgba(255, 215, 0, 0.1);
            border-top: 4px solid var(--accent);
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            animation: fadeInUp 0.8s ease;
        }

        .login-header h3 {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--accent);
            text-transform: uppercase;
        }

        /* 1. Ubah warna teks label (USERNAME & PASSWORD) jadi putih terang */
        .form-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            color: #ffffff !important; /* Pakai !important agar menimpa text-muted */
            letter-spacing: 1px;
            margin-bottom: 8px;
            opacity: 0.9;
        }

        /* 2. Ubah warna ikon di sebelah kiri input jadi putih */
        .input-group-text {
            color: #ffffff !important;
            background: transparent;
        }

        /* 3. Ubah warna teks yang kita ketik di dalam kotak input jadi putih */
        .form-control {
            background-color: var(--bg-input) !important;
            border: 1px solid rgba(255,255,255,0.1); /* Border agak diperjelas */
            color: #ffffff !important; /* Warna teks ketikan */
            padding: 12px 15px;
            border-radius: 6px;
        }

        /* 4. Mengubah warna placeholder (teks bantuan "Input username...") jadi agak putih transparan */
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        /* 5. Pastikan saat difokus (diklik), teks tetap putih */
        .form-control:focus {
            color: #ffffff !important;
            border-color: var(--accent);
            background-color: #333 !important;
        }

        .btn-login {
            background: var(--accent);
            color: #000;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px;
            border: none;
            border-radius: 6px;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--accent-dim);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--accent-glow);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .back-link:hover {
            color: var(--accent);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header text-center mb-4">
            <div class="mb-3">
                <i class="fas fa-shield-halved fa-3x text-warning"></i>
            </div>
            <h3>Admin Portal</h3>
            <p class="small text-muted">X-RPL 2 SMKN 1 Majalengka</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger border-0 py-2 small animate-fade-up" style="background: rgba(220, 53, 69, 0.1); color: #ff6b6b;">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-uppercase">Identification / Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Input username..." required autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-uppercase">Security Key / Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Execute Login <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <a href="index.php" class="back-link">
            <i class="fas fa-chevron-left me-1"></i> Kembali ke Landing Page
        </a>
    </div>

</body>
</html>