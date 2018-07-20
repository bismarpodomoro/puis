<style>
    #tableDataScore thead tr th,#tableDataScore tbody tr td {
        text-align: center;
    }

    #tableDataScore thead tr {
        background-color: #436888;color: #ffffff;
    }
</style>

<div class="col-md-12">
    <div class="row">
        <div class="col-md-4">
            <div class="">
                <label>Semester Antara</label>
                <input type="checkbox" id="formSemesterAntara" data-toggle="toggle" data-style="ios"/>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="thumbnail" style="margin-top: 30px;">
                <div class="row">
                    <div class="col-md-4">
                        <select id="filterSemester" class="form-control filter-score"></select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control filter-score" id="filterCombine">
                            <option value="0">Combine Class No</option>
                            <option value="1">Combine Class Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterBaseProdi" class="form-control filter-score"></select>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <hr/>

    <div class="row">
        <div class="col-md-12" id="divPageScore">
        </div>
    </div>

</div>


<script>
    $(document).ready(function () {

        $('#filterSemester,#filterBaseProdi').empty();
        $('#filterSemester').append('<option value="" disabled selected>-- Year Academic--</option>' +
            '                <option disabled>------------------------------------------</option>');
        loSelectOptionSemester('#filterSemester','');


        $('#filterBaseProdi').append('<option value="" disabled selected>-- Select Program Study --</option>' +
            '<option disabled>------------------------------------------</option>');
        loadSelectOptionBaseProdi('#filterBaseProdi','');
    });

    $(document).on('change','.filter-score',function () {
        loadCourse();
    });


    function loadCourse() {
        var filterSemester = $('#filterSemester').val();
        var filterCombine = $('#filterCombine').val();
        var filterBaseProdi = $('#filterBaseProdi').val();
        var IsSemesterAntara = '0';

        if(filterSemester!='' && filterSemester!=null && filterBaseProdi!='' && filterBaseProdi!=null){

            var SemesterID = filterSemester.split('.')[0];
            var ProdiID = filterBaseProdi.split('.')[0];

            var data = {
                action : 'dataCourse',
                SemesterID : SemesterID,
                ProdiID : ProdiID,
                CombinedClasses : filterCombine,
                IsSemesterAntara : IsSemesterAntara
            };

            var token = jwt_encode(data,'UAP)(*');

            var url = base_url_js+'api/__crudScore';

            loading_page('#divPageScore');
            $.post(url,{token:token},function (jsonResult) {

                setTimeout(function () {
                    if(jsonResult.length>0){

                        $('#divPageScore').html('<table class="table table-bordered" id="tableDataScore">' +
                            '                <thead>' +
                            '                <tr>' +
                            '                    <th rowspan="2" style="width: 2%;">No</th>' +
                            '                    <th rowspan="2" style="width: 7%;">Group</th>' +
                            '                    <th rowspan="2">Course</th>' +
                            '                    <th rowspan="2" style="width: 5%;">Credit</th>' +
                            '                    <th rowspan="2" style="width: 20%;">Lecturer</th>' +
                            '                    <th rowspan="2" style="width: 5%;">Silabus SAP</th>' +
                            '                    <th rowspan="2" style="width: 5%;">Act</th>' +
                            '                    <th colspan="2">Schedule</th>' +
                            '                </tr>' +
                            '                <tr>' +
                            '                    <th style="width: 25%;">Day, time | Room</th>' +
                            '                </tr>' +
                            '                </thead>' +
                            '                <tbody id="dataCourse"></tbody>' +
                            '            </table>');

                        var tr = $('#dataCourse');
                        tr.empty();

                        var no = 1;
                        for(var i=0;i<jsonResult.length;i++){
                            var dataC = jsonResult[i];

                            var schedule = '';
                            for(var c=0;c<dataC.DetailSchedule.length;c++){
                                var dd_c = dataC.DetailSchedule[c];
                                var sc_ = dd_c['DayEng']+', '+dd_c['StartSessions'].substr(0,5)+' - '+dd_c['EndSessions'].substr(0,5)+' | '+dd_c['Room'];

                                var br = (c!=0 && c!= (dataC.DetailSchedule.length)) ? '<br/>' : '';

                                schedule = schedule+''+br+''+sc_;

                            }

                            var Team = '';
                            if(dataC.DetailTeamTeaching.length>0){
                                for(var t=0;t<dataC.DetailTeamTeaching.length;t++){
                                    var tc =  dataC.DetailTeamTeaching[t];
                                    var br = '<br/> - ';
                                    Team = Team+''+br+''+tc.Name;
                                }
                            }

                            var btnAct = '<div class="btn-group">' +
                                '  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                                '    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span class="caret"></span>' +
                                '  </button>' +
                                '  <ul class="dropdown-menu">' +
                                '    <li><a href="javascript:void(0);" class="btnInputScore" data-nip="'+dataC.Coordinator+'" data-id="'+dataC.ScheduleID+'">Input Score</a></li>' +
                                '    <li><a href="javascript:void(0);" class="btnGrade" data-page="InputGrade1" data-group="'+dataC.Classgroup+'" data-id="'+dataC.ScheduleID+'">Grade Approval</a></li>' +
                                '    <li role="separator" class="divider"></li>' +
                                '    <li><a href="javascript:void(0);" class="inputScheduleExchange" data-no="'+i+'" data-id="">Cetak Report UTS</a></li>' +
                                '    <li><a href="javascript:void(0);" class="inputScheduleExchange" data-no="'+i+'" data-id="">Cetak Report UAS</a></li>' +
                                '  </ul>' +
                                '</div>';


                            var silabusSAP = '-';
                            if(dataC.dataSilabusSAP.length>0 && dataC.dataSilabusSAP[0].Status=='1'){
                                silabusSAP = '<i class="fa fa-question-circle" style="color: blue;"></i>';
                            } else if(dataC.dataSilabusSAP.length>0 && dataC.dataSilabusSAP[0].Status=='2'){
                                silabusSAP = '<i class="fa fa-check-circle" style="color: green;"></i>';
                            } else if(dataC.dataSilabusSAP.length>0 && dataC.dataSilabusSAP[0].Status=='0'){
                                silabusSAP = '<i class="fa fa-repeat"></i>';
                            }

                            tr.append('<tr>' +
                                '<td>'+(no++)+'</td>' +
                                '<td>'+dataC.Classgroup+'</td>' +
                                '<td style="text-align: left;"><b>'+dataC.MKNameEng+'</b><br/><i>'+dataC.MKName+'</i></td>' +
                                '<td>'+dataC.Credit+'</td>' +
                                '<td style="text-align: left;">(C) '+dataC.CoordinatorName+''+Team+'</td>' +
                                '<td>'+silabusSAP+'</td>' +
                                '<td>'+btnAct+'</td>' +
                                // '<td>'+dataC.Classgroup+'</td>' +
                                '<td style="text-align: right;">'+schedule+'</td>' +
                                '</tr>');
                        }
                    } else {
                        $('#divPageScore').html('<h4>Data not yet</h4>');
                    }
                },1000);

            })
        }


    }
