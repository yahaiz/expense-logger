<?php
/**
 * Reports & Analytics Page - report.php
 * ExpenseLogger - خرج‌نگار
 * Dynamic charts and CSV export functionality
 */

require_once __DIR__ . '/config/init.php';

$pageTitle = 'Reports & Analytics';
$db = Database::getInstance();

// Default date range (last 30 days)
$dateFrom = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$dateTo = $_GET['date_to'] ?? date('Y-m-d');
$selectedCategories = $_GET['categories'] ?? [];

// Get all categories
$allCategories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Build query based on filters
$params = [$dateFrom, $dateTo];
$categoryFilter = '';

if (!empty($selectedCategories) && is_array($selectedCategories)) {
    $placeholders = implode(',', array_fill(0, count($selectedCategories), '?'));
    $categoryFilter = " AND e.category_id IN ($placeholders)";
    $params = array_merge($params, $selectedCategories);
}

// Get summary statistics
$summaryQuery = "SELECT 
    COUNT(*) as total_expenses,
    COALESCE(SUM(amount), 0) as total_amount,
    COALESCE(AVG(amount), 0) as avg_amount,
    COALESCE(MAX(amount), 0) as max_amount,
    COALESCE(MIN(amount), 0) as min_amount
FROM expenses e 
WHERE e.date >= ? AND e.date <= ? $categoryFilter";

$summary = $db->query($summaryQuery, $params)->fetch();

// Get expenses by category
$categoryQuery = "SELECT c.name, COALESCE(SUM(e.amount), 0) as total, COUNT(e.id) as count
FROM categories c 
LEFT JOIN expenses e ON c.id = e.category_id 
WHERE e.date >= ? AND e.date <= ? $categoryFilter
GROUP BY c.id, c.name 
HAVING total > 0
ORDER BY total DESC";

$categoryData = $db->query($categoryQuery, $params)->fetchAll();

// Get daily expenses for line chart
$dailyQuery = "SELECT date, COALESCE(SUM(amount), 0) as total
FROM expenses e
WHERE date >= ? AND date <= ? $categoryFilter
GROUP BY date
ORDER BY date ASC";

$dailyData = $db->query($dailyQuery, $params)->fetchAll();

// Fill missing dates
$dateRange = [];
$currentDate = new DateTime($dateFrom);
$endDate = new DateTime($dateTo);

while ($currentDate <= $endDate) {
    $dateStr = $currentDate->format('Y-m-d');
    $dateRange[$dateStr] = 0;
    $currentDate->modify('+1 day');
}

foreach ($dailyData as $day) {
    $dateRange[$day['date']] = $day['total'];
}

// Get top expenses
$topExpensesQuery = "SELECT e.*, c.name as category_name
FROM expenses e
JOIN categories c ON e.category_id = c.id
WHERE e.date >= ? AND e.date <= ? $categoryFilter
ORDER BY e.amount DESC
LIMIT 10";

