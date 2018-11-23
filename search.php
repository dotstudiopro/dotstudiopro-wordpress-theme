<?php
/**
 * The template for displaying search results pages.
 *
 * @since 1.0.0
 */
get_header();
global $dsp_theme_options;
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
<div class="custom-container container mb-5 pt-5">
    <?php
    if (!is_wp_error($result)):
        ?>

        <div class="row no-gutters">
            <h3 class="page-title mb-5"><?php printf(__('Search Results for: %s', 'twentyfifteen'), get_search_query()); ?></h3>
        </div>
        <div class="row no-gutters mobile-display pb-5">
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
                    <div class="col-md-<?php echo $no_of_row; ?> p-2">
                        <a href="/video/<?php echo $data['_id']; ?>" title="<?php echo $data['_source']['title']; ?>">
                            <div class="holder">
                                <?php
                                $image = (isset($data['_source']['thumb'])) ? get_option('dsp_cdn_img_url_field') . '/' . $data['_source']['thumb'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                                $width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                $height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                $title = ($dsp_theme_options['opt-search-title-trim-word'] != 0) ? wp_trim_words($data['_source']['title'], $dsp_theme_options['opt-search-title-trim-word'], '...') : $data['_source']['title'];
                                ?>
                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height ?>" class="lazy" data-src="<?php echo $image . '/' . $width . '/' . $height; ?>"> 
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
                    <div class="col-md-<?php echo $no_of_row; ?> p-2">
                        <a href="/channel/<?php echo $data['slug']; ?>" title="<?php echo $data['_source']['title']; ?>">
                            <div class="holder">
                                <?php
                                $image = (isset($data['poster'])) ? $data['poster'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                                $width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                $height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                $title = ($dsp_theme_options['opt-search-title-trim-word'] != 0) ? wp_trim_words($data['_source']['title'], $dsp_theme_options['opt-search-title-trim-word'], '...') : $data['_source']['title'];
                                ?>
                                <img src="https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1/<?php echo $width . '/' . $height ?>" class="lazy" data-src="<?php echo $image . '/' . $width . '/' . $height; ?>"> 
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
