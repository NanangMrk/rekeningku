<?php
/**
 * RekeningKu - Reorder Account Priority
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
$direction = $_POST['direction'] ?? '';

if (!verify_csrf_token($token) || !$id || !in_array($direction, ['up', 'down'], true)) {
    set_flash_message('error', 'Permintaan pengurutan tidak valid.');
    redirect('rekening.php');
}

try {
    // Fetch all accounts sorted by sort_order
    $stmt = $pdo->query('SELECT id, sort_order FROM accounts ORDER BY sort_order ASC, id ASC');
    $accounts = $stmt->fetchAll();

    $currentIndex = null;
    foreach ($accounts as $idx => $acc) {
        if ((int)$acc['id'] === $id) {
            $currentIndex = $idx;
            break;
        }
    }

    if ($currentIndex !== null) {
        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

        if ($targetIndex >= 0 && $targetIndex < count($accounts)) {
            // Swap sort_order between current and target
            $currentAcc = $accounts[$currentIndex];
            $targetAcc = $accounts[$targetIndex];

            $order1 = $currentAcc['sort_order'];
            $order2 = $targetAcc['sort_order'];

            // If they happen to have identical sort_order values, adjust appropriately
            if ($order1 === $order2) {
                if ($direction === 'up') {
                    $order1 = max(1, $order2 - 1);
                } else {
                    $order1 = $order2 + 1;
                }
            } else {
                // Normal swap
                $temp = $order1;
                $order1 = $order2;
                $order2 = $temp;
            }

            $pdo->beginTransaction();

            $updateStmt = $pdo->prepare('UPDATE accounts SET sort_order = :order WHERE id = :id');
            $updateStmt->execute([':order' => $order1, ':id' => $currentAcc['id']]);
            $updateStmt->execute([':order' => $order2, ':id' => $targetAcc['id']]);

            $pdo->commit();
            set_flash_message('success', 'Urutan rekening berhasil diperbarui.');
        }
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Reorder account error: ' . $e->getMessage());
    set_flash_message('error', 'Gagal mengubah urutan rekening.');
}

redirect('rekening.php');
