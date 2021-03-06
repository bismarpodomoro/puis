<div class="row">
    <div class="col-xs-12" >
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Report</h4>
            </div>
            <div class="panel-body">
                <div class="tabbable tabbable-custom tabbable-full-width btn-read setRule">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="javascript:void(0)" class="PageNav" page = "report_admission">Report By Admission</a>
                        </li>
                        <li class="">
                            <a href="javascript:void(0)" class="PageNav" page = "report_finance">Report By Finance</a>
                        </li>
                    </ul>
                    <div style="padding-top: 30px;border-top: 1px solid #cccccc">
                        <div class="row" id = "pageSet" style="margin-left: 0px;margin-right: 0px">
                           
                        </div>
                    </div>
                </div>            
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    LoadPage('report_admission');
    $(".PageNav").click(function(){
        var Page = $(this).attr('page');
        $(".setRule li").removeClass('active');
        $(this).parent().addClass('active');
        LoadPage(Page)
    })

}); // exit document Function

function LoadPage(Page)
{
    $("#pageSet").empty();
    loading_page("#pageSet");
    var url = base_url_js+'finance/report_admission/'+Page;
    $.post(url,function (resultJson) {
        var response = jQuery.parseJSON(resultJson);
        var html = response.html;
        var jsonPass = response.jsonPass;
        $("#pageSet").html(html);
    }); // exit spost
}
</script>