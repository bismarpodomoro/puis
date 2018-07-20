
<div class="row" style="margin-top: 30px;">
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectCurriculum">
                <option selected value = ''>--- Curriculum ---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <select class="form-control" id="selectProdi">
                <option selected value = ''>--- Prodi---</option>
                <option disabled>------</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="thumbnail" style="min-height: 30px;padding: 10px;">
            <input type="text" name="" class="form-control" placeholder="Input NPM Mahasiswa" id = "NIM" value="">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <hr/>
        <!-- <div class="col-md-3 col-md-offset-8"> -->
          <!-- <input type="text" name="NPM" placeholder="Input NPM" id = "NPM" class="form-control"> -->
        <!-- </div>   -->
        <table class="table table-bordered datatable2 hide" id = "datatable2">
            <thead>
            <tr style="background: #333;color: #fff;">
                <th style="width: 12%;">Program Study</th>
                <!-- <th style="width: 10%;">Semester</th> -->
                <th style="width: 20%;">Nama,NPM & VA</th>
                <!-- <th style="width: 5%;">NPM</th> -->
                <!-- <th style="width: 5%;">Year</th> -->
                <th style="width: 5%;">Foto</th>
                <th style="width: 15%;">Email PU</th>
                <th style="width: 5%;">No HP</th>
                <th style="width: 5%;">Bintang</th>
                <th style="width: 5%;">Beasiswa BPP</th>
                <th style="width: 5%;">Beasiswa Credit</th>
            </tr>
            </thead>
            <tbody id="dataRow"></tbody>
        </table>
    </div>
    <div  class="col-xs-12" align="right" id="pagination_link"></div>
    <br>
</div>


