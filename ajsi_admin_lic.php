<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $table_prefix, $wpdb;
$ajsi_opt_debug = get_option('ajsi_opt_debug', "N");
$ret_db = $wpdb->get_results("select * from ".$table_prefix."ajsi_stock_log order by updated desc;" );
//$lic_match = false;
?>
    <h2 class="nav nav-tabs">
        <a href="?page=ajsi-plugin-setting&tab=basic" class="nav-tab ajsi_tab" style="color:#999999">기본 설정</a>
        <a href="?page=ajsi-plugin-setting&tab=getdata" class="nav-tab ajsi_tab" style="color:#999999">데이터 가져오기</a>
        <a href="?page=ajsi-plugin-setting&tab=chart" class="nav-tab nav-tab-active" style="color:#999999">차트 설정 <span class="badge badge-info">Pro</span></a>
        <?php if($ajsi_opt_debug == "Y"){?>
        <a href="?page=ajsi-plugin-setting&tab=debug" class="nav-tab ajsi_tab" style="color:#999999">로그</a>
        <?php }?>
        <a href="?page=ajsi-plugin-setting&tab=lic" class="nav-tab ajsi_tab_active nav-tab-active" style="background:white;color:black;border-bottom:0px">라이센스 정보</a>
    </h2>
    <div style="vertical-align:middle;border:1px solid #999999;">
        <div style="background:white;vertical-align:middle;padding-left:10px;border-bottom:1px solid #999999">
            <h4 style="padding-top:10px;margin-top: 0px;">라이센스 정보</h4>
            <div style="padding-bottom:10px;">
            레이센스에 대한 정보를 관리합니다. 라이센스가 있는 경우, AJ 주식의 모든 기능을 사용할 수 있습니다.<br/>
            라이센스는 도메인에 할당됩니다. 도메인이 변경되면 새로운 라이센스를 발급받아야 합니다.
            </div>
        </div>
        <?php if($lic_match){?>
        <div class="ajrv_setting" style="padding-left:10px;margin-bottom:10px;">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">도메인</th>
                        <td>
                            <?php echo $pinfo["domain"]?>
                        </td>
                    </tr>
					<?php if(AJSI_ISPRO){?>
						<tr>
							<th scope="row">라이센스 발급일시</th>
							<td>
								<?php echo $pinfo["created"]?>
							</td>
						</tr>
						<?php if($pinfo["expire"]){?>
						<tr>
							<th scope="row">라이센스 만료일시</th>
							<td>
								<?php echo $pinfo["expire"] ? $pinfo["expire"] : "무제한"?>
							</td>
						</tr>
						<?php }?>
						<tr>
							<th scope="row">Plugin Version</th>
							<td>
								<?php echo $pinfo["version"]?>
							</td>
						</tr>
                    <?php }else{?>
						<tr>
							<th scope="row">라이센스 관련 정보</th>
							<td>
								<p><a class="btn btn-primary btn-sm" target="_blank" href="https://2p1d.com/aj-%EC%A3%BC%EC%8B%9D-%ED%94%8C%EB%9F%AC%EA%B7%B8%EC%9D%B8/aj-%EC%A3%BC%EC%8B%9D-shortcodes/#aj-stock-version">AJ 주식 Pro 버전 안내</a></p>
								<p><a class="btn btn-success btn-sm" target="_blank" href="https://2p1d.com/qna/">2p1d@2p1d.com 에 구매 문의 하기</a></p>
							</td>
						</tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <?php }else{?>
        <div class="ajrv_setting" style="padding-left:10px;margin-bottom:10px;">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">도메인</th>
                        <td>
                            <input type="text" name="domain" id="domain" value="<?php echo $_SERVER["HTTP_HOST"];?>">
                            <div><span>지정한 도메인으로 접속된 사이트에서만 사용이 가능합니다.</span></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">인증키</th>
                        <td>
                            <input type="text" name="authkey" id="authkey" value="" style="width:600px">
                            <div><span><a href="https://www.2p1d.com" target="_blank">2P1D</a>에서 구입한 인증키를 입력해주세요.</span></div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-primary" id="btnRequestKey">발급 요청하기</button>
        </div>
        <?php }?>
    </div>
