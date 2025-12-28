<?php
/**
 * Slide Management Operations Handler
 *
 * Handles slide CRUD operations, reordering, and media library integration.
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
 * CTA Slider Slide Manager Class
 *
 * Manages slide operations
 */
class CTA_Slider_Slide_Manager {

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
     * Create new slide
     *
     * @since 1.0.0
     * @param array $data Slide data
     * @return int|false Insert ID on success, false on failure
     */
    public function create_slide($data) {
        // Sanitize data
        $data = $this->security->sanitize_slide_data($data);

        // Validate data
        $validated = $this->security->validate_slide_data($data);
        if (is_wp_error($validated)) {
            return false;
        }

        // Insert slide
        return $this->database->insert_slide($data);
    }

    /**
     * Update existing slide
     *
     * @since 1.0.0
     * @param int $slide_id Slide database ID
     * @param array $data Slide data
     * @return bool True on success, false on failure
     */
    public function update_slide($slide_id, $data) {
        // Sanitize data
        $data = $this->security->sanitize_slide_data($data);

        // Validate data
        $validated = $this->security->validate_slide_data($data);
        if (is_wp_error($validated)) {
            return false;
        }

        // Update slide
        return $this->database->update_slide($slide_id, $data);
    }

    /**
     * Delete slide
     *
     * @since 1.0.0
     * @param int $slide_id Slide database ID
     * @return bool True on success, false on failure
     */
    public function delete_slide($slide_id) {
        $slide_id = absint($slide_id);

        if ($slide_id <= 0) {
            return false;
        }

        return $this->database->delete_slide($slide_id);
    }

    /**
     * Reorder slides
     *
     * @since 1.0.0
     * @param string $slider_id Slider identifier
     * @param array $slide_order Array of slide IDs in new order
     * @return bool True on success, false on failure
     */
    public function reorder_slides($slider_id, $slide_order) {
        $slider_id = $this->security->sanitize_slider_id($slider_id);

        if (empty($slider_id) || empty($slide_order)) {
            return false;
        }

        // Sanitize slide IDs
        $slide_order = array_map('absint', $slide_order);

        return $this->database->reorder_slides($slider_id, $slide_order);
    }

    /**
     * Get slide
     *
     * @since 1.0.0
     * @param int $slide_id Slide database ID
     * @return array|false Slide data or false
     */
    public function get_slide($slide_id) {
        $slide_id = absint($slide_id);

        if ($slide_id <= 0) {
            return false;
        }

        return $this->database->get_slide($slide_id);
    }

    /**
     * Get slides for slider
     *
     * @since 1.0.0
     * @param string $slider_id Slider identifier
     * @param bool $active_only Whether to get only active slides
     * @return array Array of slides
     */
    public function get_slides($slider_id, $active_only = false) {
        $slider_id = $this->security->sanitize_slider_id($slider_id);

        if (empty($slider_id)) {
            return array();
        }

        return $this->database->get_slides($slider_id, $active_only);
    }
}
