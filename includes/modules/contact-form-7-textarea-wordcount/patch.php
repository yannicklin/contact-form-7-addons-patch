<?php

/**
 * Target Plugin: Contact Form 7 Textarea Wordcount (contact-form-7-textarea-wordcount)
 * Target Version: 1.1.1
 *  Reason: To solve the compability of WPCF7_ShortcodeManager::get_instance and WPCF7_ShortcodeManager->do_shortcode
 */

wpcf7wc_patch::instance();

class wpcf7wc_patch {

	public static function instance() {
		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;
	}

    public function __construct() {
        // error_log("Already comes into Patch for Contact Form 7 Textarea Wordcount. <br />");
        remove_action( 'wpcf7_init', 'wpcf7wc_add_shortcode_textarea', 20 );
		add_action( 'wpcf7_init', array(__CLASS__,'wpcf7wc_add_formtag_textarea'), 20 );

		// global $wp_filter;
        // error_log(print_r($wp_filter['wpcf7_init'], true));
	}

    public static function wpcf7wc_add_formtag_textarea() {
        if ( function_exists('wpcf7_add_form_tag') ) {
            wpcf7_remove_form_tag( 'textarea' );
            wpcf7_remove_form_tag( 'textarea*' );
        }
        wpcf7_add_form_tag( array( 'textarea', 'textarea*' ), array(__CLASS__, 'wpcf7wc_textarea_formtag_handler'), true );
    }

    public static function wpcf7wc_textarea_formtag_handler( $tag ) {
        $tag = new WPCF7_FormTag( $tag );

        if ( empty( $tag->name ) )
            return '';

        $validation_error = wpcf7_get_validation_error( $tag->name );

        $class = wpcf7_form_controls_class( $tag->type );

        if ( $validation_error )
            $class .= ' wpcf7-not-valid';

        $atts = array();

        $atts['cols'] = $tag->get_cols_option( '40' );
        $atts['rows'] = $tag->get_rows_option( '10' );
        $atts['maxlength'] = $tag->get_maxlength_option();
        $atts['minlength'] = $tag->get_minlength_option();

        if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
            unset( $atts['maxlength'], $atts['minlength'] );
        }

        $atts['class'] = $tag->get_class_option( $class );
        $atts['id'] = $tag->get_id_option();
        $atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

        // add our maxwc
        $atts['data-maxwc'] = $tag->get_option( 'maxwc', 'int', true );

        if ( $tag->has_option( 'readonly' ) ) {
            $atts['readonly'] = 'readonly';
        }

        if ( $tag->is_required() ) {
            $atts['aria-required'] = 'true';
        }

        $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

        $value = empty( $tag->content )
            ? (string) reset( $tag->values )
            : $tag->content;

        if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
            $atts['placeholder'] = $value;
            $value = '';
        }

        $value = $tag->get_default_option( $value );

        $value = wpcf7_get_hangover( $tag->name, $value );

        $atts['name'] = $tag->name;

        // inject our word counter
        if( $atts['data-maxwc'] ) {
            $validation_error .= '<span class="wpcf7wc-msg"><br /><br /><input type="text" name="wcount_'. $atts['name'] .'" id="wcount_'. $atts['name'] .'" size="3" maxlength="'. ( $atts['data-maxwc'] % 10 ) .'" style="text-align:center; width: auto" value="" readonly="readonly" /> words. Please limit to '. $atts['data-maxwc'] .' words or less.</span>';
        }

        $atts = wpcf7_format_atts( $atts );

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap %1$s"><textarea %2$s>%3$s</textarea>%4$s</span>',
            sanitize_html_class( $tag->name ), $atts,
            esc_textarea( $value ), $validation_error );

        return $html;
    }
}
