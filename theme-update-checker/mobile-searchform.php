<?php
/**
 * Template for displaying search forms 
 *
 * @package WordPress
 * @since 1.0
 */
$unique_id = esc_attr(uniqid('search-form-'));
?>
<div class="w-25 float-right searchform">
    <div id="sb-search" class="sb-search">
        <form id="search-form" role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <input class="sb-search-input search-autocomplete" placeholder="<?php echo esc_attr_x('Search &hellip;', 'placeholder', 'twentyseventeen'); ?>" type="text" value="<?php echo get_search_query(); ?>" name="s" id="search" data-nonce="<?php echo wp_create_nonce('dsp_autocomplete_search'); ?>">
            <input class="sb-search-submit" type="submit" value="">
            <span class="sb-icon-search"></span>
        </form>
    </div>
</div>
