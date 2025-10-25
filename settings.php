<?php
/**
 * Settings Page
 * ExpenseLogger - خرج‌نگار
 */
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/config/theme-generator.php';

$pageTitle = 'Settings';

$themes = require __DIR__ . '/config/themes.php';

include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6 animate-slide-in">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-base-content">
                <i class="fas fa-cog text-primary"></i> Settings
            </h1>
            <p class="text-base-content/70 mt-1">Customize your experience</p>
        </div>
        <button onclick="addThemeModal.showModal()" class="btn btn-primary gap-2">
            <i class="fas fa-plus"></i>
            Add Custom Theme
        </button>
    </div>

    <!-- Settings Cards -->
    <!-- Theme Settings - Full Width -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-palette text-purple-600"></i>
                Theme
            </h2>
            <p class="text-base-content/70 mb-6">Choose your preferred color scheme with unique rounded corners</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <?php foreach ($themes as $themeKey => $theme): 
                    $isChecked = $_SESSION['theme'] == $themeKey ? 'checked' : '';
                    $colors = $theme['colors'];
                    $rounded = $theme['rounded'];
                    
                    // Generate rounded class based on rounded-box value
                    $roundedClass = 'rounded-xl'; // default
                    $roundedVal = floatval(str_replace('rem', '', $rounded['box']));
                    if ($roundedVal >= 2.5) $roundedClass = 'rounded-3xl';
                    elseif ($roundedVal >= 2) $roundedClass = 'rounded-2xl';
                    elseif ($roundedVal >= 1.5) $roundedClass = 'rounded-2xl';
                    elseif ($roundedVal >= 1) $roundedClass = 'rounded-xl';
                    elseif ($roundedVal >= 0.75) $roundedClass = 'rounded-lg';
                    else $roundedClass = 'rounded-md';
                    
                    // Generate gradient from base colors
                    $gradient = "linear-gradient(135deg, {$colors['base-100']} 0%, {$colors['base-200']} 50%, {$colors['base-300']} 100%)";
                    
                    // Special glow effect for neon theme
                    $hasGlow = isset($theme['glow']) && $theme['glow'];
                    $glowEffect = $hasGlow ? "box-shadow: 0 0 25px rgba(0, 255, 136, 0.4);" : "";
                    $textShadow = $hasGlow ? "text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);" : "";
                    
                    // Check if theme is custom (can be deleted)
                    $defaultThemes = ['light', 'sunset', 'midnight', 'forest', 'pastel', 'ocean', 'lavender', 'autumn', 'minimal', 'neon'];
                    $isCustom = !in_array($themeKey, $defaultThemes);
                ?>
                <label class="theme-card cursor-pointer">
                    <input type="radio" name="theme-setting" class="radio radio-primary hidden" 
                           value="<?php echo $themeKey; ?>" <?php echo $isChecked; ?> />
                    <div class="card border-2 hover:border-primary transition-all duration-300 <?php echo $roundedClass; ?> group relative" 
                         style="background: <?php echo $gradient; ?>; border-color: <?php echo $colors['primary']; ?>; <?php echo $glowEffect; ?>">
                        <?php if ($isCustom): ?>
                        <button type="button" onclick="event.preventDefault(); event.stopPropagation(); deleteTheme('<?php echo $themeKey; ?>', '<?php echo addslashes($theme['name']); ?>')" 
                                class="absolute top-2 right-2 z-10 btn btn-xs btn-error btn-circle opacity-0 group-hover:opacity-100 transition-opacity"
                                title="Delete theme">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                        <?php endif; ?>
                        <div class="card-body p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-4 h-4 <?php echo $roundedClass; ?> shadow-sm" 
                                     style="background: <?php echo $colors['primary']; ?>;<?php echo $hasGlow ? ' box-shadow: 0 0 15px ' . $colors['primary'] . ', 0 0 25px rgba(0, 255, 136, 0.6);' : ''; ?>"></div>
                                <span class="font-semibold" style="color: <?php echo $colors['base-content']; ?>; <?php echo $textShadow; ?>">
                                    <?php echo $theme['name']; ?>
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 <?php echo $roundedClass; ?>" 
                                     style="background: <?php echo $colors['primary']; ?>;<?php echo $hasGlow ? ' box-shadow: 0 0 10px rgba(0, 255, 136, 0.6);' : ''; ?>"></div>
                                <div class="h-2 <?php echo $roundedClass; ?> w-3/4" 
                                     style="background: <?php echo $colors['secondary']; ?>;<?php echo $hasGlow ? ' box-shadow: 0 0 10px rgba(0, 212, 255, 0.6);' : ''; ?>"></div>
                                <div class="h-2 <?php echo $roundedClass; ?> w-1/2" 
                                     style="background: <?php echo $colors['accent']; ?>;<?php echo $hasGlow ? ' box-shadow: 0 0 10px rgba(255, 0, 128, 0.6);' : ''; ?>"></div>
                            </div>
                            <div class="mt-3 text-xs" style="color: <?php echo $colors['base-content']; ?>; opacity: 0.8;">
                                <?php echo $theme['description']; ?>
                            </div>
                        </div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <style>
                .theme-card {
                    position: relative;
                }
                .theme-card:hover .btn-error {
                    opacity: 1 !important;
                }
                .theme-card input:checked + .card {
                    border-color: hsl(var(--p)) !important;
                    box-shadow: 0 0 0 2px hsl(var(--p) / 0.2);
                }
                .theme-card .card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                }
            </style>
        </div>
    </div>

    <!-- Other Settings Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Currency Settings -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-money-bill-wave text-emerald-600"></i>
                    Currency
                </h2>
                <p class="text-base-content/70 mb-4">Select your preferred currency</p>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="radio" name="currency-setting" class="radio radio-primary" value="USD" <?php echo $_SESSION['currency'] == 'USD' ? 'checked' : ''; ?> />
                        <div class="flex items-center gap-2">
                            <i class="fas fa-dollar-sign text-lg"></i>
                            <span class="label-text text-base">US Dollar ($)</span>
                        </div>
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="radio" name="currency-setting" class="radio radio-primary" value="تومان" <?php echo $_SESSION['currency'] == 'تومان' ? 'checked' : ''; ?> />
                        <div class="flex items-center gap-2">
                            <i class="fas fa-coins text-lg"></i>
                            <span class="label-text text-base" lang="fa">تومان (<span class="currency-toman">ت</span>)</span>
                        </div>
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="radio" name="currency-setting" class="radio radio-primary" value="هزار تومان" <?php echo $_SESSION['currency'] == 'هزار تومان' ? 'checked' : ''; ?> />
                        <div class="flex items-center gap-2">
                            <i class="fas fa-coins text-lg"></i>
                            <span class="label-text text-base" lang="fa">هزار تومان (<span class="currency-toman">هزار ت</span>)</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Calendar Settings -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-calendar text-blue-600"></i>
                    Calendar Type
                </h2>
                <p class="text-base-content/70 mb-4">Choose calendar system for dates</p>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="radio" name="calendar-setting" class="radio radio-primary" value="gregorian" <?php echo $_SESSION['calendar'] == 'gregorian' ? 'checked' : ''; ?> />
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-day text-lg"></i>
                            <span class="label-text text-base">Gregorian Calendar (English)</span>
                        </div>
                    </label>
                </div>
                
                <div class="form-control">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="radio" name="calendar-setting" class="radio radio-primary" value="jalali" <?php echo $_SESSION['calendar'] == 'jalali' ? 'checked' : ''; ?> />
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-lg"></i>
                            <span class="label-text text-base">Jalali Calendar (Persian)</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- App Lock Settings -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-lock text-red-600"></i>
                Application Lock
            </h2>
            <p class="text-base-content/70 mb-4">Control access to your expense application</p>

            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border border-base-300 rounded-lg">
                    <div>
                        <h3 class="font-semibold">Application Status</h3>
                        <p class="text-sm text-base-content/70">
                            <?php echo isAppLocked() ? 'Currently locked' : 'Currently unlocked'; ?>
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <?php if (!isAppLocked()): ?>
                            <button onclick="lockApp()" class="btn btn-warning btn-sm">
                                <i class="fas fa-lock"></i> Lock App
                            </button>
                        <?php else: ?>
                            <a href="unlock.php" class="btn btn-success btn-sm">
                                <i class="fas fa-unlock"></i> Unlock App
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <h4 class="font-bold">How it works:</h4>
                        <ul class="text-sm list-disc list-inside mt-2">
                            <li>Set a password on first use</li>
                            <li>Lock the app when you're done</li>
                            <li>Unlock with your password when you return</li>
                            <li>App stays unlocked until you manually lock it again</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// App Lock Setting
