<link href="<?php echo base_url('assets/custom/jquery-ui.css'); ?>" rel="stylesheet" type="text/css" />
<div class="row" style="margin-top: 5px;">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4 class="header"><i class="icon-reorder"></i><?php echo $NameMenu ?></h4>
                <div class="toolbar no-padding">
                    <div class="btn-group">
                      <span data-smt="" class="btn btn-xs btn-add-event btn-add">
                        <i class="icon-plus"></i> Add
                       </span>
                    </div>
                </div>
            </div>
            <div class="widget-content">
                <div class = 'row'>
                  <div class="col-md-12">
                    <div id= "loadtableMenu"></div>
                  </div>
                </div>
                <!-- -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        loadTableEvent(loadDataEvent);
    });

   $(document).on('click','.btn-add-event', function () {
      modal_generate('add','Add Event');
   });

   function modal_generate(action,title,ID='') {
       var url = base_url_js+"admission/master-registration/modalform_event";
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
       })
   }

   $(document).on('click','#ModalbtnSaveForm', function () {
       // $.removeCookie('__tawkuuid', { path: '/' });
       loading_button('#ModalbtnSaveForm');
       var url = base_url_js+'admission/master-registration/modalform_event/save';
       var price_event = $("#price_event").val().trim();
       var name_event = $("#name_event").val().trim();
       var action = $(this).attr('action');
       var id = $("#ModalbtnSaveForm").attr('kodeuniq');
       var data = {
                   evn_price : price_event,
                   evn_name : name_event,
                   Action : action,
                   CDID : id
                   };
       var token = jwt_encode(data,"UAP)(*");
       if (validation2(data)) {
           $.post(url,{token:token},function (data_json) {
               // jsonData = data_json;
               // var obj = JSON.parse(data_json); 
               // console.log(obj);
               $('#GlobalModal').modal('hide');
           }).done(function() {
             loadTableEvent(loadDataEvent);
           }).fail(function() {
             toastr.error('The Database connection error, please try again', 'Failed!!');
           }).always(function() {
            $('#ModalbtnSaveForm').prop('disabled',false).html('Save');

           });
       }
       else
       {
           $('#ModalbtnSaveForm').prop('disabled',false).html('Save');
       }          
       
   });


   function validation2(arr)
   {
     var toatString = "";
     var result = "";
     for(var key in arr) {
        switch(key)
        {
         case  "evn_price" :
               result = Validation_numeric(arr[key],key);
               if (result['status'] == 0) {
                 toatString += result['messages'] + "<br>";
               }
               break;
         case  "evn_name" :
               result = Validation_required(arr[key],key);
               if (result['status'] == 0) {
                 toatString += result['messages'] + "<br>";
               }
               break;
        }

     }
     if (toatString != "") {
       // toastr.error(toatString, 'Failed!!');
       $("#msgMENU").html(toatString);
       $("#msgMENU").removeClass("hide");
       return false;
     }

     return true;
   }

   $(document).on('click','.btn-edit', function () {
     var ID = $(this).attr('data-smt');
      modal_generate('edit','Edit Event',ID);
   });

   $(document).on('click','.btn-delete', function () {
     var ID = $(this).attr('data-smt');
      $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Apakah anda yakin untuk melakukan request ini ?? </b> ' +
          '<button type="button" id="confirmYesDelete" class="btn btn-primary" style="margin-right: 5px;" data-smt = "'+ID+'">Yes</button>' +
          '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
          '</div>');
      $('#NotificationModal').modal('show');
   });

   $(document).on('click','.btn-Active', function () {
     var ID = $(this).attr('data-smt');
     var Active = $(this).attr('data-active');
      $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Apakah anda yakin untuk melakukan request ini ?? </b> ' +
          '<button type="button" id="confirmYesActive" class="btn btn-primary" style="margin-right: 5px;" data-smt = "'+ID+'" data-active = "'+Active+'">Yes</button>' +
          '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
          '</div>');
      $('#NotificationModal').modal('show');
   });

   $(document).on('click','#confirmYesDelete',function () {
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
         var url = base_url_js+'admission/master-registration/modalform_event/save';
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
                loadTableEvent(loadDataEvent);
                $('#NotificationModal').modal('hide');
             },500);
         });
   });

   $(document).on('click','#confirmYesActive',function () {
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
         var url = base_url_js+'admission/master-registration/modalform_event/save';
         var aksi = "getactive";
         var ID = $(this).attr('data-smt');
         var Active = $(this).attr('data-active');
         var data = {
             Action : aksi,
             CDID : ID,
             Active:Active,
         };
         var token = jwt_encode(data,"UAP)(*");
         $.post(url,{token:token},function (data_json) {
             setTimeout(function () {
                toastr.options.fadeOut = 10000;
                toastr.success('Data berhasil disimpan', 'Success!');
                loadTableEvent(loadDataEvent);
                $('#NotificationModal').modal('hide');
             },500);
         });
   });

   function loadTableEvent(callback)
   {
       // Some code
       // console.log('test');
       $("#loadtableMenu").empty();
       var table = '<div class = "table-responsive"><table class="table table-striped table-bordered table-hover table-checkable datatable" id ="EventTbl">'+
       '<thead>'+
           '<tr>'+
               '<th style="width: 106px;">No</th>'+
               '<th style="width: 106px;">Event</th>'+
               '<th style="width: 106px;">Price</th>'+
               '<th style="width: 106px;">Status</th>'+
               '<th style="width: 15px;">Created</th>'+
               '<th style="width: 15px;">Updated</th>'+
               '<th style="width: 15px;">Action</th>'+
           '</tr>'+
       '</thead>'+
       '<tbody>'+
       '</tbody>'+
       '</table></div>';
       //$("#loadtableNow").empty();
       $("#loadtableMenu").html(table);

       /*if (typeof callback === 'function') { 
           callback(); 
       }*/
       callback();
   }

   function loadDataEvent()
   {
       var url = base_url_js+'admission/master-registration/event/table_event';
   // loading_page('#loadtableNow');
       $.post(url,function (data_json) {
           var response = jQuery.parseJSON(data_json);
           // console.log(response);
           // $("#loadingProcess").remove();
           var no = 1;
           for (var i = 0; i < response.length; i++) {
                var btn_edit = '<span data-smt="'+response[i]['ID']+'" class="btn btn-xs btn-edit"><i class="fa fa-pencil-square-o"></i> Edit</span>';
                var btn_delete = '<span data-smt="'+response[i]['ID']+'"               class="btn btn-xs btn-delete"><i class="fa fa-trash"> Delete</i></span>';
                var btn_status = '<span data-smt="'+response[i]['ID']+'" class="btn btn-xs btn-Active" data-active = "'+response[i]['Active']+'"><i class="fa fa-hand-o-right"> Change Active</i></span>';
                var status = '';
                if(response[i]['Active'] == 0)
                {
                  status = '<i class="fa fa-minus-circle" style="color: red;"></i>';
                }
                else
                {
                  status = '<i class="fa fa-check-circle" style="color: green;"></i>';
                }
               $("#EventTbl tbody").append(
                   '<tr>'+
                       '<td>'+no+'</td>'+
                       '<td>'+response[i]['evn_name']+'</td>'+
                       '<td>'+response[i]['evn_price']+'</td>'+
                       '<td>'+status+'</td>'+
                       '<td>'+response[i]['Name']+'</td>'+
                       '<td>'+response[i]['name_updated']+'</td>'+
                       '<td><div class="btn-group">'+btn_edit+btn_status+btn_delete+'</div></td>'+
                   '</tr>' 
                   );
               no++;
           }
       }).done(function() {
           LoaddataTableStandard('#EventTbl');
       })
   }
</script>
