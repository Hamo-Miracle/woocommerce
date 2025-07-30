<?php
/**
 * The template for displaying search results pages.
 *
 * @package Blogus
 */

get_header(); ?>
<!--==================== main content section ====================-->
<div id="content" class="search-class">
    <!--container-->
    <div class="container">
        <!--==================== Breadcrumb section ====================-->
       <?php do_action('blogus_action_archive_page_title'); ?>
        <!--row-->
        <div class="row">
            <?php do_action('blogus_search_main_content'); ?>
        </div>
        <!--/row-->
    </div>
    <!--/container-->
</div>
<?php get_footer(); ?>