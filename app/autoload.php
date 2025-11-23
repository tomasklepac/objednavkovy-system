<?php
// Autoloader for PSR-4 namespaces
spl_autoload_register(function ($class) {
    // Namespace prefix
    $prefix = 'App\\';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Special handling for App\Config namespace
    if (strpos($relative_class, 'Config\\') === 0) {
        $file = __DIR__ . '/../config/' . str_replace('\\', '/', substr($relative_class, 7)) . '.php';
    } else {
        // Default: app/ directory
        $base_dir = __DIR__ . '/../app/';
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    }
    
    // If the file exists, load it
    if (file_exists($file)) {
        require $file;
        return;
    }
    
    // Debug log if file not found
    if (!file_exists($file)) {
        error_log("Autoloader: File not found: " . $file);
    }
});
