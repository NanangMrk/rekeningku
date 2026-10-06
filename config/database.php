<?php
/**
 * Database Configuration & Initialization
 * Precision SQLite connection using PDO
 */

declare(strict_types=1);

$databaseDir = dirname(__DIR__) . '/database';
$databasePath = $databaseDir . '/app.sqlite';

if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0755, true);
}

try {
    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialize Schema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS accounts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            bank_name TEXT NOT NULL,
            account_number TEXT NOT NULL,
            account_name TEXT NOT NULL,
            category TEXT DEFAULT 'rekening',
            qris_image TEXT DEFAULT NULL,
            label TEXT,
            icon TEXT,
            card_theme TEXT DEFAULT 'sapphire',
            notes TEXT,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Auto-migration: Check and add category & qris_image columns if upgrading existing table
    $existingCols = $pdo->query("PRAGMA table_info(accounts)")->fetchAll(PDO::FETCH_ASSOC);
    $colNames = array_column($existingCols, 'name');

    if (!in_array('category', $colNames, true)) {
        $pdo->exec("ALTER TABLE accounts ADD COLUMN category TEXT DEFAULT 'rekening'");
        // Set existing e-wallet records to ewallet category
        $pdo->exec("UPDATE accounts SET category = 'ewallet' WHERE LOWER(label) LIKE '%wallet%' OR LOWER(bank_name) IN ('gopay', 'ovo', 'dana', 'shopeepay')");
    }

    if (!in_array('qris_image', $colNames, true)) {
        $pdo->exec("ALTER TABLE accounts ADD COLUMN qris_image TEXT DEFAULT NULL");
    }

    // Check & Seed Default Admin
    $stmtAdmin = $pdo->prepare('SELECT COUNT(*) AS total FROM admins WHERE username = :username');
    $stmtAdmin->execute([':username' => 'admin']);
    $adminExists = (int) $stmtAdmin->fetchColumn() > 0;

    if (!$adminExists) {
        $defaultPasswordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare('INSERT INTO admins (username, password) VALUES (:username, :password)');
        $insertAdmin->execute([
            ':username' => 'admin',
            ':password' => $defaultPasswordHash
        ]);
    }

    // Check & Seed Initial Sample Accounts if empty
    $stmtAcc = $pdo->query('SELECT COUNT(*) FROM accounts');
    $accountCount = (int) $stmtAcc->fetchColumn();

    if ($accountCount === 0) {
        $initialAccounts = [
            [
                'bank_name' => 'BCA',
                'account_number' => '8820 1928 3491',
                'account_name' => 'Alex Pratama, S.E.',
                'label' => 'Utama',
                'icon' => 'fa-building-columns',
                'card_theme' => 'sapphire',
                'notes' => 'Transfer via BI-Fast untuk biaya admin hemat (Rp 2.500)',
                'sort_order' => 1,
                'is_active' => 1
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'account_number' => '1370 0192 8472',
                'account_name' => 'Alex Pratama',
                'label' => 'Bisnis',
                'icon' => 'fa-landmark',
                'card_theme' => 'obsidian',
                'notes' => 'Rekening operasional transaksi bisnis & invoice',
                'sort_order' => 2,
                'is_active' => 1
            ],
            [
                'bank_name' => 'Bank BRI',
                'account_number' => '0206 0100 8921 501',
                'account_name' => 'Alex Pratama',
                'label' => 'Tabungan',
                'icon' => 'fa-building-columns',
                'card_theme' => 'sapphire-dark',
                'notes' => 'Dukungan jaringan ATM terluas di seluruh nusantara',
                'sort_order' => 3,
                'is_active' => 1
            ],
            [
                'bank_name' => 'Bank Jago',
                'account_number' => '1048 2901 8234',
                'account_name' => 'Alex Pratama',
                'label' => 'Digital',
                'icon' => 'fa-mobile-screen-button',
                'card_theme' => 'amber',
                'notes' => 'Kantong Utama Bank Jago',
                'sort_order' => 4,
                'is_active' => 1
            ],
            [
                'bank_name' => 'GoPay',
                'account_number' => '0812 8920 3810',
                'account_name' => 'Alex Pratama',
                'label' => 'E-Wallet',
                'icon' => 'fa-wallet',
                'card_theme' => 'emerald',
                'notes' => 'GoPay Plus terverifikasi',
                'sort_order' => 5,
                'is_active' => 1
            ],
            [
                'bank_name' => 'OVO',
                'account_number' => '0812 8920 3810',
                'account_name' => 'Alex Pratama',
                'label' => 'E-Wallet',
                'icon' => 'fa-wallet',
                'card_theme' => 'violet',
                'notes' => 'OVO Premier siap terima transfer instan',
                'sort_order' => 6,
                'is_active' => 1
            ]
        ];

        $insStmt = $pdo->prepare("
            INSERT INTO accounts (bank_name, account_number, account_name, label, icon, card_theme, notes, sort_order, is_active)
            VALUES (:bank_name, :account_number, :account_name, :label, :icon, :card_theme, :notes, :sort_order, :is_active)
        ");

        foreach ($initialAccounts as $acc) {
            $insStmt->execute([
                ':bank_name' => $acc['bank_name'],
                ':account_number' => $acc['account_number'],
                ':account_name' => $acc['account_name'],
                ':label' => $acc['label'],
                ':icon' => $acc['icon'],
                ':card_theme' => $acc['card_theme'],
                ':notes' => $acc['notes'],
                ':sort_order' => $acc['sort_order'],
                ':is_active' => $acc['is_active']
            ]);
        }
    }
} catch (PDOException $e) {
    // Prevent sensitive database path or error stack from leaking
    error_log('Database connection error: ' . $e->getMessage());
    die('Terjadi kesalahan pada koneksi basis data. Silakan coba beberapa saat lagi.');
}