function lockApp() {
    if (confirm('Are you sure you want to lock the application? You will need to enter your password to unlock it.')) {
        fetch('api/lock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'lock' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Application locked successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        })
        .catch(error => {
            alert('Failed to lock application: ' + error.message);
        });
    }
}

// Theme Setting
document.querySelectorAll('.theme-card').forEach(card => {
    card.addEventListener('click', function(e) {
        // Don't trigger if clicking on delete button
        if (e.target.closest('.btn-error')) {
            return;
        }
        
        const radio = this.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
});

document.querySelectorAll('input[name="theme-setting"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const theme = this.value;
        console.log('Theme change requested:', theme);
        
        // Show loading feedback
        const originalText = this.closest('.theme-card').querySelector('span').textContent;
        this.closest('.theme-card').querySelector('span').textContent = 'Loading...';
        
        // Save theme preference
        fetch('api/theme.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ theme: theme })
        })
        .then(response => {
            console.log('API response status:', response.status);
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('API response data:', data);
            if (data.success) {
                console.log('Theme changed successfully, reloading...');
                // Show success briefly
                this.closest('.theme-card').querySelector('span').textContent = '✓ Applied!';
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                console.error('Theme change failed:', data.error);
                alert('Error: ' + data.error);
                // Reset text
                this.closest('.theme-card').querySelector('span').textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Failed to change theme: ' + error.message);
            // Reset text
            this.closest('.theme-card').querySelector('span').textContent = originalText;
        });
    });
});

