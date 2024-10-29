<?php
/**
 * @package AJ Stock
 */

/*
Plugin Name: AJ-Stock
Description: 주가 정보를 보여주는 Plugin 입니다.
Version: 0.9.2
Author: AJ Bang
Author URI: https://2p1d.com
License: GPLv2 or later
*/
//if ( ! defined( 'WPINC' ) ) { die; }

$ajStockInfoVersion = "0.9.2";
$ajStockInfoDBVersion = "1.0";

define('AJSI_PLUGIN_VER', $ajStockInfoVersion);
define('AJSI_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AJSI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AJSI_PLUGIN_JS', plugins_url('ajsi.js', __FILE__));
define('AJSI_PLUGIN_CSS', plugins_url('ajsi-styles.css', __FILE__));

define('AJSI_S3_AJ_BS_ISO', plugins_url('css/aj_bs_iso.min.css', __FILE__));
//define('AJSI_S3_AJ_BS_ISO', "https://2p1d.s3.ap-northeast-2.amazonaws.com/aj-stock/src/v0.9.1/css/aj_bs_iso.min.css");
//define('AJSI_S3_AJ_STYLE', "https://2p1d.s3.ap-northeast-2.amazonaws.com/aj-stock/src/v0.9.1/css/ajsi-styles.css");
define('AJSI_S3_AJ_VIEW_JS', plugins_url('js/ajsi_view.js', __FILE__));
define('AJSI_S3_AJ_VIEW_CHART_JS', plugins_url('js/ajsi_view_chart.js', __FILE__));
define('AJSI_S3_LIC_PATH', "https://2p1d.com/aj-stock-lic.php");
//define('AJSI_S3_DOWNLOAD_PATH', "https://2p1d.s3.ap-northeast-2.amazonaws.com/aj-stock/download/");
//define('AJSI_S3_VER_PATH', "https://2p1d.s3.ap-northeast-2.amazonaws.com/aj-stock/lic/latest.ver");

define('AJSI_DEBUG', true);

define('AJSI_PLUGIN_API_DAILY_PRICE', "https://finance.naver.com/item/sise_day.nhn?code=[CODE]&page=[PAGE]");
define('AJSI_PLUGIN_API_REALTIME_PRICE', "http://asp1.krx.co.kr/servlet/krx.asp.XMLSise?code=[CODE]");
define('AJSI_PLUGIN_API_INFO', "http://asp1.krx.co.kr/servlet/krx.asp.DisList4MainServlet?code=[CODE]&gubun=K");
define('AJSI_PLUGIN_API_ACCINFO', "http://asp1.krx.co.kr/servlet/krx.asp.XMLJemu?code=[CODE]");
define('AJSI_PLUGIN_API_ACCINFO2', "http://asp1.krx.co.kr/servlet/krx.asp.XMLJemu2?code=[CODE]");
define('AJSI_PLUGIN_API_TEST', "http://asp1.krx.co.kr/servlet/krx.asp.XMLText?code=[CODE]");

require_once(AJSI_PLUGIN_PATH . "ajsi_options_page_html.php");
require_once(AJSI_PLUGIN_PATH . "views/ajsi_shcode_all.php");
require_once(AJSI_PLUGIN_PATH . "views/ajsi_shcode_detail.php");
require_once(AJSI_PLUGIN_PATH . "views/ajsi_shcode_seller.php");
require_once(AJSI_PLUGIN_PATH . "views/ajsi_shcode_chart.php");
//require_once(AJRV_PLUGIN_PATH . "views/ajsi_post_metabox_html.php");
//require_once(AJRV_PLUGIN_PATH . "views/ajsi_view_single_post_html.php");
//require_once(AJRV_PLUGIN_PATH . "views/ajsi_review_list_html.php");

require_once(AJSI_PLUGIN_PATH . "function.php");
require_once(AJSI_PLUGIN_PATH . "inc/ajsi.install.php");
require_once(AJSI_PLUGIN_PATH . "views/ajsi_widget_chart.php");
require_once(AJSI_PLUGIN_PATH . "views/ajsi_widget_price.php");
require_once(AJSI_PLUGIN_PATH . "inc/ajsi_ex.php");

