<div class="row">
	<div id = "loadPageTable">
		
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    LoadTableData();

    function LoadTableData()
    {
    	$("#loadPageTable").empty();
    	var TableGenerate = '<div class = "row" style = "margin-top : 15px;margin-left : 0px;margin-right : 0px">'+         
    							'<div class="col-md-12" id = "pageForTable">'+
    									'<div class="table-responsive">'+
    										'<table class="table table-bordered tableData" id ="tableData3">'+
    										'<thead>'+
    										'<tr>'+
    											'<th width = "3%">No</th>'+
    				                            '<th>Division</th>'+
    				                            '<th>Description</th>'+
    											'<th>Menu Navigation</th>'+
    											'<th>Email</th>'+
    											'<th>Status Div</th>'+
    											'<th>Action</th>'+
    										'</tr></thead>'	
    								;
    	TableGenerate += '<tbody>';
    	var url = base_url_js+"api/__getDivision";
		$.post(url,function (resultJson) {
			var arr_menu_nav = [];
			for (var i = 0; i < resultJson.length; i++) {
				// check exist Menu Navigation
				var find = 1; 
				for (var l = 0; l < arr_menu_nav.length; l++) {
					var IDMenuNavigation = resultJson[i].IDMenuNavigation;
					if (IDMenuNavigation == arr_menu_nav[l].IDMenuNavigation) {
						find = 0;
						break;
					}
				}

				if (find == 1) {
					if (resultJson[i].MenuNavigation != null) {
						var temp = {
							IDMenuNavigation :resultJson[i].IDMenuNavigation,
							MenuNavigation : resultJson[i].MenuNavigation, 
						}

						arr_menu_nav.push(temp);
					}
				}
			}
			var EditBtn = '';
			var SaveBtn = '';
			var DivFormAdd = '<div id = "FormAdd"></div>';
			if (resultJson.length > 0) {
				EditBtn = '<button type="button" class="btn btn-warning btn-edit"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>';
				SaveBtn = '<div class = "row" style = "margin-left : 0px;margin-right : 0px;margin-top:10px" id = "rowSaveEdit"></div>';
				
			}
	    	var Mode = '<div class = "row" style = "margin-left : 0px;margin-right : 0px">'+
							'<div class = "col-xs-4">'+
									'<span data-smt="" class="btn btn-add">'+
				                    	'<i class="icon-plus"></i> Add'+
				               		'</span>'+'&nbsp'+EditBtn+
					        '</div>'+
					    '</div>';
			for (var i = 0; i < resultJson.length; i++) {
				var StatusDiv = (resultJson[i].StatusDiv == 0) ? 'Not Show' : 'Show';		    
				TableGenerate += '<tr>'+
									'<td width = "3%">'+ (parseInt(i) + 1)+'</td>'+
									'<td class = "Division">'+ resultJson[i].Division+'</td>'+
									'<td class = "Description">'+ resultJson[i].Description+'</td>'+
									'<td class = "MenuNavigation" value = "'+resultJson[i].IDMenuNavigation+'">'+resultJson[i].MenuNavigation+'</td>'+
									'<td class = "Email">'+ resultJson[i].Email+'</td>'+
									'<td class = "StatusDiv">'+ StatusDiv+'</td>'+
									'<td class = "Action">'+ '<button type="button" class="btn btn-danger btn-delete" data-sbmt="'+resultJson[i].ID+'"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>'+'</td>'+
								'</tr>';
			}
			TableGenerate += '</tbody></table></div></div></div>'; 		    
			 $("#loadPageTable").html(Mode+DivFormAdd+TableGenerate+SaveBtn);
			 var t = $('#tableData3').DataTable({
			 	"pageLength": 10
			 });

			 FuncAddClickFunction(arr_menu_nav);

    	}).fail(function() {
		  toastr.info('No Result Data'); 
		}).always(function() {
		                
		});								
    }

    function FuncAddClickFunction(arr_menu_nav)
    {
    	$(".btn-add").click(function(){
    		$("#FormAdd").empty();
    		var Thumbnail = '<div class="thumbnail" style="height: 200px;margin-top:10px;margin-left:10px;margin-right:10px"><b>Form Add</b>';
    		var OPMenuNavigation = '<select class = "form-control" id = "AddMenuNavigation">';
    		OPMenuNavigation += '<option value = "" selected>--Choice Menu Navigation--</option>';
    		for (var i = 0; i < arr_menu_nav.length; i++) {
    			OPMenuNavigation += '<option value = "'+arr_menu_nav[i]['IDMenuNavigation']+';'+arr_menu_nav[i]['MenuNavigation']+'" >'+arr_menu_nav[i]['MenuNavigation']+'</option>';

    		}
    		OPMenuNavigation += '</select>';
    		var OPStatusDiv = '<select class = "form-control" id = "AddStatusDiv">'+
    							'<option value = "1" selected>Show</option>'+
    							'<option value = "0">Not Show</option>'+
    						  '</select>';	
    		var Btn = '<div class = "row" style = "margin-left:10px;margin-right:0px;margin-top : 0px">'+
	    						'<div clas = "col-xs-4">'+
	    							'<button type="button" id="btnSaveAdd" class="btn btn-success">Save</button>'+
	    							'&nbsp'+
	    							'<button type="button" id="btnCancelAdd" class="btn btn-danger">Cancel</button>'+
	    						'</div>'+
    						'</div>';
    					;				  	
    		var html = '<div class = "row" style = "margin-left:0px;margin-right:0px;margin-top : 10px">'+
    						'<div class = "form-group">'+
    							'<div class = "col-xs-12">'+
    								'<div class = "row">'+
    									'<div class = "col-xs-3">'+
    										'<label>Division</label>'+
    										'<input type = "text" class = "form-control" id = "addDivision">'+
    									'</div>'+
    									'<div class = "col-xs-3">'+
    										'<label>Description</label>'+
    										'<input type = "text" class = "form-control" id = "addDescription">'+
    									'</div>'+
    									'<div class = "col-xs-3">'+
    										'<label>Menu Navigation</label>'+
    										OPMenuNavigation+
    									'</div>'+
    									'<div class = "col-xs-3">'+
    										'<label>Email</label>'+
    										'<input type = "text" class = "form-control" id = "addEmail">'+
    									'</div>'+
    									'<div class = "col-xs-3">'+
    										'<label>Status Division</label>'+
    										OPStatusDiv+
    									'</div>'+
    								'</div>'+
    							'</div>'+
    						'</div>'+
    					'</div>';
    		var EndThumbnail = '</div>';			
    		$("#FormAdd").html(Thumbnail+html+Btn+EndThumbnail);
    		$("#btnCancelAdd").click(function(){
    			$("#FormAdd").empty();
    		})	

    		$("#btnSaveAdd").click(function(){
                loading_button('#btnSaveAdd');
    			var MenuNavigation = $("#AddMenuNavigation").val();
    			MenuNavigation = MenuNavigation.split(';');
    			var IDMenuNavigation = MenuNavigation[0];
    			MenuNavigation = MenuNavigation[1];
    			var StatusDiv = $("#AddStatusDiv").val();
    			var Division = $("#addDivision").val();
    			var Description = $("#addDescription").val();
    			var Email = $("#addEmail").val();
    			var Action = 'add';
    			var id = '';
    			var url = base_url_js+'it/saveDivision';
    			var SaveForm = {
    				Division:Division,
    				Description:Description,
    				Email : Email,
    				MenuNavigation : MenuNavigation,
    				IDMenuNavigation : IDMenuNavigation,
    			}
    			var data = {
    			    Action : Action,
    			    CDID : id,
    			   SaveForm : SaveForm
    			};
                var token = jwt_encode(data,"UAP)(*");
                if (validationInput = validation(SaveForm)) {
                    $.post(url,{token:token},function (resultJson) {
                        LoadTableData();
                        $('#btnSaveAdd').prop('disabled',false).html('Save');
                    }).fail(function() {
                      toastr.info('Error Processing'); 
                    }).always(function() {
                                    
                    });
                }
                else
                {
                    $('#btnSaveAdd').prop('disabled',false).html('Save');
                } 

    		})						
    	}) // exit add click function

        $(".btn-edit").click(function(){
            var SaveBtn = '<div class = "col-xs-1 col-md-offset-11"> <button type="button" id="btnSaveTable" class="btn btn-success">Save</button></div>';
            $("#rowSaveEdit").html(SaveBtn);
            $(".Division").each(function(){
                var valText = $(this).text();
                var Input = '<input type = "text" class = "form-control textDivision" value = "'+valText+'" >';
                $(this).html(Input);
            })

            $(".Description").each(function(){
                var valText = $(this).text();
                var Input = '<input type = "text" class = "form-control textDescription" value = "'+valText+'" >';
                $(this).html(Input);
            })

            $(".MenuNavigation").each(function(){
                var valText = $(this).text();
                var OPMenuNavigation = '<select class = "form-control textMenuNavigation">';
                for (var i = 0; i < arr_menu_nav.length; i++) {
                    var selected = (arr_menu_nav[i]['MenuNavigation'] == valText) ? 'selected' : '';
                    OPMenuNavigation += '<option value = "'+arr_menu_nav[i]['IDMenuNavigation']+';'+arr_menu_nav[i]['MenuNavigation']+'" '+selected+'>'+arr_menu_nav[i]['MenuNavigation']+'</option>';
                }
                OPMenuNavigation += '</select>';
                $(this).html(OPMenuNavigation);
            })

            $(".Email").each(function(){
                var valText = $(this).text();
                var Input = '<input type = "text" class = "form-control textEmail" value = "'+valText+'" >';
                $(this).html(Input);
            })

            $(".StatusDiv").each(function(){
                var valText = $(this).text();
                console.log(valText);
                var OPStatusDiv = '<select class = "form-control textStatusDiv">';
                    for (var i = 0; i < 2; i++) {
                       if (i == 0) {
                        var selected = (valText == 1) ? 'selected' : '';
                        OPStatusDiv += '<option value = "1" '+selected+'>Show</option>'; 
                        
                       }

                       if (i == 1) {
                            var selected = (valText == 0) ? 'selected' : '';
                            OPStatusDiv += '<option value = "0"'+selected+'>Not Show</option>';
                       }
                    }
                    OPStatusDiv += '</select>';
                $(this).html(OPStatusDiv);
            })

        })
            
    }

}); // exit document Function

function validation(arr)
{
  var toatString = "";
  var result = "";
  for(var key in arr) {
     switch(key)
     {
      default :
            result = Validation_required(arr[key],key);
            if (result['status'] == 0) {
              toatString += result['messages'] + "<br>";
            }
     }

  }
  if (toatString != "") {
    toastr.error(toatString, 'Failed!!');
    return false;
  }

  return true;
}

</script>