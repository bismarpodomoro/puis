
<style>
    h3.header-blue{
        margin-top: 0px;
        border-left: 7px solid #2196F3;
        padding-left: 10px;
        font-weight: bold;
    }

    #tableTransfer tr th {
        text-align: center;
        background: #607d8b;
        color: #FFFFFF;
    }
</style>

<div class="row">
    <div class="col-md-3">
        <div class="thumbnail">
            <div class="row" style="min-height: 100px;">
                <div class="col-md-12">
                    <div style="padding: 15px;">
                        <h3 class="header-blue">Create NIM</h3>
                        <div style="background: lightyellow; border: 1px solid #ccc;padding: 15px;color: #f44336;margin-bottom: 20px;">
                            <b>Semua data</b> student lama akan <b>diduplikasi</b> ke NIM baru dan status NIM lama menjadi <b>"Pindah Prodi"</b>, status NIM baru <b>"Aktif"</b>
                        </div>
                    </div>


                    <div class="well">
                        <div style="text-align: center;">
                            <h4 style="margin-top: 0px;">From :</h4>
                        </div>
                        <div class="form-group">
                            <label>Class Of</label>
                            <select class="form-control" id="fromClassOf"></select>
                        </div>
                        <div class="form-group">
                            <label>Programme Study</label>
                            <select class="form-control" id="fromProdi"></select>
                        </div>
                        <div class="form-group">
                            <label>Select Student</label>
                            <div id="showStd">-</div>
                        </div>
                    </div>

                    <div class="well">
                        <div style="text-align: center;">
                            <h4 style="margin-top: 0px;">To :</h4>
                        </div>
                        <div class="form-group">
                            <label>Class Of</label>
                            <select class="form-control" id="toClassOf"></select>
                        </div>
                        <div class="form-group">
                            <label>Programme Study</label>
                            <select class="form-control" id="toProdi"></select>
                        </div>
                        <div class="form-group">
                            <label>New NIM</label>
                            <input class="form-control" id="toNewNPM"/>
                            <span id="viewStatusNewNPM" style="float: right;"></span>
                            <input class="hide" id="statusNewNPM" value="0"/>
                        </div>
                        <div class="form-group">
                            <label>Reason</label>
                            <select class="form-control" id="toReason"></select>
                        </div>
                    </div>

                    <div style="padding: 5px;">
                        <button class="btn btn-block btn-success" id="btnCreateNPM">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-9">
        <div class="thumbnail" style="padding: 15px;">
<!--            <h3 class="header-blue">Course Conversion</h3>-->

            <h3 class="header-blue">List Transfer Student</h3>

            <div class="row">
                <div class="col-md-12">
                    <div id="viewTable"></div>
                </div>
            </div>


        </div>
    </div>
</div>


