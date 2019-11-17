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
 * Plugin Name:       Blanked Template
 * Plugin URI:        https://github.com/sofyansitorus/Blanked
 * Description:       Add blank page template for all themes.
 * Version:           1.1.0
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

// Define constant for the template file name.
if ( ! defined( 'BLANKED_TEMPLATE_FILE' ) ) {
	define( 'BLANKED_TEMPLATE_FILE', 'blanked-template.php' );
}

// Load the setting page library.
require_once 'includes/class-wpyes.php';

/**
 * Get the full path of the template file location.
 *
 * @since 1.0.0
 * @return string
 */
function blanked_get_template_file() {
	return plugin_dir_path( __FILE__ ) . BLANKED_TEMPLATE_FILE;
}

/**
 * Get list of special pages conditional check
 *
 * @since 1.0.0
 * @return array
 */
function blanked_conditional_check_list() {
	return array(
		'is_home'    => __( 'Blog', 'blanked' ),
		'is_archive' => __( 'Archive', 'blanked' ),
		'is_search'  => __( 'Search', 'blanked' ),
	);
}

/**
 * Check is current page need to apply the blanked template.
 *
 * @since 1.0.0
 * @return bool
 */
function blanked_is_apply_template() {
	global $post;

	if ( ! file_exists( blanked_get_template_file() ) ) {
		return false;
	}

	if ( is_page() ) {
		return is_page_template( BLANKED_TEMPLATE_FILE );
	} elseif ( is_singular() ) {
		return get_option( 'blanked_enable_post_type__' . get_post_type( $post ) );
	} else {
		$blanked_is_apply = false;

		foreach ( array_keys( blanked_conditional_check_list() ) as $conditional ) {
			if ( function_exists( $conditional ) && call_user_func( $conditional ) && get_option( 'blanked_enable_special_page__' . $conditional ) ) {
				$blanked_is_apply = true;
				break;
			}
		}

		return $blanked_is_apply;
	}
}

/**
 * Filters list of page templates for a theme.
 *
 * @since 1.0.0
 * @param array $templates Array of page templates. Keys are filenames, values are translated names.
 * @return array
 */
