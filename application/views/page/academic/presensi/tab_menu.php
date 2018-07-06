<style>
    .form-attd[readonly] {
        cursor: cell;
        background-color: #fff;
        color: #333;
    }
</style>

<!--<h1>Menu</h1>-->
<!--<hr/>-->

<div class="col-md-12">
    <div class="row">
        <div class="col-md-4">
            <div class="">
                <label>Semester Antara</label>
                <input type="checkbox" id="formSemesterAntara" data-toggle="toggle" data-style="ios"/>
            </div>
        </div>

    </div>


    <div class="thumbnail" style="margin-top: 30px;">
        <div class="row">
            <div class="col-md-3">
                <select id="filterSemester" class="form-control filter-presensi"></select>
            </div>
            <div class="col-md-3">
                <select class="form-control filter-presensi" id="filterCombine">
                    <option value="0">Combine Class No</option>
                    <option value="1">Combine Class Yes</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterBaseProdi" class="form-control filter-presensi"></select>
            </div>
            <div class="col-md-3">
                <select id="filterGroup" class="form-control"></select>
            </div>
        </div>
    </div>


    <hr/>
</div>



<div id="divpagePresensi"></div>

<!--<script src="--><?php //echo base_url('assets/custom/js/presensi.js'); ?><!--"></script>-->
<script>

    $(document).ready(function () {

        $('#filterSemester').empty();
        $('#filterSemester').append('<option value="" disabled selected>-- Year Academic--</option>' +
            '                <option disabled>------------------------------------------</option>');
        loSelectOptionSemester('#filterSemester','');

        $('#filterBaseProdi').empty();
        $('#filterBaseProdi').append('<option value="" disabled selected>-- Select Program Study --</option>' +
            '<option disabled>------------------------------------------</option>');
        loadSelectOptionBaseProdi('#filterBaseProdi','');

        // var data = {
        //     // NIP : 2114002,
        //     page : 'InputPresensi',
        //     ScheduleID : 2
        // };
        // var token = jwt_encode(data,'UAP)(*');
        // loadPagePresensi(token);

    });

    $(document).on('change','.filter-presensi',function () {
        loadFilterPresensi();
    });

    $('#formSemesterAntara,#filterSemester,#filterCombine,#filterBaseProdi').change(function () {
        $('#divpagePresensi').html('');
    });
    $('#filterGroup').change(function () {
        var filterGroup = $('#filterGroup').val();
        // console.log(filterGroup);
        if(filterGroup!='' && filterGroup!=null){
            var data = {
                page : 'InputPresensi',
                ScheduleID : filterGroup
            };
            var token = jwt_encode(data,'UAP)(*');
            loadPagePresensi(token);
        } else {
            $('#divpagePresensi').html('');
        }

    });

    function loadPagePresensi(token) {
        var url = base_url_js+'academic/loadPagePresensi';

        loading_page('#divpagePresensi');
        $.post(url,{token:token},function (html) {
            setTimeout(function () {
                $('#divpagePresensi').html(html);
            },500)
        });
    }
    
    function loadFilterPresensi() {
        var filterSemester = $('#filterSemester').val();
        var filterCombine = $('#filterCombine').val();
        var filterBaseProdi = $('#filterBaseProdi').val();

        if(filterSemester!=null && filterSemester!=''){
            var exp_fSemester = filterSemester.split('.');
            var SemesterID = exp_fSemester[0];
            var ProdiID = '';
            var ds = (filterCombine=='1') ? true : false;
            if(filterCombine=='0' && filterBaseProdi!=null && filterBaseProdi!=''){
                var exp_BaseProdi = filterBaseProdi.split('.');
                ProdiID = exp_BaseProdi[0];
            }
            $('#filterBaseProdi').prop('disabled',ds);

            var url = base_url_js+'api/__crudAttendance';
            var data = {
                action : 'filterPresensi',
                SemesterID : SemesterID,
                CombinedClasses : filterCombine,
                ProdiID : ProdiID
            };
            var token = jwt_encode(data,'UAP)(*');

            $.post(url,{token:token},function (jsonResult) {

                var sl = $('#filterGroup');

                sl.empty();
                sl.append('<option selected disabled>--- Select Class Group ---</option>');
                if(jsonResult.length>0){
                    for(var i=0;i<jsonResult.length;i++){
                        var dataF = jsonResult[i]
                        sl.append('<option value="'+dataF.ID+'">'+dataF.ClassGroup+'</option>');
                    }
                }
            });
        }
    }


