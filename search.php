<?php
/**
 * The template for displaying search results pages.
 *
 * @since 1.0.0
 */
get_header();
global $dsp_theme_options, $is_user_subscribed;
$q = get_query_var('s');
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
    'options' => array(
        'default' => 1,
        'min_range' => 1,
    ),
        ));
$search_obj = new Dsp_External_Api_Request();
$form = ($page - 1) * $dsp_theme_options['opt-search-page-size'];
$type = $dsp_theme_options['opt-search-option'];
$result = $search_obj->search($type, $dsp_theme_options['opt-search-page-size'], $form, $q);
$no_of_row = $dsp_theme_options['opt-search-columns-row'];
?>
<div class="custom-container container mb-5 mt-5">
    <?php
    if (!is_wp_error($result)):
        ?>

        <div class="row no-gutters">
            <h3 class="page-title mb-3"><?php printf(__('Search Results for : %s', 'twentyfifteen'), get_search_query()); ?></h3>
        </div>
        <div class="row no-gutters pb-5 pt-3 d-xs-block d-md-none d-lg-none">
            <form role="search" method="get" id="searchform" class="w-100" action="<?php echo esc_url(home_url('/')); ?>">
                <div>
                    <input class="search-textbox" type="text" value="<?php echo get_search_query(); ?>" name="s" id="search" />
                    <button class="search-btn" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>
        </div>
        <?php if ($result['data']['hits'] && $type == 'video') : ?>
            <div class="row">
                <?php foreach ($result['data']['hits'] as $data): ?>
                    <div class="col-6 col-sm-3 col-md-<?php echo $no_of_row; ?> p-2 search-custom-width">
                        <a href="/video/<?php echo $data['_id']; ?>" title="<?php echo $data['_source']['title']; ?>">
                            <div class="holder">
                                <?php
                                $image = (isset($data['_source']['thumb'])) ? get_option('dsp_cdn_img_url_field') . '/' . $data['_source']['thumb'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                                if( $dsp_theme_options['opt-search-image-size'] == '0' ) {
                                    $width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                } else {
                                    $width = filter_var($dsp_theme_options['opt-search-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio_width = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $ratio_height = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio = $ratio_height / $ratio_width;
                                }
                                $title = ($dsp_theme_options['opt-search-title-trim-word'] != 0) ? wp_trim_words($data['_source']['title'], $dsp_theme_options['opt-search-title-trim-word'], '...') : $data['_source']['title'];
                                ?>
                                <?php if($dsp_theme_options['opt-search-image-size'] == '1' ) :
                                    $image_attributes = dsp_build_responsive_images( $image, $width, $ratio ); ?>

                                    <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width; ?>" class="lazy" data-src="<?php echo $image; ?>" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                                <?php else : ?>
                                    <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height; ?>" class="lazy" data-src="<?php echo $image . '/' . $width . '/' . $height; ?>">
                                <?php endif; ?>
                                <div class='title-holder'>
                                    <h3><?php echo $title; ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($result['data']['hits'] && $type == 'channel'): ?>
            <div class="row">
                <?php foreach ($result['data']['hits'] as $data): ?>
                    <div class="col-6 col-sm-3 col-md-<?php echo $no_of_row; ?> p-2  search-custom-width">
                        <a href="/channel/<?php echo $data['slug']; ?>" title="<?php echo $data['_source']['title']; ?>">
                            <div class="holder">
                                <?php
                                if($dsp_theme_options['opt-search-channel-poster-type'] == 'poster'){
                                   $image_type = $data['poster'];
                                }
                                elseif($dsp_theme_options['opt-search-channel-poster-type'] == 'spotlight_poster'){
                                    $image_type = $data['spotlight_poster'];
                                }
                                else{
                                    $image_type = $data['wallpaper'];
                                }
                                // $image_type = ($dsp_theme_options['opt-search-channel-poster-type'] == 'poster') ? $data['poster'] : $data['spotlight_poster'];
                                $image = (!empty($image_type)) ? $image_type : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                                if( $dsp_theme_options['opt-search-image-size'] == '0' ) {
                                    $width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                } else {
                                    $width = filter_var($dsp_theme_options['opt-search-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio_width = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
                                    $ratio_height = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);

                                    $ratio = $ratio_height / $ratio_width;
                                }
                                $title = ($dsp_theme_options['opt-search-title-trim-word'] != 0) ? wp_trim_words($data['_source']['title'], $dsp_theme_options['opt-search-title-trim-word'], '...') : $data['_source']['title'];
                                ?>
                                <?php if (isset($data['_source']['is_product']) && $data['_source']['is_product'] == 1 && $is_user_subscribed == false): ?>
                                    <div class="locked-channel"><i class="fa fa-lock"></i></div>
                                <?php endif; ?>

                                <?php if($dsp_theme_options['opt-search-image-size'] == '1' ) :
                                    $image_attributes = dsp_build_responsive_images( $image, $width, $ratio ); ?>

                                    <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width; ?>" class="lazy" data-src="<?php echo $image; ?>" srcset="<?php echo $image_attributes['srcset']; ?>" sizes="<?php echo $image_attributes['sizes']; ?>">
                                <?php else : ?>    
                                    <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height; ?>" class="lazy" data-src="<?php echo $image . '/' . $width . '/' . $height; ?>">
                                <?php endif; ?>
                                <div class='title-holder'>
                                    <h3><?php echo $title; ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <h4><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentyseventeen'); ?></h4>
        <?php endif; ?>

        <div class="pagination-links">
            <nav class="navigation pagination" role="navigation">
                <div class="nav-links">
                    <?php
                    $total_pages = ceil($result['data']['total'] / $dsp_theme_options['opt-search-page-size']);
                    if ($total_pages) {
                        $paginate_links = paginate_links(array(
                            'base' => @add_query_arg('page', '%#%'),
                            'format' => '?page=%#%',
                            'mid-size' => 1,
                            'current' => $page,
                            'total' => $total_pages,
                            'prev_next' => True,
                            'prev_text' => __('<< Previous'),
                            'next_text' => __('Next >>')
                        ));
                        echo $paginate_links;
                    }
                    ?>
                </div>
            </nav>
        </div>

    <?php else : ?>
        <h4><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentyseventeen'); ?></h4>
    <?php endif; ?>
</div>
<?php
get_footer();
?>
