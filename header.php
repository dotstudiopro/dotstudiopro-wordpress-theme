<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
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
        <?php $class = ($dsp_theme_options['opt-sticky'] == 1) ? 'sticky' : ''; ?>
        <header class="blog-masthead left-logo-header <?php echo $class; ?>">
            <div class="custom-container container">
                <nav class="" role="navigation">
                    <div class="site-branding float-left">
                        <!-- Logo section start -->
                        <div class="header-logo">
                            <?php $logo = isset($dsp_theme_options['opt-logo-url']['url']) ? $dsp_theme_options['opt-logo-url']['url'] : ''; ?>
                            <?php if (!empty($logo)) { ?>
                                <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><h1 class="site-logo m-0"><img src="<?php echo $logo; ?>" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>" ></h1></a>
                            <?php } else { ?>
                                <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><h1 class="site-logo m-0"><?php bloginfo('name'); ?></h1></a>                            
                            <?php } ?>
                        </div>
                        <!-- Logo section end -->
                    </div>
                    <div class="main-navigation float-right">
                        <!-- Header Menu section start -->
                        <?php if (has_nav_menu('main_menu')): ?>
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
                            <?php
                        endif;
                        if ($dsp_theme_options['opt-search'] == true)
                            get_search_form();
                        ?>
                        <!-- Header Menu section end -->
                </nav>
                </nav>
            </div>
        </header>
