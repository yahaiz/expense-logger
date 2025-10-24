<!DOCTYPE html>
<html lang="en" data-theme="sunset">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ExpenseLogger - خرج‌نگار</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.003/Vazirmatn-font-face.css">
    <style>
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
        /* Custom DaisyUI Theme - Sunset */
        [data-theme="sunset"] {
            color-scheme: dark;
            --color-base-100: oklch(22% 0.019 237.69);
            --color-base-200: oklch(20% 0.019 237.69);
            --color-base-300: oklch(18% 0.019 237.69);
            --color-base-content: oklch(77.383% 0.043 245.096);
            --color-primary: oklch(74.703% 0.158 39.947);
            --color-primary-content: oklch(14.94% 0.031 39.947);
            --color-secondary: oklch(72.537% 0.177 2.72);
            --color-secondary-content: oklch(14.507% 0.035 2.72);
            --color-accent: oklch(71.294% 0.166 299.844);
            --color-accent-content: oklch(14.258% 0.033 299.844);
            --color-neutral: oklch(26% 0.019 237.69);
            --color-neutral-content: oklch(70% 0.019 237.69);
            --color-info: oklch(85.559% 0.085 206.015);
            --color-info-content: oklch(17.111% 0.017 206.015);
            --color-success: oklch(85.56% 0.085 144.778);
            --color-success-content: oklch(17.112% 0.017 144.778);
            --color-warning: oklch(85.569% 0.084 74.427);
            --color-warning-content: oklch(17.113% 0.016 74.427);
            --color-error: oklch(85.511% 0.078 16.886);
            --color-error-content: oklch(17.102% 0.015 16.886);
            --rounded-box: 2rem;
            --rounded-btn: 1rem;
            --rounded-badge: 2rem;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-500 via-blue-500 to-emerald-500">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Welcome Card -->
            <div class="card bg-base-100 shadow-2xl mb-6 animate-fade-in">
                <div class="card-body">
                    <div class="text-center mb-6">
                        <div class="inline-block p-4 bg-purple-600 rounded-full mb-4">
                            <i class="fas fa-wallet text-white text-5xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold mb-2">Welcome to ExpenseLogger</h1>
                        <h2 class="text-2xl text-base-content/70" lang="fa">خرج‌نگار</h2>
                        <p class="text-lg mt-4 text-base-content/80">
                            Your Personal Offline Expense Manager
                        </p>
                    </div>

                    <div class="alert alert-success">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Installation Successful! 🎉</h3>
                            <p>Your expense tracking application is ready to use.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Start Steps -->
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <!-- Step 1 -->
                <div class="card bg-base-100 shadow-xl animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="card-body">
                        <div class="badge badge-primary badge-lg mb-2">Step 1</div>
                        <h3 class="card-title">
                            <i class="fas fa-database text-blue-600"></i>
                            Add Sample Data
                        </h3>
                        <p class="text-sm">
                            Load 23 sample expenses to explore all features and see how charts look with data.
                        </p>
                        <div class="card-actions justify-end">
                            <a href="demo_data.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Load Sample Data
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="card bg-base-100 shadow-xl animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="card-body">
                        <div class="badge badge-secondary badge-lg mb-2">Step 2</div>
                        <h3 class="card-title">
                            <i class="fas fa-chart-line text-emerald-600"></i>
                            View Dashboard
                        </h3>
                        <p class="text-sm">
                            See your expense overview, charts, and recent transactions at a glance.
                        </p>
                        <div class="card-actions justify-end">
                            <a href="index.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-home"></i> Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="card bg-base-100 shadow-xl animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="card-body">
                        <div class="badge badge-accent badge-lg mb-2">Step 3</div>
                        <h3 class="card-title">
                            <i class="fas fa-receipt text-purple-600"></i>
                            Manage Expenses
                        </h3>
                        <p class="text-sm">
                            Add, edit, delete, and filter your expenses with powerful search features.
                        </p>
                        <div class="card-actions justify-end">
                            <a href="expenses.php" class="btn btn-accent btn-sm">
                                <i class="fas fa-list"></i> Manage Expenses
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="card bg-base-100 shadow-xl animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="card-body">
                        <div class="badge badge-warning badge-lg mb-2">Step 4</div>
                        <h3 class="card-title">
                            <i class="fas fa-book text-orange-600"></i>
                            Read Documentation
                        </h3>
                        <p class="text-sm">
                            Learn about all features, best practices, and troubleshooting tips.
                        </p>
                        <div class="card-actions justify-end">
                            <a href="README.md" class="btn btn-warning btn-sm" target="_blank">
                                <i class="fas fa-book-open"></i> View README
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Overview -->
            <div class="card bg-base-100 shadow-xl mb-6 animate-fade-in" style="animation-delay: 0.5s;">
                <div class="card-body">
                    <h2 class="card-title text-2xl mb-4">
                        <i class="fas fa-star text-yellow-500"></i>
                        Key Features
                    </h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-chart-pie text-purple-600 text-xl mt-1"></i>
                            <div>
                                <h4 class="font-bold">Interactive Charts</h4>
                                <p class="text-sm text-base-content/70">Visualize spending with pie, bar, and line charts</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-filter text-blue-600 text-xl mt-1"></i>
                            <div>
                                <h4 class="font-bold">Advanced Filters</h4>
                                <p class="text-sm text-base-content/70">Filter by category, date range, and search notes</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-moon text-indigo-600 text-xl mt-1"></i>
                            <div>
                                <h4 class="font-bold">Dark/Light Theme</h4>
                                <p class="text-sm text-base-content/70">Toggle between themes with persistent storage</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-download text-emerald-600 text-xl mt-1"></i>
                            <div>
                                <h4 class="font-bold">Backup & Export</h4>
                                <p class="text-sm text-base-content/70">Export to JSON or CSV, import backups easily</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-mobile-alt text-pink-600 text-xl mt-1"></i>
                            <div>
                                <h4 class="font-bold">Fully Responsive</h4>
                                <p class="text-sm text-base-content/70">Works perfectly on desktop, tablet, and mobile</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fas fa-shield-alt text-red-600 text-xl mt-1"></i>
                            <div>
                                <h4 class="font-bold">Secure & Private</h4>
                                <p class="text-sm text-base-content/70">100% offline, your data stays on your device</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Links -->
            <div class="card bg-base-100 shadow-xl animate-fade-in" style="animation-delay: 0.6s;">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-question-circle text-info"></i>
                        Need Help?
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="INSTALL.md" target="_blank" class="btn btn-sm btn-outline">
                            <i class="fas fa-cog"></i> Installation Guide
                        </a>
                        <a href="FEATURES.md" target="_blank" class="btn btn-sm btn-outline">
                            <i class="fas fa-list-check"></i> Feature List
                        </a>
                        <a href="README.md" target="_blank" class="btn btn-sm btn-outline">
                            <i class="fas fa-book"></i> Full Documentation
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-white">
                <p class="text-lg font-semibold">ExpenseLogger - <span lang="fa">خرج‌نگار</span></p>
                <p class="text-sm opacity-80">Personal Expense Tracking Made Simple</p>
                <p class="text-xs opacity-60 mt-2">© <?php require_once 'config/init.php'; echo jdate('Y'); ?> - All Rights Reserved</p>
            </div>
        </div>
    </div>
</body>
</html>
