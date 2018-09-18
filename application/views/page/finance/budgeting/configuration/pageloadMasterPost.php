<style type="text/css">
	#tableData1 thead th,#tableData1 tfoot td {

	    text-align: center;
	    background: #20485A;
	    color: #FFFFFF;

	}
</style>
<div class="col-xs-12" >
	<div class="panel panel-primary">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Master Post</h4>
            <div class="toolbar no-padding pull-right">
                <span data-smt="" class="btn btn-add btn-add-master-post">
                    <i class="icon-plus"></i> Add
               </span>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive" id = "loadTable1">

            </div>	
        </div>
	</div>
</div>
<div class="col-xs-12" >
    <div class="panel panel-primary">
        <div class="panel-heading clearfix">
            <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Realization Post / Sub Post</h4>
            <div class="toolbar no-padding pull-right">
                <span data-smt="" class="btn btn-add btn-add-realization-Post">
                    <i class="icon-plus"></i> Add
               </span>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive" id = "loadTable2">

            </div>  
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    loadTable1();
    loadTable2();

    $(".btn-add").click(function(){
	    modal_generate('add','Add');
    });
    
}); // exit document Function

function modal_generate(action,title,ID='') {
    var url = base_url_js+"budgeting/MasterPost/modalform";
    var data = {
        Action : action,
        CDID : ID,
    };
    var token = jwt_encode(data,"UAP)(*");
    $.post(url,{ token:token }, function (html) {
        $('#GlobalModal .modal-header').html('<h4 class="modal-title">'+title+'</h4>');
        $('#GlobalModal .modal-body').html(html);
        $('#GlobalModal .modal-footer').html(' ');
        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        $('#ModalbtnSaveForm').click(function(){
        	if (confirm("Are you sure?") == true) {
        	    loading_button('#ModalbtnSaveForm');
        	    var url = base_url_js+'budgeting/time_period/modalform/save';

        	    var Year = $("#Year").val();
        	    var MonthStart = $("#MonthStart").val();
        	    var MonthEnd = $("#MonthEnd").val();
        	    var action = $(this).attr('action');
        	    var id = $("#ModalbtnSaveForm").attr('kodeuniq');
        	    var data = {
        	    			Year : Year,
        	                MonthStart : MonthStart,
        	                MonthEnd : MonthEnd,
        	                Action : action,
        	                CDID : id
        	                };
        	    var token = jwt_encode(data,"UAP)(*");
        	    $.post(url,{token:token},function (data_json) {
                	var response = jQuery.parseJSON(data_json);
                	if (response == '') {
                		toastr.success('Data berhasil disimpan', 'Success!');
                	}
                	else
                	{
                		toastr.error(response, 'Failed!!');
                	}
                	loadTable();
                	$('#GlobalModal').modal('hide');
                }).done(function() {
                  // loadTable();
                }).fail(function() {
                  toastr.error('The Database connection error, please try again', 'Failed!!');
                }).always(function() {
                 $('#ModalbtnSaveForm').prop('disabled',false).html('Save');

                });

        	  } 
        	  else {
        	    return false;
        	  }
               
        });
    })

}

function loadTable1()
{
	$("#loadTable1").empty();
	var TableGenerate = '<table class="table table-bordered tableData" id ="tableData1">'+
						'<thead>'+
						'<tr>'+
							'<th width = "3%">No</th>'+
                            '<th>Post Code</th>'+
							'<th>Post Name</th>'+
							'<th>Action</th>'+
						'</tr></thead>'	
						;
	TableGenerate += '<tbody>';

	var dataForTable = [];
	var url = base_url_js+'budgeting/table_all/cfg_post/1';
	$.post(url,function (resultJson) {
	    var response = jQuery.parseJSON(resultJson);
	    dataForTable = response;
	    // console.log(dataForTable);
	    for (var i = 0; i < dataForTable.length; i++) {
	    	var btn_edit = '<button type="button" class="btn btn-warning btn-edit btn-edit-post" code = "'+dataForTable[i].CodePost+'"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
	    	var btn_del = ' <button type="button" class="btn btn-danger btn-delete btn-delete-post"  code = "'+dataForTable[i].CodePost+'"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';
	    	TableGenerate += '<tr>'+
	    						'<td width = "3%">'+ (parseInt(i) + 1)+'</td>'+
                                '<td>'+ dataForTable[i].CodePost+'</td>'+
	    						'<td>'+ dataForTable[i].PostName+'</td>'+
	    						'<td>'+ btn_edit + ' '+' &nbsp' + btn_del+'</td>'+
	    					 '</tr>'	
	    }

	    TableGenerate += '</tbody></table>';
	    $("#loadTable1").html(TableGenerate);
	    LoaddataTableStandard("#tableData1");

        $(".btn-edit-post").click(function(){
    	    var ID = $(this).attr('year');
    	     modal_generate('edit','Edit',ID);
        });

        $(".btn-delete-post").click(function(){	
            var ID = $(this).attr('code');
             $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Are you sure ? </b> ' +
                 '<button type="button" id="confirmYesDelete" class="btn btn-primary" style="margin-right: 5px;" data-smt = "'+ID+'">Yes</button>' +
                 '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                 '</div>');
             $('#NotificationModal').modal('show');

            $("#confirmYesDelete").click(function(){
                 $('#NotificationModal .modal-header').addClass('hide');
                 $('#NotificationModal .modal-body').html('<center>' +
                     '                    <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>' +
                     '                    <br/>' +
                     '                    Loading Data . . .' +
                     '                </center>');
                 $('#NotificationModal .modal-footer').addClass('hide');
                 $('#NotificationModal').modal({
                     'backdrop' : 'static',
                     'show' : true
                 });
                 var url = base_url_js+'budgeting/masterpost/modalform/save';
                 var aksi = "delete";
                 var ID = $(this).attr('data-smt');
                 var data = {
                     Action : aksi,
                     CDID : ID,
                 };
                 var token = jwt_encode(data,"UAP)(*");
                 $.post(url,{token:token},function (data_json) {
                     setTimeout(function () {
                        toastr.options.fadeOut = 10000;
                        toastr.success('Data berhasil disimpan', 'Success!');
                        loadTable1();
                        $('#NotificationModal').modal('hide');
                     },500);
                 });
            });

        });
	}); 
					
}

