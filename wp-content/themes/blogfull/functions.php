<?php
/**
 * Theme functions and definitions
 *
 * @package Blogfull
 */

if ( ! function_exists( 'blogfull_enqueue_styles' ) ) :
	/**
	 * @since 0.1
	 */
	function blogfull_enqueue_styles() {
		wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css');
		wp_enqueue_style( 'blogus-style-parent', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'blogfull-style', get_stylesheet_directory_uri() . '/style.css', array( 'blogus-style-parent' ), '1.0' );
		wp_dequeue_style( 'blogus-default',get_template_directory_uri() .'/css/colors/default.css');
		wp_enqueue_style( 'blogfull-default-css', get_stylesheet_directory_uri()."/css/colors/default.css" );
		wp_enqueue_style( 'blogfull-dark', get_stylesheet_directory_uri() . '/css/colors/dark.css');

		if(is_rtl()){
			wp_enqueue_style( 'blogus_style_rtl', trailingslashit( get_template_directory_uri() ) . 'style-rtl.css' );
	    }
		
	}

endif;
add_action( 'wp_enqueue_scripts', 'blogfull_enqueue_styles', 9999 );

if ( ! function_exists( 'blogfull_enqueue_scripts' ) ) :
	/**
	 * @since 0.1
	 */
	function blogfull_enqueue_scripts() {
	
		wp_enqueue_script( 'blogfull-ticker-js', get_stylesheet_directory_uri() .'/js/jquery.marquee.min.js', array('jquery'), '1.0', true); 
		wp_enqueue_script( 'blogfull-custom', get_stylesheet_directory_uri() .'/js/custom.js', array('jquery'), '1.0', true); 
	
	}

endif;
add_action( 'wp_enqueue_scripts', 'blogfull_enqueue_scripts', 99999 ); 


function blogfull_customizer_rid_values($wp_customize) {
}

add_action( 'customize_register', 'blogfull_customizer_rid_values', 1000 );

function blogfull_theme_setup() {


	$args = array(
		'default-color' => '#eee',
		'default-image' => get_stylesheet_directory_uri() . '/images/background-image.jpg',
		'default-repeat'         => 'no-repeat',
		'default-position-x'     => 'center',
		'default-position-y'     => 'center',
		'default-size'           => 'cover',
		'default-attachment'     => 'fixed',
		'wp-head-callback' => '_custom_background_cb', // Callback function for rendering the custom background CSS in the head
		'admin-head-callback' => '',  // Callback function for rendering the custom background CSS in the admin panel head
		'admin-preview-callback' => '' // Callback function for renderin
	);
	add_theme_support( 'custom-background', $args );

	//Load text domain for translation-ready
	load_theme_textdomain('blogfull', get_stylesheet_directory() . '/languages');

	require( get_stylesheet_directory() . '/hooks/hook-header-four.php' );
	require( get_stylesheet_directory() . '/hooks/hook-featured-slide.php' );
	require( get_stylesheet_directory() . '/hooks/hook-front-page-ticker-section.php' );
	require( get_stylesheet_directory() . '/frontpage-options.php' );
	require( get_stylesheet_directory() . '/font.php' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );

} 
add_action( 'after_setup_theme', 'blogfull_theme_setup' );


if (!function_exists('blogfull_get_block')) :
    /**
     *
     *
     * @since Blogfull 1.0.0
     *
     */
    function blogfull_get_block($block = 'grid', $section = 'post')
    {

        get_template_part('hooks/blocks/block-' . $section, $block);

    }
endif;


if ( ! function_exists( 'blogfull_blog_content' ) ) :
    function blogfull_blog_content() { 
      $blogus_blog_content  = get_theme_mod('blogus_blog_content','excerpt');

      	if ( 'excerpt' == $blogus_blog_content ){
		$blogus_excerpt = blogus_the_excerpt( absint( 15 ) );
			if ( !empty( $blogus_excerpt ) ) :                   
				echo wp_kses_post( wpautop( $blogus_excerpt ) );
			endif; 
		} else{ 
       		the_content( __('Read More','blogfull') );
        }
	}
endif;


function blogeir_bg_image_wrapper(){
	?>
	<script>
	</script>
	<?php
} 
add_action('wp_footer','blogeir_bg_image_wrapper');


function blogfull_customizer_styles() { ?>
	<style>
		body #accordion-section-blogus_pro_upsell h3.accordion-section-title {
			background-image: linear-gradient(-200deg, #f67b56 0%, #c19be9 100%) !important
		}
	</style>
	<?php
}
add_action('customize_controls_enqueue_scripts', 'blogfull_customizer_styles');

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function blogfull_widgets_init() {

	$blogus_footer_column_layout = esc_attr(get_theme_mod('blogus_footer_column_layout',3));
	
	$blogus_footer_column_layout = 12 / $blogus_footer_column_layout;
	
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Widget Area', 'blogfull' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="bs-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="bs-widget-title"><h2 class="title">',
		'after_title'   => '</h2></div>',
	) );


	
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'blogfull' ),
		'id'            => 'footer_widget_area',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="col-md-'.$blogus_footer_column_layout.' rotateInDownLeft animated bs-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="bs-widget-title"><h2 class="title">',
		'after_title'   => '</h2></div>',
	) );

}
add_action( 'widgets_init', 'blogfull_widgets_init' );