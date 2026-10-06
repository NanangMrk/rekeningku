<?php
/**
 * RekeningKu - Admin Settings & Change Password
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pageTitle = 'Setting & Ganti Password';
$activeNav = 'settings';
$errors = [];

// Fetch current admin record
$adminId = $_SESSION['admin_id'] ?? null;
$adminUsername = $_SESSION['admin_username'] ?? 'admin';

if ($adminId) {
    $stmt = $pdo->prepare('SELECT id, username, password, created_at, updated_at FROM admins WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $adminId]);
    $admin = $stmt->fetch();
} else {
    $stmt = $pdo->prepare('SELECT id, username, password, created_at, updated_at FROM admins WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $adminUsername]);
    $admin = $stmt->fetch();
}

if (!$admin) {
    logout_admin();
    redirect('index.php');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $errors[] = 'Token keamanan tidak valid atau telah kadaluarsa. Silakan muat ulang halaman.';
    } else {
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        // Validation
        if ($currentPassword === '') {
            $errors[] = 'Password saat ini wajib diisi.';
        } elseif (!password_verify($currentPassword, $admin['password'])) {
            $errors[] = 'Password saat ini yang Anda masukkan salah.';
        }

        if ($newPassword === '') {
            $errors[] = 'Password baru wajib diisi.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Password baru minimal harus 6 karakter.';
        }

        if ($confirmPassword === '') {
            $errors[] = 'Konfirmasi password baru wajib diisi.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Konfirmasi password baru tidak sama dengan password baru.';
        }

        if (empty($errors)) {
            if (password_verify($newPassword, $admin['password'])) {
                $errors[] = 'Password baru tidak boleh sama dengan password saat ini. Silakan buat password yang berbeda.';
            }
        }

        if (empty($errors)) {
            try {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare('UPDATE admins SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
                $updateStmt->execute([
                    ':password' => $newPasswordHash,
                    ':id' => $admin['id']
                ]);

                set_flash_message('success', 'Password administrator berhasil diperbarui! Silakan gunakan password baru untuk login selanjutnya.');
                redirect('settings.php');
            } catch (PDOException $e) {
                error_log('Ganti password error: ' . $e->getMessage());
                $errors[] = 'Terjadi kesalahan sistem saat menyimpan password baru ke database.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-title-box">
    <div>
        <h1 class="page-title">Pengaturan Akun</h1>
        <p class="page-description">Kelola preferensi akun dan perbarui password akses administrator RekeningKu.</p>
    </div>
    <div>
        <a href="dashboard.php" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>
            <?php foreach ($errors as $err): ?>
                <div><?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="settings-grid">
    <!-- Main Column: Form Ganti Password -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">
                <i class="fa-solid fa-key" style="color: var(--primary);"></i>
                <span>Ganti Password Administrator</span>
            </h2>
        </div>
        <div class="panel-body">
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 22px;">
                Untuk menjaga keamanan akun Anda, masukkan password saat ini sebelum menentukan password baru.
            </p>

            <form method="POST" action="settings.php" id="formChangePassword">
                <?= csrf_field() ?>

                <!-- Current Password -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="current_password">
                        Password Saat Ini <span class="required">*</span>
                    </label>
                    <div class="input-password-wrapper">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               class="form-control" 
                               placeholder="Masukkan password saat ini" 
                               required 
                               autocomplete="current-password">
                        <button type="button" class="btn-toggle-password" data-target="current_password" aria-label="Lihat password" title="Tampilkan / Sembunyikan Password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="new_password">
                        Password Baru <span class="required">*</span>
                    </label>
                    <div class="input-password-wrapper">
                        <i class="fa-solid fa-shield-halved input-icon-left"></i>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-control" 
                               placeholder="Minimal 6 karakter" 
                               required 
                               autocomplete="new-password">
                        <button type="button" class="btn-toggle-password" data-target="new_password" aria-label="Lihat password" title="Tampilkan / Sembunyikan Password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>

                    <!-- Live Password Strength Meter -->
                    <div class="password-strength-container" id="strengthContainer" style="display: none;">
                        <div class="strength-meter-bars">
                            <div class="strength-meter-segment" id="seg1"></div>
                            <div class="strength-meter-segment" id="seg2"></div>
                            <div class="strength-meter-segment" id="seg3"></div>
                            <div class="strength-meter-segment" id="seg4"></div>
                        </div>
                        <div class="strength-feedback">
                            <span>Kekuatan Password: <strong class="strength-text" id="strengthText">Sangat Lemah</strong></span>
                            <span id="strengthHint">Gunakan huruf besar, angka & simbol</span>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" for="confirm_password">
                        Konfirmasi Password Baru <span class="required">*</span>
                    </label>
                    <div class="input-password-wrapper">
                        <i class="fa-solid fa-check-double input-icon-left"></i>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               class="form-control" 
                               placeholder="Ulangi password baru yang sama" 
                               required 
                               autocomplete="new-password">
                        <button type="button" class="btn-toggle-password" data-target="confirm_password" aria-label="Lihat password" title="Tampilkan / Sembunyikan Password">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-match-status" id="matchStatus" style="display: none;"></div>
                </div>

                <!-- Form Action Buttons -->
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 12px; padding-top: 20px; border-top: 1px solid var(--border-subtle);">
                    <a href="dashboard.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary" id="btnSubmit">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Simpan Password Baru</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Side Column: Info Akun & Tips Keamanan -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Profil Ringkas Admin -->
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fa-solid fa-user-shield" style="color: var(--primary);"></i>
                    <span>Informasi Akun</span>
                </h2>
            </div>
            <div class="panel-body">
                <div class="admin-profile-summary">
                    <div class="admin-avatar-lg">
                        <?= strtoupper(substr(e($admin['username'] ?? 'AD'), 0, 2)) ?>
                    </div>
                    <div class="admin-profile-details">
                        <h3><?= e($admin['username']) ?></h3>
                        <p>
                            <i class="fa-solid fa-circle" style="color: var(--success); font-size: 8px;"></i>
                            <span>Status: Administrator Aktif</span>
                        </p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Username</span>
                        <strong style="color: var(--text-primary); font-family: var(--font-mono);"><?= e($admin['username']) ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 8px; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Hak Akses</span>
                        <span class="badge badge-sapphire" style="font-size: 11px;">Super Admin</span>
                    </div>
                    <?php if (!empty($admin['updated_at'])): ?>
                    <div style="display: flex; justify-content: space-between; font-size: 12px;">
                        <span style="color: var(--text-muted);">Terakhir Diperbarui</span>
                        <span style="color: var(--text-secondary);"><?= e((string)$admin['updated_at']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Panduan & Tips Keamanan -->
        <div class="panel">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fa-solid fa-shield-check" style="color: var(--success);"></i>
                    <span>Tips Keamanan Password</span>
                </h2>
            </div>
            <div class="panel-body">
                <ul class="security-tips-list">
                    <li class="security-tip-item">
                        <div class="security-tip-icon">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <strong>Panjang Minimal</strong>
                            <p style="margin: 0; color: var(--text-muted);">Gunakan minimal 6 hingga 12 karakter atau lebih.</p>
                        </div>
                    </li>
                    <li class="security-tip-item">
                        <div class="security-tip-icon">
                            <i class="fa-solid fa-font"></i>
                        </div>
                        <div>
                            <strong>Kombinasi Karakter</strong>
                            <p style="margin: 0; color: var(--text-muted);">Kombinasikan huruf besar, huruf kecil, angka, dan simbol (misal: @, #, $, !).</p>
                        </div>
                    </li>
                    <li class="security-tip-item">
                        <div class="security-tip-icon">
                            <i class="fa-solid fa-user-lock"></i>
                        </div>
                        <div>
                            <strong>Jaga Kerahasiaan</strong>
                            <p style="margin: 0; color: var(--text-muted);">Jangan bagikan kata sandi kepada siapa pun dan hindari kata sandi umum seperti tanggal lahir.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Password Visibility Toggle
    const toggleButtons = document.querySelectorAll('.btn-toggle-password');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            if (!targetInput) return;

            const icon = btn.querySelector('i');
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                targetInput.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });

    // 2. Real-time Password Strength Meter
    const newPassInput = document.getElementById('new_password');
    const strengthContainer = document.getElementById('strengthContainer');
    const strengthText = document.getElementById('strengthText');
    const strengthHint = document.getElementById('strengthHint');
    const segments = [
        document.getElementById('seg1'),
        document.getElementById('seg2'),
        document.getElementById('seg3'),
        document.getElementById('seg4')
    ];

    if (newPassInput && strengthContainer) {
        newPassInput.addEventListener('input', () => {
            const val = newPassInput.value;
            if (!val) {
                strengthContainer.style.display = 'none';
                return;
            }

            strengthContainer.style.display = 'block';

            // Calculate strength score (0 to 4)
            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 9) score++;
            if (/[0-9]/.test(val) && /[a-zA-Z]/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            // Reset segment styles
            segments.forEach(seg => {
                seg.className = 'strength-meter-segment';
            });

            if (score <= 1) {
                segments[0].classList.add('active-weak');
                strengthText.textContent = 'Lemah';
                strengthText.style.color = 'var(--danger)';
                strengthHint.textContent = 'Minimal 6 karakter, tambahkan angka';
            } else if (score === 2) {
                segments[0].classList.add('active-medium');
                segments[1].classList.add('active-medium');
                strengthText.textContent = 'Sedang';
                strengthText.style.color = '#f59e0b';
                strengthHint.textContent = 'Tambahkan kombinasi simbol & huruf besar';
            } else if (score === 3) {
                segments[0].classList.add('active-strong');
                segments[1].classList.add('active-strong');
                segments[2].classList.add('active-strong');
                strengthText.textContent = 'Bagus';
                strengthText.style.color = 'var(--success)';
                strengthHint.textContent = 'Kombinasi password sudah kuat';
            } else {
                segments.forEach(seg => seg.classList.add('active-strong'));
                strengthText.textContent = 'Sangat Kuat';
                strengthText.style.color = 'var(--success)';
                strengthHint.textContent = 'Kombinasi password sangat optimal';
            }

            checkMatch();
        });
    }

    // 3. Confirm Password Match Checker
    const confirmPassInput = document.getElementById('confirm_password');
    const matchStatus = document.getElementById('matchStatus');

    function checkMatch() {
        if (!confirmPassInput || !matchStatus || !newPassInput) return;
        const p1 = newPassInput.value;
        const p2 = confirmPassInput.value;

        if (!p2) {
            matchStatus.style.display = 'none';
            return;
        }

        matchStatus.style.display = 'flex';
        if (p1 === p2) {
            matchStatus.className = 'password-match-status match-yes';
            matchStatus.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Password baru cocok!</span>';
        } else {
            matchStatus.className = 'password-match-status match-no';
            matchStatus.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> <span>Password konfirmasi belum cocok.</span>';
        }
    }

    if (confirmPassInput) {
        confirmPassInput.addEventListener('input', checkMatch);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
