<?php
/**
 * Template for displaying search forms 
 *
 * @package WordPress
 * @since 1.0
 */
?>

<?php $unique_id = esc_attr(uniqid('search-form-')); ?>
<form id="search-form" role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <input type="search" id="<?php echo $unique_id; ?>" class="search-field" placeholder="<?php echo esc_attr_x('Search &hellip;', 'placeholder', 'twentyseventeen'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
</form>