// Currency Setting
document.querySelectorAll('input[name="currency-setting"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const currency = this.value;
        
        // Save currency preference
        fetch('api/currency.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ currency: currency })
        }).then(() => {
            // Reload page to update displayed amounts
            location.reload();
        });
    });
});

// Calendar Setting
document.querySelectorAll('input[name="calendar-setting"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const calendar = this.value;
        
        // Save calendar preference
        fetch('api/calendar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ calendar: calendar })
        }).then(() => {
            // Reload page to update date formats
            location.reload();
        });
    });
});
</script>

<!-- Add Custom Theme Modal -->
<dialog id="addThemeModal" class="modal">
    <div class="modal-box max-w-2xl">
        <h3 class="font-bold text-2xl mb-4">
            <i class="fas fa-palette text-primary"></i>
            Add Custom Theme
        </h3>
        
        <p class="text-base-content/70 mb-4">
            Paste your DaisyUI theme CSS configuration below. The theme will be parsed and added automatically.
            <br>
            <a href="https://daisyui.com/theme-generator" target="_blank" class="link link-primary text-sm">
                <i class="fas fa-external-link-alt"></i> Create themes using DaisyUI Theme Generator
            </a>
        </p>
        
        <div class="form-control">
            <div class="flex justify-between items-center mb-2">
                <label class="label">
                    <span class="label-text font-semibold">Theme CSS</span>
                </label>
                <button type="button" onclick="document.getElementById('themeInput').value=''" class="btn btn-xs btn-ghost">
                    <i class="fas fa-trash"></i> Clear
                </button>
            </div>
            <textarea id="themeInput" class="textarea textarea-bordered h-64 font-mono text-sm" placeholder='@plugin "daisyui/theme" {
  name: "my-theme";
  color-scheme: "dark";
  --color-base-100: #1a1a1a;
  --color-primary: #3b82f6;
  --radius-box: 1rem;
  /* ... other colors ... */
}'></textarea>
            <label class="label">
                <span class="label-text-alt text-info">
                    <i class="fas fa-info-circle"></i>
                    Supports DaisyUI format with oklch, hex, or rgb colors
                </span>
            </label>
        </div>
        
        <div id="themePreview" class="mt-4 p-4 border border-base-300 rounded-lg bg-base-200">
            <h4 class="font-semibold mb-3 flex items-center gap-2">
                <i class="fas fa-eye"></i>
                Preview
            </h4>
            <div id="previewContent" class="text-sm space-y-2">
                <div class="text-base-content/60">Enter theme CSS above to see preview</div>
            </div>
        </div>
        
        <div class="modal-action">
            <button type="button" onclick="addThemeModal.close()" class="btn btn-ghost">
                <i class="fas fa-times"></i>
                Cancel
            </button>
            <button type="button" id="addThemeBtn" onclick="parseAndAddTheme()" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Theme
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
function parseAndAddTheme() {
    const input = document.getElementById('themeInput').value.trim();
    const btn = document.getElementById('addThemeBtn');
    
    if (!input) {
        alert('Please paste theme configuration');
        return;
    }
    
    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    try {
        const theme = parseThemeCSS(input);
        
        // Send to API to save
        fetch('api/add-theme.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(theme)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Theme added successfully!');
                location.reload();
            } else {
                alert('❌ Error: ' + data.error);
                // Reset button
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus"></i> Add Theme';
            }
        })
        .catch(error => {
            alert('❌ Failed to add theme: ' + error.message);
            // Reset button
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i> Add Theme';
        });
        
    } catch (error) {
        alert('❌ Invalid theme format: ' + error.message);
        // Reset button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Add Theme';
    }
}

