<?php
/**
 * Delete Custom Theme API
 * Allows users to delete custom themes
 */

header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing theme key']);
    exit;
}

$themeKey = $data['key'];

// Protect default themes
$protectedThemes = [
    'light', 'sunset', 'midnight', 'forest', 'pastel', 
    'ocean', 'lavender', 'autumn', 'minimal', 'neon', 'retro'
];

if (in_array($themeKey, $protectedThemes)) {
    http_response_code(403);
    echo json_encode(['error' => 'Cannot delete default themes']);
    exit;
}

try {
    // Load existing themes
    $themesFile = __DIR__ . '/../config/themes.php';
    $themes = require $themesFile;
    
    // Check if theme exists
    if (!isset($themes[$themeKey])) {
        http_response_code(404);
        echo json_encode(['error' => 'Theme not found']);
        exit;
    }
    
    // Remove theme
    unset($themes[$themeKey]);
    
    // Generate new themes.php content
    $output = "<?php\n";
    $output .= "/**\n";
    $output .= " * Theme Configurations\n";
    $output .= " * ExpenseLogger - خرج‌نگار\n";
    $output .= " * All theme definitions with colors, rounded corners, and metadata\n";
    $output .= " */\n\n";
    $output .= "return [\n";
    
    foreach ($themes as $key => $theme) {
        $output .= "    '{$key}' => [\n";
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
        'message' => 'Theme deleted successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
