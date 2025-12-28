<?php
/**
 * Security Utilities Handler
 *
 * Provides security functions including nonce management, capability checks,
 * input sanitization, and output escaping helpers.
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
 * CTA Slider Security Class
 *
 * Centralizes all security-related operations
 */
class CTA_Slider_Security {

    /**
     * Nonce action identifier
     *
     * @var string
     */
    const NONCE_ACTION = 'cta_slider_admin_action';

    /**
     * Nonce field name
     *
     * @var string
     */
    const NONCE_NAME = 'cta_slider_nonce';

    /**
     * NONCE MANAGEMENT
     */

    /**
     * Create nonce for admin actions
     *
     * @since 1.0.0
     * @param string $action Specific action identifier
     * @return string Nonce value
     */
    public function create_nonce($action = '') {
        $nonce_action = self::NONCE_ACTION;
        if (!empty($action)) {
            $nonce_action .= '_' . $action;
        }
        return wp_create_nonce($nonce_action);
    }

    /**
     * Verify nonce for admin actions
     *
     * @since 1.0.0
     * @param string $nonce Nonce value to verify
     * @param string $action Specific action identifier
     * @return bool True if valid, false otherwise
     */
    public function verify_nonce($nonce, $action = '') {
        $nonce_action = self::NONCE_ACTION;
        if (!empty($action)) {
            $nonce_action .= '_' . $action;
        }
        return wp_verify_nonce($nonce, $nonce_action) !== false;
    }

    /**
     * Verify nonce from request and die if invalid
     *
     * @since 1.0.0
     * @param string $action Specific action identifier
     * @return void Dies if nonce is invalid
     */
    public function check_nonce($action = '') {
        $nonce = isset($_POST[self::NONCE_NAME]) ? $_POST[self::NONCE_NAME] : '';

        if (!$this->verify_nonce($nonce, $action)) {
            wp_die(
                __('Security check failed. Please refresh the page and try again.', 'cta-slider'),
                __('Security Error', 'cta-slider'),
                array('response' => 403, 'back_link' => true)
            );
        }
    }

    /**
     * CAPABILITY CHECKS
     */

    /**
     * Check if current user can manage sliders
     *
     * @since 1.0.0
     * @return bool True if user has capability
     */
    public function current_user_can_manage() {
        return current_user_can('manage_options');
    }

    /**
     * Check admin capability and die if insufficient
     *
     * @since 1.0.0
     * @return void Dies if user lacks capability
     */
    public function check_admin_capability() {
        if (!$this->current_user_can_manage()) {
            wp_die(
                __('You do not have sufficient permissions to access this page.', 'cta-slider'),
                __('Permission Error', 'cta-slider'),
                array('response' => 403, 'back_link' => true)
            );
        }
    }

    /**
     * AJAX SECURITY
     */

    /**
     * Verify AJAX request security
     *
     * Checks nonce and capability for AJAX requests
     *
     * @since 1.0.0
     * @param string $action Specific action identifier
     * @return void Sends JSON error if invalid
     */
    public function verify_ajax_request($action = '') {
        // Check nonce
        $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
        if (!$this->verify_nonce($nonce, $action)) {
            wp_send_json_error(array(
                'message' => __('Security check failed.', 'cta-slider')
            ));
        }

        // Check capability
        if (!$this->current_user_can_manage()) {
            wp_send_json_error(array(
                'message' => __('Insufficient permissions.', 'cta-slider')
            ));
        }
    }

    /**
     * INPUT SANITIZATION
     */

    /**
     * Sanitize slider ID
     *
     * @since 1.0.0
     * @param string $id Slider identifier
     * @return string Sanitized ID
     */
    public function sanitize_slider_id($id) {
        return sanitize_key($id);
    }

