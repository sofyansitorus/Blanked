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
		<?php
		if ( ! get_option( 'blanked_disable_wp_head' ) ) :
			wp_head();
		else :
			blanked_render_page_title();
		endif;
		?>
	</head>
	<body <?php body_class(); ?>>
		<?php
		if ( function_exists( 'wp_body_open' ) && ! get_option( 'blanked_disable_wp_body_open' ) ) :
			wp_body_open();
		endif;

		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'blanked' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			?>
			</article>
			<?php
		endwhile;

		if ( ! get_option( 'blanked_disable_wp_head' ) ) :
			wp_footer();
		endif;
		?>
	</body>
</html>
