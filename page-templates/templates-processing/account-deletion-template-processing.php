<?php 
global $client_token;
$dotstudio_api = new Dsp_External_Api_Request();
$dsp_subscription_object = new Dotstudiopro_Subscription_Request();
if ($client_token) {
    // Condition to check account is already deleted to not
    $account_deleted = false;
    $get_account_deletion_date = $dotstudio_api->get_user_account_deletion_date($client_token);
    if(!is_wp_error($get_account_deletion_date) && isset($get_account_deletion_date['success']) && $get_account_deletion_date['success'] == 1){
        $date = date('M d, Y', $get_account_deletion_date['account_deletion_ts'] / 1000);
        $account_deleted = true;
    }
    // Condition to check user is subscribed or not
    /*$is_user_subscribed = false;
    $user_subscribe = $dsp_subscription_object->getUserProducts($client_token);
    if (!is_wp_error($user_subscribe) && $user_subscribe && !empty($user_subscribe['products']['svod'][0]['product']['id'])) {
        $is_user_subscribed = true;
    }*/
}
?>