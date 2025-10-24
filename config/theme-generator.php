<?php
/**
 * Theme CSS Generator
 * Automatically generates CSS for all themes from themes.php
 */

/**
 * Calculate brightness of a color (0-255)
 * Works with hex colors like #ffffff or #000000 and oklch format
 */
function getColorBrightness($color) {
    // Remove # if present
    $color = ltrim($color, '#');
    
    // Handle oklch format - extract lightness percentage
    if (strpos($color, 'oklch') !== false) {
        preg_match('/oklch\((\d+(?:\.\d+)?)%/', $color, $matches);
        if (isset($matches[1])) {
            return floatval($matches[1]) * 2.55; // Convert percentage to 0-255
        }
    }
    
    // Convert hex to RGB
    if (strlen($color) === 3) {
        $r = hexdec(substr($color, 0, 1) . substr($color, 0, 1));
        $g = hexdec(substr($color, 1, 1) . substr($color, 1, 1));
        $b = hexdec(substr($color, 2, 1) . substr($color, 2, 1));
    } else {
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
    }
    
    // Calculate brightness using perceived luminance formula
    return ($r * 299 + $g * 587 + $b * 114) / 1000;
}

/**
 * Auto-detect if theme is dark or light based on base-100 color
 */
function detectThemeScheme($baseColor) {
    $brightness = getColorBrightness($baseColor);
    // If brightness > 128 (out of 255), it's a light theme
    return $brightness > 128 ? 'light' : 'dark';
}

function generateThemeCSS() {
    $themes = require __DIR__ . '/themes.php';
    $css = '';
    
    foreach ($themes as $themeKey => $theme) {
        // Auto-detect scheme if not set or validate it
        $autoScheme = detectThemeScheme($theme['colors']['base-100']);
        $scheme = isset($theme['scheme']) ? $theme['scheme'] : $autoScheme;
        
        $css .= "        /* Custom DaisyUI Theme - {$theme['name']} ({$theme['description']}) - {$autoScheme} */\n";
        $css .= "        [data-theme=\"{$themeKey}\"] {\n";
        $css .= "            color-scheme: {$scheme};\n";
        
        // Add color variables
        foreach ($theme['colors'] as $colorKey => $colorValue) {
            $css .= "            --color-{$colorKey}: {$colorValue};\n";
        }
        
        // Add rounded variables
        foreach ($theme['rounded'] as $roundedKey => $roundedValue) {
            $css .= "            --rounded-{$roundedKey}: {$roundedValue};\n";
        }
        
        // Add standard animation variables
        $css .= "            --animation-btn: 0.25s;\n";
        $css .= "            --animation-input: 0.2s;\n";
        $css .= "            --btn-focus-scale: 0.95;\n";
        $css .= "            --border-btn: 1.5px;\n";
        $css .= "            --tab-border: 1.5px;\n";
        
        $css .= "        }\n\n";
    }
    
    return $css;
}

function getAllThemeKeys() {
    $themes = require __DIR__ . '/themes.php';
    return array_keys($themes);
}

function getThemeData($themeKey) {
    $themes = require __DIR__ . '/themes.php';
    return $themes[$themeKey] ?? null;
}
