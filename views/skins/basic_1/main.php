<?php
$aris_info_box = "
<div class='aj_bs_iso'>
    <div class='row ajsi_info_box' style='margin-left:0px;margin-right:0px;border:1px solid #efefef;padding:8px;'>
        <div class='col-md-4'>
            <div class='ajsi_updown_chk'>
                <span class='ajsi_updown ajsi_big font-weight-bold ajsi_CurJuka' style='font-size:28px;'>[CurJuka]</span>
                <div class='ajsi_small'>
                    <label>전일 대비</label>
                    <img class='ajsi_DungRak_up ajsi_arrow_updown' src='[DungRak_UP]' style='display:none;'>
                    <img class='ajsi_DungRak_down ajsi_arrow_updown' src='[DungRak_DOWN]' style='display:none;'>
                    <img class='ajsi_DungRak_upup ajsi_arrow_updown' src='[DungRak_UPUP]' style='display:none;'>
                    <img class='ajsi_DungRak_downdown ajsi_arrow_updown' src='[DungRak_DOWNDOWN]' style='display:none;'>
                    <img class='ajsi_DungRak_same ajsi_arrow_updown' src='[DungRak_SAME]' style='display:none;'>
                    <span class='ajsi_updown ajsi_diffYesterday'>([diffYesterday])</span>
                    <span class='ajsi_updown ajsi_Debi'>[Debi]</span>
                </div>
            </div>
            <hr class='d-none d-sm-block d-md-none'/>
        </div>
        <div class='col-md-8 pull-right'>
            <table class='table table-borderless ajsi_info_money' style='margin-bottom:0px;margin-bottom: 0px;'>
                <tr>
                    <td>
                        <dt class='text-right'>시가</dt>
                        <dd class='text-right' style='margin:0px'><b><span class='ajsi_StartJuka h3'>[StartJuka]</span></b></dd>
                    </td>
                    <td>
                        <dt class='text-right'>고가</dt>
                        <dd class='text-right' style='margin:0px'><b><span class='ajsi_HighJuka h3'>[HighJuka]</span></b></dd>
                    </td>
                    <td>
                        <dt class='text-right'>저가</dt>
                        <dd class='text-right' style='margin:0px'><b><span class='ajsi_LowJuka h3'>[LowJuka]</span></b></dd>
                    </td>
                    <td>
                        <dt class='text-right'>거래량<dt>
                        <dd class='text-right' style='margin:0px'><b><span class='ajsi_Volume h3'>[Volume]</span></b></dd>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
";
$aris_detail_box = "
<div class='aj_bs_iso'>
    <table class='table table-striped'>
        <tbody>
            <tr>
                <td>전일가(원)</td>
                <td class='text-right'><b><span class='ajsi_PrevJuka'>[PrevJuka]</span></b></td>
                <td class='ajsi_brl'>52주최고(원)</td>
                <td class='text-right'><b><span class='ajsi_High52'>[High52]</span></b></td>
            </tr>
            <tr>
                <td>상한가(원)</td>
                <td class='text-right'><b><span class='ajsi_UpJuka'>[UpJuka]</span></b></td>
                <td class='ajsi_brl'>52주최저(원)</td>
                <td class='text-right'><b><span class='ajsi_Low52'>[Low52]</span></b></td>
            </tr>
            <tr>
                <td>하한가(원)</td>
                <td class='text-right'><b><span class='ajsi_DownJuka'>[DownJuka]</span></b></td>
                <td class='ajsi_brl'>PER</td>
                <td class='text-right'><b><span class='ajsi_Per'>[Per]</span></b></td>
            </tr>
            <tr>
                <td>액면가(원)</td>
                <td class='text-right'><b><span class='ajsi_FaceJuka'>[FaceJuka]</span></b></td>
                <td class='ajsi_brl'></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
";
$aris_seller_box = "
<div class='aj_bs_iso'>
    <div class='row'>
        <div class='col-md-6'>
            <table class='table'>
                <thead class='ajsi_table_header'>
                    <tr>
                        <th>매도 상위</th>
                        <th class='text-right'>거래량</th>
                    </tr>
                </thead>
                <tbody class='ajsi_data_sell'>
                </tbody>
            </table>
        </div>
        <div class='col-md-6'>
            <table class='table'>
                <thead class='ajsi_table_header'>
                    <tr>
                        <th>매수 상위</th>
                        <th class='text-right'>거래량</th>
                    </tr>
                </thead>
                <tbody class='ajsi_data_buy'>
                </tbody>
            </table>
        </div>
    </div>
</div>
";
$aris_seller_box_item = "
<tr>
    <td class='ajsi_th'>[NAME]</td>
    <td class='text-right'><span class='ajsi_b ajsi_[UPDOWN]'>[VAL]</span></td>
</tr>
";
$aris_chart_box = "
<div class=''>
    <div class='ajsi_cs_chart ajsi_cs_chart_[RND]' chartkey='[RND]' style='width:100%;height:[MHEIGHT]px'></div>
</div>";
$aris_chart_box_vol = "
<div class=''>
    <div class='ajsi_vol_chart ajsi_cs_vol_chart_[RND]' chartkey='[RND]' style='width:100%;height:[VHEIGHT]px'></div>
</div>
";
