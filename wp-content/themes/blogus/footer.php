<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package Blogus
 */ ?>
<!--==================== Missed ====================-->
<div class="missed">
  <?php do_action('blogus_action_footer_missed_section'); ?>
</div> 
<!-- end missed -->
<!--==================== FOOTER AREA ====================-->
<?php $blogus_footer_bg = get_theme_mod('blogus_footer_widget_background');
  $blogus_footer_overlay_color = get_theme_mod('blogus_footer_overlay_color');
  $fBgColor = !empty($blogus_footer_overlay_color) ? " style='background-color:".esc_attr($blogus_footer_overlay_color)."'" : ""; ?>
    <footer class="footer<?php if($blogus_footer_bg != '') { ?> back-img" style="background-image:url('<?php echo esc_url($blogus_footer_bg);?>');" <?php } else { echo'"';} ?>>
      <div class="overlay"<?php echo $fBgColor;?>>
        <?php do_action('blogus_footer_widget_area_content'); ?>
        <?php do_action('blogus_footer_bottom_area_content'); ?>
        <div class="bs-footer-copyright">
          <?php do_action('blogus_footer_copyright_content'); ?>
        </div>
      </div>
      <!--/overlay-->
    </footer>
    <!--/footer-->
  </div>
  <!--/wrapper-->
  <!--Scroll To Top-->
    <?php blogus_scrolltoup(); ?>
  <!--/Scroll To Top-->
  <!-- Modal -->
    <?php do_action('blogus_search_model_content'); ?>                         
  <!-- /Modal -->
<?php wp_footer(); ?>
</body>
</html>