$topExpenses = $db->query($topExpensesQuery, $params)->fetchAll();

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $exportQuery = "SELECT e.date, c.name as category, e.amount, e.note
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.date >= ? AND e.date <= ? $categoryFilter
    ORDER BY e.date DESC";
    
    $exportData = $db->query($exportQuery, $params)->fetchAll();
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="expense_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date', 'Category', 'Amount', 'Note']);
    
    foreach ($exportData as $row) {
        fputcsv($output, [
            $row['date'],
            $row['category'],
            $row['amount'],
            $row['note']
        ]);
    }
    
    fclose($output);
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6 animate-slide-in">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-base-content">
                <i class="fas fa-chart-line text-purple-600"></i> Reports & Analytics
            </h1>
            <p class="text-base-content/70 mt-1">Visualize and analyze your spending patterns</p>
        </div>
        <button onclick="exportCSV()" class="btn btn-success">
            <i class="fas fa-download"></i> Export CSV
        </button>
    </div>

    <!-- Filters -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg mb-4">
                <i class="fas fa-filter text-blue-600"></i> Report Filters
            </h2>
            <form method="GET" action="report.php" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Date From -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Date From</span>
                        </label>
                        <?php if ($_SESSION['calendar'] === 'jalali'): ?>
                        <input type="text" id="report_date_from_display" class="input input-bordered" 
                               placeholder="Select Date" readonly required>
                        <input type="hidden" name="date_from" id="report_date_from" 
                               value="<?php echo h($dateFrom); ?>">
                        <?php else: ?>
                        <input type="date" name="date_from" class="input input-bordered" 
                               value="<?php echo h($dateFrom); ?>" required>
                        <?php endif; ?>
                    </div>

                    <!-- Date To -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Date To</span>
                        </label>
                        <?php if ($_SESSION['calendar'] === 'jalali'): ?>
                        <input type="text" id="report_date_to_display" class="input input-bordered" 
                               placeholder="Select Date" readonly required>
                        <input type="hidden" name="date_to" id="report_date_to" 
                               value="<?php echo h($dateTo); ?>">
                        <?php else: ?>
                        <input type="date" name="date_to" class="input input-bordered" 
                               value="<?php echo h($dateTo); ?>" required>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Presets -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Quick Select</span>
                        </label>
                        <select class="select select-bordered" onchange="setDateRange(this.value)">
                            <option value="">Custom Range</option>
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 3 Months</option>
                            <option value="365">Last Year</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="form-control mt-4">
                    <label class="label">
                        <span class="label-text">Categories (leave empty for all)</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($allCategories as $cat): ?>
                            <label class="label cursor-pointer gap-2 border rounded-lg px-3 py-2 hover:bg-base-200">
                                <input type="checkbox" name="categories[]" value="<?php echo $cat['id']; ?>"
                                       class="checkbox checkbox-primary checkbox-sm"
                                       <?php echo in_array($cat['id'], $selectedCategories) ? 'checked' : ''; ?>>
                                <span class="label-text"><?php echo h($cat['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Generate Report
                    </button>
                    <a href="report.php" class="btn btn-ghost">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="stats shadow bg-gradient-to-br from-purple-500 to-purple-700 text-white">
            <div class="stat">
                <div class="stat-title text-white/90">Total Amount</div>
                <div class="stat-value text-2xl"><?php echo formatCurrency($summary['total_amount']); ?></div>
            </div>
        </div>

        <div class="stats shadow bg-gradient-to-br from-blue-500 to-blue-700 text-white">
            <div class="stat">
                <div class="stat-title text-white/90">Total Expenses</div>
                <div class="stat-value text-2xl"><?php echo $summary['total_expenses']; ?></div>
            </div>
        </div>

        <div class="stats shadow bg-gradient-to-br from-emerald-500 to-emerald-700 text-white">
            <div class="stat">
                <div class="stat-title text-white/90">Average</div>
                <div class="stat-value text-2xl"><?php echo formatCurrency($summary['avg_amount']); ?></div>
            </div>
        </div>

        <div class="stats shadow bg-gradient-to-br from-orange-500 to-orange-700 text-white">
            <div class="stat">
                <div class="stat-title text-white/90">Highest</div>
                <div class="stat-value text-2xl"><?php echo formatCurrency($summary['max_amount']); ?></div>
            </div>
        </div>

        <div class="stats shadow bg-gradient-to-br from-pink-500 to-pink-700 text-white">
            <div class="stat">
                <div class="stat-title text-white/90">Lowest</div>
                <div class="stat-value text-2xl"><?php echo formatCurrency($summary['min_amount']); ?></div>
            </div>
        </div>
    </div>

    <?php if ($summary['total_expenses'] > 0): ?>
        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pie Chart - Category Distribution -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie text-purple-600"></i>
                        Spending by Category
                    </h2>
                    <div style="height: 350px;">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bar Chart - Category Comparison -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-chart-bar text-blue-600"></i>
                        Category Comparison
                    </h2>
                    <div style="height: 350px;">
                        <canvas id="categoryBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Chart - Daily Trend -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-chart-line text-emerald-600"></i>
                    Daily Spending Trend
                </h2>
                <div style="height: 300px;">
                    <canvas id="dailyLineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Expenses & Category Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top 10 Expenses -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-trophy text-yellow-600"></i>
                        Top 10 Expenses
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topExpenses as $index => $expense): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo formatDate($expense['date'], 'M d'); ?></td>
                                        <td><span class="badge badge-sm"><?php echo h($expense['category_name']); ?></span></td>
                                        <td class="font-bold"><?php echo formatCurrency($expense['amount']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-list text-purple-600"></i>
                        Category Breakdown
                    </h2>
                    <div class="space-y-3">
                        <?php 
                        $totalAmount = array_sum(array_column($categoryData, 'total'));
                        foreach ($categoryData as $cat): 
                            $percentage = $totalAmount > 0 ? ($cat['total'] / $totalAmount) * 100 : 0;
                        ?>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-semibold"><?php echo h($cat['name']); ?></span>
                                    <span>$<?php echo number_format($cat['total'], 2); ?> (<?php echo number_format($percentage, 1); ?>%)</span>
                                </div>
                                <progress class="progress progress-primary w-full" value="<?php echo $percentage; ?>" max="100"></progress>
                                <div class="text-xs text-base-content/70"><?php echo $cat['count']; ?> expense(s)</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- No Data Message -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center py-12">
                <i class="fas fa-chart-line text-6xl text-base-content/30 mb-4"></i>
                <h2 class="text-2xl font-bold mb-2">No Data Available</h2>
                <p class="text-base-content/70 mb-4">
                    No expenses found for the selected date range and categories.
                </p>
                <div class="flex gap-2 justify-center">
                    <a href="expenses.php?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Expense
                    </a>
                    <button onclick="document.querySelector('form').reset(); document.querySelector('form').submit();" 
                            class="btn btn-ghost">
                        <i class="fas fa-redo"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize Date Pickers for Report
$(document).ready(function() {
    const isJalali = <?php echo $_SESSION['calendar'] === 'jalali' ? 'true' : 'false'; ?>;
    
    if (isJalali) {
        const pickerConfig = {
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: true,
            calendar: {
                persian: {
                    locale: 'en'
                }
            },
            toolbox: {
                enabled: false
            }
        };
        
        // Report date from
        $('#report_date_from_display').persianDatepicker({
            ...pickerConfig,
            altField: '#report_date_from',
            altFormat: 'YYYY-MM-DD',
            altFieldFormatter: function(unixTime) {
                return new persianDate(unixTime).toCalendar('gregorian').format('YYYY-MM-DD');
            }
        });
        
        // Report date to
        $('#report_date_to_display').persianDatepicker({
            ...pickerConfig,
            altField: '#report_date_to',
            altFormat: 'YYYY-MM-DD',
            altFieldFormatter: function(unixTime) {
                return new persianDate(unixTime).toCalendar('gregorian').format('YYYY-MM-DD');
            }
        });
        
        // Set initial values
        $('#report_date_from_display').val(new persianDate(new Date('<?php echo $dateFrom; ?>')).format('YYYY/MM/DD'));
        $('#report_date_to_display').val(new persianDate(new Date('<?php echo $dateTo; ?>')).format('YYYY/MM/DD'));
    }
});

// Quick date range selector
function setDateRange(days) {
    if (days === '') return;
    
    const isJalali = <?php echo $_SESSION['calendar'] === 'jalali' ? 'true' : 'false'; ?>;
    
    if (isJalali) {
        const dateTo = new persianDate();
        let dateFrom = new persianDate();
        
        if (days === 'all') {
            dateFrom = new persianDate([1380, 1, 1]);
        } else {
            dateFrom.subtract('day', parseInt(days));
        }
        
        $('#report_date_from_display').val(dateFrom.format('YYYY/MM/DD'));
        $('#report_date_from').val(dateFrom.toCalendar('gregorian').format('YYYY-MM-DD'));
        
        $('#report_date_to_display').val(dateTo.format('YYYY/MM/DD'));
        $('#report_date_to').val(dateTo.toCalendar('gregorian').format('YYYY-MM-DD'));
    } else {
        const dateTo = new Date();
        let dateFrom = new Date();
        
        if (days === 'all') {
            dateFrom = new Date('2000-01-01');
        } else {
            dateFrom.setDate(dateTo.getDate() - parseInt(days));
        }
        
        document.querySelector('input[name="date_from"]').value = dateFrom.toISOString().split('T')[0];
        document.querySelector('input[name="date_to"]').value = dateTo.toISOString().split('T')[0];
    }
}

// Export CSV
function exportCSV() {
    const form = document.getElementById('filterForm');
    const params = new URLSearchParams(new FormData(form));
    params.append('export', 'csv');
    window.location.href = 'report.php?' + params.toString();
}

<?php if ($summary['total_expenses'] > 0): ?>
const currencySymbol = '<?php 
$currency = $_SESSION['currency'] ?? 'USD';
if ($currency === 'تومان') echo 'ت';
elseif ($currency === 'هزار تومان') echo 'هزار ت';
else echo '$';
?>';
const isToman = currencySymbol === 'ت' || currencySymbol === 'هزار ت';
const factor = <?php 
$currency = $_SESSION['currency'] ?? 'USD';
if ($currency === 'هزار تومان') echo 1000;
elseif ($currency === 'USD') echo 100000;
else echo 1;
?>;

// Category Pie Chart
const categoryPieData = {
    labels: <?php echo json_encode(array_column($categoryData, 'name')); ?>,
    datasets: [{
        data: <?php echo json_encode(array_column($categoryData, 'total')); ?>.map(v => v / factor),
        backgroundColor: [
            'rgba(139, 92, 246, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(236, 72, 153, 0.8)',
        ],
        borderWidth: 2
    }]
};

new Chart(document.getElementById('categoryPieChart'), {
    type: 'doughnut',
    data: categoryPieData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + currencySymbol + (isToman ? context.parsed.toFixed(0) : context.parsed.toFixed(2));
                    }
                }
            }
        }
    }
});

