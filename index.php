<?php
/**
 * RekeningKu - Public Landing Page
 * Precision Banking & Card Portal
 * Reference: stitch_digital_bank_card_directory (ISO 7810 ID-1 Standard Proportions)
 */

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

try {
    // Fetch only active accounts ordered by sort_order
    $stmt = $pdo->prepare('
        SELECT * FROM accounts 
        WHERE is_active = 1 
        ORDER BY sort_order ASC, id ASC
    ');
    $stmt->execute();
    $accounts = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Error fetching accounts: ' . $e->getMessage());
    $accounts = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekening Pembayaran Resmi - RekeningKu</title>
    <meta name="description" content="Daftar nomor rekening bank dan dompet digital resmi untuk kemudahan transaksi dan transfer pembayaran.">
    
    <!-- Google Fonts: Manrope, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <main class="container">
        <!-- Hero Header -->
        <header class="hero-header">
            <div class="brand-badge">
                <i class="fa-solid fa-circle-check"></i>
                <span>Tervalidasi 24/7 &bull; Direktori Resmi</span>
            </div>
            <h1 class="hero-title">Rekening Pembayaran &amp; Transfer</h1>
            <p class="hero-subtitle">
                Silakan pilih rekening bank atau e-wallet tujuan, lalu klik salin untuk transfer dengan cepat tanpa resiko salah nomor.
            </p>
        </header>

        <!-- Quick Filters -->
        <?php if (!empty($accounts)): ?>
        <section class="filter-bar">
            <div class="filter-chips">
                <button type="button" class="filter-chip active" data-filter="all">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Semua</span>
                </button>
                <button type="button" class="filter-chip" data-filter="rekening">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Bank</span>
                </button>
                <button type="button" class="filter-chip" data-filter="ewallet">
                    <i class="fa-solid fa-wallet"></i>
                    <span>E-Wallet</span>
                </button>
                <button type="button" class="filter-chip" data-filter="qris">
                    <i class="fa-solid fa-qrcode"></i>
                    <span>QRIS</span>
                </button>
            </div>
        </section>
        <?php endif; ?>

        <!-- Responsive Precision Cards Grid -->
        <section class="accounts-grid" id="accountsGrid">
            <?php if (empty($accounts)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-building-columns"></i>
                    <h3>Belum Ada Rekening Aktif</h3>
                    <p>Daftar rekening pembayaran sedang dalam pemeliharaan. Silakan hubungi pemilik layanan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($accounts as $acc): 
                    $themeKey = !empty($acc['card_theme']) ? $acc['card_theme'] : 'sapphire';
                    $theme = get_card_theme($themeKey);
                    $category = !empty($acc['category']) ? $acc['category'] : 'rekening';
                    $catMeta = get_category_meta($category);
                    $formattedPan = format_account_pan($acc['account_number']);
                    $cleanNumber = preg_replace('/\s+/', '', $acc['account_number']);
                    $label = !empty($acc['label']) ? $acc['label'] : ($category === 'qris' ? 'QRIS' : 'Utama');
                    $qrisImage = !empty($acc['qris_image']) ? $acc['qris_image'] : '';
                ?>
                <article class="account-card-wrapper <?= $category === 'qris' ? 'account-card-qris' : '' ?>" 
                         data-category="<?= e($category) ?>"
                         data-label="<?= e($label) ?>" 
                         data-bank="<?= e($acc['bank_name']) ?>"
                         id="account-<?= e((string)$acc['id']) ?>">
                    
                    <!-- Tactile ATM Card Visual (Authentic 1.586:1 Ratio) -->
                    <div class="card-stage-container">
                        <!-- Colored Ambient Backglow -->
                        <div class="card-ambient-backglow" style="background-color: <?= $theme['glow'] ?>;"></div>

                        <div class="atm-card-visual" style="background: <?= $theme['gradient'] ?>;">
                            <div class="card-texture-overlay"></div>
                            <div class="card-mesh-pattern"></div>

                            <!-- Top row: EMV Smart Chip, Contactless, Bank Name, Card Tier -->
                            <div class="atm-top-bar">
                                <div class="card-chip-group">
                                    <?php if ($category === 'qris'): ?>
                                        <div class="qris-card-icon-chip" title="QRIS Standar Nasional">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="emv-chip" title="EMV Smart Chip">
                                            <div class="chip-line-h"></div>
                                            <div class="chip-line-h"></div>
                                            <div class="chip-line-v"></div>
                                            <div class="chip-line-v"></div>
                                        </div>
                                        <i class="fa-solid fa-wifi contactless-wave" title="Contactless Payment"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="card-bank-header">
                                    <div class="bank-name-title"><?= e($acc['bank_name']) ?></div>
                                    <span class="card-tier-label" style="background: <?= $theme['badge_bg'] ?>; color: <?= $theme['badge_text'] ?>;">
                                        <?= strtoupper(e($label)) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Middle row: Tabular PAN Account Number & Inline Copy Button -->
                            <div class="atm-mid-bar">
                                <span class="card-label-small">
                                    <?= $category === 'qris' ? 'NMID / Kode Transaksi' : 'Nomor Rekening / Account' ?>
                                </span>
                                <div class="account-number-row">
                                    <div class="card-account-number"><?= !empty($cleanNumber) ? e($formattedPan) : 'Scan Barcode QRIS' ?></div>
                                    <?php if ($category === 'qris'): ?>
                                        <button type="button" 
                                                class="btn-open-inline btn-view-qris" 
                                                data-qris-url="<?= !empty($qrisImage) ? 'assets/images/qris/' . e($qrisImage) : '' ?>"
                                                data-merchant="<?= e($acc['account_name']) ?>"
                                                data-bank="<?= e($acc['bank_name']) ?>"
                                                data-nmid="<?= e($acc['account_number']) ?>"
                                                title="Buka Barcode QRIS"
                                                aria-label="Buka Barcode QRIS">
                                            <i class="fa-solid fa-qrcode"></i>
                                            <span>Buka</span>
                                        </button>
                                    <?php elseif (!empty($cleanNumber)): ?>
                                        <button type="button" 
                                                class="btn-copy btn-copy-inline" 
                                                data-account-number="<?= e($acc['account_number']) ?>"
                                                data-clean-number="<?= e($cleanNumber) ?>"
                                                data-bank="<?= e($acc['bank_name']) ?>"
                                                title="Salin Nomor <?= e($acc['bank_name']) ?>"
                                                aria-label="Salin Nomor <?= e($acc['bank_name']) ?>">
                                            <i class="fa-regular fa-copy"></i>
                                            <span>Salin</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Bottom row: Card Holder Name & Payment Institution Badge -->
                            <div class="atm-bottom-bar">
                                <div>
                                    <span class="card-label-small"><?= $category === 'qris' ? 'Merchant / Pemilik' : 'Card Holder' ?></span>
                                    <div class="card-holder-name"><?= strtoupper(e($acc['account_name'])) ?></div>
                                </div>
                                <div class="card-network-group">
                                    <?php if ($category === 'qris'): ?>
                                        <div class="badge-qris-pill">
                                            <span>QRIS</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="badge-gpn">
                                            <span>GPN</span>
                                            <span class="badge-circle-dot"></span>
                                        </div>
                                        <div class="interlocking-circles">
                                            <div class="circle-red"></div>
                                            <div class="circle-amber"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <!-- Public Footer -->
    <footer class="public-footer">
        <p>&copy; <?= date('Y') ?> RekeningKu &bull; Precision Payment Hub</p>
    </footer>

    <!-- QRIS Barcode Modal Dialog -->
    <div class="qris-modal-overlay" id="qrisModal" role="dialog" aria-modal="true" aria-labelledby="qrisModalTitle">
        <div class="qris-modal-backdrop" id="qrisBackdrop"></div>
        <div class="qris-modal-card">
            <div class="qris-modal-header">
                <div class="qris-modal-title-group">
                    <span class="qris-badge-pill"><i class="fa-solid fa-qrcode"></i> QRIS</span>
                    <h3 class="qris-modal-title" id="qrisModalTitle">Barcode Pembayaran</h3>
                </div>
                <button type="button" class="btn-close-qris-modal" id="qrisCloseBtn" aria-label="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="qris-modal-body">
                <div class="qris-image-stage">
                    <img id="qrisModalImg" src="" alt="Barcode QRIS" class="qris-modal-img" />
                </div>
                
                <div class="qris-modal-meta">
                    <div class="qris-meta-merchant" id="qrisModalMerchant">Merchant</div>
                    <div class="qris-meta-bank" id="qrisModalBank">Layanan</div>
                    <div class="qris-meta-nmid-box" id="qrisModalNmidWrap" style="display: none;">
                        <span class="qris-nmid-label">NMID / Kode:</span>
                        <code id="qrisModalNmid" class="qris-nmid-value"></code>
                        <button type="button" class="btn-copy-nmid" id="qrisCopyNmidBtn" title="Salin Kode">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="qris-modal-instructions">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>Pindai langsung menggunakan BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, ShopeePay, atau aplikasi QRIS lainnya.</span>
                </div>
            </div>

            <div class="qris-modal-footer">
                <a id="qrisDownloadBtn" href="" download="qris_barcode.png" class="btn-download-qris">
                    <i class="fa-solid fa-download"></i>
                    <span>Unduh Gambar QRIS</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Notification Feedback -->
    <div class="toast-container" id="copyToast" role="alert" aria-live="polite">
        <i class="fa-solid fa-circle-check toast-icon"></i>
        <span id="toastMessage">Nomor rekening berhasil disalin!</span>
    </div>

    <!-- Scripts -->
    <script src="assets/js/app.js"></script>
</body>
</html>