function blanked_theme_page_templates( $templates ) {
	if ( ! is_array( $templates ) ) {
		$templates = array();
	}

	return array_merge(
		$templates,
		array(
			BLANKED_TEMPLATE_FILE => __( 'Blanked Page', 'blanked' ),
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
	if ( blanked_is_apply_template() ) {
		return blanked_get_template_file();
	}

	return $template;
}

/**
 * Print page title tag into head element.
 *
 * @since 1.0.0
 * @return void
 */
function blanked_render_page_title() {
	if ( get_theme_support( 'title-tag' ) ) {
		_wp_render_title_tag();
	} else {
		// Print the page title for backward compatibility.
		?>
		<title><?php wp_title(); ?></title>
		<?php
	}
}

/**
 * Show compatibility notice to admin.
 *
 * @return void
 */
function blanked_admin_notices() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Blanked Template plugin only works for WordPress version 4.7 or later.', 'blanked' ); ?></p>
	</div>
	<?php
}

/**
 * The admin settings callback.
 *
 * @return void
 */
function blanked_admin_setting() {
	$settings = new Wpyes(
		'blanked_setting',
		array(
			'menu_title' => __( 'Blanked Template', 'blanked' ),
			'page_title' => __( 'Blanked Template Settings', 'blanked' ),
			'method'     => 'add_options_page',
		),
		'blanked'
	);

	$settings->add_section(
		array(
			'id'    => 'disable_functions',
			'title' => __( 'Disable functions for any singular and special pages that applied to use blanked template', 'blanked' ),
		)
	);

	$settings->add_fields(
		array(
			array(
				'type'  => 'checkbox',
				'id'    => 'disable_wp_head',
				'label' => __( 'Disable wp_head()', 'blanked' ),
			),
			array(
				'type'  => 'checkbox',
				'id'    => 'disable_wp_body_open',
				'label' => __( 'Disable wp_body_open()', 'blanked' ),
			),
			array(
				'type'  => 'checkbox',
				'id'    => 'disable_wp_footer',
				'label' => __( 'Disable wp_footer()', 'blanked' ),
			),
		)
	);

	$settings->add_section(
		array(
			'id'    => 'remove_css_class',
			'title' => __( 'Remove CSS class for any singular and special pages that applied to use blanked template', 'blanked' ),
		)
	);

	$settings->add_fields(
		array(
			array(
				'type'        => 'text',
				'id'          => 'remove_body_class',
				'label'       => __( 'Remove Body CSS Class', 'blanked' ),
				'description' => __( 'Separate with space for multiple classes.', 'blanked' ),
			),
			array(
				'type'        => 'text',
				'id'          => 'remove_post_class',
				'label'       => __( 'Remove Post CSS Class', 'blanked' ),
				'description' => __( 'Separate with space for multiple classes.', 'blanked' ),
			),
		)
	);

	$settings->add_section(
		array(
			'id'    => 'add_css_class',
			'title' => __( 'Add CSS class for any singular and special pages that applied to use blanked template', 'blanked' ),
		)
	);

	$settings->add_fields(
		array(
			array(
				'type'        => 'text',
				'id'          => 'add_body_class',
				'label'       => __( 'Add Body CSS Class', 'blanked' ),
				'description' => __( 'Separate with space for multiple classes.', 'blanked' ),
			),
			array(
				'type'        => 'text',
				'id'          => 'add_post_class',
				'label'       => __( 'Add Post CSS Class', 'blanked' ),
				'description' => __( 'Separate with space for multiple classes.', 'blanked' ),
			),
		)
	);

	$settings->add_section(
		array(
			'id'    => 'post_types',
			'title' => __( 'Apply blanked template to post type singular pages', 'blanked' ),
		)
	);

	$post_types = get_post_types(
		array(
			'public' => true,
		),
		'objects'
	);

	foreach ( $post_types as $post_type_slug => $post_type_object ) {
		if ( 'page' === $post_type_slug ) {
			continue;
		}

		$settings->add_field(
			array(
				'type'  => 'checkbox',
				'id'    => 'enable_post_type__' . $post_type_slug,
				'label' => $post_type_object->label,
			)
		);
	}

	$settings->add_section(
		array(
			'id'    => 'special_pages',
			'title' => __( 'Apply blanked template to special pages', 'blanked' ),
		)
	);

	foreach ( blanked_conditional_check_list() as $key => $label ) {
		$settings->add_field(
			array(
				'type'  => 'checkbox',
				'id'    => 'enable_special_page__' . $key,
				'label' => $label,
			)
		);
	}

	$settings->init(); // Run the Wpyes class.
}

/**
 * Filters body tag element classes when page using the blanked template.
 *
 * @since 1.0.0
 * @param array $classes Raw body classes data passed to the filter.
 * @return array
 */
function blanked_filter_body_class( $classes ) {
	$blanked_is_apply_template = blanked_is_apply_template();

	if ( ! $blanked_is_apply_template ) {
		return $classes;
	}

	// Remove CSS class.
	$blanked_remove_body_class = get_option( 'blanked_remove_body_class', '' );

	if ( $blanked_remove_body_class && blanked_is_apply_template() ) {
		$classes = array_diff( $classes, explode( ' ', $blanked_remove_body_class ) );
	}

	// Add CSS class.
	$blanked_add_body_class = get_option( 'blanked_add_body_class', '' );

	if ( $blanked_add_body_class && blanked_is_apply_template() ) {
		$classes = array_unique( array_merge( $classes, explode( ' ', $blanked_add_body_class ) ) );
	}

	return $classes;
}

/**
 * Filters post content wrapper CSS classes when page using the blanked template.
 *
 * @since 1.0.0
 * @param array $classes Raw post classes data passed to the filter.
 * @return array
 */
function blanked_filter_post_class( $classes ) {
	$blanked_is_apply_template = blanked_is_apply_template();

	if ( ! $blanked_is_apply_template ) {
		return $classes;
	}

	// Remove CSS class.
	$blanked_remove_post_class = get_option( 'blanked_remove_post_class', '' );

	if ( $blanked_remove_post_class && blanked_is_apply_template() ) {
		$classes = array_diff( $classes, explode( ' ', $blanked_remove_post_class ) );
	}

	// Add CSS class.
	$blanked_add_post_class = get_option( 'blanked_add_post_class', '' );

	if ( $blanked_add_post_class && blanked_is_apply_template() ) {
		$classes = array_unique( array_merge( $classes, explode( ' ', $blanked_add_post_class ) ) );
	}

	return $classes;
}

/**
 * Plugin bootstrap function
 *
 * @since  1.0.0
 * @return void
 */
function blanked_bootstrap() {
	// Load plugin textdomain.
	load_plugin_textdomain( 'blanked', false, basename( plugin_dir_path( __FILE__ ) ) . '/languages' );

	// Check version compatibility. Bail early if minimum version requirements not met.
	if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
		add_action( 'admin_notices', 'blanked_admin_notices' );
		return;
	}

	// Initialize the admin settings page.
	add_action( 'init', 'blanked_admin_setting' );

	// Hooked into theme_page_templates to modify list of page templates for a theme.
	add_filter( 'theme_page_templates', 'blanked_theme_page_templates', 999 );

	// Hooked into template_include to modify the path of the current template before including it.
	add_filter( 'template_include', 'blanked_template_include', 999 );

	// Hooked into body_class to modify the CSS classes.
	add_filter( 'body_class', 'blanked_filter_body_class', 999 );

	// Hooked into post_class to modify the CSS classes.
	add_filter( 'post_class', 'blanked_filter_post_class', 999 );
}
add_action( 'plugins_loaded', 'blanked_bootstrap' );
