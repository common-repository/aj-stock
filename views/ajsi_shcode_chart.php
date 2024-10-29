<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function ajsi_shcode_chart($atts){
    //global $post;
    //$ajsi_opt_reviewskin = get_option('ajsi_opt_reviewskin', "basic_1");
    //if (is_single()){
        return ajsi_stockinfo_chart();
    //}
}
function ajsi_stockinfo_chart() {
    $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
    $ajsi_opt_refresh = get_option('ajsi_opt_refresh', "10000");
    $ajsi_opt_skin = get_option('ajsi_opt_reviewskin', "basic_1");
    $ajsi_opt_chartviewtype = get_option('ajsi_opt_chartviewtype', "1d");
    $ajsi_opt_chart_interval = get_option('ajsi_opt_chart_interval', "1m");
    $ajsi_opt_chartvolume = get_option('ajsi_opt_chartvolume', "Y");
    $ajsi_opt_showchartopt = get_option('ajsi_opt_showchartopt', "Y");
	$ajsi_opt_chart_main_height = get_option('ajsi_opt_chart_main_height', "240");
	$ajsi_opt_chart_vol_height = get_option('ajsi_opt_chart_vol_height', "100");

	$ajsi_opt_chart_down_color = get_option('ajsi_opt_chart_down_color', "#0f9d58");
	$ajsi_opt_chart_up_color = get_option('ajsi_opt_chart_up_color', "#a52714");
    
    if(!$ajsi_opt_stockcode)
        return "NOT REGISTER CODE";

    $rndKey = rand(1000, 9999);
    wp_enqueue_script('googlechart', esc_url_raw('https://www.gstatic.com/charts/loader.js'), array(), null );
    wp_enqueue_script('googlejsapi', esc_url_raw('https://www.google.com/jsapi'), array(), null );
    wp_enqueue_script('ajsi_stockinfo_chart_js', AJSI_S3_AJ_VIEW_CHART_JS, array('jquery'), '1.0', true );
	wp_localize_script('ajsi_stockinfo_chart_js', 'chartinfo', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajsi-ajax-nonce'),
        'code' => $ajsi_opt_stockcode,
        'rndkey' => $rndKey,
        'interval' => $ajsi_opt_chartviewtype,
        'refresh_interval' => $ajsi_opt_refresh,
        'iswidget' => "N",
        'usevolume' => $ajsi_opt_chartvolume,
        'showopt' => $ajsi_opt_showchartopt,
        'colorup' => $ajsi_opt_chart_up_color,
        'colordown' => $ajsi_opt_chart_down_color,
        'mh' => $ajsi_opt_chart_main_height,
        'vh' => $ajsi_opt_chart_vol_height,
    ));
    include(plugin_dir_path(__FILE__)."skins/".$ajsi_opt_skin."/main.php");
    $aris_chart_box = str_replace("[RND]", $rndKey, $aris_chart_box);
    $aris_chart_box = str_replace("[MHEIGHT]", $ajsi_opt_chart_main_height, $aris_chart_box);

    if($ajsi_opt_chartvolume == "Y"){
        $aris_chart_box_vol = str_replace("[RND]", $rndKey, $aris_chart_box_vol);
        $aris_chart_box_vol = str_replace("[VHEIGHT]", $ajsi_opt_chart_vol_height, $aris_chart_box_vol);
    }
    //date_default_timezone_set('Asia/Seoul');
    //$content = file_get_contents($url);
    //if ($content !== false) {
    //    $data = "";
    //    foreach(explode("\n", $content) as $line){
    //        if(trim($line)){
    //            $data .= $line."\n";
    //        }
    //    }
    //    $xml = new SimpleXMLElement($data);
    //    $daily = array();
    //    foreach($xml->TBL_DailyStock->DailyStock as $item){
    //        $vls = str_replace(",", "", $item["day_Low"]).", ".str_replace(",", "", $item["day_Start"]).", ";
    //        $vls .= str_replace(",", "", $item["day_EndPrice"]).", ".str_replace(",", "", $item["day_High"]);
    //        array_push($daily, "['".substr($item["day_Date"], 3, 5)."', ".$vls."]");
    //    }
    //    $chart_data = implode(",", $daily);
    //    $aris_chart_box = str_replace("[RND]", $rndKey, $aris_chart_box);
    //    $aris_chart_box = str_replace("[INTERVAL]", $ajsi_opt_chart_interval, $aris_chart_box);
    //    return str_replace("[CHART_DATA]", $chart_data, $aris_chart_box);
    //}
    if($ajsi_opt_chartvolume == "Y"){
        return $aris_chart_box.$aris_chart_box_vol;
    }else{
        return $aris_chart_box;
    }
}
function ajsi_get_chart_data(){
    global $wpdb, $table_prefix;
	$nonce = $_GET['nonce'];
	if(!wp_verify_nonce( $nonce, 'ajsi-ajax-nonce'))
		die ( 'Busted!');

    $code = sanitize_text_field($_GET["code"]);
    $rnd = sanitize_text_field($_GET["rnd"]);
    $interval = sanitize_text_field($_GET["interval"]);
    $usevolume = sanitize_text_field($_GET["usevolume"]);
    $iswidget = sanitize_text_field($_GET["iswidget"]);
    $ajsi_opt_chart_daycount = get_option('ajsi_opt_chart_daycount', "90");
    
    $arrList = array();
    $arrVolList = array();
    $arrTicks = array();
    $startval = 0;
    if($interval == "1m"){
        $limit = $iswidget == "Y" ? "150" : "300";
        $ret_db = $wpdb->get_results("select * from ".$table_prefix."ajsi_stock_1m where code = '". $code. "' order by registered desc limit ".$limit.";" );
        $tmp_arr = array_reverse($ret_db);
        $pre_vol = 0;
        $pre_date = "";
        foreach ($tmp_arr as $item){
            $date = date("Ymd", strtotime(substr($item->registered, 0, 16) . ":00"));
            $dt = date("H:i", strtotime(substr($item->registered, 0, 16) . ":00"));
            if($pre_vol == 0){
                $pre_date = $date;
                $pre_vol = (int)$item->volumn;
                $vol = $pre_vol;
                continue;
            }else{
                if($pre_date != $date){
                    $vol = (int)$item->volumn;
                    $pre_vol = (int)$item->volumn;
                    $pre_date = $date;
                }else{
                    $vol = (int)$item->volumn - $pre_vol;
                    $pre_vol = (int)$item->volumn;
                }
            }
            $arrList[] =  array($dt, (int)$item->curprice);
            if($usevolume == "Y")
                $arrVolList[] =  array($dt, (int)$vol);
            if($startval == 0)
                $startval = (int)$item->curprice;
        }
        //$arrVolList = array_reverse($arrVolList);
        //$arrList = array_reverse($arrList);
    }else if($interval == "1d"){
        $limit = $iswidget == "Y" ? "30" : $ajsi_opt_chart_daycount;
        $sql = "select * from ".$table_prefix."ajsi_stock_1d where code = '". $code."' ";
        $sql .= " and registered >= '". date("Y-m-d H:i:s", strtotime("-".$limit." day", time()))."' order by registered;";
        $ret_db = $wpdb->get_results($sql);
        $m = 0;
        foreach ($ret_db as $item){
            $dt = date("m.d", strtotime($item->registered));
            $arrList[] =  array($dt, (int)$item->low, (int)$item->open, (int)$item->close, (int)$item->high);
            if($usevolume == "Y"){
                $arrVolList[] =  array($dt, (int)$item->volumn);
                //$arrVolList[] =  array(date("Y-m-d", strtotime($item->registered)), (int)$item->volumn);
            }
            $m++;
            if($m % 10 == 0)
                $arrTicks[] = $dt;
        }
    }else{
        //$ret_db = $wpdb->get_results("select * from ".$table_prefix."ajsi_stock_5m where code = ". $code. " and registered > '". date("Y-m-d ") . "00:00:00';" );
        $sql = "select * from ".$table_prefix."ajsi_stock_5m where code = '". $code."' ";
        $sql .= " and registered > '". date("Y-m-d H:i:s", strtotime("-1 day", time())) . "00:00:00';";
        $ret_db = $wpdb->get_results($sql);
        foreach ($ret_db as $item){
            $dt = date("d/m/Y H:i:s", strtotime(substr($item->registered, 0, 16) . ":00"));
            $arrList[] =  array(date("m월 d일", $dt), (int)$item->open, (int)$item->high, (int)$item->low, (int)$item->close);
            if($usevolume == "Y")
                $arrVolList[] =  array(date("m월 d일", $dt), (int)$item->volumn);
        }
    }
    $ret = array("chartdata" => $arrList, "chartvoldata" => $arrVolList, "ticks" => $arrTicks, "rndkey" => $rnd, "interval" => $interval, "preval" => $startval );
    echo json_encode($ret);
	wp_die();
}
?>
