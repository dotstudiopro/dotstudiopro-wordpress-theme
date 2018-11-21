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
if (!is_wp_error($result)):
    ?>

    <div class="custom-container container mb-5 pt-5">
        <div class="row no-gutters">
            <h3 class="page-title"><?php printf(__('Search Results for: %s', 'twentyfifteen'), get_search_query()); ?></h3>
        </div>
        <?php if ($result && $type == 'video') : ?>
            <div class="row">
                <?php foreach ($result['data']['hits'] as $data): ?>
                    <div class="col-md-4 p-2">
                        <a href="/video/<?php echo $data['_id']; ?>" title="<?php echo $data['_source']['title']; ?>">
                            <div class="holder">
                                <?php
                                $image = (isset($data['_source']['thumb'])) ? get_option('dsp_cdn_img_url_field') . '/' . $data['_source']['thumb'] : 'https://images.dotstudiopro.com/5bd9ea4cd57fdf6513eb27f1';
                                $width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
                                $height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
                                ?>
                                <img src="<?php echo $image . '/' . $width . '/' . $height; ?>" class="lazy">
                                <div class='title-holder'>
                                    <h3><?php echo $data['_source']['title']; ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination-links row">
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
        <?php else : ?>
            <h4><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentyseventeen'); ?></h4>
        <?php endif; ?>
    </div>
    </div>
<?php else : ?>
    <h4><?php _e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentyseventeen'); ?></h4>
<?php
endif;
get_footer();
?>
