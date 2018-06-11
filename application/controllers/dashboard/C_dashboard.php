<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_dashboard extends Globalclass {

    public function temp($content)
    {
        parent::template($content);
    }

    public function index()
    {
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('dashboard/dashboard',$data,true);
        $this->temp($content);
    }

    public function change_departement(){
        $dpt = $this->input->post('departement');
        $IDDivision = $this->input->post('IDDivision');
        $this->session->set_userdata('IDdepartementNavigation', ''.$IDDivision);
        parent::__setDepartement($dpt);
    }

    public function profile($username=''){
        $data['']=123;
        $content = $this->load->view('dashboard/profile','',true);
        $this->temp($content);
    }

    public function load_data_registration_upload()
    {
        $content = $this->load->view('page/load_data_registration_upload',$this->data,true);
        echo $content;
    }

    public function readNotificationDivision()
    {
        $input = $this->getInputToken();
        $this->load->model('master/m_master');
        $this->m_master->readNotificationDivision($input['IDDivision']);
        echo json_encode(1);
    }

    public function testadi()
    {
        for ($i=0; $i <= 100 ; $i= $i + 5) {
            $dataSave = array(
                'discount' => $i,
            );
            $this->db->insert('db_finance.discount', $dataSave);
        }

        echo 'test';
    }


}