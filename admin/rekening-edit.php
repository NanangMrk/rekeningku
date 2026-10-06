<?php
/**
 * RekeningKu - Edit Existing Account
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pageTitle = 'Edit Rekening';
$activeNav = 'rekening';
$errors = [];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message('error', 'ID rekening tidak valid.');
    redirect('rekening.php');
}

// Fetch existing account
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$account = $stmt->fetch();

if (!$account) {
    set_flash_message('error', 'Data rekening tidak ditemukan.');
    redirect('rekening.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $errors[] = 'Token keamanan tidak valid. Silakan coba lagi.';
    } else {
        $category = trim((string) ($_POST['category'] ?? ($account['category'] ?? 'rekening')));
        if (!in_array($category, ['rekening', 'ewallet', 'qris'], true)) {
            $category = 'rekening';
        }

        $bankName = trim((string) ($_POST['bank_name'] ?? ''));
        $accountNumber = trim((string) ($_POST['account_number'] ?? ''));
        $accountName = trim((string) ($_POST['account_name'] ?? ''));
        $label = trim((string) ($_POST['label'] ?? ''));
        $cardTheme = trim((string) ($_POST['card_theme'] ?? 'sapphire'));
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $sortOrder = filter_var($_POST['sort_order'] ?? 0, FILTER_VALIDATE_INT);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Validation based on category
        if ($category === 'qris') {
            if (empty($bankName)) {
                $bankName = 'QRIS All Payment';
            }
            if (empty($accountNumber)) {
                $accountNumber = 'QRIS - Standar Pembayaran';
            }
        } else {
            if (empty($bankName)) {
                $errors[] = 'Nama Bank / Institusi tidak boleh kosong.';
            }
            if (empty($accountNumber)) {
                $errors[] = 'Nomor rekening tidak boleh kosong.';
            }
        }

        if (empty($accountName)) {
            $errors[] = 'Nama pemilik rekening / merchant tidak boleh kosong.';
        }
        if ($sortOrder === false) {
            $errors[] = 'Urutan harus berupa angka bulat.';
        }

        // Handle QRIS Image Upload
        $qrisImage = $account['qris_image'] ?? null;
        if ($category === 'qris') {
            $uploadRes = handle_qris_upload($_FILES['qris_image'] ?? [], $qrisImage);
            if (!$uploadRes['success']) {
                $errors[] = $uploadRes['error'];
            } else {
                $qrisImage = $uploadRes['filename'];
            }
        }

        if (empty($errors)) {
            try {
                $icon = ($category === 'qris') ? 'fa-qrcode' : (($category === 'ewallet') ? 'fa-wallet' : 'fa-building-columns');
                $defaultLabel = ($category === 'qris') ? 'QRIS' : (($category === 'ewallet') ? 'E-Wallet' : 'Utama');

                $updateStmt = $pdo->prepare("
                    UPDATE accounts 
                    SET bank_name = :bank_name,
                        account_number = :account_number,
                        account_name = :account_name,
                        category = :category,
                        qris_image = :qris_image,
                        label = :label,
                        icon = :icon,
                        card_theme = :card_theme,
                        notes = :notes,
                        sort_order = :sort_order,
                        is_active = :is_active,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                ");

                $updateStmt->execute([
                    ':bank_name' => $bankName,
                    ':account_number' => $accountNumber,
                    ':account_name' => $accountName,
                    ':category' => $category,
                    ':qris_image' => $qrisImage,
                    ':label' => $label ?: $defaultLabel,
                    ':icon' => $icon,
                    ':card_theme' => $cardTheme,
                    ':notes' => $notes,
                    ':sort_order' => $sortOrder,
                    ':is_active' => $isActive,
                    ':id' => $id
                ]);

                set_flash_message('success', 'Data rekening ' . $bankName . ' berhasil diperbarui!');
                redirect('rekening.php');
            } catch (PDOException $e) {
                error_log('Update account error: ' . $e->getMessage());
                $errors[] = 'Gagal menyimpan perubahan ke database.';
            }
        }
    }
}

// Current theme for visual preview
$currentThemeKey = $account['card_theme'] ?? 'sapphire';
$theme = get_card_theme($currentThemeKey);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-title-box">
    <div>
        <h1 class="page-title">Edit Rekening: <?= e($account['bank_name']) ?></h1>
        <p class="page-description">Perbarui informasi detail rekening, status visibilitas, atau urutan tampil.</p>
    </div>
    <div>
        <a href="rekening.php" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar</span>
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

<div class="card-studio-layout">
    <!-- Live Card Studio Preview Column -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">
                <i class="fa-solid fa-eye" style="color: var(--primary);"></i>
                <span>Studio Pratinjau Kartu</span>
            </h2>
            <span class="badge badge-primary">Live Sync</span>
        </div>
        <div class="panel-body">
            <div class="preview-stage">
                <!-- Virtual ATM Card Mockup -->
                <div class="atm-card-visual" id="virtualCard" style="background: <?= $theme['gradient'] ?>; width: 100%; border-radius: 16px;">
                    <div class="card-texture-overlay"></div>
                    <div class="card-mesh-pattern"></div>

                    <!-- Top -->
                    <div class="atm-top-bar">
                        <div class="card-chip-group">
                            <div class="emv-chip">
                                <div class="chip-line-h"></div>
                                <div class="chip-line-h"></div>
                                <div class="chip-line-v"></div>
                                <div class="chip-line-v"></div>
                            </div>
                            <i class="fa-solid fa-wifi contactless-wave"></i>
                        </div>
                        <div class="card-bank-header">
                            <div class="bank-name-title" id="previewBankName"><?= e($account['bank_name']) ?></div>
                            <span class="card-tier-label" id="previewCardTier" style="background: <?= $theme['badge_bg'] ?>; color: <?= $theme['badge_text'] ?>;">
                                <?= strtoupper(e($account['label'] ?: 'UTAMA')) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Mid -->
                    <div class="atm-mid-bar">
                        <span class="card-label-small">Nomor Rekening / Account</span>
                        <div class="card-account-number" id="previewCardNumber"><?= e(format_account_pan($account['account_number'])) ?></div>
                    </div>

                    <!-- Bottom -->
                    <div class="atm-bottom-bar">
                        <div>
                            <span class="card-label-small">Card Holder</span>
                            <div class="card-holder-name" id="previewCardHolder"><?= strtoupper(e($account['account_name'])) ?></div>
                        </div>
                        <div class="card-network-group">
                            <div class="badge-gpn">
                                <span>GPN</span>
                                <span class="badge-circle-dot"></span>
                            </div>
                            <div class="interlocking-circles">
                                <div class="circle-red"></div>
                                <div class="circle-amber"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p style="margin-top: 16px; font-size: 12.5px; color: var(--text-muted); text-align: center;">
                <i class="fa-solid fa-circle-info"></i> Tampilan di atas adalah representasi visual yang akan dilihat pengunjung landing page.
            </p>
        </div>
    </div>

    <!-- Account Details Edit Form Column -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">
                <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i>
                <span>Edit Informasi Rekening</span>
            </h2>
        </div>
        <div class="panel-body">
            <form method="POST" action="rekening-edit.php?id=<?= e((string)$account['id']) ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <?php 
                $selectedCat = $_POST['category'] ?? ($account['category'] ?? 'rekening'); 
                $currentQrisImage = $account['qris_image'] ?? null;
                ?>

                <!-- Category Pill Selector -->
                <div class="form-group" style="margin-bottom: 22px;">
                    <label class="form-label">
                        Kategori Pembayaran <span class="required">*</span>
                    </label>
                    <div class="category-pill-group">
                        <label class="category-pill-item">
                            <input type="radio" name="category" value="rekening" <?= ($selectedCat === 'rekening') ? 'checked' : '' ?>>
                            <span class="category-pill-card">
                                <i class="fa-solid fa-building-columns"></i>
                                <span>Rekening Bank</span>
                            </span>
                        </label>
                        <label class="category-pill-item">
                            <input type="radio" name="category" value="ewallet" <?= ($selectedCat === 'ewallet') ? 'checked' : '' ?>>
                            <span class="category-pill-card">
                                <i class="fa-solid fa-wallet"></i>
                                <span>E-Wallet</span>
                            </span>
                        </label>
                        <label class="category-pill-item">
                            <input type="radio" name="category" value="qris" <?= ($selectedCat === 'qris') ? 'checked' : '' ?>>
                            <span class="category-pill-card">
                                <i class="fa-solid fa-qrcode"></i>
                                <span>QRIS Barcode</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- QRIS Barcode Upload Box (Dynamic for QRIS) -->
                <div class="form-group qris-upload-box" id="qrisUploadSection" style="<?= ($selectedCat === 'qris') ? '' : 'display: none;' ?>">
                    <label class="form-label">
                        <i class="fa-solid fa-qrcode" style="color: var(--primary);"></i>
                        Unggah / Ganti Foto Barcode QRIS
                    </label>
                    <div class="qris-dropzone" id="qrisDropzone">
                        <input type="file" id="qris_image" name="qris_image" accept="image/png, image/jpeg, image/jpg, image/webp" class="qris-file-input">
                        <div class="qris-dropzone-content">
                            <div class="qris-preview-holder" id="qrisPreviewHolder" style="<?= $currentQrisImage ? '' : 'display: none;' ?>">
                                <img src="<?= $currentQrisImage ? '../assets/images/qris/' . e($currentQrisImage) : '' ?>" alt="Pratinjau Barcode QRIS" id="qrisImagePreview" class="qris-thumb-preview">
                                <button type="button" class="btn-remove-qris" id="btnRemoveQris" title="Hapus foto"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="qris-upload-prompt" id="qrisUploadPrompt" style="<?= $currentQrisImage ? 'display: none;' : '' ?>">
                                <i class="fa-solid fa-cloud-arrow-up qris-upload-icon"></i>
                                <span class="qris-prompt-title">Pilih atau seret barcode QRIS baru ke sini</span>
                                <span class="qris-prompt-desc">Format PNG, JPG, JPEG, atau WebP (Maksimal 5MB)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="bank_name">
                            Nama Bank / Institusi <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="bank_name" 
                               name="bank_name" 
                               class="form-control" 
                               value="<?= e($_POST['bank_name'] ?? $account['bank_name']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="label">
                            Label Kartu
                        </label>
                        <input type="text" 
                               id="label" 
                               name="label" 
                               class="form-control" 
                               value="<?= e($_POST['label'] ?? $account['label']) ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="account_number">
                            Nomor Rekening / No. E-Wallet <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="account_number" 
                               name="account_number" 
                               class="form-control" 
                               style="font-family: var(--font-mono);" 
                               value="<?= e($_POST['account_number'] ?? $account['account_number']) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="account_name">
                            Nama Pemilik Rekening <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="account_name" 
                               name="account_name" 
                               class="form-control" 
                               value="<?= e($_POST['account_name'] ?? $account['account_name']) ?>" 
                               required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="card_theme">Tema Kartu (Gradien Visual)</label>
                        <?php $selectedTheme = $_POST['card_theme'] ?? $account['card_theme'] ?? 'sapphire'; ?>
                        <select id="card_theme" name="card_theme" class="form-control">
                            <option value="sapphire" <?= $selectedTheme === 'sapphire' ? 'selected' : '' ?>>Deep Sapphire (Biru Royal &amp; Navy)</option>
                            <option value="sapphire-dark" <?= $selectedTheme === 'sapphire-dark' ? 'selected' : '' ?>>Royal Navy (Biru Tua Mewah)</option>
                            <option value="emerald" <?= $selectedTheme === 'emerald' ? 'selected' : '' ?>>Emerald Wealth (Hijau Zamrud)</option>
                            <option value="obsidian" <?= $selectedTheme === 'obsidian' ? 'selected' : '' ?>>Obsidian Onyx (Hitam Elegan)</option>
                            <option value="violet" <?= $selectedTheme === 'violet' ? 'selected' : '' ?>>Imperial Violet (Ungu Eksklusif)</option>
                            <option value="amber" <?= $selectedTheme === 'amber' ? 'selected' : '' ?>>Sunset Amber (Cokelat Keemasan)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sort_order">
                            Urutan Tampil (Sort Order) <span class="required">*</span>
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               class="form-control" 
                               value="<?= e((string)($_POST['sort_order'] ?? $account['sort_order'])) ?>" 
                               min="1" 
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">Catatan Khusus / Instruksi Transfer (Opsional)</label>
                    <textarea id="notes" 
                              name="notes" 
                              class="form-control" 
                              rows="2"><?= e($_POST['notes'] ?? $account['notes']) ?></textarea>
                </div>

                <div class="form-group" style="margin-top: 10px; background: #f8fafc; padding: 14px 18px; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong style="display: block; font-size: 13.5px; color: var(--text-primary);">Tampilkan di Halaman Publik</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">Jika dinonaktifkan, rekening ini akan disembunyikan dari landing page utama.</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="is_active" value="1" <?= (isset($_POST['is_active']) ? $_POST['is_active'] : $account['is_active']) ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-subtle);">
                    <a href="rekening.php" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
