<style type="text/css">
	.thumbnail {
	    height: 100px;
	}

	.row {
	    margin-right: 0px;
	    margin-left: 0px;
	}

	#tableData3 thead th,#tableData3 tfoot td {

	    text-align: center;
	    background: #20485A;
	    color: #FFFFFF;

	}

	#tableData3>thead>tr>th, #tableData3>tbody>tr>th, #tableData3>tfoot>tr>th, #tableData3>thead>tr>td, #tableData3>tbody>tr>td, #tableData3>tfoot>tr>td {
	    border: 1px solid #b7b7b7
	}
</style>
<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="thumbnail">
			<div class="col-xs-6">
				<div class="form-group">
					<label>Year</label>
					<select class="select2-select-00 full-width-fix" id="YearPostDepartement">
					     <!-- <option></option> -->
					 </select>
				</div>	
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label>Departement</label>
					<select class="select2-select-00 full-width-fix" id="DepartementPost">
					     <!-- <option></option> -->
					 </select>
				</div>	
			</div>
			<div class="col-xs-10 col-md-offset-3">
				<b>Status : </b><i class="fa fa-circle" style="color:#8ED6EA;"></i> Already Set | <i class="fa fa-circle" style="color: #eade8e;"></i> Unset
			</div>
		</div>
	</div>
</div>
<br>
<div class="row" id = "loadPageTable">