add_filter('cron_schedules', 'ajsi_cron_intervals');
function ajsi_cron_intervals( $schedules ) {
	$schedules['1_min'] = array(
		'interval'	=> 60,
		'display'	=> 'Once Every 1 Minute'
	);
    return (array)$schedules; 
}

//Admin Pages
add_action('admin_menu', 'ajsi_admin_menu');
add_action('admin_init', 'ajsi_admin_init');
$icon_svg = 'data:image/png;base64,iVBORw0...';
function ajsi_admin_menu(){    
    //add_menu_page('AJSI', 'AJ 주식 설정', 'manage_options', 'ajsi-plugin-setting', 'ajsi_options_page_html', "dashicons-chart-area");
    add_menu_page('AJSI', 'AJ 주식', 'manage_options', 'ajsi-plugin-setting', 'ajsi_options_page_html', AJSI_PLUGIN_URL.'aj-stock-18-18-Icon.png');
    add_submenu_page('ajsi-plugin-setting', 'Settings', 'Settings', 'manage_options', 'ajsi-plugin-setting');
    //add_submenu_page('ajsi-plugin-setting', 'Pro', 'Pro', 'manage_options', 'ajrv-plugin-setting-pro', 'ajrv_options_page_pro_html');
}
function ajsi_admin_init(){
    add_action( 'delete_post', 'delete_review', 10 );
}

add_action('wp_ajax_nopriv_ajsi_get_xml_data', 'ajsi_get_xml_data');
add_action('wp_ajax_ajsi_get_xml_data', 'ajsi_get_xml_data' );
add_action('wp_ajax_nopriv_ajsi_get_chart_data', 'ajsi_get_chart_data');
add_action('wp_ajax_ajsi_get_chart_data', 'ajsi_get_chart_data' );
add_action('wp_ajax_ajsi_get_ex_data', 'ajsi_get_ex_data' );
//add_action('wp_ajax_nopriv_ajsi_get_ex_data', 'ajsi_get_ex_data');

//Write Post
//add_action('add_meta_boxes', 'ajsi_post_metabox_html');
//add_action('save_post', 'save_metabox', 10, 2 );
add_action('wp_ajax_nopriv_ajsi_get_xml_data', 'ajsi_get_xml_data');

add_shortcode('aj-stockinfo-all', 'ajsi_shcode_all');
add_shortcode('aj-stockinfo-detail', 'ajsi_shcode_detail');
add_shortcode('aj-stockinfo-seller', 'ajsi_shcode_seller');
//add_shortcode('aj-stockinfo-chart', 'ajrv_shcode_review_single');
//add_shortcode('aj-stockinfo-daylist', 'ajrv_shcode_review_single');

register_activation_hook( __FILE__, 'ajsi_create_plugin_database_table');
register_deactivation_hook( __FILE__, 'ajsi_deactivate' );

$p_info = ajsi_getLicense();
if(isset($p_info["expire"])){
	if($p_info["expire"] > date("Y-m-d H:i:s")){
		define('AJSI_ISPRO', true);
		add_shortcode('aj-stockinfo-chart', 'ajsi_shcode_chart');
	}else{
		define('AJSI_ISPRO', false);
	}
}else{
	define('AJSI_ISPRO', false);
}

//Widget
add_action( 'widgets_init', function(){
	register_widget('AJ_Stock_Price');
	if(AJSI_ISPRO){
		register_widget('AJ_Stock_Chart');
	}
});
function ajsi_load_plugin_js(){
    wp_register_script('ajsijs', plugins_url('ajsi.js', __FILE__), array('jquery'));
    wp_register_script('ajsimincolorjs', plugins_url('jquery.minicolors.min.js', __FILE__), array('jquery'));
    wp_register_script('ajsichartjs', plugins_url('js/Chart.min.js', __FILE__), array('jquery'));

    wp_enqueue_script('ajsijs');
    wp_enqueue_script('ajsimincolorjs');

    //wp_enqueue_style('ajsicss', AJSI_S3_AJ_STYLE);
    wp_enqueue_style('ajsimincolorcss', AJSI_S3_AJ_BS_ISO);
    wp_enqueue_style('bootstrapcss', plugins_url('jquery.minicolors.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'ajsi_load_plugin_js' );
add_action('admin_enqueue_scripts', 'ajsi_load_plugin_js' );
add_action('ajsi_event_getdata', 'ajsi_setStockData');
?>
