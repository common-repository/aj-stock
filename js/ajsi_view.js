jQuery(document).ready(function($){
    function ajsi_data_update(){
        $.ajax({
            url : getinfo.ajax_url,
            type : 'get',
            data : {
                action : 'ajsi_get_xml_data',
				nonce : getinfo.nonce
            },
            success : function(response) {
                setData(JSON.parse(response));
            }
        });
    }
    function setData(data){
        $.each($(".ajsi_CurJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["CurJuka"]); });
        $.each($(".ajsi_HighJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["HighJuka"]); });
        $.each($(".ajsi_LowJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["LowJuka"]); });
        $.each($(".ajsi_Volume"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["Volume"]); });
        $.each($(".ajsi_PrevJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["PrevJuka"]); });
        $.each($(".ajsi_High52"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["High52"]); });
        $.each($(".ajsi_Low52"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["Low52"]); });
        $.each($(".ajsi_DownJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["DownJuka"]); });
        $.each($(".ajsi_FaceJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["FaceJuka"]); });
        $.each($(".ajsi_Per"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["Per"]); });
        $.each($(".ajsi_UpJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["UpJuka"]); });
        $.each($(".ajsi_StartJuka"), function (k, v){ $(v).text(data["TBL_StockInfo"]["@attributes"]["StartJuka"]); });

        var cur = parseFloat(replaceAll(data["TBL_StockInfo"]["@attributes"]["CurJuka"], ",", ""));
        var pre = parseFloat(replaceAll(data["TBL_StockInfo"]["@attributes"]["PrevJuka"], ",", ""));
        var diffVal = parseFloat((cur - pre) * 100 / pre).toFixed(2);
        $.each($(".ajsi_Debi"), function (k, v){ $(v).text("(" + diffVal + "%)"); });
        $.each($(".ajsi_diffYesterday"), function (k, v){ $(v).text(formatNumber(cur - pre)); });

        //Detail box
        $.each($(".ajsi_arrow_updown"), function (k, v){ $(v).hide(); });
        if(cur > pre){
            $.each($(".ajsi_updown_chk"), function (k, v){ 
                $(v).removeClass("ajsi_down");
                if(!$(v).hasClass("ajsi_up"))
                    $(v).addClass("ajsi_up");
            });
            $.each($(".ajsi_DungRak_up"), function (k, v){ $(v).show(); });
        }else if(cur < pre){
            $.each($(".ajsi_updown_chk"), function (k, v){ 
                $(v).removeClass("ajsi_up");
                if(!$(v).hasClass("ajsi_down"))
                    $(v).addClass("ajsi_down");
            });
            $.each($(".ajsi_DungRak_down"), function (k, v){ $(v).show(); });
        }else{
            $.each($(".ajsi_DungRak_same"), function (k, v){ $(v).show(); });
        }

    $.each($(".ajsi_data_sell"), function(k2, v){
	$(v).empty();
    });
    $.each($(".ajsi_data_buy"), function(k2, v){
	$(v).empty();
    });

        //Seller & Buyer
        $.each(data["TBL_AskPrice"]["AskPrice"], function(k, d){
            $.each($(".ajsi_data_sell"), function(k2, v){
                var tr = "<tr><td class='ajsi_th'>"+d["@attributes"]["member_memsoMem"]+"</td><td class='text-right'>";
                tr += "<span class='ajsi_b ajsi_down'>"+d["@attributes"]["member_mesuoVol"]+"</span></td></tr>";
                $(v).append(tr);
            });
            $.each($(".ajsi_data_buy"), function(k2, v){
                var tr = "<tr><td class='ajsi_th'>"+d["@attributes"]["member_memdoMem"]+"</td><td class='text-right'>";
                tr += "<span class='ajsi_b ajsi_up'>"+d["@attributes"]["member_memdoVol"]+"</span></td></tr>";
                $(v).append(tr);
            });
        });
    }
    ajsi_data_update();
    if(getinfo.refresh_interval != "NO")
        setInterval (ajsi_data_update, getinfo.refresh_interval);
});
function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
  }
function replaceAll(str, searchStr, replaceStr) {
    return str.split(searchStr).join(replaceStr);
}
