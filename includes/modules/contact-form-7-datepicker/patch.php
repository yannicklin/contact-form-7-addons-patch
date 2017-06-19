<?php

/**
 * Target Plugin: Contact Form 7 DatePicker (contact-form-7-datepicker)
 * Target Version: 2.6.0
 *  Reason: To solve the compability of wpcf7_add_shortcode(), wpcf7_remove_shortcode(), wpcf7_scan_shortcode(), WPCF7_Shortcode, and WPCF7_ShortcodeManager
 */

ContactForm7Datepicker_patch::instance();

class ContactForm7Datepicker_patch extends ContactForm7Datepicker {

    static $inline_js = array();

	public static function instance() {
		static $instance;

		if ( ! $instance )
			$instance = new self();

		return $instance;
	}

    function __construct() {
         // error_log("Already comes into Patch for ContactForm7Datepicker.. <br />");

        if (has_filter('wpcf7_init', array('ContactForm7Datepicker_Date', 'add_shortcodes'))) {
            // error_log("Remove Date add_shortcodes. <br />");
            remove_action('wpcf7_init', array('ContactForm7Datepicker_Date', 'add_shortcodes'));
            add_action('wpcf7_init', array(__CLASS__, 'add_formtags_Date'));
        }

        if (has_filter('wpcf7_init', array('ContactForm7Datepicker_Time', 'add_shortcodes'))) {
            // error_log("Remove Time add_shortcodes. <br />");
            remove_action('wpcf7_init', array('ContactForm7Datepicker_Time', 'add_shortcodes'));
            add_action('wpcf7_init', array(__CLASS__, 'add_formtags_Time'));
        }

        if (has_filter('wpcf7_init', array('ContactForm7Datepicker_DateTime', 'add_shortcodes'))) {
            //error_log("Remove DateTime add_shortcodes. <br />");
            remove_action('wpcf7_init', array('ContactForm7Datepicker_DateTime', 'add_shortcodes'));
            add_action('wpcf7_init', array(__CLASS__, 'add_formtags_DateTime'));
        }

        // global $wp_filter;
        // error_log(print_r($wp_filter['wpcf7_init'], true));
	}

    public static function add_formtags_Date() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_remove_form_tag('date');
            wpcf7_remove_form_tag('date*');

