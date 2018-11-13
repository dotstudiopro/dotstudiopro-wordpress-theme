<footer class="blog-footer pt-5 pm-5">
    <div class="custom-container container">
        <div class="row no-gutters">
            <?php
            global $dsp_theme_options;
            $social_icons = [];
            $social_icons['facebook'] = ($dsp_theme_options['facebook-link']) ? $dsp_theme_options['facebook-link'] : '';
            $social_icons['twitter'] = ($dsp_theme_options['twitter-link']) ? $dsp_theme_options['twitter-link'] : '';
            $social_icons['dribbble'] = ($dsp_theme_options['dribbble-link']) ? $dsp_theme_options['dribbble-link'] : '';
            $social_icons['flickr'] = ($dsp_theme_options['flickr-link']) ? $dsp_theme_options['flickr-link'] : '';
            $social_icons['github'] = ($dsp_theme_options['github-link']) ? $dsp_theme_options['github-link'] : '';
            $social_icons['pinterest'] = ($dsp_theme_options['pinterest-link']) ? $dsp_theme_options['pinterest-link'] : '';
            $social_icons['youtube'] = ($dsp_theme_options['youtube-link']) ? $dsp_theme_options['youtube-link'] : '';
            $social_icons['google-plus'] = ($dsp_theme_options['google-plus-link']) ? $dsp_theme_options['google-plus-link'] : '';
            $social_icons['linkedin'] = ($dsp_theme_options['linkedin-link']) ? $dsp_theme_options['linkedin-link'] : '';
            $social_icons['instagram'] = ($dsp_theme_options['instagram-link']) ? $dsp_theme_options['instagram-link'] : '';
            $social_icons['vimeo'] = ($dsp_theme_options['vimeo-link']) ? $dsp_theme_options['vimeo-link'] : '';
            ?>
            <div class="col-md-12">
						<h3 class="post-title pb-1">FOLLOW US</h3>
            </div>
            <div class="col-md-7">
                <!-- social icon section start -->
                <?php if ($dsp_theme_options['opt-social-icons'] == 1): ?>
                    <div class="social-icons">
                        <div class="social-links-menu pl-3">
                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                            <?php
                            foreach ($social_icons as $key => $value) {
                                if ($value) {
                                    echo '<a href="' . $value . '" class="fa fa-' . $key . '"></a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- social icon section end -->

                <!-- copyright text section start -->
                <div class="copyright">
                    <?php $copyright = ($dsp_theme_options['opt-copyright-text']) ? $dsp_theme_options['opt-copyright-text'] : ''; ?>
                    <p><?php echo $copyright; ?></p>
                </div>
                <!-- copyright text section end -->
            </div>
            <div class="col-md-5">
                <!-- footer menu section start -->
                <?php if (!empty($dsp_theme_options['opt-select-menus'])): ?>
                    <div class="footer-nav">
                        <?php
                        wp_nav_menu(array(
                            'menu' => $dsp_theme_options['opt-select-menus'],
                        ));
                        ?>
                    </div>
                <?php endif; ?>
                <!-- footer menu section end -->
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>