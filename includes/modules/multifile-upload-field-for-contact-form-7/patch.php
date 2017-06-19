<?php

/**
 * Target Plugin: Multifile Upload Field for Contact Form 7 (multifile-upload-field-for-contact-form-7)
 * Target Version: 1.0.1
 *  Reason: To solve the compability of WPCF7_ShortcodeManager::get_instance and WPCF7_ShortcodeManager->do_shortcode, wpcf7_scan_shortcode, $contact_form->form_scan_shortcode
 */

wpcf7_multifile_patch::instance();

class wpcf7_multifile_patch {

	public static function instance() {
		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;
	}

	private function __construct() {
        // error_log("Already comes into Patch for Multifile Upload Field for Contact Form 7. <br />");
        remove_action( 'wpcf7_init', 'wpcf7_add_shortcode_multifile' );
        remove_filter( 'wpcf7_form_enctype', 'wpcf7_multifile_form_enctype_filter' );
        remove_action( 'wpcf7_admin_notices', 'wpcf7_multifile_display_warning_message' );

        add_action( 'wpcf7_init', array($this, 'wpcf7_add_formtag_multifile') );
        add_filter( 'wpcf7_form_enctype', array($this, 'wpcf7_multifile_form_enctype_filter') );
        add_action( 'wpcf7_admin_notices', array($this, 'wpcf7_multifile_display_warning_message') );

        // global $wp_filter;
        // error_log(print_r($wp_filter['wpcf7_init'], true));
	}

    function wpcf7_add_formtag_multifile() {
        wpcf7_remove_form_tag( 'multifile' );
        wpcf7_remove_form_tag( 'multifile*' );

        wpcf7_add_form_tag( array( 'multifile', 'multifile*' ), 'wpcf7_multifile_formtag_handler', true );
    }

    function wpcf7_multifile_formtag_handler( $tag ) {
        $tag = new WPCF7_FormTag( $tag );

        if ( empty( $tag->name ) ) {
            return '';
        }

        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error ) {
            $class .= ' wpcf7-not-valid';
        }

        $atts = array();

        $atts['size'] = $tag->get_size_option( '40' );
        $atts['class'] = $tag->get_class_option( $class );
        $atts['id'] = $tag->get_id_option();
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
        $atts['accept'] = $tag->get_option( 'accept', null, true);
        $atts['multiple'] = 'multiple';

        $accept_wildcard = '';
        $accept_wildcard = $tag->get_option( 'accept_wildcard');

        if ( !empty($accept_wildcard)) {
            $atts['accept'] = $atts['accept'] .'/*';
        }
        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $atts['type'] = 'file';
        $atts['name'] = $tag->name.'[]';

        $atts = apply_filters('cf7_multifile_atts', $atts);

        $atts = wpcf7_format_atts( $atts );

        $html = sprintf(
            apply_filters('cf7_multifile_input', '<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>', $atts),
            sanitize_html_class( $tag->name ), $atts, $validation_error );

        return $html;
    }

    function wpcf7_multifile_form_enctype_filter( $enctype ) {
        $multipart = (bool) wpcf7_scan_form_tags( array( 'type' => array( 'multifile', 'multifile*' ) ) );

        if ( $multipart ) {
            $enctype = 'multipart/form-data';
        }

        return $enctype;
    }

    function wpcf7_multifile_display_warning_message() {
        if ( ! $contact_form = wpcf7_get_current_contact_form() ) {
            return;
        }

        $has_tags = (bool) $contact_form->scan_form_tags(
            array( 'type' => array( 'multifile', 'multifile*' ) ) );

        if ( ! $has_tags ) {
            return;
        }

        $uploads_dir = wpcf7_upload_tmp_dir();
        wpcf7_init_uploads();

        if ( ! is_dir( $uploads_dir ) || ! wp_is_writable( $uploads_dir ) ) {
            $message = sprintf( __( 'This contact form contains file uploading fields, but the temporary folder for the files (%s) does not exist or is not writable. You can create the folder or change its permission manually.', 'contact-form-7' ), $uploads_dir );

            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
        }
    }
}