            wpcf7_add_form_tag(array('date', 'date*'), array(__CLASS__, 'formtag_handler_Date'), true);
        }
    }

    public static function add_formtags_Time() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_remove_form_tag('time');
            wpcf7_remove_form_tag('time*');

            wpcf7_add_form_tag(array('time', 'time*'), array(__CLASS__, 'formtag_handler_Time'), true);
        }
    }

    public static function add_formtags_DateTime() {
        if (function_exists('wpcf7_add_form_tag')) {
            wpcf7_remove_form_tag('datetime');
            wpcf7_remove_form_tag('datetime*');

            wpcf7_add_form_tag(array('datetime', 'datetime*'), array(__CLASS__, 'formtag_handler_DateTime'), true);
        }
    }

    public static function formtag_handler_Date($tag) {
        $tag = new WPCF7_FormTag($tag);

        if (empty($tag->name))
            return '';

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type, 'wpcf7-date');

        if ($validation_error)
            $class .= ' wpcf7-not-valid';

        $atts = array();

        $atts['size'] = $tag->get_size_option('40');
        $atts['maxlength'] = $tag->get_maxlength_option();
        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_option('id', 'id', true);
        $atts['tabindex'] = $tag->get_option('tabindex', 'int', true);
        $atts['type'] = 'text';

        if ($tag->has_option('readonly'))
            $atts['readonly'] = 'readonly';

        if ($tag->is_required())
            $atts['aria-required'] = 'true';

        $value = (string)reset($tag->values);

        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $atts['placeholder'] = $value;
            $value = '';
        }

        if (wpcf7_is_posted() && isset($_POST[$tag->name]))
            $value = stripslashes_deep($_POST[$tag->name]);

        $atts['value'] = $value;

        $dpOptions = array();
        $dpOptions['dateFormat'] = str_replace('_', ' ', $tag->get_option('date-format', '', true));
        $dpOptions['minDate'] = $tag->get_option('min-date', '', true);
        $dpOptions['maxDate'] = $tag->get_option('max-date', '', true);
        $dpOptions['firstDay'] = (int)$tag->get_option('first-day', 'int', true);
        $dpOptions['showAnim'] = $tag->get_option('animate', '', true);
        $dpOptions['yearRange'] = str_replace('-', ':', $tag->get_option('year-range', '', true));
        $dpOptions['numberOfMonths'] = (int)$tag->get_option('months', 'int', true);

        $dpOptions['showButtonPanel'] = $tag->has_option('buttons');
        $dpOptions['changeMonth'] = $tag->has_option('change-month');
        $dpOptions['changeYear'] = $tag->has_option('change-year');
        $dpOptions['noWeekends'] = $tag->has_option('no-weekends');

        $inline = $tag->has_option('inline');

        if ($inline) {
            $dpOptions['altField'] = "#{$tag->name}_alt";
            $atts['id'] = "{$tag->name}_alt";
        }

        $atts['type'] = $inline ? 'hidden' : 'text';
        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts($atts);

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s %4$s</span>',
            $tag->name, $atts, $validation_error,
            $inline ? "<div id=\"{$tag->name}_datepicker\"></div>" : '');

        $html = apply_filters('cf7dp_date_input', $html);

        $dp_selector = $inline ? '#' . $tag->name . '_datepicker' : $tag->name;

        $dp = new CF7_DateTimePicker('date', $dp_selector, $dpOptions);
        self::$inline_js[] = $dp->generate_code($inline);

        /* Print inline javascript */
        remove_action('wp_print_footer_scripts', array('ContactForm7Datepicker_Date', 'print_inline_js'), 99999);
        add_action('wp_print_footer_scripts', array(__CLASS__, 'print_inline_js_wo_Time'), 99999);

        return $html;
    }

    public static function formtag_handler_Time($tag) {
        $tag = new WPCF7_FormTag($tag);

        if (empty($tag->name))
            return '';

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type, 'wpcf7-date');

        if ($validation_error)
            $class .= ' wpcf7-not-valid';

        $atts = array();

        $atts['size'] = $tag->get_size_option('40');
        $atts['maxlength'] = $tag->get_maxlength_option();
        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_option('id', 'id', true);
        $atts['tabindex'] = $tag->get_option('tabindex', 'int', true);
        $atts['type'] = 'text';

        if ($tag->has_option('readonly'))
            $atts['readonly'] = 'readonly';

        if ($tag->is_required())
            $atts['aria-required'] = 'true';

        $value = (string)reset($tag->values);

        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $atts['placeholder'] = $value;
            $value = '';
        }

        if (wpcf7_is_posted() && isset($_POST[$tag->name]))
            $value = stripslashes_deep($_POST[$tag->name]);

        $atts['value'] = $value;

        $dpOptions = array();
        $dpOptions['timeFormat'] = str_replace('_', ' ', $tag->get_option('time-format', '', true));
        $dpOptions['firstDay'] = (int)$tag->get_option('first-day', 'int', true);
        $dpOptions['showAnim'] = $tag->get_option('animate', '', true);
        $dpOptions['controlType'] = $tag->get_option('control-type', '', true);

        $dpOptions['showButtonPanel'] = $tag->has_option('buttons');
        $dpOptions['changeMonth'] = $tag->has_option('change-month');
        $dpOptions['changeYear'] = $tag->has_option('change-year');

        foreach (array('minute', 'hour', 'second') as $s) {
            foreach (array('min', 'max') as $m) {
                $dpOptions[$s . ucfirst($m)] = (int)$tag->get_option("$m-$s", 'int', true);
            }

            $dpOptions['step' . ucfirst($s)] = (int)$tag->get_option("step-$s", 'int', true);
        }

        $inline = $tag->has_option('inline');

        if ($inline) {
            $dpOptions['altField'] = "#{$tag->name}_alt";
            $atts['id'] = "{$tag->name}_alt";
        }

        $atts['type'] = $inline ? 'hidden' : 'text';
        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts($atts);

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s %4$s</span>',
            $tag->name, $atts, $validation_error,
            $inline ? "<div id=\"{$tag->name}_timepicker\"></div>" : '');

        $html = apply_filters('cf7dp_time_input', $html);

        $dp_selector = $inline ? '#' . $tag->name . '_timepicker' : $tag->name;

        $dp = new CF7_DateTimePicker('time', $dp_selector, $dpOptions);

        self::$inline_js[] = $dp->generate_code($inline);

        /* Print inline javascript */
        remove_action('wp_print_footer_scripts', array('ContactForm7Datepicker_Time', 'print_inline_js'), 99999);
        add_action('wp_print_footer_scripts', array(__CLASS__, 'print_inline_js_w_Time'), 99999);

        return $html;
    }

    public static function formtag_handler_DateTime($tag) {
        $tag = new WPCF7_FormTag($tag);

        if (empty($tag->name))
            return '';

        $validation_error = wpcf7_get_validation_error($tag->name);

        $class = wpcf7_form_controls_class($tag->type, 'wpcf7-date');

        if ($validation_error)
            $class .= ' wpcf7-not-valid';

        $atts = array();

        $atts['size'] = $tag->get_size_option('40');
        $atts['maxlength'] = $tag->get_maxlength_option();
        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_option('id', 'id', true);
        $atts['tabindex'] = $tag->get_option('tabindex', 'int', true);
        $atts['type'] = 'text';

        if ($tag->has_option('readonly'))
            $atts['readonly'] = 'readonly';

        if ($tag->is_required())
            $atts['aria-required'] = 'true';

        $value = (string)reset($tag->values);

        if ($tag->has_option('placeholder') || $tag->has_option('watermark')) {
            $atts['placeholder'] = $value;
            $value = '';
        }

        if (wpcf7_is_posted() && isset($_POST[$tag->name]))
            $value = stripslashes_deep($_POST[$tag->name]);

        $atts['value'] = $value;

        $dpOptions = array();
        $dpOptions['dateFormat'] = str_replace('_', ' ', $tag->get_option('date-format', '', true));
        $dpOptions['timeFormat'] = str_replace('_', ' ', $tag->get_option('time-format', '', true));
        $dpOptions['minDate'] = $tag->get_option('min-date', '', true);
        $dpOptions['maxDate'] = $tag->get_option('max-date', '', true);
        $dpOptions['firstDay'] = (int)$tag->get_option('first-day', 'int', true);
        $dpOptions['showAnim'] = $tag->get_option('animate', '', true);
        $dpOptions['yearRange'] = str_replace('-', ':', $tag->get_option('year-range', '', true));
        $dpOptions['numberOfMonths'] = $tag->get_option('months', 'int', true);
        $dpOptions['controlType'] = $tag->get_option('control-type', '', true);

        $dpOptions['showButtonPanel'] = $tag->has_option('buttons');
        $dpOptions['changeMonth'] = $tag->has_option('change-month');
        $dpOptions['changeYear'] = $tag->has_option('change-year');
        $dpOptions['noWeekends'] = $tag->has_option('no-weekends');

        foreach (array('minute', 'hour', 'second') as $s) {
            foreach (array('min', 'max') as $m) {
                $dpOptions[$s . ucfirst($m)] = (int)$tag->get_option("$m-$s", 'int', true);
            }

            $dpOptions['step' . ucfirst($s)] = (int)$tag->get_option("step-$s", 'int', true);
        }

        $inline = $tag->has_option('inline');

        if ($inline) {
            $dpOptions['altField'] = "#{$tag->name}_alt";
            $atts['id'] = "{$tag->name}_alt";
        }

        $atts['type'] = $inline ? 'hidden' : 'text';
        $atts['name'] = $tag->name;

        $atts = wpcf7_format_atts($atts);

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s %4$s</span>',
            $tag->name, $atts, $validation_error,
            $inline ? "<div id=\"{$tag->name}_datetimepicker\"></div>" : '');

        $html = apply_filters('cf7dp_datetime_input', $html);

        $dp_selector = $inline ? '#' . $tag->name . '_datetimepicker' : $tag->name;

        $dp = new CF7_DateTimePicker('datetime', $dp_selector, $dpOptions);

        self::$inline_js[] = $dp->generate_code($inline);

        /* Print inline javascript */
        remove_action('wp_print_footer_scripts', array('ContactForm7Datepicker_DateTime', 'print_inline_js'), 99999);
        add_action('wp_print_footer_scripts', array(__CLASS__, 'print_inline_js_w_Time'), 99999);

        return $html;
    }

    public static function print_inline_js_wo_Time() {
        if (! wp_script_is('jquery-ui-datepicker', 'done') || empty(self::$inline_js))
            return;

        $out = implode("\n\t", self::$inline_js);
        $out = "jQuery(function($){\n\t$out\n});";

        echo "\n<script type=\"text/javascript\">\n{$out}\n</script>\n";
    }

    public static function print_inline_js_w_Time() {
        if (! wp_script_is('jquery-ui-timepicker', 'done') || empty(self::$inline_js))
            return;

        $out = implode("\n\t", self::$inline_js);
        $out = "jQuery(function($){\n\t$out\n});";

        echo "\n<script type=\"text/javascript\">\n{$out}\n</script>\n";
    }
}
