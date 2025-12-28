<?php
/**
 * Frontend Functionality Handler
 *
 * Handles frontend asset enqueuing and public-facing functionality.
 *
 * @package    CTA_Slider
 * @subpackage CTA_Slider/public
 * @since      1.0.0
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * CTA Slider Public Class
 *
 * Manages all public-facing functionality
 */
class CTA_Slider_Public {

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
     * Constructor
     *
     * @since 1.0.0
     * @param string $plugin_name Plugin name
     * @param string $version Plugin version
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Enqueue public styles
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        // Enqueue custom CSS if file exists and is not empty
        $css_file = CTA_SLIDER_PLUGIN_DIR . 'public/css/cta-slider-public.css';

        if (file_exists($css_file) && filesize($css_file) > 0) {
            wp_enqueue_style(
                $this->plugin_name,
                CTA_SLIDER_PLUGIN_URL . 'public/css/cta-slider-public.css',
                array(),
                $this->version,
                'all'
            );
        }
    }

    /**
     * Enqueue public scripts
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        // Enqueue custom JS if file exists and is not empty
        $js_file = CTA_SLIDER_PLUGIN_DIR . 'public/js/cta-slider-public.js';

        if (file_exists($js_file) && filesize($js_file) > 0) {
            wp_enqueue_script(
                $this->plugin_name,
                CTA_SLIDER_PLUGIN_URL . 'public/js/cta-slider-public.js',
                array('jquery'),
                $this->version,
                true
            );
        }

        // Note: Bootstrap 5.3 CSS and JS are expected to be loaded by the theme
        // If you need to include Bootstrap, uncomment the lines below:

        /*
        wp_enqueue_style(
            'bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
            array(),
            '5.3.0',
            'all'
        );

        wp_enqueue_script(
            'bootstrap-bundle',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
            array(),
            '5.3.0',
            true
        );
        */
    }
}
