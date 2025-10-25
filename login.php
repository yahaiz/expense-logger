<?php
/**
 * Login Page - Disabled for Single-User Mode
 * ExpenseLogger - خرج‌نگار
 */

require_once __DIR__ . '/config/init.php';

// This application is now configured for single-user offline operation
// Authentication is not required
setFlashMessage('info', 'This application is configured for single-user offline operation. Authentication is not required.');
redirect('index.php');
exit;
?>

<div class="min-h-screen flex items-center justify-center bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-primary">
                <i class="fas fa-wallet text-2xl text-primary-content"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-base-content">
                Sign in to ExpenseLogger
            </h2>
            <p class="mt-2 text-center text-sm text-base-content/70">
                Track your expenses with ease
            </p>
        </div>

        <form class="mt-8 space-y-6" method="POST" action="login.php">
            <div class="space-y-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <ul class="list-disc list-inside">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo h($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="username" class="label">
                        <span class="label-text">Username or Email</span>
                    </label>
                    <input id="username" name="username" type="text" autocomplete="username"
                           class="input input-bordered w-full" placeholder="Enter your username or email"
                           value="<?php echo h($_POST['username'] ?? ''); ?>" required>
                </div>

                <div>
                    <label for="password" class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input id="password" name="password" type="password" autocomplete="current-password"
                           class="input input-bordered w-full" placeholder="Enter your password" required>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-base-content/70">
                    Default admin account: <strong>admin</strong> / <strong>admin123</strong>
                </p>
            </div>

            <div class="divider">Don't have an account?</div>
            <div class="text-center">
                <a href="register.php" class="link link-primary">
                    <i class="fas fa-user-plus mr-1"></i>
                    Create New Account
                </a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>