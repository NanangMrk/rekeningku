<?php
/**
 * RekeningKu - Admin Login Portal
 * Secure authentication with password_verify and CSRF protection
 */

declare(strict_types=1);

// Canonical trailing slash redirect for /admin or /rekeningku/admin
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if ($path && substr($path, -1) !== '/' && substr($path, -4) !== '.php') {
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: ' . $path . '/' . ($qs ? '?' . $qs : ''), true, 301);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$adminBase = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/admin'), '/\\') . '/';

// If already logged in, redirect straight to dashboard
if (is_admin_logged_in()) {
    redirect($adminBase . 'dashboard.php');
}

$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!verify_csrf_token($token)) {
        $errorMessage = 'Sesi keamanan kadaluarsa. Silakan refresh dan coba lagi.';
    } elseif (empty($username) || empty($password)) {
        $errorMessage = 'Silakan masukkan username dan password.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = :username LIMIT 1');
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                login_admin($admin);
                set_flash_message('success', 'Selamat datang kembali di panel admin!');
                redirect($adminBase . 'dashboard.php');
            } else {
                $errorMessage = 'Username atau password yang Anda masukkan salah.';
            }
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $errorMessage = 'Terjadi kesalahan sistem. Silakan coba kembali.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - RekeningKu</title>

    <!-- Google Fonts: Manrope, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9ff 0%, #eff4ff 100%);
            padding: 24px 16px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            box-shadow: 0 12px 35px -5px rgba(15, 23, 42, 0.08);
            padding: 36px 32px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-logo {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary) 0%, #1e3a8a 100%);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 14px rgba(0, 74, 198, 0.3);
            margin-bottom: 16px;
        }

        .login-title {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.01em;
        }

        .login-desc {
            font-size: 13.5px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .input-with-icon {
            padding-left: 38px;
            padding-right: 42px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            margin-top: 10px;
        }
    </style>
</head>
<body class="login-page">

    <div class="login-box">
        <div class="login-header">
            <div class="login-logo">
                <i class="fa-solid fa-vault"></i>
            </div>
            <h1 class="login-title">Admin RekeningKu</h1>
            <p class="login-desc">Masuk untuk mengelola daftar rekening Anda</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-error" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= e($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-group">
                    <i class="fa-solid fa-user input-icon"></i>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control input-with-icon" 
                           placeholder="Masukkan username" 
                           required 
                           autocomplete="off"
                           autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-password-wrapper">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control input-with-icon" 
                           placeholder="Masukkan password" 
                           required 
                           autocomplete="current-password">
                    <button type="button" class="btn-toggle-password" id="togglePasswordBtn" aria-label="Lihat password" title="Tampilkan / Sembunyikan Password">
                        <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary btn-login">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk ke Dashboard</span>
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleIcon.className = isPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
            });
        }
    });
    </script>
</body>
</html>
