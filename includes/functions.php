<?php
/**
 * Helper Functions
 */

declare(strict_types=1);

/**
 * Escape HTML output safely
 */
function e(?string $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or get CSRF token
 */
function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF input field
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Validate CSRF token
 */
function verify_csrf_token(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message
 */
function set_flash_message(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message
    ];
}

/**
 * Retrieve and clear flash message
 */
function get_flash_message(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    return null;
}

/**
 * Redirect safely
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Get Card Theme Details (Gradient, Accent color, Tier badge)
 */
function get_card_theme(string $theme): array
{
    $themes = [
        'sapphire' => [
            'name' => 'Deep Sapphire',
            'gradient' => 'linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%)',
            'accent' => '#facc15',
            'text_color' => '#ffffff',
            'badge_bg' => 'rgba(250, 204, 21, 0.2)',
            'badge_text' => '#fef08a',
            'glow' => 'rgba(37, 99, 235, 0.35)'
        ],
        'sapphire-dark' => [
            'name' => 'Royal Navy',
            'gradient' => 'linear-gradient(135deg, #1e40af 0%, #172554 100%)',
            'accent' => '#93c5fd',
            'text_color' => '#ffffff',
            'badge_bg' => 'rgba(147, 197, 253, 0.2)',
            'badge_text' => '#dbeafe',
            'glow' => 'rgba(30, 64, 175, 0.3)'
        ],
        'emerald' => [
            'name' => 'Emerald Wealth',
            'gradient' => 'linear-gradient(135deg, #064e3b 0%, #022c22 100%)',
            'accent' => '#34d399',
            'text_color' => '#ffffff',
            'badge_bg' => 'rgba(52, 211, 153, 0.2)',
            'badge_text' => '#a7f3d0',
            'glow' => 'rgba(16, 185, 129, 0.3)'
        ],
        'obsidian' => [
            'name' => 'Obsidian Onyx',
            'gradient' => 'linear-gradient(135deg, #1f2937 0%, #0b0f19 100%)',
            'accent' => '#e2e8f0',
            'text_color' => '#ffffff',
            'badge_bg' => 'rgba(255, 255, 255, 0.15)',
            'badge_text' => '#f1f5f9',
            'glow' => 'rgba(15, 23, 42, 0.4)'
        ],
        'violet' => [
            'name' => 'Imperial Violet',
            'gradient' => 'linear-gradient(135deg, #4c1d95 0%, #1e1b4b 100%)',
            'accent' => '#c084fc',
            'text_color' => '#ffffff',
            'badge_bg' => 'rgba(192, 132, 252, 0.2)',
            'badge_text' => '#f3e8ff',
            'glow' => 'rgba(124, 58, 237, 0.3)'
        ],
        'amber' => [
            'name' => 'Sunset Amber',
            'gradient' => 'linear-gradient(135deg, #7c2d12 0%, #1c1917 100%)',
            'accent' => '#fbbf24',
            'text_color' => '#ffffff',
            'badge_bg' => 'rgba(251, 191, 36, 0.2)',
            'badge_text' => '#fef3c7',
            'glow' => 'rgba(217, 119, 6, 0.3)'
        ]
    ];

    return $themes[$theme] ?? $themes['sapphire'];
}

/**
 * Format account number for readable display with grouped spaces
 */
function format_account_pan(string $number): string
{
    $clean = preg_replace('/\s+/', '', $number);
    return trim(chunk_split($clean, 4, ' '));
}

/**
 * Handle QRIS Barcode Image Upload
 * @param array $file $_FILES['qris_image']
 * @param string|null $oldImage
 * @return array [success => bool, filename => string|null, error => string|null]
 */
function handle_qris_upload(array $file, ?string $oldImage = null): array
{
    if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'filename' => $oldImage, 'error' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'filename' => null, 'error' => 'Gagal mengunggah berkas QRIS (Error code: ' . $file['error'] . ').'];
    }

    // Size limit: 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'filename' => null, 'error' => 'Ukuran gambar QRIS maksimal 5MB.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed, true)) {
        return ['success' => false, 'filename' => null, 'error' => 'Format file barcode harus JPG, PNG, atau WebP.'];
    }

    $uploadDir = __DIR__ . '/../assets/images/qris/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFilename = 'qris_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $uploadDir . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'filename' => null, 'error' => 'Gagal memindahkan file QRIS yang diunggah ke server.'];
    }

    // Delete old image if exists
    if ($oldImage) {
        $oldFilePath = $uploadDir . basename($oldImage);
        if (file_exists($oldFilePath) && is_file($oldFilePath)) {
            @unlink($oldFilePath);
        }
    }

    return ['success' => true, 'filename' => $newFilename, 'error' => null];
}

/**
 * Get Category metadata (label, icon, badge styling)
 */
function get_category_meta(string $category): array
{
    return match (strtolower(trim($category))) {
        'ewallet' => [
            'key' => 'ewallet',
            'name' => 'E-Wallet',
            'badge_class' => 'badge-ewallet',
            'icon' => 'fa-solid fa-wallet'
        ],
        'qris' => [
            'key' => 'qris',
            'name' => 'QRIS',
            'badge_class' => 'badge-qris',
            'icon' => 'fa-solid fa-qrcode'
        ],
        default => [
            'key' => 'rekening',
            'name' => 'Bank',
            'badge_class' => 'badge-bank',
            'icon' => 'fa-solid fa-building-columns'
        ]
    };
}
