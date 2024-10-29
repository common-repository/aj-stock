<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function ajsi_options_page_html(){
    if( isset( $_GET["tab"] ) ) {
        $active_tab = sanitize_text_field($_GET["tab"]);
    }else{
        $active_tab = "basic";
    }
    $pinfo = ajsi_getLicense();
    $lic_match = false;
    if($pinfo){
        $lic_match = true;
    }
    //$latestver = ajsi_getLatestVer();
?>
<div class="wrap aj_bs_iso">
    <h2 style="margin-bottom: 15px;">AJ 주식</h2>
<?php
    $newvermsg = "";
    //if(AJSI_PLUGIN_VER != trim($latestver)){
    //    require_once(AJSI_PLUGIN_PATH . "ajsi_admin_newver_msg.php");
    //    echo $newvermsg;
    //}
    if($active_tab == "basic"){
        require_once(AJSI_PLUGIN_PATH . "ajsi_admin_basic.php");
    }else if($active_tab == "chart"){
        require_once(AJSI_PLUGIN_PATH . "ajsi_admin_chart.php");
    }else if($active_tab == "debug"){
        require_once(AJSI_PLUGIN_PATH . "ajsi_admin_debug.php");
    }else if($active_tab == "lic"){
        require_once(AJSI_PLUGIN_PATH . "ajsi_admin_lic.php");
    }else{
        require_once(AJSI_PLUGIN_PATH . "ajsi_admin_data.php");
    }
?>
</div>
<?php
}
?>