</div>
<script type="text/javascript">
	$(document).ready(function() {
		LoadFirstLoad()
	    
	}); // exit document Function

	function LoadFirstLoad()
	{
		// load Year
		$("#YearPostDepartement").empty();
		var url = base_url_js+'budgeting/table_all/cfg_dateperiod/1';
		var thisYear = (new Date()).getFullYear();
		$.post(url,function (resultJson) {
			var response = jQuery.parseJSON(resultJson);
			for(var i=0;i<response.length;i++){
			    //var selected = (i==0) ? 'selected' : '';
			    var selected = (response[i].Activated==1) ? 'selected' : '';
			    $('#YearPostDepartement').append('<option value="'+response[i].Year+'" '+selected+'>'+response[i].Year+'</option>');
			}
			$('#YearPostDepartement').select2({
			   //allowClear: true
			});

			getAllDepartementPU();

			// get change function
			$("#YearPostDepartement").change(function(){
				loadPageTable();
			})
		}); 
	}

	function getAllDepartementPU()
	{
	  var url = base_url_js+"api/__getAllDepartementPU";
	  $('#DepartementPost').empty();
	  $.post(url,function (data_json) {
	    for (var i = 0; i < data_json.length; i++) {
	        var selected = (i==0) ? 'selected' : '';
	        $('#DepartementPost').append('<option value="'+ data_json[i]['Code']  +'" '+selected+'>'+data_json[i]['Name2']+'</option>');
	    }
	   
	    $('#DepartementPost').select2({
	       //allowClear: true
	    });

	    $("#DepartementPost").change(function(){
	    	loadPageTable();
	    })

	    loadPageTable();

	  })
	}

	function loadPageTable()
	{
		var Year = $("#YearPostDepartement").val();
		// console.log(Year);
		var Departement = $("#DepartementPost").val();
		var url = base_url_js+"budgeting/getDomPostDepartement";

		$("#loadPageTable").empty();

		// var Export = '<div class="col-lg-3 col-md-3 col-xs-4">'+
		// 					'<h4 class="header"><i class="icon-reorder"></i> Export</h4>'+
		// 					'<div class="col-xs-12">'+
		// 						'<button class = "btn btn-default"><i class="fa fa-download" aria-hidden="true"></i> Excel</button>&nbsp'+
		// 						'<button class = "btn btn-default"><i class="fa fa-download" aria-hidden="true"></i> PDF</button>'+
		// 					'</div>'+
		// 			 '</div>';	      

		// var setLastYear = '<div class="col-md-12">'+
		// 						'<div class="thumbnail" style="min-height: 130px;padding: 10px;">'+
		// 							'<div class="col-lg-3 col-md-3 col-xs-4">'+
		//                                 '<h4 class="header"><i class="icon-reorder"></i> Get Budget Last Year</h4>'+
		//                                 '<div class = "col-xs-12"> <br>'+
		//                                       '<button class = "btn btn-success btn-edit" id = "generateBudgetLastYear">Take</button>'+
		//                                 '</div>'+
		//                             '</div>'+
		//                             '<div class="col-lg-3 col-md-3 col-xs-4">'+
		//                                 '<h4 class="header"><i class="icon-reorder"></i> Add</h4>'+
		//                                 '<div class = "col-xs-12">'+
		//                                       '<button class = "btn btn-default btn-add" id = "addRow"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>'+
		//                                 '</div>'+
		//                             '</div>'+      
	 //                                Export+  
  //                               '</div>'+
  //                          '</div>';

  		var setLastYear = '';

		var TableGenerate = '<div class="col-md-12" id = "pageForTable">'+
								'<div class="table-responsive">'+
									'<table class="table table-bordered tableData" id ="tableData3">'+
									'<thead>'+
									'<tr>'+
										'<th width = "3%">No</th>'+
			                            '<th>Departement</th>'+
			                            '<th>Code</th>'+
										'<th>Post Realization</th>'+
										'<th>Year</th>'+
										'<th>Budget</th>'+
										'<th>Action</th>'+
									'</tr></thead>'	
							;
		TableGenerate += '<tbody>';
		var data = {
		    Year : Year,
			Departement : Departement,
		};
		var token = jwt_encode(data,'UAP)(*');
		$.post(url,{token:token},function (resultJson) {
			var response = jQuery.parseJSON(resultJson);
			var dataDB = response['data'];
			var OpPostRealisasi = response['OpPostRealisasi'];
			var SumBudget = 0;

			for (var i = 0; i < dataDB.length; i++) {
				var CodePostBudget = (dataDB[i].CodePostBudget == null) ? 'Unset' : dataDB[i].CodePostBudget;
				var Budget = (dataDB[i].Budget == null) ? '<td class = "Budget'+dataDB[i].CodePostRealisasi+'">'+ 'Unset'+'</td>' : '<td class = "Budget'+CodePostBudget+'">'+ formatRupiah(dataDB[i].Budget)+'</td>';
				var Action = '';
				if(CodePostBudget == 'Unset')
				{
					Action = '<button class = "btn btn-success btn-default getBudgetLastYear" CodePostRealisasi = "'+dataDB[i].CodePostRealisasi+'" trno = "'+(parseInt(i) + 1)+'">Load Budget Last Year</button>&nbsp'+
							 '<button class = "btn btn-default btn-add" CodePostRealisasi = "'+dataDB[i].CodePostRealisasi+'" trno = "'+(parseInt(i) + 1)+'"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>';	
				}
				else
				{
					Action = '<button type="button" class="btn btn-warning btn-edit btn-edit-postbudget" CodePostBudget="'+CodePostBudget+'" trno = "'+(parseInt(i) + 1)+'" budget = "'+dataDB[i].Budget+'"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button>&nbsp <button type="button" class="btn btn-danger btn-delete btn-delete-postbudget" CodePostBudget="'+CodePostBudget+'" trno = "'+(parseInt(i) + 1)+'"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';
				}

				var BG = (CodePostBudget == 'Unset') ? 'style="background-color: #eade8e; color: black;"' : 'style="background-color: #8ED6EA; color: black;"';

				TableGenerate += '<tr '+BG+'>'+
									'<td width = "3%">'+ (parseInt(i) + 1)+'</td>'+
									'<td>'+ $("#DepartementPost").find(":selected").text()+'</td>'+
									'<td>'+ CodePostBudget+'</td>'+
									'<td>'+ dataDB[i].CodePostRealisasi+'<br>'+dataDB[i].PostName+'-'+dataDB[i].RealisasiPostName+'</td>'+
									'<td>'+ $("#YearPostDepartement").find(":selected").text()+'</td>'+
									Budget+
									'<td class = "No'+(parseInt(i) + 1)+'">'+ Action+'</td>'+
								'</tr>';

					SumBudget = parseInt(SumBudget) + ((dataDB[i].Budget == null) ? 0 : 	parseInt(dataDB[i].Budget));			

			}

			TableGenerate += '</tbody></table></div></div>';
			
			$("#loadPageTable").html(setLastYear+'<br>'+TableGenerate);
			SumBudget = formatRupiah(SumBudget);
			$("#pageForTable").append('<div class="col-md-3 col-md-offset-9" style="background-color : #20485A; min-height : 50px;color: #FFFFFF;" align="center"><h4>Total : '+SumBudget+'</h4></div>');
			// LoaddataTableStandard("#tableData1");
			var t = $('#tableData3').DataTable({
				"pageLength": 5
			});
			    // var counter = 1;
			 	
			    // $('#addRow').on( 'click', function () {
			    // 	var No = counter;
			    // 	t
			    // 	    .clear()
			    // 	    .draw();
			    //     t.row.add( [
			    //         1,
			    //         $("#DepartementPost").find(":selected").text(),
			    //         'Automatic after submit',
			    //         counter +'.4',
			    //         $("#Year").find(":selected").text(),
			    //         $("#Year").find(":selected").text(),
			    //         counter +'.7',
			    //     ] ).draw( false );
			 
			    //     counter++;
			    // } );
			 
			    // Automatically add a first row of data
			    // $('#addRow').click();


			EventButtonAction();			    

		}).fail(function() {
		  toastr.info('No Result Data'); 
		}).always(function() {
		                
		});	
		
	}

	function EventButtonAction()
	{
		$('#tableData3 tbody').on('click', '.getBudgetLastYear', function () {
			var CodePostRealisasi = $(this).attr('CodePostRealisasi');
			var trno = $(this).attr('trno');
			if (confirm("Are you sure?") == true) {
				loadingStart();
				var url =base_url_js+'budgeting/getBudgetLastYearByCode';
				var data = {
				          CodePostRealisasi : CodePostRealisasi,
				          Year : $("#YearPostDepartement").val(),
				      };
				var token = jwt_encode(data,'UAP)(*');
				$.post(url,{token:token},function (data_json) {
					var response = jQuery.parseJSON(data_json);
					if(response.length > 0)
					{
						var input = '<input type = "text" class = "form-control BudgetInput'+CodePostRealisasi+'">';
						var Cost = response[0]['Budget'];
				         var n = Cost.indexOf(".");
				         var Cost = Cost.substring(0, n);

						var input = '<input type = "text" class = "form-control BudgetInput'+CodePostRealisasi+'">';
						$('.Budget'+CodePostRealisasi).html(input);
						$('.BudgetInput'+CodePostRealisasi).val(Cost);
						$('.BudgetInput'+CodePostRealisasi).maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
						$('.BudgetInput'+CodePostRealisasi).maskMoney('mask', '9894');

						var ActionSave = '<button class="btn btn-primary btn-save'+CodePostRealisasi+'"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>';
						$(".No"+trno).html(ActionSave);
						$('.BudgetInput'+CodePostRealisasi).focus(); 

						$(".btn-save"+CodePostRealisasi).click(function(){
							var getBudget = $('.BudgetInput'+CodePostRealisasi).val();
							for(i = 0; i <getBudget.length; i++) {
							 
							 getBudget = getBudget.replace(".", "");
							 
							}

							var Year = $("#YearPostDepartement").val();
							if (confirm("Are you sure?") == true) {
								loadingStart();
								var url =base_url_js+'budgeting/save-setpostdepartement';
								var data = {
								          CodeSubPost : CodePostRealisasi,
								          Year : Year,
								          Budget : getBudget,
								          Action : 'add'
								      };
								var token = jwt_encode(data,'UAP)(*');
								$.post(url,{token:token},function (data_json) {
									var response = jQuery.parseJSON(data_json);
									if (response == '') {
									    toastr.success('Data berhasil disimpan', 'Success!');
									}
									else
									{
									    toastr.error(response, 'Failed!!');
									}
							        loadPageTable();
									loadingEnd(500)
								});
							}
							else
							{
								loadPageTable();
							}
						})
					} // if response length
					else
					{
						toastr.info('The data last year unavailable');
					}
					loadingEnd(500)
				});
			}
			else
			{
				loadPageTable();
			}	
		});

		$('#tableData3 tbody').on('click', '.btn-delete-postbudget', function () {
			var CodePostBudget = $(this).attr('CodePostBudget');
			if (confirm("Are you sure?") == true) {
				loadingStart();
				var url =base_url_js+'budgeting/save-setpostdepartement';
				var data = {
				          CodePostBudget : CodePostBudget,
				          Action : 'delete'
				      };
				var token = jwt_encode(data,'UAP)(*');
				$.post(url,{token:token},function (data_json) {
					var response = jQuery.parseJSON(data_json);
					if (response == '') {
					    toastr.success('Data berhasil disimpan', 'Success!');
					}
					else
					{
					    toastr.error(response, 'Failed!!');
					}
			        loadPageTable();
					loadingEnd(500)
				});
			}
			else
			{
				loadPageTable();
			}
			
		});	

		$('#tableData3 tbody').on('click', '.btn-add', function () {
			var codepostrealisasi = $(this).attr('codepostrealisasi');
			var input = '<input type = "text" class = "form-control BudgetInput'+codepostrealisasi+'">';
			$('.Budget'+codepostrealisasi).html(input);
			$('.BudgetInput'+codepostrealisasi).maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
			var trno = $(this).attr('trno');
			var ActionSave = '<button class="btn btn-primary btn-save-add"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>';
			$(".No"+trno).html(ActionSave);
			$('.BudgetInput'+codepostrealisasi).focus();
			$(".btn-save-add").click(function(){
				var getBudget = $('.BudgetInput'+codepostrealisasi).val();
				for(i = 0; i <getBudget.length; i++) {
				 
				 getBudget = getBudget.replace(".", "");
				 
				}

				var Year = $("#YearPostDepartement").val();
				if (confirm("Are you sure?") == true) {
					loadingStart();
					var url =base_url_js+'budgeting/save-setpostdepartement';
					var data = {
					          CodeSubPost : codepostrealisasi,
					          Year : Year,
					          Budget : getBudget,
					          Action : 'add'
					      };
					var token = jwt_encode(data,'UAP)(*');
					$.post(url,{token:token},function (data_json) {
						var response = jQuery.parseJSON(data_json);
						if (response == '') {
						    toastr.success('Data berhasil disimpan', 'Success!');
						}
						else
						{
						    toastr.error(response, 'Failed!!');
						}
				        loadPageTable();
						loadingEnd(500)
					});
				}
				else
				{
					loadPageTable();
				}
			})

		});


		$('#tableData3 tbody').on('click', '.btn-edit-postbudget', function () {
			var codepostbudget = $(this).attr('codepostbudget');
			var Cost = $(this).attr('budget');
	         var n = Cost.indexOf(".");
	         var Cost = Cost.substring(0, n);

			var input = '<input type = "text" class = "form-control BudgetInput'+codepostbudget+'">';
			$('.Budget'+codepostbudget).html(input);
			$('.BudgetInput'+codepostbudget).val(Cost);
			$('.BudgetInput'+codepostbudget).maskMoney({thousands:'.', decimal:',', precision:0,allowZero: true});
			$('.BudgetInput'+codepostbudget).maskMoney('mask', '9894');

			var trno = $(this).attr('trno');
			var ActionSave = '<button class="btn btn-primary btn-save'+codepostbudget+'"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>';
			$(".No"+trno).html(ActionSave);
			$('.BudgetInput'+codepostbudget).focus(); 

			$(".btn-save"+codepostbudget).click(function(){
				var getBudget = $('.BudgetInput'+codepostbudget).val();
				for(i = 0; i <getBudget.length; i++) {
				 
				 getBudget = getBudget.replace(".", "");
				 
				}

				var Year = $("#YearPostDepartement").val();
				if (confirm("Are you sure?") == true) {
					loadingStart();
					var url =base_url_js+'budgeting/save-setpostdepartement';
					var data = {
					          CodePostBudget : codepostbudget,
					          Year : Year,
					          Budget : getBudget,
					          Action : 'edit'
					      };
					var token = jwt_encode(data,'UAP)(*');
					$.post(url,{token:token},function (data_json) {
						var response = jQuery.parseJSON(data_json);
						if (response == '') {
						    toastr.success('Data berhasil disimpan', 'Success!');
						}
						else
						{
						    toastr.error(response, 'Failed!!');
						}
				        loadPageTable();
						loadingEnd(500)
					});
				}
				else
				{
					loadPageTable();
				}
			})

		});
		
	}
</script>