function parseThemeCSS(cssText) {
    console.log('Parsing CSS:', cssText.substring(0, 200));
    
    const theme = {
        colors: {},
        rounded: {}
    };
    
    // Extract theme name
    const nameMatch = cssText.match(/name:\s*["']?([^"';]+)["']?/);
    if (nameMatch) {
        theme.name = nameMatch[1].trim();
        theme.key = theme.name.toLowerCase().replace(/[^a-z0-9]/g, '-');
    } else {
        throw new Error('Theme name not found');
    }
    
    console.log('Theme name:', theme.name);
    
    // Extract color-scheme
    const schemeMatch = cssText.match(/color-scheme:\s*["']?([^"';]+)["']?/);
    theme.scheme = schemeMatch ? schemeMatch[1].trim() : 'dark';
    
    console.log('Scheme:', theme.scheme);
    
    // Extract colors - using split method for more reliability
    const lines = cssText.split('\n');
    for (const line of lines) {
        const colorMatch = line.match(/--color-([a-z0-9-]+):\s*([^;]+);/i);
        if (colorMatch) {
            const colorKey = colorMatch[1];
            let colorValue = colorMatch[2].trim();
            
            // Normalize oklch format
            if (colorValue.includes('oklch')) {
                colorValue = colorValue.replace(/oklch\s*\(\s*/g, 'oklch(');
                colorValue = colorValue.replace(/\s+/g, ' ');
                colorValue = colorValue.replace(/\s*\)/g, ')');
            }
            
            theme.colors[colorKey] = colorValue;
            console.log('Found color:', colorKey, '=', colorValue);
        }
    }
    
    console.log('Total colors found:', Object.keys(theme.colors).length);
    
    // Extract rounded values
    const radiusBox = cssText.match(/--radius-box:\s*([^;]+);/);
    const radiusField = cssText.match(/--radius-field:\s*([^;]+);/);
    
    theme.rounded = {
        box: radiusBox ? radiusBox[1].trim() : '1rem',
        btn: radiusField ? radiusField[1].trim() : '0.5rem',
        badge: radiusBox ? radiusBox[1].trim() : '1rem'
    };
    
    // Validate required colors
    const requiredColors = [
        'base-100', 'base-200', 'base-300', 'base-content',
        'primary', 'primary-content', 'secondary', 'secondary-content',
        'accent', 'accent-content', 'neutral', 'neutral-content',
        'info', 'info-content', 'success', 'success-content',
        'warning', 'warning-content', 'error', 'error-content'
    ];
    
    for (const color of requiredColors) {
        if (!theme.colors[color]) {
            throw new Error(`Missing required color: ${color}`);
        }
    }
    
    theme.description = 'Custom theme';
    
    return theme;
}

// Preview theme as user types
document.getElementById('themeInput')?.addEventListener('input', function() {
    const input = this.value.trim();
    const preview = document.getElementById('themePreview');
    const content = document.getElementById('previewContent');
    
    if (!input) {
        content.innerHTML = '<div class="text-base-content/60">Enter theme CSS above to see preview</div>';
        return;
    }
    
    try {
        const theme = parseThemeCSS(input);
        
        content.innerHTML = `
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div><strong>Name:</strong> ${theme.name}</div>
                <div><strong>Scheme:</strong> ${theme.scheme}</div>
                <div><strong>Colors:</strong> ${Object.keys(theme.colors).length}</div>
                <div><strong>Rounded:</strong> ${theme.rounded.box}</div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded" style="background: ${theme.colors['base-100'] || '#ccc'}"></div>
                    <span class="text-xs">Base</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded" style="background: ${theme.colors['primary'] || '#ccc'}"></div>
                    <span class="text-xs">Primary</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded" style="background: ${theme.colors['secondary'] || '#ccc'}"></div>
                    <span class="text-xs">Secondary</span>
                </div>
                <div class="flex items-center gap-1">
                    <div class="w-4 h-4 rounded" style="background: ${theme.colors['accent'] || '#ccc'}"></div>
                    <span class="text-xs">Accent</span>
                </div>
            </div>
        `;
        
    } catch (error) {
        content.innerHTML = '<div class="text-error text-sm"><i class="fas fa-exclamation-triangle"></i> Invalid format</div>';
    }
});

function deleteTheme(themeKey, themeName) {
    if (!confirm(`Are you sure you want to delete "${themeName}" theme? This cannot be undone.`)) {
        return;
    }
    
    fetch('api/delete-theme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ key: themeKey })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Theme deleted successfully!');
            location.reload();
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('❌ Failed to delete theme: ' + error.message);
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
