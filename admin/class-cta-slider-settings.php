<?php
/**
 * Slider Settings Handler
 *
 * Handles slider configuration create/edit/delete operations
 * and default configuration management.
 *
 * @package    CTA_Slider
 * @subpackage CTA_Slider/admin
 * @since      1.0.0
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * CTA Slider Settings Class
 *
 * Manages slider configuration operations
 */
class CTA_Slider_Settings {

    /**
     * Database handler
     *
     * @var CTA_Slider_Database
     */
    private $database;

    /**
     * Security handler
     *
     * @var CTA_Slider_Security
     */
    private $security;

    /**
     * Constructor
     *
     * @since 1.0.0
     * @param CTA_Slider_Database $database Database handler
     * @param CTA_Slider_Security $security Security handler
     */
    public function __construct($database, $security) {
        $this->database = $database;
        $this->security = $security;
    }

    /**
     * Get default slider configuration
     *
     * Returns default values for a new slider
     *
     * @since 1.0.0
     * @return array Default configuration
     */
    public function get_default_config() {
        return array(
            'id' => '',
            'name' => '',
            'indicators' => true,
            'controls' => true,
            'transition' => 'slide',
            'autoplay' => true,
            'interval' => 5000,
            'keyboard' => true,
            'touch' => true,
            'pause_hover' => true,
            'wrap' => true,
            'active' => true,
            'image_height' => 'auto',
            'image_fit' => 'cover',
        );
    }

    /**
     * Save slider configuration
     *
     * Creates new or updates existing slider
     *
     * @since 1.0.0
     * @param array $data Slider configuration data
     * @return bool True on success, false on failure
     */
    public function save_slider($data) {
        // Sanitize data
        $data = $this->security->sanitize_slider_config($data);

        // Validate data
        $validated = $this->security->validate_slider_config($data);
        if (is_wp_error($validated)) {
            return false;
        }

        // Save to database
        return $this->database->save_slider($data['id'], $data);
    }

    /**
     * Delete slider configuration
     *
     * Removes slider and all associated slides
     *
     * @since 1.0.0
     * @param string $slider_id Slider identifier
     * @return bool True on success, false on failure
     */
    public function delete_slider($slider_id) {
        $slider_id = $this->security->sanitize_slider_id($slider_id);

        if (empty($slider_id)) {
            return false;
        }

        return $this->database->delete_slider($slider_id);
    }

    /**
     * Get slider configuration
     *
     * @since 1.0.0
     * @param string $slider_id Slider identifier
     * @return array|false Slider configuration or false
     */
    public function get_slider($slider_id) {
        $slider_id = $this->security->sanitize_slider_id($slider_id);

        if (empty($slider_id)) {
            return false;
        }

        return $this->database->get_slider($slider_id);
    }

    /**
     * Check if slider ID is available
     *
     * @since 1.0.0
     * @param string $slider_id Slider identifier to check
     * @return bool True if available, false if taken
     */
    public function is_slider_id_available($slider_id) {
        $slider_id = $this->security->sanitize_slider_id($slider_id);

        if (empty($slider_id)) {
            return false;
        }

        return !$this->database->slider_exists($slider_id);
    }
}