</script>

<script>
    $(document).on('click','.btnInputScore',function () {
        var NIP = $(this).attr('data-nip');
        var ScheduleID = $(this).attr('data-id');
        var data = {
            NIP : NIP,
            ScheduleID : ScheduleID
        };
        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'academic/inputScore';
        loading_page('#divPageScore');
        $.post(url,{token:token},function (html) {
            setTimeout(function () {
                $('#divPageScore').html(html);
            },1000);
        });
    });
</script>

<!-- Grade -->
<script>
    $(document).on('click','.btnGrade',function () {

        var ScheduleID = $(this).attr('data-id');
        var ClassGroup = $(this).attr('data-group');

        var url = base_url_js+'api/__crudScore';

        var token = jwt_encode({action:'getGrade',ScheduleID:ScheduleID},'UAP)(*');

        $.post(url,{token:token},function (jsonResult) {

            var bodyGrade = '<div style="text-align:center;"><h3>Belum Input Grade</h3></div>';
            if(jsonResult.length>0){
                var dataG = jsonResult[0];

                var silabus = (dataG.Silabus!=null && dataG.Silabus!='') ? '<a target="_blank" href="'+base_url_portal_lecturers+'uploads/silabus/'+dataG.Silabus+'">Download Silabus</a>' : 'Belum Upload';
                var sap = (dataG.SAP!=null && dataG.SAP!='') ? '<a target="_blank" href="'+base_url_portal_lecturers+'uploads/sap/'+dataG.SAP+'">Download SAP</a>' : 'Belum Upload';
                var status = 'Not Yet Send Grade';
                var btnAct = 'disabled';
                var btnCheck = '';
                if(dataG.Status=='1') {
                    btnAct = '';
                    btnCheck = '';
                    status = 'Waiting Approval';
                } else if(dataG.Status=='2') {
                    btnCheck = '';
                    status = '<i class="fa fa-check-circle" style="color: green;"></i> Approved';
                }

                bodyGrade = '<h4>Silabus & SAP</h4>' +
                    '                    <table class="table">' +
                    '                        <tr>' +
                    '                            <td style="width: 50%;">'+silabus+'</td>' +
                    '                            <td style="width: 50%;">'+sap+'</td>' +
                    '                        </tr>' +
                    '                    </table>' +
                    '                    <h4>Grade</h4>' +
                    '                    <table class="table">' +
                    '                        <tr>' +
                    '                            <td style="width: 20%;">Assigment</td>' +
                    '                            <td style="width: 20%;">UTS</td>' +
                    '                            <td style="width: 20%;">UAS</td>' +
                    '                            <td style="width: 20%;">Status</td>' +
                    '                        </tr>' +
                    '                        <tr>' +
                    '                            <td>'+dataG.Assigment+' %</td>' +
                    '                            <td>'+dataG.UTS+' %</td>' +
                    '                            <td>'+dataG.UAS+' %</td>' +
                    '                            <td id="viewStatus'+dataG.ID+'">'+status+'</td>' +
                    '                        </tr>' +
                    '                        <tr>' +
                    '                            <td colspan="4" style="text-align: right;">' +
                    '                                <button data-id="'+dataG.ID+'" id="btnGradeApprove" class="btn btn-default btn-default-success" '+btnAct+'>Approved</button>' +
                    '                            </td>' +
                    '                        </tr>' +
                    '                    </table>' +
                    '                    <hr/>' +
                    '                    <div class="checkbox">' +
                    '                        <label>' +
                    '                            <input id="checkGradeAgain" type="checkbox" value="'+dataG.ID+'" '+btnCheck+'> Berikan Akses Untuk Input Ulang Silabus & SAP' +
                    '                        </label>' +
                    '                    </div>';


            } else {

            }

            $('#GlobalModal .modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h4 class="modal-title">'+ClassGroup+'</h4>');
            $('#GlobalModal .modal-body').html(bodyGrade);

            if(dataG.Status=='0'){
                $('#checkGradeAgain').prop('checked',true);
            }


            $('#GlobalModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>');
            $('#GlobalModal').modal({
                'show' : true,
                'backdrop' : 'static'
            });

        });

    });

    $(document).on('click','#btnGradeApprove',function () {

        var ID = $(this).attr('data-id');
        loading_button('#btnGradeApprove');

        var url = base_url_js+'api/__crudScore';
        var token = jwt_encode({action:'gradeUpdate',ID:ID,Status:'2'},'UAP)(*');
        $.post(url,{token:token},function (result) {
            loadCourse();
            setTimeout(function () {
                $('#btnGradeApprove').html('Approved');
                $('#viewStatus'+ID).html('<i class="fa fa-check-circle" style="color: green;"></i> Approved');
                toastr.success('Grade Approved','Saved');
            },500);

        })
    });

    $(document).on('change','#checkGradeAgain',function () {
        var ID = $('#checkGradeAgain').val();
        var url = base_url_js+'api/__crudScore';
        var Status = '0';

        if($('#checkGradeAgain').is(':checked')){
            Status = '0';
            $('#btnGradeApprove').prop('disabled',true);
            $('#viewStatus'+ID).html('Not Yet Send Grade');
        } else {
            Status = '1';
            $('#btnGradeApprove').prop('disabled',false);
            $('#viewStatus'+ID).html('Waiting Approval');
        }

        var token = jwt_encode({action:'gradeUpdate',ID:ID,Status:Status},'UAP)(*');
        $.post(url,{token:token},function (result) {
            loadCourse();
            toastr.success('Grade Approved','Saved');
        });
    });

    $(document).on('click','#btnBackFromInputScore',function () {
        loadCourse();
    })
