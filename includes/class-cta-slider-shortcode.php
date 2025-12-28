<?php
/**
 * Shortcode Handler
 *
 * Registers and renders the [cta_slider] shortcode,
 * generating Bootstrap 5.3 carousel HTML.
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
 * CTA Slider Shortcode Class
 *
 * Handles shortcode registration and carousel rendering
 */
class CTA_Slider_Shortcode {

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
     * Instance counter for unique carousel IDs
     *
     * @var int
     */
    private static $instance_count = 0;

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
     * Register shortcode
     *
     * @since 1.0.0
     */
    public function register() {
        add_shortcode('cta_slider', array($this, 'render_shortcode'));
    }

    /**
     * Render shortcode
     *
     * @since 1.0.0
     * @param array $atts Shortcode attributes
     * @return string Carousel HTML or empty string
     */
    public function render_shortcode($atts) {
        // Parse attributes
        $atts = shortcode_atts(
            array(
                'id' => '',
            ),
            $atts,
            'cta_slider'
        );

        // Validate slider ID
        if (empty($atts['id'])) {
            return '<!-- CTA Slider: No slider ID provided -->';
        }

        $slider_id = sanitize_key($atts['id']);

        // Get slider configuration
        $slider = $this->database->get_slider($slider_id);
        if (!$slider) {
            return '<!-- CTA Slider: Slider "' . esc_attr($slider_id) . '" not found -->';
        }

        // Check if slider is active
        if (!$slider['active']) {
            return '<!-- CTA Slider: Slider "' . esc_attr($slider_id) . '" is inactive -->';
        }

        // Get active slides
        $slides = $this->database->get_slides($slider_id, true);
        if (empty($slides)) {
            return '<!-- CTA Slider: Slider "' . esc_attr($slider_id) . '" has no active slides -->';
        }

        // Generate unique ID for this carousel instance
        self::$instance_count++;
        $unique_id = 'cta-slider-' . $slider_id . '-' . self::$instance_count;

        // Generate and return carousel HTML
        return $this->generate_carousel_html($slider, $slides, $unique_id);
    }

