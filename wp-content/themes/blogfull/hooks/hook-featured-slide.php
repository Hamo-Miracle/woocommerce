<?php
if (!function_exists('blogfull_featured_section')) :
    /**
     *
     * @since Blogfull
     *
     */
    function blogfull_featured_section(){ 
        if (is_front_page() || is_home()) {
            $blogus_enable_main_slider = get_theme_mod('show_main_news_section',false);
            $select_vertical_slider_news_category = blogus_get_option('select_vertical_slider_news_category');
            $all_posts_vertical = blogus_get_posts($select_vertical_slider_news_category);
            if ($blogus_enable_main_slider): ?>
                <div  class="col-12 cc">
                    <div class="homemain-two bs swiper-container">
                        <div class="swiper-wrapper">
                            <?php blogus_get_block('list', 'banner'); ?>         
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
                <!--==/ Home Slider ==-->
            <?php endif; ?>
            <!-- end slider-section -->
        <?php }
    }
    endif;
            
add_action('blogfull_action_featured_section', 'blogfull_featured_section', 40);