</script>

<!-- Input Score -->
<script>
    $(document).on('change','#formTotalAsg',function () {
        var valu = $('#formTotalAsg').val();

        disabledAssigment(valu);
    });

    function disabledAssigment(valu) {
        $('.formAsg').prop('disabled',false);
        for(var d=(parseInt(valu)+1); d<=5;d++ ){
            $('.formAsg'+d).val(0).prop('disabled',true);
        }
        for(var i=0;i<dataIDStudyPlanning.length;i++){
            countScore(dataIDStudyPlanning[i]);
        }
    }


    $(document).on('keyup','.formScore',function () {
        var ID = $(this).attr('data-id');
        if($(this).val()>100){
            $(this).val(100);
        } else if($(this).val()<0){
            $(this).val(0);
        }
        countScore(ID);

    });
    $(document).on('change','.formScore',function () {
        var ID = $(this).attr('data-id');
        if($(this).val()>100){
            $(this).val(100);
        } else if($(this).val()<0){
            $(this).val(0);
        }
        countScore(ID);
    });
    $(document).on('blur','.formScore',function () {
        var ID = $(this).attr('data-id');
        if($(this).val()==''){
            $(this).val(0);
        }
        if($(this).val()>100){
            $(this).val(100);
        } else if($(this).val()<0){
            $(this).val(0);
        }
        countScore(ID);
    });

    $(document).on('click','#btnSaveScore',function () {

        loading_buttonSm('#btnSaveScore');
        $('#btnBackFromInputScore').prop('disabled',true);

        var formUpdate = [];
        for(var i=0;i<dataIDStudyPlanning.length;i++){
            var da = {
                DB_Student : $('#db_student'+dataIDStudyPlanning[i]).val(),
                ID : dataIDStudyPlanning[i],
                dataForm : {
                    Evaluasi1 : $('#formAsg1'+dataIDStudyPlanning[i]).val(),
                    Evaluasi2 : $('#formAsg2'+dataIDStudyPlanning[i]).val(),
                    Evaluasi3 : $('#formAsg3'+dataIDStudyPlanning[i]).val(),
                    Evaluasi4 : $('#formAsg4'+dataIDStudyPlanning[i]).val(),
                    Evaluasi5 : $('#formAsg5'+dataIDStudyPlanning[i]).val(),
                    UTS : $('#formUTS'+dataIDStudyPlanning[i]).val(),
                    UAS : $('#formUAS'+dataIDStudyPlanning[i]).val(),
                    Score : $('#formScoreValue'+dataIDStudyPlanning[i]).val(),
                    Grade : $('#formGrade'+dataIDStudyPlanning[i]).val(),
                    GradeValue : $('#formGradeValue'+dataIDStudyPlanning[i]).val()
                }

            };

            formUpdate.push(da);
        }

        var ScheduleID = $('#formScheduleID').val();
        var TotalAssigment = $('#formTotalAsg').val();

        var url = base_url_js+'api/__crudScore';
        var token = jwt_encode({action:'update',ScheduleID:ScheduleID,TotalAssigment:TotalAssigment,formUpdate:formUpdate},'UAP)(*');
        $.post(url,{token:token},function (resultJson) {
            toastr.success('Score Saved','Success!');
            setTimeout(function () {
                $('#btnSaveScore').prop('disabled',false).html('Save');
                $('#btnBackFromInputScore').prop('disabled',false);
            },1000);

        });

    });

    function countScore(ID) {
        var TotalAsg = $('#formTotalAsg').val();

        var TotalAsgValue = 0;
        for(var a=1;a<=TotalAsg;a++){
            var n = ($('#formAsg'+a+''+ID).val()!='') ? $('#formAsg'+a+''+ID).val() : 0;
            TotalAsgValue = TotalAsgValue + parseFloat(n);
        }

        // Misal => tugas 30%, UTS 35%, UAS 35%

        var AvgAsg = (parseFloat(TotalAsgValue) / parseInt(TotalAsg)) * (Grade_Assigment/100);
        var AvgUTS = parseFloat($('#formUTS'+ID).val()) * (Grade_UTS/100);
        var AvgUAS = parseFloat($('#formUAS'+ID).val()) * (Grade_UAS/100);

        var TotalScore = AvgAsg + AvgUTS + AvgUAS;
        var Score = parseFloat(TotalScore).toFixed(2);
        $('#score'+ID).html('<b>'+Score+'</b>');
        $('#formScoreValue'+ID).val(Score);

        var url = base_url_js+'api/__crudScore';
        var token = jwt_encode({action:'grade',Score:Score},'UAP)(*');
        $.post(url,{token:token},function (jsonResult) {

            var color = '';
            if(jsonResult.Grade=='A' || jsonResult.Grade=='A-') {
                color = 'style="color:green;"';
            }
            else if(jsonResult.Grade=='B+' || jsonResult.Grade=='B' || jsonResult.Grade=='B-'){
                color = 'style="color:blue;"';
            }
            else if(jsonResult.Grade=='C+' || jsonResult.Grade=='C'){
                color = 'style="color:#dc8300;"';
            }
            else if(jsonResult.Grade=='D' || jsonResult.Grade=='E'){
                color = 'style="color:red;"';
            }

            $('#grade'+ID).html('<b '+color+'>'+jsonResult.Grade+'</b>');
            $('#formGrade'+ID).val(jsonResult.Grade);
            $('#formGradeValue'+ID).val(jsonResult.Score);

        });

    }
</script>