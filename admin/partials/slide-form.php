<?php
/**
 * Admin Page - Slide Add/Edit Form
 *
 * Form for creating or editing individual slides
 *
 * @package    CTA_Slider
 * @subpackage CTA_Slider/admin/partials
 * @since      1.0.0
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

$is_new = empty($slide);
$page_title = $is_new ? __('Add New Slide', 'cta-slider') : __('Edit Slide', 'cta-slider');

// Set defaults for new slide
if ($is_new) {
    $slide = array(
        'image_id' => 0,
        'image_url' => '',
        'image_alt' => '',
        'caption_enabled' => false,
        'caption_title' => '',
        'caption_text' => '',
        'button_enabled' => false,
        'button_text' => '',
        'button_url' => '',
        'button_style' => 'btn-primary',
        'button_new_tab' => false,
        'active' => true,
    );
}
?>

<div class="wrap">
    <h1><?php echo esc_html($page_title); ?></h1>

    <hr class="wp-header-end">

    <!-- Breadcrumb -->
    <p class="cta-slider-breadcrumb">
        <a href="<?php echo esc_url(admin_url('admin.php?page=cta-slider')); ?>"><?php echo esc_html__('CTA Sliders', 'cta-slider'); ?></a> &raquo;
        <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'slides', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>"><?php echo esc_html($slider['name']); ?></a> &raquo;
        <?php echo esc_html($page_title); ?>
    </p>

    <?php settings_errors('cta_slider_messages'); ?>

    <form method="post" action="">
        <input type="hidden" name="cta_slider_action" value="save_slide">
        <input type="hidden" name="slider_id" value="<?php echo esc_attr($slider['id']); ?>">
        <?php if (!$is_new) : ?>
            <input type="hidden" name="slide_id" value="<?php echo absint($slide['id']); ?>">
        <?php endif; ?>
        <input type="hidden" name="cta_slider_nonce" value="<?php echo esc_attr($this->security->create_nonce('save_slide')); ?>">
        <input type="hidden" name="image_id" id="slide_image_id" value="<?php echo absint($slide['image_id']); ?>">

        <table class="form-table" role="presentation">
            <!-- Image Section -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Slide Image', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label><?php echo esc_html__('Image', 'cta-slider'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <div id="slide-image-preview">
                        <?php if (!empty($slide['image_url'])) : ?>
                            <img src="<?php echo esc_url($slide['image_url']); ?>" alt="" style="max-width: 400px; height: auto; display: block; margin-bottom: 10px;">
                        <?php endif; ?>
                    </div>
                    <button type="button" id="upload-image-button" class="button">
                        <?php echo empty($slide['image_url']) ? esc_html__('Select Image', 'cta-slider') : esc_html__('Change Image', 'cta-slider'); ?>
                    </button>
                    <?php if (!empty($slide['image_url'])) : ?>
                        <button type="button" id="remove-image-button" class="button" style="color: #b32d2e;">
                            <?php echo esc_html__('Remove Image', 'cta-slider'); ?>
                        </button>
                    <?php endif; ?>
                    <p class="description"><?php echo esc_html__('Select or upload an image for this slide. Recommended size: 1200x600 pixels.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="image_alt"><?php echo esc_html__('Image Alt Text', 'cta-slider'); ?></label>
                </th>
                <td>
                    <input type="text" id="image_alt" name="image_alt" value="<?php echo esc_attr($slide['image_alt']); ?>" class="regular-text">
                    <p class="description"><?php echo esc_html__('Alternative text for accessibility and SEO.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <!-- Caption Section -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Caption', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="caption_enabled"><?php echo esc_html__('Enable Caption', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="caption_enabled" name="caption_enabled" value="1" <?php checked($slide['caption_enabled'], true); ?>>
                        <?php echo esc_html__('Show caption overlay on this slide', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <tr class="caption-field">
                <th scope="row">
                    <label for="caption_title"><?php echo esc_html__('Caption Title', 'cta-slider'); ?></label>
                </th>
                <td>
                    <input type="text" id="caption_title" name="caption_title" value="<?php echo esc_attr($slide['caption_title']); ?>" class="large-text">
                    <p class="description"><?php echo esc_html__('Main heading displayed on the slide.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr class="caption-field">
                <th scope="row">
                    <label for="caption_text"><?php echo esc_html__('Caption Text', 'cta-slider'); ?></label>
                </th>
                <td>
                    <textarea id="caption_text" name="caption_text" rows="4" class="large-text"><?php echo esc_textarea($slide['caption_text']); ?></textarea>
                    <p class="description"><?php echo esc_html__('Supporting text displayed below the caption title.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <!-- CTA Button Section -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Call to Action Button', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="button_enabled"><?php echo esc_html__('Enable Button', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="button_enabled" name="button_enabled" value="1" <?php checked($slide['button_enabled'], true); ?>>
                        <?php echo esc_html__('Show call-to-action button on this slide', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <tr class="button-field">
                <th scope="row">
                    <label for="button_text"><?php echo esc_html__('Button Text', 'cta-slider'); ?></label>
                </th>
                <td>
                    <input type="text" id="button_text" name="button_text" value="<?php echo esc_attr($slide['button_text']); ?>" class="regular-text">
                    <p class="description"><?php echo esc_html__('Text displayed on the button (e.g., "Learn More", "Shop Now", "Get Started").', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr class="button-field">
                <th scope="row">
                    <label for="button_url"><?php echo esc_html__('Button URL', 'cta-slider'); ?></label>
                </th>
                <td>
                    <input type="url" id="button_url" name="button_url" value="<?php echo esc_url($slide['button_url']); ?>" class="large-text">
                    <p class="description"><?php echo esc_html__('Destination URL when button is clicked.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr class="button-field">
                <th scope="row">
                    <label for="button_style"><?php echo esc_html__('Button Style', 'cta-slider'); ?></label>
                </th>
                <td>
                    <select id="button_style" name="button_style">
                        <option value="btn-primary" <?php selected($slide['button_style'], 'btn-primary'); ?>><?php echo esc_html__('Primary (Blue)', 'cta-slider'); ?></option>
                        <option value="btn-secondary" <?php selected($slide['button_style'], 'btn-secondary'); ?>><?php echo esc_html__('Secondary (Gray)', 'cta-slider'); ?></option>
                        <option value="btn-success" <?php selected($slide['button_style'], 'btn-success'); ?>><?php echo esc_html__('Success (Green)', 'cta-slider'); ?></option>
                        <option value="btn-danger" <?php selected($slide['button_style'], 'btn-danger'); ?>><?php echo esc_html__('Danger (Red)', 'cta-slider'); ?></option>
                        <option value="btn-warning" <?php selected($slide['button_style'], 'btn-warning'); ?>><?php echo esc_html__('Warning (Yellow)', 'cta-slider'); ?></option>
                        <option value="btn-info" <?php selected($slide['button_style'], 'btn-info'); ?>><?php echo esc_html__('Info (Cyan)', 'cta-slider'); ?></option>
                        <option value="btn-light" <?php selected($slide['button_style'], 'btn-light'); ?>><?php echo esc_html__('Light (White)', 'cta-slider'); ?></option>
                        <option value="btn-dark" <?php selected($slide['button_style'], 'btn-dark'); ?>><?php echo esc_html__('Dark (Black)', 'cta-slider'); ?></option>
                    </select>
                    <p class="description"><?php echo esc_html__('Bootstrap button style/color.', 'cta-slider'); ?></p>
                </td>
            </tr>

            <tr class="button-field">
                <th scope="row">
                    <label for="button_new_tab"><?php echo esc_html__('Open in New Tab', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="button_new_tab" name="button_new_tab" value="1" <?php checked($slide['button_new_tab'], true); ?>>
                        <?php echo esc_html__('Open link in a new browser tab', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>

            <!-- Settings Section -->
            <tr>
                <th scope="row" colspan="2">
                    <h2><?php echo esc_html__('Slide Settings', 'cta-slider'); ?></h2>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="active"><?php echo esc_html__('Status', 'cta-slider'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="active" name="active" value="1" <?php checked($slide['active'], true); ?>>
                        <?php echo esc_html__('Active (slide will be displayed in carousel)', 'cta-slider'); ?>
                    </label>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Save Slide', 'cta-slider'); ?>">
            <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'slides', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>" class="button">
                <?php echo esc_html__('Cancel', 'cta-slider'); ?>
            </a>
        </p>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Toggle caption fields visibility
    function toggleCaptionFields() {
        if ($('#caption_enabled').is(':checked')) {
            $('.caption-field').show();
        } else {
            $('.caption-field').hide();
        }
    }

    // Toggle button fields visibility
    function toggleButtonFields() {
        if ($('#button_enabled').is(':checked')) {
            $('.button-field').show();
        } else {
            $('.button-field').hide();
        }
    }

    // Initialize visibility
    toggleCaptionFields();
    toggleButtonFields();

    // Handle checkbox changes
    $('#caption_enabled').on('change', toggleCaptionFields);
    $('#button_enabled').on('change', toggleButtonFields);

    // WordPress Media Uploader
    var mediaUploader;

    $('#upload-image-button').on('click', function(e) {
        e.preventDefault();

        // If uploader already exists, open it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create new media uploader
        mediaUploader = wp.media({
            title: ctaSliderAdmin.strings.select_image,
            button: {
                text: ctaSliderAdmin.strings.use_image
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        // When image is selected
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            // Set image ID
            $('#slide_image_id').val(attachment.id);

            // Set image alt if empty
            if ($('#image_alt').val() === '') {
                $('#image_alt').val(attachment.alt || attachment.title || '');
            }

            // Show preview
            var imgHtml = '<img src="' + attachment.url + '" alt="" style="max-width: 400px; height: auto; display: block; margin-bottom: 10px;">';
            $('#slide-image-preview').html(imgHtml);

            // Update buttons
            $('#upload-image-button').text('<?php echo esc_js(__('Change Image', 'cta-slider')); ?>');
            if ($('#remove-image-button').length === 0) {
                $('#upload-image-button').after('<button type="button" id="remove-image-button" class="button" style="color: #b32d2e; margin-left: 5px;"><?php echo esc_js(__('Remove Image', 'cta-slider')); ?></button>');
                bindRemoveButton();
            }
        });

        mediaUploader.open();
    });

    // Handle remove image
    function bindRemoveButton() {
        $('#remove-image-button').on('click', function(e) {
            e.preventDefault();

            if (!confirm('<?php echo esc_js(__('Are you sure you want to remove this image?', 'cta-slider')); ?>')) {
                return;
            }

            $('#slide_image_id').val('');
            $('#slide-image-preview').html('');
            $('#upload-image-button').text('<?php echo esc_js(__('Select Image', 'cta-slider')); ?>');
            $(this).remove();
        });
    }

    bindRemoveButton();

    // Form validation
    $('form').on('submit', function(e) {
        if ($('#slide_image_id').val() === '' || $('#slide_image_id').val() === '0') {
            alert('<?php echo esc_js(__('Please select an image for this slide.', 'cta-slider')); ?>');
            e.preventDefault();
            return false;
        }
    });
});
</script>

<style>
.required {
    color: #d63638;
}
.cta-slider-breadcrumb {
    font-size: 13px;
    margin: 10px 0;
}
.cta-slider-breadcrumb a {
    text-decoration: none;
}
#slide-image-preview img {
    border: 1px solid #ddd;
    padding: 5px;
}
</style>
