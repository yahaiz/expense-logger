<?php
/**
 * Registration Page - Disabled for Single-User Mode
 * ExpenseLogger - خرج‌نگار
 */

require_once __DIR__ . '/config/init.php';

// This application is now configured for single-user offline operation
// Registration is not required
setFlashMessage('info', 'This application is configured for single-user offline operation. Registration is not required.');
redirect('index.php');
exit;

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validation
    if (empty($username)) {
        $errors['username'] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters long.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (empty($fullName)) {
        $errors['full_name'] = 'Full name is required.';
    } elseif (strlen($fullName) < 2) {
        $errors['full_name'] = 'Full name must be at least 2 characters long.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
        $errors['password'] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number.';
    }

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check if username or email already exists
    if (empty($errors)) {
        $db = Database::getInstance();

        $existingUser = $db->query(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [$username, $email]
        )->fetch();

        if ($existingUser) {
            $errors['general'] = 'Username or email already exists.';
        }
    }

    // If no errors, create the user
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $db->query(
                "INSERT INTO users (username, email, full_name, password_hash, role, created_at) VALUES (?, ?, ?, ?, 'user', datetime('now'))",
                [$username, $email, $fullName, $hashedPassword]
            );

            // Log the registration
            logActivity('User registered: ' . $username);

            // Set success message and redirect to login
            setFlashMessage('success', 'Registration successful! Please log in with your credentials.');
            redirect('login.php');

        } catch (Exception $e) {
            logActivity('Registration failed for username: ' . $username . ' - ' . $e->getMessage());
            $errors['general'] = 'Registration failed. Please try again.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="inline-block p-4 bg-primary rounded-full mb-4">
                <i class="fas fa-user-plus text-primary-content text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-base-content">Create Account</h2>
            <p class="mt-2 text-base-content/70">Join ExpenseLogger to start tracking your expenses</p>
        </div>

        <!-- Registration Form -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo h($errors['general']); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php" class="space-y-4">
                    <!-- Username -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Username <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="username" class="input input-bordered <?php echo isset($errors['username']) ? 'input-error' : ''; ?>"
                               placeholder="Choose a username" value="<?php echo h($_POST['username'] ?? ''); ?>" required>
                        <?php if (isset($errors['username'])): ?>
                            <label class="label">
                                <span class="label-text-alt text-error"><?php echo h($errors['username']); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Email <span class="text-error">*</span></span>
                        </label>
                        <input type="email" name="email" class="input input-bordered <?php echo isset($errors['email']) ? 'input-error' : ''; ?>"
                               placeholder="your@email.com" value="<?php echo h($_POST['email'] ?? ''); ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <label class="label">
                                <span class="label-text-alt text-error"><?php echo h($errors['email']); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>

                    <!-- Full Name -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Full Name <span class="text-error">*</span></span>
                        </label>
                        <input type="text" name="full_name" class="input input-bordered <?php echo isset($errors['full_name']) ? 'input-error' : ''; ?>"
                               placeholder="Your full name" value="<?php echo h($_POST['full_name'] ?? ''); ?>" required>
                        <?php if (isset($errors['full_name'])): ?>
                            <label class="label">
                                <span class="label-text-alt text-error"><?php echo h($errors['full_name']); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Password <span class="text-error">*</span></span>
                        </label>
                        <input type="password" name="password" id="password" class="input input-bordered <?php echo isset($errors['password']) ? 'input-error' : ''; ?>"
                               placeholder="Create a strong password" required>
                        <?php if (isset($errors['password'])): ?>
                            <label class="label">
                                <span class="label-text-alt text-error"><?php echo h($errors['password']); ?></span>
                            </label>
                        <?php endif; ?>
                        <div class="text-xs text-base-content/60 mt-1">
                            Must be at least 8 characters with uppercase, lowercase, and numbers
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Confirm Password <span class="text-error">*</span></span>
                        </label>
                        <input type="password" name="confirm_password" class="input input-bordered <?php echo isset($errors['confirm_password']) ? 'input-error' : ''; ?>"
                               placeholder="Confirm your password" required>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <label class="label">
                                <span class="label-text-alt text-error"><?php echo h($errors['confirm_password']); ?></span>
                            </label>
                        <?php endif; ?>
                    </div>

                    <!-- Password Strength Indicator -->
                    <div class="form-control">
                        <div class="text-sm">
                            <div class="flex justify-between text-xs mb-1">
                                <span>Password Strength:</span>
                                <span id="strength-text" class="font-semibold">Weak</span>
                            </div>
                            <progress id="strength-bar" class="progress progress-error w-full" value="0" max="100"></progress>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Account
                        </button>
                    </div>
                </form>

                <!-- Login Link -->
                <div class="divider">Already have an account?</div>
                <div class="text-center">
                    <a href="login.php" class="link link-primary">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        Sign In Instead
                    </a>
                </div>
            </div>
        </div>

        <!-- Terms and Privacy -->
        <div class="text-center text-xs text-base-content/50">
            By creating an account, you agree to our Terms of Service and Privacy Policy
        </div>
    </div>
</div>

<script>
// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    let feedback = [];

    if (password.length >= 8) strength += 25;
    else feedback.push('At least 8 characters');

    if (/[a-z]/.test(password)) strength += 25;
    else feedback.push('Lowercase letter');

    if (/[A-Z]/.test(password)) strength += 25;
    else feedback.push('Uppercase letter');

    if (/\d/.test(password)) strength += 25;
    else feedback.push('Number');

    return { strength, feedback };
}

document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const { strength } = checkPasswordStrength(password);

    const bar = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text');

    bar.value = strength;

    if (strength < 25) {
        bar.className = 'progress progress-error w-full';
        text.textContent = 'Weak';
        text.className = 'font-semibold text-error';
    } else if (strength < 50) {
        bar.className = 'progress progress-warning w-full';
        text.textContent = 'Fair';
        text.className = 'font-semibold text-warning';
    } else if (strength < 75) {
        bar.className = 'progress progress-info w-full';
        text.textContent = 'Good';
        text.className = 'font-semibold text-info';
    } else {
        bar.className = 'progress progress-success w-full';
        text.textContent = 'Strong';
        text.className = 'font-semibold text-success';
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }

    const { strength } = checkPasswordStrength(password);
    if (strength < 50) {
        e.preventDefault();
        alert('Please choose a stronger password. It should be at least "Good" strength.');
        return false;
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>