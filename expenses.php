<?php
/**
 * Expenses Management Page - expenses.php
 * ExpenseLogger - خرج‌نگار
 * Full CRUD operations for expenses with filters, search, and pagination
 */

require_once __DIR__ . '/config/init.php';

$pageTitle = 'Expenses';
$db = Database::getInstance();

// Pagination settings
$itemsPerPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $amount = floatval($_POST['amount']);
        $category_id = intval($_POST['category_id']);
        // If using Jalali calendar, prefer server-side conversion from the
        // user-entered `date_display` to Gregorian to avoid JS timezone shifts.
        if (isset($_SESSION['calendar']) && $_SESSION['calendar'] === 'jalali' && !empty($_POST['date_display'])) {
            $parts = preg_split('/[\/\-]/', trim($_POST['date_display']));
            if (count($parts) === 3) {
                $jy = intval($parts[0]);
                $jm = intval($parts[1]);
                $jd = intval($parts[2]);
                list($gy, $gm, $gd) = JalaliDate::toGregorian($jy, $jm, $jd);
                $date = sprintf('%04d-%02d-%02d', $gy, $gm, $gd);
            } else {
                $date = sanitizeInput($_POST['date'] ?? '');
            }
        } else {
            $date = sanitizeInput($_POST['date'] ?? '');
        }
        $note = sanitizeInput($_POST['note'] ?? '');
        
        if ($amount > 0 && $category_id > 0 && !empty($date)) {
            try {
                $db->query(
                    "INSERT INTO expenses (amount, category_id, date, note) VALUES (?, ?, ?, ?)",
                    [$amount, $category_id, $date, $note]
                );
                setFlashMessage('success', 'Expense added successfully!');
                redirect('expenses.php');
            } catch (Exception $e) {
                setFlashMessage('error', 'Failed to add expense: ' . $e->getMessage());
            }
        } else {
            setFlashMessage('error', 'Please fill all required fields correctly.');
        }
    }
    
    if ($action === 'edit') {
        $id = intval($_POST['id']);
        $amount = floatval($_POST['amount']);
        $category_id = intval($_POST['category_id']);
        // Server-side conversion for Jalali input to avoid off-by-one errors
        if (isset($_SESSION['calendar']) && $_SESSION['calendar'] === 'jalali' && !empty($_POST['date_display'])) {
            $parts = preg_split('/[\/\-]/', trim($_POST['date_display']));
            if (count($parts) === 3) {
                $jy = intval($parts[0]);
                $jm = intval($parts[1]);
                $jd = intval($parts[2]);
                list($gy, $gm, $gd) = JalaliDate::toGregorian($jy, $jm, $jd);
                $date = sprintf('%04d-%02d-%02d', $gy, $gm, $gd);
            } else {
                $date = sanitizeInput($_POST['date'] ?? '');
            }
        } else {
            $date = sanitizeInput($_POST['date'] ?? '');
        }
        $note = sanitizeInput($_POST['note'] ?? '');
        
        if ($id > 0 && $amount > 0 && $category_id > 0 && !empty($date)) {
            try {
                $db->query(
                    "UPDATE expenses SET amount = ?, category_id = ?, date = ?, note = ? WHERE id = ?",
                    [$amount, $category_id, $date, $note, $id]
                );
                setFlashMessage('success', 'Expense updated successfully!');
                redirect('expenses.php');
            } catch (Exception $e) {
                setFlashMessage('error', 'Failed to update expense: ' . $e->getMessage());
            }
        } else {
            setFlashMessage('error', 'Invalid expense data.');
        }
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $db->query("DELETE FROM expenses WHERE id = ?", [$id]);
        setFlashMessage('success', 'Expense deleted successfully!');
        redirect('expenses.php');
    } catch (Exception $e) {
        setFlashMessage('error', 'Failed to delete expense: ' . $e->getMessage());
    }
}

// Get categories for filter and form
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Build filter query
$whereConditions = [];
$params = [];

// Category filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $whereConditions[] = "e.category_id = ?";
    $params[] = intval($_GET['category']);
}

// Date range filter
if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $whereConditions[] = "e.date >= ?";
    $params[] = $_GET['date_from'];
}
if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $whereConditions[] = "e.date <= ?";
    $params[] = $_GET['date_to'];
}

// Search filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $whereConditions[] = "e.note LIKE ?";
    $params[] = '%' . $_GET['search'] . '%';
}

