<?php
/**
 * The template for displaying the content.
 * @package Blogfull
 */
?>
<div id="blog-list" class="bs-content-list">
    <?php while(have_posts()){ the_post(); ?>        
        <div id="post-<?php the_ID(); ?>" <?php post_class('bs-blog-post list-blog'); ?>>
            <?php  
            $url = blogus_get_freatured_image_url($post->ID, 'blogus-medium');
                blogus_post_image_display_type($post); 
            ?>
            <article class="small text-xs<?php if(empty($url)){ echo' p-0';} ?>">
              <?php $blogus_global_category_enable = get_theme_mod('blogus_global_category_enable','true');
                if($blogus_global_category_enable == 'true') {
                    blogus_post_categories();
                } ?>
                <h4 class="title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
                <?php blogfull_blog_content();
                echo '<div class="small-bottom">';
                   blogus_post_meta(); ?>
                </div>
            </article>
          </div>
    <!-- // bs-posts-sec block_6 -->
    <?php } blogus_page_pagination(); ?>
</div>