<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @package Blogfull
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> >
<?php wp_body_open(); ?>
<div id="page" class="site">
<a class="skip-link screen-reader-text" href="#content">
<?php _e( 'Skip to content', 'blogfull' ); ?></a>
<?php $background_image = get_theme_support( 'custom-header', 'default-image' );
  if ( has_header_image() ) { $background_image = get_header_image(); } ?>
<div class="wrapper" id="custom-background-css">
<?php if ( has_header_image() ) { ?>
  <img src="<?php echo esc_url( $background_image ); ?>">
<?php } ?>
  <!--header-->
    <?php do_action('blogfull_action_header_section'); ?> 
  <!--header end-->
  <!-- Add News ticker -->
  <?php do_action('blogfull_action_front_page_ticker_section'); ?> 
  <!--mainfeatured start-->
<div class="mainfeatured">
  <!--container-->
  <div class="container">    
      <?php do_action('blogfull_action_featured_section'); ?> 
  </div><!--/container-->
</div>
<!--mainfeatured end-->
<?php
do_action('blogus_action_featured_ads_section');
?>   