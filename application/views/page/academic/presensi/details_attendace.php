
<style>
    #tableMonAttd tr th {
        text-align: center;
        background: #607d8b;
        color: #FFFFFF;
    }
    #tableMonAttd tr td {
        text-align: center;
    }
    .btnDeleteAttd {
        padding: 0px 4px;
        border-radius: 11px;
    }
</style>

<div class="row">
    <div class="col-md-3">
        <a href="<?php echo base_url('academic/attendance/input-attendace'); ?>" class="btn btn-warning"><i class="fa fa-arrow-left margin-right"></i> Back to List</a>
    </div>
    <div class="col-md-6">
        <div class="" style="text-align: center;background: lightyellow;padding: 15px;border: 1px solid #CCCCCC;margin-bottom: 15px;">
            <h3 style="margin-top: 0px;" id="viewCourse">-</h3>
        </div>
        <textarea id="viewLecturer" class="hide"></textarea>
    </div>
</div>

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="well">
            <select class="form-control" id="filterSD"></select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12" style="text-align: right;margin-bottom: 15px;">
        <hr/>
        <button class="btn btn-default btnEdAttd" disabled id="btnBAP"><i class="fa fa-edit margin-right"></i> BAP</button> |
        <button class="btn btn-primary btnEdAttd" disabled id="btnLecAttd"><i class="fa fa-edit margin-right"></i> Lecturer Attendance</button> |
        <button class="btn btn-success btnEdAttd" disabled id="btnStdAttd"><i class="fa fa-edit margin-right"></i> Student Attendance</button>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="divLoadAttd"></div>
    </div>
</div>

