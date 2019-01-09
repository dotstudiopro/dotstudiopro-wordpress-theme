<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <?php
        global $dsp_theme_options;
        if (isset($dsp_theme_options['opt-favicon-url']['url'])) {
            echo '<link rel="shortcut icon" href="' . $dsp_theme_options['opt-favicon-url']['url'] . '" />';
        }
        ?>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
                <script src="<?php echo esc_url(get_template_directory_uri()); ?>/bootstrap/js/html5.min.js"></script>
                <script src="<?php echo esc_url(get_template_directory_uri()); ?>/bootstrap/js/respond.min.js"></script>
                <![endif]-->
        <?php wp_head(); ?>
        <?php if ($dsp_theme_options['opt-color-selection-section'] == 1): ?>
            <style type="text/css">
                .slick-prev:before,
                .slick-next:before,
                .channel-banner:hover h4,
                .slide.active .slide_content h4,
                .video-content h4,
                .simple-navigation-item-content span,
                .simple-navigation-item-content i:before,
                .simple-navigation-item-content h5,
                .credit-block h4, .jconfirm.jconfirm-custom .jconfirm-box .jconfirm-title-c, .thankyou-page i
                {
                    color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                h2:before,
                h3:before {
                    border-left-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .video-next-prev,
                .sb-search.sb-search-open .sb-search-input {
                    border-bottom-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .slide.active {
                    -webkit-box-shadow: inset 0 0 10px #87b145;
                    box-shadow: inset 0 0 10px <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .custom-tooltip.tooltipster-sidetip .tooltipster-box,
                .center_title:before, .jconfirm.jconfirm-custom .jconfirm-box, .card .card-block {
                    border-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .custom-tooltip.tooltipster-sidetip.tooltipster-top .tooltipster-arrow-border {
                    border-top-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .custom-tooltip.tooltipster-sidetip.tooltipster-bottom .tooltipster-arrow-border {
                    border-bottom-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .custom-tooltip.tooltipster-sidetip.tooltipster-left .tooltipster-arrow-border {
                    border-left-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .custom-tooltip.tooltipster-sidetip.tooltipster-right .tooltipster-arrow-border {
                    border-right-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }
                .home-main-slider .slide_content .watch_now a:after,
                .watch_now a:after {
                    border-left-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }

                .mCS-minimal.mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar, .mCS-minimal.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar, .mCS-minimal.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar, .mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar, .mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar, .mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar{
                    background-color: <?php echo $dsp_theme_options['opt-main-theme-color'] . ' !important'; ?>;
                }

                .btn-ds-primary, .btn-ds-secondary,
                .card .card-header {
                    background-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                    border-color: <?php echo $dsp_theme_options['opt-main-theme-color']; ?>;
                }

                /* hover css */
                .simple-navigation-item-content:hover,
                .other-categories .slick-arrow.slick-prev:hover,
                .other-categories .slick-arrow.slick-next:hover {
                    background: <?php echo $dsp_theme_options['opt-main-theme-hover-color']; ?>;
                }
                .main-navigation .navbar-nav > li.current-menu-item a,
                .other-categories h3 a:hover,
                .main-navigation .navbar-nav > li a:hover,
                .other-categories .slick-slide:hover h4,
                .footer-nav ul li a:hover,
                .autocomplete-suggestions .information-top ul li a:hover,
                .autocomplete-suggestions .channl_information a:hover h5
                {
                    color: <?php echo $dsp_theme_options['opt-main-theme-hover-color']; ?>;
                }
                
            </style>
        <?php endif; ?>
    </head>

    <body <?php theme_body_class(); ?>>
        <?php
        $back = $dsp_theme_options['opt-back-to-top'];
        if ($back) {
            ?>
            <a href="javascript:" id="return-to-top"><i class="fa fa-arrow-up"></i></a>
            <?php
        }
        ?>
        <div class="fix-footer-bottom">
            <div class="content-area-custom">
                <?php
                $header_align = $dsp_theme_options['opt-logo-align'];
                get_template_part('page-templates/templates-part/header/' . $header_align . '-align');
                