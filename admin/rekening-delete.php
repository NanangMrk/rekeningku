<?php
/**
 * RekeningKu - Delete Account Endpoint
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

if (!verify_csrf_token($token)) {
    set_flash_message('error', 'Token keamanan tidak valid. Silakan coba lagi.');
    redirect('rekening.php');
}

if (!$id) {
    set_flash_message('error', 'ID rekening tidak valid.');
    redirect('rekening.php');
}

try {
    $stmt = $pdo->prepare('DELETE FROM accounts WHERE id = :id');
    $stmt->execute([':id' => $id]);

    set_flash_message('success', 'Rekening berhasil dihapus dari direktori.');
} catch (PDOException $e) {
    error_log('Delete account error: ' . $e->getMessage());
    set_flash_message('error', 'Gagal menghapus rekening.');
}

redirect('rekening.php');
