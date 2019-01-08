<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_employees extends HR_Controler {

    function __construct()
    {
        parent::__construct();
//        $this->session->set_userdata('departement_nav', 'academic');
        $this->load->model('akademik/m_akademik');
        $this->load->model('hr/m_hr');
        $this->load->model('master/m_master');
    }


    public function temp($content)
    {
        parent::template($content);
    }

    public function tab_menu($page)
    {
        $department = parent::__getDepartement();
        $data['page'] = $page;
        $content = $this->load->view('page/'.$department.'/employees/tab_employees',$data,true);
        $this->temp($content);
    }


    public function employees()
    {
        $department = parent::__getDepartement();
        $page = $this->load->view('page/'.$department.'/employees/employees','',true);
        $this->tab_menu($page);
    }

    public function input_employees(){
        $department = parent::__getDepartement();
        // get Prodi
        $data['ProdiArr'] = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        $page = $this->load->view('page/'.$department.'/employees/inputEmployees',$data,true);
        $this->tab_menu($page);
    }

    public function edit_employees($NIP){
        $department = parent::__getDepartement();
        // get Prodi
        $data['ProdiArr'] = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        $arrEmp = $this->db->get_where('db_employees.employees',array('NIP'=>$NIP),1)->result_array();
        $data['arrEmp'] = (count($arrEmp)>0) ? $arrEmp[0] : [];

        // Cek apakah NIP dapat di hapus secara permanen atau tidak
        $data['btnDelPermanent'] = $this->m_hr->checkPermanentDelete($NIP);

        $page = $this->load->view('page/'.$department.'/employees/editEmployees',$data,true);
        $this->tab_menu($page);
    }

    public function upload_photo(){

        $fileName = $this->input->get('fileName');

        $config['upload_path']          = './uploads/employees/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['max_size']             = 8000; // 8 mb
        $config['file_name']            = $fileName;

        if(is_file('./uploads/employees/'.$fileName)){
            unlink('./uploads/employees/'.$fileName);
        }

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('userfile')){
            $error = array('error' => $this->upload->display_errors());
            return print_r(json_encode($error));
        }
        else {

            $success = array('success' => $this->upload->data());
            $success['success']['formGrade'] = 0;

            return print_r(json_encode($success));
        }



    }

    public function upload_ijazah(){

        $fileName = $this->input->get('fileName');

        $config['upload_path']          = './uploads/ijazah/';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 8000; // 8 mb
        $config['file_name']            = $fileName;

//        $pathUn = realpath(APPPATH);
        if(is_file('./uploads/ijazah/'.$fileName)){
            unlink('./uploads/ijazah/'.$fileName);
        }


        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('userfile')){
            $error = array('error' => $this->upload->display_errors());
            return print_r(json_encode($error));
        }
        else {

            $success = array('success' => $this->upload->data());
            $success['success']['formGrade'] = 0;

            return print_r(json_encode($success));
        }



    }


    // =============================================

    public function tab_menu_report($page)
    {
        $department = parent::__getDepartement();
        $data['page'] = $page;
        $content = $this->load->view('page/'.$department.'/monitoring/tab_monitoring',$data,true);
        $this->temp($content);
    }

    public function with_range_date(){

        $department = parent::__getDepartement();
        $page = $this->load->view('page/'.$department.'/monitoring/with_range_date','',true);
        $this->tab_menu_report($page);

    }


}
