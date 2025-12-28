<?php
/**
 * Core Plugin Orchestrator
 *
 * Initializes all plugin components, loads dependencies,
 * and registers WordPress hooks.
 *
 * @package    CTA_Slider
 * @subpackage CTA_Slider/includes
 * @since      1.0.0
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * CTA Slider Core Class
 *
 * Main plugin class that orchestrates all components
 */
class CTA_Slider_Core {

    /**
     * Plugin name
     *
     * @var string
     */
    protected $plugin_name;

    /**
     * Plugin version
     *
     * @var string
     */
    protected $version;

    /**
     * Database handler instance
     *
     * @var CTA_Slider_Database
     */
    private $database;

    /**
     * Security handler instance
     *
     * @var CTA_Slider_Security
     */
    private $security;

    /**
     * Admin handler instance
     *
     * @var CTA_Slider_Admin
     */
    private $admin;

    /**
     * Public handler instance
     *
     * @var CTA_Slider_Public
     */
    private $public_handler;

    /**
     * Shortcode handler instance
     *
     * @var CTA_Slider_Shortcode
     */
    private $shortcode;

    /**
     * Constructor
     *
     * Initializes the plugin core and loads all dependencies
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->plugin_name = 'cta-slider';
        $this->version = CTA_SLIDER_VERSION;

        $this->load_dependencies();
        $this->init_core_classes();
    }

    /**
     * Load required dependencies
     *
     * Includes all necessary class files
     *
     * @since 1.0.0
     */
    private function load_dependencies() {
        // Core classes
        require_once CTA_SLIDER_PLUGIN_DIR . 'includes/class-cta-slider-database.php';
        require_once CTA_SLIDER_PLUGIN_DIR . 'includes/class-cta-slider-security.php';
        require_once CTA_SLIDER_PLUGIN_DIR . 'includes/class-cta-slider-shortcode.php';

        // Admin classes (only load if in admin)
        if (is_admin()) {
            require_once CTA_SLIDER_PLUGIN_DIR . 'admin/class-cta-slider-admin.php';
            require_once CTA_SLIDER_PLUGIN_DIR . 'admin/class-cta-slider-settings.php';
            require_once CTA_SLIDER_PLUGIN_DIR . 'admin/class-cta-slider-slide-manager.php';
        }

        // Public classes
        require_once CTA_SLIDER_PLUGIN_DIR . 'public/class-cta-slider-public.php';
    }

    /**
     * Initialize core class instances
     *
     * Creates instances of all main plugin classes
     *
     * @since 1.0.0
     */
    private function init_core_classes() {
        // Initialize core components
        $this->database = new CTA_Slider_Database();
        $this->security = new CTA_Slider_Security();

        // Initialize shortcode handler
        $this->shortcode = new CTA_Slider_Shortcode($this->database, $this->security);

        // Initialize admin components (only in admin)
        if (is_admin()) {
            $this->admin = new CTA_Slider_Admin(
                $this->plugin_name,
                $this->version,
                $this->database,
                $this->security
            );
        }

        // Initialize public components
        $this->public_handler = new CTA_Slider_Public(
            $this->plugin_name,
            $this->version
        );
    }

    /**
     * Run the plugin
     *
     * Registers all hooks and starts plugin execution
     *
     * @since 1.0.0
     */
    public function run() {
        // Register shortcode
        $this->register_shortcode_hooks();

        // Register admin hooks (only in admin)
        if (is_admin()) {
            $this->register_admin_hooks();
        }

        // Register public hooks
        $this->register_public_hooks();
    }

    /**
     * Register shortcode hooks
     *
     * @since 1.0.0
     */
    private function register_shortcode_hooks() {
        add_action('init', array($this->shortcode, 'register'));
    }

    /**
     * Register admin hooks
     *
     * @since 1.0.0
     */
    private function register_admin_hooks() {
        // Enqueue admin styles and scripts
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this->admin, 'enqueue_scripts'));

        // Register admin menu
        add_action('admin_menu', array($this->admin, 'add_admin_menu'));

        // Register AJAX handlers
        add_action('wp_ajax_cta_slider_save_slide', array($this->admin, 'handle_ajax_save_slide'));
        add_action('wp_ajax_cta_slider_delete_slide', array($this->admin, 'handle_ajax_delete_slide'));
        add_action('wp_ajax_cta_slider_reorder_slides', array($this->admin, 'handle_ajax_reorder_slides'));
        add_action('wp_ajax_cta_slider_toggle_slide', array($this->admin, 'handle_ajax_toggle_slide'));
    }

    /**
     * Register public hooks
     *
     * @since 1.0.0
     */
    private function register_public_hooks() {
        // Enqueue public styles and scripts (if needed)
        add_action('wp_enqueue_scripts', array($this->public_handler, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this->public_handler, 'enqueue_scripts'));
    }

    /**
     * Get plugin name
     *
     * @since 1.0.0
     * @return string Plugin name
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Get plugin version
     *
     * @since 1.0.0
     * @return string Plugin version
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Get database instance
     *
     * @since 1.0.0
     * @return CTA_Slider_Database Database handler
     */
    public function get_database() {
        return $this->database;
    }

    /**
     * Get security instance
     *
     * @since 1.0.0
     * @return CTA_Slider_Security Security handler
     */
    public function get_security() {
        return $this->security;
    }
}
