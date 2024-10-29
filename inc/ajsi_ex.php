<?php
function ajsi_get_ex_data(){
    global $table_prefix, $wpdb;
	if ( ! function_exists( 'file_get_html' ) ) {
		require_once(AJSI_PLUGIN_PATH."inc/simple_html_dom.php");
	}
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $type = sanitize_text_field($_GET["type"]);
    $code = sanitize_text_field($_GET["code"]);
    $page = sanitize_text_field($_GET["page"]);
    $max = sanitize_text_field($_GET["max"]);
    if($type == "clear"){
        $mysqli->query("truncate table ".$table_prefix."ajsi_stock_1d");
        echo json_encode(
            array(
                "type" => "clear",
                "error" => "",
                "data" => array("result" => "ok")
            )
        );
        wp_die();
    //}else if($type == "getdata"){
    //    $mysqli->query("delete from ".$table_prefix."ajsi_stock_data where code = '".$code."' and datatype = 'getdataall'");
    //    $sql = "insert into ".$table_prefix."ajsi_stock_data(code , datatype, datavalue, updated)  values";
    //    $sql .= "('".$code."', 'getdataall', '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."')";
    //    wp_die();
    }else if($type == "getdata"){
        if($page == "1"){
            $mysqli->query("truncate table ".$table_prefix."ajsi_stock_1d");
        }
		update_option('ajsi_opt_isgetdata', "Y");
        $url = str_replace("[CODE]", $code, AJSI_PLUGIN_API_DAILY_PRICE);
        $url = str_replace("[PAGE]", $page, $url);
        $html = file_get_html($url);
        //$html = str_get_html(file_get_contents(AJSI_PLUGIN_PATH."data/html.data"));
        $sql = "insert into ".$table_prefix."ajsi_stock_1d(code, open, high, low, close, volumn, registered) values";
        $sql_insert = array();
        foreach($html->find('tr') as $article) {
            $item = array();
            foreach($article->find('span') as $vl) {
                $item[] = trim($vl->plaintext);
            }
            if(count($item)){
                $articles[] = $item;
                $insert = "('".$code."', ";
                $insert .= str_replace(",", "", $item[3]).", ";
                $insert .= str_replace(",", "", $item[4]).", ";
                $insert .= str_replace(",", "", $item[5]).", ";
                $insert .= str_replace(",", "", $item[1]).", ";
                $insert .= str_replace(",", "", $item[6]).", '".str_replace(".", "-", $item[0])." 00:00:00')";
                $sql_insert[] = $insert;
            }
        }
        if(!$max){
            $isset = false;
            foreach($html->find('a') as $article) {
                if(strstr($article->plaintext, "맨뒤")){
                    if($article->href){
                        if(strstr($article->href, "page=")){
                            $tmp = explode("page=", $article->href);
                            $max = trim($tmp[1]);
                            $isset = true;
                        }
                    }
                }
            }
            if(!$isset)
                $max = "1";
        }
        if(count($sql_insert)){
            $sql .= implode(",", $sql_insert).";";

            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $mysqli->query($sql);

            echo json_encode(
                array(
                    "type" => "getdata",
                    "error" => "",
                    "data" => array("IsDone" => "N", "code" => $code, "page" => $page, "max" => $max)
                )
            );
        }else{
            echo json_encode(
                array(
                    "type" => "getdata",
                    "error" => "Y",
                    "data" => array("IsDone" => "N", "code" => $code, "page" => $page, "max" => $max)
                )
            );
        }
        wp_die();
    }
}
