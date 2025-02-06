<?php
/**
 * Plugin Name: Build Your Own Battery
 * Description: A plugin to create and filter through battery options
 * Version: 1.0
 * Author: Genex Marketing Agency Ltd.
 * Text Domain: build-your-own-battery
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BYOB_VERSION', '1.0.0');
define('BYOB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BYOB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'BYOB\\';
    $base_dir = BYOB_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize plugin
if (class_exists('BYOB\\Plugin')) {
    add_action('plugins_loaded', function() {
        $plugin = new BYOB\Plugin();
        $plugin->init();
    });
} 