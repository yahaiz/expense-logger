<?php
/**
 * Dashboard Page - index.php
 * ExpenseLogger - خرج‌نگار
 * Main dashboard showing expense summary and charts
 */

require_once __DIR__ . '/config/init.php';

$pageTitle = 'Dashboard';
$db = Database::getInstance();

// Get total expenses
$totalResult = $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses")->fetch();
$totalExpenses = $totalResult['total'];

// Get expense count
$countResult = $db->query("SELECT COUNT(*) as count FROM expenses")->fetch();
$expenseCount = $countResult['count'];

// Get category count
$categoryCountResult = $db->query("SELECT COUNT(*) as count FROM categories")->fetch();
$categoryCount = $categoryCountResult['count'];

// Get current month expenses
$currentMonthResult = $db->query(
    "SELECT COALESCE(SUM(amount), 0) as total FROM expenses 
     WHERE strftime('%Y-%m', date) = strftime('%Y-%m', 'now')"
)->fetch();
$currentMonthExpenses = $currentMonthResult['total'];

// Get expenses by category for pie chart
$categoryExpenses = $db->query(
    "SELECT c.name, COALESCE(SUM(e.amount), 0) as total 
     FROM categories c 
     LEFT JOIN expenses e ON c.id = e.category_id 
     GROUP BY c.id, c.name 
     ORDER BY total DESC"
)->fetchAll();

// Get daily expenses for last 7 days for bar chart
$dailyExpenses = $db->query(
    "SELECT date, COALESCE(SUM(amount), 0) as total 
     FROM expenses 
     WHERE date >= date('now', '-6 days') 
     GROUP BY date 
     ORDER BY date ASC"
)->fetchAll();

