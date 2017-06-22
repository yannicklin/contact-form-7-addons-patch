<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/yannicklin/contact-form-7-addons-patch/
 * @since      1.0.0
 *
 * @package    Contact_Form_7_Addons_Patch
 * @subpackage Contact_Form_7_Addons_Patch/admin/partials
 */
?>

<div class="wrap cf7apa">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <p><?php _e('The activation status of patches for responding plugins', 'contact-form-7-addons-patch'); ?>
        <ul>
            <li>Contact Form 7 DatePicker : <?php echo $this->plugin_active_check_html_output('contact-form-7-datepicker'); ?></li>
            <li>Contact Form 7 Controls : <?php echo $this->plugin_active_check_html_output('contact-form-7-extras'); ?></li>
            <li>Contact Form 7 Image Captcha : <?php echo $this->plugin_active_check_html_output('cf7-image-captcha'); ?></li>
            <li>Contact Form 7 Lead Info with Country : <?php echo $this->plugin_active_check_html_output('wpshore_cf7_lead_tracking'); ?></li>
            <li>Contact Form 7 Textarea Wordcount : <?php echo $this->plugin_active_check_html_output('contact-form-7-textarea-wordcount'); ?></li>
            <li>Contact Form 7 Submissions : <?php echo $this->plugin_active_check_html_output('contact-form-submissions'); ?></li>
            <li>Multifile Upload Field for Contact Form 7 : <?php echo $this->plugin_active_check_html_output('multifile-upload-field-for-contact-form-7'); ?></li>
            <li>Rich Text Editor Field for Contact Form 7 : <?php echo $this->plugin_active_check_html_output('rich-text-editor-field-for-contact-form-7'); ?></li>
        </ul>
    </p>
</div>
