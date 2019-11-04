<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/sofyansitorus
 * @since             1.0.0
 * @package           Blanked
 *
 * @wordpress-plugin
 * Plugin Name:       Blanked
 * Plugin URI:        https://github.com/sofyansitorus/Blanked
 * Description:       Add blank page template for all themes.
 * Version:           1.0.0
 * Author:            Sofyan Sitorus
 * Author URI:        https://github.com/sofyansitorus
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       blanked
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Filters list of page templates for a theme.
 *
 * @since 1.0.0
 * @param array $templates Array of page templates. Keys are filenames, values are translated names.
 * @return array
 */
function blanked_theme_page_templates( $templates ) {
	if ( is_array( $templates ) ) {
		$templates = array();
	}

	return array_merge( $templates, array(
			'blanked-template.php' => __( 'Blanked Page', 'blanked' ),
		)
	);
}

/**
 * Filters the path of the current template before including it.
 *
 * @since 1.0.0
 * @param string $template The path of the template to include.
 * @return string
 */
function blanked_template_include( $template ) {
	global $post;

	// Bail early if post object is empty or not in page.
	if ( ! $post || ! is_page() ) {
		return $template;
	}

	// Validate if current template file meta is match and file exists.
	if ( 'blanked-template.php' === get_post_meta( $post->ID, '_wp_page_template', true ) && file_exists( dirname( __FILE__ ) . '/blanked-template.php' ) ) {
		return dirname( __FILE__ ) . '/blanked-template.php';
	}

	return $template;
}

/**
 * Print page title tag into head element.
 *
 * @since 1.0.0
 * @return void
 */
function blanked_wp_head() {
	// Bail early if current theme support title-tag.
	if ( get_theme_support( 'title-tag' ) ) {
		return;
	}

	// Print the page title.
	?>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php
}

/**
 * Show compatibility notice to admin.
 *
 * @return void
 */
function blanked_admin_notices() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Blanked Plugin only works for WordPress version 4.7 or later.', 'blanked' ); ?></p>
	</div>
	<?php
}

/**
 * Plugin bootstrap function
 *
 * @since  1.0.0
 * @return void
 */
function blanked_bootstrap() {
	// Check version compatibility. Bail early if minimum version requirements not met.
	if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
		add_action( 'admin_notices', 'blanked_admin_notices' );
		return;
	}

	// Hooked into theme_page_templates to modify list of page templates for a theme.
	add_filter( 'theme_page_templates', 'blanked_theme_page_templates' );

	// Hooked into template_include to modify the path of the current template before including it.
	add_filter( 'template_include', 'blanked_template_include' );

	// Hooked into wp_head to print page title tag into head element.
	add_action( 'wp_head', 'blanked_wp_head' );
}
add_action( 'plugins_loaded', 'blanked_bootstrap' );
