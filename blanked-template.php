<?php
/**
 * The template for displaying blanked page
 *
 * @package Blanked
 * @since 1.0.0
 * @version 1.0.0
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="profile" href="https://gmpg.org/xfn/11" />
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php
		if ( function_exists( 'wp_body_open') ):
			wp_body_open();
		endif;

		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;

		wp_footer();
		?>
	</body>
</html>
