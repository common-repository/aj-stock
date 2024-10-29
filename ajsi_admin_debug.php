<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $table_prefix, $wpdb;
$ajsi_opt_debug = get_option('ajsi_opt_debug', "N");
$ret_db = $wpdb->get_results("select * from ".$table_prefix."ajsi_stock_log order by updated desc;" );
?>
    <h2 class="nav nav-tabs">
        <a href="?page=ajsi-plugin-setting&tab=basic" class="nav-tab ajsi_tab" style="color:#999999">기본 설정</a>
        <a href="?page=ajsi-plugin-setting&tab=getdata" class="nav-tab ajsi_tab" style="color:#999999">데이터 가져오기</a>
        <a href="?page=ajsi-plugin-setting&tab=chart" class="nav-tab nav-tab-active" style="color:#999999">차트 설정 <span class="badge badge-info">Pro</span></a>
        <a href="?page=ajsi-plugin-setting&tab=debug" class="nav-tab ajsi_tab_active nav-tab-active" style="background:white;color:black;border-bottom:0px">로그</a>
        <a href="?page=ajsi-plugin-setting&tab=lic" class="nav-tab ajsi_tab" style="color:#999999">라이센스 정보</a>
    </h2>
    <div style="vertical-align:middle;border:1px solid #999999;">
        <div style="background:white;vertical-align:middle;padding-left:10px;border-bottom:1px solid #999999">
            <h4 style="padding-top:10px;margin-top: 0px;">Debug Log</h4>
            <div style="padding-bottom:10px;">문제가 있는 경우, Debug Log를 확인할 필요가 있습니다.<br/>디버그 로그를 메일로 보내주시면 파악하여 해결을 도와드릴 수 있습니다.<br/>
            Debug Mode를 "사용"으로 적용한 이후부터 수집됩니다.
            </div>
        </div>
        <?php if($lic_match){?>
        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Level</th>
                    <th>Message</th>
                </tr>
            </thead>
            <?php if(count($ret_db)){?>
            <tbody>
                <?php foreach($ret_db as $item){?>
                <tr>
                    <td><?php echo $item->updated?></td>
                    <td><?php echo $item->loglevel?></td>
                    <td><?php echo $item->logdata?></td>
                </tr>
                <?php }?>
            </tbody>
            <?php }else{?>
            <tbody>
                <tr>
                    <td colspan="3" class="text-center">로그가 없습니다.</td>
                </tr>
            </tbody>
            <?php }?>
        </table>
        <?php }else{?>
        <div method="post" action="" class="ajrv_setting">
            <div class="alert alert-danger" style="margin-bottom:0px;">
                <h5 style="margin-bottom:0px;">
                    라이센스가 없습니다.<br/>
                    <a href="?page=ajsi-plugin-setting&tab=lic">라이센스 정보</a>에서 라이센스를 등록하면 정상적으로 모든 기능을 이용할 수 있습니다.
                </h5>
            </div>
        </div>
        <?php }?>
    </div>
