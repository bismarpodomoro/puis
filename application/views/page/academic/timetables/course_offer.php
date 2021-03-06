
<style>
    .left-box, .right-box {
        width: 46% !important;
    }
</style>


<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="well">
            <div class="row">
                <div class="col-xs-4">
                    <select class="form-control" id="filterSemester"></select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control" id="formProdi"></select>
                </div>
                <div class="col-xs-4">
                    <select class="form-control" id="formSemester"></select>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="widget box">
            <div class="widget-header">
                <h4><i class="icon-reorder"></i> Semester <span id="textSemester"></span> <span id="textCurriculum"></span></h4>

                <input id="formSemesterID" class="hide" readonly>

                <input id="formCurriculumYear" class="hide" readonly>

                <input id="formCurriculumID" class="hide" readonly>
                <input id="formProdiID" class="hide" readonly>
                <input id="formDataYear" class="hide" readonly>
                <input id="formSemesterSearch" class="hide" readonly>
            </div>
            <div class="widget-content">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Left box -->
                        <div class="left-box">
                            <input type="text" id="box1Filter" class="form-control box-filter form-offer" placeholder="Filter entries..."><button type="button" id="box1Clear" class="filter">x</button>
                            <select id="box1View" multiple="multiple" class="multiple form-offer">
                            </select>
                            <span id="box1Counter" class="count-label"></span>
                            <select id="box1Storage" class="form-offer"></select>
                        </div>
                        <!--left-box -->

                        <!-- Control buttons -->
                        <div class="dual-control">
                            <button id="to2" type="button" class="btn btn-default btn-default-success form-offer"><i class="fa fa-step-forward" aria-hidden="true"></i></button>
                            <!--                            <button id="allTo2" type="button" class="btn btn-default btn-default-success"><i class="fa fa-fast-forward" aria-hidden="true"></i></button>-->
                            <hr/>
                            <button id="to1" type="button" class="btn btn-default btn-default-danger form-offer"><i class="fa fa-step-backward" aria-hidden="true"></i></button>
                            <!--                            <button id="allTo1" type="button" class="btn btn-default btn-default-danger"><i class="fa fa-fast-backward" aria-hidden="true"></i></button>-->
                        </div>
                        <!--control buttons -->

                        <!-- Right box -->
                        <div class="right-box">
                            <input type="text" id="box2Filter" class="form-control box-filter form-offer" placeholder="Filter entries..."><button type="button" id="box2Clear" class="filter">x</button>
                            <select id="box2View" multiple="multiple" class="multiple form-offer"></select>
                            <span id="box2Counter" class="count-label"></span>
                            <select id="box2Storage" class="form-offer"></select>
                        </div>
                        <!--right box -->
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 hide" id="OfferingDiv">
                        <h4>Offering Another Semester</h4>
                        <div class="well">
                            <div id="btnAnother"></div>
                        </div>
                    </div>
                    <div class="col-md-12" style="text-align: right;">
                        <hr/>
                        <button class="btn btn-success form-offer" data-action="add" id="btnSaveMK">Save</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-12" style="margin-bottom: 30px;">
        <hr/>
        <h3>List Course Offerings</h3>

        <!--        <div class="thumbnail">-->
        <!--            <div id="listProdi">-->
        <!--                <label class="checkbox-inline" style="font-weight: bold;color: blue;">-->
        <!--                    <input type="checkbox" class="checkProdi" value="All"> All-->
        <!--                </label>-->
        <!--            </div>-->
        <!--        </div>-->

        <div class="row" id="dataOfferings"></div>
    </div>
</div>


