<?php
/**
 * Admin Page - Slide Management View
 *
 * Interface for managing slides within a slider
 *
 * @package    CTA_Slider
 * @subpackage CTA_Slider/admin/partials
 * @since      1.0.0
 */

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php echo esc_html__('Manage Slides', 'cta-slider'); ?>:
        <?php echo esc_html($slider['name']); ?>
    </h1>
    <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit_slide', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>" class="page-title-action">
        <?php echo esc_html__('Add New Slide', 'cta-slider'); ?>
    </a>

    <hr class="wp-header-end">

    <!-- Breadcrumb -->
    <p class="cta-slider-breadcrumb">
        <a href="<?php echo esc_url(admin_url('admin.php?page=cta-slider')); ?>"><?php echo esc_html__('CTA Sliders', 'cta-slider'); ?></a> &raquo;
        <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>"><?php echo esc_html($slider['name']); ?></a> &raquo;
        <?php echo esc_html__('Manage Slides', 'cta-slider'); ?>
    </p>

    <?php settings_errors('cta_slider_messages'); ?>

    <!-- Slider Info Box -->
    <div class="notice notice-info inline" style="margin: 15px 0;">
        <p>
            <strong><?php echo esc_html__('Shortcode:', 'cta-slider'); ?></strong>
            <code style="background: #fff; padding: 5px 10px; margin: 0 5px;">[cta_slider id="<?php echo esc_attr($slider['id']); ?>"]</code>
            <button type="button" class="button button-small cta-slider-copy-shortcode" data-shortcode='[cta_slider id="<?php echo esc_attr($slider['id']); ?>"]'>
                <?php echo esc_html__('Copy', 'cta-slider'); ?>
            </button>
            <span style="margin-left: 20px;">
                <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>">
                    <?php echo esc_html__('Edit Slider Settings', 'cta-slider'); ?>
                </a>
            </span>
        </p>
    </div>

    <?php if (empty($slides)) : ?>
        <div class="notice notice-warning inline">
            <p><?php echo esc_html__('No slides found. Add your first slide to get started!', 'cta-slider'); ?></p>
        </div>
    <?php else : ?>
        <p class="description">
            <?php echo esc_html__('Drag and drop slides to reorder them.', 'cta-slider'); ?>
        </p>

        <table class="wp-list-table widefat fixed striped cta-slider-slides-table">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-order" style="width: 50px;">
                        <?php echo esc_html__('Order', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column column-image" style="width: 150px;">
                        <?php echo esc_html__('Image', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column column-caption column-primary">
                        <?php echo esc_html__('Caption', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column column-button" style="width: 200px;">
                        <?php echo esc_html__('CTA Button', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column column-status" style="width: 80px;">
                        <?php echo esc_html__('Status', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column column-actions" style="width: 150px;">
                        <?php echo esc_html__('Actions', 'cta-slider'); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="cta-slider-slides-list">
                <?php foreach ($slides as $slide) : ?>
                    <tr data-slide-id="<?php echo absint($slide['id']); ?>">
                        <td class="column-order">
                            <span class="dashicons dashicons-menu drag-handle" style="cursor: move;" title="<?php echo esc_attr__('Drag to reorder', 'cta-slider'); ?>"></span>
                            <span class="slide-order-number"><?php echo absint($slide['slide_order']) + 1; ?></span>
                        </td>
                        <td class="column-image">
                            <?php if (!empty($slide['image_url'])) : ?>
                                <img src="<?php echo esc_url($slide['image_url']); ?>" alt="<?php echo esc_attr($slide['image_alt']); ?>" style="max-width: 120px; height: auto; display: block;">
                            <?php else : ?>
                                <span class="dashicons dashicons-format-image" style="font-size: 48px; color: #ccc;"></span>
                            <?php endif; ?>
                        </td>
                        <td class="column-caption column-primary">
                            <?php if ($slide['caption_enabled']) : ?>
                                <?php if (!empty($slide['caption_title'])) : ?>
                                    <strong><?php echo esc_html($slide['caption_title']); ?></strong><br>
                                <?php endif; ?>
                                <?php if (!empty($slide['caption_text'])) : ?>
                                    <span class="description"><?php echo esc_html(wp_trim_words($slide['caption_text'], 15)); ?></span>
                                <?php endif; ?>
                                <?php if (empty($slide['caption_title']) && empty($slide['caption_text'])) : ?>
                                    <em><?php echo esc_html__('Caption enabled (no text)', 'cta-slider'); ?></em>
                                <?php endif; ?>
                            <?php else : ?>
                                <em style="color: #999;"><?php echo esc_html__('No caption', 'cta-slider'); ?></em>
                            <?php endif; ?>
                        </td>
                        <td class="column-button">
                            <?php if ($slide['button_enabled'] && !empty($slide['button_text'])) : ?>
                                <span class="button <?php echo esc_attr($slide['button_style']); ?>" style="pointer-events: none;">
                                    <?php echo esc_html($slide['button_text']); ?>
                                </span>
                                <br>
                                <small class="description"><?php echo esc_html(wp_trim_words($slide['button_url'], 5)); ?></small>
                            <?php else : ?>
                                <em style="color: #999;"><?php echo esc_html__('No button', 'cta-slider'); ?></em>
                            <?php endif; ?>
                        </td>
                        <td class="column-status">
                            <?php if ($slide['active']) : ?>
                                <span class="dashicons dashicons-yes-alt" style="color: #46b450; font-size: 20px;" title="<?php echo esc_attr__('Enabled', 'cta-slider'); ?>"></span>
                            <?php else : ?>
                                <span class="dashicons dashicons-dismiss" style="color: #dc3232; font-size: 20px;" title="<?php echo esc_attr__('Disabled', 'cta-slider'); ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td class="column-actions">
                            <?php if ($slide['active']) : ?>
                                <button type="button" class="button button-small cta-slider-toggle-slide"
                                        data-slide-id="<?php echo absint($slide['id']); ?>"
                                        data-current-status="1"
                                        style="background: #dc3232; color: white; border-color: #dc3232;">
                                    <?php echo esc_html__('Disable', 'cta-slider'); ?>
                                </button>
                            <?php else : ?>
                                <button type="button" class="button button-small cta-slider-toggle-slide"
                                        data-slide-id="<?php echo absint($slide['id']); ?>"
                                        data-current-status="0"
                                        style="background: #46b450; color: white; border-color: #46b450;">
                                    <?php echo esc_html__('Enable', 'cta-slider'); ?>
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit_slide', 'slider_id' => $slider['id'], 'slide_id' => $slide['id']), admin_url('admin.php'))); ?>" class="button button-small">
                                <?php echo esc_html__('Edit', 'cta-slider'); ?>
                            </a>
                            <button type="button" class="button button-small cta-slider-delete-slide" data-slide-id="<?php echo absint($slide['id']); ?>" style="color: #b32d2e;">
                                <?php echo esc_html__('Delete', 'cta-slider'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p>
        <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit_slide', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>" class="button button-primary">
            <?php echo esc_html__('Add New Slide', 'cta-slider'); ?>
        </a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=cta-slider')); ?>" class="button">
            <?php echo esc_html__('Back to Sliders', 'cta-slider'); ?>
        </a>
    </p>
</div>

<!-- Hidden delete form -->
<form id="cta-slider-delete-slide-form" method="post" style="display: none;">
    <input type="hidden" name="cta_slider_action" value="delete_slide">
    <input type="hidden" name="slider_id" value="<?php echo esc_attr($slider['id']); ?>">
    <input type="hidden" name="slide_id" id="delete-slide-id" value="">
    <input type="hidden" name="cta_slider_nonce" value="<?php echo esc_attr($this->security->create_nonce('delete_slide')); ?>">
</form>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Make slides sortable
    $('#cta-slider-slides-list').sortable({
        handle: '.drag-handle',
        axis: 'y',
        cursor: 'move',
        opacity: 0.6,
        update: function(event, ui) {
            // Get new order
            var slideOrder = [];
            $('#cta-slider-slides-list tr').each(function(index) {
                var slideId = $(this).data('slide-id');
                slideOrder.push(slideId);
                // Update order number display
                $(this).find('.slide-order-number').text(index + 1);
            });

            // Save via AJAX
            $.ajax({
                url: ctaSliderAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'cta_slider_reorder_slides',
                    nonce: ctaSliderAdmin.nonce,
                    slider_id: '<?php echo esc_js($slider['id']); ?>',
                    slide_order: slideOrder
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message briefly
                        var $notice = $('<div class="notice notice-success is-dismissible" style="margin: 10px 0;"><p>' + response.data.message + '</p></div>');
                        $('.wrap h1').after($notice);
                        setTimeout(function() {
                            $notice.fadeOut(function() { $(this).remove(); });
                        }, 3000);
                    }
                },
                error: function() {
                    alert(ctaSliderAdmin.strings.error_occurred);
                }
            });
        }
    });

    // Handle toggle slide status
    $('.cta-slider-toggle-slide').on('click', function(e) {
        e.preventDefault();

        var $btn = $(this);
        var slideId = $btn.data('slide-id');
        var currentStatus = $btn.data('current-status');
        var newStatus = currentStatus === 1 ? 0 : 1;

        // Disable button during request
        $btn.prop('disabled', true);

        $.ajax({
            url: ctaSliderAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'cta_slider_toggle_slide',
                nonce: ctaSliderAdmin.nonce,
                slide_id: slideId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    // Update button
                    $btn.data('current-status', newStatus);

                    if (newStatus === 1) {
                        // Now enabled
                        $btn.text('<?php echo esc_js(__('Disable', 'cta-slider')); ?>');
                        $btn.css({
                            'background': '#dc3232',
                            'border-color': '#dc3232',
                            'color': 'white'
                        });
                        // Update status icon
                        $btn.closest('tr').find('.column-status').html('<span class="dashicons dashicons-yes-alt" style="color: #46b450; font-size: 20px;" title="<?php echo esc_attr__('Enabled', 'cta-slider'); ?>"></span>');
                    } else {
                        // Now disabled
                        $btn.text('<?php echo esc_js(__('Enable', 'cta-slider')); ?>');
                        $btn.css({
                            'background': '#46b450',
                            'border-color': '#46b450',
                            'color': 'white'
                        });
                        // Update status icon
                        $btn.closest('tr').find('.column-status').html('<span class="dashicons dashicons-dismiss" style="color: #dc3232; font-size: 20px;" title="<?php echo esc_attr__('Disabled', 'cta-slider'); ?>"></span>');
                    }

                    // Show success message
                    var $notice = $('<div class="notice notice-success is-dismissible" style="margin: 10px 0;"><p>' + response.data.message + '</p></div>');
                    $('.wrap h1').after($notice);
                    setTimeout(function() {
                        $notice.fadeOut(function() { $(this).remove(); });
                    }, 3000);
                } else {
                    alert(response.data.message || ctaSliderAdmin.strings.error_occurred);
                }
                $btn.prop('disabled', false);
            },
            error: function() {
                alert(ctaSliderAdmin.strings.error_occurred);
                $btn.prop('disabled', false);
            }
        });
    });

    // Handle delete slide
    $('.cta-slider-delete-slide').on('click', function(e) {
        e.preventDefault();

        if (!confirm(ctaSliderAdmin.strings.confirm_delete_slide)) {
            return;
        }

        var slideId = $(this).data('slide-id');
        $('#delete-slide-id').val(slideId);
        $('#cta-slider-delete-slide-form').submit();
    });

    // Handle copy shortcode
    $('.cta-slider-copy-shortcode').on('click', function(e) {
        e.preventDefault();

        var shortcode = $(this).data('shortcode');
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(shortcode).select();
        document.execCommand('copy');
        $temp.remove();

        var $btn = $(this);
        var originalText = $btn.text();
        $btn.text('<?php echo esc_js(__('Copied!', 'cta-slider')); ?>');

        setTimeout(function() {
            $btn.text(originalText);
        }, 2000);
    });
});
</script>

<style>
.cta-slider-breadcrumb {
    font-size: 13px;
    margin: 10px 0;
}
.cta-slider-breadcrumb a {
    text-decoration: none;
}
.drag-handle {
    color: #999;
}
.drag-handle:hover {
    color: #333;
}
.cta-slider-slides-table .ui-sortable-helper {
    background: #f0f0f1;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
</style>