<script>
    $(document).ready(function () {
        window.ScheduleID = "<?php echo $ScheduleID; ?>";
        loadSchedule();

        var loadFirs = setInterval(function () {
            var filterSD = $('#filterSD').val();
            if(filterSD!='' && filterSD!=null){
                loadAttendace();
                clearInterval(loadFirs);
            }
        },1000);

    });

    function loadSchedule() {
        var data = {
            action : 'loadScheduleDetails',
            ScheduleID : ScheduleID
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api2/__crudAttendance2';

        $.post(url,{token:token},function (jsonResult) {

            var Course = jsonResult.Course[0];
            $('#viewCourse').html(Course.MKCode+' - '+Course.CourseEng+'<br/><small>Group : '+Course.ClassGroup+'</small>');

            $('#viewLecturer').val(JSON.stringify(jsonResult.Lecturer));

            $.each(jsonResult.Schedule,function (index, val) {
                var sesi = val.StartSessions.substr(0,5)+' - '+val.EndSessions.substr(0,5)+' | '+val.Room;
                $('#filterSD').append('<option value="'+val.SDID+'.'+val.ID_Attd+'">'+val.DayEng+', '+sesi+'</option>');
            });

        });
    }

    function loadAttendace() {

        $('.btnEdAttd').prop('disabled',true);


        var filterSD = $('#filterSD').val();
        if(filterSD!='' && filterSD!=null){

            loading_page('#divLoadAttd');

            var SDID = filterSD.split('.')[0];

            var data = {
                action : 'readDetailAttendance',
                ScheduleID : ScheduleID,
                SDID : SDID
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api2/__crudAttendance2';

            $.post(url,{token:token},function (jsonResult) {

                setTimeout(function () {
                    $('.btnEdAttd').prop('disabled',false);
                    $('#divLoadAttd').html('<table class="table table-bordered table-striped" id="tableMonAttd">' +
                        '            <thead>' +
                        '            <tr>' +
                        '                <th rowspan="2" style="width: 1%;">Sessions</th>' +
                        '                <th rowspan="2" style="width: 15%;">Subject</th>' +
                        '                <th rowspan="2" style="width: 15%;">Material</th>' +
                        '                <th rowspan="2" style="width: 15%;">Description</th>' +
                        '                <th colspan="2" style="width: 15%;">Attendance</th>' +
                        '                <th colspan="2">Signature</th>' +
                        '            </tr>' +
                        '            <tr>' +
                        '                <th style="width: 5%;">P</th>' +
                        '                <th style="width: 5%;">A</th>' +
                        '                <th style="width: 25%;">Lecturer</th>' +
                        '                <th style="width: 15%;">Student</th>' +
                        '            </tr>' +
                        '            </thead>' +
                        '            <tbody id="dataRowBAB"></tbody>' +
                        '        </table>');

                    var no =1;
                    for(var i=0;i<jsonResult.length;i++){
                        var d = jsonResult[i];
                        var dataLecturer = d.Lecturer;
                        var dataBAP = d.BAP;

                        var Subject = (dataBAP.length>0) ? dataBAP[0].Subject : '';
                        var Material = (dataBAP.length>0) ? dataBAP[0].Material : '';
                        var Description = (dataBAP.length>0) ? dataBAP[0].Description : '';

                        var dataBAPToReset = (dataBAP.length>0) ? 1 : 0;

                        var lecturer = '';
                        if(dataLecturer.length>0){
                            for(var l=0;l<dataLecturer.length;l++){
                                var d_l = dataLecturer[l];
                                var Start = (d_l.In !='' && d_l.In !=null) ? d_l.In.substr(0,5) : '';
                                var End = (d_l.Out !='' && d_l.Out !=null) ? d_l.Out.substr(0,5) : '';

                                var time = (Start!='' && End!='') ? '<span style="color: #2196f3;">'+Start+' - '+End+'</span>' : '';

                                var date = moment(d_l.Date).format('dddd, DD MMM YYYY');
                                lecturer = lecturer+'<div><i class="fa fa-user margin-right"></i> '+d_l.Lecturer+' | <button data-id="'+d_l.ID+'" class="btn btn-sm btn-default btn-default-danger btnDeleteAttd"><i class="fa fa-minus-circle"></i></button> <br/><span style="color: #ff5722;">'+date+' | '+time+'</span></div>';
                            }
                        }

                        var btnSignStudent = (dataBAP.length>0 && dataBAP[0].StudentSignBy!=null && dataBAP[0].StudentSignBy!='')
                            ? '<div style="text-align: left;">'+dataBAP[0].StudentSignBy+' - '+dataBAP[0].Student+'<br/><span style="color: #9E9E9E;">'+moment(dataBAP[0].StudentSignAt).format('dddd, DD MMM YYYY HH:mm')+'</span></div>'
                            : '-';

                        $('#dataRowBAB').append('<tr>' +
                            '<td>'+no+'</td>' +
                            '<td style="text-align: left;"><span class="viewerBAP viewerBAP'+no+'" id="viewSubject'+no+'">'+Subject+'</span><textarea class="form-control hide formBAP formBAP'+no+'" rows="3" id="formSubject'+no+'">'+Subject+'</textarea></td>' +
                            '<td style="text-align: left;"><span class="viewerBAP viewerBAP'+no+'" id="viewMaterial'+no+'">'+Material+'</span><textarea class="form-control hide formBAP formBAP'+no+'" rows="3" id="formMaterial'+no+'">'+Material+'</textarea></td>' +
                            '<td style="text-align: left;"><span class="viewerBAP viewerBAP'+no+'" id="viewDescription'+no+'">'+Description+'</span><textarea class="form-control hide formBAP formBAP'+no+'" rows="3" id="formDescription'+no+'">'+Description+'</textarea></td>' +
                            '<td>'+d.Present+'</td>' +
                            '<td>'+d.Absent+'</td>' +
                            '<td style="text-align: left;">'+lecturer+'</td>' +
                            '<td>'+btnSignStudent+'</td>' +
                            '</tr>');
                        no+=1;
                    }
                },500);


            });

        }



    }

    $('#filterSD').change(function () {
        loadAttendace();
    });

    $('#btnBAP').click(function () {
        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">BAP</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-4 col-md-offset-4">' +
            '        <div class="form-group">' +
            '            <label>Sesi</label>' +
            '            <select class="form-control" id="formBAP_Sesi"></select>' +
            '        </div><hr/>' +
            '    </div>' +
            '</div>' +
            '<div id="divViewBap"></div>' +
            '<div class="row">' +
            '    <div class="col-md-12" style="text-align: right;">' +
            '        <hr/>' +
            '        <button type="button" class="btn btn-success" id="btnActSubmitBAP">Submit</button> | ' +
            '        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '    </div>' +
            '</div>');

        for(var i=1;i<=14;i++){
            $('#formBAP_Sesi').append('<option value="'+i+'">'+i+'</option>');
        }

        loadBAP();

        $('#formBAP_Sesi').change(function () {
            loadBAP();
        });

        $('#btnActSubmitBAP').click(function () {

            var Sesi = $('#formBAP_Sesi').val();

            var filterSD = $('#filterSD').val();
            var ID_Attd = filterSD.split('.')[1];

            var formBAP_Subject = $('#formBAP_Subject').val();
            var formBAP_Material = $('#formBAP_Material').val();
            var formBAP_Description = $('#formBAP_Description').val();

            var data = {
              action : ''
            };

        });

        $('#GlobalModal .modal-footer').addClass('hide');
        // $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
        //     '<button type="button" class="btn btn-primary"><i class="fa fa-paper-plane-o right-margin" aria-hidden="true"></i> Publish</button>');

        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });
    });

    function loadBAP() {

        loading_page('#divViewBap');

        var formBAP_Sesi = $('#formBAP_Sesi').val();
        var Sesi = (formBAP_Sesi!='' && formBAP_Sesi!=1 && formBAP_Sesi!=null && formBAP_Sesi!='1')
            ? formBAP_Sesi
            : 1;

        var filterSD = $('#filterSD').val();

        var ID_Attd = filterSD.split('.')[1];

        var data = {
            action : 'loadBAP',
            Sesi : Sesi,
            ID_Attd : ID_Attd
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api2/__crudAttendance2';

        $.post(url,{token:token},function (jsonResult) {

            setTimeout(function () {
                $('#divViewBap').html(  '<div class="row">' +
                    '    <div class="col-md-12">' +
                    '        <div class="form-group">' +
                    '            <label>Subject</label>' +
                    '            <textarea class="form-control" id="formBAP_Subject" rows="4"></textarea>' +
                    '        </div>' +
                    '    </div>' +
                    '    <div class="col-md-12">' +
                    '        <div class="form-group">' +
                    '            <label>Material</label>' +
                    '            <textarea class="form-control" id="formBAP_Material" rows="4"></textarea>' +
                    '        </div>' +
                    '    </div>' +
                    '    <div class="col-md-12">' +
                    '        <div class="form-group">' +
                    '            <label>Description</label>' +
                    '            <textarea class="form-control" id="formBAP_Description" rows="4"></textarea>' +
                    '        </div>' +
                    '    </div>' +
                    '</div>');

                var formBAP_Subject = (jsonResult.length>0) ? jsonResult[0].Subject : '';
                var formBAP_Material = (jsonResult.length>0) ? jsonResult[0].Material : '';
                var formBAP_Description = (jsonResult.length>0) ? jsonResult[0].Description : '';

                $('#formBAP_Subject').val(formBAP_Subject);
                $('#formBAP_Material').val(formBAP_Material);
                $('#formBAP_Description').val(formBAP_Description);

            },500);

        });

    }

    $('#btnLecAttd').click(function () {
        $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title">Lecturer Attendance</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-4">' +
            '        <div class="form-group">' +
            '            <label>Sesi</label>' +
            '            <select class="form-control" id="formLecAttd_Sesi"></select>' +
            '        </div>' +
            '    </div>' +
            '    <div class="col-md-8">' +
            '        <div class="form-group">' +
            '            <label>Lecturer</label>' +
            '            <select class="form-control" id="formLecAttd_Lecturer"></select>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<div class="row">' +
            '    <div class="col-md-6">' +
            '        <label>Date</label>' +
            '        <input class="hide" id="formLecAttd_Date">' +
            '        <input class="form-control" id="viewDate">' +
            '    </div>' +
            '    <div class="col-md-3">' +
            '        <label>Start</label>' +
            '        <div id="div_formSesiAwal" data-no="1" class="input-group">' +
            '            <input data-format="hh:mm" type="text" id="formLecAttd_Start" class="form-control" value="00:00"/>' +
            '            <span class="add-on input-group-addon">' +
            '                <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
            '            </span>' +
            '        </div>' +
            '    </div>' +
            '    <div class="col-md-3">' +
            '        <label>End</label>' +
            '        <div id="div_formSesiEnd" data-no="1" class="input-group">' +
            '            <input data-format="hh:mm" type="text" id="formLecAttd_End" class="form-control" value="00:00"/>' +
            '            <span class="add-on input-group-addon">' +
            '                <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>' +
            '            </span>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<div class="row">' +
            '    <div class="col-md-12" style="text-align: right;">' +
            '       <hr/>' +
            '        <button class="btn btn-success" id="btnSubmitLecAttd">Submit</button> | ' +
            '        <button class="btn btn-default" data-dismiss="modal">Close</button>' +
            '    </div>' +
            '</div>');

        for(var i=1;i<=14;i++){
            $('#formLecAttd_Sesi').append('<option value="'+i+'">'+i+'</option>');
        }

        // Load Lecturer
        var viewLecturer = JSON.parse($('#viewLecturer').val());
        if(viewLecturer.length>0){
            $.each(viewLecturer,function (i, v) {
                $('#formLecAttd_Lecturer').append('<option value="'+v.NIP+'">'+v.NIP+' - '+v.Name+'</option>');
            });
        }

        $( "#viewDate" ).datepicker({
                showOtherMonths:true,
                autoSize: true,
                dateFormat: 'dd MM yy',
                // minDate: new Date(moment().year(),moment().month(),moment().date()),
                onSelect : function () {
                    var d = moment($(this).datepicker("getDate")).format('YYYY-MM-DD');
                    $('#formLecAttd_Date').val(d);
                }
            });

        $('#div_formSesiAwal,#div_formSesiEnd').datetimepicker({
            pickDate: false,
            pickSeconds : false
        });

        $('#GlobalModal .modal-footer').addClass('hide');
        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });


        $('#btnSubmitLecAttd').click(function () {

            var formLecAttd_Sesi = $('#formLecAttd_Sesi').val();
            var formLecAttd_Lecturer = $('#formLecAttd_Lecturer').val();
            var formLecAttd_Date = $('#formLecAttd_Date').val();
            var formLecAttd_Start = $('#formLecAttd_Start').val();
            var formLecAttd_End = $('#formLecAttd_End').val();

            if(formLecAttd_Sesi!=null && formLecAttd_Sesi!='' &&
                formLecAttd_Lecturer!=null && formLecAttd_Lecturer!='' &&
                formLecAttd_Date!=null && formLecAttd_Date!='' &&
                formLecAttd_Start!=null && formLecAttd_Start!='' &&
                formLecAttd_End!=null && formLecAttd_End!=''){

                loading_buttonSm('#btnSubmitLecAttd');
                $('button[data-dismiss=modal]').prop('disabled',true);

                var filterSD = $('#filterSD').val();
                var ID_Attd = filterSD.split('.')[1];

                var data = {
                    action : 'updateAttendanceLecturer',
                    ScheduleID : ScheduleID,
                    ID_Attd : ID_Attd,
                    Meet : formLecAttd_Sesi,
                    NIP : formLecAttd_Lecturer,
                    Date : formLecAttd_Date,
                    In : formLecAttd_Start,
                    Out : formLecAttd_End
                };

                var token = jwt_encode(data,'UAP)(*');
                var url = base_url_js+'api2/__crudAttendance2';

                $.post(url,{token:token},function (jsonResult) {
                    toastr.success('Attendance Saved','Success');
                    loadAttendace();
                    setTimeout(function () {
                        $('#GlobalModal').modal('hide');
                    },500);
                });

            } else {
                toastr.warning('All form required','Warning');
            }

        });
    });

    $('#btnStdAttd').click(function () {

        $('#GlobalModal .modal-header').html('<h4 class="modal-title">Students Attendance</h4>');
        $('#GlobalModal .modal-body').html('<div class="row">' +
            '    <div class="col-md-4 col-md-offset-4">' +
            '        <div class="">' +
            '        <div class="form-group">' +
            '            <label>Sesi</label>' +
            '            <select class="form-control" id="formAttdStd_Sesi"></select>' +
            '               <input id="formAttdStd_TotalStd" class="hide">' +
            '        </div><hr/>' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '' +
            '<div class="row">' +
            '    <div class="col-md-12">' +
            '       <div id="divStdAttd"></div>' +
            '        <hr>' +
            '        <div style="text-align: right;">' +
            '            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> | ' +
            '            <button type="button" class="btn btn-success" id="btnSubmitAttdStd" disabled>Submit</button>' +
            '        </div>' +
            '    </div>' +
            '</div>');
        $('#GlobalModal .modal-footer').addClass('hide');

        for(var i=1;i<=14;i++){
            $('#formAttdStd_Sesi').append('<option value="'+i+'">'+i+'</option>');
        }

        $('#formAttdStd_Sesi').change(function () {
            loadStdAttd();
        });


        $('#GlobalModal').modal({
            'show' : true,
            'backdrop' : 'static'
        });

        loadStdAttd();

        $('#btnSubmitAttdStd').click(function () {
            var Sesi = $('#formAttdStd_Sesi').val();
            var formAttdStd_TotalStd = $('#formAttdStd_TotalStd').val();

            if(parseInt(formAttdStd_TotalStd)>0){

                loading_buttonSm('#btnSubmitAttdStd');
                $('button[data-dismiss=modal]').prop('disabled',true);

                var attdStudent = [];
                for(var i=1;i<=formAttdStd_TotalStd;i++){
                    var m = ($('#formAttdStd_Attd'+i).is(':checked')) ? '1' : '2';
                    var arr = {
                        ID : $('#formAttdStd_ID'+i).val(),
                        M : m,
                        D : $('#formAttdStd_Reason'+i).val()
                    };
                    attdStudent.push(arr);
                }

                var data = {
                    action : 'UpdateStudentAttd',
                    Meet : Sesi,
                    attdStudent : attdStudent
                };

                var token = jwt_encode(data,'UAP)(*');
                var url = base_url_js+'api2/__crudAttendance2';

                $.post(url,{token:token},function (result) {
                    toastr.success('Attendance Saved','Success');
                    loadAttendace();
                    setTimeout(function () {
                        $('button[data-dismiss=modal],#btnSubmitAttdStd').prop('disabled',false);
                        $('#btnSubmitAttdStd').html('Submit');
                    },500);
                });

            } else {
                toastr.warning('Students Not Yet','Warning');
            }

        });

    });

    function loadStdAttd() {

        loading_page('#divStdAttd');
        $('#btnSubmitAttdStd').prop('disabled',true);

        var formAttdStd_Sesi = $('#formAttdStd_Sesi').val();
        var Meet = (formAttdStd_Sesi!=1 && formAttdStd_Sesi!='' && formAttdStd_Sesi!=null) ? formAttdStd_Sesi : 1;

        var filterSD = $('#filterSD').val();

        var ID_Attd = filterSD.split('.')[1];
        var data = {
            action : 'readAttdStudent',
            Meet : Meet,
            ID_Attd : ID_Attd
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api2/__crudAttendance2';

        $.post(url,{token:token},function (jsonResult) {

            setTimeout(function () {
                $('#divStdAttd').html('<table class="table">' +
                    '    <thead>' +
                    '    <tr>' +
                    '        <th style="width: 1%;">No</th>' +
                    '        <th>Students</th>' +
                    '        <th style="width: 15%;">Attd</th>' +
                    '        <th style="width: 25%;">Reason</th>' +
                    '    </tr>' +
                    '    </thead>' +
                    '    <tbody id="rowAtdStd"></tbody>' +
                    '   <tr style="background: #f4f4f4;">' +
                    '       <td style="text-align: right;" colspan="2">Total</td>' +
                    '       <td><span id="viewStd"></span></td>' +
                    '       <td>-</td>' +
                    '   </tr>' +
                    '</table>');

                var StatusMeet = jsonResult.Attendance[0].StatusMeet;
                var DefaultAttendance = jsonResult.Setting[0].DefaultAttendance;

                $('#formAttdStd_TotalStd').val(jsonResult.Students.length);
                if(jsonResult.Students.length>0){
                    var no=1;
                    $.each(jsonResult.Students,function (i,v) {

                        var check = 'checked';
                        if(StatusMeet==1 || StatusMeet=='1'){
                            check = (v.M==1 || v.M=='1') ? 'checked' : '' ;
                        } else {
                            check = (DefaultAttendance==1 || DefaultAttendance=='1') ? 'checked' : '' ;
                        }

                        var d = (v.D!=null && v.D!='') ? v.D: '';

                        $('#rowAtdStd').append('<tr id="trStd_'+no+'">' +
                            '<td>'+no+'</td>' +
                            '<td>' +
                            '<span style="font-size: 15px;">'+v.Name+'</span><br/>'+v.NPM+'<input class="hide" value="'+v.ID+'" id="formAttdStd_ID'+no+'">' +
                            '</td>' +
                            '<td>' +
                            '       <div class="checkbox checbox-switch switch-success ">' +
                            '           <label>' +
                            '               <input type="checkbox" class="checkAttdStd" '+check+' id="formAttdStd_Attd'+no+'">' +
                            '               <span></span>' +
                            '           </label>' +
                            '       </div>' +
                            '</td>' +
                            '<td><textarea class="form-control" id="formAttdStd_Reason'+no+'" rows="2">'+d+'</textarea></td>' +
                            '</tr>');

                        no+=1;
                    });
                    checkAttd();
                }

                $('#btnSubmitAttdStd').prop('disabled',false);
            },500);

        });
    }

    $(document).on('change','.checkAttdStd',function () {
        checkAttd();
    });

    function checkAttd() {
        var formAttdStd_TotalStd = $('#formAttdStd_TotalStd').val();

        var p = 0;
        if(parseInt(formAttdStd_TotalStd)>0){
            for(var i=1;i<=formAttdStd_TotalStd;i++){
                if($('#formAttdStd_Attd'+i).is(':checked')){
                    $('#trStd_'+i).css({
                        'background': '#ffffff',
                        'color': '#333'
                    });
                    p += 1;
                } else {
                    $('#trStd_'+i).css({
                        'background': '#ff000014',
                        'color': 'red'
                    });
                }
            }
        }

        $('#viewStd').html('<span style="color: blue;">'+p+'</span> of '+formAttdStd_TotalStd);

    }

    // Remove Attendance Lecturer
    $(document).on('click','.btnDeleteAttd',function () {

        if(confirm('Are you sure to remove?')){
            var ID = $(this).attr('data-id');
            var data = {
                action : 'deleteAttendanceLecturer',
                ID : ID
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api2/__crudAttendance2';

            $.post(url,{token:token},function (result) {
                toastr.success('Data removed','Success');
                loadAttendace();
            });
        }



    });


</script>