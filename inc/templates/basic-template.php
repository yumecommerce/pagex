<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
	<?php do_action( 'pagex_head' ); ?>
	<?php do_action( 'pagex_head_styles' ); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action( 'pagex_before_page_layout' ); ?>
<div id="page">
	<?php do_action( 'pagex_before_header' ); ?>
    <div id="header" <?php pagex_header_class(); ?>><?php do_action( 'pagex_header_layout' ); ?></div>
	<?php do_action( 'pagex_before_main' ); ?>
    <div id="main" <?php pagex_template_class(); ?>>
		<?php
		do_action( 'pagex_before_post_content' );
		do_action( 'pagex_post_content' );
		do_action( 'pagex_after_post_content' );
		?>
    </div>
	<?php do_action( 'pagex_after_main' ); ?>
    <div id="footer" <?php pagex_footer_class(); ?>><?php do_action( 'pagex_footer_layout' ); ?></div>
	<?php do_action( 'pagex_after_footer' ); ?>
</div>
<?php do_action( 'pagex_after_page_layout' ); ?>
<?php wp_footer(); ?>
</body>
</html>