jQuery(document).ready(function($){
    $("#ajsi_admin_clear_data").click(function(e){
        e.preventDefault();
        ajsi_go_get_data("clear");
        $(this).prop("disabled", true);
        $("#ajsi_admin_get_data").prop("disabled", true);
        //$("#sjsi_dv_prog").show();
    });
    $("#ajsi_admin_get_data").click(function(e){
        e.preventDefault();
		if(confirm("모든 데이터를 초기화하고 이전 데이터를 가져오시겠습니까?")){
			ajsi_go_get_data("getdata");
			$(this).prop("disabled", true);
			$("#ajsi_admin_clear_data").prop("disabled", true);
			$("#sjsi_dv_prog").show();
		}
    });
    function ajsi_go_get_data(tp){
        $.ajax({
            url : admin_url,
            type : 'get',
            data : {
                action : 'ajsi_get_ex_data',
                code : admin_code,
                type : tp,
                page : page,
                max : max,
            },
            success : function(response) {
                var res = JSON.parse(response);
                if(res.error == "Y"){
                    alert("데이터를 가져오는 중에 문제가 발생했습니다.");
                    return;
                }
                if(res.type == "clear"){
                    alert("데이터를 초기화했습니다.");
                    $("#ajsi_admin_clear_data").prop("disabled", false);
                    $("#ajsi_admin_get_data").prop("disabled", false);
                }else if(res.type == "getdata"){
                    page = parseInt(res.data.page);
                    max = parseInt(res.data.max);
                    if(isfirst){
                        //$("#ajsi_get_data_prog").attr("aria-valuemax", max);
                        isfirst = false;
                    }
                    $("#ajsi_get_data_prog").css("width", ((page * 100) / max) + "%");
                    if(page < max){
                        page++;
                        console.log(page + " / " + max);
                        ajsi_go_get_data(res.type);
                    }else{
                        ajsi_go_get_data("getall");
                        alert("모든 데이터를 가져왔습니다.");
                        page = 1;
                        max = 0;
                        isfirst = true;
                        $("#ajsi_admin_clear_data").prop("disabled", false);
                        $("#ajsi_admin_get_data").prop("disabled", false);
                    }
                }
            }
        });
    }
});
