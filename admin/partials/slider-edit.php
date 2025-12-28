<?php
/**
 * Admin Page - Slider Configuration Form
 *
 * Form for creating or editing slider settings
 *
 * @package    CTA_Slider
 * @subpackage CTA_Slider/admin/partials
 * @since      1.0.0
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

$is_new = empty($slider['id']) || !$this->database->slider_exists($slider['id']);
$page_title = $is_new ? __('Add New Slider', 'cta-slider') : __('Edit Slider', 'cta-slider');
?>

<div class="wrap">
    <h1><?php echo esc_html($page_title); ?></h1>

    <hr class="wp-header-end">

    <?php settings_errors('cta_slider_messages'); ?>

    <form method="post" action="">
        <input type="hidden" name="cta_slider_action" value="save_slider">
        <input type="hidden" name="cta_slider_nonce" value="<?php echo esc_attr($this->security->create_nonce('save_slider')); ?>">

        <table class="form-table" role="presentation">
            <!-- Basic Information -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Basic Information', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="slider_name"><?php echo esc_html__('Slider Name', 'cta-slider'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" id="slider_name" name="name" value="<?php echo esc_attr($slider['name']); ?>" class="regular-text" required>
                    <p class="description"><?php echo esc_html__('Display name for identifying this slider in the admin.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="slider_id"><?php echo esc_html__('Slider ID', 'cta-slider'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text" id="slider_id" name="id" value="<?php echo esc_attr($slider['id']); ?>"
                           class="regular-text" <?php echo $is_new ? '' : 'readonly'; ?>
                           pattern="[a-z0-9\-]+" required>
                    <p class="description">
                        <?php echo esc_html__('Unique identifier (lowercase letters, numbers, hyphens only). Used in shortcode: [cta_slider id="your-id"]', 'cta-slider'); ?>
                        <?php if (!$is_new) : ?>
                            <br><strong><?php echo esc_html__('Note: Cannot be changed after creation.', 'cta-slider'); ?></strong>
                        <?php endif; ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="slider_active"><?php echo esc_html__('Status', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="slider_active" name="active" value="1" <?php checked($slider['active'], true); ?>>
                        <?php echo esc_html__('Active (slider will be displayed on frontend)', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <!-- Carousel Display Options -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Carousel Display Options', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="indicators"><?php echo esc_html__('Indicators', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="indicators" name="indicators" value="1" <?php checked($slider['indicators'], true); ?>>
                        <?php echo esc_html__('Show dot indicators at bottom of carousel', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="controls"><?php echo esc_html__('Controls', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="controls" name="controls" value="1" <?php checked($slider['controls'], true); ?>>
                        <?php echo esc_html__('Show previous/next arrow controls', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label><?php echo esc_html__('Transition Type', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="radio" name="transition" value="slide" <?php checked($slider['transition'], 'slide'); ?>>
                        <?php echo esc_html__('Slide', 'cta-slider'); ?>
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="transition" value="crossfade" <?php checked($slider['transition'], 'crossfade'); ?>>
                        <?php echo esc_html__('Crossfade', 'cta-slider'); ?>
                    </label>
                    <p class="description"><?php echo esc_html__('Animation style when transitioning between slides.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <!-- Autoplay Settings -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Autoplay Settings', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="autoplay"><?php echo esc_html__('Autoplay', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="autoplay" name="autoplay" value="1" <?php checked($slider['autoplay'], true); ?>>
                        <?php echo esc_html__('Automatically cycle through slides', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="interval"><?php echo esc_html__('Interval', 'cta-slider'); ?></label>
                </th>
                <td>
                    <input type="number" id="interval" name="interval" value="<?php echo esc_attr($slider['interval']); ?>"
                           min="1000" max="30000" step="500" class="small-text">
                    <?php echo esc_html__('milliseconds', 'cta-slider'); ?>
                    <p class="description"><?php echo esc_html__('Time between slide transitions (1000ms = 1 second). Range: 1000-30000.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="pause_hover"><?php echo esc_html__('Pause on Hover', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="pause_hover" name="pause_hover" value="1" <?php checked($slider['pause_hover'], true); ?>>
                        <?php echo esc_html__('Pause autoplay when mouse hovers over carousel', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <!-- Navigation Options -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Navigation Options', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="keyboard"><?php echo esc_html__('Keyboard Navigation', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="keyboard" name="keyboard" value="1" <?php checked($slider['keyboard'], true); ?>>
                        <?php echo esc_html__('Allow keyboard arrow keys to control slides', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="touch"><?php echo esc_html__('Touch/Swipe Support', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="touch" name="touch" value="1" <?php checked($slider['touch'], true); ?>>
                        <?php echo esc_html__('Enable touch/swipe gestures on mobile devices', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="wrap"><?php echo esc_html__('Continuous Loop', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="wrap" name="wrap" value="1" <?php checked($slider['wrap'], true); ?>>
                        <?php echo esc_html__('Loop back to first slide after last slide (continuous cycling)', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <!-- Image Display Options -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Image Display Options', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="image_height"><?php echo esc_html__('Image Height', 'cta-slider'); ?></label>
                </th>
                <td>
                    <select id="image_height" name="image_height">
                        <option value="auto" <?php selected($slider['image_height'], 'auto'); ?>><?php echo esc_html__('Auto (Original Height)', 'cta-slider'); ?></option>
                        <option value="300" <?php selected($slider['image_height'], '300'); ?>>300px</option>
                        <option value="400" <?php selected($slider['image_height'], '400'); ?>>400px</option>
                        <option value="500" <?php selected($slider['image_height'], '500'); ?>>500px</option>
                        <option value="600" <?php selected($slider['image_height'], '600'); ?>>600px (Recommended)</option>
                        <option value="700" <?php selected($slider['image_height'], '700'); ?>>700px</option>
                        <option value="800" <?php selected($slider['image_height'], '800'); ?>>800px</option>
                    </select>
                    <p class="description"><?php echo esc_html__('Maximum height for carousel images. On mobile devices, height automatically adjusts to maintain aspect ratio.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="image_fit"><?php echo esc_html__('Image Fit', 'cta-slider'); ?></label>
                </th>
                <td>
                    <select id="image_fit" name="image_fit">
                        <option value="cover" <?php selected($slider['image_fit'], 'cover'); ?>><?php echo esc_html__('Cover - Zoom to Fill (Recommended)', 'cta-slider'); ?></option>
                        <option value="contain" <?php selected($slider['image_fit'], 'contain'); ?>><?php echo esc_html__('Contain - Fit Inside', 'cta-slider'); ?></option>
                        <option value="none" <?php selected($slider['image_fit'], 'none'); ?>><?php echo esc_html__('None - Original Size', 'cta-slider'); ?></option>
                    </select>
                    <p class="description">
                        <strong><?php echo esc_html__('Cover (Recommended):', 'cta-slider'); ?></strong> <?php echo esc_html__('Image zooms/scales to fill entire space while maintaining aspect ratio. Excess is cropped. No stretching or distortion.', 'cta-slider'); ?><br>
                        <strong><?php echo esc_html__('Contain:', 'cta-slider'); ?></strong> <?php echo esc_html__('Entire image always visible, maintains aspect ratio. May show empty space around image.', 'cta-slider'); ?><br>
                        <strong><?php echo esc_html__('None:', 'cta-slider'); ?></strong> <?php echo esc_html__('Image displayed at original size without scaling.', 'cta-slider'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Save Settings', 'cta-slider'); ?>">
            <a href="<?php echo esc_url(admin_url('admin.php?page=cta-slider')); ?>" class="button">
                <?php echo esc_html__('Cancel', 'cta-slider'); ?>
            </a>
            <?php if (!$is_new) : ?>
                <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'slides', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>" class="button button-secondary">
                    <?php echo esc_html__('Manage Slides', 'cta-slider'); ?> &rarr;
                </a>
            <?php endif; ?>
        </p>
    </form>
</div>

<style>
.required {
    color: #d63638;
}
</style>