</script>

<!-- Schedule Exchange -->
<script>
    
    $(document).on('click','#btnSaveScheduleEx',function () {
        var ID_Attd = $(this).attr('data-id');
        var Meeting = $(this).attr('data-no');
        checkSchedule(ID_Attd,Meeting);
    });

    $(document).on('click','#btnDeleteScheduleEx',function () {
        var ID_Attd = $(this).attr('data-id');
        var Meeting = $(this).attr('data-no');

        if(confirm('Delete ?')){
            var url = base_url_js+'api/__crudScheduleExchange';
            var dataToken = {
                action : 'deleteSceduleEx',
                ID_Attd : ID_Attd,
                Meeting : Meeting
            };

            loading_buttonSm('#btnDeleteScheduleEx');
            $('.btn-schedule-exchange,.form-ScEx').prop('disabled',true);

            var token = jwt_encode(dataToken,'UAP)(*');
            $.post(url,{token:token},function (jsonResult) {
                getDataAttendance();
                toastr.success('Data Deleted','Deleted');
                $('#GlobalModal').modal('hide');
            });
        }
    });
    
    $(document).on('click','.inputScheduleExchange',function () {

        var filterAttendance = $('#filterAttendance').val();
        var ID_Attd = $(this).attr('data-id');
        var No = $(this).attr('data-no');

        var url = base_url_js+'api/__crudScheduleExchange';
        var data = {
            action : 'readExchange',
            ID_Attd : ID_Attd,
            ScheduleID : filterAttendance.split('.')[1],
            SDID : filterAttendance.split('.')[2],
            Meeting : No
        };

        var token = jwt_encode(data,'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {

            var Credit = jsonResult.S_Details.Credit;
            var TimePerCredit = jsonResult.S_Details.TimePerCredit;

            var data_start = (jsonResult.S_Exchange.length>0 && jsonResult.S_Exchange[0].StartSessions!=''
                && jsonResult.S_Exchange[0].StartSessions!=null) ? jsonResult.S_Exchange[0].StartSessions.substr(0,5) : '00:00' ;

            var data_end = (jsonResult.S_Exchange.length>0 && [0].EndSessions!=''
                && jsonResult.S_Exchange[0].EndSessions!=null) ? jsonResult.S_Exchange[0].EndSessions.substr(0,5) : '00:00' ;

            var data_ClassroomID = (jsonResult.S_Exchange.length>0 && jsonResult.S_Exchange[0].ClassroomID!=''
                && jsonResult.S_Exchange[0].ClassroomID!=null) ? jsonResult.S_Exchange[0].ClassroomID : '';

            var btnActDel = (jsonResult.S_Exchange.length>0) ? '' : 'disabled';


            var body_attd = '<div class="row">' +
                '    <div class="col-xs-12">' +
                '        <table class="table">' +
                '            <tr>' +
                '                <td style="width: 20%;">Lecturer</td>' +
                '                <td>' +
                '                    <select class="form-control form-ScEx" id="formLecturers"></select>' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Date</td>' +
                '                <td>' +
                '                    <input class="form-control form-ScEx" id="formDate" style="width: 170px;">' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Classroom</td>' +
                '                <td>' +
                '                    <select class="form-control form-ScEx" id="formClassroom"></select>' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Start</td>' +
                '                <td>' +
                '                   <div class="row">' +
                '                   <div class="col-xs-4">' +
                '                       <div id="dataStart" class="input-group">' +
                '                           <input data-format="hh:mm" type="text" id="formStart" class="form-control form-ScEx" value="'+data_start+'"/>' +
                '                           <span class="add-on input-group-addon">' +
                '                               <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
                '                           </span>' +
                '                       </div>' +
                '                   </div> ' +
                '                   <div class="col-xs-8" style="padding-top:5px;">' +
                '                       <b>Credit : '+Credit+' | Time : '+TimePerCredit+' minutes/credit</b>' +
                '                   </div> ' +
                '                   </div>' +

                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>End</td>' +
                '                <td>' +
                '                    <input class="form-control form-ScEx" id="formEnd" value="'+data_end+'" style="width: 150px;">' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>Action</td>' +
                '                <td>' +
                '                    <div class="checkbox" style="margin-top: 0px;">' +
                '                       <label>' +
                '                       <input type="checkbox" class="form-ScEx" id="formCkStatus" value="1"> Approve' +
                '                       </label>' +
                '                   </div>' +
                '                </td>' +
                '            </tr>' +
                '        </table>' +
                '       <div id="divAlertEx"></div>' +
                '    </div>' +
                '</div>';

            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Schedule Exchange | Pertemuan '+No+'</h4>');

            $('#GlobalModal .modal-body').html(body_attd);

            for(var l=0;l<jsonResult.Lecturer.length;l++){
                var sc = (jsonResult.S_Exchange.length>0 && jsonResult.S_Exchange[0].NIP!=''
                    && jsonResult.S_Exchange[0].NIP!=null) ? 'selected' : '';
                $('#formLecturers').append('<option value="'+jsonResult.Lecturer[l].NIP+'" '+sc+'>'+jsonResult.Lecturer[l].NIP+' - '+jsonResult.Lecturer[l].Name+'</option>');
            }

            loadSelectOptionClassroom('#formClassroom',data_ClassroomID);

            $("#formDate").datepicker({
                showOtherMonths:true,
                autoSize: true,
                dateFormat: 'dd MM yy'
            });

            if(jsonResult.S_Exchange.length>0 && jsonResult.S_Exchange[0].Date !=='0000-00-00'
                && jsonResult.S_Exchange[0].Date != null && jsonResult.S_Exchange[0].Date != '' ){

                var d = new Date(jsonResult.S_Exchange[0].Date);
                $('#formDate').datepicker('setDate',d);
            }

            $('#formCkStatus').prop('checked',false);
            if(jsonResult.S_Exchange.length>0 && jsonResult.S_Exchange[0].Status=='1'){
                $('#formCkStatus').prop('checked',true);
            }

            $('#dataStart').datetimepicker({
                pickDate: false,
                pickSeconds : false
            })
                .on('changeDate', function(e) {
                var d = new Date(e.localDate);
                var totalTime = parseInt(TimePerCredit) * parseInt(Credit);

                var sesiAkhir = moment()
                    .hours(d.getHours())
                    .minutes(d.getMinutes())
                    .add(parseInt(totalTime), 'minute').format('HH:mm');

                $('#formEnd').val(sesiAkhir);

            });

            $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default btn-schedule-exchange" data-dismiss="modal">Close</button> | ' +
                '<button class="btn btn-danger btn-schedule-exchange" id="btnDeleteScheduleEx" '+btnActDel+' data-no="'+No+'" data-id="'+ID_Attd+'">Delete Permission</button> ' +
                '<button class="btn btn-success btn-schedule-exchange" id="btnSaveScheduleEx" data-no="'+No+'" data-id="'+ID_Attd+'">Save</button>');
            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });


        });

    });

    function checkSchedule(ID_Attd,Meeting) {


        var filterSemester = $('#filterSemester').val();
        // var SemesterID = filterSemester.split('.')[0];
        var SemesterID = 13;

        var formLecturers = $('#formLecturers').val();
        var formDate = $('#formDate').datepicker("getDate");
        var formClassroom = $('#formClassroom').val();
        var formStart = $('#formStart').val();
        var formEnd = $('#formEnd').val();


        if(filterSemester!=null && filterSemester!='' && formDate!=null && formDate!=''
            && formStart!=null && formStart!='' && formStart!='00:00'
            && formEnd!=null && formEnd!=''){

            loading_buttonSm('#btnSaveScheduleEx');
            $('.btn-schedule-exchange,.form-ScEx').prop('disabled',true);

            var DayMoment = moment(formDate).day();
            var DayID = (DayMoment==0)? 7 : DayMoment;

            var Status = ($('#formCkStatus').is(':checked')) ? '1' : '0' ;

            var data = {
                action : 'check',
                formData : {
                    SemesterID : SemesterID,
                    IsSemesterAntara : '0',
                    ClassroomID : formClassroom,
                    DayID : DayID,
                    StartSessions : formStart,
                    EndSessions : formEnd
                }
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api/__checkSchedule';
            var element = '#divAlertEx';
            $.post(url,{token:token},function (json_result) {
                $(element).html('');
                if(json_result.length>0){
                    $(element).append('<div class="row">' +
                        '                        <div class="col-xs-12" style="margin-top: 20px;">' +
                        '                            <div class="alert alert-danger" role="alert">' +
                        '                                <b><i class="fa fa-exclamation-triangle" aria-hidden="true" style="margin-right: 5px;"></i> Jadwal bentrok</b>, Silahklan rubah : Ruang / Hari / Jam' +
                        '                                <hr style="margin-bottom: 3px;margin-top: 10px;"/>' +
                        '                                <ol id="ulbentrok">' +
                        '                                </ol>' +
                        '                            </div>' +
                        '                        </div>' +
                        '' +
                        '                    </div>');

                    var ol = $('#ulbentrok');
                    for(var i=0;i<json_result.length;i++){
                        var data = json_result[i];
                        ol.append('<li>' +
                            'Group <strong style="color:#333;">'+data.ClassGroup+'</strong> : <span style="color: blue;">'+data.Room+' | '+daysEng[(parseInt(data.DayID)-1)]+' '+data.StartSessions+' - '+data.EndSessions+'</span>' +
                            '<ul style="color: #607d8b;" id="dtMK'+i+'"></ul>' +
                            '</li>');

                        var ul = $('#dtMK'+i);
                        for(var m=0;m<data.DetailsCourse.length;m++){
                            var mk_ = data.DetailsCourse[m];
                            ul.append('<li>'+mk_.MKCode+' | '+mk_.NameEng+'</li>');
                        }
                    }

                }
                else {
                    var dataInsert = {
                        ID_Attd : ID_Attd,
                        NIP : formLecturers,
                        Meeting : Meeting,
                        ClassroomID : formClassroom,
                        Date : moment(formDate).format('YYYY-MM-DD'),
                        DayID : DayID,
                        StartSessions : formStart,
                        EndSessions : formEnd,
                        Status : Status
                    };

                    var url = base_url_js+'api/__crudScheduleExchange';
                    var dataToken = {
                        action : 'addSceduleEx',
                        dataInsert : dataInsert
                    };
                    var token2 = jwt_encode(dataToken,'UAP)(*');
                    $.post(url,{token:token2},function (jsonResult) {
                        // console.log(jsonResult);
                        $('#GlobalModal').modal('hide');
                        getDataAttendance();
                        toastr.success('Data saved','Success');

                    });

                }

                setTimeout(function () {
                    $('#btnSaveScheduleEx').prop('disabled',false).html('Save');
                    $('.btn-schedule-exchange,.form-ScEx').prop('disabled',false);
                },1000);
            });

        } else {
            toastr.warning('Form Required','Warning');
        }

    }
    
</script>

<!-- Input Attd Students -->
<script>

    $(document).on('click','.rdAttd',function () {
        var No = $(this).attr('data-no');
        var valu = $(this).val();
        // console.log(No);
        $('#trA'+No+',#trP'+No).css('background','none');

        if(valu=='1'){
            $('#trP'+No).css('background','#438848');
        } else {
            $('#trA'+No).css('background','#884343');
        }

        countP_A();

    });

    $(document).on('click','#ckAllP',function () {
        $('.trA,.trP').css('background','none');
        if($('#ckAllP').is(':checked')){
            $('.trP').css('background','#438848');
            $('.rd-trA').prop('checked',false);
            $('.rd-trP').prop('checked',true);

            $('#ckAllA').prop('checked',false);
        } else {
            $('.rd-trP').prop('checked',false);
        }
        countP_A();
    });

    $(document).on('click','#ckAllA',function () {
        $('.trA,.trP').css('background','none');
        if($('#ckAllA').is(':checked')){
            $('.trA').css('background','#884343');
            $('.rd-trP').prop('checked',false);
            $('.rd-trA').prop('checked',true);

            $('#ckAllP').prop('checked',false);
        } else {
            $('.rd-trA').prop('checked',false);
        }
        countP_A();
    });

    $(document).on('click','.inputStudentAttd',function () {

        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');

        var SemesterID = 13;

        var DataAttendance = $('#filterAttendance').val();
        var ScheduleID = DataAttendance.split('.')[1];
        var SDID = DataAttendance.split('.')[2];

        var url = base_url_js+'api/__crudAttendance';
        var data = {
            action : 'getAttdStudents',
            SemesterID : SemesterID,
            ScheduleID : ScheduleID,
            SDID : SDID,
            Meeting : No
        };
        var token = jwt_encode(data,'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {


            var body_attd = '<h3>Students Not Yet</h3>';
            if(jsonResult.length>0){
                body_attd = '<table class="table table-bordered table-striped" id="tableStdAttd">' +
                    '        <thead>' +
                    '        <tr style="background:#436f88;color: #ffffff;">' +
                    '            <th style="width: 1%;">No</th>' +
                    '            <th style="width: 10%;">NPM</th>' +
                    '            <th style="width: 35%;">Name</th>' +
                    '            <th  style="width: 5%;">' +
                    '                P' +
                    '                <br/>' +
                    '                <input id="ckAllP" type="checkbox">' +
                    '            </th>' +
                    '            <th style="width: 5%;">' +
                    '                A' +
                    '                <br/>' +
                    '                <input id="ckAllA" type="checkbox">' +
                    '            </th>' +
                    '            <th>Description</th>' +
                    '        </tr>' +
                    '        </thead>' +
                    '        <tbody id="trBody">' +
                    '        </tbody>' +
                    '    </table>';
            }

            $('#GlobalModalLarge .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Attendance Students | Pertemuan '+No+'</h4>');

            $('#GlobalModalLarge .modal-body').html(body_attd);

            if(jsonResult.length>0){

                window.totalMhs = jsonResult.length;
                var no_tr=1;
                for(var i=0;i<totalMhs;i++){
                    var dataRowStd = jsonResult[i];
                    var p = '';
                    var bgP = '';
                    var a = 'checked';
                    var bgA = 'style="background: #884343;"';

                    if(dataRowStd.Status==1){
                        p = 'checked';
                        bgP = 'style="background: #438848;"';
                        a = '';
                        bgA = '';
                    }

                    var dc = (dataRowStd.Description!='' && dataRowStd.Description!=null) ? dataRowStd.Description : '';

                    $('#trBody').append('<tr>' +
                        '<td>'+(no_tr++)+'</td>' +
                        '<td>'+dataRowStd.DetailStudent.NPM+'<input value="'+dataRowStd.ID_Attd_S+'" id="formID_Attd_S'+i+'" class="hide" hidden readonly/></td>' +
                        '<td style="text-align: left;font-weight: bold;">'+dataRowStd.DetailStudent.Name+'</td>' +
                        '<td class="trP" id="trP'+i+'" '+bgP+'><input type="radio" class="rdAttd rd-trP form-attd-students" data-mhs="'+totalMhs+'" data-no="'+i+'" name="optRAttd'+i+'" value="1" '+p+'></td>' +
                        '<td class="trA" id="trA'+i+'" '+bgA+'><input type="radio" class="rdAttd rd-trA form-attd-students" data-mhs="'+totalMhs+'" data-no="'+i+'" name="optRAttd'+i+'" value="2" '+a+'></td>' +
                        '<td><textarea class="form-control form-attd-students" id="formDesc'+i+'" rows="1">'+dc+'</textarea></td>' +
                        '</tr>');
                }
            }

            $('#GlobalModalLarge .modal-footer').html('Total : '+totalMhs+' | P : <span id="totalP"></span> | A : <span id="totalA"></span> | <button type="button" class="btn btn-default btn-attd-students" data-dismiss="modal">Close</button> ' +
                '<button class="btn btn-success" id="btnSaveAttdStudents" data-no="'+No+'" data-id="'+ID+'">Save</button>');
            $('#GlobalModalLarge').modal({
                'show' : true,
                'backdrop' : 'static'
            });

            countP_A();

        });


    });

    $(document).on('click','#btnSaveAttdStudents',function () {
        var Meeting = $(this).attr('data-no');
        var dataUpdate = [];
        for(var s=0;s<totalMhs;s++){
            var arrData = {
                ID : $('#formID_Attd_S'+s).val(),
                Status : $('input[type=radio][name=optRAttd'+s+']:checked').val(),
                Meeting : Meeting,
                Description : $('#formDesc'+s).val()
            }

            dataUpdate.push(arrData);
        }

        var data = {
            action : 'addAttdStudents',
            dataUpdate : dataUpdate
        };

        var url = base_url_js+'api/__crudAttendance';
        var token = jwt_encode(data,'UAP)(*');

        loading_buttonSm('#btnSaveAttdStudents');
        $('.form-attd-students,.btn-attd-students').prop('disabled',true);
        $.post(url,{token:token},function (jsonResult) {
            getDataAttendance();
            toastr.success('Data Saved','Success');
            setTimeout(function () {
                $('#GlobalModalLarge').modal('hide');
            },1000);
        });

    });

    function countP_A() {

        var p = 0;
        var a = 0;
        for(var i=0;i<totalMhs;i++){
            var valu = $('input[type=radio][name=optRAttd'+i+']:checked').val();
            if(valu=='1') {
                p += 1;
            } else {
                a += 1;
            }
        }

        if(totalMhs!=a){
            $('#ckAllA').prop('checked',false);
        } else {
            $('#ckAllA').prop('checked',true);
        }

        if(totalMhs!=p){
            $('#ckAllP').prop('checked',false);
        } else {
            $('#ckAllP').prop('checked',true);
        }

        $('#totalP').html(p);
        $('#totalA').html(a);
    }

