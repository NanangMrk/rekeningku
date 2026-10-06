<?php
/**
 * Admin Layout Header
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pageTitle = $pageTitle ?? 'Admin Dashboard';
$activeNav = $activeNav ?? 'dashboard';
$flash = get_flash_message();
$adminUser = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - RekeningKu Admin</title>

    <!-- Google Fonts: Manrope, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-shell">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div>
            <div class="sidebar-header">
                <a href="dashboard.php" class="brand-wrapper">
                    <div class="brand-logo-icon">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div class="brand-title-group">
                        <h2>RekeningKu</h2>
                        <span>Admin Panel</span>
                    </div>
                </a>
                <span class="role-badge">Pro</span>
            </div>

            <nav class="sidebar-nav">
                <div>
                    <span class="nav-section-title">Menu Utama</span>
                    <ul class="nav-list">
                        <li>
                            <a href="dashboard.php" class="nav-link <?= $activeNav === 'dashboard' ? 'active' : '' ?>">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="rekening.php" class="nav-link <?= $activeNav === 'rekening' ? 'active' : '' ?>">
                                <i class="fa-solid fa-building-columns"></i>
                                <span>Kelola Rekening</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <span class="nav-section-title">Sistem</span>
                    <ul class="nav-list">
                        <li>
                            <a href="settings.php" class="nav-link <?= $activeNav === 'settings' ? 'active' : '' ?>">
                                <i class="fa-solid fa-gear"></i>
                                <span>Setting</span>
                            </a>
                        </li>
                        <li>
                            <a href="logout.php" class="nav-link" style="color: var(--danger);">
                                <i class="fa-solid fa-right-from-bracket" style="color: var(--danger);"></i>
                                <span>Keluar / Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile-card">
                <div class="user-avatar">
                    <?= strtoupper(substr(e($adminUser), 0, 2)) ?>
                </div>
                <div class="user-meta">
                    <div class="user-name"><?= e($adminUser) ?></div>
                    <div class="user-status">
                        <i class="fa-solid fa-circle" style="font-size: 8px;"></i>
                        <span>Online</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 4px;">
                    <a href="settings.php" class="btn-icon-setting <?= $activeNav === 'settings' ? 'active' : '' ?>" title="Setting Akun">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    <a href="logout.php" class="btn-icon-logout" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile Drawer Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main Container -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div style="display: flex; align-items: center; gap: 14px;">
                <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle Navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-breadcrumbs">
                    <a href="dashboard.php">Dashboard</a>
                    <span>/</span>
                    <span class="current"><?= e($pageTitle) ?></span>
                </div>
            </div>

            <div class="topbar-actions">
                <a href="../" target="_blank" class="btn-link-public">
                    <i class="fa-solid fa-globe" style="color: var(--success);"></i>
                    <span>Tampilan Publik</span>
                </a>
                <a href="settings.php" class="btn-secondary" title="Setting Akun">
                    <i class="fa-solid fa-gear"></i>
                    <span class="topbar-btn-text">Setting</span>
                </a>
                <a href="rekening-add.php" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Rekening</span>
                </a>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="admin-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
                    <i class="fa-solid fa-circle-<?= $flash['type'] === 'success' ? 'check' : 'exclamation' ?>"></i>
                    <span><?= e($flash['message']) ?></span>
                </div>
            <?php endif; ?>
