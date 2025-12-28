<?php
/**
 * Plugin Activation and Deactivation Handler
 *
 * Handles plugin activation and deactivation processes including
 * database table creation, version checks, and cleanup operations.
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
 * CTA Slider Activator Class
 *
 * Manages plugin activation and deactivation hooks
 */
class CTA_Slider_Activator {

    /**
     * Plugin activation handler
     *
     * Creates database tables, checks system requirements,
     * and initializes default options
     *
     * @since 1.0.0
     */
    public static function activate() {
        // Check system requirements
        self::check_requirements();

        // Create database tables
        self::create_tables();

        // Initialize default options if needed
        self::initialize_options();

        // Flush rewrite rules (if we add custom post types in future)
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation handler
     *
     * Performs cleanup but preserves user data for potential reactivation
     *
     * @since 1.0.0
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Note: We do NOT delete data on deactivation
        // Data is only removed via uninstall.php when plugin is deleted
    }

    /**
     * Check system requirements
     *
     * Verifies WordPress and PHP versions meet minimum requirements
     *
     * @since 1.0.0
     * @throws Exception If requirements are not met
     */
    private static function check_requirements() {
        global $wp_version;

        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                __('CTA Slider requires PHP version 7.4 or higher. Your server is running PHP ' . PHP_VERSION, 'cta-slider'),
                __('Plugin Activation Error', 'cta-slider'),
                array('back_link' => true)
            );
        }

        // Check WordPress version
        if (version_compare($wp_version, '5.8', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                __('CTA Slider requires WordPress version 5.8 or higher. You are running WordPress ' . $wp_version, 'cta-slider'),
                __('Plugin Activation Error', 'cta-slider'),
                array('back_link' => true)
            );
        }
    }

    /**
     * Create database tables
     *
     * Creates the custom table for storing slide entries
     *
     * @since 1.0.0
     */
    private static function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'cta_slider_slides';
        $charset_collate = $wpdb->get_charset_collate();

        // SQL for creating slides table
        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            slider_id VARCHAR(100) NOT NULL,
            slide_order INT(11) NOT NULL DEFAULT 0,

            image_id BIGINT(20) UNSIGNED NULL,
            image_url VARCHAR(500) NULL,
            image_alt VARCHAR(255) NULL,

            caption_enabled TINYINT(1) NOT NULL DEFAULT 0,
            caption_title VARCHAR(255) NULL,
            caption_text TEXT NULL,

            button_enabled TINYINT(1) NOT NULL DEFAULT 0,
            button_text VARCHAR(100) NULL,
            button_url VARCHAR(500) NULL,
            button_style VARCHAR(50) NULL,
            button_new_tab TINYINT(1) NOT NULL DEFAULT 0,

            active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            modified_at DATETIME NOT NULL,

            PRIMARY KEY  (id),
            KEY slider_id (slider_id),
            KEY slide_order (slide_order),
            KEY slider_order (slider_id, slide_order)
        ) $charset_collate;";

        // Use dbDelta for safe table creation/updates
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Store database version for future migrations
        add_option('cta_slider_db_version', CTA_SLIDER_DB_VERSION);
    }

    /**
     * Initialize default options
     *
     * Sets up default plugin options if they don't exist
     *
     * @since 1.0.0
     */
    private static function initialize_options() {
        // Initialize slider list if it doesn't exist
        if (false === get_option('cta_slider_list')) {
            add_option('cta_slider_list', array());
        }
    }
}
