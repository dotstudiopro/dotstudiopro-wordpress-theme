<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
    </head>

    <body <?php theme_body_class(); ?>>
        <header class="blog-masthead">
            <div class="container">
                <nav class="navbar navbar-default" role="navigation">
                    <div class="col-md-4">
                        <div class="header-logo">
                            <?php $logo = isset($dsp_theme_options['opt-logo-url']['url']) ? $dsp_theme_options['opt-logo-url']['url'] : ''; ?>
                            <?php if (!empty($logo)) { ?>
                                <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><h1 class="site-logo"><img src="<?php echo $logo; ?>" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>" ></h1></a>
                            <?php } else { ?>
                                <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><h1 class="site-logo"><?php bloginfo('name'); ?></h1></a>                            
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="blog-nav">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'main_menu',
                                'menu_class' => 'nav navbar-nav navbar-left',
                                'depth' => 3,
                                'container' => false,
                                'walker' => new Walker_DSP_Submenu
                            ));
                            ?>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <div class="container">
            <div class="blog-header">
                <h1 class="blog-title">Dotstudio Pro</h1>
                <p class="lead blog-description">website for video streaming.</p>
            </div>
            <div class="row">