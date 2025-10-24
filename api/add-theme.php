<?php
/**
 * Add Custom Theme API
 * Allows users to add custom themes to themes.php
 */

header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Validate required fields
$required = ['name', 'key', 'scheme', 'colors', 'rounded', 'description'];
foreach ($required as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing field: {$field}"]);
        exit;
    }
}

// Validate colors
$requiredColors = [
    'base-100', 'base-200', 'base-300', 'base-content',
    'primary', 'primary-content', 'secondary', 'secondary-content',
    'accent', 'accent-content', 'neutral', 'neutral-content',
    'info', 'info-content', 'success', 'success-content',
    'warning', 'warning-content', 'error', 'error-content'
];

foreach ($requiredColors as $color) {
    if (!isset($data['colors'][$color])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing color: {$color}"]);
        exit;
    }
}

try {
    // Load existing themes
    $themesFile = __DIR__ . '/../config/themes.php';
    $themes = require $themesFile;
    
    // Check if theme key already exists
    if (isset($themes[$data['key']])) {
        http_response_code(409);
        echo json_encode(['error' => 'Theme key already exists. Please use a different name.']);
        exit;
    }
    
    // Add new theme
    $themes[$data['key']] = [
        'name' => $data['name'],
        'description' => $data['description'],
        'scheme' => $data['scheme'],
        'colors' => $data['colors'],
        'rounded' => $data['rounded']
    ];
    
    // Generate new themes.php content
    $output = "<?php\n";
    $output .= "/**\n";
    $output .= " * Theme Configurations\n";
    $output .= " * ExpenseLogger - خرج‌نگار\n";
    $output .= " * All theme definitions with colors, rounded corners, and metadata\n";
    $output .= " */\n\n";
    $output .= "return [\n";
    
    foreach ($themes as $themeKey => $theme) {
        $output .= "    '{$themeKey}' => [\n";
        $output .= "        'name' => '" . addslashes($theme['name']) . "',\n";
        $output .= "        'description' => '" . addslashes($theme['description']) . "',\n";
        $output .= "        'scheme' => '{$theme['scheme']}',\n";
        $output .= "        'colors' => [\n";
        
        foreach ($theme['colors'] as $colorKey => $colorValue) {
            $output .= "            '{$colorKey}' => '" . addslashes($colorValue) . "',\n";
        }
        
        $output .= "        ],\n";
        $output .= "        'rounded' => [\n";
        
        foreach ($theme['rounded'] as $roundKey => $roundValue) {
            $output .= "            '{$roundKey}' => '{$roundValue}',\n";
        }
        
        $output .= "        ],\n";
        
        if (isset($theme['glow']) && $theme['glow']) {
            $output .= "        'glow' => true,\n";
        }
        
        $output .= "    ],\n\n";
    }
    
    $output .= "];\n";
    
    // Backup current file
    $backupFile = $themesFile . '.backup-' . date('YmdHis');
    copy($themesFile, $backupFile);
    
    // Write new file
    if (file_put_contents($themesFile, $output) === false) {
        throw new Exception('Failed to write themes.php');
    }
    
    // Clear any opcode cache
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($themesFile, true);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Theme added successfully',
        'theme_key' => $data['key']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
