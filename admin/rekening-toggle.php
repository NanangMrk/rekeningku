<?php
/**
 * RekeningKu - Toggle Account Visibility
 * CSRF Protected POST action
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('rekening.php');
}

$token = $_POST['csrf_token'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$isActive = isset($_POST['is_active']) ? 1 : 0;

if (!verify_csrf_token($token)) {
    set_flash_message('error', 'Token keamanan tidak valid.');
    redirect('rekening.php');
}

if (!$id) {
    set_flash_message('error', 'ID rekening tidak valid.');
    redirect('rekening.php');
}

try {
    $stmt = $pdo->prepare('UPDATE accounts SET is_active = :is_active WHERE id = :id');
    $stmt->execute([
        ':is_active' => $isActive,
        ':id' => $id
    ]);

    set_flash_message(
        'success', 
        $isActive ? 'Rekening berhasil diaktifkan untuk publik.' : 'Rekening dinonaktifkan dari halaman publik.'
    );
} catch (PDOException $e) {
    error_log('Toggle account status error: ' . $e->getMessage());
    set_flash_message('error', 'Gagal memperbarui status rekening.');
}

redirect('rekening.php');
