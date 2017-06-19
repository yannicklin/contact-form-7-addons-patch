<?php

/**
 * Target Plugin: Contact Form 7 Controls (contact-form-7-extras)
 * Target Version: 0.3.5
 *  Reason: To solve the compability of WPCF7_ShortcodeManager::get_instance and WPCF7_ShortcodeManager->do_shortcode
 */

cf7_extras_patch::instance();

class cf7_extras_patch extends cf7_extras {

	public static function instance() {
		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;
	}

	private function __construct() {
        // error_log("Already comes into Patch for CF7_extras. <br />");
        remove_filter( 'wpcf7_form_elements', array( cf7_extras::instance(), 'maybe_reset_autop' ) );
		add_filter( 'wpcf7_form_elements', array( $this, 'maybe_reset_autop' ) );
		// global $wp_filter;
        // error_log(print_r($wp_filter['wpcf7_form_elements'], true));
	}

	function maybe_reset_autop( $form ) {

		$form_instance = WPCF7_ContactForm::get_current();
		$disable_autop = $this->get_form_settings( $form_instance, 'disable-autop' );
        // error_log("disable_autop option is : " . print_r($disable_autop, true));

		if ( $disable_autop ) {
			$manager = WPCF7_FormTagsManager::get_instance();

			$form_meta = get_post_meta( $form_instance->id(), '_form', true );
			$form = $manager->replace_all( $form_meta );

			$form_instance->set_properties( array(
					'form' => $form
				) );
		}

		return $form;
	}
}
