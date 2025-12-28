<?php
/**
 * Database Operations Handler
 *
 * Handles all database operations for sliders and slides including
 * CRUD operations, data validation, and prepared statements for security.
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
 * CTA Slider Database Class
 *
 * Manages all database interactions for the plugin
 */
class CTA_Slider_Database {

    /**
     * WordPress database object
     *
     * @var wpdb
     */
    private $wpdb;

    /**
     * Slides table name
     *
     * @var string
     */
    private $table_slides;

    /**
     * Constructor
     *
     * Initializes database connection and table names
     *
     * @since 1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_slides = $wpdb->prefix . 'cta_slider_slides';
    }

    /**
     * SLIDER CONFIGURATION OPERATIONS (wp_options)
     */

    /**
     * Get slider configuration by ID
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @return array|false Slider configuration array or false if not found
     */
    public function get_slider($slider_id) {
        $option_name = 'cta_slider_config_' . sanitize_key($slider_id);
        $config = get_option($option_name);

        return $config !== false ? $config : false;
    }

    /**
     * Save slider configuration
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @param array $config Slider configuration array
     * @return bool True on success, false on failure
     */
    public function save_slider($slider_id, $config) {
        $slider_id = sanitize_key($slider_id);
        $option_name = 'cta_slider_config_' . $slider_id;

        // Add timestamps
        $existing = $this->get_slider($slider_id);
        if ($existing) {
            $config['created'] = $existing['created'];
            $config['modified'] = current_time('mysql');
        } else {
            $config['created'] = current_time('mysql');
            $config['modified'] = current_time('mysql');
        }

        // Ensure slider ID is in config
        $config['id'] = $slider_id;

        // Save configuration
        $result = update_option($option_name, $config);

        // Add to slider list if new
        if (!$existing) {
            $this->add_slider_to_list($slider_id);
        }

        return $result;
    }

    /**
     * Delete slider configuration
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @return bool True on success, false on failure
     */
    public function delete_slider($slider_id) {
        $slider_id = sanitize_key($slider_id);
        $option_name = 'cta_slider_config_' . $slider_id;

        // Delete all slides for this slider
        $this->delete_all_slides($slider_id);

        // Remove from slider list
        $this->remove_slider_from_list($slider_id);

        // Delete configuration
        return delete_option($option_name);
    }

    /**
     * Get all sliders
     *
     * @since 1.0.0
     * @return array Array of slider configurations
     */
    public function get_all_sliders() {
        $slider_list = get_option('cta_slider_list', array());
        $sliders = array();

        foreach ($slider_list as $slider_id) {
            $slider = $this->get_slider($slider_id);
            if ($slider) {
                $sliders[] = $slider;
            }
        }

        return $sliders;
    }

    /**
     * Check if slider exists
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @return bool True if exists, false otherwise
     */
    public function slider_exists($slider_id) {
        return $this->get_slider($slider_id) !== false;
    }

    /**
     * Add slider to list
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @return bool True on success
     */
    private function add_slider_to_list($slider_id) {
        $slider_list = get_option('cta_slider_list', array());

        if (!in_array($slider_id, $slider_list)) {
            $slider_list[] = $slider_id;
            update_option('cta_slider_list', $slider_list);
        }

        return true;
    }

    /**
     * Remove slider from list
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @return bool True on success
     */
    private function remove_slider_from_list($slider_id) {
        $slider_list = get_option('cta_slider_list', array());

        $key = array_search($slider_id, $slider_list);
        if ($key !== false) {
            unset($slider_list[$key]);
            $slider_list = array_values($slider_list); // Re-index array
            update_option('cta_slider_list', $slider_list);
        }

        return true;
    }

    /**
     * SLIDE OPERATIONS (custom table)
     */