<script>
    $(document).ready(function () {


        getListStudentTransfer();

        loadSelectOptionClassOf_ASC('#fromClassOf','');
        loadSelectOptionBaseProdi('#fromProdi','');

        loadSelectOptionClassOf_ASC('#toClassOf','');
        loadSelectOptionBaseProdi('#toProdi','');
        loadSelectOptionReasonTransferStudent('#toReason','');

        $('#filterStudent').select2();

        var firsLoad = setInterval(function () {
            var fromClassOf = $('#fromClassOf').val();
            var fromProdi = $('#fromProdi').val();
            if(fromClassOf != '' && fromClassOf!=null &&
                fromProdi != '' && fromProdi!=null){
                loadFromStudent();
                clearInterval(firsLoad);
            }
        },1000);

    });

    $('#toNewNPM').keyup(function () {
        var toNewNPM = $('#toNewNPM').val();
        if(toNewNPM!='' && toNewNPM!=null){
            checkNPMTransferStudent();
        }
    });

    function checkNPMTransferStudent() {
        var toNewNPM = $('#toNewNPM').val();
        if(toNewNPM!='' && toNewNPM!=null){
            var url = base_url_js+'api/__crudTransferStudent';
            var token = jwt_encode({action : 'checkNPMTransferStudent', NPM : toNewNPM},'UAP)(*');

            $.post(url,{token:token},function (jsonResult) {

                $('#statusNewNPM').val(0);
                $('#viewStatusNewNPM').html('<span style="color: red;">NPM can\'t use</span>');
                if(jsonResult.Status==1 || jsonResult.Status=='1'){
                    $('#statusNewNPM').val(1);
                    $('#viewStatusNewNPM').html('<span style="color: green;">NPM can use</span>');
                }
            });

        }
    }

    $('#fromClassOf,#fromProdi').change(function () {
        var fromClassOf = $('#fromClassOf').val();
        var fromProdi = $('#fromProdi').val();
        if(fromClassOf != '' && fromClassOf!=null &&
        fromProdi != '' && fromProdi!=null){
            loadFromStudent();
        }
    });

    function loadFromStudent() {

        var fromClassOf = $('#fromClassOf').val();
        var fromProdi = $('#fromProdi').val();
        var elSt = $('#showStd');
        if(fromClassOf != '' && fromClassOf!=null &&
            fromProdi != '' && fromProdi!=null){

            var url = base_url_js+'api/__crudTransferStudent';
            var ClassOf = fromClassOf.split('.')[1];
            var ProdiID = fromProdi.split('.')[0];
            var token = jwt_encode({action : 'readFromStudentTransfer', ClassOf : ClassOf, ProdiID : ProdiID},'UAP)(*');
            $.post(url,{token:token},function (jsonResult) {

                elSt.html('<select class="select2-select-00 full-width-fix form-jadwal"' +
                    '                                    size="5" id="fromStudent"><option></option></select>');

                if(jsonResult.length>0){
                    for(var i=0;i<jsonResult.length; i++){
                        var d = jsonResult[i];
                        $('#fromStudent').append('<option value="'+d.NPM+'">'+d.NPM+' - '+ucwords(d.Name)+'</option>');
                    }
                }

                $('#fromStudent').select2({allowClear: true});

            });

        } else {
            elSt.html('-');
        }

    }

    $('#btnCreateNPM').click(function () {
        var fromClassOf = $('#fromClassOf').val();
        var fromProdi = $('#fromProdi').val();
        var fromStudent = $('#fromStudent').val();

        var toClassOf = $('#toClassOf').val();
        var toProdi = $('#toProdi').val();
        var toNewNPM = $('#toNewNPM').val();
        var toReason = $('#toReason').val();

        if(fromClassOf!='' && fromClassOf!=null &&
            fromProdi!='' && fromProdi!=null &&
            fromStudent!='' && fromStudent!=null &&
            toClassOf!='' && toClassOf!=null &&
            toProdi!='' && toProdi!=null &&
            toNewNPM!='' && toNewNPM!=null &&
            toReason!='' && toReason!=null){

            var statusNewNPM = $('#statusNewNPM').val();
            if(statusNewNPM==1 || statusNewNPM=='1'){

                if(confirm('Are you sure to create NIM?')){

                    loading_button('#btnCreateNPM');

                    var ProdiID_f = fromProdi.split('.')[0];
                    var ClassOf_f = fromClassOf.split('.')[1];

                    var ProdiID_t = toProdi.split('.')[0];
                    var ClassOf_t = toClassOf.split('.')[1];

                    var data = {
                        action : 'addingTransferStudent',
                        fromClassOf : ClassOf_f,
                        fromProdi : ProdiID_f,
                        fromStudent : fromStudent,
                        toClassOf : ClassOf_t,
                        toProdi : ProdiID_t,
                        toNewNPM : toNewNPM,
                        TransferTypeID : toReason,
                        CreateAt : dateTimeNow(),
                        CreateBy : sessionNIP
                    };

                    // console.log(data);
                    // return false;

                    var token = jwt_encode(data,'UAP)(*');

                    var url = base_url_js+'api/__crudTransferStudent';
                    $.post(url,{token:token},function (jsonResult) {
                        setTimeout(function () {
                            window.location.href = '';
                        },500);
                    });
                }

            } else {
                toastr.warning('NIM canot to use','Warning');
            }

        } else {
            toastr.error('All Form Required','Error');
        }
    });



    // LIST TRANSFER STUDENT
    function getListStudentTransfer() {

        $('#viewTable').html('<table class="table table-bordered table-striped" id="tableTransfer">' +
            '                        <thead>' +
            '                        <tr>' +
            '                            <th style="width: 1%;" rowspan="2">No</th>' +
            '                            <th style="width: 15%;" rowspan="2">Name</th>' +
            '                            <th style="background: #985a55;" colspan="3">From</th>' +
            '                            <th rowspan="2" style="width: 7%;">Action</th>' +
            '                            <th colspan="3" style="background: #608862;">To</th>' +
            '                        </tr>' +
            '                        <tr>' +
            '                            <th style="background: #985a55;width: 7%;">NIM</th>' +
            '                            <th style="background: #985a55;width: 7%;">Class Of</th>' +
            '                            <th style="background: #985a55;">Prodi</th>' +
            '                            <th style="width: 7%;background: #608862;">NIM</th>' +
            '                            <th style="width: 7%;background: #608862;">Class Of</th>' +
            '                            <th style="background: #608862;">Prodi</th>' +
            '                        </tr>' +
            '                        </thead>' +
            '                    </table>');


        var dataTable = $('#tableTransfer').DataTable( {
            "processing": true,
            "serverSide": true,
            "iDisplayLength" : 10,
            "ordering" : false,
            "language": {
                "searchPlaceholder": "NIM, Name, Programme Study"
            },
            "ajax":{
                url : base_url_js+'academic/transfer-student/__loadListTransferStudent', // json datasource
                ordering : false,
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".employee-grid-error").html("");
                    $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#employee-grid_processing").css("display","none");
                }
            }
        } );
    }

    $(document).on('click','.btnRemoveData',function () {

        $('.btnRemoveData').prop('disabled',true);

        var ID = $(this).attr('data-id');
        var url = base_url_js+'api/__crudTransferStudent';
        var token = jwt_encode({action : 'removeTransverStudent', ID : ID},'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {
            setTimeout(function () {
                getListStudentTransfer();
            },500);
        });

    });

</script>