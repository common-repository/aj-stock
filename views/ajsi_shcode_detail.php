<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function ajsi_shcode_detail($atts){
        return ajsi_stockinfo_detail();
}
function ajsi_stockinfo_detail() {

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
    //$url = str_replace("[CODE]", $ajsi_opt_stockcode, AJSI_PLUGIN_API_REALTIME_PRICE);
    $url = AJSI_PLUGIN_PATH."data/".$ajsi_opt_stockcode.".xml";
    $aris_detail_box = ajsi_getsetdata($url, $aris_detail_box, $ajsi_opt_skin);
    return $aris_detail_box;    //ajsi_getsetdata($url, $aris_detail_box, $ajsi_opt_skin);
}
?>
