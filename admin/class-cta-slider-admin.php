<?php
/**
 * Admin Functionality Coordinator
 *
 * Handles admin menu registration, page routing, asset enqueuing,
 * and coordination between settings and slide management.
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
 * CTA Slider Admin Class
 *
 * Manages all admin-side functionality
 */
class CTA_Slider_Admin {

    /**
     * Plugin name
     *
     * @var string
     */
    private $plugin_name;

    /**
     * Plugin version
     *
     * @var string
     */
    private $version;

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
     * Settings handler
     *
     * @var CTA_Slider_Settings
     */
    private $settings;

    /**
     * Slide manager
     *
     * @var CTA_Slider_Slide_Manager
     */
    private $slide_manager;

    /**
     * Constructor
     *
     * @since 1.0.0
     * @param string $plugin_name Plugin name
     * @param string $version Plugin version
     * @param CTA_Slider_Database $database Database handler
     * @param CTA_Slider_Security $security Security handler
     */
    public function __construct($plugin_name, $version, $database, $security) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->database = $database;
        $this->security = $security;

        // Initialize sub-components
        $this->settings = new CTA_Slider_Settings($database, $security);
        $this->slide_manager = new CTA_Slider_Slide_Manager($database, $security);
    }

    /**
     * Enqueue admin styles
     *
     * @since 1.0.0
     * @param string $hook_suffix Current admin page hook
     */
    public function enqueue_styles($hook_suffix) {
        // Only load on our plugin pages
        if (!$this->is_plugin_page($hook_suffix)) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            CTA_SLIDER_PLUGIN_URL . 'admin/css/cta-slider-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue admin scripts
     *
     * @since 1.0.0
     * @param string $hook_suffix Current admin page hook
     */
    public function enqueue_scripts($hook_suffix) {
        // Only load on our plugin pages
        if (!$this->is_plugin_page($hook_suffix)) {
            return;
        }

        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Enqueue jQuery UI for sortable (drag and drop)
        wp_enqueue_script('jquery-ui-sortable');

        // Enqueue our admin script
        wp_enqueue_script(
            $this->plugin_name,
            CTA_SLIDER_PLUGIN_URL . 'admin/js/cta-slider-admin.js',
            array('jquery', 'jquery-ui-sortable'),
            $this->version,
            false
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name,
            'ctaSliderAdmin',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => $this->security->create_nonce('ajax'),
                'strings' => array(
                    'confirm_delete_slider' => __('Are you sure you want to delete this slider? This will also delete all slides.', 'cta-slider'),
                    'confirm_delete_slide' => __('Are you sure you want to delete this slide?', 'cta-slider'),
                    'error_occurred' => __('An error occurred. Please try again.', 'cta-slider'),
                    'select_image' => __('Select Slide Image', 'cta-slider'),
                    'use_image' => __('Use this image', 'cta-slider'),
                ),
            )
        );
    }

    /**
     * Check if current page is a plugin admin page
     *
     * @since 1.0.0
     * @param string $hook_suffix Current admin page hook
     * @return bool True if plugin page
     */
    private function is_plugin_page($hook_suffix) {
        return strpos($hook_suffix, 'cta-slider') !== false;
    }

    /**
     * Register admin menu
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            __('CTA Sliders', 'cta-slider'),         // Page title
            __('CTA Sliders', 'cta-slider'),         // Menu title
            'manage_options',                         // Capability
            'cta-slider',                            // Menu slug
            array($this, 'render_admin_page'),       // Callback
            'dashicons-images-alt2',                 // Icon
            30                                        // Position
        );
    }

    /**
     * Render admin page
     *
     * Routes to appropriate view based on action parameter
     *
     * @since 1.0.0
     */
    public function render_admin_page() {
        // Check user capability
        $this->security->check_admin_capability();

        // Handle form submissions
        $this->handle_form_submission();

        // Get current action
        $action = $this->get_current_action();

        // Route to appropriate view
        switch ($action) {
            case 'edit':
                $this->render_edit_slider();
                break;

            case 'slides':
                $this->render_manage_slides();
                break;

            case 'edit_slide':
                $this->render_edit_slide();
                break;

            case 'list':
            default:
                $this->render_slider_list();
                break;
        }
    }

    /**
     * Get current action from request
     *
     * @since 1.0.0
     * @return string Action name
     */
    private function get_current_action() {
        return isset($_GET['action']) ? sanitize_key($_GET['action']) : 'list';
    }

    /**
     * Handle form submissions
     *
     * Processes POST requests for various admin actions
     *
     * @since 1.0.0
     */
    private function handle_form_submission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $action = isset($_POST['cta_slider_action']) ? sanitize_key($_POST['cta_slider_action']) : '';

        switch ($action) {
            case 'save_slider':
                $this->handle_save_slider();
                break;

            case 'delete_slider':
                $this->handle_delete_slider();
                break;

            case 'save_slide':
                $this->handle_save_slide();
                break;

            case 'delete_slide':
                $this->handle_delete_slide();
                break;
        }
    }

    /**
     * Handle save slider form submission
     *
     * @since 1.0.0
     */
    private function handle_save_slider() {
        // Verify nonce
        $this->security->check_nonce('save_slider');

        // Get and sanitize data
        $slider_data = $this->security->sanitize_slider_config($_POST);

        // Validate data
        $validated = $this->security->validate_slider_config($slider_data);
        if (is_wp_error($validated)) {
            $this->add_admin_notice($validated->get_error_message(), 'error');
            return;
        }

        // Check if slider ID already exists (for new sliders)
        $is_new = !$this->database->slider_exists($slider_data['id']);
        if ($is_new) {
            // For new sliders, check if ID is already taken
            $existing = $this->database->get_slider($slider_data['id']);
            if ($existing !== false) {
                $this->add_admin_notice(__('A slider with this ID already exists.', 'cta-slider'), 'error');
                return;
            }
        }

        // Save slider
        $result = $this->settings->save_slider($slider_data);

        if ($result) {
            $message = $is_new
                ? __('Slider created successfully.', 'cta-slider')
                : __('Slider updated successfully.', 'cta-slider');
            $this->add_admin_notice($message, 'success');

            // Redirect to slides management
            $redirect_url = add_query_arg(
                array(
                    'page' => 'cta-slider',
                    'action' => 'slides',
                    'slider_id' => $slider_data['id'],
                ),
                admin_url('admin.php')
            );
            wp_redirect($redirect_url);
            exit;
        } else {
            $this->add_admin_notice(__('Failed to save slider.', 'cta-slider'), 'error');
        }
    }

    /**
     * Handle delete slider request
     *
     * @since 1.0.0
     */
    private function handle_delete_slider() {
        // Verify nonce
        $this->security->check_nonce('delete_slider');

        $slider_id = isset($_POST['slider_id']) ? sanitize_key($_POST['slider_id']) : '';

        if (empty($slider_id)) {
            $this->add_admin_notice(__('Invalid slider ID.', 'cta-slider'), 'error');
            return;
        }

        $result = $this->settings->delete_slider($slider_id);

        if ($result) {
            $this->add_admin_notice(__('Slider deleted successfully.', 'cta-slider'), 'success');
        } else {
            $this->add_admin_notice(__('Failed to delete slider.', 'cta-slider'), 'error');
        }
    }

    /**
     * Handle save slide form submission
     *
     * @since 1.0.0
     */
    private function handle_save_slide() {
        // Verify nonce
        $this->security->check_nonce('save_slide');

        // Get and sanitize data
        $slide_data = $this->security->sanitize_slide_data($_POST);

        // Validate data
        $validated = $this->security->validate_slide_data($slide_data);
        if (is_wp_error($validated)) {
            $this->add_admin_notice($validated->get_error_message(), 'error');
            return;
        }

        // Determine if new or update
        $slide_id = isset($_POST['slide_id']) ? absint($_POST['slide_id']) : 0;

        if ($slide_id > 0) {
            $result = $this->slide_manager->update_slide($slide_id, $slide_data);
            $message = __('Slide updated successfully.', 'cta-slider');
        } else {
            $result = $this->slide_manager->create_slide($slide_data);
            $message = __('Slide created successfully.', 'cta-slider');
        }

        if ($result) {
            $this->add_admin_notice($message, 'success');

            // Redirect back to slides management
            $redirect_url = add_query_arg(
                array(
                    'page' => 'cta-slider',
                    'action' => 'slides',
                    'slider_id' => $slide_data['slider_id'],
                ),
                admin_url('admin.php')
            );
            wp_redirect($redirect_url);
            exit;
        } else {
            $this->add_admin_notice(__('Failed to save slide.', 'cta-slider'), 'error');
        }
    }

    /**
     * Handle delete slide request
     *
     * @since 1.0.0
     */
    private function handle_delete_slide() {
        // Verify nonce
        $this->security->check_nonce('delete_slide');

        $slide_id = isset($_POST['slide_id']) ? absint($_POST['slide_id']) : 0;
        $slider_id = isset($_POST['slider_id']) ? sanitize_key($_POST['slider_id']) : '';

        if (empty($slide_id)) {
            $this->add_admin_notice(__('Invalid slide ID.', 'cta-slider'), 'error');
            return;
        }

        $result = $this->slide_manager->delete_slide($slide_id);

        if ($result) {
            $this->add_admin_notice(__('Slide deleted successfully.', 'cta-slider'), 'success');
        } else {
            $this->add_admin_notice(__('Failed to delete slide.', 'cta-slider'), 'error');
        }
    }

    /**
     * AJAX HANDLERS
     */

    /**
     * Handle AJAX save slide request
     *
     * @since 1.0.0
     */
    public function handle_ajax_save_slide() {
        $this->security->verify_ajax_request('ajax');

        $slide_data = $this->security->sanitize_slide_data($_POST);
        $slide_id = isset($_POST['slide_id']) ? absint($_POST['slide_id']) : 0;

        if ($slide_id > 0) {
            $result = $this->slide_manager->update_slide($slide_id, $slide_data);
        } else {
            $result = $this->slide_manager->create_slide($slide_data);
        }

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Slide saved successfully.', 'cta-slider'),
                'slide_id' => $result,
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to save slide.', 'cta-slider'),
            ));
        }
    }

    /**
     * Handle AJAX delete slide request
     *
     * @since 1.0.0
     */
    public function handle_ajax_delete_slide() {
        $this->security->verify_ajax_request('ajax');

        $slide_id = isset($_POST['slide_id']) ? absint($_POST['slide_id']) : 0;

        $result = $this->slide_manager->delete_slide($slide_id);

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Slide deleted successfully.', 'cta-slider'),
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to delete slide.', 'cta-slider'),
            ));
        }
    }

    /**
     * Handle AJAX reorder slides request
     *
     * @since 1.0.0
     */
    public function handle_ajax_reorder_slides() {
        $this->security->verify_ajax_request('ajax');

        $slider_id = isset($_POST['slider_id']) ? sanitize_key($_POST['slider_id']) : '';
        $slide_order = isset($_POST['slide_order']) ? (array)$_POST['slide_order'] : array();

        if (empty($slider_id) || empty($slide_order)) {
            wp_send_json_error(array(
                'message' => __('Invalid data provided.', 'cta-slider'),
            ));
        }

        $result = $this->slide_manager->reorder_slides($slider_id, $slide_order);

        if ($result) {
            wp_send_json_success(array(
                'message' => __('Slides reordered successfully.', 'cta-slider'),
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to reorder slides.', 'cta-slider'),
            ));
        }
    }

    /**
     * Handle AJAX toggle slide status request
     *
     * @since 1.0.0
     */
    public function handle_ajax_toggle_slide() {
        $this->security->verify_ajax_request('ajax');

        $slide_id = isset($_POST['slide_id']) ? absint($_POST['slide_id']) : 0;
        $new_status = isset($_POST['status']) ? absint($_POST['status']) : 0;

        if ($slide_id <= 0) {
            wp_send_json_error(array(
                'message' => __('Invalid slide ID.', 'cta-slider'),
            ));
        }

        // Update only the active status
        $result = $this->database->update_slide($slide_id, array(
            'active' => $new_status
        ));

        if ($result) {
            $status_text = $new_status ? __('enabled', 'cta-slider') : __('disabled', 'cta-slider');
            wp_send_json_success(array(
                'message' => sprintf(__('Slide %s successfully.', 'cta-slider'), $status_text),
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to update slide status.', 'cta-slider'),
            ));
        }
    }

    /**
     * VIEW RENDERING METHODS
     */

    /**
     * Render slider list view
     *
     * @since 1.0.0
     */
    private function render_slider_list() {
        $sliders = $this->database->get_all_sliders();

        // Add slide count to each slider
        foreach ($sliders as &$slider) {
            $slider['slide_count'] = $this->database->get_slide_count($slider['id']);
        }

        require_once CTA_SLIDER_PLUGIN_DIR . 'admin/partials/admin-main.php';
    }

    /**
     * Render edit slider view
     *
     * @since 1.0.0
     */
    private function render_edit_slider() {
        $slider_id = isset($_GET['slider_id']) ? sanitize_key($_GET['slider_id']) : '';
        $slider = null;

        if (!empty($slider_id)) {
            $slider = $this->database->get_slider($slider_id);
        }

        // Set defaults for new slider
        if (!$slider) {
            $slider = $this->settings->get_default_config();
        }

        require_once CTA_SLIDER_PLUGIN_DIR . 'admin/partials/slider-edit.php';
    }

    /**
     * Render manage slides view
     *
     * @since 1.0.0
     */
    private function render_manage_slides() {
        $slider_id = isset($_GET['slider_id']) ? sanitize_key($_GET['slider_id']) : '';

        if (empty($slider_id)) {
            $this->add_admin_notice(__('Invalid slider ID.', 'cta-slider'), 'error');
            $this->render_slider_list();
            return;
        }

        $slider = $this->database->get_slider($slider_id);
        if (!$slider) {
            $this->add_admin_notice(__('Slider not found.', 'cta-slider'), 'error');
            $this->render_slider_list();
            return;
        }

        $slides = $this->database->get_slides($slider_id);

        require_once CTA_SLIDER_PLUGIN_DIR . 'admin/partials/slider-slides.php';
    }

    /**
     * Render edit slide view
     *
     * @since 1.0.0
     */
    private function render_edit_slide() {
        $slider_id = isset($_GET['slider_id']) ? sanitize_key($_GET['slider_id']) : '';
        $slide_id = isset($_GET['slide_id']) ? absint($_GET['slide_id']) : 0;

        if (empty($slider_id)) {
            $this->add_admin_notice(__('Invalid slider ID.', 'cta-slider'), 'error');
            $this->render_slider_list();
            return;
        }

        $slider = $this->database->get_slider($slider_id);
        if (!$slider) {
            $this->add_admin_notice(__('Slider not found.', 'cta-slider'), 'error');
            $this->render_slider_list();
            return;
        }

        $slide = null;
        if ($slide_id > 0) {
            $slide = $this->database->get_slide($slide_id);
        }

        require_once CTA_SLIDER_PLUGIN_DIR . 'admin/partials/slide-form.php';
    }

    /**
     * Add admin notice
     *
     * @since 1.0.0
     * @param string $message Notice message
     * @param string $type Notice type (success, error, warning, info)
     */
    private function add_admin_notice($message, $type = 'info') {
        add_settings_error(
            'cta_slider_messages',
            'cta_slider_message',
            $message,
            $type
        );
    }
}
