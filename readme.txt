=== cbParallax ===
Contributors: flavor
Tags: parallax, background, fullscreen, scroll, custom background
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.0
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom background images with parallax effect for posts, pages and products.

== Description ==

cbParallax lets you add custom background images with a vertical or horizontal parallax effect to your WordPress site. You can set a single background for the entire site or configure individual backgrounds for each post, page, or product.

**Features:**

* Background image with smooth parallax scrolling effect
* One image for all pages or individual backgrounds per page/post
* Works vertically and horizontally
* Various overlay patterns to choose from
* Color overlay with opacity control
* Boxed layout option with customizable margins
* Mobile device detection with option to disable parallax on mobile
* Compatible with WooCommerce products

**Requirements:**

* Your theme must support the WordPress [Custom Backgrounds](https://developer.wordpress.org/themes/functionality/custom-backgrounds/) theme feature
* For parallax effect, images should be at least 1920px x 1200px
* PHP 8.0 or higher

== Installation ==

1. Upload the `cb-parallax` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > cbParallax to configure the plugin
4. Optionally, enable per-post backgrounds and edit individual posts to add custom backgrounds

== Frequently Asked Questions ==

= Where do I interact with this plugin? =

You can access the main settings under Settings > cbParallax in your WordPress admin. If you enable the "Individual Background Images" option, a meta box will appear on your post/page edit screens.

= Why doesn't it work with my theme? =

Most likely your theme doesn't support the WordPress `custom-background` theme feature. Check with your theme author to see if they can add support. The theme needs to call `add_theme_support('custom-background')`.

= My theme supports custom-background but it still doesn't work! =

Make sure no container element (like a full-width wrapper) is covering the body element where the background is displayed. You may need to add some CSS transparency to your content container.

= How do I add theme support? =

If you're a theme developer, add this to your theme's functions.php:
`add_theme_support( 'custom-background' );`

= What image dimensions work best? =

For the best parallax effect, use images at least 1920px wide and 1200px tall. This ensures smooth scrolling on fullHD displays with room for the parallax movement.

= Can I disable the parallax effect on mobile? =

Yes! There's an option in the settings to disable the parallax effect on mobile devices, which can help with performance.

== Screenshots ==

1. Multiple background views of a single post
2. Settings Page with all configuration options
3. Custom background meta box on the post edit screen

== Changelog ==

= 1.1.1 =
* Corrected the bundled WP Color Picker Alpha version to 3.0.4.
* Updated SweetAlert2 to 11.26.25, Smooth Scroll to 0.4.4, and jQuery UI to 1.14.2.
* Added third-party license and source documentation.
* Modernized and integrated the settings-page switches and alpha color pickers.
* Added accessible switch semantics and clearer keyboard focus states.
* Removed duplicate settings-page and meta-box hook registration.
* Fixed parallax redraws when iOS browser chrome changes the visual viewport.
* Improved output escaping, internationalization, and WordPress coding-standards compliance.

= 1.1.0 =
* Security: Removed global suppression of WordPress doing-it-wrong notices.
* Security: Rejects unauthorized AJAX requests before nonce and input processing.
* Fixed: Activation and deactivation now target the actual plugin entry file.
* Fixed: PHP 8.5 switch syntax deprecations and missing-option notices.
* Fixed: Consistent versioning across headers, runtime, settings, and assets.
* Improved: Existing settings are completed safely when new defaults are added.
* Updated: SweetAlert2 to 11.26.25.
* Updated: Tested with WordPress 7.1.

= 1.0.0 =
* Security: Added proper sanitization and escaping throughout the plugin
* Security: Added capability checks on AJAX handlers
* Security: Fixed nonce verification issues
* Fixed: Corrected form tag closure in settings page
* Fixed: Removed references to non-existent options in AJAX responses
* Improved: Mobile detection using WordPress wp_is_mobile() function
* Improved: Code formatting to WordPress coding standards
* Improved: Updated file headers and documentation
* Updated: Requires WordPress 6.6 or higher
* Updated: Requires PHP 8.0 or higher

= 0.9.2.4 =
* Previous stable release

== Upgrade Notice ==

= 1.1.1 =
Dependency, licensing, and WordPress Plugin Check maintenance update.

= 1.1.0 =
Compatibility and lifecycle fixes for current WordPress and PHP releases.

= 1.0.0 =
Major release with security improvements. All users are encouraged to update.

== Privacy Policy ==

This plugin does not collect, store, or transmit any personal data. All settings are stored locally in your WordPress database.
