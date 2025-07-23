<?php
echo "=== Composer Autoload Test ===\n";
echo "Current directory: " . getcwd() . "\n";
echo "Script directory: " . __DIR__ . "\n";

$autoloadPath = __DIR__ . '/vendor/autoload.php';
echo "Autoload path: $autoloadPath\n";
echo "Autoload exists: " . (file_exists($autoloadPath) ? 'YES' : 'NO') . "\n";

if (file_exists($autoloadPath)) {
    echo "Loading autoload...\n";
    require_once $autoloadPath;
    echo "✅ Autoload loaded successfully!\n";
    
    if (class_exists('Dotenv\Dotenv')) {
        echo "✅ Dotenv class found!\n";
    } else {
        echo "❌ Dotenv class not found!\n";
    }
} else {
    echo "❌ Autoload not found!\n";
    
    if (is_dir(__DIR__ . '/vendor')) {
        echo "But vendor directory exists. Contents:\n";
        foreach (scandir(__DIR__ . '/vendor') as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "  - $file\n";
            }
        }
    } else {
        echo "Vendor directory does not exist!\n";
    }
}
?>