$whereClause = count($whereConditions) > 0 ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as count FROM expenses e $whereClause";
$totalItems = $db->query($countQuery, $params)->fetch()['count'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Get expenses with filters
$query = "SELECT e.*, c.name as category_name 
          FROM expenses e 
          JOIN categories c ON e.category_id = c.id 
          $whereClause 
          GROUP BY e.id
          ORDER BY e.date DESC, e.created_at DESC 
          LIMIT ? OFFSET ?";
$params[] = $itemsPerPage;
$params[] = $offset;
$expenses = $db->query($query, $params)->fetchAll();

// Add jalali date for each expense if calendar is jalali
if ($_SESSION['calendar'] === 'jalali') {
    foreach ($expenses as &$expense) {
        $expense['jalali_date'] = formatDate($expense['date'], 'Y/m/d');
    }
    unset($expense);
}

// Get expense for editing if action=edit
$editExpense = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editId = intval($_GET['id']);
    $editExpense = $db->query("SELECT * FROM expenses WHERE id = ?", [$editId])->fetch();
    if ($editExpense && $_SESSION['calendar'] === 'jalali') {
        $editExpense['jalali_date'] = formatDate($editExpense['date'], 'Y/m/d');
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6 animate-slide-in">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-base-content">
                <i class="fas fa-receipt text-purple-600"></i> Expenses Management
            </h1>
            <p class="text-base-content/70 mt-1">Track and manage your expenses</p>
        </div>
        <button onclick="showAddModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Expense
        </button>
    </div>

    <!-- Filters -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-lg mb-4">
                <i class="fas fa-filter text-blue-600"></i> Filters
            </h2>
            <form method="GET" action="expenses.php" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Category Filter -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category</span>
                    </label>
                    <select name="category" class="select select-bordered">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo h($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date From -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Date From</span>
                    </label>
                    <?php if ($_SESSION['calendar'] === 'jalali'): ?>
                    <input type="text" id="filter_date_from_display" class="input input-bordered" 
                           placeholder="Select Date" readonly>
                    <input type="hidden" name="date_from" id="filter_date_from" 
                           value="<?php echo h($_GET['date_from'] ?? ''); ?>">
                    <?php else: ?>
                    <input type="date" name="date_from" class="input input-bordered" 
                           value="<?php echo h($_GET['date_from'] ?? ''); ?>">
                    <?php endif; ?>
                </div>

                <!-- Date To -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Date To</span>
                    </label>
                    <?php if ($_SESSION['calendar'] === 'jalali'): ?>
                    <input type="text" id="filter_date_to_display" class="input input-bordered" 
                           placeholder="Select Date" readonly>
                    <input type="hidden" name="date_to" id="filter_date_to" 
                           value="<?php echo h($_GET['date_to'] ?? ''); ?>">
                    <?php else: ?>
                    <input type="date" name="date_to" class="input input-bordered" 
                           value="<?php echo h($_GET['date_to'] ?? ''); ?>">
                    <?php endif; ?>
                </div>

                <!-- Search -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Search Notes</span>
                    </label>
                    <input type="text" name="search" placeholder="Search in notes..." 
                           class="input input-bordered" value="<?php echo h($_GET['search'] ?? ''); ?>">
                </div>

                <!-- Buttons -->
                <div class="col-span-full flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Apply Filters
                    </button>
                    <a href="expenses.php" class="btn btn-ghost">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses List -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title mb-4">
                <i class="fas fa-list text-emerald-600"></i> 
                Expenses List 
                <span class="badge badge-primary"><?php echo $totalItems; ?> total</span>
            </h2>

            <?php if (count($expenses) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Note</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td><?php echo formatDate($expense['date'], 'd M Y'); ?></td>
                                    <td>
                                        <span class="badge badge-primary badge-lg">
                                            <?php echo h($expense['category_name']); ?>
                                        </span>
                                    </td>
                                    <td class="font-bold text-lg"><?php echo formatCurrency($expense['amount']); ?></td>
                                    <td class="max-w-xs truncate">
                                        <?php echo h($expense['note'] ?: '-'); ?>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button onclick='editExpense(<?php echo json_encode($expense); ?>)' 
                                                    class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmDelete(<?php echo $expense['id']; ?>)" 
                                                    class="btn btn-sm btn-error">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center mt-6">
                        <div class="join">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from=' . $_GET['date_from'] : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to=' . $_GET['date_to'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                                   class="join-item btn">«</a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPages || abs($i - $page) <= 2): ?>
                                    <a href="?page=<?php echo $i; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from=' . $_GET['date_from'] : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to=' . $_GET['date_to'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                                       class="join-item btn <?php echo $i == $page ? 'btn-active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php elseif (abs($i - $page) == 3): ?>
                                    <button class="join-item btn btn-disabled">...</button>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['category']) ? '&category=' . $_GET['category'] : ''; ?><?php echo isset($_GET['date_from']) ? '&date_from=' . $_GET['date_from'] : ''; ?><?php echo isset($_GET['date_to']) ? '&date_to=' . $_GET['date_to'] : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                                   class="join-item btn">»</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-12 text-base-content/50">
                    <i class="fas fa-receipt text-6xl mb-4"></i>
                    <p class="text-xl">No expenses found</p>
                    <p class="text-sm mb-4">Start adding your expenses or adjust your filters</p>
                    <button onclick="showAddModal()" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Expense
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Expense Modal -->
<dialog id="expenseModal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4" id="modalTitle">
            <i class="fas fa-plus-circle text-purple-600"></i> Add New Expense
        </h3>
        
        <form method="POST" action="expenses.php" id="expenseForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="expenseId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Amount -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Amount <span class="text-error">*</span></span>
                    </label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                           class="input input-bordered" placeholder="0.00" required>
                </div>

                <!-- Category -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Category <span class="text-error">*</span></span>
                    </label>
                    <select name="category_id" id="category_id" class="select select-bordered" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo h($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Date <span class="text-error">*</span></span>
                    </label>
                    <div class="relative">
               <input type="<?php echo $_SESSION['calendar'] === 'jalali' ? 'text' : 'date'; ?>" 
                   name="<?php echo $_SESSION['calendar'] === 'jalali' ? 'date_display' : 'date'; ?>" 
                   id="<?php echo $_SESSION['calendar'] === 'jalali' ? 'date_display' : 'date'; ?>" 
                   class="input input-bordered w-full" 
                   <?php if ($_SESSION['calendar'] === 'jalali'): ?>
                   placeholder="YYYY/MM/DD"
                   <?php else: ?>
                   value="<?php echo date('Y-m-d'); ?>"
                   <?php endif; ?>
                   required>
                        <?php if ($_SESSION['calendar'] === 'jalali'): ?>
                        <input type="hidden" name="date" id="date" value="<?php echo date('Y-m-d'); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="flex gap-1 mt-2">
                        <button type="button" class="btn btn-xs btn-outline" onclick="setDateAction('today')">Today</button>
                        <button type="button" class="btn btn-xs btn-outline" onclick="setDateAction('prev')">
                            <i class="fas fa-minus"></i> 1 Day
                        </button>
                        <button type="button" class="btn btn-xs btn-outline" onclick="setDateAction('next')">
                            <i class="fas fa-plus"></i> 1 Day
                        </button>
                    </div>
                </div>

                <!-- Note -->
                <div class="form-control md:col-span-2">
                    <label class="label">
                        <span class="label-text">Note</span>
                    </label>
                    <textarea name="note" id="note" class="textarea textarea-bordered" 
                              placeholder="Optional note about this expense" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-action">
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Expense
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<!-- Delete Confirmation Modal -->
<dialog id="deleteModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg text-error">
            <i class="fas fa-exclamation-triangle"></i> Confirm Delete
        </h3>
        <p class="py-4">Are you sure you want to delete this expense? This action cannot be undone.</p>
        <div class="modal-action">
            <button class="btn" onclick="document.getElementById('deleteModal').close()">Cancel</button>
            <a href="#" id="confirmDeleteBtn" class="btn btn-error">
                <i class="fas fa-trash"></i> Delete
            </a>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
// Initialize Date Pickers based on calendar setting
$(document).ready(function() {
    const isJalali = <?php echo $_SESSION['calendar'] === 'jalali' ? 'true' : 'false'; ?>;
    const todayGregorian = '<?php echo date('Y-m-d'); ?>';
    const todayJalali = '<?php echo $_SESSION['calendar'] === 'jalali' ? jdate('Y/m/d') : date('Y-m-d'); ?>';
    
    if (isJalali) {
        const pickerConfig = {
            format: 'YYYY/MM/DD',
            autoClose: true,
            calendar: {
                persian: {
                    locale: 'en'
                }
            },
            toolbox: {
                enabled: false
            }
        };
        
        // Note: we intentionally DO NOT attach the persianDatepicker to the modal's
        // `#date_display` input. The user requested a plain input inside the modal.
        // Instead, keep a small sync handler so when the user types a Jalali date
        // (YYYY/MM/DD or YYYY-MM-DD) we convert it to Gregorian and store it in
        // the hidden `#date` field which is what the server expects.
        $('#date_display').on('input change', function() {
            const v = $(this).val().trim();
            if (!v) { $('#date').val(''); return; }
            const parts = v.split(/[-\/]/);
            if (parts.length === 3) {
                const y = parseInt(parts[0], 10);
                const m = parseInt(parts[1], 10);
                const d = parseInt(parts[2], 10);
                if (!Number.isNaN(y) && !Number.isNaN(m) && !Number.isNaN(d)) {
                    try {
                        // persianDate accepts array [year, month, day]
                        const p = new persianDate([y, m, d]);
                        const g = p.toCalendar('gregorian').format('YYYY-MM-DD');
                        $('#date').val(g);
                    } catch (e) {
                        // ignore parse errors
                    }
                }
            }
        });
        
        // Filter date from
        $('#filter_date_from_display').persianDatepicker({
            ...pickerConfig,
            initialValue: false,
            altField: '#filter_date_from',
            altFormat: 'YYYY-MM-DD',
            altFieldFormatter: function(unixTime) {
                return new persianDate(unixTime).toCalendar('gregorian').format('YYYY-MM-DD');
            }
        });
        
        // Filter date to
        $('#filter_date_to_display').persianDatepicker({
            ...pickerConfig,
            initialValue: false,
            altField: '#filter_date_to',
            altFormat: 'YYYY-MM-DD',
            altFieldFormatter: function(unixTime) {
                return new persianDate(unixTime).toCalendar('gregorian').format('YYYY-MM-DD');
            }
        });
        
        <?php if (!empty($_GET['date_from'])): ?>
        $('#filter_date_from_display').val(new persianDate(new Date('<?php echo str_replace('-', '/', $_GET['date_from']); ?>')).format('YYYY/MM/DD'));
        <?php endif; ?>
        
        <?php if (!empty($_GET['date_to'])): ?>
        $('#filter_date_to_display').val(new persianDate(new Date('<?php echo str_replace('-', '/', $_GET['date_to']); ?>')).format('YYYY/MM/DD'));
        <?php endif; ?>
    }
});

// Ensure datepicker portal for jalali inputs
if (typeof DatepickerPortal !== 'undefined') {
    DatepickerPortal.ensure('#filter_date_from_display');
    DatepickerPortal.ensure('#filter_date_to_display');
}

function showAddModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle text-purple-600"></i> Add New Expense';
    document.getElementById('formAction').value = 'add';
    document.getElementById('expenseForm').reset();
    
    const isJalali = <?php echo $_SESSION['calendar'] === 'jalali' ? 'true' : 'false'; ?>;
    const todayGregorian = '<?php echo date('Y-m-d'); ?>';
    const todayJalali = '<?php echo $_SESSION['calendar'] === 'jalali' ? jdate('Y/m/d') : date('Y-m-d'); ?>';
    
    if (isJalali) {
        $('#date_display').val(todayJalali);
        $('#date').val(todayGregorian);
    } else {
        document.getElementById('date').value = todayGregorian;
    }
    
    document.getElementById('expenseId').value = '';
    document.getElementById('expenseModal').showModal();
}

function editExpense(expense) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit text-blue-600"></i> Edit Expense';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('expenseId').value = expense.id;
    document.getElementById('amount').value = expense.amount;
    document.getElementById('category_id').value = expense.category_id;
    
    const isJalali = <?php echo $_SESSION['calendar'] === 'jalali' ? 'true' : 'false'; ?>;
    
    if (isJalali) {
        $('#date_display').val(expense.jalali_date);
        $('#date').val(expense.date);
    } else {
        document.getElementById('date').value = expense.date;
    }
    
    document.getElementById('note').value = expense.note || '';
    document.getElementById('expenseModal').showModal();
}

function closeModal() {
    document.getElementById('expenseModal').close();
}

function confirmDelete(id) {
    document.getElementById('confirmDeleteBtn').href = 'expenses.php?delete=' + id;
    document.getElementById('deleteModal').showModal();
}

function setDateAction(action) {
    const isJalali = <?php echo $_SESSION['calendar'] === 'jalali' ? 'true' : 'false'; ?>;
    const todayGregorian = '<?php echo date('Y-m-d'); ?>';
    const todayJalali = '<?php echo $_SESSION['calendar'] === 'jalali' ? jdate('Y/m/d') : date('Y-m-d'); ?>';
    let currentDate;

    if (isJalali) {
        const hiddenDate = $('#date').val();
        currentDate = hiddenDate ? new Date(hiddenDate + 'T12:00:00Z') : new Date(todayGregorian + 'T12:00:00Z');
    } else {
        const dateInput = document.getElementById('date');
        currentDate = dateInput.value ? new Date(dateInput.value + 'T12:00:00') : new Date();
    }

    switch (action) {
        case 'today':
            if (isJalali) {
                $('#date_display').val(todayJalali);
                $('#date').val(todayGregorian);
                return;
            } else {
                currentDate = new Date(todayGregorian + 'T12:00:00');
            }
            break;
        case 'prev':
            currentDate.setDate(currentDate.getDate() - 1);
            break;
        case 'next':
            currentDate.setDate(currentDate.getDate() + 1);
            break;
    }

    if (isJalali) {
        const pDate = new persianDate(currentDate);
        $('#date_display').val(pDate.format('YYYY/MM/DD'));
        $('#date').val(pDate.toCalendar('gregorian').format('YYYY-MM-DD'));
    } else {
        document.getElementById('date').value = currentDate.toISOString().split('T')[0];
    }
}

<?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
    showAddModal();
<?php endif; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
