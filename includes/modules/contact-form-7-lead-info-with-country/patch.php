<?php

/**
 * Target Plugin: Contact Form 7 Lead Info with Country (wpshore_cf7_lead_tracking)
 * Target Version: 1.4.0
 *  Reason: To solve the typo of $CF7isSecure to $cf7ltisSecure
 */

wpshore_cf7_lead_tracking_patch::instance();

class wpshore_cf7_lead_tracking_patch {

	public static function instance() {
		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;
	}

	private function __construct() {
        // error_log("Already comes into Patch for Contact Form 7 Lead info with country. <br />");
        remove_action('init', 'wpshore_set_session_values', 10);
        add_action('init', array($this, 'wpshore_set_session_values' ), 10);
		// global $wp_filter;
        // error_log(print_r($wp_filter['wpcf7_form_elements'], true));
	}

    function wpshore_set_session_values() {
        if (!session_id()) {
            session_start();
        }
        if (!isset($_SESSION['OriginalRef'])) {
            if(isset($_SERVER['HTTP_REFERER'])) {
                $_SESSION['OriginalRef'] = $_SERVER["HTTP_REFERER"];
            } else {
                $_SESSION['OriginalRef'] = __('not set','wpshore_cf7_lead_tracking');
            }
        }
        if( $_SESSION['OriginalRef'] == 'not set' ) {
            $_SESSION['OriginalRef'] = __('not set','wpshore_cf7_lead_tracking');
        }
        if (!isset($_SESSION['LandingPage'])) {
            $cf7ltisSecure = false;
            // if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') || $_SERVER['SERVER_PORT'] == 443 {
            // The server port check is an extra for sheetty servers, best to remove it if it is not needed. Nb: port 443 does not guarantee connection is encrypted.
            if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
                $cf7ltisSecure = true;
            }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
                $cf7ltisSecure = true;
            }
            $CF7LT_REQUEST_PROTOCOL = $cf7ltisSecure ? 'https' : 'http';
            $_SESSION['LandingPage'] = $CF7LT_REQUEST_PROTOCOL . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
    }
}
