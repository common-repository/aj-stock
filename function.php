<?php
$ajsi_tz = get_option('timezone_string');
if($ajsi_tz){
	date_default_timezone_set($ajsi_tz);
}else{
	date_default_timezone_set('Asia/Seoul');
}
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$mysqli->query('set names utf8');
function ajsi_getDigit($v){
    return substr($v, 0, 1) == "0" ? substr($v, 1, 1) : $v;
}
function ajsi_getMinMax($code){
    global $table_prefix, $mysqli;
    $data = $mysqli->query("select * from ".$table_prefix."ajsi_stock_data where code = '".$code."' and datatype = 'minmax'");
    if(mysqli_num_rows($data)){
        $d = mysqli_fetch_assoc($data);
        return $d["datavalue"];
    }
    return "";
}
function ajsi_updateXMLData($v, $code){
    global $table_prefix, $mysqli;
    $xmldata = $mysqli->query("select * from ".$table_prefix."ajsi_stock_data where code = '".$code."' and datatype = 'xmldata'");
    if(mysqli_num_rows($xmldata)){
        $sql = "update ".$table_prefix."ajsi_stock_data set datavalue = '".$v."', updated = '".date("Y-m-d H:i:s")."' ";
        $sql .= " where code = '".$code."' and datatype = 'xmldata';";
    }else{
        $sql = "insert into ".$table_prefix."ajsi_stock_data(code, datatype, datavalue, updated) values(";
        $sql .= "'".$code."', ";
        $sql .= "'xmldata', ";
        $sql .= "'".$v."', ";
        $sql .= "'".date("Y-m-d H:i:s")."');";
    }
    $mysqli->query($sql);
}
function ajsi_setStockData() {
    global $table_prefix, $mysqli;
    $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
    $ajsi_opt_startchkeckhour = get_option('ajsi_opt_startchkeckhour', 9);
    if(!$ajsi_opt_stockcode)
        return;
    //return;

    $isCheck = false;
    $wkday = date('w');

    $cltime = $mysqli->query("select * from ".$table_prefix."ajsi_stock_data where code = '".$ajsi_opt_stockcode."' and datatype = 'closetime'");
    if(mysqli_num_rows($cltime)){
        $closetime_arr = mysqli_fetch_assoc($cltime);
        $closetime = $closetime_arr["datavalue"];
        if((ajsi_getDigit(date("H")) >= $ajsi_opt_startchkeckhour && (date("Ymd", $closetime) != date("Ymd"))) 
				&& ($wkday != 0  && $wkday != 6)){
            $isCheck = true;
        }else{
            $msg = ajsi_getDigit(date("H")) . " - " . date("Ymd", $closetime) . " - ". date("Ymd");
            return;
        }
    }
    $url = str_replace("[CODE]", $ajsi_opt_stockcode, AJSI_PLUGIN_API_REALTIME_PRICE);

	$content = file_get_contents($url);
    if ($content) {
        $data = "";
        foreach(explode("\n", $content) as $line){
            if(trim($line)){
                $data .= $line."\n";
            }
        }
        ajsi_updateXMLData($data, $ajsi_opt_stockcode);
        $xml = new SimpleXMLElement($data);

        if($isCheck){
            if($xml->stockInfo["myJangGubun"] != "장마감" ){
				ajsi_L("info", "START(" . $xml->stockInfo["myJangGubun"] . ")");
                //장이 이제 열렸다!!!
                $mysqli->query("delete from ".$table_prefix."ajsi_stock_1m where code = '".$ajsi_opt_stockcode."' and registered < '".date("Y-m-d", strtotime("-7 day", time())) ." 00:00:00';");
                $mysqli->query("delete from ".$table_prefix."ajsi_stock_data where code = '".$ajsi_opt_stockcode."' and datatype = 'minmax';");
                $mysqli->query("delete from ".$table_prefix."ajsi_stock_data where code = '".$ajsi_opt_stockcode."' and datatype = 'closetime';");
                $mysqli->query("delete from ".$table_prefix."ajsi_stock_log where updated < '".date("Y-m-d", strtotime("-7 day", time())) ."00:00:00';");
            }else{
                return;
            }
        }

        $volume = str_replace(",", "", $xml->TBL_StockInfo["Volume"]);
        $s_price = str_replace(",", "", $xml->TBL_StockInfo["StartJuka"]);
        $curprice = str_replace(",", "", $xml->TBL_StockInfo["CurJuka"]);
        $min = str_replace(",", "", $xml->TBL_StockInfo["LowJuka"]);
        $max = str_replace(",", "", $xml->TBL_StockInfo["HighJuka"]);
        $old_minmax = ajsi_getMinMax($ajsi_opt_stockcode);
        if($old_minmax){
            $ret_arr = explode("-", $old_minmax);
            $prev_min = $ret_arr[0];
            $prev_max = $ret_arr[1];
            if($prev_min > $min){
                $curprice = $min;
            }
            if($prev_max < $max){
                $curprice = $max;
            }
        }
        //min max update
        $mysqli->query("delete from ".$table_prefix."ajsi_stock_data where code = '".$ajsi_opt_stockcode."' and datatype = 'minmax'");
        $sql = "insert into ".$table_prefix."ajsi_stock_data(code, datatype, datavalue, updated) values(";
        $sql .= "'".$ajsi_opt_stockcode."', ";
        $sql .= "'minmax', ";
        $sql .= "'".$min . "-" . $max."', ";
        $sql .= "'".date("Y-m-d H:i:s")."');";
        $mysqli->query($sql);

        //1m data save
        $sql = "insert into ".$table_prefix."ajsi_stock_1m(code, curprice, volumn, registered) values(";
        $sql .= "'".$ajsi_opt_stockcode."', ";
        $sql .= $curprice.", ";   //open
        $sql .= $volume.", ";   //high
        $sql .= "'".str_replace("/", "-", $xml->stockInfo["myNowTime"])."');";   //volumn
        $res = $mysqli->query($sql);

        $now_datetime = date("Y-m-d H:i:s", strtotime(str_replace("/", "-", $xml->stockInfo["myNowTime"])));
        $now_date = date("Y-m-d", strtotime(str_replace("/", "-", $xml->stockInfo["myNowTime"])));

        //1d data save
        $l1d = $mysqli->query("select * from ".$table_prefix."ajsi_stock_1d where code = '".$ajsi_opt_stockcode."' and registered = '".$now_date." 00:00:00'");
        if(mysqli_num_rows($l1d)){
            $sql = "update ".$table_prefix."ajsi_stock_1d set high = ".$max.", low = ".$min.", close = ".$curprice.", volumn = ".$volume." ";
            $sql .= " where code = '".$ajsi_opt_stockcode."' and registered = '".$now_date." 00:00:00'";
        }else{
            $sql = "insert into ".$table_prefix."ajsi_stock_1d(code, open, high, low, close, volumn, registered) values(";
            $sql .= "'".$ajsi_opt_stockcode."', ";
            $sql .= $curprice.", ";
            $sql .= $max.", ";
            $sql .= $min.", ";
            $sql .= $curprice.", ";
            $sql .= $volume.", ";
            $sql .= "'".$now_date." 00:00:00');";
        }
        $mysqli->query($sql);

        if($xml->stockInfo["myJangGubun"] == "장마감"){
			ajsi_L("info", "장마감");
            //min max update
            $mysqli->query("delete from ".$table_prefix."ajsi_stock_data where code = '".$ajsi_opt_stockcode."' and datatype = 'minmax'");
            $sql = "insert into ".$table_prefix."ajsi_stock_data(code, datatype, datavalue, updated) values(";
            $sql .= "'".$ajsi_opt_stockcode."', ";
            $sql .= "'closetime', ";
            $sql .= "'".time()."', ";
            $sql .= "'".date("Y-m-d H:i:s")."');";
            $mysqli->query($sql);
        }
    }
}
function ajsi_L($level, $msg){
	global $table_prefix, $mysqli;
	$ajsi_opt_debug = get_option('ajsi_opt_debug', "N");
	if($ajsi_opt_debug == "Y"){
		$sql = "insert into ".$table_prefix . "ajsi_stock_log(loglevel, logdata, updated) values";
		$sql .= "('".$level."','".$msg."', '".date("Y-m-d H:i:s")."');";
		//file_put_contents(AJSI_PLUGIN_PATH."data/data.log", $sql."\n", FILE_APPEND | LOCK_EX);
		$mysqli->query($sql);
	}
}
function ajsi_get_xml_data(){
	$nonce = $_GET['nonce'];
	if(!wp_verify_nonce( $nonce, 'ajsi-ajax-nonce'))
		die ( 'Busted!');

    $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
    global $table_prefix, $mysqli;
    $arr = array();
    $xmldata = $mysqli->query("select datavalue from ".$table_prefix."ajsi_stock_data where code = '".$ajsi_opt_stockcode."' and datatype = 'xmldata'");
    if(mysqli_num_rows($xmldata)){
        $xdata = mysqli_fetch_assoc($xmldata);
        $xml = new SimpleXMLElement($xdata["datavalue"]);
        $arr = array();
        $arr = ajsi_xml2array($xml, $arr);
    }
    echo json_encode($arr);
	wp_die();
}
function ajsi_xml2array($xmlObject, $out=array ()) {
    foreach ((array)$xmlObject as $index=>$node )
        $out[$index] = (is_object($node)) ? ajsi_xml2array($node) : $node;
    return $out;
}
function ajsi_getsetdata($url, $html, $skin_name){
    global $table_prefix, $mysqli;
    $ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
    $xmldata = $mysqli->query("select datavalue from ".$table_prefix."ajsi_stock_data where code = '".$ajsi_opt_stockcode."' and datatype = 'xmldata'");
    $content = "";
    if(mysqli_num_rows($xmldata)){
        $xdata = mysqli_fetch_assoc($xmldata);
        $content = $xdata["datavalue"];
    }
    if ($content) {
        $data = "";
        foreach(explode("\n", $content) as $line){
            if(trim($line)){
                $data .= $line."\n";
            }
        }
        $xml = new SimpleXMLElement($data);
        $html= str_replace("[CurJuka]", $xml->TBL_StockInfo["CurJuka"], $html);
        $html= str_replace("[Debi]", $xml->TBL_StockInfo["Debi"], $html);
        $html= str_replace("[PrevJuka]", $xml->TBL_StockInfo["PrevJuka"], $html);
        $html= str_replace("[HighJuka]", $xml->TBL_StockInfo["HighJuka"], $html);
        $html= str_replace("[Volume]", $xml->TBL_StockInfo["Volume"], $html);
        $html= str_replace("[StartJuka]", $xml->TBL_StockInfo["StartJuka"], $html);
        $html= str_replace("[LowJuka]", $xml->TBL_StockInfo["LowJuka"], $html);
        $html= str_replace("[Money]", ajsi_getKorWon($xml->TBL_StockInfo["Money"]), $html);
        $html= str_replace("[UpJuka]", $xml->TBL_StockInfo["UpJuka"], $html);
        $html= str_replace("[DownJuka]", $xml->TBL_StockInfo["DownJuka"], $html);
        $html= str_replace("[High52]", $xml->TBL_StockInfo["High52"], $html);
        $html= str_replace("[Low52]", $xml->TBL_StockInfo["Low52"], $html);
        $html= str_replace("[Amount]", $xml->TBL_StockInfo["Amount"], $html);
        $html= str_replace("[Per]", $xml->TBL_StockInfo["Per"], $html);
        $html= str_replace("[FaceJuka]", $xml->TBL_StockInfo["FaceJuka"], $html);

        $cur = str_replace(",", "", $xml->TBL_StockInfo["CurJuka"]);
        $pre = str_replace(",", "", $xml->TBL_StockInfo["PrevJuka"]);
        $diffVal = round((($cur - $pre) * 100 / $pre), 2) . "%";
        $html= str_replace("[diffYesterday]", $diffVal, $html);
        if($cur > $pre){
            $html= str_replace("[UPDOWNCSS]", "up", $html);
            if($cur == $pre){
                $html= str_replace("[DungRak]", plugins_url("views/skins/".$skin_name."/images/up.png", __FILE__), $html);
            }else{
                $html= str_replace("[DungRak]", plugins_url("views/skins/".$skin_name."/images/up.png", __FILE__), $html);
            }
        }else if($cur < $pre){
            $html= str_replace("[UPDOWNCSS]", "down", $html);
            if($cur == $pre){
                $html= str_replace("[DungRak]", plugins_url("views/skins/".$skin_name."/images/down.png", __FILE__), $html);
            }else{
                $html= str_replace("[DungRak]", plugins_url("views/skins/".$skin_name."/images/down.png", __FILE__), $html);
            }
        }else{
            $html= str_replace("[UPDOWNCSS]", "same", $html);
            $html= str_replace("[DungRak]", plugins_url("views/skins/".$skin_name."/images/bohap.png", __FILE__), $html);
        }
        return $html;
    } else {
        return $html;
    }
}
function ajsi_generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function ajsi_getLatestVer(){
    //$ver = file_get_contents(AJSI_S3_VER_PATH);
    //return $ver;
}
function ajsi_getLicense(){
    $host = $_SERVER["HTTP_HOST"];
	$request = wp_remote_get(AJSI_S3_LIC_PATH . "?domain=" . $host);
	$data = wp_remote_retrieve_body($request);
    if($data){
        $pinfo = json_decode($data, true);
    }else{
        return array();
    }
    return $pinfo;
}
function ajsi_getCur($val, $n){
    if($row["useramount"] == $row["useramount"]){
        return number_format($row["useramount"], 0);
    }else{
        return number_format($row["useramount"], 2);
    }
}
function ajsi_getKorWon($num){
    $num = str_replace(",", "", $num);
    if(!ctype_digit($num))
        $num = (string)$num;

    $won = array('', '만', '억', '조', '경', '해');
    $rtn = '';
    $len = strlen($num);
    $mod = $len % 4;
    if($mod) {
        $mod = 4 - $mod;
        $num = str_pad($num, $len + $mod, '0', STR_PAD_LEFT);
    }
    $arr_r = array();
    $arr = str_split($num, 4);
    for($i=0,$cnt=count($arr);$i<$cnt;$i++) {
        if($tmp = (int)$arr[$i])
            array_push($arr_r, number_format($tmp).$won[$cnt - $i - 1]);
    }
    for($i = 0; $i < (count($arr_r) > 2 ? 2 : count($arr_r)); $i++){
        $rtn .= $arr_r[$i];
        if(strlen($arr_r[$i]) > 3)
            break;
    }
    return $rtn."원";
} 