</script>

<!-- Input Attd lecturers -->
<script>
    $(document).on('click','.inputLecturerAttd',function () {
        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');

        var url = base_url_js+'api/__crudAttendance';
        var data = {
            action : 'getAttdLecturers',
            ID : ID,
            No : No
        };
        var token = jwt_encode(data,'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {

            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">Attendance '+No+'</h4>');

            var attd_nip = jsonResult.NIP;
            var attd_bap = (jsonResult.BAP!='' && jsonResult.BAP!=null) ? jsonResult.BAP : '';
            // var attd_date = (jsonResult.Date!='' && jsonResult.Date!=null) ? jsonResult.Date : '';
            var attd_in = (jsonResult.In!='' && jsonResult.In!=null) ? jsonResult.In.substr(0,5) : '00:00';
            var attd_out = (jsonResult.Out!='' && jsonResult.Out!=null) ? jsonResult.Out.substr(0,5) : '00:00';


            var body_attd = '<div class="row">' +
                '                        <div class="col-xs-4">' +
                '                            <div class="form-group">' +
                '                                <label>Date</label>' +
                '                               <input type="text" id="formDate" class="form-control form-attd" readonly>' +
                '                            </div>' +
                '                            <div class="form-group">' +
                '                                <label>In</label>' +
                '                                <div id="inputIn" class="input-group">' +
                '                                    <input data-format="hh:mm" type="text" id="formIn" class="form-control form-attd" value="'+attd_in+'"/>' +
                '                                    <span class="add-on input-group-addon">' +
                '                                        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
                '                                    </span>' +
                '                                </div>' +
                '                            </div>' +
                '                            <div class="form-group">' +
                '                                <label>Out</label>' +
                '                                <div id="inputOut" class="input-group">' +
                '                                    <input data-format="hh:mm" type="text" id="formOut" class="form-control form-attd" value="'+attd_out+'"/>' +
                '                                    <span class="add-on input-group-addon">' +
                '                                      <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
                '                                    </span>' +
                '                                </div>' +
                '                            </div>' +
                '                        </div>' +
                '                        <div class="col-xs-8">' +
                '                           <div class="form-group">' +
                '                               <label>Lecturer</label>' +
                '                               <select class="form-control" id="formLecturer"></select>' +
                '                           </div>' +
                '                            <div class="form-group">' +
                '                                <label>BAP</label>' +
                '                                <textarea class="form-control" id="formBAP" rows="5">'+attd_bap+'</textarea>' +
                '                            </div>' +
                '                        </div>' +
                '                    </div>';

            $('#GlobalModal .modal-body').html(body_attd);

            for(var t=0;t<jsonResult.DetailLecturers.length;t++){
                var lec = jsonResult.DetailLecturers[t];
                var sc = (attd_nip==lec.NIP) ? 'selected' : '';
                $('#formLecturer').append('<option value="'+lec.NIP+'" '+sc+'>'+lec.NIP+' - '+lec.Name+'</option>');
            }

            $('#inputIn,#inputOut').datetimepicker({
                pickDate: false,
                pickSeconds : false
            });

            $("#formDate").datepicker({
                showOtherMonths:true,
                autoSize: true,
                dateFormat: 'dd MM yy'
            });

            if(jsonResult.Date !=='0000-00-00' && jsonResult.Date != null){
                var d = new Date(jsonResult.Date);

                $('#formDate').datepicker('setDate',d);
            }

            $('a.ui-state-default').attr('href','javascript:void(0)');

            $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> ' +
                '<button class="btn btn-success" id="btnSaveAttdLecturer" data-no="'+No+'" data-id="'+ID+'">Save</button>');
            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });
        });

    });

    $(document).on('click','#btnSaveAttdLecturer',function () {
        var ID = $(this).attr('data-id');
        var No = $(this).attr('data-no');


        var formDate = $('#formDate').datepicker("getDate");
        var formIn = $('#formIn').val();
        var formOut = $('#formOut').val();
        var formBAP = $('#formBAP').val();
        var NIP = $('#formLecturer').val();


        if(formDate!=null && formDate!='' &&
            formIn!=null && formIn!='' &&
            formOut!=null && formOut!='' &&
            formBAP!=null && formBAP!=''){

            $('#formDate,#formIn,#formOut,#formBAP').prop('disabled',true);
            loading_buttonSm('#btnSaveAttdLecturer');

            var url = base_url_js+'api/__crudAttendance';
            var data = {
                action : 'UpdtAttdLecturers',
                ID : ID,
                No : No,
                formUpdate : {
                    NIP : NIP,
                    Date : moment(formDate).format('YYYY-MM-DD'),
                    In : formIn,
                    Out : formOut,
                    BAP : formBAP
                }
            };
            var token = jwt_encode(data,'UAP)(*');

            $.post(url,{token:token},function (resultJson) {
                getDataAttendance();
                toastr.success('Data Saved','Success');
                $('#GlobalModal').modal('hide');
            });

        } else {
            toastr.warning('Form Required','Warning');
        }


    });

</script>