<script>
    $(document).ready(function () {
        loadSelectOptionCurriculum('#selectCurriculum','');
        loadSelectOptionBaseProdi('#selectProdi','');
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

    $(document).on("click", ".pagination li a", function(event){
      event.preventDefault();
      var page = $(this).attr("data-ci-pagination-page");
      if (page == null){
          page = 1;
      }
      loadData(page);
      // loadData_register_document(page);
    });

    $(document).on('keypress','#NIM', function ()
    {

        if (event.keyCode == 10 || event.keyCode == 13) {
          loadData(1);
        }
    }); // exit enter

    function loadData(page) {
        $("#btn-submit").addClass('hide');
        $("#datatable2").addClass('hide');

        $('#dataResultCheckAll').prop('checked', false); // Unchecks it
        $("span").removeClass('checked');

        var ta = $('#selectCurriculum').val();
        var prodi = $('#selectProdi').val();
        var NPM = $('#NIM').val();
        if(ta!='' && ta!=null){
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
                var url = base_url_js+'finance/master/mahasiswa_list/'+page;
                var data = {
                    ta : ta,
                    prodi : prodi,
                    NPM : NPM
                };
                var token = jwt_encode(data,'UAP)(*');
                $.post(url,{token:token},function (resultJson) {
                   var resultJson = jQuery.parseJSON(resultJson);
                   console.log(resultJson);
                    var Data_mhs = resultJson.loadtable;
                    var xx = resultJson.loadtable;
                    var Data_mhs = Data_mhs['Data_mhs'];
                    var res = ta.split(".");
                   for(var i=0;i<Data_mhs.length;i++){
                        var img = '<img src="'+base_url_js+'uploads/students/ta_'+res[1]+'/'+Data_mhs[i]['Photo']+'" class="img-rounded" width="30" height="30" style="max-width: 30px;object-fit: scale-down;">';

                        var Bea_BPP = Data_mhs[i]['Bea_BPP'];
                        var Bea_Credit = Data_mhs[i]['Bea_Credit']; 

                        var selecTOption = '<select class="selecTOption getDom" id="'+'discountBPP_'+Data_mhs[i]['NPM']+'" NPM = "'+Data_mhs[i]['NPM']+'">';
                        var selecTOptionCredit = '<select class="selecTOptionCredit getDom" id="'+'discountCredit_'+Data_mhs[i]['NPM']+'" NPM = "'+Data_mhs[i]['NPM']+'">';
                        var selecTOptionBintang = '<select class="selecTbintang getDom" id="'+'bintang_'+Data_mhs[i]['NPM']+'" NPM = "'+Data_mhs[i]['NPM']+'">';

                        for (var l = 0; l < 2; l++) {
                          var ll = parseInt(l) + 1;
                          var selected = (ll == Data_mhs[i]['Pay_Cond']) ? 'selected' : '';
                          var bntg = '';
                          for (var m = 0; m < ll; m++) {
                           bntg += '*';
                          }
                          selecTOptionBintang += '<option value="'+ll+'" '+selected+'>'+bntg+'</option>';
                        }

                        selecTOptionBintang += '</select>';

                            for (var k = 0;k < xx['Discount'].length; k++)
                            {
                                var O_discount = xx['Discount'];
                                var selected = (O_discount[k]['Discount'] == Bea_BPP) ? 'selected' : '';
                                /*if(PTID == 2)
                                {
                                  var selected = (O_discount[k]['Discount'] == Bea_BPP) ? 'selected' : '';
                                }
                                else if(PTID == 3)
                                {
                                  var selected = (O_discount[k]['Discount'] == Bea_Credit) ? 'selected' : '';
                                }*/
                                selecTOption += '<option value="'+xx['Discount'][k]['Discount']+'" '+selected+'>'+xx['Discount'][k]['Discount']+'%'+'</option>';
                            }
                        selecTOption += '</select>';

                            for (var k = 0;k < xx['Discount'].length; k++)
                            {
                                var O_discount = xx['Discount'];
                                var selected = (O_discount[k]['Discount'] == Bea_Credit) ? 'selected' : '';
                                /*if(PTID == 2)
                                {
                                  var selected = (O_discount[k]['Discount'] == Bea_BPP) ? 'selected' : '';
                                }
                                else if(PTID == 3)
                                {
                                  var selected = (O_discount[k]['Discount'] == Bea_Credit) ? 'selected' : '';
                                }*/
                                selecTOptionCredit += '<option value="'+xx['Discount'][k]['Discount']+'" '+selected+'>'+xx['Discount'][k]['Discount']+'%'+'</option>';
                            }
                        selecTOptionCredit += '</select>';

                        // show bintang
                        var bintang = (Data_mhs[i]['Pay_Cond'] == 1) ? '<p style="color: red;">*</p>' : '<p style="color: red;">**</p>';

                       $('#dataRow').append('<tr>' +
                           '<td>'+Data_mhs[i]['ProdiEng']+'</td>' +
                           // '<td>'+Data_mhs[i]['SemesterName']+'</td>' +
                           '<td>'+bintang+Data_mhs[i]['Name']+'<br>'+Data_mhs[i]['NPM']+'<br>'+Data_mhs[i]['VA']+'</td>' +
                           // '<td>'+Data_mhs[i]['NPM']+'</td>' +
                           // '<td>'+Data_mhs[i]['ClassOf']+'</td>' +
                           '<td>'+img+'</td>' +
                           '<td>'+Data_mhs[i]['EmailPU']+'</td>' +
                           '<td>'+Data_mhs[i]['HP']+'</td>' +
                           '<td>'+selecTOptionBintang+'</td>' +
                           '<td>'+selecTOption+'</td>' +
                           '<td>'+selecTOptionCredit+'</td>' +
                           '</tr>');
                   }

                   if(Data_mhs.length > 0)
                   {
                    $('#btn-submit').removeClass('hide');
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
    }

    $(document).on('change','.selecTbintang', function () {
      var bintang = $(this).val();
      var Npm = $(this).attr('npm');
      // update data to db
      $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Apakah anda yakin untuk melakukan request ini ?? </b> ' +
          '<button type="button" id="confirmYes3" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
          '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
          '</div>');
      $('#NotificationModal').modal('show');

      $("#confirmYes3").click(function(){
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
          var url =base_url_js+'finance/master/edited-pay-cond';
          var data = {
            bintang : bintang,
            Npm : Npm,

          };
          var token = jwt_encode(data,'UAP)(*');
          $.post(url,{token:token},function (data_json) {
              setTimeout(function () {
                 toastr.options.fadeOut = 10000;
                 toastr.success('Data berhasil disimpan', 'Success!');
                 loadData(1);
                 $('#NotificationModal').modal('hide');
              },500);
          });
      })
    });

    $(document).on('change','.selecTOptionCredit', function () {
      var Discount = $(this).val();
      var Npm = $(this).attr('npm');
      // update data to db
      $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Apakah anda yakin untuk melakukan request ini ?? </b> ' +
          '<button type="button" id="confirmYes2" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
          '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
          '</div>');
      $('#NotificationModal').modal('show');

      $("#confirmYes2").click(function(){
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
          var url =base_url_js+'finance/master/edited-bea-credit';
          var data = {
            Discount : Discount,
            Npm : Npm,

          };
          var token = jwt_encode(data,'UAP)(*');
          $.post(url,{token:token},function (data_json) {
              setTimeout(function () {
                 toastr.options.fadeOut = 10000;
                 toastr.success('Data berhasil disimpan', 'Success!');
                 loadData(1);
                 $('#NotificationModal').modal('hide');
              },500);
          });
      })
    });

    $(document).on('change','.selecTOption', function () {
      var Discount = $(this).val();
      var Npm = $(this).attr('npm');
      // update data to db
      $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Apakah anda yakin untuk melakukan request ini ?? </b> ' +
          '<button type="button" id="confirmYes" class="btn btn-primary" style="margin-right: 5px;">Yes</button>' +
          '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
          '</div>');
      $('#NotificationModal').modal('show');

      $("#confirmYes").click(function(){
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
          var url =base_url_js+'finance/master/edited-bea-bpp';
          var data = {
            Discount : Discount,
            Npm : Npm,

          };
          var token = jwt_encode(data,'UAP)(*');
          $.post(url,{token:token},function (data_json) {
              setTimeout(function () {
                 toastr.options.fadeOut = 10000;
                 toastr.success('Data berhasil disimpan', 'Success!');
                 loadData(1);
                 $('#NotificationModal').modal('hide');
              },500);
          });
      })
    });

</script>