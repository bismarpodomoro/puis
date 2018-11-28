<div class="row">
    <div class="col-xs-12" >
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Import Pembayaran Manual Mahasiswa</h4>
            </div>
            <div class="panel-body">
               <div class="form-horizontal">
               	<div class="form-group">
               		<div class="col-xs-12">
               			<div class="thumbnail" style="padding: 10px;">
               			    <b>Note : </b>
               			    <p><b>Data yang diimport ke system adalah data pembayaran yang telah lunas</b> </p> 
               			</div>
               		</div>
               	</div>	
               	<div class="form-group">
               			<div class="col-xs-2">
               		        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
               		            <select class="form-control" id="selectPTID">
               		                <option selected value = ''>--- Payment Type ---</option>
               		                <option disabled>------</option>
               		            </select>
               		        </div>
               			</div>
               			<div class="col-xs-2">
               		        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
               		            <select class="form-control" id="selectSemester">
               		                <option selected value = ''>--- Semester Type ---</option>
               		                <option disabled>------</option>
               		            </select>
               		        </div>
               			</div>
               			<div class="col-xs-1" style="">
               				<label class="control-label">Upload File:</label>
               				<input type="file" data-style="fileinput" id="ExFile">
               			</div>
               			<div class="col-xs-1">
               				<label class="control-label"><a href="<?php echo base_url('download_template/finance-Template_import_pembayaran_manual.xlsx'); ?>">Template</a></label>
               			</div>	
               			<!-- <div class="col-xs-2">
               				<button class="btn btn-inverse btn-notification" id="btn-proses">Proses</button>
               			</div> -->
               		</div>
               		<div class="form-group">
               			<div class="form-check col-xs-2">
               			    <input type="checkbox" class="form-check-input" id="maba">
               			    <label class="form-check-label" for="exampleCheck1">Mahasiswa Baru</label>
               			</div>
               		</div>
               		<div class="form-group">
               			<div class="form-check col-xs-2">
               				<button class="btn btn-inverse btn-notification" id="btn-proses">Proses</button>
               			</div>
               		</div>
               	<!-- </div> -->
               </div>            
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
	    loadSelectOptionPaymentTypeAll('#selectPTID','');
	    loadSelectOptionSemester('#selectSemester','');
	});

	$(document).on('click','#btn-proses', function () {
		loading_button('#btn-proses');
	  if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
	  		toastr.error('The File APIs are not fully supported in this browser.', 'Failed!!');
	  		$('#btn-proses').prop('disabled',false).html('Proses');  
	        return;
	      }   

	      input = document.getElementById('ExFile');
	      if (!input) {
	        toastr.error('Um, couldnot find the fileinput element.', 'Failed!!');
	        $('#btn-proses').prop('disabled',false).html('Proses');  
	      }
	      else if (!input.files) {
	        toastr.error('This browser doesnot seem to support the `files', 'Failed!!');
	        $('#btn-proses').prop('disabled',false).html('Proses');  
	      }
	      else if (!input.files[0]) {
	        toastr.error('Please select a file before clicking Proses', 'Failed!!');
	        $('#btn-proses').prop('disabled',false).html('Proses');  
	      }
	      else {
	        /*file = input.files[0];
	        fr = new FileReader();
	        fr.onload = receivedText;
          	fr.readAsText(file);
          	//fr.readAsDataURL(file);*/
          	processFile();

	      }
	     
	});

	function validation(arr)
	{
	  var toatString = "";
	  var result = "";
	  for(var key in arr) {
	     switch(key)
	     {
	      case  "Action" :
	      case  "CDID" :
	            break;
	      case  "Cost" :
	            result = Validation_required(arr[key],key);
	            if (result['status'] == 0) {
	              toatString += result['messages'] + "<br>";
	            }
	            break;
	      default:
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

	function processFile()
	{
		var PTID = $("#selectPTID").val();
		var selectSemester = $("#selectSemester").val();
		var data = {
			PTID : PTID,
			selectSemester : selectSemester
		}

		if (validationInput = validation(data)) {
			var form_data = new FormData();
			var fileData = document.getElementById("ExFile").files[0];
			var url = base_url_js + "finance/tagihan-mhs/submit_import_pembayaran_manual";
			var selectKategory = $("#selectKategory").val();
			if ($('#maba').is(':checked')) {
				var maba = 1;
			}
			else
			{
				var maba = 0;
			}

			form_data.append('fileData',fileData);
			form_data.append('selectPTID',PTID);
			form_data.append('selectSemester',selectSemester);
			form_data.append('maba',maba);
		  	$.ajax({
		  	  type:"POST",
		  	  url:url,
		  	  data: form_data, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
		  	  contentType: false,       // The content type used when sending data to the server.
		  	  cache: false,             // To unable request pages to be cached
		  	  processData:false,
		  	  dataType: "json",
		  	  success:function(data)
		  	  {
		  	    if(data.status == 1) {
		  	    	toastr.options.fadeOut = 100000;
		  	    	toastr.success(data.msg, 'Success!');
		  	    }
		  	    else
		  	    {
		  	    	toastr.options.fadeOut = 100000;
		  	    	toastr.error(data.msg, 'Failed!!');
		  	    }
		    	setTimeout(function () {
		         toastr.clear();
		     	},1000);
		    	$('#btn-proses').prop('disabled',false).html('Proses');
		 		
		  	  },
		  	  error: function (data) {
		  	    toastr.error("Connection Error, Please try again", 'Error!!');
		  	    $('#btn-proses').prop('disabled',false).html('Proses');  
		  	  }
		  	})
		}
		else
		{
			$('#btn-proses').prop('disabled',false).html('Proses'); 
		}
		
	}

</script>
 