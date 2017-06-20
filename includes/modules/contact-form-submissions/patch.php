<?php

/**
 * Target Plugin: Contact Form 7 Submissions (contact-form-submissions)
 * Target Version: 1.5.5
 *  Reason: To solve the potential unexisting $_GET['wpcf7_contact_form']
 */

WPCF7SAdmin_patch::instance();

class WPCF7SAdmin_patch extends WPCF7SAdmin {

	public static function instance() {
		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;
	}

	public function __construct() {
        // error_log("Already comes into Patch for Contact Form 7 Submissions. <br />");
        remove_action('admin_init', 'contact_form_submissions_admin_init');
        add_action('admin_init', array($this, 'replace_origin_actions'));
	}

	public function replace_origin_actions(){
        global $contact_form_submissions_admin;
        remove_action('restrict_manage_posts', array($contact_form_submissions_admin, 'filters'));
        add_action('restrict_manage_posts', array($this, 'filters'));
        // global $wp_filter;
        // error_log(print_r($wp_filter['restrict_manage_posts'], true));
    }

    public function filters()
    {
        $contact_form_target = (isset( $_GET['wpcf7_contact_form'] )) ? $_GET['wpcf7_contact_form'] : 0;

        global $post_type;
        if ($post_type == 'wpcf7s') {
            $args = array(
                'post_type'      =>'wpcf7_contact_form',
                'posts_per_page' => '-1'
            );
            $forms = get_posts($args); ?>
            <select name="wpcf7_contact_form">
                <option value="0"><?php _e('Contact Form', 'contact-form-submissions'); ?></option>
                <?php foreach ($forms as $post) {
                    ?>
                    <?php $selected = ($post->ID == $contact_form_target) ? 'selected' : ''; ?>
                    <option value="<?php echo $post->ID; ?>" <?php echo $selected; ?>><?php echo $post->post_title; ?></option>
                    <?php
                } ?>
            </select>
            <?php
        }
    }
}