// Fill missing dates with 0
$last7Days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $found = false;
    foreach ($dailyExpenses as $expense) {
        if ($expense['date'] == $date) {
            $last7Days[$date] = $expense['total'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $last7Days[$date] = 0;
    }
}

// Get recent expenses
$recentExpenses = $db->query(
    "SELECT e.*, c.name as category_name 
     FROM expenses e 
     JOIN categories c ON e.category_id = c.id 
     GROUP BY e.id
     ORDER BY e.date DESC, e.created_at DESC 
     LIMIT 5"
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<style>
    /* Make the currency sign smaller on the dashboard stats only */
    .stat-value .currency-sign {
        font-size: 0.7em;
        vertical-align: middle;
        opacity: 0.95;
        /* small margin so sign doesn't stick to numbers */
        margin-left: 0.35rem;
    }
    /* If Toman appears after the number, push it slightly to the left */
    .stat-value .currency-toman {
        margin-left: 0.35rem;
        margin-right: 0;
    }
    /* For USD sign which is before the number, give a small right margin */
    .stat-value .currency-usd {
        margin-right: 0.35rem;
        margin-left: 0;
    }
</style>

<div class="space-y-6 animate-slide-in">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-base-content">
                <i class="fas fa-chart-pie text-purple-600"></i> Dashboard
            </h1>
            <p class="text-base-content/70 mt-1">Overview of your expenses</p>
        </div>
        <a href="expenses.php?action=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Expense
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Expenses -->
        <div class="stats shadow bg-gradient-to-br from-purple-500 to-purple-700 text-white">
            <div class="stat">
                <div class="stat-figure text-white/70">
                    <i class="fas fa-dollar-sign text-3xl"></i>
                </div>
                <div class="stat-title text-white/90">Total Expenses</div>
                <div class="stat-value"><?php echo formatCurrency($totalExpenses); ?></div>
                <div class="stat-desc text-white/70">All time</div>
            </div>
        </div>

        <!-- This Month -->
        <div class="stats shadow bg-gradient-to-br from-blue-500 to-blue-700 text-white">
            <div class="stat">
                <div class="stat-figure text-white/70">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
                <div class="stat-title text-white/90">This Month</div>
                <div class="stat-value"><?php echo formatCurrency($currentMonthExpenses); ?></div>
                <div class="stat-desc text-white/70"><?php echo formatDate(time(), 'F Y'); ?></div>
            </div>
        </div>

        <!-- Total Entries -->
        <div class="stats shadow bg-gradient-to-br from-emerald-500 to-emerald-700 text-white">
            <div class="stat">
                <div class="stat-figure text-white/70">
                    <i class="fas fa-receipt text-3xl"></i>
                </div>
                <div class="stat-title text-white/90">Total Entries</div>
                <div class="stat-value"><?php echo $expenseCount; ?></div>
                <div class="stat-desc text-white/70">Expense records</div>
            </div>
        </div>

        <!-- Categories -->
        <div class="stats shadow bg-gradient-to-br from-pink-500 to-pink-700 text-white">
            <div class="stat">
                <div class="stat-figure text-white/70">
                    <i class="fas fa-tags text-3xl"></i>
                </div>
                <div class="stat-title text-white/90">Categories</div>
                <div class="stat-value"><?php echo $categoryCount; ?></div>
                <div class="stat-desc text-white/70">Active categories</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pie Chart - Expenses by Category -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-chart-pie text-purple-600"></i>
                    Expenses by Category
                </h2>
                <div class="flex justify-center items-center" style="height: 300px;">
                    <?php if (array_sum(array_column($categoryExpenses, 'total')) > 0): ?>
                        <canvas id="categoryChart"></canvas>
                    <?php else: ?>
                        <div class="text-center text-base-content/50">
                            <i class="fas fa-chart-pie text-5xl mb-4"></i>
                            <p>No expense data available</p>
                            <p class="text-sm">Add your first expense to see the chart</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bar Chart - Last 7 Days -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar text-blue-600"></i>
                    Last 7 Days
                </h2>
                <div class="flex justify-center items-center" style="height: 300px;">
                    <?php if (array_sum($last7Days) > 0): ?>
                        <canvas id="dailyChart"></canvas>
                    <?php else: ?>
                        <div class="text-center text-base-content/50">
                            <i class="fas fa-chart-bar text-5xl mb-4"></i>
                            <p>No recent expenses</p>
                            <p class="text-sm">Add expenses to see daily trends</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h2 class="card-title">
                    <i class="fas fa-clock text-emerald-600"></i>
                    Recent Expenses
                </h2>
                <a href="expenses.php" class="btn btn-sm btn-ghost">
                    View All <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <?php if (count($recentExpenses) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentExpenses as $expense): ?>
                                <tr>
                                    <td><?php echo formatDate($expense['date'], 'd M Y'); ?></td>
                                    <td>
                                        <span class="badge badge-primary">
                                            <?php echo h($expense['category_name']); ?>
                                        </span>
                                    </td>
                                    <td class="font-semibold"><?php echo formatCurrency($expense['amount']); ?></td>
                                    <td class="truncate max-w-xs">
                                        <?php echo h($expense['note'] ?: '-'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-base-content/50">
                    <i class="fas fa-receipt text-5xl mb-4"></i>
                    <p>No expenses recorded yet</p>
                    <p class="text-sm mb-4">Start tracking your expenses today!</p>
                    <a href="expenses.php?action=add" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add First Expense
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js Scripts -->
<script>
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

// Pie Chart - Expenses by Category
<?php if (array_sum(array_column($categoryExpenses, 'total')) > 0): ?>
const categoryData = {
    labels: <?php echo json_encode(array_column($categoryExpenses, 'name')); ?>,
    datasets: [{
        data: <?php echo json_encode(array_column($categoryExpenses, 'total')); ?>.map(v => v / factor),
        backgroundColor: [
            'rgba(139, 92, 246, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(168, 85, 247, 0.8)',
        ],
        borderColor: [
            'rgba(139, 92, 246, 1)',
            'rgba(59, 130, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(245, 158, 11, 1)',
            'rgba(239, 68, 68, 1)',
            'rgba(168, 85, 247, 1)',
        ],
        borderWidth: 2
    }]
};

const categoryChart = new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: categoryData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += currencySymbol + (isToman ? context.parsed.toFixed(0) : context.parsed.toFixed(2));
                        return label;
                    }
                }
            }
        }
    }
});
<?php endif; ?>

// Bar Chart - Last 7 Days
<?php if (array_sum($last7Days) > 0): ?>
const dailyData = {
    labels: <?php echo json_encode(array_map(function($date) {
        return formatDate($date, 'M d');
    }, array_keys($last7Days))); ?>,
    datasets: [{
        label: 'Daily Expenses',
        data: <?php echo json_encode(array_values($last7Days)); ?>.map(v => v / factor),
        backgroundColor: 'rgba(59, 130, 246, 0.7)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 2,
        borderRadius: 6,
    }]
};

const dailyChart = new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: dailyData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
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
