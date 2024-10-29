<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if($_POST){
	$r_nonce = $_REQUEST['_wpnonce'];
	if (!wp_verify_nonce($r_nonce, 'ajsi_chart_set' ) ) die( 'Failed security check' );

    update_option('ajsi_opt_chartviewtype', sanitize_text_field($_POST['ajsi_opt_chartviewtype']));
    update_option('ajsi_opt_chartvolume', sanitize_text_field($_POST['ajsi_opt_chartvolume']));
    update_option('ajsi_opt_chart_daycount', sanitize_text_field($_POST['ajsi_opt_chart_daycount']));
    update_option('ajsi_opt_chart_down_color', sanitize_text_field($_POST['ajsi_opt_chart_down_color']));
    update_option('ajsi_opt_chart_up_color', sanitize_text_field($_POST['ajsi_opt_chart_up_color']));
    update_option('ajsi_opt_chart_main_height', sanitize_text_field($_POST['ajsi_opt_chart_main_height']));
    update_option('ajsi_opt_chart_vol_height', sanitize_text_field($_POST['ajsi_opt_chart_vol_height']));
}
$ajsi_opt_chartviewtype = get_option('ajsi_opt_chartviewtype', "1d");
$ajsi_opt_chartvolume = get_option('ajsi_opt_chartvolume', "Y");
$ajsi_opt_chart_daycount = get_option('ajsi_opt_chart_daycount', "90");
$ajsi_opt_chart_main_height = get_option('ajsi_opt_chart_main_height', "240");
$ajsi_opt_chart_vol_height = get_option('ajsi_opt_chart_vol_height', "100");
$ajsi_opt_chart_down_color = get_option('ajsi_opt_chart_down_color', "#0f9d58");
$ajsi_opt_chart_up_color = get_option('ajsi_opt_chart_up_color', "#a52714");
$ajsi_opt_debug = get_option('ajsi_opt_debug', "N");
?>
    <h2 class="nav nav-tabs">
        <a href="?page=ajsi-plugin-setting&tab=basic" class="nav-tab ajsi_tab" style="color:#999999">기본 설정</a>
        <a href="?page=ajsi-plugin-setting&tab=getdata" class="nav-tab ajsi_tab" style="color:#999999">데이터 가져오기</a>
        <a href="?page=ajsi-plugin-setting&tab=chart" class="nav-tab nav-tab-active nav-tab-active" style="background:white;color:black;border-bottom:0px">차트 설정 <span class="badge badge-info">Pro</span></a>
        <?php if($ajsi_opt_debug == "Y"){?>
        <a href="?page=ajsi-plugin-setting&tab=debug" class="nav-tab ajsi_tab" style="color:#999999">로그</a>
        <?php }?>
        <a href="?page=ajsi-plugin-setting&tab=lic" class="nav-tab ajsi_tab" style="color:#999999">라이센스 정보</a>
    </h2>
    <div style="vertical-align:middle;border:1px solid #999999;">
        <div style="background:white;vertical-align:middle;padding-left:10px;border-bottom:1px solid #999999">
            <h4 style="padding-top:10px;margin-top: 0px;">차트 설정 <span class="badge badge-info">Pro</span></h4>
            <div style="padding-bottom:10px;">차트에 대한 세밀한 설정이 가능합니다.<br/>
            아래의 설정은 모든 Shortcode와 Widget에 즉시 반영됩니다.</div>
        </div>
		<?php if(AJSI_ISPRO){?>
        <form method="post" action="" class="ajrv_setting" style="padding-left:10px;margin-bottom:10px;">
			<?php wp_nonce_field('ajsi_chart_set'); ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">기본 차트 설정</th>
                        <td>
                            <input type="radio" name="ajsi_opt_chartviewtype" <?php if($ajsi_opt_chartviewtype == "1d"){ echo "checked"; }?> value="1d">일봉 차트
                            <input type="radio" name="ajsi_opt_chartviewtype" <?php if($ajsi_opt_chartviewtype == "1m"){ echo "checked"; }?> value="1m">1분 라인 차트
                            <!--<input type="radio" name="ajsi_opt_chartviewtype" <?php if($ajsi_opt_chartviewtype == "1w"){ echo "checked"; }?> value="1w">주봉 차트-->
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">일봉 차트 범위</th>
                        <td>
                            최근 <input type="number" name="ajsi_opt_chart_daycount" min=10 max=100 value=<?php echo $ajsi_opt_chart_daycount?>>일 ~ 현재까지
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">일봉/1분 차트 높이</th>
                        <td>
                            <input type="number" name="ajsi_opt_chart_main_height" min=100 max=600 value=<?php echo $ajsi_opt_chart_main_height?>>px (100 ~ 600)
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">거래량 차트 높이</th>
                        <td>
                            <input type="number" name="ajsi_opt_chart_vol_height" min=100 max=300 value=<?php echo $ajsi_opt_chart_vol_height?>>px (100 ~ 300)
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">일봉 차트 색상</th>
                        <td class="form-inline">
							<div class="form-group">
								<label style="margin-right:4px;">양복 색상</label>
								<input type="hidden" class="aj_si_cp" name="ajsi_opt_chart_down_color" value="<?php echo $ajsi_opt_chart_down_color?>">
							</div>
							<div class="form-group" style="margin-left:12px;">
								<label style="margin-right:4px;">음복 색상</label>
								<input type="hidden" class="aj_si_cp" name="ajsi_opt_chart_up_color" value="<?php echo $ajsi_opt_chart_up_color?>">
							</div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">차트 하단 거래량 차트</th>
                        <td>
                            <input type="radio" name="ajsi_opt_chartvolume" <?php if($ajsi_opt_chartvolume == "Y"){ echo "checked"; }?> value="Y">보여주기
                            <input type="radio" name="ajsi_opt_chartvolume" <?php if($ajsi_opt_chartvolume == "N"){ echo "checked"; }?> value="N">가리기
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="submit" class="button button-primary" value="변경 사항 적용">
        </form>
        <?php }else{?>
        <div method="post" action="" class="ajrv_setting">
            <div class="alert" style="margin-bottom:0px;">
                <h5 style="margin-bottom:0px;">Pro 버전에서만 제공되는 기능입니다.</h5>
				<div>
					<a href="?page=ajsi-plugin-setting&tab=lic">라이센스 정보</a>에서 Pro 버전을 구매하면 정상적으로 모든 기능을 이용할 수 있습니다.<br/>
					<p>Pro 버전인 경우, 아래와 같은 실시간 차트를 Page/Post에 삽입할 수 있습니다.</p>
				</div>
				<table>
					<tr>
						<td><img src='<?php echo plugin_dir_url(__FILE__);?>images/aj-stock-chart.png'></td>
						<td style="vertical-align: top;padding-left:15px;">
							<h5>일봉 차트와 1분 차트 제공</h5>
							<div> 기본으로 일봉 캔들 차트와 1분단위 라인차트가 제공됩니다.<br/>
							설정을 통하여 거래량 차트와 차트 범위등을 상세히 설정할 수 있습니다.</div>
							<p></p>
							<h5>실시간 반영</h5>
							<div>1분마다 자동 업데이트 됩니다.<br/>
							화면의 깜박임 없이 변경된 사항이 자동으로 반영됩니다.</div>
						</td>
					</tr>
				</table>
            </div>
        </div>
        <?php }?>
    </div>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.aj_si_cp').minicolors();
});
</script>
