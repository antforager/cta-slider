<?php
/**
 * Uninstall CTA Slider Plugin
 *
 * Deletes all plugin data when the plugin is uninstalled (not just deactivated).
 * This includes:
 * - Custom database table for slides
 * - All slider configurations from wp_options
 * - Slider list index
 * - Database version option
 *
 * @package    CTA_Slider
 * @since      1.0.0
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Delete plugin data for single site
 */
function cta_slider_uninstall_site() {
    global $wpdb;

    // Delete custom database table
    $table_name = $wpdb->prefix . 'cta_slider_slides';
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

    // Get slider list
    $slider_list = get_option('cta_slider_list', array());

    // Delete each slider configuration
    if (is_array($slider_list)) {
        foreach ($slider_list as $slider_id) {
            delete_option('cta_slider_config_' . $slider_id);
        }
    }

    // Delete slider list index
    delete_option('cta_slider_list');

    // Delete database version option
    delete_option('cta_slider_db_version');
}

// Check if this is a multisite installation
if (is_multisite()) {
    // Get all blog IDs
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");

    // Loop through each site
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        cta_slider_uninstall_site();
        restore_current_blog();
    }
} else {
    // Single site installation
    cta_slider_uninstall_site();
}
