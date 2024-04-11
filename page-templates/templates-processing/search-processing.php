<?php
global $dsp_theme_options, $client_token;

// Code to check user is subscribed or not
$is_user_subscribed = false;
if (class_exists('Dotstudiopro_Subscription') && $client_token) {
    $dsp_subscription_object = new Dotstudiopro_Subscription_Request();
    $user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
    if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['products']['svod'][0]['product']['id'])) {
        $is_user_subscribed = true;
    }
}

$q = get_query_var('s');
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
    'options' => array(
        'default' => 1,
        'min_range' => 1,
    ),
        ));
$search_obj = new Dsp_External_Api_Request();
$type = $dsp_theme_options['opt-search-option'];
if ($type == 'channel')
    $form = ($page - 1) * $dsp_theme_options['opt-search-page-size'];
else
    $form = $page;
// Api call to get search data
$result = $search_obj->search($type, $dsp_theme_options['opt-search-page-size'], $form, $q);

$final_channel_data = array();
// var used to display the number of rows for the search result
$final_channel_data['number_of_row'] = $dsp_theme_options['opt-search-columns-row'];

if (!is_wp_error($result)){

    // get width, height and ration based on the option selectes on theme
    if( $dsp_theme_options['opt-search-image-size'] == '0' ) {
        $width = filter_var($dsp_theme_options['opt-search-image-dimensions']['width'], FILTER_SANITIZE_NUMBER_INT);
        $height = filter_var($dsp_theme_options['opt-search-image-dimensions']['height'], FILTER_SANITIZE_NUMBER_INT);
    } else {
        $width = filter_var($dsp_theme_options['opt-search-image-width']['width'], FILTER_SANITIZE_NUMBER_INT);
        $ratio_width = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['width'], FILTER_SANITIZE_NUMBER_INT);
        $ratio_height = filter_var($dsp_theme_options['opt-search-image-aspect-ratio']['height'], FILTER_SANITIZE_NUMBER_INT);
        $ratio = $ratio_height / $ratio_width;
    }

    // if we select search type as video in theme option then loop through search videos and add the required values into an array which we need to display on the page like title, link, banner, etc. 
    if($type == 'video'){
        $search_data = array();
        if(isset($result['videos']) && !empty($result['videos'])){
            foreach ($result['videos'] as $key => $data){
                $search_data[$key]['title'] = ($dsp_theme_options['opt-search-title-trim-word'] != 0) ? wp_trim_words($data['title'], $dsp_theme_options['opt-search-title-trim-word'], '...') : $data['title'];
                $search_data[$key]['slug'] = '/video/'.$data['_id'];
                $banner = (isset($data['thumb'])) ? $data['thumb'] : 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1';
                if($dsp_theme_options['opt-search-image-size'] == '1'){
                    $image_attributes = dsp_build_responsive_images( $banner, $width, $ratio );
                    $search_data[$key]['image_attributes_srcset'] = $image_attributes['srcset'];
                    $search_data[$key]['image_attributes_sizes'] = $image_attributes['sizes'];
                }
                $search_data[$key]['banner'] = $banner.'/'.$width;
                if(isset($height))
                    $search_data[$key]['banner'] = $search_data[$key]['banner'].'/'.$height;
                if($dsp_theme_options['opt-display-webp-image'] == 1)
                    $search_data[$key]['banner'] = $search_data[$key]['banner'].'?webp=1';
            }    
        }
    }

    // if we select search type as channels in theme option then loop through search channels and add the required values into an array which we need to display on the page like title, link, banner, etc. 
    if($type == 'channel'){
        $search_data = array();
        if(isset($result['channels']) && !empty($result['channels'])){
            foreach ($result['channels'] as $key => $data){
                $search_data[$key]['title'] = ($dsp_theme_options['opt-search-title-trim-word'] != 0) ? wp_trim_words($data['title'], $dsp_theme_options['opt-search-title-trim-word'], '...') : $data['title'];
                $search_data[$key]['slug'] = '/channel/'.$data['slug'];
                if($dsp_theme_options['opt-search-channel-poster-type'] == 'poster'){
                   $image_type = $data['poster'];
                }
                elseif($dsp_theme_options['opt-search-channel-poster-type'] == 'spotlight_poster'){
                    $image_type = $data['spotlight_poster'];
                }
                else{
                    $image_type = $data['wallpaper'];
                }
                $banner = (!empty($image_type)) ? $image_type : 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1';
                if($dsp_theme_options['opt-search-image-size'] == '1'){
                    $image_attributes = dsp_build_responsive_images( $banner, $width, $ratio );
                    $search_data[$key]['image_attributes_srcset'] = $image_attributes['srcset'];
                    $search_data[$key]['image_attributes_sizes'] = $image_attributes['sizes'];
                }
                $search_data[$key]['banner'] = $banner.'/'.$width;
                if(isset($height))
                    $search_data[$key]['banner'] = $search_data[$key]['banner'].'/'.$height;
                if($dsp_theme_options['opt-display-webp-image'] == 1)
                    $search_data[$key]['banner'] = $search_data[$key]['banner'].'?webp=1';
                if (isset($data['is_product']) && $data['is_product'] == 1 && $is_user_subscribed == false)
                    $search_data[$key]['is_product'] = true;
            }
        }
    }
    
    // assign all the data into a final array with the default image
    $final_channel_data['search_result'] = $search_data;
    $final_channel_data['default_image'] = 'https://defaultdspmedia.cachefly.net/images/5bd9ea4cd57fdf6513eb27f1/'.$width;
    if(isset($height))
        $final_channel_data['default_image'] = $final_channel_data['default_image'].'/'.$height;

}

?>