    /**
     * Get slides for a slider
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @param bool $active_only Whether to return only active slides
     * @return array Array of slide objects
     */
    public function get_slides($slider_id, $active_only = false) {
        $sql = "SELECT * FROM {$this->table_slides} WHERE slider_id = %s";

        if ($active_only) {
            $sql .= " AND active = 1";
        }

        $sql .= " ORDER BY slide_order ASC";

        $results = $this->wpdb->get_results(
            $this->wpdb->prepare($sql, sanitize_key($slider_id)),
            ARRAY_A
        );

        return $results ? $results : array();
    }

    /**
     * Get single slide by ID
     *
     * @since 1.0.0
     * @param int $slide_id The slide database ID
     * @return array|false Slide data or false if not found
     */
    public function get_slide($slide_id) {
        $sql = "SELECT * FROM {$this->table_slides} WHERE id = %d";

        $result = $this->wpdb->get_row(
            $this->wpdb->prepare($sql, absint($slide_id)),
            ARRAY_A
        );

        return $result ? $result : false;
    }

    /**
     * Insert new slide
     *
     * @since 1.0.0
     * @param array $data Slide data array
     * @return int|false Insert ID on success, false on failure
     */
    public function insert_slide($data) {
        // Get next slide order if not provided
        if (!isset($data['slide_order'])) {
            $data['slide_order'] = $this->get_next_slide_order($data['slider_id']);
        }

        // Add timestamps
        $data['created_at'] = current_time('mysql');
        $data['modified_at'] = current_time('mysql');

        // Cache image URL if image_id provided
        if (isset($data['image_id']) && !empty($data['image_id'])) {
            $data['image_url'] = wp_get_attachment_url($data['image_id']);
        }

        $result = $this->wpdb->insert(
            $this->table_slides,
            array(
                'slider_id' => sanitize_key($data['slider_id']),
                'slide_order' => absint($data['slide_order']),
                'image_id' => isset($data['image_id']) ? absint($data['image_id']) : null,
                'image_url' => isset($data['image_url']) ? esc_url_raw($data['image_url']) : null,
                'image_alt' => isset($data['image_alt']) ? sanitize_text_field($data['image_alt']) : '',
                'caption_enabled' => isset($data['caption_enabled']) ? (int)(bool)$data['caption_enabled'] : 0,
                'caption_title' => isset($data['caption_title']) ? sanitize_text_field($data['caption_title']) : '',
                'caption_text' => isset($data['caption_text']) ? sanitize_textarea_field($data['caption_text']) : '',
                'button_enabled' => isset($data['button_enabled']) ? (int)(bool)$data['button_enabled'] : 0,
                'button_text' => isset($data['button_text']) ? sanitize_text_field($data['button_text']) : '',
                'button_url' => isset($data['button_url']) ? esc_url_raw($data['button_url']) : '',
                'button_style' => isset($data['button_style']) ? sanitize_text_field($data['button_style']) : 'btn-primary',
                'button_new_tab' => isset($data['button_new_tab']) ? (int)(bool)$data['button_new_tab'] : 0,
                'active' => isset($data['active']) ? (int)(bool)$data['active'] : 1,
                'created_at' => $data['created_at'],
                'modified_at' => $data['modified_at'],
            ),
            array(
                '%s', // slider_id
                '%d', // slide_order
                '%d', // image_id
                '%s', // image_url
                '%s', // image_alt
                '%d', // caption_enabled
                '%s', // caption_title
                '%s', // caption_text
                '%d', // button_enabled
                '%s', // button_text
                '%s', // button_url
                '%s', // button_style
                '%d', // button_new_tab
                '%d', // active
                '%s', // created_at
                '%s', // modified_at
            )
        );

        return $result ? $this->wpdb->insert_id : false;
    }

