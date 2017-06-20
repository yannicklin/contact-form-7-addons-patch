=== Patch for 3rd party addons of Contact Form 7 ===
Contributors: yannicklin
Donate link: https://github.com/yannicklin/contact-form-7-addons-patch
Tags: contact form 7, patch
Requires at least: 4.0.0
Tested up to: 4.7
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt

This plugin is used as the packages of patches of common 3rd party addons for Contact Form 7.

== Description ==

After the update of Contact Form 7 v 4.6, there are many functions have been put into 'Depressed', such as 'WPCF7_ShortcodeManager' and 'WPCF7_ShortcodeManager->do_shortcode'.
Such messages of PHP notice/warming cause the log growed very rapid but hard to figure out the hints for function unworking.
To eliminiate the issues with minimum efforts, this plugin was created to put the patches together as a whole package.

The 3rd party addons this plugin can help to fix are as below.

* Contact Form 7 DatePicker (contact-form-7-datepicker) - v 2.6.0 : solve the depreciated functions since Contact Form v 4.6
* Contact Form 7 Controls (contact-form-7-extras) - v 0.3.5 : solve the depreciated functions since Contact Form v 4.6
* Contact Form 7 Image Captcha (cf7-image-captcha) - v 2.2.0 : solve the depreciated functions since Contact Form v 4.6
* Contact Form 7 Lead Info with Country (wpshore_cf7_lead_tracking) - v 1.4.0 : coorect the typo variable '$cf7isSecure'
<li>Contact Form 7 Textarea Wordcount (contact-form-7-textarea-wordcount) - v 1.1.1 : solve the depreciated functions since Contact Form v 4.6
<li>Contact Form 7 Submissions (contact-form-submissions) - v 1.5.5 : the careless of checking $_GET[] existence
<li>Multifile Upload Field for Contact Form 7 (multifile-upload-field-for-contact-form-7) - v 1.0.1 : solve the depreciated functions since Contact Form v 4.6
<li>Rich Text Editor Field for Contact Form 7 (rich-text-editor-field-for-contact-form-7) - v 1.1.0 : solve the depreciated functions since Contact Form v 4.6

== Installation ==

= Munual Upload =
1. Upload `contact-form-7-addons-patch.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

= via built-in Plugin menu =
1. Install and Activate


== Frequently Asked Questions ==

Any question you may commend on [GitHub](https://github.com/yannicklin/contact-form-7-addons-patch“Patch for 3rd party addons of Contact Form 7”) directly. Thanks

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
Initial release.
The current patches included are :
* Contact Form 7 DatePicker (v 2.6.0)
* Contact Form 7 Controls (v 0.3.5)
* Contact Form 7 Image Captcha (v 2.2.0)
* Contact Form 7 Lead Info with Country (v 1.4.0)
* Contact Form 7 Textarea Wordcount (v 1.1.1)
* Contact Form 7 Submissions (v 1.5.5)
* Multifile Upload Field for Contact Form 7 (v 1.0.1)
* Rich Text Editor Field for Contact Form 7 (v 1.1.0)

== Upgrade Notice ==

= 1.0 =
* Initial release.