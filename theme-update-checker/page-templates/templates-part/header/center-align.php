<?php
global $dsp_theme_options;
$class = ($dsp_theme_options['opt-sticky'] == 1) ? 'fixed-top' : '';
?>
<header class="blog-masthead center-logo-header small-sub-nav <?php echo $class; ?>">
    <div class="custom-container container">
        <nav class="navbar navbar-expand-lg pt-0">
            <div class="site-branding text-center mb-3"> 
                <!-- Logo section start -->
                <div class="header-logo">
                    <?php if(!empty($dsp_theme_options['opt-logo-text'])){
                        $logo = $dsp_theme_options['opt-logo-text'];
                    }
                    else{
                        $logo = isset($dsp_theme_options['opt-logo-url']['url']) ? $dsp_theme_options['opt-logo-url']['url'] : '';
                    } ?>
                    <?php if (!empty($logo)) { ?>
                        <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>">
                            <h1 class="site-logo"><img src="<?php echo $logo; ?>" title="<?php bloginfo('name'); ?>" alt="<?php bloginfo('name'); ?>" ></h1>
                        </a>
                    <?php } else { ?>
                        <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>">
                            <h1 class="site-logo">
                                <?php bloginfo('name'); ?>
                            </h1>
                        </a>
                    <?php } ?>
                </div>
                <!-- Logo section end --> 
            </div>
            <div class="main-navigation float-left">
                <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <div id="content-1" class="content">
                        <!-- Header Menu section start -->
                        <?php if (has_nav_menu('main_menu')): ?>
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'main_menu',
                                'menu_class' => 'navbar-nav mr-auto navbar-left',
                                'depth' => 3,
                                'container' => false,
                                'walker' => new Walker_DSP_Submenu
                            ));
                            ?>
                            <?php
                        endif;
                        ?>
                        <!-- Header Menu section end --> 
                    </div>
                </div>
            </div>

            <?php if (is_active_sidebar('dsp_web_login_area')) { ?>
                <div id="primary-login-area" class="primary-login-area widget-area" role="complementary">
                    <?php dynamic_sidebar('dsp_web_login_area'); ?>
                </div><!-- #primary-sidebar -->
            <?php } ?>
            <?php
            if ($dsp_theme_options['opt-search'] == true)
                get_search_form();
            ?>
        </nav>
    </div>
</header>
<?php
if ($dsp_theme_options['opt-sticky'] == 1):
    ?>
    <div class="padding-fixed-center"></div>
    <?php
endif;
?>
