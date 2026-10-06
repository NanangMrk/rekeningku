<?php
/**
 * RekeningKu - Admin Logout Endpoint
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

logout_admin();

$adminBase = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/admin'), '/\\') . '/';
redirect($adminBase . 'index.php');
