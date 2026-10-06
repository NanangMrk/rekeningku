<?php
/**
 * RekeningKu - Manage Accounts List
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pageTitle = 'Kelola Rekening';
$activeNav = 'rekening';

// Fetch all accounts sorted by sort_order
$stmt = $pdo->query('SELECT * FROM accounts ORDER BY sort_order ASC, id ASC');
$accounts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-title-box">
    <div>
        <h1 class="page-title">Kelola Rekening Bank &amp; E-Wallet</h1>
        <p class="page-description">Atur urutan tampilan, status aktif/nonaktif, dan edit informasi rekening.</p>
    </div>
    <div class="page-title-actions">
        <a href="rekening-add.php" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Rekening Baru</span>
        </a>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i>
            <span>Daftar Rekening Terdaftar (<?= count($accounts) ?>)</span>
        </h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 70px; text-align: center;">Urutan</th>
                    <th>Institusi / Bank</th>
                    <th>Nomor Rekening</th>
                    <th>Nama Pemilik</th>
                    <th>Tema Kartu</th>
                    <th style="width: 140px;">Visibilitas Publik</th>
                    <th style="text-align: right; width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 32px; margin-bottom: 8px; display: block;"></i>
                            Belum ada rekening. Silakan klik tombol <strong>Tambah Rekening Baru</strong> di atas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accounts as $acc): 
                        $theme = get_card_theme($acc['card_theme'] ?? 'sapphire');
                        $catMeta = get_category_meta($acc['category'] ?? 'rekening');
                    ?>
                        <tr>
                            <!-- Order with Quick Move -->
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 4px;">
                                    <form method="POST" action="reorder.php" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string)$acc['id']) ?>">
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" class="btn-icon" style="width: 26px; height: 26px; font-size: 11px;" title="Pindah ke Atas">
                                            <i class="fa-solid fa-arrow-up"></i>
                                        </button>
                                    </form>
                                    <span style="font-family: var(--font-mono); font-weight: 700; min-width: 24px; text-align: center;">
                                        <?= e((string)$acc['sort_order']) ?>
                                    </span>
                                    <form method="POST" action="reorder.php" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= e((string)$acc['id']) ?>">
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" class="btn-icon" style="width: 26px; height: 26px; font-size: 11px;" title="Pindah ke Bawah">
                                            <i class="fa-solid fa-arrow-down"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            <!-- Bank Name -->
                            <td>
                                <div class="bank-cell">
                                    <div class="bank-avatar-mini" style="background: <?= $theme['gradient'] ?>;">
                                        <?= strtoupper(substr(e($acc['bank_name']), 0, 3)) ?>
                                    </div>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            <strong style="color: var(--text-primary); font-size: 14px;">
                                                <?= e($acc['bank_name']) ?>
                                            </strong>
                                            <span class="badge <?= $catMeta['badge_class'] ?>" style="font-size: 10px; padding: 2px 7px;">
                                                <i class="<?= $catMeta['icon'] ?>" style="font-size: 9px;"></i>
                                                <?= $catMeta['name'] ?>
                                            </span>
                                            <span class="badge badge-primary" style="font-size: 10px;">
                                                <?= e($acc['label'] ?: 'Utama') ?>
                                            </span>
                                            <?php if (!empty($acc['qris_image'])): ?>
                                                <a href="../assets/images/qris/<?= e($acc['qris_image']) ?>" target="_blank" title="Lihat Barcode QRIS">
                                                    <img src="../assets/images/qris/<?= e($acc['qris_image']) ?>" alt="QRIS" class="table-qris-thumb" />
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($acc['notes'])): ?>
                                            <div style="font-size: 11.5px; color: var(--text-muted); margin-top: 2px;">
                                                <?= e($acc['notes']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- Account Number -->
                            <td>
                                <code style="font-family: var(--font-mono); font-weight: 600; font-size: 14px; background: #f1f5f9; padding: 4px 8px; border-radius: 6px;">
                                    <?= e(format_account_pan($acc['account_number'])) ?>
                                </code>
                            </td>

                            <!-- Account Holder -->
                            <td style="font-weight: 600; color: var(--text-secondary);">
                                <?= e($acc['account_name']) ?>
                            </td>

                            <!-- Card Theme -->
                            <td>
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">
                                    <span style="width: 12px; height: 12px; border-radius: 3px; background: <?= $theme['gradient'] ?>; display: inline-block;"></span>
                                    <?= e($theme['name']) ?>
                                </span>
                            </td>

                            <!-- Toggle Status -->
                            <td>
                                <form method="POST" action="rekening-toggle.php" style="display: inline-flex; align-items: center; gap: 8px;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e((string)$acc['id']) ?>">
                                    <label class="toggle-switch" title="Klik untuk mengaktifkan/menonaktifkan">
                                        <input type="checkbox" name="is_active" value="1" <?= $acc['is_active'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span style="font-size: 12px; font-weight: 600; color: <?= $acc['is_active'] ? 'var(--success-text)' : 'var(--text-muted)' ?>;">
                                        <?= $acc['is_active'] ? 'Tampil' : 'Sembunyi' ?>
                                    </span>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <a href="rekening-edit.php?id=<?= e((string)$acc['id']) ?>" class="btn-icon" title="Edit Rekening">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn-icon btn-icon-danger btn-trigger-delete" 
                                            data-id="<?= e((string)$acc['id']) ?>" 
                                            data-name="<?= e($acc['bank_name'] . ' - ' . $acc['account_number']) ?>"
                                            title="Hapus Rekening">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card List View (No Horizontal Scroll on Mobile <= 768px) -->
    <div class="mobile-account-list">
        <?php if (empty($accounts)): ?>
            <div style="text-align: center; padding: 32px 16px; color: var(--text-muted);">
                <i class="fa-solid fa-folder-open" style="font-size: 28px; margin-bottom: 8px; display: block;"></i>
                Belum ada rekening.
            </div>
        <?php else: ?>
            <?php foreach ($accounts as $acc): 
                $theme = get_card_theme($acc['card_theme'] ?? 'sapphire');
                $catMeta = get_category_meta($acc['category'] ?? 'rekening');
            ?>
                <div class="mobile-account-card">
                    <div class="mac-header">
                        <div class="mac-bank-info">
                            <div class="bank-avatar-mini" style="background: <?= $theme['gradient'] ?>;">
                                <?= strtoupper(substr(e($acc['bank_name']), 0, 3)) ?>
                            </div>
                            <div>
                                <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                                    <strong class="mac-bank-name"><?= e($acc['bank_name']) ?></strong>
                                    <span class="badge <?= $catMeta['badge_class'] ?>" style="font-size: 9px; padding: 1px 6px;">
                                        <i class="<?= $catMeta['icon'] ?>" style="font-size: 8px;"></i>
                                        <?= $catMeta['name'] ?>
                                    </span>
                                    <?php if (!empty($acc['qris_image'])): ?>
                                        <a href="../assets/images/qris/<?= e($acc['qris_image']) ?>" target="_blank" title="Lihat Barcode QRIS">
                                            <img src="../assets/images/qris/<?= e($acc['qris_image']) ?>" alt="QRIS" class="table-qris-thumb" style="width: 18px; height: 18px;" />
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <span class="badge badge-primary mac-badge"><?= e($acc['label'] ?: 'Utama') ?></span>
                            </div>
                        </div>
                        <div class="mac-status">
                            <form method="POST" action="rekening-toggle.php" style="display: inline-flex; align-items: center; gap: 6px;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e((string)$acc['id']) ?>">
                                <label class="toggle-switch" title="Ubah status">
                                    <input type="checkbox" name="is_active" value="1" <?= $acc['is_active'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span style="font-size: 11px; font-weight: 600; color: <?= $acc['is_active'] ? 'var(--success-text)' : 'var(--text-muted)' ?>;">
                                    <?= $acc['is_active'] ? 'Tampil' : 'Sembunyi' ?>
                                </span>
                            </form>
                        </div>
                    </div>

                    <div class="mac-body">
                        <div class="mac-detail-row">
                            <span class="mac-label">No. Rekening</span>
                            <code class="mac-number"><?= e(format_account_pan($acc['account_number'])) ?></code>
                        </div>
                        <div class="mac-detail-row">
                            <span class="mac-label">Pemilik</span>
                            <span class="mac-holder"><?= e($acc['account_name']) ?></span>
                        </div>
                        <div class="mac-detail-row">
                            <span class="mac-label">Tema Kartu</span>
                            <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; color: var(--text-secondary);">
                                <span style="width: 10px; height: 10px; border-radius: 2px; background: <?= $theme['gradient'] ?>; display: inline-block;"></span>
                                <?= e($theme['name']) ?>
                            </span>
                        </div>
                        <?php if (!empty($acc['notes'])): ?>
                            <div class="mac-notes">
                                <i class="fa-solid fa-circle-info"></i>
                                <span><?= e($acc['notes']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mac-footer">
                        <!-- Order arrows -->
                        <div style="display: flex; align-items: center; gap: 3px;">
                            <form method="POST" action="reorder.php" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e((string)$acc['id']) ?>">
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="btn-icon" style="width: 26px; height: 26px; font-size: 11px;" title="Naik">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </button>
                            </form>
                            <span class="mac-order" style="min-width: 20px; text-align: center;">#<?= e((string)$acc['sort_order']) ?></span>
                            <form method="POST" action="reorder.php" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= e((string)$acc['id']) ?>">
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="btn-icon" style="width: 26px; height: 26px; font-size: 11px;" title="Turun">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Actions -->
                        <div class="mac-actions">
                            <a href="rekening-edit.php?id=<?= e((string)$acc['id']) ?>" class="btn-action-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </a>
                            <button type="button" 
                                    class="btn-action-delete btn-trigger-delete" 
                                    data-id="<?= e((string)$acc['id']) ?>" 
                                    data-name="<?= e($acc['bank_name'] . ' - ' . $acc['account_number']) ?>">
                                <i class="fa-solid fa-trash-can"></i>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal Component -->
<div class="modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title" id="deleteModalTitle" style="color: var(--danger);">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Konfirmasi Hapus Rekening
            </h3>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus rekening <strong id="deleteAccountName" style="color: var(--text-primary);"></strong>?</p>
            <p style="margin-top: 8px; font-size: 13px; color: var(--text-muted);">
                Tindakan ini tidak dapat dibatalkan dan rekening ini tidak akan lagi muncul di landing page publik.
            </p>
        </div>
        <form method="POST" action="rekening-delete.php" id="deleteForm">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="deleteIdInput" value="">
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="btnCancelDelete">Batal</button>
                <button type="submit" class="btn-danger">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Ya, Hapus Rekening</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