function loadTable2()
{
    $("#loadTable2").empty();
    var TableGenerate = '<table class="table table-bordered" id ="tableData2">'+
                        '<thead>'+
                        '<tr>'+
                            '<th width = "3%">No</th>'+
                            '<th>Post Code Realization</th>'+
                            '<th>Post Code</th>'+
                            '<th>Realization Name</th>'+
                            '<th>Departement</th>'+
                            '<th>Action</th>'+
                        '</tr></thead>' 
                        ;
    TableGenerate += '<tbody>';

    var dataForTable = [];
    var url = base_url_js+'budgeting/get_cfg_postrealisasi';
    $.post(url,function (resultJson) {
        var response = jQuery.parseJSON(resultJson);
        dataForTable = response;
        // console.log(dataForTable);
        for (var i = 0; i < dataForTable.length; i++) {
            var btn_edit = '<button type="button" class="btn btn-warning btn-edit btn-edit-postrealization" code = "'+dataForTable[i].CodePostRealisasi+'"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
            var btn_del = ' <button type="button" class="btn btn-danger btn-delete btn-delete-postrealization"  code = "'+dataForTable[i].CodePostRealisasi+'"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';
            TableGenerate += '<tr>'+
                                '<td width = "3%">'+ (parseInt(i) + 1)+'</td>'+
                                '<td>'+ dataForTable[i].CodePostRealisasi+'</td>'+
                                '<td>'+ dataForTable[i].CodePost+'</td>'+ // plus name
                                '<td>'+ dataForTable[i].RealisasiPostName+'</td>'+
                                '<td>'+ dataForTable[i].Departement+'</td>'+
                                '<td>'+ btn_edit + ' '+' &nbsp' + btn_del+'</td>'+
                             '</tr>'    
        }

        TableGenerate += '</tbody></table>';
        $("#loadTable2").html(TableGenerate);
        LoaddataTableStandard("#tableData2");

        $(".btn-edit-postrealization").click(function(){
            var ID = $(this).attr('year');
             modal_generate('edit','Edit',ID);
        });

        $(".btn-delete-postrealization").click(function(){  
            var ID = $(this).attr('code');
             $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Are you sure ? </b> ' +
                 '<button type="button" id="confirmYesDelete" class="btn btn-primary" style="margin-right: 5px;" data-smt = "'+ID+'">Yes</button>' +
                 '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                 '</div>');
             $('#NotificationModal').modal('show');

            $("#confirmYesDelete").click(function(){
                 $('#NotificationModal .modal-header').addClass('hide');
                 $('#NotificationModal .modal-body').html('<center>' +
                     '                    <i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>' +
                     '                    <br/>' +
                     '                    Loading Data . . .' +
                     '                </center>');
                 $('#NotificationModal .modal-footer').addClass('hide');
                 $('#NotificationModal').modal({
                     'backdrop' : 'static',
                     'show' : true
                 });
                 var url = base_url_js+'budgeting/masterpost/modalform/save';
                 var aksi = "delete";
                 var ID = $(this).attr('data-smt');
                 var data = {
                     Action : aksi,
                     CDID : ID,
                 };
                 var token = jwt_encode(data,"UAP)(*");
                 $.post(url,{token:token},function (data_json) {
                     setTimeout(function () {
                        toastr.options.fadeOut = 10000;
                        toastr.success('Data berhasil disimpan', 'Success!');
                        loadTable1();
                        $('#NotificationModal').modal('hide');
                     },500);
                 });
            });

        });
    }); 
                    
}

</script>