    /**
     * Update existing slide
     *
     * @since 1.0.0
     * @param int $slide_id The slide database ID
     * @param array $data Slide data array
     * @return bool True on success, false on failure
     */
    public function update_slide($slide_id, $data) {
        // Update modified timestamp
        $data['modified_at'] = current_time('mysql');

        // Update cached image URL if image_id changed
        if (isset($data['image_id']) && !empty($data['image_id'])) {
            $data['image_url'] = wp_get_attachment_url($data['image_id']);
        }

        $update_data = array();
        $format = array();

        // Build update data with sanitization
        $fields = array(
            'slider_id' => '%s',
            'slide_order' => '%d',
            'image_id' => '%d',
            'image_url' => '%s',
            'image_alt' => '%s',
            'caption_enabled' => '%d',
            'caption_title' => '%s',
            'caption_text' => '%s',
            'button_enabled' => '%d',
            'button_text' => '%s',
            'button_url' => '%s',
            'button_style' => '%s',
            'button_new_tab' => '%d',
            'active' => '%d',
            'modified_at' => '%s',
        );

        foreach ($fields as $field => $field_format) {
            if (isset($data[$field])) {
                switch ($field) {
                    case 'slider_id':
                        $update_data[$field] = sanitize_key($data[$field]);
                        break;
                    case 'slide_order':
                    case 'image_id':
                        $update_data[$field] = absint($data[$field]);
                        break;
                    case 'image_url':
                    case 'button_url':
                        $update_data[$field] = esc_url_raw($data[$field]);
                        break;
                    case 'caption_text':
                        $update_data[$field] = sanitize_textarea_field($data[$field]);
                        break;
                    case 'caption_enabled':
                    case 'button_enabled':
                    case 'button_new_tab':
                    case 'active':
                        $update_data[$field] = (int)(bool)$data[$field];
                        break;
                    case 'modified_at':
                        $update_data[$field] = $data[$field];
                        break;
                    default:
                        $update_data[$field] = sanitize_text_field($data[$field]);
                }
                $format[] = $field_format;
            }
        }

        $result = $this->wpdb->update(
            $this->table_slides,
            $update_data,
            array('id' => absint($slide_id)),
            $format,
            array('%d')
        );

        return $result !== false;
    }

    /**
     * Delete slide
     *
     * @since 1.0.0
     * @param int $slide_id The slide database ID
     * @return bool True on success, false on failure
     */
    public function delete_slide($slide_id) {
        $result = $this->wpdb->delete(
            $this->table_slides,
            array('id' => absint($slide_id)),
            array('%d')
        );

        return $result !== false;
    }

    /**
     * Delete all slides for a slider
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @return bool True on success, false on failure
     */
    public function delete_all_slides($slider_id) {
        $result = $this->wpdb->delete(
            $this->table_slides,
            array('slider_id' => sanitize_key($slider_id)),
            array('%s')
        );

        return $result !== false;
    }

    /**
     * Reorder slides
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @param array $slide_order_array Array of slide IDs in new order
     * @return bool True on success, false on failure
     */
    public function reorder_slides($slider_id, $slide_order_array) {
        $slider_id = sanitize_key($slider_id);

        foreach ($slide_order_array as $order => $slide_id) {
            $this->wpdb->update(
                $this->table_slides,
                array('slide_order' => absint($order)),
                array(
                    'id' => absint($slide_id),
                    'slider_id' => $slider_id
                ),
                array('%d'),
                array('%d', '%s')
            );
        }

        return true;
    }

    /**
     * Get next slide order number
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @return int Next available order number
     */
    public function get_next_slide_order($slider_id) {
        $sql = "SELECT MAX(slide_order) FROM {$this->table_slides} WHERE slider_id = %s";

        $max_order = $this->wpdb->get_var(
            $this->wpdb->prepare($sql, sanitize_key($slider_id))
        );

        return ($max_order !== null) ? absint($max_order) + 1 : 0;
    }

    /**
     * Get slide count for a slider
     *
     * @since 1.0.0
     * @param string $slider_id The slider identifier
     * @param bool $active_only Whether to count only active slides
     * @return int Number of slides
     */
    public function get_slide_count($slider_id, $active_only = false) {
        $sql = "SELECT COUNT(*) FROM {$this->table_slides} WHERE slider_id = %s";

        if ($active_only) {
            $sql .= " AND active = 1";
        }

        $count = $this->wpdb->get_var(
            $this->wpdb->prepare($sql, sanitize_key($slider_id))
        );

        return absint($count);
    }
}
