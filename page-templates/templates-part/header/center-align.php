<?php
global $dsp_theme_options;
$class = ($dsp_theme_options['opt-sticky'] == 1) ? 'fixed-top' : '';
?>
<header class="blog-masthead center-logo-header small-sub-nav <?php echo $class; ?>">
    <div class="custom-container container">
        <nav class="" role="navigation">
            <div class="site-branding text-center"> 
                <!-- Logo section start -->
                <div class="header-logo">
                    <?php $logo = isset($dsp_theme_options['opt-logo-url']['url']) ? $dsp_theme_options['opt-logo-url']['url'] : ''; ?>
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
            <div class="main-navigation pt-4">
                <!-- Header Menu section start -->
                <?php if (has_nav_menu('main_menu')): ?>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'main_menu',
                        'menu_class' => 'nav navbar-nav navbar-left',
                        'depth' => 3,
                        'container' => false,
                        'walker' => new Walker_DSP_Submenu
                    ));
                    ?>
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
<?php
if ($dsp_theme_options['opt-sticky'] == 1):
   ?>
   <div class="padding-fixed-center"></div>
   <?php
endif;
?>