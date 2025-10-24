<?php
/**
 * Categories Management Page - categories.php
 * ExpenseLogger - خرج‌نگار
 * CRUD operations for expense categories
 */

require_once __DIR__ . '/config/init.php';

$pageTitle = 'Categories';
$db = Database::getInstance();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = sanitizeInput($_POST['name']);
        
        if (!empty($name)) {
            try {
                $db->query("INSERT INTO categories (name) VALUES (?)", [$name]);
                setFlashMessage('success', 'Category added successfully!');
                redirect('categories.php');
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                    setFlashMessage('error', 'Category name already exists.');
                } else {
                    setFlashMessage('error', 'Failed to add category: ' . $e->getMessage());
                }
            }
        } else {
            setFlashMessage('error', 'Category name is required.');
        }
    }
    
    if ($action === 'edit') {
        $id = intval($_POST['id']);
        $name = sanitizeInput($_POST['name']);
        
        if ($id > 0 && !empty($name)) {
            try {
                $db->query("UPDATE categories SET name = ? WHERE id = ?", [$name, $id]);
                setFlashMessage('success', 'Category updated successfully!');
                redirect('categories.php');
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                    setFlashMessage('error', 'Category name already exists.');
                } else {
                    setFlashMessage('error', 'Failed to update category: ' . $e->getMessage());
                }
            }
        } else {
            setFlashMessage('error', 'Invalid category data.');
        }
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Check if category has expenses
    $expenseCount = $db->query(
        "SELECT COUNT(*) as count FROM expenses WHERE category_id = ?", 
        [$id]
    )->fetch()['count'];
    
    if ($expenseCount > 0) {
        setFlashMessage('error', "Cannot delete category. It has $expenseCount expense(s) associated with it.");
        redirect('categories.php');
    }
    
    try {
        $db->query("DELETE FROM categories WHERE id = ?", [$id]);
        setFlashMessage('success', 'Category deleted successfully!');
        redirect('categories.php');
    } catch (Exception $e) {
        setFlashMessage('error', 'Failed to delete category: ' . $e->getMessage());
    }
}