<script>
    $(document).ready(function () {


        loSelectOptionSemester('#filterSemester','');

        $('#formProdi').append('<option value="" disabled selected>--- Select Program Study ---</option>' +
            '                        <option disabled>------------------------------------------</option>');
        loadSelectOptionBaseProdi('#formProdi','');

        $('#formSemester').append('<option value="" disabled selected>--- Select Semester ---</option>' +
            '                <option disabled>------------------------------------------</option>');
        loadSelectOPtionAllSemester('#formSemester','','',0);
    });


    $('#filterSemester').change(function () {

        var filterSemester = $('#filterSemester').val();
        var SemesterID = filterSemester.split('.')[0];

        $('#formSemester').empty();
        $('#formSemester').append('<option value="" disabled selected>--- Select Semester ---</option>' +
            '                <option disabled>------------------------------------------</option>');
        loadSelectOPtionAllSemester('#formSemester','',SemesterID,0);
    });

    $('#formSemester,#formProdi').change(function () {
        loadDatapage();
    });

    $(document).on('click','.btnSmtAnother-cl',function () {

        var tg = $(this).attr('data-tg');

        var id = $(this).attr('data-id');
        var dataCourse = $('#dataMK'+id).val();

        var dataJSON = JSON.parse(dataCourse);

        if(tg==1){
            $(this).addClass('btn-default btn-default-warning');
            $(this).removeClass('btn-warning');
            $(this).attr('data-tg',0);

            for(var i=0;i<dataJSON.DetailSemester.length;i++){
                var Courses = dataJSON.DetailSemester[i];


                $('#box1View option[value='+Courses.CDID+']').remove();
                $('#box2View option[value='+Courses.CDID+']').remove();
            }

        } else {
            $(this).removeClass('btn-default btn-default-warning');
            $(this).addClass('btn-warning');
            $(this).attr('data-tg',1);

            for(var i=0;i<dataJSON.DetailSemester.length;i++){
                var Courses = dataJSON.DetailSemester[i];

                if(Courses.Offering==false){
                    var color = (Courses.StatusMK==1) ? '#ff9800' : 'red';
                    var status = (Courses.StatusMK==1) ? '' : 'disabled';

                    $('#box1View').append('<option value="'+Courses.CDID+'" style="color: '+color+';" '+status+'>Smt '+Courses.Semester+' - '+Courses.MKCode+' | '+Courses.NameMKEng+' (Credit : '+Courses.TotalSKS+')</option>');
                }


            }

        }


    });

    $(document).on('click','.btn-delete-offer',function () {

        // Cek Apakah Offering sudah di set jadwal atau belum
        var CDID = $(this).attr('data-cdid');
        var OfferID = $(this).attr('data-idoffer');
        var SemesterID = $('#formSemesterID').val();
        var Course = $(this).attr('data-mk').split('|');

        var data = {
            action : 'checkCourse',
            dataWhere : {
                SemesterID : SemesterID,
                MKID : Course[0]
            }

        };

        var url = base_url_js+'api/__crudCourseOfferings';
        var token = jwt_encode(data,'UAP)(*');

        $.post(url,{token:token},function (result) {
            if(result==0){
                $('#NotificationModal .modal-body').html('<div style="text-align: center;color: red;"><h4><strong>Courses Are Scheduled</strong></h4>' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
                    '</div>');
            } else {
                $('#NotificationModal .modal-body').html('<div style="text-align: center;"><b>Delete Offerings ?? </b> ' +
                    '<button type="button" id="btnDeleteOfferYes" data-idoffer="'+OfferID+'" data-cdid="'+CDID+'" class="btn btn-primary" style="margin-right: 5px;">Yes</button> ' +
                    '<button type="button" id="btnDeleteOfferNo" class="btn btn-default" data-dismiss="modal">No</button>' +
                    '</div>');
            }

            $('#NotificationModal').modal('show');
        });

    });

    $(document).on('click','#btnDeleteOfferYes',function () {

        var CDID = $(this).attr('data-cdid');
        var OfferID = $(this).attr('data-idoffer');

        var data = {
            action : 'delete',
            OfferID : OfferID,
            CDID : CDID
        };

        var token = jwt_encode(data,"UAP)(*");
        var url = base_url_js+'api/__crudCourseOfferings';

        loading_buttonSm('#btnDeleteOfferYes');
        $('#btnDeleteOfferNo').prop('disabled',true);

        $.post(url,{token:token},function (result) {
            toastr.success('Data Deleted','Success');
            loadDatapage();

            setTimeout(function () {
                $('#NotificationModal').modal('hide');
            },1000);
        });

    });

    $('#btnSaveMK').click(function () {


        var action = $(this).attr('data-action');
        var dataID = $('#box2View').find('option').map(function() { return this.value }).get().join(",");

        var data_CDID = dataID.split(',');


        var Arr_CDID = (action=='edit') ? $.merge(DataArr_CDID,data_CDID) : data_CDID;
        var OfferID = (action=='edit') ? $(this).attr('data-idoffer') : '';

        var SemesterID = $('#formSemesterID').val();
        var ProdiID = $('#formProdi').val();


        var dataSemester = $('#formSemester').val();

        if( ProdiID!=null && dataSemester!=null && ProdiID!='' && dataSemester!=''){

            loading_button('#btnSaveMK');

            var Curriculum = dataSemester.split('|')[1];
            var Semester = dataSemester.split('|')[0];

            var CurriculumID = Curriculum.split('.')[0];

            var data = {
                action : action,
                OfferID : OfferID,
                formData : {
                    SemesterID : SemesterID,
                    ProgramsCampusID : 1,
                    CurriculumID : CurriculumID,
                    ProdiID : ProdiID.split('.')[0],
                    Semester : Semester,
                    Arr_CDID : JSON.stringify(Arr_CDID.sort()),
                    IsSemesterAntara : '0',
                    UpdateBy : sessionNIP,
                    UpdateAt : dateTimeNow()
                }
            };


            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api/__crudCourseOfferings';

            $.post(url,{token:token},function (resultJSON) {
                toastr.success('Saved','Success');
                $('#box2View,#box2Storage').empty();
                loadDatapage();
                // getListCourseOfferings(SemesterID,CurriculumID,ProdiID,Semester);
                // loadCourse(Semester);
                // $('#formSemester').val('');
                // $('#btnAnother').html('');
                setTimeout(function () {
                    $('#btnSaveMK').html('Save').prop('disabled',false);
                },500);
            });

        }

    });

    function loadDatapage() {

        var filterSemester = $('#filterSemester').val();
        var Prodi = $('#formProdi').val();
        var dataSmt = $('#formSemester').val();

        if(filterSemester!=null && dataSmt!=null && Prodi!=null &&
            filterSemester!="" && dataSmt!="" && Prodi!=""){

            // 5|3.2016|Curriculum 2016

            var DataYear = dataSmt.split('|')[1];
            var Semester = dataSmt.split('|')[0];

            $('#textSemester').text(Semester);
            $('#textCurriculum').text(' - '+dataSmt.split('|')[2]);


            var CurriculumID = DataYear.split('.')[0];
            var ProdiID = Prodi.split('.')[0];

            $('#formCurriculumID').val(CurriculumID);
            $('#formProdiID').val(ProdiID);
            $('#formDataYear').val(DataYear);
            $('#formSemesterSearch').val(Semester);

            loadCourse();

            getSemesterActive();
            $('.divSmt-cl').removeClass('hide');
            $('#divSmt'+Semester).addClass('hide');
            $('#OfferingDiv').removeClass('hide');

            return false;

            loadCourse(Semester,DataYear,ProdiID);



        }
    }


    function loadCourse() {

        var SemesterSearch = $('#formSemesterSearch').val();
        var DataYear = $('#formDataYear').val();

        var year = (DataYear!=null) ? DataYear.split('.')[1] : '';
        var ProdiID = $('#formProdiID').val();

        var url = base_url_js+'api/__getKurikulumByYear';

        var data = {
            SemesterSearch : SemesterSearch,
            year : year,
            ProdiID : ProdiID
        };

        var token = jwt_encode(data,'UAP)(*');
        $.post(url,{token:token},function (resultJeson) {

            $('#btnAnother').html('');

            if(resultJeson.MataKuliah.length>0){

                if(SemesterSearch!=''){
                    $('#OfferingDiv').removeClass('hide');
                    $('#btnAnother').html('');

                } else {
                    $('#OfferingDiv').addClass('hide');
                    $('#formSemester').empty();

                    $('#formSemester').append('<option value="" disabled selected>--- Select Semester ---</option>' +
                        '                <option disabled>------------------------------------------</option>');
                }


                for(var i=0;i<resultJeson.MataKuliah.length;i++){
                    var mk = resultJeson.MataKuliah[i];

                    if(mk.DetailSemester.length>0){

                        if(SemesterSearch!=''){
                            if(SemesterSearch!=mk.Semester){
                                $('#btnAnother').append('<span class="divSmt-cl" id="divSmt'+mk.Semester+'"><button class="btn btn-sm btn-default btn-default-warning btnSmtAnother-cl" data-tg="0" data-id="'+mk.Semester+'" id="btnSmtAnother'+mk.Semester+'">Semester '+mk.Semester+'</button> ' +
                                    '<input id="dataMK'+mk.Semester+'" class="hide" type="hide" hidden readonly /></span>');

                                $('#dataMK'+mk.Semester).val(JSON.stringify(mk));
                            }

                        } else {
                            $('#formSemester').append('<option value="'+mk.Semester+'">Semester '+mk.Semester+'</option>');
                        }



                    }

                }

                if(SemesterSearch==''){
                    $('#formSemester').append('<option disabled>------------------------------------------</option>');
                    for(var r=9;r<=14;r++){
                        $('#formSemester').append('<option value="'+r+'" style="color: red;">Semester '+r+'</option>');
                    }
                }



            }

        });

        // var Semester = $('#formSemester').val();

        // if(DataYear!=null && Semester!=null){
        //     $('#box1View,#box1Storage,#box2View,#box2Storage').empty();
        //     var CurriculumID = DataYear.split('.')[0];
        //     // getSemesterActive(CurriculumID,ProdiID,Semester);
        // }


    }

    function getSemesterActive() {

        var CurriculumID = $('#formCurriculumID').val();
        var ProdiID = $('#formProdiID').val();
        var Semester = $('#formSemesterSearch').val();

        var filterSemester = $('#filterSemester').val();
        var SemesterID = filterSemester.split('.')[0];


        var url = base_url_js+'api/__crudSemester';
        var data = {
            action : 'ReadSemesterActive',
            formData : {
                SemesterID : SemesterID,
                CurriculumID : CurriculumID,
                ProdiID : ProdiID,
                Semester : Semester,
                IsSemesterAntara : '0'
            }
        };

        var token = jwt_encode(data,'UAP)(*');
        $.post(url,{token:token},function (jsonResult) {

            var SemesterActive = jsonResult.SemesterActive;
            $('#formSemesterID').val(SemesterID);

            $('#box1View,#box1Storage,#box2View,#box2Storage').empty();
            for(var i=0;i<jsonResult.DetailCourses.length;i++){
                var Courses = jsonResult.DetailCourses[i];
                if(Courses.Offering==false){

                    var color = (Courses.StatusMK==1) ? 'green' : 'red';
                    var status = (Courses.StatusMK==1) ? '' : 'disabled';
                    var type = (Courses.MKType==1) ? '*' : '';
                    $('#box1View').append('<option value="'+Courses.CurriculumDetailID+'" style="color: '+color+';" '+status+'>Smt '+Courses.Semester+' - '+Courses.MKCode+' | '+Courses.MKNameEng+' (Credit : '+Courses.TotalSKS+')'+type+'</option>');
                }

            }

            getListCourseOfferings(SemesterID,CurriculumID,ProdiID,Semester);

        });
    }

    function getListCourseOfferings(SemesterID,CurriculumID,ProdiID,Semester) {
        var url = base_url_js+'api/__crudCourseOfferings';

        var Prodi = (ProdiID.split('.').length>0) ? ProdiID.split('.')[0] : ProdiID;

        var data = {
            action : 'read',
            formData : {
                SemesterID : SemesterID,
                CurriculumID : CurriculumID,
                ProdiID : Prodi,
                Semester : Semester,
                IsSemesterAntara : '0'
            }
        };

        var token = jwt_encode(data,'UAP)(*');

        $('#btnSaveMK').prop('disabled',true);
        $.post(url,{token:token},function (jsonResult) {


            // console.log(jsonResult);

            $('#dataOfferings').empty();

            for(var i=0;i<jsonResult.length;i++){
                var data = jsonResult[i];

                $('#listProdi').append('<label class="checkbox-inline">' +
                    '  <input type="checkbox" class="checkProdi" value="'+data.Prodi.ID+'"> '+data.Prodi.NameEng+
                    '</label>');

                $('#dataOfferings').append('<div class="col-md-12"><h3><span class="label label-primary" style="font-size: 15px;">'+data.Prodi.NameEng+'</span></h3>' +
                    '        <table id="tbData'+i+'" class="table table-bordered">' +
                    '            <thead>' +
                    '            <tr>' +
                    '                <th class="th-center" style="width: 1%;">No</th>' +
                    '                <th class="th-center" style="width: 5%;">Code</th>' +
                    '                <th class="th-center">Course</th>' +
                    // '                <th class="th-center" style="width: 15%;">Offerings To Semester</th>' +
                    '                <th class="th-center" style="width: 5%;">Semester</th>' +
                    '                <th class="th-center" style="width: 5%;">Credit</th>' +
                    '                <th class="th-center" style="width: 5%;">Type</th>' +
                    '                <th class="th-center" style="width: 15%;">Action</th>' +
                    '            </tr>' +
                    '            </thead>' +
                    '<tbody id="trData'+i+'"></tbody>' +
                    '        </table><hr/></div>');

                var Offerings = data.Offerings;
                // console.log(Offerings);
                if(Offerings.length>0){
                    $('#btnSaveMK').attr({
                        'data-action' : 'edit',
                        'data-idOffer' : Offerings[0].ID
                    });

                    DataArr_CDID = JSON.parse(Offerings[0].Arr_CDID);
                    var tr = $('#trData'+i);
                    var no=1;
                    for(var s=0;s<Offerings[0].Details.length;s++){
                        var _data = Offerings[0].Details[s];
                        var label = (_data.MKType=='1' && _data.Semester == Semester) ? '<span class="label label-success">Required</span>' : '<span class="label label-danger">Optional</span>';

                        var tr_bg = (_data.Semester != Semester) ? '#ffffdd' : '';

                        var btnDelete = (_data.ScheduleID!=null) ? 'Courses Are Scheduled'
                            : '<button class="btn btn-default btn-default-danger btn-delete-offer" data-idoffer="'+Offerings[0].ID+'" data-cdid="'+_data.CDID+'" data-mk="'+_data.MKID+'|'+_data.MKCode+'">Remove Offer</button>' ;


                        tr.append('<tr style="background: '+tr_bg+';">' +
                            '<td class="td-center">'+(no++)+'</td>' +
                            '<td class="td-center">'+_data.MKCode+'</td>' +
                            '<td><b>'+_data.MKNameEng+'</b><br/><i>'+_data.MKName+'</i></td>' +
                            // '<td class="td-center"> '+smt__+' ' +
                            // // ' '+_data.ToSemester+' ' +
                            // '<br/><a href="javascript:void(0)" class="btn-semester-offer" data-id="'+_data.ID+'" data-prodi="'+ProdiID+'" data-smt="'+dataSmt+'" style="float: right;"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a> </td>' +
                            '<td class="td-center">'+_data.Semester+'</td>' +
                            '<td class="td-center">'+_data.TotalSKS+'</td>' +
                            '<td class="td-center">'+label+'</td>' +
                            '<td class="td-center">'+btnDelete+'</td>' +
                            '</tr>');
                    }
                } else {
                    $('#btnSaveMK').attr({
                        'data-action' : 'add',
                        'data-idOffer' : 0
                    });
                }


                var table = $('#tbData'+i).DataTable({
                    'iDisplayLength' : 25
                });
            }

        }).done(function () {
            $('#btnSaveMK').prop('disabled',false);
        });

    }


</script>