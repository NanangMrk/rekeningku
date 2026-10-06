<?php
/**
 * RekeningKu - Admin Dashboard
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pageTitle = 'Dashboard Ringkasan';
$activeNav = 'dashboard';

// Fetch statistics
$totalAccounts = (int) $pdo->query('SELECT COUNT(*) FROM accounts')->fetchColumn();
$activeAccounts = (int) $pdo->query('SELECT COUNT(*) FROM accounts WHERE is_active = 1')->fetchColumn();
$inactiveAccounts = $totalAccounts - $activeAccounts;

// Fetch accounts ordered by sort_order
$stmt = $pdo->query('SELECT * FROM accounts ORDER BY sort_order ASC, id ASC');
$accounts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-title-box">
    <div>
        <h1 class="page-title">Ringkasan Sistem</h1>
        <p class="page-description">Pantau status rekening aktif dan konfigurasi tampilan landing page.</p>
    </div>
    <div class="page-title-actions">
        <a href="rekening.php" class="btn-secondary">
            <i class="fa-solid fa-list-check"></i>
            <span>Kelola Urutan</span>
        </a>
        <a href="rekening-add.php" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Rekening Baru</span>
        </a>
    </div>
</div>

<!-- KPI Stats Grid -->
<section class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Rekening</div>
            <div class="stat-value"><?= $totalAccounts ?></div>
            <div class="stat-subtext">Semua metode terdaftar</div>
        </div>
        <div class="stat-icon-box stat-icon-blue">
            <i class="fa-solid fa-building-columns"></i>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Rekening Aktif</div>
            <div class="stat-value" style="color: var(--success);"><?= $activeAccounts ?></div>
            <div class="stat-subtext">
                <i class="fa-solid fa-circle" style="color: var(--success); font-size: 7px;"></i>
                Tampil di halaman publik
            </div>
        </div>
        <div class="stat-icon-box stat-icon-green">
            <i class="fa-solid fa-circle-check"></i>
        </div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-label">Rekening Nonaktif</div>
            <div class="stat-value" style="color: var(--text-muted);"><?= $inactiveAccounts ?></div>
            <div class="stat-subtext">Disembunyikan dari pengunjung</div>
        </div>
        <div class="stat-icon-box stat-icon-amber">
            <i class="fa-solid fa-eye-slash"></i>
        </div>
    </div>
</section>

<!-- Overview Panel Table -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <i class="fa-solid fa-credit-card" style="color: var(--primary);"></i>
            <span>Daftar Rekening Pembayaran</span>
        </h2>
        <a href="rekening.php" class="btn-secondary" style="font-size: 12px; padding: 6px 12px;">
            <span>Lihat Semua</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <!-- Desktop Table View (>= 769px) -->
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Urutan</th>
                    <th>Bank / Lembaga</th>
                    <th>Nomor Rekening</th>
                    <th>Nama Pemilik</th>
                    <th>Label</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-folder-open" style="font-size: 32px; margin-bottom: 8px; display: block;"></i>
                            Belum ada rekening yang ditambahkan. Silakan klik tombol <strong>Tambah Rekening</strong>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accounts as $acc): 
                        $theme = get_card_theme($acc['card_theme'] ?? 'sapphire');
                        $catMeta = get_category_meta($acc['category'] ?? 'rekening');
                    ?>
                        <tr>
                            <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); text-align: center;">
                                #<?= e((string)$acc['sort_order']) ?>
                            </td>
                            <td>
                                <div class="bank-cell">
                                    <div class="bank-avatar-mini" style="background: <?= $theme['gradient'] ?>;">
                                        <?= strtoupper(substr(e($acc['bank_name']), 0, 3)) ?>
                                    </div>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <strong style="color: var(--text-primary); font-size: 14px;">
                                                <?= e($acc['bank_name']) ?>
                                            </strong>
                                            <span class="badge <?= $catMeta['badge_class'] ?>" style="font-size: 10px; padding: 2px 7px;">
                                                <i class="<?= $catMeta['icon'] ?>" style="font-size: 9px;"></i>
                                                <?= $catMeta['name'] ?>
                                            </span>
                                            <?php if (!empty($acc['qris_image'])): ?>
                                                <a href="../assets/images/qris/<?= e($acc['qris_image']) ?>" target="_blank" title="Lihat Barcode QRIS">
                                                    <img src="../assets/images/qris/<?= e($acc['qris_image']) ?>" alt="QRIS" class="table-qris-thumb" />
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <small style="color: var(--text-muted); font-size: 11px;">
                                            <?= e($acc['notes'] ?: 'Tidak ada catatan') ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code style="font-family: var(--font-mono); font-weight: 600; font-size: 13.5px; background: #f1f5f9; padding: 4px 8px; border-radius: 6px;">
                                    <?= e(format_account_pan($acc['account_number'])) ?>
                                </code>
                            </td>
                            <td style="font-weight: 600; color: var(--text-secondary);">
                                <?= e($acc['account_name']) ?>
                            </td>
                            <td>
                                <span class="badge badge-primary">
                                    <?= e($acc['label'] ?: 'Utama') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($acc['is_active']): ?>
                                    <span class="badge badge-success">
                                        <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-muted">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <a href="rekening-edit.php?id=<?= e((string)$acc['id']) ?>" class="btn-icon" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn-icon btn-icon-danger btn-trigger-delete" 
                                            data-id="<?= e((string)$acc['id']) ?>" 
                                            data-name="<?= e($acc['bank_name'] . ' - ' . $acc['account_number']) ?>"
                                            title="Hapus">
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
                Belum ada rekening yang ditambahkan.
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
                            <?php if ($acc['is_active']): ?>
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-circle" style="font-size: 6px;"></i> Aktif
                                </span>
                            <?php else: ?>
                                <span class="badge badge-muted">Nonaktif</span>
                            <?php endif; ?>
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
                        <?php if (!empty($acc['notes'])): ?>
                            <div class="mac-notes">
                                <i class="fa-solid fa-circle-info"></i>
                                <span><?= e($acc['notes']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mac-footer">
                        <span class="mac-order">Urutan #<?= e((string)$acc['sort_order']) ?></span>
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
                Tindakan ini tidak dapat dibatalkan dan rekening ini tidak akan lagi muncul di landing page.
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
