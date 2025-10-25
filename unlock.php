<?php
/**
 * App Unlock / Password Setup Page
 * ExpenseLogger - خرج‌نگار
 */

require_once __DIR__ . '/config/init.php';

$pageTitle = 'Unlock Application';
$errors = [];
$success = false;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'set_password') {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 4) {
            $errors[] = 'Password must be at least 4 characters long.';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        } else {
            if (setAppPassword($password)) {
                unlockApp(); // Unlock the app after setting password
                setFlashMessage('success', 'Password set successfully! Application is now unlocked.');
                redirect('index.php');
            } else {
                $errors[] = 'Failed to set password. Please try again.';
            }
        }
    }

    if ($action === 'unlock') {
        $password = $_POST['password'] ?? '';

        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (verifyAppPassword($password)) {
            unlockApp();
            setFlashMessage('success', 'Application unlocked successfully!');
            redirect('index.php');
        } else {
            $errors[] = 'Incorrect password.';
            logWarning('Failed unlock attempt', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
        }
    }
}

// Determine what to show
$showSetup = !isPasswordSet();
$showUnlock = isPasswordSet() && isAppLocked();
$showAlreadyUnlocked = isPasswordSet() && !isAppLocked();

// If already unlocked, redirect to main app
if ($showAlreadyUnlocked) {
    redirect('index.php');
}

include __DIR__ . '/includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-base-200 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-primary">
                <i class="fas fa-lock text-2xl text-primary-content"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-base-content">
                <?php echo $showSetup ? 'Set Application Password' : 'Unlock Application'; ?>
            </h2>
            <p class="mt-2 text-center text-sm text-base-content/70">
                <?php echo $showSetup ? 'Set a password to protect your expense data' : 'Enter your password to access the application'; ?>
            </p>
        </div>

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

        <?php if ($showSetup): ?>
            <!-- Password Setup Form -->
            <form class="mt-8 space-y-6" method="POST" action="unlock.php">
                <input type="hidden" name="action" value="set_password">

                <div class="space-y-4">
                    <div>
                        <label class="label">
                            <span class="label-text">Password</span>
                        </label>
                        <input type="password" name="password" class="input input-bordered w-full"
                               placeholder="Enter a secure password" required minlength="4">
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Confirm Password</span>
                        </label>
                        <input type="password" name="confirm_password" class="input input-bordered w-full"
                               placeholder="Confirm your password" required minlength="4">
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-key"></i> Set Password & Unlock
                    </button>
                </div>
            </form>
        <?php elseif ($showUnlock): ?>
            <!-- Unlock Form -->
            <form class="mt-8 space-y-6" method="POST" action="unlock.php">
                <input type="hidden" name="action" value="unlock">

                <div>
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" name="password" class="input input-bordered w-full"
                           placeholder="Enter your password" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-unlock"></i> Unlock Application
                    </button>
                </div>
            </form>
        <?php else: ?>
            <!-- Fallback: Something went wrong -->
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <h4 class="font-bold">Application Status</h4>
                    <p>Password Set: <?php echo isPasswordSet() ? 'Yes' : 'No'; ?></p>
                    <p>App Locked: <?php echo isAppLocked() ? 'Yes' : 'No'; ?></p>
                    <p>If you see this message, there might be an issue with the lock system.</p>
                    <a href="index.php" class="btn btn-primary btn-sm mt-2">Go to Dashboard</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <div class="text-sm text-base-content/60">
                <i class="fas fa-shield-alt"></i>
                Your data is stored locally and never sent to external servers.
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>