    /**
     * Sanitize slider configuration
     *
     * @since 1.0.0
     * @param array $config Slider configuration array
     * @return array Sanitized configuration
     */
    public function sanitize_slider_config($config) {
        // Sanitize image height
        $image_height = isset($config['image_height']) ? sanitize_text_field($config['image_height']) : 'auto';
        if ($image_height !== 'auto' && !preg_match('/^\d+$/', $image_height)) {
            $image_height = 'auto';
        }

        // Sanitize image fit
        $allowed_fits = array('cover', 'contain', 'none');
        $image_fit = isset($config['image_fit']) && in_array($config['image_fit'], $allowed_fits)
            ? $config['image_fit'] : 'cover';

        return array(
            'id' => isset($config['id']) ? sanitize_key($config['id']) : '',
            'name' => isset($config['name']) ? sanitize_text_field($config['name']) : '',
            'indicators' => isset($config['indicators']) ? (bool)$config['indicators'] : true,
            'controls' => isset($config['controls']) ? (bool)$config['controls'] : true,
            'transition' => isset($config['transition']) && in_array($config['transition'], array('slide', 'crossfade'))
                ? $config['transition'] : 'slide',
            'autoplay' => isset($config['autoplay']) ? (bool)$config['autoplay'] : true,
            'interval' => isset($config['interval']) ? max(1000, min(30000, absint($config['interval']))) : 5000,
            'keyboard' => isset($config['keyboard']) ? (bool)$config['keyboard'] : true,
            'touch' => isset($config['touch']) ? (bool)$config['touch'] : true,
            'pause_hover' => isset($config['pause_hover']) ? (bool)$config['pause_hover'] : true,
            'wrap' => isset($config['wrap']) ? (bool)$config['wrap'] : true,
            'active' => isset($config['active']) ? (bool)$config['active'] : true,
            'image_height' => $image_height,
            'image_fit' => $image_fit,
        );
    }

    /**
     * Sanitize slide data
     *
     * @since 1.0.0
     * @param array $data Slide data array
     * @return array Sanitized slide data
     */
    public function sanitize_slide_data($data) {
        return array(
            'slider_id' => isset($data['slider_id']) ? sanitize_key($data['slider_id']) : '',
            'slide_order' => isset($data['slide_order']) ? absint($data['slide_order']) : 0,
            'image_id' => isset($data['image_id']) ? absint($data['image_id']) : 0,
            'image_alt' => isset($data['image_alt']) ? sanitize_text_field($data['image_alt']) : '',
            'caption_enabled' => isset($data['caption_enabled']) ? (bool)$data['caption_enabled'] : false,
            'caption_title' => isset($data['caption_title']) ? sanitize_text_field($data['caption_title']) : '',
            'caption_text' => isset($data['caption_text']) ? sanitize_textarea_field($data['caption_text']) : '',
            'button_enabled' => isset($data['button_enabled']) ? (bool)$data['button_enabled'] : false,
            'button_text' => isset($data['button_text']) ? sanitize_text_field($data['button_text']) : '',
            'button_url' => isset($data['button_url']) ? esc_url_raw($data['button_url']) : '',
            'button_style' => isset($data['button_style']) ? $this->sanitize_button_style($data['button_style']) : 'btn-primary',
            'button_new_tab' => isset($data['button_new_tab']) ? (bool)$data['button_new_tab'] : false,
            'active' => isset($data['active']) ? (bool)$data['active'] : true,
        );
    }

    /**
     * Sanitize button style (whitelist validation)
     *
     * @since 1.0.0
     * @param string $style Button style class
     * @return string Valid button style or default
     */
    public function sanitize_button_style($style) {
        $allowed_styles = array(
            'btn-primary',
            'btn-secondary',
            'btn-success',
            'btn-danger',
            'btn-warning',
            'btn-info',
            'btn-light',
            'btn-dark',
        );

        return in_array($style, $allowed_styles) ? $style : 'btn-primary';
    }

    /**
     * OUTPUT ESCAPING
     */