// Category Bar Chart
const categoryBarData = {
    labels: <?php echo json_encode(array_column($categoryData, 'name')); ?>,
    datasets: [{
        label: 'Total Amount',
        data: <?php echo json_encode(array_column($categoryData, 'total')); ?>.map(v => v / factor),
        backgroundColor: 'rgba(59, 130, 246, 0.7)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        borderRadius: 6
    }]
};

new Chart(document.getElementById('categoryBarChart'), {
    type: 'bar',
    data: categoryBarData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return currencySymbol + (isToman ? context.parsed.y.toFixed(0) : context.parsed.y.toFixed(2));
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return currencySymbol + (isToman ? value.toFixed(0) : value.toFixed(2));
                    }
                }
            }
        }
    }
});

// Daily Line Chart
const dailyLineData = {
    labels: <?php echo json_encode(array_map(function($date) {
        return formatDate($date, 'M d');
    }, array_keys($dateRange))); ?>,
    datasets: [{
        label: 'Daily Expenses',
        data: <?php echo json_encode(array_values($dateRange)); ?>.map(v => v / factor),
        borderColor: 'rgba(16, 185, 129, 1)',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true,
        borderWidth: 3,
        pointRadius: 4,
        pointHoverRadius: 6
    }]
};

new Chart(document.getElementById('dailyLineChart'), {
    type: 'line',
    data: dailyLineData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return currencySymbol + (isToman ? context.parsed.y.toFixed(0) : context.parsed.y.toFixed(2));
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return currencySymbol + (isToman ? value.toFixed(0) : value.toFixed(2));
                    }
                }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
