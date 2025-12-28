<?php
/**
 * Admin Main Page - Slider List View
 *
 * Displays list of all sliders with management actions
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
    <h1 class="wp-heading-inline"><?php echo esc_html__('CTA Sliders', 'cta-slider'); ?></h1>
    <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit'), admin_url('admin.php'))); ?>" class="page-title-action">
        <?php echo esc_html__('Add New', 'cta-slider'); ?>
    </a>

    <hr class="wp-header-end">

    <?php settings_errors('cta_slider_messages'); ?>

    <?php if (empty($sliders)) : ?>
        <div class="notice notice-info">
            <p><?php echo esc_html__('No sliders found. Create your first slider to get started!', 'cta-slider'); ?></p>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-name column-primary">
                        <?php echo esc_html__('Name', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php echo esc_html__('Slider ID', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php echo esc_html__('Slides', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php echo esc_html__('Status', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php echo esc_html__('Shortcode', 'cta-slider'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php echo esc_html__('Actions', 'cta-slider'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sliders as $slider) : ?>
                    <tr>
                        <td class="column-name column-primary" data-colname="<?php echo esc_attr__('Name', 'cta-slider'); ?>">
                            <strong>
                                <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'slides', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>">
                                    <?php echo esc_html($slider['name']); ?>
                                </a>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>">
                                        <?php echo esc_html__('Edit Settings', 'cta-slider'); ?>
                                    </a> |
                                </span>
                                <span class="slides">
                                    <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'slides', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>">
                                        <?php echo esc_html__('Manage Slides', 'cta-slider'); ?>
                                    </a> |
                                </span>
                                <span class="delete">
                                    <a href="#" class="cta-slider-delete-slider" data-slider-id="<?php echo esc_attr($slider['id']); ?>" style="color: #a00;">
                                        <?php echo esc_html__('Delete', 'cta-slider'); ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td data-colname="<?php echo esc_attr__('Slider ID', 'cta-slider'); ?>">
                            <code><?php echo esc_html($slider['id']); ?></code>
                        </td>
                        <td data-colname="<?php echo esc_attr__('Slides', 'cta-slider'); ?>">
                            <?php echo absint($slider['slide_count']); ?>
                        </td>
                        <td data-colname="<?php echo esc_attr__('Status', 'cta-slider'); ?>">
                            <?php if ($slider['active']) : ?>
                                <span class="dashicons dashicons-yes-alt" style="color: #46b450;" title="<?php echo esc_attr__('Active', 'cta-slider'); ?>"></span>
                                <?php echo esc_html__('Active', 'cta-slider'); ?>
                            <?php else : ?>
                                <span class="dashicons dashicons-dismiss" style="color: #dc3232;" title="<?php echo esc_attr__('Inactive', 'cta-slider'); ?>"></span>
                                <?php echo esc_html__('Inactive', 'cta-slider'); ?>
                            <?php endif; ?>
                        </td>
                        <td data-colname="<?php echo esc_attr__('Shortcode', 'cta-slider'); ?>">
                            <input type="text" readonly value='[cta_slider id="<?php echo esc_attr($slider['id']); ?>"]'
                                   class="cta-slider-shortcode-input"
                                   onclick="this.select();"
                                   style="width: 100%; max-width: 300px; background: #f0f0f1;">
                            <button type="button" class="button button-small cta-slider-copy-shortcode"
                                    data-shortcode='[cta_slider id="<?php echo esc_attr($slider['id']); ?>"]'
                                    style="margin-left: 5px;">
                                <?php echo esc_html__('Copy', 'cta-slider'); ?>
                            </button>
                        </td>
                        <td data-colname="<?php echo esc_attr__('Actions', 'cta-slider'); ?>">
                            <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'edit', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>" class="button button-small">
                                <?php echo esc_html__('Edit', 'cta-slider'); ?>
                            </a>
                            <a href="<?php echo esc_url(add_query_arg(array('page' => 'cta-slider', 'action' => 'slides', 'slider_id' => $slider['id']), admin_url('admin.php'))); ?>" class="button button-small button-primary">
                                <?php echo esc_html__('Slides', 'cta-slider'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Hidden delete form -->
<form id="cta-slider-delete-form" method="post" style="display: none;">
    <input type="hidden" name="cta_slider_action" value="delete_slider">
    <input type="hidden" name="slider_id" id="delete-slider-id" value="">
    <input type="hidden" name="cta_slider_nonce" value="<?php echo esc_attr($this->security->create_nonce('delete_slider')); ?>">
</form>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Handle delete slider
    $('.cta-slider-delete-slider').on('click', function(e) {
        e.preventDefault();

        if (!confirm(ctaSliderAdmin.strings.confirm_delete_slider)) {
            return;
        }

        var sliderId = $(this).data('slider-id');
        $('#delete-slider-id').val(sliderId);
        $('#cta-slider-delete-form').submit();
    });

    // Handle copy shortcode
    $('.cta-slider-copy-shortcode').on('click', function(e) {
        e.preventDefault();

        var shortcode = $(this).data('shortcode');
        var $input = $(this).prev('input');

        $input.select();
        document.execCommand('copy');

        var $btn = $(this);
        var originalText = $btn.text();
        $btn.text('<?php echo esc_js(__('Copied!', 'cta-slider')); ?>');

        setTimeout(function() {
            $btn.text(originalText);
        }, 2000);
    });
});
</script>
