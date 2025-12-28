<?php
/**
 * Plugin Name: CTA Slider
 * Plugin URI: https://example.com/cta-slider
 * Description: Call to Action banner slider with Bootstrap 5.3 carousel implementation. Create beautiful, responsive CTA sliders with images, captions, and call-to-action buttons.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cta-slider
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CTA_SLIDER_VERSION', '1.0.0');
define('CTA_SLIDER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CTA_SLIDER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CTA_SLIDER_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('CTA_SLIDER_DB_VERSION', '1.0');

/**
 * Activation hook
 * Creates database tables and sets up initial options
 */
function activate_cta_slider() {
    require_once CTA_SLIDER_PLUGIN_DIR . 'includes/class-cta-slider-activator.php';
    CTA_Slider_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_cta_slider');

/**
 * Deactivation hook
 * Cleanup temporary data but preserve slider configurations
 */
function deactivate_cta_slider() {
    require_once CTA_SLIDER_PLUGIN_DIR . 'includes/class-cta-slider-activator.php';
    CTA_Slider_Activator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_cta_slider');

/**
 * Load plugin core class
 */
require_once CTA_SLIDER_PLUGIN_DIR . 'includes/class-cta-slider-core.php';

/**
 * Initialize and run the plugin
 *
 * Creates the core plugin instance and starts execution
 * This function runs on every page load after WordPress is fully initialized
 */
function run_cta_slider() {
    $plugin = new CTA_Slider_Core();
    $plugin->run();
}
run_cta_slider();
