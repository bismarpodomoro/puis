<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_matakuliah extends Academic_Controler {

    function __construct()
    {
        parent::__construct();
//        $this->session->set_userdata('departement_nav', 'academic');
        $this->load->model('akademik/m_matakuliah');
    }


    public function temp($content)
    {
        parent::template($content);
    }

    public function mata_kuliah()
    {
        $department = parent::__getDepartement();
//        $data['data_mk'] = $this->m_matakuliah->__getAllMK();
        $content = $this->load->view('page/'.$department.'/matakuliah/matakuliah','',true);
        $this->temp($content);
    }

    public function dataTableMK(){
        $department = parent::__getDepartement();
        $data['data_mk'] = $this->m_matakuliah->__getAllMK();
        $this->load->view('page/'.$department.'/matakuliah/matakuliah_table',$data);
    }

}
