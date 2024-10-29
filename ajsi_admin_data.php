<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$ajsi_opt_isgetdata = get_option('ajsi_opt_isgetdata', "");
$ajsi_opt_stockcode = get_option('ajsi_opt_stockcode', "");
$ajsi_opt_debug = get_option('ajsi_opt_debug', "N");
?>
    <h2 class="nav nav-tabs">
        <a href="?page=ajsi-plugin-setting&tab=basic" class="nav-tab ajsi_tab" style="color:#999999">기본 설정</a>
        <a href="?page=ajsi-plugin-setting&tab=getdata" class="nav-tab ajsi_tab_active nav-tab-active" style="background:white;color:black;border-bottom:0px">데이터 가져오기</a>
        <a href="?page=ajsi-plugin-setting&tab=chart" class="nav-tab nav-tab-active" style="color:#999999">차트 설정 <span class="badge badge-info">Pro</span></a>
        <?php if($ajsi_opt_debug == "Y"){?>
        <a href="?page=ajsi-plugin-setting&tab=debug" class="nav-tab ajsi_tab" style="color:#999999">로그</a>
        <?php }?>
        <a href="?page=ajsi-plugin-setting&tab=lic" class="nav-tab ajsi_tab" style="color:#999999">라이센스 정보</a>
    </h2>
    <div style="vertical-align:middle;border:1px solid #999999;">
        <div style="background:white;vertical-align:middle;padding-left:10px;border-bottom:1px solid #999999">
           <h4 style="padding-top:10px;margin-top: 0px;">데이터 가져오기</h4>
            <div style="padding-bottom:10px;">세팅 초기에 과거 일자별 시세를 가져옵니다.<br/>이 작업은 초기 1회 진행하거나 데이터를 다시 가져올때만 진행하는 것이 좋습니다 .<br/>
            이 작업은 매우 오래걸리 수 있습니다. 완료되기전까지 기다려야 합니다.<br/>
            데이터 가져오기를 하면, <b>기존의 1일 데이터를 모두 초기화</b> 하고 진행됩니다.</div>
        </div>
        <?php if($lic_match){?>
            <?php if(!$ajsi_opt_stockcode){?>
            <div class="alert alert-danger" style="margin-bottom:0px;">
                종목 코드가 등록되지 않았습니다.<br/>
                <a href="?page=ajsi-plugin-setting&tab=basic">기본 설정</a>에서 종목 코드를 입력하고 저장한 다음 데이터 가져오기를 실행하시기 바랍니다.
            </div>
            <?php }else{?>
            <div class="card" style="max-width:inherit;margin-top:0px;">
                <div class="card-body">
                    <div>
						<?php if($ajsi_opt_isgetdata){?>
							<div class="alert alert-success">
								이미 데이터를 한번 가져왔습니다. 추가로 가져오기할 필요는 없습니다.<br/>
								기존의 일일 주가 데이터를 초기화 하고 다시 가져오려면 아래의 데이터 가져오기를 클릭해주시기 바랍니다.
							</div>
						<?php }?>
                        <button type="button" class="btn btn-primary" id="ajsi_admin_get_data">데이터 가져오기</button>
                        <div>이미 한번 가져왔다면, 또 다시 데이터 가져오기를 할 필요는 없습니다.<br/>
						시간이 오래걸리 수 있습니다. <b>완료될때가지 페이지 이동을 하면 안됩니다.</b></div>
                    </div>
                    <div id="sjsi_dv_prog" style="display:none;">
                        <label>진행 상황</label>
                        <div class="progress" style="height:40px;">
                            <div class="progress-bar" id="ajsi_get_data_prog" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
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
<script type="text/javascript">
var page = 1;
var max = 0;
var isfirst = true;
var admin_url = "<?php echo admin_url('admin-ajax.php')?>";
var admin_code = "<?php echo $ajsi_opt_stockcode;?>";
</script>