    /**
     * Generate complete carousel HTML
     *
     * @since 1.0.0
     * @param array $slider Slider configuration
     * @param array $slides Array of slides
     * @param string $unique_id Unique carousel ID
     * @return string Carousel HTML
     */
    private function generate_carousel_html($slider, $slides, $unique_id) {
        // Build custom styles for image sizing
        $custom_styles = $this->get_custom_styles($slider, $unique_id);

        $output = $custom_styles;

        $output .= '<div id="' . esc_attr($unique_id) . '" class="carousel slide cta-slider-carousel';

        // Add crossfade class if enabled
        if ($slider['transition'] === 'crossfade') {
            $output .= ' carousel-fade';
        }

        $output .= '"';

        // Add data attributes
        $output .= $this->get_carousel_data_attributes($slider);

        $output .= '>';

        // Add indicators if enabled
        if ($slider['indicators']) {
            $output .= $this->render_indicators($slides, $unique_id);
        }

        // Add slides
        $output .= '<div class="carousel-inner">';
        foreach ($slides as $index => $slide) {
            $output .= $this->render_slide_html($slide, $index === 0);
        }
        $output .= '</div>';

        // Add controls if enabled
        if ($slider['controls']) {
            $output .= $this->render_controls($unique_id);
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Get Bootstrap carousel data attributes
     *
     * @since 1.0.0
     * @param array $slider Slider configuration
     * @return string Data attributes HTML
     */
    private function get_carousel_data_attributes($slider) {
        $attributes = '';

        // Autoplay/ride
        if ($slider['autoplay']) {
            $attributes .= ' data-bs-ride="carousel"';
            $attributes .= ' data-bs-interval="' . absint($slider['interval']) . '"';
        } else {
            $attributes .= ' data-bs-ride="false"';
        }

        // Keyboard navigation
        if (!$slider['keyboard']) {
            $attributes .= ' data-bs-keyboard="false"';
        }

        // Touch/swipe
        if (!$slider['touch']) {
            $attributes .= ' data-bs-touch="false"';
        }

        // Pause on hover
        if ($slider['pause_hover']) {
            $attributes .= ' data-bs-pause="hover"';
        } else {
            $attributes .= ' data-bs-pause="false"';
        }

        // Wrap (continuous loop)
        if (!$slider['wrap']) {
            $attributes .= ' data-bs-wrap="false"';
        }

        return $attributes;
    }

    /**
     * Render carousel indicators
     *
     * @since 1.0.0
     * @param array $slides Array of slides
     * @param string $unique_id Unique carousel ID
     * @return string Indicators HTML
     */
    private function render_indicators($slides, $unique_id) {
        $output = '<div class="carousel-indicators">';

        foreach ($slides as $index => $slide) {
            $active = $index === 0 ? ' class="active" aria-current="true"' : '';
            $output .= '<button type="button" data-bs-target="#' . esc_attr($unique_id) . '" ';
            $output .= 'data-bs-slide-to="' . absint($index) . '"' . $active;
            $output .= ' aria-label="' . esc_attr(sprintf(__('Slide %d', 'cta-slider'), $index + 1)) . '"';
            $output .= '></button>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Render single slide HTML
     *
     * @since 1.0.0
     * @param array $slide Slide data
     * @param bool $is_active Whether this is the active/first slide
     * @return string Slide HTML
     */
    private function render_slide_html($slide, $is_active) {
        $slide = $this->security->escape_slide_output($slide);

        $output = '<div class="carousel-item' . ($is_active ? ' active' : '') . '">';

        // Image
        $output .= '<img src="' . $slide['image_url'] . '" ';
        $output .= 'class="d-block w-100" ';
        $output .= 'alt="' . $slide['image_alt'] . '">';

        // Caption (if enabled)
        if ($slide['caption_enabled'] && (!empty($slide['caption_title']) || !empty($slide['caption_text']) || $slide['button_enabled'])) {
            $output .= '<div class="carousel-caption d-none d-md-block">';

            // Caption title
            if (!empty($slide['caption_title'])) {
                $output .= '<h5>' . $slide['caption_title'] . '</h5>';
            }

            // Caption text
            if (!empty($slide['caption_text'])) {
                $output .= '<p>' . nl2br($slide['caption_text']) . '</p>';
            }

            // CTA Button (if enabled)
            if ($slide['button_enabled'] && !empty($slide['button_text']) && !empty($slide['button_url'])) {
                $target = $slide['button_new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : '';
                $output .= '<a href="' . $slide['button_url'] . '" ';
                $output .= 'class="btn ' . $slide['button_style'] . '"' . $target . '>';
                $output .= $slide['button_text'];
                $output .= '</a>';
            }

            $output .= '</div>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Get custom styles for image sizing
     *
     * @since 1.0.0
     * @param array $slider Slider configuration
     * @param string $unique_id Unique carousel ID
     * @return string Style tag with custom CSS
     */
    private function get_custom_styles($slider, $unique_id) {
        $image_height = isset($slider['image_height']) ? $slider['image_height'] : 'auto';
        $image_fit = isset($slider['image_fit']) ? $slider['image_fit'] : 'cover';

        // Only add styles if height is set
        if ($image_height === 'auto' && $image_fit === 'cover') {
            return '';
        }

        $output = '<style>';
        $output .= '#' . esc_attr($unique_id) . ' .carousel-item img {';

        if ($image_height !== 'auto') {
            $output .= 'height: ' . absint($image_height) . 'px;';
        }

        $output .= 'object-fit: ' . esc_attr($image_fit) . ';';
        $output .= 'width: 100%;';
        $output .= '}';

        // Responsive adjustments for mobile
        $output .= '@media (max-width: 768px) {';
        $output .= '#' . esc_attr($unique_id) . ' .carousel-item img {';

        if ($image_height !== 'auto') {
            // On mobile, use max-height instead of fixed height for better responsiveness
            $output .= 'height: auto;';
            $output .= 'max-height: ' . absint($image_height) . 'px;';
        }

        $output .= '}';
        $output .= '}';

        // Extra small devices
        $output .= '@media (max-width: 576px) {';
        $output .= '#' . esc_attr($unique_id) . ' .carousel-item img {';

        if ($image_height !== 'auto') {
            // On very small screens, cap at 300px or half the configured height
            $max_mobile_height = min(300, absint($image_height) / 2);
            $output .= 'max-height: ' . $max_mobile_height . 'px;';
        }

        $output .= '}';
        $output .= '}';

        $output .= '</style>';

        return $output;
    }

    /**
     * Render carousel controls
     *
     * @since 1.0.0
     * @param string $unique_id Unique carousel ID
     * @return string Controls HTML
     */
    private function render_controls($unique_id) {
        $output = '<button class="carousel-control-prev" type="button" ';
        $output .= 'data-bs-target="#' . esc_attr($unique_id) . '" data-bs-slide="prev">';
        $output .= '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        $output .= '<span class="visually-hidden">' . esc_html__('Previous', 'cta-slider') . '</span>';
        $output .= '</button>';

        $output .= '<button class="carousel-control-next" type="button" ';
        $output .= 'data-bs-target="#' . esc_attr($unique_id) . '" data-bs-slide="next">';
        $output .= '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        $output .= '<span class="visually-hidden">' . esc_html__('Next', 'cta-slider') . '</span>';
        $output .= '</button>';

        return $output;
    }
}
