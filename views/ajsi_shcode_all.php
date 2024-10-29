<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function ajsi_shcode_all($atts){
    //global $post;
    //if (is_single()){
        return ajsi_stockinfo_all();
    //}
}
function ajsi_stockinfo_all() {

    $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
    $ajsi_opt_refresh = get_option('ajsi_opt_refresh', "10000");
    $ajsi_opt_skin = get_option('ajsi_opt_reviewskin', "basic_1");

    if(!$ajsi_opt_stockcode)
        return "NOT REGISTER CODE";

    //add_action('wp_ajax_nopriv_ajsi_get_xml_data', 'ajsi_get_xml_data');
    //add_action('wp_ajax_ajsi_get_xml_data', 'ajsi_get_xml_data' );
    wp_enqueue_script('ajsi_stockinfo_all_js', AJSI_S3_AJ_VIEW_JS, array('jquery'), '1.0', true );
	wp_localize_script('ajsi_stockinfo_all_js', 'getinfo', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajsi-ajax-nonce'),
        'refresh_interval' => $ajsi_opt_refresh
	));

    include(plugin_dir_path(__FILE__)."skins/".$ajsi_opt_skin."/main.php");
    //$url = str_replace("[CODE]", $ajsi_opt_stockcode, AJSI_PLUGIN_API_REALTIME_PRICE);
    $url = AJSI_PLUGIN_PATH."data/".$ajsi_opt_stockcode.".xml";
    //if(AJSI_DEBUG){
    //    $url = AJSI_PLUGIN_PATH.'test.data.xml';
    //}
    $aris_info_box = str_replace("[DungRak_UP]", plugins_url("skins/".$ajsi_opt_skin."/images/up.png", __FILE__), $aris_info_box);
    $aris_info_box = str_replace("[DungRak_UPUP]", plugins_url("skins/".$ajsi_opt_skin."/images/up.png", __FILE__), $aris_info_box);
    $aris_info_box = str_replace("[DungRak_DOWN]", plugins_url("skins/".$ajsi_opt_skin."/images/down.png", __FILE__), $aris_info_box);
    $aris_info_box = str_replace("[DungRak_DOWNDOWN]", plugins_url("skins/".$ajsi_opt_skin."/images/down.png", __FILE__), $aris_info_box);
    $aris_info_box = str_replace("[DungRak_SAME]", plugins_url("skins/".$ajsi_opt_skin."/images/bohap.png", __FILE__), $aris_info_box);
    $aris_info_box = ajsi_getsetdata($url, $aris_info_box, $ajsi_opt_skin);
//echo $aris_info_box;
    return $aris_info_box;  //ajsi_getsetdata($url, $aris_info_box, $ajsi_opt_skin);
}
?>
