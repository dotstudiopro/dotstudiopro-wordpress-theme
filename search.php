<?php
/**
 * The template for displaying search results pages.
 *
 * @since 1.0.0
 */

include(locate_template('page-templates/templates-processing/search-processing.php'));
get_header();
?>
<div class="custom-container container mb-5 mt-5">
    <?php
    if (isset($final_channel_data['search_result']) && !empty($final_channel_data['search_result'])):
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
        <div class="row">
            <?php foreach ($final_channel_data['search_result'] as $data): ?>
                <div class="col-6 col-sm-3 col-md-<?php echo $final_channel_data['number_of_row']; ?> p-2  search-custom-width">
                    <a href="<?php echo $data['slug']?>" title="<?php echo $data['title']; ?>">
                        <div class="holder">
                            <?php if (isset($data['is_product']) && $data['is_product'] == 1): ?>
                                <div class="locked-channel"><i class="fa fa-lock"></i></div>
                            <?php endif; ?>

                            <?php if(isset($data['image_attributes_sizes']) && isset($data['image_attributes_srcset'])) :?>
                                <img src="<?php echo $final_channel_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $data['banner']; ?>" title="<?php echo $data['title']; ?>" alt="<?php echo $data['title']; ?>" srcset="<?php echo $data['image_attributes_srcset']; ?>" sizes="<?php echo $data['image_attributes_sizes']; ?>">
                            <?php else : ?>   
                                <img src="<?php echo $final_channel_data['default_image']; ?>" class="lazy w-100" data-src="<?php echo $data['banner']; ?>" title="<?php echo $data['title']; ?>" alt="<?php echo $data['title']; ?>">
                            <?php endif; ?>
                            <div class='title-holder'>
                                <h3><?php echo $data['title']; ?></h3>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination-links">
            <nav class="navigation pagination" role="navigation">
                <div class="nav-links">
                    <?php
                    if(isset($result['pages']['total']) && !empty($result['pages']['total'])){
                        $total_pages = ceil($result['pages']['total'] / $dsp_theme_options['opt-search-page-size']);
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
