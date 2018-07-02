<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_api extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('master/m_master');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->library('JWT');
        $this->load->library('google');

        if($this->session->userdata('loggedIn')==false){
            $data = array(
                'Message' => 'Error',
                'Description' => 'Your Session Login Is Destroy'
            );
            print_r(json_encode($data));
            exit;
        }
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }



    public function getKurikulumByYear(){

//        $year = $this->input->get('year');

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $result = $this->m_api->__getKurikulumByYear($data_arr['SemesterSearch'],$data_arr['year'],$data_arr['ProdiID']);

        return print_r(json_encode($result));
    }

    public function getProdi(){
        $data = $this->m_api->__getBaseProdi();
        return print_r(json_encode($data));
    }

    public function getProdiSelectOption(){
        $data = $this->m_api->__getBaseProdiSelectOption();
        return print_r(json_encode($data));
    }

    public function getProdiSelectOptionAll(){
        $data = $this->m_api->__getBaseProdiSelectOptionAll();
        return print_r(json_encode($data));
    }

    public function getKurikulumSelectOption(){
        $data = $this->m_api->__getKurikulumSelectOption();
        return print_r(json_encode($data));
    }

    public function getMKByID(){
        $ID = $this->input->post('idMK');
        $data = $this->m_api->__getMKByID($ID);
        return print_r(json_encode($data));
    }

    public function getSemester(){
        $data = $this->m_tahun_akademik->__getSemester();
        return print_r(json_encode($data));
    }

    public function getLecturer2(){
        $data = $this->m_api->__getLecturer();
        return print_r(json_encode($data));
    }

    public function getLecturer(){
        $requestData= $_REQUEST;

        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE PositionMain = "14.7"')->result_array();

        if( !empty($requestData['search']['value']) ) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain = "14.5" OR em.PositionMain = "14.6" OR em.PositionMain = "14.7")  AND ( ';

            $sql.= ' em.NIP LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR em.Name LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ') ORDER BY em.PositionMain, NIP ASC';

        }
        else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain = "14.5" OR em.PositionMain = "14.6" OR em.PositionMain = "14.7")';
            $sql.= 'ORDER BY em.PositionMain, NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $jb = explode('.',$row["PositionMain"]);
            $Division = '';
            $Position = '';

            if(count($jb)>1){
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division',array('ID'=>$jb[0]),1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position',array('ID'=>$jb[1]),1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $nestedData[] = $row["NIP"];
            $nestedData[] = $row["NIDN"];
            $nestedData[] = '<div style="text-align: center;"><img src="http://siak.podomorouniversity.ac.id/includes/foto/'.$row["Photo"].'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="'.base_url('database/lecturer-details/'.$row["NIP"]).'" style="font-weight: bold;">'.$row["Name"].'</a>';
            $nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = $Division.' - '.$Position;
            $nestedData[] = $row["ProdiNameEng"];

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function getEmployees()
    {
        $requestData= $_REQUEST;
        // print_r($requestData);

        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE PositionMain not like "%14%"')->result_array();

        if( !empty($requestData['search']['value']) ) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain not like "%14%")  AND ( ';

            $sql.= ' em.NIP LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR em.Name LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ') ORDER BY NIP,em.PositionMain  ASC';

        }
        else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain not like "%14%")';
            $sql.= 'ORDER BY NIP,em.PositionMain ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $jb = explode('.',$row["PositionMain"]);
            $Division = '';
            $Position = '';

            if(count($jb)>1){
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division',array('ID'=>$jb[0]),1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position',array('ID'=>$jb[1]),1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $nestedData[] = $row["NIP"];
            // $nestedData[] = $row["NIDN"];
            $nestedData[] = '<div style="text-align: center;"><img src="http://siak.podomorouniversity.ac.id/includes/foto/'.$row["Photo"].'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="'.base_url('database/lecturer-details/'.$row["NIP"]).'" style="font-weight: bold;">'.$row["Name"].'</a>';
            $nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = $Division.' - '.$Position;
            $nestedData[] = $row["EmailPU"];
            $nestedData[] = $row["ProdiNameEng"];

            $data[] = $nestedData;
        }

        // print_r($data);

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getStudents(){
        $requestData= $_REQUEST;

        $dataYear = $this->input->get('dataYear');
        $dataProdiID = $this->input->get('dataProdiID');

        $db_ = 'ta_'.$dataYear;


        $totalData = $this->db->get_where($db_.'.students',
                array('ProdiID' => $dataProdiID))->result_array();

        $sql = 'SELECT s.NPM, s.Photo, s.Name, s.Gender, s.ClassOf, ps.NameEng AS ProdiNameEng, s.StatusStudentID, ss.Description AS StatusStudent 
                          FROM '.$db_.'.students s 
                          LEFT JOIN db_academic.program_study ps ON (ps.ID = s.ProdiID)
                          LEFT JOIN db_academic.status_student ss ON (ss.ID = s.StatusStudentID)';

        if( !empty($requestData['search']['value']) ) {
            $sql.= ' WHERE s.NPM LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR s.Name LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR s.ClassOf LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' ORDER BY s.NPM, s.ProdiID ASC';
        }
        else {
            $sql.= 'ORDER BY s.NPM, s.ProdiID ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';
        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $Gender = ($row["Gender"]=='P') ? 'Female' : 'Male';

            $label = '';
            if($row['StatusStudentID']==7 || $row['StatusStudentID'] ==6 || $row['StatusStudentID'] ==4){
                $label = 'label-danger';
            } else if($row['StatusStudentID'] ==2){
                $label = 'label-warning';
            } else if($row['StatusStudentID'] ==3){
                $label = 'label-success';
            } else if($row['StatusStudentID'] ==1){
                $label = 'label-primary';
            }

            $nestedData[] = '<div style="text-align: center;">'.$row["NPM"].'</div>';
            $nestedData[] = '<div style="text-align: center;"><img src="'.base_url('uploads/students/').$db_.'/'.$row["Photo"].'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="javascript:void(0);" data-npm="'.$row["NPM"].'" data-ta="'.$row["ClassOf"].'" class="btnDetailStudent"><b>'.$row["Name"].'</b></a>';
            $nestedData[] = '<div style="text-align: center;">'.$Gender.'</div>';
            $nestedData[] = '<div style="text-align: center;">'.$row["ClassOf"].'</div>';
//            $nestedData[] = $row["ProdiNameEng"];
            $nestedData[] = '<div style="text-align: center;"><span class="label '.$label.' ">'.$row["StatusStudent"].'</span></div>';

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function getAllMK(){
        $data = $this->m_api->__getAllMK();
        return print_r(json_encode($data));
    }

    public function setLecturersAvailability(){

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
//        print_r($data_arr);

        if($data_arr['action']=='add'){
            $dataInsert = (array) $data_arr['dataForm'];
            $this->db->insert('db_academic.lecturers_availability',$dataInsert);
            return print_r($this->db->insert_id());
        } else if($data_arr['action']=='edit'){

            $update_lad = (array) $data_arr['dataForm_lad'];
            $this->db->where('ID', $data_arr['ladID']);
            $this->db->update('db_academic.lecturers_availability_detail',$update_lad);

            return print_r(1);
        } else if($data_arr['action']=='delete'){

            // Cek apakah ID lebih dari satu
            $dataCek = $this->m_api->__cekTotalLAD($data_arr['laID']);


            if(count($dataCek)==1){
//                print_r($data_arr['laID']);
                $this->db->where('ID', $data_arr['ladID']);
                $this->db->delete('db_academic.lecturers_availability_detail');

                $this->db->where('ID', $data_arr['laID']);
                $this->db->delete('db_academic.lecturers_availability');
//

            } else {
//                print_r('delete1');
                $this->db->where('ID', $data_arr['ladID']);
                $this->db->delete('db_academic.lecturers_availability_detail');
            }


            return print_r(1);

        }

    }

    public function setLecturersAvailabilityDetail($action){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = $this->jwt->decode($token,$key);

//        print_r($data_arr);
        if($action=='insert'){
            $this->db->insert('db_academic.lecturers_availability_detail',$data_arr);
            return $this->db->insert_id();
        }
    }

    public function changeTahunAkademik(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);


        $data['department'] = $this->session->userdata('departementNavigation');
        $data['dosen'] = $this->m_tahun_akademik->__getKetersediaanDosenByTahunAkademik($data_arr['ID']);
        print_r(json_encode($data['dosen']));
//        $this->load->view('page/'.$data['department'].'/ketersediaan_dosen_detail',$data);
    }


    //-------- Kurikulum -----
    public function insertKurikulum(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        // Cek Tahun
        $data = $this->m_api->cekTahunKurikulum($data_arr['Year']);
        if(count($data)>0){
            return print_r(0);
        } else {
            $this->db->insert('db_academic.curriculum',$data_arr);
            return print_r(1);
        }

    }

    public function geteducationLevel(){
        $data = $this->m_api->__geteducationLevel();
        return print_r(json_encode($data));
    }

    public function getDosenSelectOption(){
        $data = $this->m_api->__getDosenSelectOption();
        return print_r(json_encode($data));
    }

    public function crudKurikulum(){

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

//        print_r($data_arr);
//        exit;
        if($data_arr['action']=='add'){
            $insert = (array) $data_arr['data_insert'];
            $this->db->insert('db_academic.'.$data_arr['table'],$insert);
            $insert_id = $this->db->insert_id();
            return print_r($insert_id);
        } else if($data_arr['action']=='edit'){
            $dataupdate = (array) $data_arr['data_insert'];
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.'.$data_arr['table'],$dataupdate);
            return print_r(1);
        } else if($data_arr['action']=='delete'){
            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_academic.'.$data_arr['table']);
            return print_r(1);
        } else if($data_arr['action']=='read'){
            $data = $this->m_api->__getItemKuriklum($data_arr['table']);
            return print_r(json_encode($data));
        }
    }

    public function crudDetailMK(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if($data_arr['action']=='add'){
            $insert = (array) $data_arr['dataForm'];

            // Cek apakah sudah dimasukan ke detail kurikulum

            $where = array(
                'CurriculumID' => $insert['CurriculumID'],
                'ProdiID' => $insert['ProdiID'],
//                'EducationLevelID' => $insert['EducationLevelID'],
                'MKID' => $insert['MKID']);
            $this->db->select('Semester');
            $dataSmt = $this->db->get_where('db_academic.curriculum_details', $where)->result_array();

            if(count($dataSmt)>0){
                $result = array(
                    'msg' => 0,
                    'Semester' => $dataSmt[0]['Semester']
                );
                return print_r(json_encode($result));

            } else {


                $this->db->insert('db_academic.curriculum_details',$insert);
                $insert_id = $this->db->insert_id();
                $result = array(
                    'msg' => $insert_id
                );
                return print_r(json_encode($result));
            }



        }
        else if($data_arr['action']=='edit'){
            $update = (array) $data_arr['dataForm'];
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.curriculum_details',$update);
//            print_r($data_arr);

//            $this->db->where('CurriculumDetailID', $data_arr['ID']);
//            $this->db->delete('db_academic.precondition');

            $insert_id = $data_arr['ID'];
            return print_r($insert_id);
        }
        else if($data_arr['action']=='delete') {
            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_academic.curriculum_details');
            return print_r(1);
        }

//        if($data_arr['DataPraSyart']!=''){
//            for($i=0;$i<count($data_arr['DataPraSyart']);$i++){
//
//                $ex = explode(".",$data_arr['DataPraSyart'][$i]);
//
//                $data_Pra = array(
//                    'CurriculumDetailID' => $insert_id,
//                    'MKID' => trim($ex[0]),
//                    'MKCode' => trim($ex[1])
//                );
//                $this->db->insert('db_academic.precondition',$data_Pra);
//            }
//        }
    }

    public function getdetailKurikulum(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            $CDID = $data_arr['CDID'];
            $data = $this->m_api->__getdetailKurikulum($CDID);

            return print_r(json_encode($data));
        }

    }

    public function genrateMKCode(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            $ID = $data_arr['ID'];
            $data = $this->m_api->__genrateMKCode($ID);

            return print_r(json_encode($data));
        }

    }

    public function cekMKCode(){
        $MKCode = $this->input->post('MKCode');
        $data = $this->m_api->__cekMKCode($MKCode);
        return print_r(json_encode($data));
    }

    public function crudMataKuliah(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='add'){
                $dataInsert = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.mata_kuliah',$dataInsert);
                $insert_id = $this->db->insert_id();

                return print_r($insert_id);
            }
            else if($data_arr['action']=='edit')
            {
                $dataInsert = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.mata_kuliah',$dataInsert);

                return print_r(1);
            }
            else if($data_arr['action']=='delete')
            {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.mata_kuliah');
                return print_r(1);
            }
            else if($data_arr['action']=='read'){
                $ID = $data_arr['ID'];
                $MKCode = $data_arr['MKCode'];
                $data = $this->m_api->getMataKuliahSingle($ID,$MKCode);

                if(count($data)>0){
                    return print_r(json_encode($data[0]));
                }
            }
            else if($data_arr['action']=='readOfferings') {
                $dataForm = (array) $data_arr['dataForm'];
                $data = $this->m_api->getMatakuliahOfferings($dataForm['SemesterID'],$dataForm['MKID'],$dataForm['MKCode']);

                return print_r(json_encode($data[0]));
            }
        }
    }

    public function crudTahunAkademik(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){

            if($data_arr['action']=='add'){
                $dataForm = (array) $data_arr['dataForm'];
                // Cek
                $check = $this->db->get_where('db_academic.semester',array('Year'=>$dataForm['Year'],'Code'=>$dataForm['Code']))
                    ->result_array();

//                print_r($check);
//                exit;
                if(count($check)>0){
                    return print_r(0);
                } else {
                    $this->db->insert('db_academic.semester',$dataForm);
                    $insert_id = $this->db->insert_id();

                    $this->db->insert('db_academic.academic_years',
                        array('SemesterID' => $insert_id));

                    return print_r($insert_id);
                }

            }
            else if($data_arr['action']=='edit'){
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.semester',$dataForm);
                return print_r(1);
            }
            else if($data_arr['action']=='delete'){
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.semester');
                return print_r(1);
            }
            else if($data_arr['action']=='read'){

                $data = $this->db->order_by('ID', 'DESC')
                    ->get('db_academic.semester')
                    ->result_array();

                return print_r(json_encode($data));

            }

            else if($data_arr['action']=='addSemesterAntara'){
                $dataForm = (array) $data_arr['dataForm'];
                // Cek
                $check = $this->db->get_where('db_academic.semester_antara',array('Year'=>$dataForm['Year'],'Code'=>$dataForm['Code']))
                    ->result_array();

                if(count($check)>0){
                    return print_r(0);
                } else {
                    $this->db->insert('db_academic.semester_antara',$dataForm);
                    $insert_id = $this->db->insert_id();

//                    $this->db->insert('db_academic.academic_years',
//                        array('SemesterID' => $insert_id));

                    return print_r($insert_id);
                }
            }
            else if($data_arr['action']=='readSemesterAntara'){
                $data = $this->db
                    ->select('semester_antara.*')
                    ->join('db_academic.semester','semester.ID = semester_antara.SemesterID')
                    ->order_by('semester_antara.Year', 'DESC')
                    ->get('db_academic.semester_antara')
                    ->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkSemesterAntara'){
                $data = $this->db
                    ->get_where('db_academic.semester_antara',array('Status'=>'1'))
                    ->result_array();
                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='DataSemester'){


                $data = $this->m_api->getSemesterCurriculum($data_arr['SemesterID'],$data_arr['IsSemesterAntara']);

                return print_r(json_encode($data));

            }
        }

    }

    public function crudDataDetailTahunAkademik(){

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

//        print_r($data_arr);
        if(count($data_arr)>0){
            if($data_arr['action']=='read'){

                $data = $this->m_api->__crudDataDetailTahunAkademik($data_arr['ID']);
                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='edit') {

                $dataForm = (array) $data_arr['dataForm'];
                $this->db->where('SemesterID',$data_arr['SemesterID']);
                $this->db->update('db_academic.academic_years',$dataForm);

                return print_r($data_arr['SemesterID']);
            }
            else if($data_arr['action']=='publish'){
                $ID = $data_arr['ID'];
                $this->db->query('UPDATE db_academic.semester s SET s.Status=IF(s.ID="'.$ID.'",1,0)');
                return print_r($ID);
            }
            else if($data_arr['action']=='schedule'){
                $SemesterID = $data_arr['SemesterID'];
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getScheduleTeacher($SemesterID,$NIP);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='insertSC'){
                $dataForm = (array) $data_arr['dataForm'];

                $result = [];
                for($i=0;$i<count($dataForm);$i++){
                    $dataM = (array) $dataForm[$i];
                    $dataInsert = (array) $dataM['Details'];

                    $dataWhere = array(
                        'SemesterID' => $dataInsert['SemesterID'],
                        'AcademicDescID' => $dataInsert['AcademicDescID'],
                        'UserID' => $dataInsert['UserID'],
                        'DataID' => $dataInsert['DataID']
                    );

                    $dataCek = $this->db->get_where('db_academic.academic_years_special_case', $dataWhere,1)->result_array();

                    if(count($dataCek)>0){
                        $msg = array(
                            'Course' => $dataM['Course'],
                            'Msg' => 'Already Exists',
                            'Status' => 0
                        );
                    } else {
                        $this->db->insert('db_academic.academic_years_special_case', $dataInsert);
                        $insert_id = $this->db->insert_id();

                        $dataDetails = $this->db->query('SELECT s.ClassGroup,aysc.*,mk.NameEng, em.Name AS Lecturers FROM db_academic.academic_years_special_case aysc 
                                            LEFT JOIN db_academic.schedule s ON (s.ID=aysc.DataID)
                                            RIGHT JOIN db_academic.schedule_details_course sdc ON (s.ID = sdc.ScheduleID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                            LEFT JOIN  db_employees.employees em ON (em.NIP=aysc.UserID)
                                            WHERE aysc.ID = "'.$insert_id.'" 
                                            GROUP BY sdc.ScheduleID')->result_array();

                        $msg = array(
                            'Details' => $dataDetails[0],
                            'Course' => $dataM['Course'],
                            'Msg' => 'Saved',
                            'Status' => 1
                        );
                    }

                    array_push($result,$msg);

                }

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='dataSC'){
                $SemesterID = $data_arr['SemesterID'];
                $AcademicDescID = $data_arr['AcademicDescID'];
                $data = $this->m_api->__getSpecialCase($SemesterID,$AcademicDescID);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='deleteSC'){
                $id = $data_arr['ID'];
                $this->db->where('ID', $id);
                $this->db->delete('db_academic.academic_years_special_case');

                return print_r(1);
            }
        }

    }

    public function getAcademicYearOnPublish(){

        $smt = $this->input->get('smt');

        if($smt=='SemesterAntara'){
            $data = $this->db
                ->get_where('db_academic.semester_antara',array('Status'=>'1'))
                ->result_array();
        } else {
            $data = $this->m_api->__getAcademicYearOnPublish();
        }

//        $dataSMT = $this->m_api->getSemesterCurriculum();

//        $data[0]['Semester'] = $dataSMT[0]['Semester'];


        return print_r(json_encode($data[0]));
    }

    public function crudSchedule(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

//        print_r($data_arr);
        if(count($data_arr)>0){
            if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];

                // Scedule
                $insertSchedule = (array) $formData['schedule'];
                $this->db->insert('db_academic.schedule',$insertSchedule);
                $insert_id = $this->db->insert_id();

                //schedule_class_group
                $dataGroup = (array) $formData['schedule_class_group'];
                $dataGroup['ScheduleID'] = $insert_id;
                $this->db->insert('db_academic.schedule_class_group',$dataGroup);


                // schedule_details
                $dataScheduleDetails = (array) $formData['schedule_details'];
                for($s=0;$s<count($dataScheduleDetails);$s++){
                    $arr = (array) $dataScheduleDetails[$s];
                    $arr['ScheduleID'] = $insert_id;
                    $this->db->insert('db_academic.schedule_details',$arr);
                    $insert_id_SD = $this->db->insert_id();

                    // Insert Attd
                    $dataInsetAttd = array(
                        'SemesterID' => $insertSchedule['SemesterID'],
                        'ScheduleID' => $insert_id,
                        'SDID' => $insert_id_SD
                    );

                    $this->db->insert('db_academic.attendance',$dataInsetAttd);
                }


                // schedule_details_course
                $dataScheduleDetailsCourse = (array) $formData['schedule_details_course'];
                for($sdc=0;$sdc<count($dataScheduleDetailsCourse);$sdc++){
                    $arr = (array) $dataScheduleDetailsCourse[$sdc];
                    $arr['ScheduleID'] = $insert_id;
                    $this->db->insert('db_academic.schedule_details_course',$arr);
                }


                //schedule_team_teaching
                if($insertSchedule['TeamTeaching']==1){
                    $dataTemaTeaching = (array) $formData['schedule_team_teaching'];
                    for($t=0;$t<count($dataTemaTeaching);$t++){
                        $arr = (array) $dataTemaTeaching[$t];
                        $arr['ScheduleID'] = $insert_id;

                        $this->db->insert('db_academic.schedule_team_teaching',$arr);
                    }
                }



                return print_r(1);


            }
            else if($data_arr['action']=='read'){
                $dataWhere = (array) $data_arr['dataWhere'];

//                $days = (count((array) $dataWhere['Days'])>0) ? $dataWhere['Days'] : [1,2,3,4,5,6,7] ;
                $days = $this->db->order_by('ID','ASC')->get('db_academic.days')->result_array();

//                $daysName = (array) $dataWhere['DaysName'];

//                return print_r(json_encode($data_arr));
                for($i=0;$i<count($days);$i++){
                    $data[$i]['Day'] = $days[$i];
                    $data[$i]['Details'] = $this->m_api->getSchedule($days[$i]['ID'],$dataWhere);
                }
//
//
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='readOneSchedule'){

                $data = $this->m_api->getOneSchedule($data_arr['ScheduleID']);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='delete'){
                $ID = $data_arr['ScheduleID'];

                // Get Attendance
                $dataAttd = $this->db->get_where('db_academic.attendance',
                            array('ScheduleID' => $ID))->result_array();

                // Delete Attendance Students
                $this->db->delete('db_academic.attendance_students',array('ID_Attd' => $dataAttd[0]['ID']));

                // Delete Attendance
                $this->db->delete('db_academic.attendance',array('ScheduleID' => $ID));


                $tables = array('db_academic.schedule_details',
                    'db_academic.schedule_details_course', 'db_academic.schedule_team_teaching');
                $this->db->where('ScheduleID', $ID);
                $this->db->delete($tables);

                $this->db->reset_query();
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.schedule');


                return print_r(1);
            }
            else if($data_arr['action']=='deleteSubSesi') {
                $ID = $data_arr['sdID'];

                $dataSD = $this->db->get_where('db_academic.schedule_details',
                    array('ID' => $ID),1)->result_array();

                $whereAttd = array(
                    'ScheduleID' => $dataSD[0]['ScheduleID'],
                    'SDID' => $ID
                );

                $dataAttd = $this->db->get_where('db_academic.attendance',$whereAttd,1)->result_array();

                $this->db->delete('db_academic.attendance_students',array('ID_Attd'=>$dataAttd[0]['ID']));
                $this->db->delete('db_academic.attendance', $whereAttd);

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.schedule_details');

                // Update Subsesi jika tinggal 1
                $dataSubSesi = $this->db->get_where('db_academic.schedule_details',
                    array('ScheduleID'=>$dataSD[0]['ScheduleID']))->result_array();
                $SubSesi = (count($dataSubSesi)>1) ? '1' : '0';
                $this->db->set('SubSesi', $SubSesi);
                $this->db->where('ID', $dataSD[0]['ScheduleID']);
                $this->db->update('db_academic.schedule');

                return print_r(1);
            }
            else if($data_arr['action']=='edit'){

                $formData = (array) $data_arr['formData'];
                $schedule_details = (array) $formData['schedule_details'];

                // Update Schedule
                $ScheduleID = $data_arr['ID'];
                $ScheduleUpdate = (array) $formData['schedule'];
                $this->db->where('ID', $ScheduleID);
                $this->db->update('db_academic.schedule',$ScheduleUpdate);
                $this->db->reset_query();

                // Update Schedule Detail
                $dataScheduleDetailsArray = (array) $schedule_details['dataScheduleDetailsArray'];
                for($d=0;$d<count($dataScheduleDetailsArray);$d++){
                    $ds = (array) $dataScheduleDetailsArray[$d];
                    $this->db->where('ID', $ds['sdID']);
                    $this->db->update('db_academic.schedule_details',(array) $ds['update']);
                    $this->db->reset_query();
                }

                // Insert Schedule Detail
                $dataScheduleDetailsArrayNew = (array) $schedule_details['dataScheduleDetailsArrayNew'];
                for($d2=0;$d2<count($dataScheduleDetailsArrayNew);$d2++){

                    $dataNewSesi = (array) $dataScheduleDetailsArrayNew[$d2];

                    $this->db->insert('db_academic.schedule_details', $dataNewSesi);


                    // Get Schedule
                    $dataSch = $this->db->get_where('db_academic.schedule',
                        array('ID' => $dataNewSesi['ScheduleID']),1)->result_array();

                    $insert_id_SD = $this->db->insert_id();
                    $this->db->reset_query();

                    // Insert Attd
                    $dataInsetAttd = array(
                        'SemesterID' => $dataSch[0]['SemesterID'],
                        'ScheduleID' => $dataNewSesi['ScheduleID'],
                        'SDID' => $insert_id_SD
                    );
                    $this->db->insert('db_academic.attendance',$dataInsetAttd);
                    $insert_id_attd = $this->db->insert_id();


                    // Cek Mahasiswa Yang Ngambil
                    $dataMhs = $this->m_api->getDataStudents_Schedule($dataSch[0]['SemesterID'],$dataNewSesi['ScheduleID']);

                    for($m=0;$m<count($dataMhs);$m++){
                        $data_attd_s = array(
                            'ID_Attd' => $insert_id_attd,
                            'NPM' => $dataMhs[$m]['NPM']
                        );
                    }

                    $this->db->reset_query();
                }

                $this->db->where('ScheduleID', $ScheduleID);
                $this->db->delete('db_academic.schedule_team_teaching');
                $this->db->reset_query();
                // Team Teaching
                if($ScheduleUpdate['TeamTeaching']==1){
                    $dataTemaTeaching = (array) $formData['schedule_team_teaching'];
                    for($t=0;$t<count($dataTemaTeaching['teamTeachingArray']);$t++){

                        $arr = (array) $dataTemaTeaching['teamTeachingArray'][$t];
                        $this->db->insert('db_academic.schedule_team_teaching',$arr);
                        $this->db->reset_query();

                    }
                }


                // Update Subsesi jika tinggal 1
                $dataSubSesi = $this->db->get_where('db_academic.schedule_details',
                    array('ScheduleID'=>$ScheduleID))->result_array();
                $SubSesi = (count($dataSubSesi)>1) ? '1' : '0';
                $this->db->set('SubSesi', $SubSesi);
                $this->db->where('ID', $ScheduleID);
                $this->db->update('db_academic.schedule');

                return print_r(1);

            }
            else if($data_arr['action']=='readDetail') {

                $data = $this->m_api->getScheduleDetails($data_arr['ScheduleID']);

                return print_r(json_encode($data));
            }

        }
    }

    public function checkSchedule(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

//        print_r($data_arr);
        if(count($data_arr)>0 && $data_arr['action']=='check'){
            $dataFilter =(array) $data_arr['formData'];
            $data = $this->m_api->__checkSchedule($dataFilter);

            return print_r(json_encode($data));
        }
    }

    public function crudProgramCampus(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->getProgramCampus();
                return print_r(json_encode($data));
            }
        }
    }

    public function crudSemester(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->getSemester($data_arr['order']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='ReadSemesterActive'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getSemesterActive($formData['CurriculumID'],$formData['ProdiID'],$formData['Semester'],$formData['IsSemesterAntara']);
                return print_r(json_encode($data));
            }
        }
    }

    public function crudCourseOfferings(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.course_offerings',$formData);
                $insert_id = $this->db->insert_id();
                return print_r($insert_id);
            }
            else if($data_arr['action']=='edit'){
                $formData = (array) $data_arr['formData'];

                $this->db->where('ID', $data_arr['OfferID']);
                $this->db->update('db_academic.course_offerings',$formData);

                return print_r($data_arr['OfferID']);
            }
            else if($data_arr['action']=='read'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getAllCourseOfferings($formData['SemesterID'],$formData['CurriculumID'],
                    $formData['ProdiID'],$formData['Semester'],$formData['IsSemesterAntara']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='readgabungan'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getAllCourseOfferingsMKU($formData['SemesterID']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='editSemester') {
//                $formData = (array) $data_arr['formData'];
                $this->db->set('ToSemester', $data_arr['ToSemester']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.course_offerings');

                return print_r(1);
            }
            // Untuk mengecek apakah MK Offering sudah dibuatkan jadwal atau belum
            else if($data_arr['action']=='checkCourse'){
                $dataWhere = (array) $data_arr['dataWhere'];
//                $query = $this->db
//                    ->get_where('db_academic.schedule', $dataWhere)
//                    ->result_array();

                $query = $this->m_api->__checkCourse($dataWhere['SemesterID'],$dataWhere['MKID']);

                if(count($query)>0){
                    return print_r(0);
                } else {
                    return print_r(1);
                }
            }
            else if($data_arr['action']=='delete'){

                $query = $this->db->get_where('db_academic.course_offerings', array('ID' => $data_arr['OfferID']), 1)->result_array();

                if(count($query)>0){
                    $Arr_CDID = json_decode($query[0]['Arr_CDID']);

//                    print_r($Arr_CDID);
//
//                    exit;

                    if(count($Arr_CDID)>1){
                        $result = [];
                        if (($key = array_search($data_arr['CDID'], $Arr_CDID)) !== false) {
                            for($a=0;$a<count($Arr_CDID);$a++){
                                if($a!=$key){
                                    array_push($result,$Arr_CDID[$a]);
                                }
                            }
                        }

                        $this->db->set('Arr_CDID', json_encode($result));
                        $this->db->where('ID', $data_arr['OfferID']);
                        $this->db->update('db_academic.course_offerings');

                        return print_r(1);


                    } else if(count($Arr_CDID)==1){
                        $this->db->where('ID', $data_arr['OfferID']);
                        $this->db->delete('db_academic.course_offerings');
                        return print_r(1);

                    }


//                    print_r(json_encode($r));


                }

            }
            else if($data_arr['action']=='readToSchedule') {
                $formData = (array) $data_arr['formData'];

                $data = $this->m_api->getOfferingsToSetSchedule($formData);
                return print_r(json_encode($data));

            }
        }
    }


    public function getAllStudents(){

        $data = $this->m_api->__getTahunAngkatan();

        return print_r(json_encode($data));
    }

    public function crudeStudent(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->__getStudentByNPM($formData['ta'],$formData['NPM']);

                return print_r(json_encode($data));

            }
        }
    }

    public function getClassGroup(){
//        $token = $this->input->post('token');
//        $key = "UAP)(*";
//        $data_arr = (array) $this->jwt->decode($token,$key);
        $data_arr = $this->getInputToken();

        $data = $this->m_api->__checkClassGroup(
            $data_arr['ProgramsCampusID'],
            $data_arr['SemesterID'],
            $data_arr['ProdiCode'],
            $data_arr['IsSemesterAntara']
        );

        $result = array(
            'Group' => $data_arr['ProdiCode'].'-'.(count($data)+1)
        );

        return print_r(json_encode($result));
    }

    public function getClassGroupParalel(){
        $data_arr = $this->getInputToken();
        $data = $this->m_api->__checkClassGroupParalel(
            $data_arr['ProgramsCampusID'],
            $data_arr['SemesterID'],
            $data_arr['ProdiCode'],
            $data_arr['IsSemesterAntara']
        );

        return print_r(json_encode($data));;
    }

    public function crudClassroom(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllClassRoom();
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'add'){
                $formData = (array) $data_arr['formData'];

                // Cek Apakah ruangan sudah di input
                $this->db->where('Room', $formData['Room']);
                $room = $this->db->get('db_academic.classroom')->result_array();


                if(count($room)>0){
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.classroom',$formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action'] == 'edit'){
                $formData = (array) $data_arr['formData'];

                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.classroom',$formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action'] == 'delete'){
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.classroom');
                return print_r($ID);
            }

        }

    }

    public function crudGrade(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllGrade();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                // Cek grade
                $this->db->where('Grade', $formData['Grade']);
                $grade = $this->db->get('db_academic.grade')->result_array();

                if(count($grade)>0){
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.grade',$formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='edit'){
                $formData = (array) $data_arr['formData'];
                // Cek grade
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.grade',$formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action'] == 'delete'){
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.grade');
                return print_r($ID);
            }
        }
    }

    public function crudRangeCredits() {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getRangeCredits();
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'delete'){
//                print_r($data_arr);
//                exit;
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.range_credits');
                return print_r(1);
            }
            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.range_credits', $formData);
                $insert_id = $this->db->insert_id();
                return print_r($insert_id);
            }
            else if($data_arr['action']=='edit'){
                $ID = $data_arr['ID'];
                $formData = (array) $data_arr['formData'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.range_credits',$formData);

                return print_r($ID);
            }
        }
    }

    public function crudTimePerCredit(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllTimePerCredit();
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'add'){
                $formData = (array) $data_arr['formData'];
                // Cek Time
                $this->db->where('Time', $formData['Time']);
                $time = $this->db->get('db_academic.time_per_credits')->result_array();

                if(count($time)>0){
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.time_per_credits',$formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action'] == 'delete') {
                $time = $this->db->get('db_academic.time_per_credits')->result_array();

                if(count($time)>1){
                    $ID = $data_arr['ID'];
                    $this->db->where('ID', $ID);
                    $this->db->delete('db_academic.time_per_credits');
                    $result = array(
                        'inserID' => $ID
                    );

                } else {
                    $result = array(
                        'inserID' => 0
                    );

                }
                return print_r(json_encode($result));
            }
        }
    }

    public function crudLecturer(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getLecturerDetail($NIP);
                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='readMini'){
                $NIP = $data_arr['NIP'];
                $data = $this->db->select('NIP,NIDN,Name,TitleAhead,TitleBehind,PositionMain,Phone,
                                        HP,Email,EmailPU,Password,Address,Photo,Photo_new')
                    ->get_where('db_employees.employees',array('NIP'=>$NIP),1)
                    ->result_array();



//                $data = $this->db->query('SELECT NIP,NIDN,Name,TitleAhead,TitleBehind,PositionMain,Phone,HP,Email,EmailPU,Password,Address,Photo,Photo_new
//                                            FROM db_employees.employees
//                                            WHERE NIP = "'.$NIP.'" LIMIT 1 ')->result_array();
//
//                print_r($data);

                if(count($data)>0){
                    $sp = explode('.',$data[0]['PositionMain']);
                    $DiviosionID = $sp[0];
                    $PositionID = $sp[1];

                    $div = $this->db->get_where('db_employees.division',array('ID'=>$DiviosionID),1)->result_array();
                    $data[0]['Division'] = $div[0]['Division'];

                    $pos = $this->db->get_where('db_employees.position',array('ID'=>$PositionID),1)->result_array();
                    $data[0]['Position'] = $pos[0]['Position'];

                    return print_r(json_encode($data[0]));
                } else {
                    return print_r(json_encode($data));
                }



            }

        }

    }

    public function insertWilayahURLJson()
    {
        $data = $this->input->post('data');
        $generate = $this->m_api->saveDataWilayah($data);
        echo json_encode($generate);
    }

    public function insertSchoolURLJson()
    {
        $data = $this->input->post('data');
        $generate = $this->m_api->saveDataSchool($data);
        echo json_encode($generate);
    }

    public function getWilayahURLJson()
    {
        $generate = $this->m_api->getdataWilayah();
        echo json_encode($generate);
    }

    public function getSMAWilayah()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $result = $this->m_api->__getSMAWilayah($data_arr['wilayah']);

        return print_r(json_encode($result));
    }

    public function getDataRegisterBelumBayar()
    {
        $getData = $this->m_api->getDataRegisterBelumBayar();
        echo json_encode($getData);
    }

    public function getDataRegisterTelahBayar()
    {
        $getData = $this->m_api->getDataRegisterTelahBayar();
        echo json_encode($getData);
    }

    public function crudStudyPlanning()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $dataWhere = (array) $data_arr['dataWhere'];
                $data = $this->m_api->__getStudyPlanning($dataWhere);
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'detailStudent'){
                $data = $this->m_api->getDetailStudyPlanning($data_arr['NPM'],$data_arr['ta']);
                return print_r(json_encode($data));
            }
        }

    }

    public function crudYearAcademic()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if($data_arr['action']=='read'){
                $data = $this->db->order_by('YearAcademic','ASC')->get('db_academic.std_ta')
                    ->result_array();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='add') {
                $dataInsert = (array) $data_arr['dataInsert'];
                $this->db->insert('db_academic.std_ta',$dataInsert);
                $insert_id = $this->db->insert_id();

                $db_new = 'ta_'.$dataInsert['YearAcademic'];

                $this->m_api->createDBYearAcademicNew($db_new);

                return print_r($insert_id);
            }
        }


    }

    public function filterStudents(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='readStudents'){
                $filter = (array) $data_arr['dataFilter'];

//                $data = $this->m_api->__filterStudents($filter);
                $data = $this->m_api->__filterStudents($filter);

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='delete'){
                $this->db->where('ID', $data_arr['IDMA']);
                $this->db->delete('db_academic.mentor_academic');
                return print_r(1);

            }
            else if($data_arr['action']=='add'){
                $dataForm = (array) $data_arr['dataForm'];
                $dataNPM = (array) $data_arr['dataNPM'];

                for($i=0;$i<count($dataNPM);$i++){
                    $dataForm['NPM'] = $dataNPM[$i];
//                    print_r($dataForm);
                    $this->db->insert('db_academic.mentor_academic',$dataForm);
                }

                return print_r(1);
//                print_r($dataNPM);
            }
        }
    }

    public function crudTuitionFee(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $ClassOf = $data_arr['ClassOf'];

                $prodi = $this->db->select('ID,NameEng')->get('db_academic.program_study')->result_array();

                $result = [];

                for($i=0;$i<count($prodi);$i++){
                    $data = $this->db->query('SELECT tf.ID,tf.PTID,tf.Cost,pt.Description,pt.Abbreviation FROM db_finance.tuition_fee tf 
                                                    LEFT JOIN db_finance.payment_type pt ON (tf.PTID = pt.ID)
                                                    LEFT JOIN db_academic.program_study ps ON (tf.ProdiID = ps.ID)
                                                    WHERE tf.ClassOf = "'.$ClassOf.'" AND tf.ProdiID = "'.$prodi[$i]['ID'].'" 
                                                    ORDER BY tf.ProdiID, tf.PTID ASC ')->result_array();
                    if(count($data)>0){
                        $data_p = array(
                            'ProdiID' => $prodi[$i]['ID'],
                            'ProdiName' => $prodi[$i]['NameEng'],
                            'Detail' => $data
                        );
                        array_push($result,$data_p);
                    }

                }

                return print_r(json_encode($result));
            }
        }
    }

    public function getEmployeesBy($division = null,$position = null)
    {
        try{
            $key = "UAP)(*";
            $division = $this->jwt->decode($division,$key);
            $position = $this->jwt->decode($position,$key);
            $getData = $this->m_api->getEmployeesBy($division,$position);
            echo json_encode($getData);
        }
        catch(Exception $e)
        {
            echo json_encode('No Result Data');
        }

    }

    public function getFormulirOfflineAvailable()
    {
        $getData = $this->m_api->getFormulirOfflineAvailable();
        echo json_encode($getData);
    }

    public function AutoCompleteSchool()
    {
        $input = $this->getInputToken();
        $data['response'] = 'true'; //mengatur response
        $data['message'] = array(); //membuat array
        $getData = $this->m_api->getSchoolbyNameAC($input['School']);
        for ($i=0; $i < count($getData); $i++) {
            $data['message'][] = array(
                'label' => $getData[$i]['SchoolName'],
                'value' => $getData[$i]['ID']
            );
        }
        echo json_encode($data);
    }

    public function getSumberIklan()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.source_from_event',1);
        echo json_encode($getData);
    }

    public function getPriceFormulirOffline()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.price_formulir_offline',1);
        echo json_encode($getData);
    }

    public function getEvent()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.price_event',1);
        echo json_encode($getData);
    }

    public function getDocument()
    {
        $input = $this->getInputToken();
        $this->load->model('admission/m_admission');
        $getData = $this->m_admission->getDataDokumentRegister($input['ID_register_formulir']);
        echo json_encode($getData);
    }

    public function crudJadwalUjian(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->getJadwalUjian();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkDateExam'){
                $data = $this->m_api->getDateExam();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkCourse'){
                $data = $this->m_api
                    ->__checkDataCourseForExam($data_arr['ScheduleID'],$data_arr['Type']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                $dataStudents = (array) $data_arr['dataStudents'];

                $this->db->insert('db_academic.exam',$formData);
                $insert_id = $this->db->insert_id();

                for($e=0;$e<count($dataStudents);$e++){
                    $dataM = (array) $dataStudents[$e];
                    $dataInsert = array(
                        'ExamID' => $insert_id,
                        'MhswID' => $dataM['MhswID'],
                        'NPM' => $dataM['NPM'],
                        'DB_Students' => $dataM['DB_Students']
                    );
                    $this->db->insert('db_academic.exam_details',$dataInsert);
                }

                return print_r(1);

//                $data = $this->m_api->
            }
            else if($data_arr['action']=='readSchedule'){

                $data = $this->m_api->getScheduleExam(
                    $data_arr['SemesterID'],
                    $data_arr['Type'],
                    $data_arr['ProdiID']
                );

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='save2pdfLayout'){

            }
        }
    }

    public function crudEmployees(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->db->select('NIP,Name')->get('db_employees.employees')->result_array();
                return print_r(json_encode($data));
            }
        }

    }

    public function getProvinsi()
    {
        $generate = $this->m_master->showData_array('db_admission.province');
        echo json_encode($generate);
    }

    public function getRegionByProv()
    {
        $input = $this->getInputToken();
        $generate = $this->m_master->getRegionByProv($input['selectProvinsi']);
        echo json_encode($generate);
    }

    public function getDistrictByRegion()
    {
        $input = $this->getInputToken();
        $generate = $this->m_master->getDistrictByRegion($input['selectRegion']);
        echo json_encode($generate);
    }

    public function getTypeSekolah()
    {
        $generate = $this->m_master->getTypeSekolah();
        echo json_encode($generate);
    }

    public function getNotification()
    {
        $generateCount = $this->m_master->CountgetNotification();
        $generate = $this->m_master->getNotification();
        echo json_encode(array('count' => $generateCount, 'data'=>$generate));
    }

    public function getBasePaymentTypeSelectOption()
    {
        $generate = $this->m_master->showData_array('db_finance.payment_type');
        echo json_encode($generate);
    }

    public function getNotification_divisi()
    {
        $generateCount = $this->m_master->CountgetNotificationDivisi();
        $generate = $this->m_master->getNotificationDivisi();
        //print_r($generate);
        // $generate = json_encode($generate);

        $output = array(

            'count'  => $generateCount,

            'data'   => $generate,

        );

        echo json_encode($output);

    }

    public function getSMAWilayahApproval()
    {
        $generate = $this->m_master->getSMAWilayahApproval();
        echo json_encode($generate);
    }

    public function crudScore(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $ScheduleID = $data_arr['ScheduleID'];
                $data = $this->m_api->__getScore($ScheduleID);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='grade'){
                $Score = $data_arr['Score'];
                $data = $this->m_api->__getGrade($Score);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkGrade'){
                $data = $this->db->get_where('db_academic.grade_course',
                    array('ScheduleID'=>$data_arr['ID']),1)->result_array();

                $result = array(
                    'Status' => 0,
                    'Details' => $data
                );
                if(count($data)>0){
                    if($data[0]['Silabus']!='' && $data[0]['Silabus']!=null &&
                        $data[0]['SAP']!='' && $data[0]['SAP']!=null &&
                        $data[0]['Assigment']!='' && $data[0]['Assigment']!=null &&
                        $data[0]['UTS']!='' && $data[0]['UTS']!=null &&
                        $data[0]['UAS']!='' && $data[0]['UAS']!=null &&
                        $data[0]['Status']=='2'){
                        $result['Status']=1;
                        $result['Details']=$data[0];
                    }
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='update'){

                $formUpdate = (array) $data_arr['formUpdate'];

                for($s=0;$s<count($formUpdate);$s++){
                    $dataF = (array) $formUpdate[$s];

                    $DB_Student = $dataF['DB_Student'];
                    $ID = $dataF['ID'];

                    $dataToUpdate = (array)$dataF['dataForm'];

//                    print_r($dataToUpdate);

                    $this->db->where('ID', $ID);
                    $this->db->update($DB_Student.'.study_planning',$dataToUpdate);
                }

                return print_r(1);

            }
            else if($data_arr['action']=='getGrade'){
                $ScheduleID = $data_arr['ScheduleID'];
                $data = $this->m_api->__getGradeSchedule($ScheduleID);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='gradeUpdate'){
                $this->db->set('Status', $data_arr['Status']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.grade_course');
                return print_r(1);
            }

            else if($data_arr['action']=='dataCourse'){

                $data = $this->m_api->getDataCourse2Score($data_arr['SemesterID']
                    ,$data_arr['ProdiID'],$data_arr['CombinedClasses'],$data_arr['IsSemesterAntara']);
                return print_r(json_encode($data));
            }
        }
    }

    public function getBaseDiscountSelectOption()
    {
        $generate = $this->m_master->showData_array('db_finance.discount');
        echo json_encode($generate);

    }

    public function crudAttendance(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->__getDataAttendance($data_arr['ScheduleID']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='getAttendance'){

                $data = $this->m_api->__getAttendanceSchedule($data_arr['AttendanceID']);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='getAttdLecturers'){
                $ID = $data_arr['ID'];
                $No = $data_arr['No'];
                $data = $this->db->get_where('db_academic.attendance',
                            array('ID'=>$ID))->result_array();

                $coor = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule s 
                                            LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                            WHERE s.ID = "'.$data[0]['ScheduleID'].'"
                                              ')->result_array();

                $teamt = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule_team_teaching stt 
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                    WHERE stt.ScheduleID = "'.$data[0]['ScheduleID'].'" 
                                                    ')->result_array();

                if(count($teamt)>0){
                    for($t=0;$t<count($teamt);$t++){
                        array_push($coor,$teamt[$t]);
                    }
                }

                $res = array(
                    'NIP' => $data[0]['NIP'.$No],
                    'BAP' => $data[0]['BAP'.$No],
                    'Date' => $data[0]['Date'.$No],
                    'In' => $data[0]['In'.$No],
                    'Out' => $data[0]['Out'.$No],
                    'DetailLecturers' => $coor
                );
                return print_r(json_encode($res));
            }
            else if($data_arr['action']=='UpdtAttdLecturers'){

                $ID = $data_arr['ID'];
                $No = $data_arr['No'];

                $formUpdate = (array) $data_arr['formUpdate'];

//                print_r($formUpdate);

                $dataUpdate = array(
                    'NIP'.$No => $formUpdate['NIP'],
                    'BAP'.$No => $formUpdate['BAP'],
                    'Date'.$No => $formUpdate['Date'],
                    'In'.$No => $formUpdate['In'],
                    'Out'.$No => $formUpdate['Out']
                );

                $this->db->where('ID', $ID);
                $this->db->update('db_academic.attendance', $dataUpdate);

                return print_r(1);
            }

            else if($data_arr['action']=='filterPresensi'){

                if($data_arr['CombinedClasses']=='0'){

                    $data = $this->db->query('SELECT s.* FROM db_academic.schedule s 
                                              LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                              WHERE s.SemesterID = "'.$data_arr['SemesterID'].'" 
                                              AND CombinedClasses = "0" 
                                              AND sdc.ProdiID = "'.$data_arr['ProdiID'].'" 
                                              ORDER BY s.ClassGroup ASC')->result_array();

                    $result = $data;

                } else {
                    $data_where = array(
                        'SemesterID' => $data_arr['SemesterID'],
                        'CombinedClasses' => '1'
                    );
                    $data = $this->db->order_by('ClassGroup', 'ASC')
                        ->get_where('db_academic.schedule',
                        $data_where)->result_array();

                    $result = $data;
                }

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='getAttdStudents'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];
                $data = $this->m_api->__getStudensAttd($SemesterID,$ScheduleID,$SDID,$Meeting);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='addAttdStudents'){
                $dataUpdate = (array) $data_arr['dataUpdate'];
//                print_r($dataUpdate);
                for($u=0;$u<count($dataUpdate);$u++){
                    $dataObj = (array) $dataUpdate[$u];

                    $d_updt = array(
                        'M'.$dataObj['Meeting'] => $dataObj['Status'],
                        'D'.$dataObj['Meeting'] => $dataObj['Description']
                    );

                    $this->db->where('ID', $dataObj['ID']);
                    $this->db->update('db_academic.attendance_students', $d_updt);
                }

                return print_r(1);
            }
        }
    }

    public function crudScheduleExchange(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='readExchange'){
                $ID_Attd = $data_arr['ID_Attd'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];

                $data = $this->m_api->__getdataExchange($ID_Attd,$ScheduleID,$SDID,$Meeting);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='addSceduleEx'){
                $dataInsert = (array) $data_arr['dataInsert'];

                // Cek Apakah sudah ada atau belum
                $dataWhere = array(
                    'ID_Attd' => $dataInsert['ID_Attd'],
                    'Meeting' => $dataInsert['Meeting']
                );

                $dataC = $this->db->get_where('db_academic.schedule_exchange',
                                $dataWhere,1)->result_array();

                if(count($dataC)>0){
                    $dataUpdate = array(
                        'NIP' => $dataInsert['NIP'],
                        'ClassroomID' => $dataInsert['ClassroomID'],
                        'Date' => $dataInsert['Date'],
                        'DayID' => $dataInsert['DayID'],
                        'StartSessions' => $dataInsert['StartSessions'],
                        'EndSessions' => $dataInsert['EndSessions'],
                        'Status' => $dataInsert['Status']
                    );

                    $this->db->where('ID', $dataC[0]['ID']);
                    $this->db->update('db_academic.schedule_exchange', $dataUpdate);

                } else {
                    $this->db->insert('db_academic.schedule_exchange',$dataInsert);
                }

                return print_r(1);

            }
            else if($data_arr['action']=='deleteSceduleEx'){
                $dataWhere = array(
                    'ID_Attd' => $data_arr['ID_Attd'],
                    'Meeting' => $data_arr['Meeting']
                );
                $dataC = $this->db->get_where('db_academic.schedule_exchange',
                    $dataWhere)->result_array();

                if(count($dataC)>0){
                    $this->db->delete('db_academic.schedule_exchange',$dataWhere);
                }

                return print_r(1);

            }
        }
    }



}