    /**
     * Escape slider configuration for output
     *
     * @since 1.0.0
     * @param array $config Slider configuration
     * @return array Escaped configuration
     */
    public function escape_slider_config($config) {
        $escaped = array();

        foreach ($config as $key => $value) {
            if (is_bool($value)) {
                $escaped[$key] = $value;
            } elseif (is_numeric($value)) {
                $escaped[$key] = $value;
            } else {
                $escaped[$key] = esc_html($value);
            }
        }

        return $escaped;
    }

    /**
     * Escape slide data for output
     *
     * @since 1.0.0
     * @param array $slide Slide data
     * @return array Escaped slide data
     */
    public function escape_slide_output($slide) {
        return array(
            'id' => isset($slide['id']) ? absint($slide['id']) : 0,
            'slider_id' => isset($slide['slider_id']) ? esc_attr($slide['slider_id']) : '',
            'slide_order' => isset($slide['slide_order']) ? absint($slide['slide_order']) : 0,
            'image_id' => isset($slide['image_id']) ? absint($slide['image_id']) : 0,
            'image_url' => isset($slide['image_url']) ? esc_url($slide['image_url']) : '',
            'image_alt' => isset($slide['image_alt']) ? esc_attr($slide['image_alt']) : '',
            'caption_enabled' => isset($slide['caption_enabled']) ? (bool)$slide['caption_enabled'] : false,
            'caption_title' => isset($slide['caption_title']) ? esc_html($slide['caption_title']) : '',
            'caption_text' => isset($slide['caption_text']) ? esc_html($slide['caption_text']) : '',
            'button_enabled' => isset($slide['button_enabled']) ? (bool)$slide['button_enabled'] : false,
            'button_text' => isset($slide['button_text']) ? esc_html($slide['button_text']) : '',
            'button_url' => isset($slide['button_url']) ? esc_url($slide['button_url']) : '',
            'button_style' => isset($slide['button_style']) ? esc_attr($slide['button_style']) : 'btn-primary',
            'button_new_tab' => isset($slide['button_new_tab']) ? (bool)$slide['button_new_tab'] : false,
            'active' => isset($slide['active']) ? (bool)$slide['active'] : true,
        );
    }

    /**
     * VALIDATION
     */

    /**
     * Validate slider configuration
     *
     * @since 1.0.0
     * @param array $config Slider configuration
     * @return array|WP_Error Validated config or WP_Error on failure
     */
    public function validate_slider_config($config) {
        $errors = array();

        // Check required fields
        if (empty($config['id'])) {
            $errors[] = __('Slider ID is required.', 'cta-slider');
        }

        if (empty($config['name'])) {
            $errors[] = __('Slider name is required.', 'cta-slider');
        }

        // Validate slider ID format (letters, numbers, hyphens only)
        if (!empty($config['id']) && !preg_match('/^[a-z0-9\-]+$/', $config['id'])) {
            $errors[] = __('Slider ID can only contain lowercase letters, numbers, and hyphens.', 'cta-slider');
        }

        if (!empty($errors)) {
            return new WP_Error('invalid_config', implode(' ', $errors));
        }

        return $config;
    }

    /**
     * Validate slide data
     *
     * @since 1.0.0
     * @param array $data Slide data
     * @return array|WP_Error Validated data or WP_Error on failure
     */
    public function validate_slide_data($data) {
        $errors = array();

        // Check required fields
        if (empty($data['slider_id'])) {
            $errors[] = __('Slider ID is required.', 'cta-slider');
        }

        if (empty($data['image_id'])) {
            $errors[] = __('Slide image is required.', 'cta-slider');
        }

        // Validate button URL if button enabled
        if (!empty($data['button_enabled']) && !empty($data['button_url'])) {
            if (filter_var($data['button_url'], FILTER_VALIDATE_URL) === false) {
                $errors[] = __('Button URL is not valid.', 'cta-slider');
            }
        }

        if (!empty($errors)) {
            return new WP_Error('invalid_slide', implode(' ', $errors));
        }

        return $data;
    }
}
