
<style type="text/css">
  .btn-submit{
    background-color: #1ace37;
  }
</style>
<div class="row" style="margin-top: 30px;">
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectCurriculum">
                <option selected value = ''>--- All Curriculum ---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectProdi">
                <option selected value = ''>--- All Prodi---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <input type="text" name="" class="form-control" placeholder="Input NPM Mahasiswa" id = "NIM">
        </div>
    </div>
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectPTID">
                <option selected value = ''>--- All Payment Type ---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <hr/>
        <table class="table table-bordered datatable2 hide" id = "datatable2">
            <thead>
            <tr style="background: #333;color: #fff;">
                <th style="width: 3%;"><input type="checkbox" class="uniform" value="nothing" id ="dataResultCheckAll"></th>
                <th style="width: 12%;">Program Study</th>
                <th style="width: 10%;">Semester</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 5%;">NPM</th>
                <th style="width: 15%;">Payment Type</th>
                <th style="width: 15%;">Email PU</th>
                <th style="width: 10%;">Discount</th>
                <th style="width: 10%;">Invoice</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Detail Payment</th>
            </tr>
            </thead>
            <tbody id="dataRow"></tbody>
        </table>
    </div>
    <div  class="col-xs-12" align="right" id="pagination_link"></div>
    <div  class="col-xs-12" align="right"><button class="btn btn-inverse btn-notification btn-submi-unapprove hide" id="btn-submit-unapprove">Unapprove</button>&nbsp<button class="btn btn-inverse btn-notification btn-submit hide" id="btn-submit">Approve</button></div>
</div>


