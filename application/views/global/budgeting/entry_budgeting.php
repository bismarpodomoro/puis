<script type="text/javascript" src="<?php echo base_url();?>assets/custom/jquery.maskMoney.js"></script>
<div class="tabbable tabbable-custom tabbable-full-width btn-read menuEBudget">
    <ul class="nav nav-tabs">
        <li role="presentation">
            <a href="<?php echo base_url().'/budgeting' ?>" style="padding:0px 15px">
                <button class="btn btn-primary" id="btnBackToHome" name="button"><i class="fa fa-home" aria-hidden="true"></i></button>
            </a>
        </li>
        <li class="<?php if($request==null || $request=='EntryBudget'){echo "active";} ?>">
            <a href="javascript:void(0)" class="pageAnchor" page = "EntryBudget">EntryBudget</a>
        </li>
        <li class="<?php if($request=='View'){echo "active";} ?>">
            <a href="javascript:void(0)" class="pageAnchor" page = "View">View</a>
        </li>
    </ul>
    <div style="padding-top: 30px;border-top: 1px solid #cccccc">
        <div class="row" id = "pageContent">
           
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#container").attr('class','fixed-header sidebar-closed');
        <?php if ($request == null): ?>
            LoadPage('EntryBudget');
        <?php else: ?>
           LoadPage("<?php echo $request ?>");    
        <?php endif ?>

        $(".pageAnchor").click(function(){
            var Page = $(this).attr('page');
            $(".menuEBudget li").removeClass('active');
            $(this).parent().addClass('active');
            $("#pageContent").empty();
            LoadPage(Page);
        });
        
    }); // exit document Function

    function LoadPage(page)
    {
        loading_page("#pageContent");
        var url = base_url_js+'budgeting/EntryBudget/'+page;
        $.post(url,function (resultJson) {
            var response = jQuery.parseJSON(resultJson);
            var html = response.html;
            var jsonPass = response.jsonPass;
            setTimeout(function () {
                $("#pageContent").html(html);
            },1000);
            
        }); // exit spost
    }
</script>