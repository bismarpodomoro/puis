
<div class="col-md-12" style="text-align: center;">
    <h4 id="viewCC_Semester"></h4>
    <hr/>
    <input id="formCC_SemesterID" class="hide" readonly hidden>
</div>

<div class="col-md-4">
    <div class="widget box">
        <div class="widget-header">
            <h4><i class="icon-reorder"></i> Add Combined Class</h4>
        </div>
        <div class="widget-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Select Prodi</label>
                        <select class="form-control selc-prodi" id="filterCC_Prodi">
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Course</label>
                        <div id="dvCC_Course">-</div>
                        <hr/>
                    </div>
                    <div class="form-group" style="text-align: center;background:#ffeb3bab;padding-top: 5px;">
                        <label>Combined with</label>
                    </div>
                    <div class="form-group">
                        <label>Select Prodi</label>
                        <select class="form-control selc-prodi" id="formCC_addProdi"></select>
                    </div>
                    <div class="form-group">
                        <label>Select Group Class</label>
                        <select class="form-control" id="formCC_ClassGroup"></select>
                    </div>

                    <div class="form-group" style="text-align: right;">
                        <button class="btn btn-success" id="btnSaveAddCC">Save</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<div class="col-md-8">
    <div class="widget box">
        <div class="widget-header">
            <h4><i class="icon-reorder"></i> Remove Combined Class</h4>
        </div>
        <div class="widget-content">
            <div class="row">
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Select Prodi</label>
                        <select class="form-control selc-prodi" id="filterCC_ProdiDell"></select>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="form-group">
                        <label>Select Group Class</label>
                        <select class="form-control" id="formCC_ClassGroupDell"></select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                        <tr style="background: #607D8B;color: #fff;">
                            <th>Course</th>
                            <th style="width: 7%;">Action</th>
                        </tr>
                        </thead>
                        <tbody id="trCombinedCl">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {

        loadCC_AcademicYearOnPublish('');

        $('.selc-prodi').empty();
        $('.selc-prodi').append('<option value="" selected disabled>--- Select Prodi ---</option>');
        loadSelectOptionBaseProdi('.selc-prodi');

    });

    $('#btnSaveAddCC').click(function () {
        var SemesterID = $('#formCC_SemesterID').val();
        var ProdiA = $('#filterCC_Prodi').val();
        var Course = $('#formCC_MataKuliah').val();
        var ProdiB = $('#formCC_addProdi').val();
        var ScheduleID = $('#formCC_ClassGroup').val();

        if(
            SemesterID!='' && SemesterID!=null
            && ProdiA!='' && ProdiA!=null
            && Course!='' && Course!=null
            && ProdiB!='' && ProdiB!=null
            && ScheduleID!='' && ScheduleID!=null
        ){

            loading_buttonSm('#btnSaveAddCC');

            var data = {
              action : 'addCombine',
              dataInsert : {
                  ScheduleID : ScheduleID,
                  ProdiID : ProdiA.split('.')[0],
                  CDID : Course.split('|')[0],
                  MKID : Course.split('|')[1]
              }
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api/__crudCombinedClass';
            $.post(url,{token:token},function (result) {
                toastr.success('Saved','Success');
                setTimeout(function () {
                    $('#btnSaveAddCC').prop('disabled',false).html('Save');
                    $('#filterCC_Prodi,#formCC_addProdi').val('');
                    $('#dvCC_Course').html('-');
                    $('#formCC_ClassGroup').empty();
                },500);
            });
        }


    });


    function loadCC_AcademicYearOnPublish(smt) {
        var url = base_url_js+"api/__getAcademicYearOnPublish";
        $.getJSON(url,{smt:smt},function (data_json) {
            if(smt=='SemesterAntara'){
                $('#formCC_SemesterID').val(data_json.SemesterID);
            } else {
                $('#formCC_SemesterID').val(data_json.ID);
            }

            $('#viewCC_Semester').html(data_json.Year+''+data_json.Code+' | '+data_json.Name);

        });
    }

    function getCC_CourseOfferings(ProdiID) {
        var url = base_url_js+'api/__crudCourseOfferings';
        var SemesterID = $('#formCC_SemesterID').val();
        var data = {
            action : 'readToSchedule',
            formData : {
                SemesterID : SemesterID,
                ProdiID : ProdiID,
                IsSemesterAntara : ''+SemesterAntara
            }
        };
        var token = jwt_encode(data,'UAP)(*');
        $.post(url,{token:token},function (jsonResult) {

            console.log(jsonResult);

            if(jsonResult.length>0){
                $('#dvCC_Course').html('<select class="select2-select-00 full-width-fix" size="5" id="formCC_MataKuliah">' +
                    '                        <option value=""></option>' +
                    '                    </select>');

                for(var i=0;i<jsonResult.length;i++){
                    var semester = jsonResult[i].Offerings.Semester;

                    var mk = jsonResult[i].Details;
                    for(var m=0;m<mk.length;m++){
                        var dataMK = mk[m];
                        var asalSmt = (semester!=dataMK.Semester) ? '('+dataMK.Semester+')' : '';
                        var schDisabled = (dataMK.ScheduleID!="") ? 'disabled' : '';

                        $('#formCC_MataKuliah').append('<option value="'+dataMK.CDID+'|'+dataMK.ID+'" '+schDisabled+'>Smt '+semester+' '+asalSmt+' - '+dataMK.MKCode+' | '+dataMK.MKNameEng+'</option>');
                    }

                    $('#formCC_MataKuliah').append('<option disabled>-------</option>');

                }

                $('#formCC_MataKuliah').select2({allowClear: true});
            } else {
                $('#dvCC_Course').html('<b>No Course To Offerings</b>')
            }
        });
    }

    function loadGroupClass(elm,ProdiID) {
        var data = {
            action : 'readGroupCalss',
            ProdiID : ProdiID
        };

        var token = jwt_encode(data,'UAP)(*');
        var url = base_url_js+'api/__crudCombinedClass';
        $.post(url,{token:token},function (jsonResult) {

            $(elm).empty();
            if(jsonResult.length>0){
                $(elm).append('<option disabled selected>-- Select Group --</option>');
                for(var c=0;c<jsonResult.length;c++){
                    var d = jsonResult[c];
                    $(elm).append('<option value="'+d.ID+'">'+d.ClassGroup+'</option>');
                }
            }
        });
    }

    function loadScheduleFromGC() {
        var SemesterID = $('#formCC_SemesterID').val();
        var filterCC_ProdiDell = $('#filterCC_ProdiDell').val();
        var formCC_ClassGroupDell = $('#formCC_ClassGroupDell').val();

        if(
            SemesterID !='' && SemesterID!=null &&
            filterCC_ProdiDell !='' && filterCC_ProdiDell!=null &&
            formCC_ClassGroupDell !='' && formCC_ClassGroupDell!=null
        ) {
            var data = {
               action : 'getScheduleGC',
                SemesterID : SemesterID,
                ProdiID : filterCC_ProdiDell.split('.')[0],
                ScheduleID : formCC_ClassGroupDell
            };

            var token = jwt_encode(data,'UAP)(*');
            var url = base_url_js+'api/__crudCombinedClass';
            $.post(url,{token:token},function (jsonResult) {
                $('#trCombinedCl').empty();
                if(jsonResult.length>0){

                    var btnDel = (jsonResult[0].TotalProdi>1) ? '<button class="btn btn-sm btn-danger" data-id="'+jsonResult[0].ScheduleID+'" data-sdcid="'+jsonResult[0].SDCID+'" id="btnDellCombined">Del</button>' : '-';

                    $('#trCombinedCl').append('<tr>' +
                        '<td>'+jsonResult[0].MKCode+' - '+jsonResult[0].MKNameEng+'</td>' +
                        '<td style="text-align: center;">'+btnDel+'</td>' +
                        '</tr>');
                }
            });
        }

    }

</script>