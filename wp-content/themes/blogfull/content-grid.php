<?php
/**
 * The template for displaying the content.
 * @package Blogfull
 */
$layout = esc_attr(get_theme_mod('blogus_content_layout','grid-right-sidebar')) == 'grid-fullwidth' ? 'col-lg-4 col-md-6': 'col-md-6';
?>

<div id="grid" class="row">
    <?php while(have_posts()){ the_post(); ?>
    <div class="<?php echo $layout?>">
        <div id="post-<?php the_ID(); ?>" <?php post_class('bs-blog-post grid-blog'); ?>> 
            <?php blogus_post_image_display_type($post); ?>
            <article class="small">
                <?php $blogus_global_category_enable = get_theme_mod('blogus_global_category_enable','true');
                if($blogus_global_category_enable == 'true') { blogus_post_categories(); } ?>
                <h4 class="title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                <?php blogus_posted_content();
                echo '<div class="small-bottom">';
                   blogus_post_meta(); ?>
                </div>                
            </article>
        </div> 
    </div> 
    <?php } ?>
    <div class="col-lg-12 text-center d-md-flex justify-content-center">
        <?php //Previous / next page navigation
                the_posts_pagination( array(
                'prev_text'          => '<i class="fa fa-angle-left"></i>',
                'next_text'          => '<i class="fa fa-angle-right"></i>',
                ) ); 
        ?>
    </div>
</div>