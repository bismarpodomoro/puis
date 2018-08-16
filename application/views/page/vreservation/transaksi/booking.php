<div class = 'row'>
	<!--=== Calendar ===-->
	<div class="col-md-12">
		<div class="widget">
			<div class="widget-header">
					<h4><i class="icon-calendar"></i> Schedule</h4>
				</div>
			<div class="widget-content">
				<div class = "row">	
					<div class="col-xs-3">
						<div id="datetimepicker1" class="input-group input-append date datetimepicker">
								<input data-format="yyyy-MM-dd" class="form-control" id="datetime_deadline1" type="	text" readonly="" >
								<span class="input-group-addon add-on"><i data-time-icon="icon-time" data-date-icon="icon-calendar" class="icon-calendar"></i></span>
						</div>
					</div>
					<div class="col-xs-3">
					<button class="btn btn-success" id = "search"><span class="glyphicon glyphicon-search"></span> Search</button>
					</div>
				</div>
				<br>
				<!-- <div class = "row">	 -->
					<div id="schedule"></div>
				<!-- </div> -->
			</div>
		</div> <!-- /.widget box -->
	</div> <!-- /.col-md-6 -->
	<!-- /Calendar -->
</div>	

<script type="text/javascript">
	$(document).ready(function(){
    // var url = base_url_js+'api/__cek_deadline_paymentNPM';
    // var data = {
    //     NPM : '12140015'
    // };
    // var token = jwt_encode(data,"UAP)(*");
    // $.post(url,{ token:token },function (data_json) {
    //     console.log(data_json);
    // });         

		var divHtml = $("#schedule");
		loadDataSchedule(divHtml);

		Date.prototype.addDays = function(days) {
	          var date = new Date(this.valueOf());
	          date.setDate(date.getDate() + days);
	          return date;
	    }
          var date = new Date();

		$('#datetimepicker1').datetimepicker({
			format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
		 startDate: date.addDays(0),
		});

		$('#datetime_deadline1').prop('readonly',true);
	});

  $(document).on('click','#search', function () {
    var get = $('#datetime_deadline1').val();
    var divHtml = $("#schedule");
    loadDataSchedule(divHtml,get);
  });

  

  $(document).on('click','#ModalbtnSaveForm', function () {
    loading_button('#ModalbtnSaveForm');
    var Room = $("#Room").val();
    var Start = $("#Start").val();
    var End = $("#End").val();
    var Agenda = $("#Agenda").val();
    var chk_e_additional = '';
    if ($('#e_additionalYA').is(':checked')) {
      var chk_e_additional = [];
      $('.chke_additional').each(function() {
         if ($(this).is(':checked')) {
            var valuee = $(this).val();
            var Qty = $(".chke_additional"+valuee).val();
            var eeArr = {
              ID_equipment_add : valuee,
              Qty : Qty
            };
            chk_e_additional.push(eeArr);
         }
      });
    }

    // console.log(chk_e_additional);

    var chk_person_support = '';
    if ($('#person_supportYA').is(':checked')) {
      var chk_person_support = [];
      $('.chk_person_support_td').each(function() {
         if ($(this).is(':checked')) {
            var valuee = $(this).val();
            chk_person_support.push(valuee);
         }
      });
    }

     // console.log(chk_person_support);

    // var chk_e_multiple = '';
    // if ($('#multipleYA').is(':checked')) {
    //   var chk_e_multiple = [];
    //   $('.datetime_deadlineMulti').each(function() {
    //      var valuee = $(this).val();
    //      chk_e_multiple.push(valuee);
    //   });
    // }

   var data = {
       Room : Room,
       Start : Start,
       End : End,
       Agenda : Agenda,
       chk_e_additional : chk_e_additional,
       chk_person_support : chk_person_support,
       //chk_e_multiple : chk_e_multiple,
       file : file_validation(),
       date : $('#datetime_deadline1').val(),
       Participant : $("#Participant").val(),
   };

   console.log(data);

   if (validationInput = validationModal(data)) {
          var form_data = new FormData();
          var fileData = document.getElementById("ExFile").files[0];
          var url = base_url_js + "vreservation/add_save_transaksi"
          var token = jwt_encode(data,"UAP)(*");
          form_data.append('token',token);
          form_data.append('fileData',fileData);
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
                // toastr.options.fadeOut = 100000;
                toastr.success(data.msg, 'Success!');
                var divHtml = $("#schedule");
                loadDataSchedule(divHtml);
                $('#GlobalModalLarge').modal('hide');

                // send notification other school from client
                var socket = io.connect( 'http://'+window.location.hostname+':3000' );
                // var socket = io.connect( '<?php echo serverRoot ?>'+':3000' );
                  socket.emit('update_schedule_notifikasi', { 
                    update_schedule_notifikasi: '1',
                    date : $('#datetime_deadline1').val(),
                  });

              }
              else
              {
                // toastr.options.fadeOut = 100000;
                toastr.error(data.msg, 'Failed!!');
              }
              $('#ModalbtnSaveForm').prop('disabled',false).html('Save');

            },
            error: function (data) {
              toastr.error("Connection Error, Please try again", 'Error!!');
              $('#ModalbtnSaveForm').prop('disabled',false).html('Save');
            }
          })
   }
   else
   {
    $('#ModalbtnSaveForm').prop('disabled',false).html('Save');
   }

  });

  function file_validation()
  {
    try{
      var name = document.getElementById("ExFile").files[0].name;
      var ext = name.split('.').pop().toLowerCase();
      if(jQuery.inArray(ext, ['pdf','PDF']) == -1) 
      {
        toastr.error("Invalid File", 'Failed!!');
        return false;
      }
      var oFReader = new FileReader();
      oFReader.readAsDataURL(document.getElementById("ExFile").files[0]);
      var f = document.getElementById("ExFile").files[0];
      var fsize = f.size||f.fileSize;
      if(fsize > 2000000) // 2mb
      {
       toastr.error("Image File Size is very big", 'Failed!!');
       return false;
      }

    }
    catch(err)
    {
      // return false;
    }
      return true;
  }

  function validationModal(arr)
  {
    var toatString = "";
    var result = "";
    for(var key in arr) {
       switch(key)
       {
        case  "Room" :
        case  "Start" :
        case  "End" :
        case  "Agenda" :
              result = Validation_required(arr[key],key);
              if (result['status'] == 0) {
                toatString += result['messages'] + "<br>";
              }
              break;
        case  "file" :
              if (!arr[key]) {
                toatString += 'Invalid File' + "<br>";
              }
              break;
        case  "chk_e_additional" :
              if ($('#e_additionalYA').is(':checked')) {
                // check lenght lebih dari satu
                var aa = arr[key];
                if (aa.length == 0) {
                  toatString += 'Please check Equipment Additional' + "<br>";
                }
              }
              else
              {
                if($("#e_additionalTDK"). prop("checked") == false){
                  toatString += 'Please Choices Equipment Additional' + "<br>";
                }
              }
              break;
        case  "chk_person_support" :
              if ($('#person_supportYA').is(':checked')) {
                // check lenght lebih dari satu
                var aa = arr[key];
                if (aa.length == 0) {
                  toatString += 'Please check Person Support' + "<br>";
                }
              }
              else
              {
                if($("#person_supportTDK"). prop("checked") == false){
                  toatString += 'Please Choices Person Support' + "<br>";
                }
              }
              break;
        case  "chk_e_multiple" :
              if ($('#multipleYA').is(':checked')) {
                if ($("#countDays").val() == '') {
                  toatString += 'Please choices Days' + "<br>";
                }
                else
                {
                  // check lenght lebih dari satu
                  var ab = $("#countDays").val();
                  var aa = arr[key];
                  var bool = true;
                  for (var i = 0; i < aa.length; i++) {
                    if (aa[i] == '') {
                      bool = false;
                    }
                  }
                  if (!bool) {
                    toatString += 'Please check Multiple Days' + "<br>";
                  }
                }
              }
              else
              {
                if($("#e_multipleTDK"). prop("checked") == false){
                  toatString += 'Please Choices Multiple Days' + "<br>";
                }
              }
              break;            
       }

    }
    if (toatString != "") {
      toastr.error(toatString, 'Failed!!');
      return false;
    }

    return true;
  }

	$(document).on('click','.panel-blue', function () {
		var room = $(this).attr('room');
		var time =  $(this).attr('title');
		var tgl = $("#datetime_deadline1").val();
		modal_generate('add','Form Booking Reservation',room,time,tgl);
  });

    $(document).on('click','.chk_e_additional', function () {
        $('input.chk_e_additional').prop('checked', false);
        $(this).prop('checked',true);
    });

    $(document).on('click','.chk_person_support', function () {
        $('input.chk_person_support').prop('checked', false);
        $(this).prop('checked',true);
    });

    $(document).on('click','.chk_e_multiple', function () {
        $('input.chk_e_multiple').prop('checked', false);
        $(this).prop('checked',true);
    });


    // event ya multiple belum
    $(document).on('change','#e_multipleTDK', function () {
        if(this.checked) {
            $('#divE_multiple').remove();
            $('.divPageSelect').remove();
        }
    });

    $(document).on('change','#countDays', function () {
        getCountDays = $(this).val();
        $(".divPageSelect").remove();
        var input = '<div class="form-group col-md-6 divPageSelect"><br>';
        for (var i = 0; i < getCountDays; i++) {
           input += '<div id="datetimepickerMulti'+i+'" class="input-group input-append date datetimepicker">'+
                                       '<input data-format="yyyy-MM-dd hh:mm:ss" class="form-control datetime_deadlineMulti" id="datetime_deadlineMulti'+i+'" type="text"></input>'+
                                       '<span class="input-group-addon add-on">'+
                                         '<i data-time-icon="icon-time" data-date-icon="icon-calendar">'+
                                         '</i>'+
                                       '</span>'+
                                   '</div><br>';
         }
         input += '</div>';
         $('#divE_multiple').after(input); 

          Date.prototype.addDays = function(days) {
            var date = new Date(this.valueOf());
            date.setDate(date.getDate() + days);
            return date;
          }
          var date = new Date();

         for (var i = 0 ; i < getCountDays; i++) {
            $('#datetimepickerMulti'+i).datetimepicker({
             // startDate: today,
             // startDate: '+2d',
             // startDate: date.addDays(i),
              format: 'yyyy-MM-dd',autoclose: true, minView: 2,pickTime: false,
              startDate: date.addDays(1),
            });

            $('#datetime_deadlineMulti'+i).prop('readonly',true);
         }

    });

    $(document).on('change','#multipleYA', function () {
        if(this.checked) {
            //equipment_additional = [];
            $('#divE_multiple').remove();
            var sss = '<select class = "full-width-fix" id = "countDays">'+
                      '<option value = "'+''+'">'+'--Select--'+'</option>';
            for (var l = 1; l <= 5; l++) {
                sss += ' <option value = "'+l+'">'+l+'</option>'
            }

            sss += '</select>';
            var divE_multiple = '<div class="col-md-6" id="divE_multiple"><strong>Choices Days</strong></div>';
            $('#multiplePage').after(divE_multiple);      
            $('#divE_multiple').append(sss);
        }

    });

    $(document).on('change','#e_additionalYA', function () {
        if(this.checked) {
            //equipment_additional = [];
            $('#divE_additional').remove();
            // get data m_equipment_additional
            var url = base_url_js+"api/__m_equipment_additional";
            $.post(url,function (data_json) {
              var response = data_json;
              console.log(response);
              var splitBagi = 2;
              var split = parseInt(response.length / splitBagi);
              var sisa = response.length % splitBagi;
              
              if (sisa > 0) {
                    split++;
              }
              var getRow = 0;
              var divE_additional = '<div class="col-md-6" id="divE_additional" style="width: 500px;"><strong>Choices Equipment Additional</strong></div>';
              $('#e_additional').after(divE_additional);
              $('#divE_additional').append('<table class="table" id ="tablechk_e_additional">');
              for (var i = 0; i < split; i++) {
                if ((sisa > 0) && ((i + 1) == split) ) {
                                    splitBagi = sisa;    
                }
                $('#tablechk_e_additional').append('<tr id = "a'+i+'">');
                for (var k = 0; k < splitBagi; k++) {
                    $('#a'+i).append('<td>'+
                                        '<input type="checkbox" class = "chke_additional" name="chke_additional" value = "'+response[getRow].ID_add+'">&nbsp'+ response[getRow].Equipment+' By '+response[getRow].Division+
                                     '</td>'+
                                     '<td>'+
                                        ' <input type="number" class="form-control chke_additional'+response[getRow].ID_add+' hide"  value="1" id = "chke_additional'+response[getRow].ID_add+'">'+'</td>'
                                    );
                    getRow++;
                }
                $('#a'+i).append('</tr>');
              }
              $('#tablechk_e_additional').append('</table>');
            }).done(function () {
              //loadAlamatSekolah();
            });
        }

    });

    $(document).on('change','#person_supportTDK', function () {
        if(this.checked) {
            $('#divperson_support').remove();
        }
    });

    $(document).on('change','.chke_additional', function () {
      var aa = $(this).val();
        if(this.checked) {
            $('#chke_additional'+aa).removeClass('hide');
        }
        else
        {
          $('#chke_additional'+aa).addClass('hide');
        }

    });

    $(document).on('change','#e_additionalTDK', function () {
        if(this.checked) {
            $('#divE_additional').remove();
        }

    });

    $(document).on('change','#person_supportYA', function () {
        if(this.checked) {
            //equipment_additional = [];
            $('#divperson_support').remove();
            // get data m_equipment_additional
            var url = base_url_js+"api/__m_additional_personel";
            $.post(url,function (data_json) {
              var response = data_json;
              var splitBagi = 3;
              var split = parseInt(response.length / splitBagi);
              var sisa = response.length % splitBagi;
              
              if (sisa > 0) {
                    split++;
              }
              var getRow = 0;
              var divE_additional = '<div class="col-md-6" id="divperson_support"><strong>Choices Person Support</strong></div>';
              $('#person_support').after(divE_additional);
              $('#divperson_support').append('<table class="table" id ="tablechk_divperson_support">');
              for (var i = 0; i < split; i++) {
                if ((sisa > 0) && ((i + 1) == split) ) {
                                    splitBagi = sisa;    
                }
                $('#tablechk_divperson_support').append('<tr id = "psa'+i+'">');
                for (var k = 0; k < splitBagi; k++) {
                    $('#psa'+i).append('<td>'+
                                        '<input type="checkbox" class = "chk_person_support_td" name="chk_person_support_td" value = "'+response[getRow].ID+'">&nbsp'+ response[getRow].Division+
                                     '</td>'
                                    );
                    getRow++;
                }
                $('#psa'+i).append('</tr>');
              }
              $('#tablechk_divperson_support').append('</table>');
            }).done(function () {
              //loadAlamatSekolah();
            });
        }

    });

</script>
