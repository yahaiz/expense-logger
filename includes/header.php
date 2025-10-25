<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default theme if not set (sunset theme)
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'sunset';
}

// Set default currency if not set (USD)
if (!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'USD';
}

// Set default calendar type if not set (gregorian)
if (!isset($_SESSION['calendar'])) {
    $_SESSION['calendar'] = 'gregorian';
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $_SESSION['theme']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? h($pageTitle) . ' - ' : ''; ?>ExpenseLogger</title>
    
    <!-- TailwindCSS + DaisyUI -->
    <link href="assets/css/daisyui.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/tailwindcss.min.js"></script>
    
    <!-- Chart.js -->
    <script src="assets/js/chart.umd.min.js"></script>
    
    <!-- jQuery (required for Persian DatePicker) -->
    <script src="assets/js/jquery-3.7.1.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E💰%3C/text%3E%3C/svg%3E">
    
    <!-- Vazirmatn Font -->
    <link rel="stylesheet" href="assets/css/vazirmatn-font-face.css">
    
    <!-- Persian Date Picker -->
    <script src="assets/js/persian-date.min.js"></script>
    <script src="assets/js/persian-datepicker.min.js"></script>
    <link rel="stylesheet" href="assets/css/persian-datepicker.min.css">
    
    <style>
        /* Fix DatePicker z-index above modal */
        .datepicker-plot-area {
            z-index: 999999 !important;
            position: fixed !important;
        }
        
        /* Prevent modal from clipping datepicker */
        dialog.modal {
            overflow: visible !important;
        }
        
        .modal-box {
            overflow: visible !important;
        }
        
        /* Custom font for تومان currency symbol */
        @font-face {
            font-family: 'ProFont_Fekrah';
            src: url('assets/ProFont_Fekrah.otf') format('opentype');
        }
        
        .currency-toman {
            font-family: 'ProFont_Fekrah', sans-serif;
        }
        
        /* Apply Vazirmatn to Farsi text */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        
        [lang="fa"], .farsi {
            font-family: Vazirmatn, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        
        /* ========================================
           THEME STYLES - Auto-generated from themes.php
           ======================================== */
<?php 
require_once __DIR__ . '/../config/theme-generator.php';
echo generateThemeCSS();
?>
        
        /* Custom animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: oklch(20% 0.019 237.69);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, 
                oklch(74.703% 0.158 39.947) 0%, 
                oklch(72.537% 0.177 2.72) 50%,
                oklch(71.294% 0.166 299.844) 100%);
            border-radius: 10px;
            border: 2px solid oklch(20% 0.019 237.69);
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, 
                oklch(80% 0.18 39.947) 0%, 
                oklch(78% 0.19 2.72) 50%,
                oklch(76% 0.18 299.844) 100%);
        }
        
        ::-webkit-scrollbar-thumb:active {
            background: linear-gradient(180deg, 
                oklch(70% 0.15 39.947) 0%, 
                oklch(68% 0.16 2.72) 50%,
                oklch(66% 0.15 299.844) 100%);
        }
        
        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: oklch(74.703% 0.158 39.947) oklch(20% 0.019 237.69);
        }
        
        /* Dock styling */
        .dock {
            display: flex;
            align-items: end;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: oklch(var(--b2));
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            border: 1px solid oklch(var(--b3));
        }
        
        .dock-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            color: oklch(var(--bc));
            transition: all 0.2s ease-in-out;
            position: relative;
        }
        
        .dock-item:hover {
            background: oklch(var(--b3));
            transform: scale(1.1);
        }
        
        .dock-active {
            background: oklch(var(--p));
            color: oklch(var(--pc));
            transform: scale(1.2);
        }
        
        .dock-active:hover {
            transform: scale(1.25);
        }
        
        /* Disable dragging and text selection globally */
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-user-drag: none;
            -moz-user-drag: none;
            -ms-user-drag: none;
            user-drag: none;
            -webkit-touch-callout: none;
        }
        
        /* Allow text selection in input fields and textareas */
        input, textarea, [contenteditable="true"] {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
        
        /* Allow dragging for specific interactive elements if needed */
        .dock-item, .btn, a, button {
            cursor: pointer;
        }
        
    </style>
</head>
<body class="min-h-screen bg-base-200 pt-20">
    <!-- Fixed Top Navigation -->
    <div class="navbar bg-base-100 fixed top-0 left-0 right-0 z-50 border-b border-base-300">
        <div class="navbar-start">
            <a href="index.php" class="btn btn-ghost text-xl">
                <i class="fas fa-wallet text-primary"></i>
                ExpenseLogger
            </a>
        </div>
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal bg-base-200 rounded-box gap-2">
                <li>
                    <a href="index.php" class="<?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="expenses.php" class="<?php echo $currentPage == 'expenses.php' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Expenses
                    </a>
                </li>
                <li>
                    <a href="categories.php" class="<?php echo $currentPage == 'categories.php' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Categories
                    </a>
                </li>
                <li>
                    <a href="report.php" class="<?php echo $currentPage == 'report.php' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reports
                    </a>
                </li>
                <li>
                    <a href="backup.php" class="<?php echo $currentPage == 'backup.php' ? 'active' : ''; ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                        Backup
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-end">
            <button onclick="window.location.reload()" class="btn btn-ghost btn-sm" title="Reload">
                <i class="fas fa-sync-alt"></i>
            </button>
            <a href="lock.php" class="btn btn-ghost btn-sm" title="Lock">
                <i class="fas fa-lock"></i>
            </a>
            <a href="settings.php" class="btn btn-ghost btn-sm">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 pb-24 lg:pb-8 p-4 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Flash Messages -->
            <?php
            $flash = getFlashMessage();
            if ($flash):
            ?>
            <div class="toast toast-top toast-end z-50 animate-slide-in">
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <span><?php echo h($flash['message']); ?></span>
                </div>
            </div>
            <script>
                setTimeout(() => {
                    document.querySelector('.toast')?.remove();
                }, 3000);
            </script>
            <?php endif; ?>
            
            <!-- Page Content Starts Here -->