// Get all categories with expense count
$categories = $db->query(
    "SELECT c.*, COUNT(e.id) as expense_count, COALESCE(SUM(e.amount), 0) as total_amount
     FROM categories c 
     LEFT JOIN expenses e ON c.id = e.category_id 
     GROUP BY c.id 
     ORDER BY c.name"
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6 animate-slide-in">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-base-content">
                <i class="fas fa-tags text-purple-600"></i> Categories Management
            </h1>
            <p class="text-base-content/70 mt-1">Organize your expenses by categories</p>
        </div>
        <button onclick="showAddModal()" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>

    <!-- Statistics -->
    <div class="stats stats-vertical lg:stats-horizontal shadow w-full">
        <div class="stat">
            <div class="stat-figure text-purple-600">
                <i class="fas fa-tags text-3xl"></i>
            </div>
            <div class="stat-title">Total Categories</div>
            <div class="stat-value text-purple-600"><?php echo count($categories); ?></div>
            <div class="stat-desc">Active categories</div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-blue-600">
                <i class="fas fa-receipt text-3xl"></i>
            </div>
            <div class="stat-title">Total Expenses</div>
            <div class="stat-value text-blue-600">
                <?php echo array_sum(array_column($categories, 'expense_count')); ?>
            </div>
            <div class="stat-desc">Across all categories</div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-emerald-600">
                <i class="fas fa-dollar-sign text-3xl"></i>
            </div>
            <div class="stat-title">Total Amount</div>
            <div class="stat-value text-emerald-600">
                $<?php echo number_format(array_sum(array_column($categories, 'total_amount')), 2); ?>
            </div>
            <div class="stat-desc">All expenses combined</div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($categories as $category): ?>
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                <div class="card-body">
                    <div class="flex justify-between items-start">
                        <h2 class="card-title text-2xl">
                            <i class="fas fa-tag text-purple-600"></i>
                            <?php echo h($category['name']); ?>
                        </h2>
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-ghost btn-sm btn-circle">
                                <i class="fas fa-ellipsis-v"></i>
                            </label>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                <li>
                                    <a onclick='editCategory(<?php echo json_encode($category); ?>)'>
                                        <i class="fas fa-edit text-info"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo h($category['name']); ?>', <?php echo $category['expense_count']; ?>)">
                                        <i class="fas fa-trash text-error"></i> Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="divider my-2"></div>
                    
                    <div class="stats stats-vertical shadow">
                        <div class="stat place-items-center">
                            <div class="stat-title">Expenses</div>
                            <div class="stat-value text-primary"><?php echo $category['expense_count']; ?></div>
                        </div>
                        
                        <div class="stat place-items-center">
                            <div class="stat-title">Total Amount</div>
                            <div class="stat-value text-secondary text-2xl">
                                $<?php echo number_format($category['total_amount'], 2); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($category['expense_count'] > 0): ?>
                        <div class="card-actions justify-end mt-4">
                            <a href="expenses.php?category=<?php echo $category['id']; ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View Expenses
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (count($categories) === 0): ?>
            <div class="col-span-full">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body text-center py-12">
                        <i class="fas fa-tags text-6xl text-base-content/30 mb-4"></i>
                        <h2 class="text-2xl font-bold mb-2">No Categories Yet</h2>
                        <p class="text-base-content/70 mb-4">Create your first category to start organizing expenses</p>
                        <button onclick="showAddModal()" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Category
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info Card -->
    <div class="alert alert-info">
        <i class="fas fa-info-circle text-2xl"></i>
        <div>
            <h3 class="font-bold">About Categories</h3>
            <div class="text-sm">
                Categories help you organize and analyze your expenses. You can only delete categories that have no expenses associated with them.
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<dialog id="categoryModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-4" id="modalTitle">
            <i class="fas fa-plus-circle text-purple-600"></i> Add New Category
        </h3>
        
        <form method="POST" action="categories.php" id="categoryForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="id" id="categoryId">
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Category Name <span class="text-error">*</span></span>
                </label>
                <input type="text" name="name" id="categoryName" 
                       class="input input-bordered" placeholder="e.g., Food, Transport, etc." 
                       required maxlength="50">
                <label class="label">
                    <span class="label-text-alt">Must be unique</span>
                </label>
            </div>

            <div class="modal-action">
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Category
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
        <p class="py-4" id="deleteMessage">Are you sure you want to delete this category?</p>
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
function showAddModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle text-purple-600"></i> Add New Category';
    document.getElementById('formAction').value = 'add';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModal').showModal();
}

function editCategory(category) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit text-blue-600"></i> Edit Category';
    document.getElementById('formAction').value = 'edit';
    document.getElementById('categoryId').value = category.id;
    document.getElementById('categoryName').value = category.name;
    document.getElementById('categoryModal').showModal();
}

function closeModal() {
    document.getElementById('categoryModal').close();
}

function confirmDelete(id, name, expenseCount) {
    if (expenseCount > 0) {
        document.getElementById('deleteMessage').innerHTML = 
            `Cannot delete category "<strong>${name}</strong>". It has <strong>${expenseCount}</strong> expense(s) associated with it.<br><br>Please delete or reassign those expenses first.`;
        document.getElementById('confirmDeleteBtn').style.display = 'none';
    } else {
        document.getElementById('deleteMessage').innerHTML = 
            `Are you sure you want to delete the category "<strong>${name}</strong>"? This action cannot be undone.`;
        document.getElementById('confirmDeleteBtn').style.display = 'inline-flex';
        document.getElementById('confirmDeleteBtn').href = 'categories.php?delete=' + id;
    }
    document.getElementById('deleteModal').showModal();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
