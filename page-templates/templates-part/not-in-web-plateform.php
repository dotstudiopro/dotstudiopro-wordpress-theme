<?php
/**
 * Template Name: test
 *
 * This template is used to display Home Page of the site.
 * @since 1.0.0
 */
global $dsp_theme_options, $client_token;
get_header();

$theme_function = new Theme_Functions();
$main_carousel = $theme_function->home_page_main_carousel();
?>
<div class="custom-container container pt-5 pb-5 center-page-content">
    <div class="row no-gutters other-categories">
    	<div class="col-md-12 text-center">
    	<i class="far fa-ban display-3 pb-3 text-danger"></i>
        <p>The channel <?php echo ($chnl_title)? ' "' . $chnl_title . '" ' : ' '; ?> is not available for website plateform</p>
        <div class="sb_wrapper">
         	<a href="/" class="btn btn-secondary btn-ds-secondary">Back to Home</a>
         </div>
      </div>
    </div>
</div>

<?php get_footer(); ?>