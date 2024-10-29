<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function ajsi_shcode_seller($atts){
        return ajsi_stockinfo_seller();
}
function ajsi_stockinfo_seller() {

    $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
    $ajsi_opt_refresh = get_option('ajsi_opt_refresh', "10000");
    $ajsi_opt_skin = get_option('ajsi_opt_reviewskin', "basic_1");

    if(!$ajsi_opt_stockcode)
        return "NOT REGISTER CODE";

    include(plugin_dir_path(__FILE__)."skins/".$ajsi_opt_skin."/main.php");
    wp_enqueue_script('ajsi_stockinfo_all_js', AJSI_S3_AJ_VIEW_JS, array('jquery'), '1.0', true );
	wp_localize_script('ajsi_stockinfo_all_js', 'getinfo', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajsi-ajax-nonce'),
        'refresh_interval' => $ajsi_opt_refresh
	));
    return $aris_seller_box;
}
//function ajrv_content_filter($content) {
//    global $post;
//    wp_register_script('ajrvjs', AJRV_PLUGIN_JS, array('jquery'));
//    wp_enqueue_script('ajrvjs');
//
//    if (is_single()){
//        $ajrv_opt_showpos = get_option('ajrv_opt_showpos', "1");
//        $arjv_skin = ajrv_review_single($post->ID);
//
//        if($ajrv_opt_showpos == "1"){
//            $content .= $arjv_skin;
//        }else if($ajrv_opt_showpos == "2"){
//            $content = $arjv_skin . $content;
//        }else{
//            $content = $arjv_skin . $content . $arjv_skin;
//        }
//    }
//    return $content;
//}
?>
