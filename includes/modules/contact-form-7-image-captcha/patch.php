<?php

/**
 * Target Plugin: Contact Form 7 Image Captcha (cf7-image-captcha)
 * Target Version: 2.2.0
 *  Reason: To solve the compability of wpcf7_add_shortcode(), wpcf7_remove_shortcode(), wpcf7_scan_shortcode(), WPCF7_Shortcode, and WPCF7_ShortcodeManager
 */

cf7ic_patch::instance();

class cf7ic_patch {

	public static function instance() {
		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;
	}

	private function __construct() {
        // error_log("Already comes into Patch for Contact Form 7 Image Captcha. <br />");
        remove_action('wpcf7_init', 'add_shortcode_cf7ic');
        remove_filter('wpcf7_validate_cf7ic*','cf7ic_check_if_spam', 10, 2);
        remove_filter('wpcf7_validate_cf7ic','cf7ic_check_if_spam', 10, 2);
        add_action('wpcf7_init', array($this, 'add_formtag_cf7ic' ));
        add_filter('wpcf7_validate_cf7ic*',array($this, 'cf7ic_check_if_spam' ), 10, 2);
        add_filter('wpcf7_validate_cf7ic',array($this, 'cf7ic_check_if_spam' ), 10, 2);
		// global $wp_filter;
        // error_log(print_r($wp_filter['wpcf7_form_elements'], true));
	}

    function add_formtag_cf7ic() {
        wpcf7_remove_form_tag('cf7ic');

        wpcf7_add_form_tag( 'cf7ic', array(__CLASS__, 'call_cf7ic'), true );
    }

    public static function call_cf7ic( $tag ) {
        $tag = new WPCF7_FormTag( $tag );
        wp_enqueue_style( 'cf7ic_style' ); // enqueue css

        // Create an array to hold the image library
        $captchas = array(
            __( 'Heart', 'cf7-image-captcha') => "fa-heart",
            __( 'House', 'cf7-image-captcha') => "fa-home",
            __( 'Star', 'cf7-image-captcha')  => "fa-star",
            __( 'Car', 'cf7-image-captcha')   => "fa-car",
            __( 'Cup', 'cf7-image-captcha')   => "fa-coffee",
            __( 'Flag', 'cf7-image-captcha')  => "fa-flag",
            __( 'Key', 'cf7-image-captcha')   => "fa-key",
            __( 'Truck', 'cf7-image-captcha') => "fa-truck",
            __( 'Tree', 'cf7-image-captcha')  => "fa-tree",
            __( 'Plane', 'cf7-image-captcha') => "fa-plane"
        );

        $choice = array_rand( $captchas, 3);
        foreach($choice as $key) {
            $choices[$key] = $captchas[$key];
        }

        // Pick a number between 0-2 and use it to determine which array item will be used as the answer
        $human = rand(0,2);

        $output = '
    <span class="captcha-image">
        <span class="cf7ic_instructions">';
        $output .= __('Please prove you are human by selecting the ', 'cf7-image-captcha');
        $output .= '<span>'.$choice[$human].'</span>';
        $output .= __('.', 'cf7-image-captcha').'</span>';
        $i = -1;
        foreach($choices as $title => $image) {
            $i++;
            if($i == $human) { $value = "kc_human"; } else { $value = "bot"; };
            $output .= '<label><input type="radio" name="kc_captcha" value="'. $value .'" /><i class="fa '. $image .'"></i></label>';
        }
        $output .= '
    </span>
    <span style="display:none">
        <input type="text" name="kc_honeypot">
    </span>';

        return '<span class="wpcf7-form-control-wrap kc_captcha"><span class="wpcf7-form-control wpcf7-radio">'.$output.'</span></span>';
    }

    function cf7ic_check_if_spam( $result, $tag ) {
        $tag = new WPCF7_FormTag( $tag );
        $kc_val1 = isset( $_POST['kc_captcha'] ) ? trim( $_POST['kc_captcha'] ) : '';   // Get selected icon value
        $kc_val2 = isset( $_POST['kc_honeypot'] ) ? trim( $_POST['kc_honeypot'] ) : ''; // Get honeypot value

        if(!empty($kc_val1) && $kc_val1 != 'kc_human' ) {
            $tag->name = "kc_captcha";
            $result->invalidate( $tag, __('Please select the correct icon.', 'cf7-image-captcha') );
        }
        if(empty($kc_val1) ) {
            $tag->name = "kc_captcha";
            $result->invalidate( $tag, __('Please select an icon.', 'cf7-image-captcha') );
        }
        if(!empty($kc_val2) ) {
            $tag->name = "kc_captcha";
            $result->invalidate( $tag, wpcf7_get_message( 'spam' ) );
        }
        return $result;
    }
}