<script>
    window.dataa = '';
    window.dataaModal = '';
    $(document).ready(function () {
        loadData(1);
        loadSelectOptionCurriculum('#selectCurriculum','');
        loadSelectOptionBaseProdi('#selectProdi','');
        loadSelectOptionPaymentType('#selectPTID','');
        getReloadTableSocket();
        // $("#btn-submit").addClass('hide');
    });

    $('#selectCurriculum').change(function () {
        loadData(1);
    });

    $('#selectProdi').change(function () {
        loadData(1);
    });

    $('#selectPTID').change(function () {
        loadData(1);
    });

    $(document).on('keypress','#NIM', function ()
    {

        if (event.keyCode == 10 || event.keyCode == 13) {
          loadData(1);
        }
   }); // exit enter

    $(document).on("click", ".pagination li a", function(event){
      event.preventDefault();
      var page = $(this).attr("data-ci-pagination-page");
      if (page == null){
          page = 1;
      }
      loadData(page);
      // loadData_register_document(page);
    });

    function loadData(page) {
        $("#btn-submit").addClass('hide');
        $("#btn-submit-unapprove").addClass('hide');
        $("#datatable2").addClass('hide');

        var ta = $('#selectCurriculum').val();
        var prodi = $('#selectProdi').val();
        var PTID = $('#selectPTID').val();
        var NIM = $('#NIM').val().trim();
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
            $('#dataRow').html('');
            var url = base_url_js+'finance/get_created_tagihan_mhs/'+page;
            var data = {
                ta : ta,
                prodi : prodi,
                PTID  : PTID,
                NIM : NIM,
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               var resultJson = jQuery.parseJSON(resultJson);
               console.log(resultJson);
                var Data_mhs = resultJson.loadtable;
                data = Data_mhs;
                dataaModal = Data_mhs;
               for(var i=0;i<Data_mhs.length;i++){
                    var ccc = 0;
                    var yy = (Data_mhs[i]['InvoicePayment'] != '') ? formatRupiah(Data_mhs[i]['InvoicePayment']) : '-';
                    // proses status
                    var status = '';
                    if(Data_mhs[i]['StatusPayment'] == 0)
                    {
                      status = 'Belum Approve <br> Belum Lunas';
                    }
                    else
                    {
                      status = 'Approve';
                      // check lunas atau tidak
                        // count jumlah pembayaran dengan status 1
                        var b = 0;
                        for (var j = 0; j < Data_mhs[i]['DetailPayment'].length; j++) {
                          var a = Data_mhs[i]['DetailPayment'][j]['Status'];
                          if(a== 1)
                          {
                            b = parseInt(b) + parseInt(Data_mhs[i]['DetailPayment'][j]['Invoice']);
                          }
                        }

                        // console.log('b : '+b+ '  ..InvoicePayment : ' + Data_mhs[i]['InvoicePayment']);
                        if(b < Data_mhs[i]['InvoicePayment'])
                        {
                          status += '<br> Belum Lunas';
                          ccc = 1;
                        }
                        else
                        {
                          status += '<br> Lunas';
                          ccc = 2
                        }
                    }

                   var tr = '<tr NPM = "'+Data_mhs[i]['NPM']+'">';
                   var inputCHK = ''; 
                   if (ccc == 0) {
                    tr = '<tr NPM = "'+Data_mhs[i]['NPM']+'">';
                    inputCHK = '<input type="checkbox" class="uniform" value ="'+Data_mhs[i]['NPM']+'" Prodi = "'+Data_mhs[i]['ProdiEng']+'" Nama ="'+Data_mhs[i]['Nama']+'" semester = "'+Data_mhs[i]['SemesterID']+'" ta = "'+Data_mhs[i]['Year']+'" invoice = "'+Data_mhs[i]['InvoicePayment']+'" discount = "'+Data_mhs[i]['Discount']+'" PTID = "'+Data_mhs[i]['PTID']+'" PTName = "'+Data_mhs[i]['PTIDDesc']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'" Status = "'+ccc+'">'; 
                   } else if(ccc == 1) {
                      tr = '<tr style="background-color: #eade8e; color: black;" NPM = "'+Data_mhs[i]['NPM']+'">';
                      inputCHK = '<input type="checkbox" class="uniform" value ="'+Data_mhs[i]['NPM']+'" Prodi = "'+Data_mhs[i]['ProdiEng']+'" Nama ="'+Data_mhs[i]['Nama']+'" semester = "'+Data_mhs[i]['SemesterID']+'" ta = "'+Data_mhs[i]['Year']+'" invoice = "'+Data_mhs[i]['InvoicePayment']+'" discount = "'+Data_mhs[i]['Discount']+'" PTID = "'+Data_mhs[i]['PTID']+'" PTName = "'+Data_mhs[i]['PTIDDesc']+'" PaymentID = "'+Data_mhs[i]['PaymentID']+'" Status = "'+ccc+'">'; 
                   }
                   else
                   {
                    tr = '<tr style="background-color: #8ED6EA; color: black;" NPM = "'+Data_mhs[i]['NPM']+'">';
                    inputCHK = ''; 
                   } 
                   
                   $('#dataRow').append(tr +
                       '<td>'+inputCHK+'</td>' +
                       '<td>'+Data_mhs[i]['ProdiEng']+'</td>' +
                       '<td>'+Data_mhs[i]['SemesterName']+'</td>' +
                       '<td>'+Data_mhs[i]['Nama']+'</td>' +
                       '<td>'+Data_mhs[i]['NPM']+'</td>' +
                       '<td>'+Data_mhs[i]['PTIDDesc']+'</td>' +
                       '<td>'+Data_mhs[i]['EmailPU']+'</td>' +
                       '<td>'+Data_mhs[i]['Discount']+'%</td>' +
                       '<td>'+yy+'</td>' +
                       '<td>'+status+'</td>' +
                       '<td>'+'<button class = "DetailPayment" NPM = "'+Data_mhs[i]['NPM']+'">View</button>'+'</td>' +
                       '</tr>');
               }

               if(Data_mhs.length > 0)
               {
                $('#btn-submit').removeClass('hide');
                $("#btn-submit-unapprove").removeClass('hide');
                $('#datatable2').removeClass('hide');
                $("#pagination_link").html(resultJson.pagination_link);
               }
               
            }).fail(function() {
              
              toastr.info('No Result Data'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#NotificationModal').modal('hide');
            });
    }

    $(document).on('click','#dataResultCheckAll', function () {
        $('input.uniform').not(this).prop('checked', this.checked);
    });

    $(document).on('click','.DetailPayment', function () {
        var NPM = $(this).attr('NPM');
        var html = '';
        var table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                      '<thead>'+
                          '<tr>'+
                              '<th style="width: 5px;">No</th>'+
                              '<th style="width: 55px;">Nama</th>'+
                              '<th style="width: 55px;">Invoice</th>'+
                              '<th style="width: 55px;">BilingID</th>'+
                              '<th style="width: 55px;">Status</th>'+
                              '<th style="width: 55px;">Deadline</th>'+
                              '<th style="width: 55px;">UpdateAt</th>';
        table += '</tr>' ;  
        table += '</thead>' ; 
        table += '<tbody>' ;
        var isi = '';
        // console.log(dataaModal);
        for (var i = 0; i < dataaModal.length; i++) {
          if(dataaModal[i]['NPM'] == NPM)
          {
            var DetailPaymentArr = dataaModal[i]['DetailPayment'];
            var Nama = dataaModal[i]['Nama'];
            for (var j = 0; j < DetailPaymentArr.length; j++) {
              var yy = (DetailPaymentArr[j]['Invoice'] != '') ? formatRupiah(DetailPaymentArr[j]['Invoice']) : '-';
              var status = (DetailPaymentArr[j]['Status'] == 0) ? 'Belum Bayar' : 'Sudah Bayar';
              isi += '<tr>'+
                    '<td>'+ (j+1) + '</td>'+
                    '<td>'+ Nama + '</td>'+
                    '<td>'+ yy + '</td>'+
                    '<td>'+ DetailPaymentArr[j]['BilingID'] + '</td>'+
                    '<td>'+ status + '</td>'+
                    '<td>'+ DetailPaymentArr[j]['Deadline'] + '</td>'+
                    '<td>'+ DetailPaymentArr[j]['UpdateAt'] + '</td>'+
                  '<tr>'; 
            }
            break;
          }
        }

        table += isi+'</tbody>' ; 
        table += '</table>' ;

        html += table;

        var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
            '';

        $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'Detail Payment'+'</h4>');
        $('#GlobalModalLarge .modal-body').html(html);
        $('#GlobalModalLarge .modal-footer').html(footer);
        $('#GlobalModalLarge').modal({
            'show' : true,
            'backdrop' : 'static'
        });    

    });


    function getChecboxNPM(element)
    {
         var allVals = [];
         $('.datatable2 :checked').each(function() {
            var NPM = $(this).val();
            var Invoice = $(this).attr('invoice');
            var Discount = $(this).attr('discount');
            var semester = $(this).attr('semester');
            var PTID = $(this).attr('PTID');
            var PTName = $(this).attr('PTName');
            var ta = $(this).attr('ta');
            var PaymentID = $(this).attr('PaymentID');
            var Status = $(this).attr('Status');

            if (Discount != null){
                var arr = {
                        Nama : $(this).attr('Nama'),
                        NPM : NPM,
                        semester : semester,
                        Prodi : $(this).attr('Prodi'),
                        Invoice : Invoice,
                        Discount : Discount,
                        PTID : PTID,
                        PTName : PTName,
                        ta : ta,
                        PaymentID : PaymentID,
                        Status : Status,

                };
                allVals.push(arr);
            }
            
         });
         return allVals;
    }

    $(document).on('click','#btn-submit', function () {
        var arrValueCHK = getChecboxNPM();
        console.log(arrValueCHK);
        if (arrValueCHK.length > 0) {
            // check status jika 1
            var bool = true;
            var html = '';
            var table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                          '<thead>'+
                              '<tr>'+
                                  '<th style="width: 5px;">No</th>'+
                                  '<th style="width: 55px;">Nama</th>'+
                                  '<th style="width: 55px;">NPM</th>'+
                                  '<th style="width: 55px;">Prodi</th>'+
                                  '<th style="width: 55px;">Payment Type</th>'+
                                  '<th style="width: 55px;">Discount</th>'+
                                  '<th style="width: 55px;">Invoice</th>';
            table += '</tr>' ;  
            table += '</thead>' ; 
            table += '<tbody>' ;
            var isi = '';
            for (var i = 0; i < arrValueCHK.length ; i++) {
              // console.log(arrValueCHK[i]['Status']);
              if (arrValueCHK[i]['Status'] == 1) {
                bool = false;
                break;
              }
              var yy = (arrValueCHK[i]['Invoice'] != '') ? formatRupiah(arrValueCHK[i]['Invoice']) : '-';
                isi += '<tr>'+
                      '<td>'+ (i+1) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Nama']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['NPM']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Prodi']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['PTName']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Discount']) + ' %</td>'+
                      '<td>'+ yy + '</td>'+
                    '<tr>';  
                
            }

            table += isi+'</tbody>' ; 
            table += '</table>' ;

            if (bool) {
              html += table;

              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>';
            } else {
              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '';
                  html = "Inputan data anda memiliki Status Approve, mohon periksa kembali";
            }
            

           $('#GlobalModal .modal-header').html('<h4 class="modal-title">'+'List Checklist Data'+'</h4>');
           $('#GlobalModal .modal-body').html(html);
           $('#GlobalModal .modal-footer').html(footer);
           $('#GlobalModal').modal({
               'show' : true,
               'backdrop' : 'static'
           });
         
           $( "#ModalbtnSaveForm" ).click(function() {
            loading_button('#ModalbtnSaveForm');
            var url = base_url_js+'finance/approved_created_tagihan_mhs';
            var data = {
                arrValueCHK : arrValueCHK,
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               // var resultJson = jQuery.parseJSON(resultJson);
               loadData(1);

            }).fail(function() {
              toastr.info('No Action...'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#ModalbtnSaveForm').prop('disabled',false).html('Save');
            });
             
           }); // exit click function

        }
        else
        {
            toastr.error("Silahkan checked dahulu", 'Failed!!');
        }
    });

    $(document).on('click','#btn-submit-unapprove', function () {
      // $(".uniform[value=21150045]").addClass('hide');
      // $("#datatable2 table > tbody > tr [input[value=21150045]]").addClass('hide');
      // $(".uniform[value=21150045]").parent().addClass('hide');
      // $("tr[NPM=21150045]").addClass('hide'); Ok

        var arrValueCHK = getChecboxNPM();
        console.log(arrValueCHK);
        if (arrValueCHK.length > 0) {
            // check status jika 1
            var bool = true;
            var html = '';
            var table = '<table class="table table-striped table-bordered table-hover table-checkable tableData">'+
                          '<thead>'+
                              '<tr>'+
                                  '<th style="width: 5px;">No</th>'+
                                  '<th style="width: 55px;">Nama</th>'+
                                  '<th style="width: 55px;">NPM</th>'+
                                  '<th style="width: 55px;">Prodi</th>'+
                                  '<th style="width: 55px;">Payment Type</th>'+
                                  '<th style="width: 55px;">Discount</th>'+
                                  '<th style="width: 55px;">Invoice</th>';
            table += '</tr>' ;  
            table += '</thead>' ; 
            table += '<tbody>' ;
            var isi = '';
            for (var i = 0; i < arrValueCHK.length ; i++) {
              // console.log(arrValueCHK[i]['Status']);
              if (arrValueCHK[i]['Status'] == 0) {
                bool = false;
                break;
              }
              var yy = (arrValueCHK[i]['Invoice'] != '') ? formatRupiah(arrValueCHK[i]['Invoice']) : '-';
                isi += '<tr>'+
                      '<td>'+ (i+1) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Nama']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['NPM']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Prodi']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['PTName']) + '</td>'+
                      '<td>'+ (arrValueCHK[i]['Discount']) + ' %</td>'+
                      '<td>'+ yy + '</td>'+
                    '<tr>';  
                
            }

            table += isi+'</tbody>' ; 
            table += '</table>' ;

            if (bool) {
              html += table;

              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '<button type="button" id="ModalbtnSaveForm" class="btn btn-success">Save</button>';
            } else {
              var footer = '<button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>'+
                  '';
                  html = "Inputan data anda memiliki Status Approve, mohon periksa kembali";
            }
            

           $('#GlobalModalLarge .modal-header').html('<h4 class="modal-title">'+'List Checklist Data'+'</h4>');
           $('#GlobalModalLarge .modal-body').html(html);
           $('#GlobalModalLarge .modal-footer').html(footer);
           $('#GlobalModalLarge').modal({
               'show' : true,
               'backdrop' : 'static'
           });
         
           $( "#ModalbtnSaveForm" ).click(function() {
            loading_button('#ModalbtnSaveForm');
            var url = base_url_js+'finance/unapproved_created_tagihan_mhs';
            var data = {
                arrValueCHK : arrValueCHK,
            };
            var token = jwt_encode(data,'UAP)(*');
            $.post(url,{token:token},function (resultJson) {
               var resultJson = jQuery.parseJSON(resultJson);
               console.log(resultJson);
               if (resultJson != '')
               {
                toastr.info(resultJson); 
               }
               else
               {
                toastr.success('Data berhasil disimpan', 'Success!');
               }
               loadData(1);

            }).fail(function() {
              toastr.info('No Action...'); 
              // toastr.error('The Database connection error, please try again', 'Failed!!');
            }).always(function() {
                $('#ModalbtnSaveForm').prop('disabled',false).html('Save');
            });
             
           }); // exit click function
          

        }
        else
        {
            toastr.error("Silahkan checked dahulu", 'Failed!!');
        }
    });


    function getReloadTableSocket()
    {
      var socket = io.connect( 'http://'+window.location.hostname+':3000' );
      // var socket = io.connect( '<?php echo serverRoot ?>'+':3000' );

      socket.on( 'update_notifikasi', function( data ) {

          //$( "#new_count_message" ).html( data.new_count_message );
          //$('#notif_audio')[0].play();
          if (data.update_notifikasi == 1) {
              // action
              loadData(1);
          }

      }); // exit socket
    }

</script>