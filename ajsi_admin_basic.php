<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if($_POST){
	$r_nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce($r_nonce, 'ajsi_basic_set' ) ) die( 'Failed security check' );

    update_option('ajsi_opt_stockcode', sanitize_text_field($_POST['ajsi_opt_stockcode']));
    update_option('ajsi_opt_debug', sanitize_text_field($_POST['ajsi_opt_debug']));
    update_option('ajsi_opt_refresh', 10000);
    update_option('ajsi_opt_startchkeckhour', 9);
}
$ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
$ajsi_opt_refresh = get_option('ajsi_opt_refresh', "60000");
$ajsi_opt_startchkeckhour = get_option('ajsi_opt_startchkeckhour', "9");
$ajsi_opt_debug = get_option('ajsi_opt_debug', "N");
?>
    <h2 class="nav nav-tabs">
        <a href="?page=ajsi-plugin-setting&tab=basic" class="nav-tab nav-tab-active" style="background:white;color:black;border-bottom:0px">기본 설정</a>
        <a href="?page=ajsi-plugin-setting&tab=getdata" class="nav-tab" style="color:#999999">데이터 가져오기</a>
        <a href="?page=ajsi-plugin-setting&tab=chart" class="nav-tab nav-tab-active" style="color:#999999">차트 설정 <span class="badge badge-info">Pro</span></a>
        <?php if($ajsi_opt_debug == "Y"){?>
        <a href="?page=ajsi-plugin-setting&tab=debug" class="nav-tab ajsi_tab" style="color:#999999">로그</a>
        <?php }?>
        <a href="?page=ajsi-plugin-setting&tab=lic" class="nav-tab ajsi_tab" style="color:#999999">라이센스 정보</a>
    </h2>
    <div style="vertical-align:middle;border:1px solid #999999;">
        <div style="background:white;vertical-align:middle;padding-left:10px;border-bottom:1px solid #999999">
            <h4 style="padding-top:10px;margin-top: 0px;">기본 설정</h4>
            <div style="padding-bottom:10px;">
                주가 정보에 대한 다양한 정보를 Short Code와 Widget으로 제공합니다.<br/>
                사용하기 위해서는 주식 종목 코드를 등록해야합니다.
            </div>
        </div>
        <?php if($lic_match){?>
        <form method="post" action="" class="ajrv_setting" style="padding-left:10px;margin-bottom:10px;">
			<?php wp_nonce_field('ajsi_basic_set'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">종목 코드 번호</th>
                        <td>
                            <input type="text" name="ajsi_opt_stockcode" value="<?php echo $ajsi_opt_stockcode;?>">
                            <span></span>
                        </td>
                    </tr>
					<!--
                    <tr>
                        <th scope="row">개장 시각 </th>
                        <td>
                            <?php
                                echo "<select name='ajsi_opt_startchkeckhour'>";
                                for($i = 0; $i <= 23; $i++){
                                    $issel = $ajsi_opt_startchkeckhour == $i ? " selected " : "";
                                    echo "<option value=".$i." ".$issel.">".$i."시</option>";
                                }
                                echo "</select>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">업데이트 주기</th>
                        <td>
                            <select name="ajsi_opt_refresh">
                                <option value="60000" <?php if($ajsi_opt_refresh == "60000") { echo "selected"; }?>>1분</option> 
                                <option value="300000" <?php if($ajsi_opt_refresh == "300000") { echo "selected"; }?>>5분</option> 
                                <option value="600000" <?php if($ajsi_opt_refresh == "600000") { echo "selected"; }?>>10분</option> 
                                <option value="3600000" <?php if($ajsi_opt_refresh == "3600000") { echo "selected"; }?>>1시간</option> 
                                <option value="NO" <?php if($ajsi_opt_refresh == "NO") { echo "selected"; }?>>새로고침 안함</option> 
                            </select>
                        </td>
                    </tr>
					-->
                    <tr>
                        <th scope="row">디버그 모드</th>
                        <td>
                            <input type="radio" name="ajsi_opt_debug" <?php if($ajsi_opt_debug == "Y"){ echo "checked"; }?> value="Y">사용
                            <input type="radio" name="ajsi_opt_debug" <?php if($ajsi_opt_debug == "N"){ echo "checked"; }?> value="N">사용 안함
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" class="button button-primary" value="변경 사항 적용">
        </form>
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
