<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_master extends CI_Model {


    function __construct()
    {
        parent::__construct();
    }
    public function get_departement()
    {
        $data = $this->db->query('SELECT * FROM db_navigation.departement ORDER BY priority ASC');

        return $data->result_array();
    }

    public function __getService($IDDivision){
        $data = $this->db->get_where('db_employees.rule_service',array('IDDivision' => $IDDivision))->result_array();
        $result =[];
        if(count($data)>0){
            for($i=0;$i<count($data);$i++){
                array_push($result,$data[$i]['IDService']);
            }
        }

        return $result;

    }

    public function showData($tabel)
    {
        $sql = "select * from ".$tabel;
        $query=$this->db->query($sql, array());
        return $query->result();
    }


    public function showData_array($tabel)
    {
        $sql = "select * from ".$tabel;
        $query=$this->db->query($sql, array());
        return $query->result_array();
    }



    public function showDataActive_array($tabel,$Active)
    {
        $sql = "select * from ".$tabel." where Active = ?";
        $query=$this->db->query($sql, array($Active));
        return $query->result_array();
    }

    public function showDataActive($tabel)
    {
        $sql = "select * from ".$tabel." where active = 1";
        $query=$this->db->query($sql, array());
        return $query->result();
    }

    public function caribasedprimary($tabel,$fieldPrimary,$valuePrimary)
    {
        $sql = "select * from ".$tabel." where ".$fieldPrimary." = ?";
        $query=$this->db->query($sql, array($valuePrimary));
        return $query->result_array();
    }

    public function carifilestemp() {

       $logged_in = $this->session->userdata('NIP');

       $sql = "SELECT * FROM db_employees.temp_files AS a WHERE a.user_create = '.$logged_in.' ";
       $query=$this->db->query($sql, array());
        return $query->result_array();
    
       //$query=$this->db->query($sql, result_array());
        
       //$this->db->where("user_create", $logged_in);
       //return $this->db->get("temp_files")->row_array();
    }
    
   

    public function save_image() {

        //$id_survey = $this->input->post('id_survey');
        //$dates = date("Y-m-d H:i:s");
        $filePath = $upload_data['file_name'];
    
        $data = array(
            'kode_survey_penyewa' => $id_survey,
            'nama_file' => $filePath,
            'user_upload' => $this->session->userdata('user_id'),
            'date_upload' => $dates,
            'ip_address' => $ipget,
            'mac_address' => $mac_address   
        );
        
        $this->db->insert('upload_foto_survey', $data);
    }


    public function getColumnTable($table)
    {
        $arr = array();
        $sql = "SHOW COLUMNS FROM ".$table;
        $query=$this->db->query($sql, array())->result();
        $temp = array();
        foreach ($query as $key) {
            $temp[] = $key->Field;
        }
        $arr = array('query' => $query,'field' => $temp);
        return $arr;
    }

    public function inserData_count_account($CountAccount)
    {
        $dataSave = array(
            'CountAccount' => $CountAccount,
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.count_account', $dataSave);

        $sql = "select a.ID from db_admission.count_account as a where a.active = 1 order by a.ID desc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        $ID = $query[0]['ID'];

        $sql = "update db_admission.count_account set Active = 0 where ID != ".$ID;
        $query=$this->db->query($sql, array());

    }

    public function editData_count_account($CountAccount,$ID)
    {
        $sql = "update db_admission.count_account set CountAccount = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($CountAccount));
    }

    public function getActive_count_account($ID,$Active)
    {
        if ($Active == 0) {
            $sql = "update db_admission.count_account set Active = 1 where ID = ".$ID;
            $sql2 = "update db_admission.count_account set Active = 0 where ID != ".$ID;
            $query2=$this->db->query($sql2, array());
        }
        else
        {
            $sql = "update db_admission.count_account set Active = 0 where ID = ".$ID;
        }
        $query=$this->db->query($sql, array());

    }

    public function delete_count_account($ID)
    {
        $sql = "delete from db_admission.count_account where ID = ".$ID;
        $query=$this->db->query($sql, array());
    }

    public function inserData_email_to($email_to,$fungsi)
    {
        $dataSave = array(
            'EmailTo' => $email_to,
            'Function' => $fungsi,
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.email_to', $dataSave);
    }

    public function editData_email_to($email_to,$fungsi,$ID)
    {
        $sql = "update db_admission.email_to set EmailTo = ? , Function = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($email_to,$fungsi));
    }

    public function delete_email_to($ID)
    {
        $sql = "delete from db_admission.email_to where ID = ".$ID;
        $query=$this->db->query($sql, array());
    }

    public function getActive_email_to($ID,$Active)
    {
        if ($Active == 0) {
            $sql = "update db_admission.email_to set Active = 1 where ID = ".$ID;
            /*$sql2 = "update db_admission.email_to set Active = 0 where ID != ".$ID;
            $query2=$this->db->query($sql2, array());*/
        }
        else
        {
            $sql = "update db_admission.email_to set Active = 0 where ID = ".$ID;
        }
        $query=$this->db->query($sql, array());
    }

    public function inserData_lama_pembayaran($Longtime)
    {
        $dataSave = array(
            'Longtime' => $Longtime,
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.deadline_register', $dataSave);

        $sql = "select a.ID from db_admission.deadline_register as a where a.active = 1 order by a.ID desc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        $ID = $query[0]['ID'];

        $sql = "update db_admission.deadline_register set Active = 0 where ID != ".$ID;
        $query=$this->db->query($sql, array());
    }

    public function editData_lama_pembayaran($Longtime,$ID)
    {
        $sql = "update db_admission.deadline_register set Longtime = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($Longtime));
    }

    public function delete_id_table($ID,$table)
    {
        $sql = "delete from db_admission.".$table." where ID = ".$ID;
        $query=$this->db->query($sql, array());
    }

    public function delete_id_table_all_db($ID,$table)
    {
        $sql = "delete from ".$table." where ID = ".$ID;
        $query=$this->db->query($sql, array());
    }

    public function getActive_id_active_table($ID,$Active,$table)
    {
        if ($Active == 0) {
            $sql = "update db_admission.".$table." set Active = 1 where ID = ".$ID;
            $sql2 = "update db_admission.".$table." set Active = 0 where ID != ".$ID;
            $query2=$this->db->query($sql2, array());
        }
        else
        {
            $sql = "update db_admission.".$table." set Active = 0 where ID = ".$ID;
        }
        $query=$this->db->query($sql, array());
    }

    public function inserData_harga_formulir_offline($PriceFormulir)
    {
        $dataSave = array(
            'PriceFormulir' => $PriceFormulir,
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.price_formulir_offline', $dataSave);

        $sql = "select a.ID from db_admission.price_formulir_offline as a where a.active = 1 order by a.ID desc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        $ID = $query[0]['ID'];

        $sql = "update db_admission.price_formulir_offline set Active = 0 where ID != ".$ID;
        $query=$this->db->query($sql, array());
    }

    public function inserData_harga_formulir($PriceFormulir)
    {
        $dataSave = array(
            'PriceFormulir' => $PriceFormulir,
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.price_formulir', $dataSave);

        $sql = "select a.ID from db_admission.price_formulir as a where a.active = 1 order by a.ID desc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        $ID = $query[0]['ID'];

        $sql = "update db_admission.price_formulir set Active = 0 where ID != ".$ID;
        $query=$this->db->query($sql, array());
    }

    public function editData_harga_formulir_offline($PriceFormulir,$ID)
    {
        $sql = "update db_admission.price_formulir_offline set PriceFormulir = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($PriceFormulir));
    }

    public function editData_harga_formulir($PriceFormulir,$ID)
    {
        $sql = "update db_admission.price_formulir set PriceFormulir = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($PriceFormulir));
    }

    public function getActive_id_activeAll_table($ID,$Active,$table)
    {
        if ($Active == 0) {
            $sql = "update db_admission.".$table." set Active = 1 where ID = ".$ID;
        }
        else
        {
            $sql = "update db_admission.".$table." set Active = 0 where ID = ".$ID;
        }
        $query=$this->db->query($sql, array());
    }

    public function getActive_id_activeAll_table_allDB($ID,$Active,$table)
    {
        if ($Active == 0) {
            $sql = "update ".$table." set Active = 1 where ID = ".$ID;
        }
        else
        {
            $sql = "update ".$table." set Active = 0 where ID = ".$ID;
        }
        $query=$this->db->query($sql, array());
    }

    public function inserData_jenis_tempat_tinggal($jenis_tempat_tinggal)
    {
        $dataSave = array(
            'JenisTempatTinggal' => ucwords($jenis_tempat_tinggal),
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.register_jtinggal_m', $dataSave);
    }

    public function editData_jenis_tempat_tinggal($jenis_tempat_tinggal,$ID)
    {
        $sql = "update db_admission.register_jtinggal_m set JenisTempatTinggal = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($jenis_tempat_tinggal));
    }

    public function inserData_pendapatan($Income)
    {
        $dataSave = array(
            'Income' => ucwords($Income),
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.register_income_m', $dataSave);
    }

    public function editData_pendapatan($Income,$ID)
    {
        $sql = "update db_admission.register_income_m set Income = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($Income));
    }

    public function inserData_document_checklist($DocumentChecklist)
    {
        $dataSave = array(
            'DocumentChecklist' => ucwords($DocumentChecklist),
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.reg_doc_checklist', $dataSave);
    }

    public function editData_document_checklist($DocumentChecklist,$ID,$Required)
    {
        $sql = "update db_admission.reg_doc_checklist set DocumentChecklist = ?, Required = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($DocumentChecklist,$Required));
    }

    public function getDataFormulirOnline($tahun)
    {
        $sql = "select a.ID,a.Years,a.FormulirCode,a.Status,a.CreateAT,b.Name from db_admission.formulir_number_online_m as a join db_employees.employees as b on a.CreatedBY = b.NIP where a.Years = ?";
        $query=$this->db->query($sql, array($tahun))->result_array();
        return $query;
    }

    public function count_account()
    {
        $sql = "select CountAccount from db_admission.count_account as a where a.active = 1 order by a.CreateAT desc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['CountAccount'];
    }

    public function generate_formulir_online($tahun)
    {
        $max_execution_time = 830;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', $max_execution_time); //
        $countGetData = count($this->getDataFormulirOnline($tahun));
        $count_account = $this->count_account();
        if ($countGetData > 0) {
            if ($countGetData != $count_account) {
                for ($i=($countGetData+1); $i <=$count_account; $i++) {
                    $this->insertDataFormulirOnline($tahun,$i);
                }
            }
        }
        else
        {
            for ($i=1; $i <=$count_account; $i++) {
                $this->insertDataFormulirOnline($tahun,$i);
            }
        }
    }

    public function insertDataFormulirOnline($tahun,$increment)
    {
        $yy = substr($tahun,2,2);
        $code = "O";
        for ($i=strlen($increment); $i < 4; $i++) {
            $increment = "0".$increment;
        }
        $dataSave = array(
            'Years' => $tahun,
            'FormulirCode' => $yy.$code.$increment,
            'CreateAT' => date('Y-m-d'),
            'CreatedBY' => $this->session->userdata('NIP'),
        );
        $this->db->insert('db_admission.formulir_number_online_m', $dataSave);
    }

    public function getDataFormulirOffline($tahun)
    {
        $sql = "select a.No_Ref,a.ID,a.Years,a.FormulirCode,a.Status,a.CreateAT,b.Name,a.Link,a.Print from db_admission.formulir_number_offline_m as a join db_employees.employees as b on a.CreatedBY = b.NIP where a.Years = ? order by a.Print asc,a.FormulirCode asc";
        $query=$this->db->query($sql, array($tahun))->result_array();
        return $query;
    }

    public function generate_formulir_offline($tahun,$qty)
    {
        $max_execution_time = 830;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', $max_execution_time); //
        $countGetData = count($this->getDataFormulirOffline($tahun));
        // $count_account = $this->count_account();
        $count_account = $qty;
        if ($countGetData > 0) {
            /*if ($countGetData != $count_account) {
              for ($i=($countGetData+1); $i <=$count_account; $i++) {
                $this->insertDataFormulirOffline($tahun,$i);
              }
            }*/

            // get last formulir code
            $sql = 'select * from db_admission.formulir_number_offline_m order by FormulirCode desc limit 1';
            $query=$this->db->query($sql, array())->result_array();
            $start = $query[0]['ID'] + 1;
            for ($i=0; $i < $count_account; $i++) {
                $this->insertDataFormulirOffline($tahun,$start);
                $start++;
            }
        }
        else
        {
            for ($i=1; $i <=$count_account; $i++) {
                $this->insertDataFormulirOffline($tahun,$i);
            }
        }
    }

    public function insertDataFormulirOffline($tahun,$increment)
    {
        $yy = substr($tahun,2,2);
        $code = "M";
        for ($i=strlen($increment); $i < 4; $i++) {
            $increment = "0".$increment;
        }
        $this->load->library('JWT');
        $key = "UAP)(*";
        // $url = $this->jwt->encode($yy.$code.$increment.";".$tahun,$key);
        $url = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        $baseURL = url_registration."formulir-registration-offline/".$url;
        $dataSave = array(
            'Years' => $tahun,
            'FormulirCode' => $yy.$code.$increment,
            'Link' => $baseURL,
            'CreateAT' => date('Y-m-d'),
            'CreatedBY' => $this->session->userdata('NIP'),
        );
        $this->db->insert('db_admission.formulir_number_offline_m', $dataSave);
    }

    public function inserData_Jacket_Size($JacketSize)
    {
        $dataSave = array(
            'JacketSize' => strtoupper($JacketSize),
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.register_jacket_size_m', $dataSave);
    }

    public function editData_Jacket_Size($JacketSize,$ID)
    {
        $sql = "update db_admission.register_jacket_size_m set JacketSize = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($JacketSize));
    }

    public function inserData_jurusan_sekolah($SchoolMajor)
    {
        $dataSave = array(
            'SchoolMajor' => strtoupper($SchoolMajor),
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.register_major_school', $dataSave);
    }

    public function editData_jurusan_sekolah($SchoolMajor,$ID)
    {
        $sql = "update db_admission.register_major_school set SchoolMajor = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($SchoolMajor));
    }

    public function showDataUjianMasukPerPrody()
    {
        $sql = "select a.ID,b.Name as NamaProgramStudy,a.NamaUjian,a.Bobot,a.Active,a.CreateAT from db_admission.ujian_perprody_m as a join db_academic.program_study as b on a.ID_ProgramStudy = b.ID";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function inserData_ujian_masuk($NamaUjian,$Bobot,$ID_ProgramStudy)
    {
        $dataSave = array(
            'NamaUjian' => strtoupper($NamaUjian),
            'Bobot' => $Bobot,
            'ID_ProgramStudy' => $ID_ProgramStudy,
            'CreateAT' => date('Y-m-d'),
        );
        $this->db->insert('db_admission.ujian_perprody_m', $dataSave);
    }

    public function editData_ujian_masuk($NamaUjian,$Bobot,$ID_ProgramStudy,$ID)
    {
        $sql = "update db_admission.ujian_perprody_m set NamaUjian = ? , Bobot = ? , ID_ProgramStudy = ? where ID = ".$ID;
        $query=$this->db->query($sql, array($NamaUjian,$Bobot,$ID_ProgramStudy));
    }

    public function getdataMenu()
    {
        $sql = "select * from db_admission.cfg_menu";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function saveMenu($menu)
    {
        $dataSave = array(
            'Menu' => ucwords($menu),
        );
        $this->db->insert('db_admission.cfg_menu', $dataSave);
    }

    public function saveSubMenu($menu,$sub_menu1,$sub_menu2,$chkPrevileges,$Slug,$Controller)
    {
        $sub_menu2 = ($sub_menu2 == '') ? 'empty' : $sub_menu2;
        // print_r($chkPrevileges);
        $dataSave = array();
        $dataSave['ID_Menu'] = $menu;
        $dataSave['SubMenu1'] = ucwords($sub_menu1);
        $dataSave['SubMenu2'] = ucwords($sub_menu2);
        $dataSave['Slug'] = $Slug;
        $dataSave['Controller'] = $Controller;

        for ($i=0; $i < count($chkPrevileges) ; $i++) {
            switch ($chkPrevileges[$i]) {
                case 'Read':
                    $dataSave['read'] = 1;
                    break;
                case 'Write':
                    $dataSave['write'] = 1;
                    break;
                case 'Update':
                    $dataSave['update'] = 1;
                    break;
                case 'Delete':
                    $dataSave['delete'] = 1;
                    break;
                default:
                    $dataSave['read'] = 0;
                    $dataSave['write'] = 0;
                    $dataSave['update'] = 0;
                    $dataSave['delete'] = 0;
                    break;
            }
        }
        // print_r($dataSave);
        $this->db->insert('db_admission.cfg_sub_menu', $dataSave);
    }

    public function showSubmenu()
    {
        $sql = "select a.Menu,b.* from db_admission.cfg_menu as a
          join db_admission.cfg_sub_menu as b
          on a.ID = b.ID_Menu";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function updateSubMenu($input)
    {
        // $dataArr = array();
        $ID_Menu = '';
        $Menu = '';
        $SubMenu1 = '';
        $SubMenu2 = '';
        $read = '';
        $write = '';
        $update = '';
        $delete = '';
        $ID = '';
        $query = '';
        $Slug = '';
        $Controller = '';

        if(array_key_exists("Menu",$input))
        {
            $ID_Menu = $input['ID_Menu'];
            $Menu = $input['Menu'];
            $sql = "update db_admission.cfg_menu set Menu = ? where ID = ? ";
            $query=$this->db->query($sql, array($Menu,$ID_Menu));
        }

        if(array_key_exists("Slug",$input))
        {
            $ID_Menu = $input['ID_Menu'];
            $Slug = $input['Slug'];
            $sql = "update db_admission.cfg_sub_menu set Slug = ? where ID = ? ";
            $query=$this->db->query($sql, array($Slug,$ID_Menu));
        }

        if(array_key_exists("Controller",$input))
        {
            $ID_Menu = $input['ID_Menu'];
            $Controller = $input['Controller'];
            $sql = "update db_admission.cfg_sub_menu set Controller = ? where ID = ? ";
            $query=$this->db->query($sql, array($Controller,$ID_Menu));
        }

        if(array_key_exists("SubMenu1",$input))
        {
            $ID = $input['ID'];
            $SubMenu1 = $input['SubMenu1'];
            $sql = "update db_admission.cfg_sub_menu set SubMenu1 = ? where ID = ? ";
            $query=$this->db->query($sql, array($SubMenu1,$ID));
        }

        if(array_key_exists("SubMenu2",$input))
        {
            $ID = $input['ID'];
            $SubMenu2 = $input['SubMenu2'];
            $sql = "update db_admission.cfg_sub_menu set SubMenu2 = ? where ID = ? ";
            $query=$this->db->query($sql, array($SubMenu2,$ID));
        }

        if(array_key_exists("read",$input))
        {
            $ID = $input['ID'];
            $read = $input['read'];
            $sql = "update db_admission.cfg_sub_menu set `read` = ? where ID = ? ";
            $query=$this->db->query($sql, array($read,$ID));
        }

        if(array_key_exists("write",$input))
        {
            $ID = $input['ID'];
            $write = $input['write'];
            $sql = "update db_admission.cfg_sub_menu set `write` = ? where ID = ? ";
            $query=$this->db->query($sql, array($write,$ID));
        }

        if(array_key_exists("update",$input))
        {
            $ID = $input['ID'];
            $update = $input['update'];
            $sql = "update db_admission.cfg_sub_menu set `update` = ? where ID = ? ";
            $query=$this->db->query($sql, array($update,$ID));
        }

        if(array_key_exists("delete",$input))
        {
            $ID = $input['ID'];
            $delete = $input['delete'];
            $sql = "update db_admission.cfg_sub_menu set `delete` = ? where ID = ? ";
            $query=$this->db->query($sql, array($delete,$ID));
        }

    }

    public function deleteSubMenu($input)
    {
        $sql = "delete from db_admission.cfg_sub_menu where ID = ".$input['ID'];
        $query=$this->db->query($sql, array());
    }

    public function getUserAdmission($Nama)
    {
        $sql = 'select CONCAT(a.Name," | ",a.NIP) as Name, a.NIP from db_employees.employees as a 
          join db_employees.rule_users as b
          on a.NIP = b.NIP
          where b.IDDivision = 10 and (a.Name like "%'.$Nama.'%" or a.NIP like "%'.$Nama.'%" )
          GROUP BY a.NIP';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getUserAdmissionAuth($NIP)
    {
        $sql = 'select CONCAT(a.Name," | ",a.NIP) as Name, a.NIP from db_employees.employees as a 
          join db_employees.rule_users as b
          on a.NIP = b.NIP
          where b.IDDivision = 10 and a.NIP = ?
          GROUP BY a.NIP';
        $query=$this->db->query($sql, array($NIP))->result_array();
        return $query;
    }

    public function getUserSessAuth($NIP,$IDDivision)
    {
        $sql = 'select CONCAT(a.Name," | ",a.NIP) as Name, a.NIP from db_employees.employees as a 
          join db_employees.rule_users as b
          on a.NIP = b.NIP
          where b.IDDivision = ? and a.NIP = ?
          GROUP BY a.NIP';
        $query=$this->db->query($sql, array($IDDivision,$NIP))->result_array();
        return $query;
    }

    public function get_submenu_by_menu($input)
    {
        $ID_Menu = $input['Menu'];
        $GroupUser = $input['GroupUser'];
        $sql = "select a.Menu,b.* from db_admission.cfg_menu as a
      join db_admission.cfg_sub_menu as b
      on a.ID = b.ID_Menu where b.ID_Menu = ?
      and b.ID not in (select ID_cfg_sub_menu from db_admission.cfg_rule_g_user where cfg_group_user = ?)";
        $query=$this->db->query($sql, array($ID_Menu,$GroupUser))->result_array();
        return $query;
    }

    public function groupuser_save($input)
    {
        $ID_GroupUSer = $input['ID_GroupUSer'];
        $checkbox = $input['checkbox'];
        $data = array();
        $increment = 0;
        for ($i=0; $i < count($checkbox); $i++) {
            $value = strtolower($checkbox[$i]->value);
            $ID_cfg_sub_menu = $checkbox[$i]->ID;

            // check data pertama
            if (count($data) == 0) {
                $data[$increment] = array(
                    'cfg_group_user' => $ID_GroupUSer,
                    'ID_cfg_sub_menu' => $ID_cfg_sub_menu,
                    $value => 1,
                );
                continue;
            }

            if (count($data) > 0) {
                // check data ada pada array
                $check = false;
                for ($j=0; $j < count($data); $j++) {
                    if ($data[$j]['ID_cfg_sub_menu'] == $ID_cfg_sub_menu) {
                        $data[$j][$value] = 1;
                        $check = true;
                        break;
                    }
                }

                if ($check) {
                    continue;
                }

                // check data tidak ada pada array
                for ($j=0; $j < count($data); $j++) {
                    if ($data[$j]['ID_cfg_sub_menu'] != $ID_cfg_sub_menu) {
                        $check = true;
                        break;
                    }
                }

                if ($check) {
                    $increment++;
                    $data[$increment] = array(
                        'cfg_group_user' => $ID_GroupUSer,
                        'ID_cfg_sub_menu' => $ID_cfg_sub_menu,
                        $value => 1,
                    );
                    continue;
                }

            }

        }

        // print_r($data);
        // save data
        for ($i=0; $i < count($data); $i++) {
            $dataSave = array();
            foreach ($data[$i] as $key => $value) {
                $dataSave[$key] = $value;
                // $dataSave = array($key=>$value);
            }
            $this->db->insert('db_admission.cfg_rule_g_user', $dataSave);
        }
    }

    public function save_user_previleges($input)
    {
        $NIP = $input['NIP'];
        $checkbox = $input['checkbox'];
        $data = array();
        $increment = 0;
        for ($i=0; $i < count($checkbox); $i++) {
            $value = strtolower($checkbox[$i]->value);
            $ID_cfg_sub_menu = $checkbox[$i]->ID;

            // check data pertama
            if (count($data) == 0) {
                $data[$increment] = array(
                    'NIP' => $NIP,
                    'ID_cfg_sub_menu' => $ID_cfg_sub_menu,
                    $value => 1,
                );
                continue;
            }

            if (count($data) > 0) {
                // check data ada pada array
                $check = false;
                for ($j=0; $j < count($data); $j++) {
                    if ($data[$j]['ID_cfg_sub_menu'] == $ID_cfg_sub_menu) {
                        $data[$j][$value] = 1;
                        $check = true;
                        break;
                    }
                }

                if ($check) {
                    continue;
                }

                // check data tidak ada pada array
                for ($j=0; $j < count($data); $j++) {
                    if ($data[$j]['ID_cfg_sub_menu'] != $ID_cfg_sub_menu) {
                        $check = true;
                        break;
                    }
                }

                if ($check) {
                    $increment++;
                    $data[$increment] = array(
                        'NIP' => $NIP,
                        'ID_cfg_sub_menu' => $ID_cfg_sub_menu,
                        $value => 1,
                    );
                    continue;
                }

            }

        }

        // print_r($data);
        // save data
        for ($i=0; $i < count($data); $i++) {
            $dataSave = array();
            foreach ($data[$i] as $key => $value) {
                $dataSave[$key] = $value;
                // $dataSave = array($key=>$value);
            }
            $this->db->insert('db_admission.previleges', $dataSave);
        }
    }

    public function get_previleges_user_show($NIP)
    {
        $sql = 'SELECT a.NIP, a.Name, b.Menu,c.SubMenu1,c.SubMenu2,c.ID_Menu,d.ID_cfg_sub_menu,d.ID as ID_previleges,d.`read`,d.`write`,d.`update`,
d.`delete`,c.`read` as readMenu,c.`update` as updateMenu,c.`write` as writeMenu,c.`delete` as deleteMenu from db_employees.employees as a
            join db_admission.previleges as d
            on a.NIP = d.NIP
            join db_admission.cfg_sub_menu as c
            on d.ID_cfg_sub_menu = c.ID
            join db_admission.cfg_menu as b
            on b.ID = c.ID_Menu where a.NIP = ? ';
        $query=$this->db->query($sql, array($NIP))->result_array();
        return $query;
    }

    public function previleges_groupuser_update($input)
    {
        // $dataArr = array();
        $read = '';
        $write = '';
        $update = '';
        $delete = '';
        $ID = '';
        $query = '';

        if(array_key_exists("read",$input))
        {
            $ID = $input['ID'];
            $read = $input['read'];
            $sql = "update db_admission.cfg_rule_g_user set `read` = ? where ID = ? ";
            $query=$this->db->query($sql, array($read,$ID));
        }

        if(array_key_exists("write",$input))
        {
            $ID = $input['ID'];
            $write = $input['write'];
            $sql = "update db_admission.cfg_rule_g_user set `write` = ? where ID = ? ";
            $query=$this->db->query($sql, array($write,$ID));
        }

        if(array_key_exists("update",$input))
        {
            $ID = $input['ID'];
            $update = $input['update'];
            $sql = "update db_admission.cfg_rule_g_user set `update` = ? where ID = ? ";
            $query=$this->db->query($sql, array($update,$ID));
        }

        if(array_key_exists("delete",$input))
        {
            $ID = $input['ID'];
            $delete = $input['delete'];
            $sql = "update db_admission.cfg_rule_g_user set `delete` = ? where ID = ? ";
            $query=$this->db->query($sql, array($delete,$ID));
        }
    }

    public function previleges_user_delete($input)
    {
        $sql = "delete from db_admission.cfg_rule_g_user where ID = ".$input['ID'];
        $query=$this->db->query($sql, array());
    }

    public function previleges_group_user_delete($input)
    {
        $sql = "delete from db_admission.cfg_rule_g_user where ID = ".$input['ID'];
        $query=$this->db->query($sql, array());
    }


    public function getMenuUser($NIP,$db = 'db_admission')
    {
        $sql = 'SELECT b.ID as ID_menu,b.Icon,c.ID,b.Menu,c.SubMenu1,c.SubMenu2,d.`read`,d.`update`,d.`write`,d.`delete`,c.Slug,c.Controller 
                from db_employees.employees as a
                join '.$db.'.previleges as d
                on a.NIP = d.NIP
                join '.$db.'.cfg_sub_menu as c
                on d.ID_cfg_sub_menu = c.ID
                join '.$db.'.cfg_menu as b
                on b.ID = c.ID_Menu where a.NIP = ? GROUP by b.id';
        $query=$this->db->query($sql, array($NIP))->result_array();
        return $query;
    }

    public function getMenuGroupUser($NIP,$db = 'db_admission')
    {
        $sql = 'SELECT b.ID as ID_menu,b.Icon,c.ID,b.Menu,c.SubMenu1,c.SubMenu2,x.`read`,x.`update`,x.`write`,x.`delete`,c.Slug,c.Controller 
                from db_employees.employees as a
                join '.$db.'.previleges_guser as d
                on a.NIP = d.NIP
                join '.$db.'.cfg_rule_g_user as x
                on d.G_user = x.cfg_group_user
                join '.$db.'.cfg_sub_menu as c
                on x.ID_cfg_sub_menu = c.ID
                join '.$db.'.cfg_menu as b
                on b.ID = c.ID_Menu where a.NIP = ? GROUP by b.id';
        $query=$this->db->query($sql, array($NIP))->result_array();
        return $query;
    }

    public function getSubmenu2BaseSubmenu1($submenu1,$db='db_admission')
    {
        $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
        from '.$db.'.cfg_sub_menu as a  join '.$db.'.previleges as b on a.ID = b.ID_cfg_sub_menu where a.SubMenu1 = ? and b.NIP = ?';
        $query=$this->db->query($sql, array($submenu1,$this->session->userdata('NIP')))->result_array();
        return $query;
    }

    public function getSubmenu1BaseMenu($ID_Menu,$db='db_admission')
    {
        $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
        from '.$db.'.cfg_sub_menu as a join '.$db.'.previleges as b on a.ID = b.ID_cfg_sub_menu  where a.ID_Menu = ? and b.NIP = ? group by SubMenu1';
        $query=$this->db->query($sql, array($ID_Menu,$this->session->userdata('NIP')))->result_array();
        return $query;
    }

    public function getSubmenu2BaseSubmenu1_grouping($submenu1,$db='db_admission',$IDmenu = null)
    {
        if ($IDmenu != null) {
            $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
            from '.$db.'.cfg_sub_menu as a  join '.$db.'.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
            join '.$db.'.previleges_guser as c on b.cfg_group_user = c.G_user
             where a.SubMenu1 = ? and c.NIP = ? and a.ID_Menu = ?';
            $query=$this->db->query($sql, array($submenu1,$this->session->userdata('NIP'),$IDmenu))->result_array();
        }
        else
        {
            $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
            from '.$db.'.cfg_sub_menu as a  join '.$db.'.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
            join '.$db.'.previleges_guser as c on b.cfg_group_user = c.G_user
             where a.SubMenu1 = ? and c.NIP = ?';
            $query=$this->db->query($sql, array($submenu1,$this->session->userdata('NIP')))->result_array();
        }
        
        return $query;
    }

    public function getSubmenu1BaseMenu_grouping($ID_Menu,$db='db_admission')
    {
        $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
        from '.$db.'.cfg_sub_menu as a join '.$db.'.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
        join '.$db.'.previleges_guser as c on b.cfg_group_user = c.G_user  where a.ID_Menu = ? and c.NIP = ? group by a.SubMenu1';
        $query=$this->db->query($sql, array($ID_Menu,$this->session->userdata('NIP')))->result_array();
        return $query;
    }

    public function saveGenerateVA($no_va)
    {
        // check VA existing pada table va_generate
        $sql = 'SELECT * from db_admission.va_generate where VA = ?';
        $query=$this->db->query($sql, array($no_va))->result_array();
        if (count($query) == 0) {
            // create Va
            $dataSave = array(
                'VA' => $no_va,
                'CreateAT' => date('Y-m-d'),
                'CreateBY' => $this->session->userdata('NIP')
            );
            $this->db->insert('db_admission.va_generate', $dataSave);

        }
    }

    public function loadDataVA_available()
    {
        $sql = 'SELECT a.*,b.StatusVA from db_admission.va_generate as a join db_admission.va_status as b on a.VA_Status = b.ID';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function saveDataToVerification_offline($RegisterID)
    {
        $dataSave = array(
            'RegisterID' => $RegisterID,
            'FileUpload' => '',
            'CreateAT' => date("Y-m-d"),
        );

        $this->db->insert('db_admission.register_verification', $dataSave);
    }

    public function saveDataRegisterVerified($RegVerificationID,$FormulirCode)
    {
        // $getFormulirCode = $this->getFormulirCode('online');
        $dataSave = array(
            'RegVerificationID' => $RegVerificationID,
            'FormulirCode' => $FormulirCode,
            // 'VerificationBY' => $this->session->userdata('NIP'),
            'VerificationAT' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('db_admission.register_verified', $dataSave);
    }

    public function price_formulir_offline()
    {
        $sql = "select PriceFormulir from db_admission.price_formulir_offline as a where a.active = 1 order by a.CreateAT desc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['PriceFormulir'];
    }

    public function load_data_event()
    {
        $sql = "select a.*,b.name as name_updated from (select a.*,b.Name from db_admission.price_event as a join db_employees.employees as b on a.evn_created_iduser = b.NIP) a left join db_employees.employees as b on a.evn_updated_iduser = b.NIP";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function inserData_price_event($evn_price,$evn_name)
    {
        $dataSave = array(
            'evn_price' => $evn_price,
            'evn_name' => $evn_name,
            'evn_created_iduser' => $this->session->userdata('NIP'),
            'evn_created_at' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('db_admission.price_event', $dataSave);
    }

    public function editData_price_event($evn_price,$evn_name,$id_prc_evn)
    {
        $dataSave = array(
            'evn_price' => $evn_price,
            'evn_name' => $evn_name,
            'evn_updated_iduser' => $this->session->userdata('NIP'),
            'evn_updated_at' => date('Y-m-d H:i:s'),
        );
        $this->db->where('ID', $id_prc_evn);
        $this->db->update('db_admission.price_event', $dataSave);
    }

    public function inserData_source_from_event($src_name)
    {
        $dataSave = array(
            'src_name' => ucwords($src_name),
            'CreateAT' => date("Y-m-d"),
        );
        $this->db->insert('db_admission.source_from_event', $dataSave);
    }

    public function editData_source_from_event($src_name,$ID)
    {
        $dataSave = array(
            'src_name' => ucwords($src_name),
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.source_from_event', $dataSave);
    }

    public function inserData_sales_school_m($SchoolID,$SalesNIP,$selectWilayah)
    {
        if ($SchoolID != 'All') {
            $dataSave = array(
                'SchoolID' => $SchoolID,
                'SalesNIP' => $SalesNIP,
                'CreateAT' => date("Y-m-d"),
            );
            $this->db->insert('db_admission.sales_school_m', $dataSave);
        }
        else
        {
            $arr_school = $this->caribasedprimary('db_admission.school','CityID',$selectWilayah);
            for ($i=0; $i < count($arr_school); $i++) {
                //check school existing pada sales_school_m
                $check = $this->checkExistingsales_school_m($SalesNIP,$arr_school[$i]['ID']);
                if ($check) {
                    $dataSave = array(
                        'SchoolID' => $arr_school[$i]['ID'],
                        'SalesNIP' => $SalesNIP,
                        'CreateAT' => date("Y-m-d"),
                    );
                    $this->db->insert('db_admission.sales_school_m', $dataSave);
                }
            }
        }

    }

    public function checkExistingsales_school_m($SalesNIP,$SchoolID)
    {
        $sql = 'select count(*) as total from db_admission.sales_school_m where SalesNIP = ? and SchoolID = ? ';
        $query=$this->db->query($sql, array($SalesNIP,$SchoolID))->result_array();
        if ($query[0]['total'] == 0) {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function editData_sales_school_m($SchoolID,$SalesNIP,$ID)
    {
        $dataSave = array(
            'SchoolID' => $SchoolID,
            'SalesNIP' => $SalesNIP,
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.sales_school_m', $dataSave);
    }

    public function count_sales_koordinator()
    {
        $sql = "select count(*) as total from db_admission.sales_school_m";
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['total'];
    }

    public function selectDataSalesKoordinator($limit,$start,$Wilayah,$School,$Sales,$Status)
    {
        if($Wilayah != '%') {
            $Wilayah = '"'.$Wilayah.'%"';
        }
        else
        {
            $Wilayah = '"%"';
        }

        if($School != '%') {
            $School = '"'.$School.'%"';
        }
        else
        {
            $School = '"%"';
        }

        if($Sales != '%') {
            $Sales = '"'.$Sales.'%"';
        }
        else
        {
            $Sales = '"%"';
        }

        if($Status != '%') {
            // $status = '"%'.$status.'%"';
            // $status = 'StatusUsed != '.$status;
            $Status = '"'.$Status.'%"';
        }
        else
        {
            $Status = '"%"';
        }

        $sql = 'select a.ID,z.Name,z.NIP,b.SchoolName,b.SchoolAddress,c.RegionName,d.ProvinceName,a.Active
            from db_admission.sales_school_m as a join db_employees.employees as z
            on a.SalesNIP = z.NIP
            join db_admission.school as b
            on b.ID = a.SchoolID
            join db_admission.region as c
            on c.RegionID = b.CityID
            join db_admission.province as d
            on d.ProvinceID = b.ProvinceID
        where c.RegionID like '.$Wilayah.' and a.SchoolID like '.$School.' and z.NIP like  '.$Sales.' and a.Active like '.$Status.' LIMIT '.$start. ', '.$limit;
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function updateStatusPrint($FormulirCode)
    {
        $dataSave = array(
            'Print' => 1,
        );
        $this->db->where('FormulirCode', $FormulirCode);
        $this->db->update('db_admission.formulir_number_offline_m', $dataSave);
    }

    public function proses_link_test($contain1)
    {
        $arr_temp = array('getFirst' => '','link' => '','getLast' => '');
        $karakter = "[#link]";
        $posisi=strpos($contain1,$karakter);
        $getFirst = trim(substr($contain1, 0,$posisi));
        $getLast = trim(substr($contain1, ($posisi + strlen($karakter)),strlen($contain1)));
        $karakter = "www.adi.com"; // to replace
        $arr_temp = array('getFirst' => $getFirst,'link' => $karakter, 'getLast' => $getLast);
        return $arr_temp;
    }

    public function proses_link($contain1,$url)
    {
        $arr_temp = array('getFirst' => '','link' => '','getLast' => '');
        $karakter = "[#link]";
        $posisi=strpos($contain1,$karakter);
        $getFirst = trim(substr($contain1, 0,$posisi));
        $getLast = trim(substr($contain1, ($posisi + strlen($karakter)),strlen($contain1)));
        $karakter = $url; // to replace
        $arr_temp = array('getFirst' => $getFirst,'link' => $karakter, 'getLast' => $getLast);
        return $arr_temp;
    }

    public function save_set_print_label($input)
    {
        //check data sudah ada atau belum
        $checkData =$this->showData('db_admission.set_label_token_off');
        if (count($checkData) ==  0) {
            $dataSave = array(
                'Header' => $input['header'],
                'Contain1' => $input['contain1'],
                'Contain2' => $input['contain2'],
                'setFont1' => $input['selectFontContain1'],
                'setFont2' => $input['selectFontContain2'],
                'setFontHeader' => $input['selectFontHeader'],
            );
            $this->db->insert('db_admission.set_label_token_off', $dataSave);
        }
        else
        {
            $dataSave = array(
                'Header' => $input['header'],
                'Contain1' => $input['contain1'],
                'Contain2' => $input['contain2'],
                'setFont1' => $input['selectFontContain1'],
                'setFont2' => $input['selectFontContain2'],
                'setFontHeader' => $input['selectFontHeader'],
            );
            $this->db->where('ID',1);
            $this->db->update('db_admission.set_label_token_off', $dataSave);
        }

    }

    public function updateStatusJual($FormulirCode)
    {
        $dataSave = array(
            'StatusJual' => 0,
        );
        $this->db->where('FormulirCode', $FormulirCode);
        $this->db->update('db_admission.formulir_number_offline_m', $dataSave);
    }

    public function updateStatusJual2($FormulirCode)
    {
        $dataSave = array(
            'StatusJual' => 0,
            'Status' => 0,
            'No_Ref' => '',
        );
        $this->db->where('FormulirCode', $FormulirCode);
        $this->db->update('db_admission.formulir_number_offline_m', $dataSave);
    }

    public function recycleDataVa($limit,$start)
    {
        $sql = "select * from db_admission.register_deleted where VA_recycle = 0
            LIMIT ".$start. ", ".$limit; // query undone
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function checkBiling($input)
    {
        include_once APPPATH.'third_party/bni/BniEnc.php';
        $arr_temp = array();
        // include_once APPPATH.'third_party/bni/BniEnc.php';
        $client_id = VA_client_id;
        $secret_key = VA_secret_key;
        $url = VA_url;
        for ($i=0; $i < count($input['chkValue']); $i++) {
            $data_asli = array(
                'client_id' => $client_id,
                'trx_id' => $input['chkValue'][$i], // fill with Billing ID
                'type' => 'inquirybilling',
            );
            $hashed_string = BniEnc::encrypt(
                $data_asli,
                $client_id,
                $secret_key
            );

            $data = array(
                'client_id' => $client_id,
                'data' => $hashed_string,
            );

            $response = $this->get_content($url, json_encode($data));
            $response_json = json_decode($response, true);
            if ($response_json['status'] !== '000') {
                // $arr_temp[$i]['msg'] = $response_json['status'];
                $arr_temp[$i]['msg'] = $response_json;
                $arr_temp[$i]['trx_id'] = $input['chkValue'][$i];

            }
            else {
                $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
                $arr_temp[$i]['msg'] = $response_json['status'];
                // $arr_temp[$i]['msg'] = $data_response;
                $arr_temp[$i]['trx_id'] = $input['chkValue'][$i];
            }

        }

        return $arr_temp;
    }

    public function get_content($url, $post = '') {
        $usecookie = __DIR__ . "/cookie.txt";
        $header[] = 'Content-Type: application/json';
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        // curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

        if ($post)
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $rs = curl_exec($ch);

        if(empty($rs)){
            var_dump($rs, curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $rs;
    }

    private function get_content2($url, $post = '') {
        $usecookie = __DIR__ . "/cookie2.txt";
        $header[] = 'Content-Type: application/json';
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        // curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

        if ($post)
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $rs = curl_exec($ch);

        if(empty($rs)){
            var_dump($rs, curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $rs;
    }

    public function updateBiling2($input)
    {
        include_once APPPATH.'third_party/bni/BniEnc.php';
        $arr_temp = array();
        $client_id = VA_client_id;
        $secret_key = VA_secret_key;
        $url = VA_url;
        for ($i=0; $i < count($input['chkValue']); $i++) {
            $data_asli = array(
                'client_id' => $client_id,
                'trx_id' => '1177224193', // fill with Billing ID
                'trx_amount' => '100000',
                'customer_name' => 'Mr. X',
                'customer_email' => 'xxx@email.com',
                'customer_phone' => '08123123123',
                'datetime_expired' => '2018-04-24T23:00:00+07:00',
                'description' => 'test Update',
                'type' => 'updateBilling',
            );
            $hashed_string = BniEnc::encrypt(
                $data_asli,
                $client_id,
                $secret_key
            );

            $data = array(
                'client_id' => $client_id,
                'data' => $hashed_string,
            );

            $response = $this->get_content($url, json_encode($data));
            $response_json = json_decode($response, true);
            if ($response_json['status'] !== '000') {
                $arr_temp[$i]['msg'] = $response_json['status'];
                $arr_temp[$i]['trx_id'] = $input['chkValue'][$i];

            }
            else {
                $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
                // $arr_temp[$i]['msg'] = $data_response['va_status'];
                $arr_temp[$i]['msg'] = $data_response;
                $arr_temp[$i]['trx_id'] = $input['chkValue'][$i];
            }

        }

        return $arr_temp;
    }


    public function updateBiling($input)
    {
        include_once APPPATH.'third_party/bni/BniEnc.php';
        $expiredNow = date('c', time() + 0);
        $arr_temp = array();
        // include_once APPPATH.'third_party/bni/BniEnc.php';
        $client_id = VA_client_id;
        $secret_key = VA_secret_key;
        $url = VA_url;
        // print_r($bilingStatus);
        for ($i=0; $i < count($input['chkValue']); $i++) {
            sleep(1);
            $data_asli = array(
                'client_id' => $client_id,
                'trx_id' => $input['chkValue'][$i], // fill with Billing ID
                'trx_amount' => '100000',
                'customer_name' => 'Mr. X',
                'customer_email' => 'xxx@email.com',
                'customer_phone' => '08123123123',
                'datetime_expired' => $expiredNow,
                'description' => 'Update Expired Biling',
                'type' => 'updateBilling',
            );
            $hashed_string = BniEnc::encrypt(
                $data_asli,
                $client_id,
                $secret_key
            );

            $data = array(
                'client_id' => $client_id,
                'data' => $hashed_string,
            );

            $response = $this->get_content2($url, json_encode($data));
            $response_json = json_decode($response, true);
            if ($response_json['status'] !== '000') {
                $arr_temp[$i]['msg'] = $response_json;
                $arr_temp[$i]['trx_id'] = $input['chkValue'][$i];

            }
            else {
                $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
                $arr_temp[$i]['msg'] = $response_json['status'];
                // $arr_temp[$i]['msg'] = $data_response;
                $arr_temp[$i]['trx_id'] = $input['chkValue'][$i];
            }

        }
        return $arr_temp;
    }

    public function updateDB_registerDeleted($updateBNI)
    {
        for ($i=0; $i < count($updateBNI); $i++) {
            $dataSave = array(
                'VA_recycle' => 1,
            );
            $this->db->where('BilingID', $updateBNI[$i]['trx_id']);
            $this->db->update('db_admission.register_deleted', $dataSave);

            $getVa = $this->caribasedprimary('db_admission.register_deleted','BilingID',$updateBNI[$i]['trx_id']);
            $VA = $getVa[0]['VA_number'];
            $dataSave = array(
                'VA_Status' => 3,
            );
            $this->db->where('VA', $VA);
            $this->db->update('db_admission.va_generate', $dataSave);
            sleep(1);
        }
    }

    public function getCalon_mahasiswa($Name)
    {
        $Name = ($Name == '%') ? ' like %' : ' like "%'.$Name.'%"';
        $sql = "select a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,f.Religion,concat(a.PlaceBirth,',',a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
          h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
          a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
          n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto
          from db_admission.register_formulir as a
          JOIN db_admission.register_verified as b 
          ON a.ID_register_verified = b.ID
          JOIN db_admission.register_verification as c
          ON b.RegVerificationID = c.ID
          JOIN db_admission.register as d
          ON c.RegisterID = d.ID
          JOIN db_admission.country as e
          ON a.NationalityID = e.ctr_code
          JOIN db_employees.religion as f
          ON a.ReligionID = f.IDReligion
          JOIN db_admission.register_jtinggal_m as g
          ON a.ID_register_jtinggal_m = g.ID
          JOIN db_admission.country as h
          ON a.ID_country_address = h.ctr_code
          JOIN db_admission.province as i
          ON a.ID_province = i.ProvinceID
          JOIN db_admission.region as j
          ON a.ID_region = j.RegionID
          JOIN db_admission.district as k
          ON a.ID_districts = k.DistrictID
          JOIN db_admission.school_type as l
          ON l.sct_code = a.ID_school_type
          JOIN db_admission.register_major_school as m
          ON m.ID = a.ID_register_major_school
          JOIN db_admission.school as n
          ON n.ID = d.SchoolID
          join db_academic.program_study as o
          on o.ID = a.ID_program_study
            where d.Name ".$Name. ' and a.ID not in (select ID_register_formulir from db_admission.register_butuh_ujian)';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function inserData_jpa($selectRangking1,$selectRangking2,$selectPotongan)
    {
        $dataSave = array(
            'StartRange' => $selectRangking1,
            'EndRange' => $selectRangking2,
            'DiskonSPP' => $selectPotongan,
            'CreateAT' => date("Y-m-d"),
        );
        $this->db->insert('db_admission.register_dsn_jpa', $dataSave);
    }

    public function editData_jpa($selectRangking1,$selectRangking2,$selectPotongan,$ID)
    {
        $dataSave = array(
            'StartRange' => $selectRangking1,
            'EndRange' => $selectRangking2,
            'DiskonSPP' => $selectPotongan,
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.register_dsn_jpa', $dataSave);
    }

    public function inserData_jpau($Tingkat,$selectPotongan)
    {
        $dataSave = array(
            'Tingkat' => $Tingkat,
            'DiskonSPP' => $selectPotongan,
            'CreateAT' => date("Y-m-d"),
        );
        $this->db->insert('db_admission.register_dsn_jpau', $dataSave);
    }

    public function editData_jpau($Tingkat,$selectPotongan,$ID)
    {
        $dataSave = array(
            'Tingkat' => $Tingkat,
            'DiskonSPP' => $selectPotongan,
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.register_dsn_jpau', $dataSave);
    }

    public function inserData_jpok($Tingkat,$selectPotonganSPP,$selectPotonganSKS)
    {
        $dataSave = array(
            'Tingkat' => $Tingkat,
            'DiskonSPP' => $selectPotonganSPP,
            'DiskonBiayaSKS' => $selectPotonganSKS,
            'CreateAT' => date("Y-m-d"),
        );
        $this->db->insert('db_admission.register_dsn_jpok', $dataSave);
    }

    public function editData_jpok($Tingkat,$selectPotonganSPP,$selectPotonganSKS,$ID)
    {
        $dataSave = array(
            'Tingkat' => $Tingkat,
            'DiskonSPP' => $selectPotonganSPP,
            'DiskonBiayaSKS' => $selectPotonganSKS,
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.register_dsn_jpok', $dataSave);
    }

    public function ClearPricetoDB($priceMask)
    {
        $priceMask = str_replace('.','', $priceMask);
        $koma = strpos($priceMask, ',');
        $priceMask = substr($priceMask, 0,$koma);
        return $priceMask;
    }

    public function replaceKomaToTitik($Discount)
    {
        $Discount = str_replace(',','.', $Discount);
        return $Discount;
    }

    public function update_va_log($data)
    {
        $dataSave = array(
            'payment_amount' => $data['payment_amount'],
            'cumulative_payment_amount' => $data['cumulative_payment_amount'],
            'payment_ntb' => $data['payment_ntb'],
            'datetime_payment' => $data['datetime_payment'],
            'datetime_payment_iso8601' => $data['datetime_payment_iso8601'],
            'Status' => 1,
        );
        $this->db->where('trx_id', $data['trx_id']);
        $this->db->update('db_va.va_log', $dataSave);
    }

    public function DeadLinePayment()
    {
        $sql = "select Deadline from db_admission.deadline_payment as a where a.active = 1 order by a.CreateAT desc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['Deadline'];
    }

    public function inserData_cfg_deadline($startDate,$endDate)
    {
        $dataSave = array(
            'Start_register' => $startDate,
            'Deadline_register' => $endDate,
            'CreateAT' => date("Y-m-d"),
        );
        $this->db->insert('db_admission.cfg_deadline', $dataSave);
    }

    public function editData_cfg_deadline($startDate,$endDate,$ID)
    {
        $dataSave = array(
            'Start_register' => $startDate,
            'Deadline_register' => $endDate,
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.cfg_deadline', $dataSave);
    }

    public function inserData_cfg_cicilan($max_cicilan)
    {
        $dataSave = array(
            'max_cicilan' => $max_cicilan,
            'CreateAT' => date("Y-m-d"),
        );
        $this->db->insert('db_admission.cfg_cicilan', $dataSave);
    }

    public function editData_cfg_cicilan($max_cicilan,$ID)
    {
        $dataSave = array(
            'max_cicilan' => $max_cicilan,
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.cfg_cicilan', $dataSave);
    }

    public function getRegionByProv($ProvinceID)
    {
        $sql = "select a.ProvinceID,b.RegionID,b.RegionName from db_admission.province_region as a join db_admission.region as b on a.RegionID = b.RegionID and a.ProvinceID = ?";
        $query=$this->db->query($sql, array($ProvinceID))->result_array();
        return $query;
    }

    public function getDistrictByRegion($RegionID)
    {
        $sql = "select a.RegionID,b.DistrictID,b.DistrictName from db_admission.region_district as a join db_admission.district as b on a.DistrictID = b.DistrictID and a.RegionID = ?";
        $query=$this->db->query($sql, array($RegionID))->result_array();
        return $query;
    }

    public function getTypeSekolah()
    {
        $sql = "select * from db_admission.school_type where sct_active = 1";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function inserData_Sekolah($ProvinceID,$RegionID,$DistrictID,$ID_school_type,$SchoolName,$SchoolAddress)
    {
        $ProvinceName = $this->caribasedprimary('db_admission.province','ProvinceID',$ProvinceID);
        $ProvinceName = $ProvinceName[0]['ProvinceName'];
        $RegionName = $this->caribasedprimary('db_admission.region','RegionID',$RegionID);
        $RegionName = $RegionName[0]['RegionName'];
        $DistrictName = $this->caribasedprimary('db_admission.district','DistrictID',$DistrictID);
        $DistrictName = $DistrictName[0]['DistrictName'];
        $SchoolType = $this->caribasedprimary('db_admission.school_type','sct_code',$ID_school_type);
        $SchoolType = $SchoolType[0]['sct_name_id'];

        $dataSave = array(
            'ProvinceID' => $ProvinceID,
            'ProvinceName' => $ProvinceName,
            'CityID' => $RegionID,
            'CityName' => $RegionName,
            'DistrictID' => $DistrictID,
            'DistrictName' => $DistrictName,
            'SchoolType' => $SchoolType,
            'SchoolName' => $SchoolName,
            'SchoolAddress' => $SchoolAddress,
            'Created' => 1,
            'Approved' => 1,
            'Approver' => $this->session->userdata('NIP'),
        );
        $this->db->insert('db_admission.school', $dataSave);

    }

    public function editData_Sekolah($ProvinceID,$RegionID,$DistrictID,$ID_school_type,$SchoolName,$SchoolAddress,$ID)
    {

        $ProvinceName = $this->caribasedprimary('db_admission.province','ProvinceID',$ProvinceID);
        $ProvinceName = $ProvinceName[0]['ProvinceName'];
        $RegionName = $this->caribasedprimary('db_admission.region','RegionID',$RegionID);
        $RegionName = $RegionName[0]['RegionName'];
        $DistrictName = $this->caribasedprimary('db_admission.district','DistrictID',$DistrictID);
        $DistrictName = $DistrictName[0]['DistrictName'];
        $SchoolType = $this->caribasedprimary('db_admission.school_type','sct_code',$ID_school_type);
        $SchoolType = $SchoolType[0]['sct_name_id'];

        $dataSave = array(
            'ProvinceID' => $ProvinceID,
            'ProvinceName' => $ProvinceName,
            'CityID' => $RegionID,
            'CityName' => $RegionName,
            'DistrictID' => $DistrictID,
            'DistrictName' => $DistrictName,
            'SchoolType' => $SchoolType,
            'SchoolName' => $SchoolName,
            'SchoolAddress' => $SchoolAddress,
            'Created' => 1,
            'Approved' => 1,
            'Approver' => $this->session->userdata('NIP'),
        );
        $this->db->where('ID', $ID);
        $this->db->update('db_admission.school', $dataSave);
    }


    public function CountgetNotificationDivisi()
    {
        $IDDivision = $this->session->userdata('IDdepartementNavigation');
        $sql = "select count(*) as total from db_notifikasi.notification as a 
                            join db_notifikasi.n_div as b on a.ID = b.ID_notification 
                            where b.StatusRead = 0 and b.Div = ? order by a.Created desc limit 20";
        $query=$this->db->query($sql, array($IDDivision))->result_array();

        // Get Notifikasi personal
        $NIP = $this->session->userdata('NIP');
        $dataNotif = $this->db->query('SELECT COUNT(*) as Total FROM db_notifikasi.n_personal np
                                                LEFT JOIN db_notifikasi.notification n 
                                                ON (n.ID = np.ID_notification)
                                                WHERE np.People = "'.$NIP.'"
                                                 AND np.StatusRead = 0 ')->result_array();

        return $query[0]['total'] + $dataNotif[0]['Total'];
    }

    function cmp($a, $b)
    {
        return strcmp($a['ID'], $b['ID']);
    }

    public function getNotificationDivisi()
    {
        $IDDivision = $this->session->userdata('IDdepartementNavigation');
        $sql = "select * from db_notifikasi.notification as a 
                          join db_notifikasi.n_div as b on a.ID = b.ID_notification 
                          where b.Div = ? order by a.ID desc limit 10";
        $query=$this->db->query($sql, array($IDDivision))->result_array();

        // Get Notifikasi personal
        $NIP = $this->session->userdata('NIP');
        $dataNotif = $this->db->query('SELECT np.ID AS IDUser, np.Div, np.StatusRead, np.ShowNotif, n.*  FROM db_notifikasi.n_personal np
                                                LEFT JOIN db_notifikasi.notification n 
                                                ON (n.ID = np.ID_notification)
                                                WHERE np.People = "'.$NIP.'"
                                                 ORDER BY n.ID DESC LIMIT 20 ')->result_array();

        if(count($dataNotif)>0){
            for($i=0;$i<count($dataNotif);$i++){
                array_push($query,$dataNotif[$i]);
            }
        }



        usort($query, array($this, "cmp"));

        return $query;
    }

    public function readNotificationDivision()
    {
        $IDDivision = $this->session->userdata('IDdepartementNavigation');
        $sql = "select * from db_notifikasi.n_div as b where b.Div = ? and StatusRead = 0";
        $query=$this->db->query($sql, array($IDDivision))->result_array();

        for ($i=0; $i < count($query); $i++) {
            $dataSave = array(
                'ReadFirst' => $this->session->userdata('NIP'),
                'StatusRead' => 1,
            );
            $this->db->where('ID', $query[$i]['ID']);
            $this->db->update('db_notifikasi.n_div', $dataSave);
        }

    }

    public function getSMAWilayahApproval()
    {
        $sql = "select * from db_admission.school where Approved = 0";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getDataMhsBYNPM($NPM,$dbMHS)
    {
        $sql = 'select * from ta_'.$dbMHS.'.students as a left join db_academic.auth_students as b on a.NPM = b.NPM  where a.NPM = "'.$NPM.'"';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function PaymentgetMahasiswaByNPM($NPM)
    {
        error_reporting(0);
        $arr = array();
        $sql = 'select a.*, b.Year,b.EmailPU,c.Name as NameSemester, d.Description 
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM 
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID where a.NPM = ? limit 1';
        $query=$this->db->query($sql, array($NPM))->result_array();
        
        $Year = $query[0]['Year'];
        $db = 'ta_'.$Year.'.students';
        $dt = $this->m_master->caribasedprimary($db,'NPM',$query[0]['NPM']);
        $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
        $arr = array(
            'PaymentID' => $query[0]['ID'],
            'PTID'  => $query[0]['PTID'],
            'PTIDDesc' => $query[0]['Description'],
            'SemesterID' => $query[0]['SemesterID'],
            'SemesterName' => $query[0]['NameSemester'],
            'NPM' => $query[0]['NPM'],
            'Nama' => $dt[0]['Name'],
            'EmailPU' => $query[0]['EmailPU'],
            'InvoicePayment' => $query[0]['Invoice'],
            'Discount' => $query[0]['Discount'],
            'StatusPayment' => $query[0]['Status'],
            'ProdiID' => $dt[0]['ProdiID'],
            'ProdiEng' => $ProdiEng[0]['NameEng'],
            'Year' => $Year,
            'DetailPayment' => $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$query[0]['ID']),
        );

        return $arr;        

    }

    public function saveNotification($data)
    {
        $key = "UAP)(*";
        $subject = "You have received payment from : ".$data['Nama'].'<br> Type : '.$data['PTIDDesc'];
        $URL = "finance/tagihan-mhs/cek-tagihan-mhs/".$data['NPM'];
        $From = $data['EmailPU'];
        $ToDiv = "9";
        $ToPeople = "All";
        $Desc = $data['PTIDDesc'];
        $Created = date('Y-m-d H:i:s' );

        $data = array(
                        'subject' => $subject,
                        'URL'   => $URL,
                        'From'  => $From,
                     );
        $token = $this->jwt->encode($data,$key);

        
        $saveToDBRegister = $this->saveToNotificationDivisi($token,$Desc,$Created,$ToDiv);
    }

    public function saveToNotificationDivisi($token,$Desc,$Created,$ToDiv)
    {
        $ToDiv = explode(',', $ToDiv);
        for ($i=0; $i < count($ToDiv); $i++) { 
            $dataSave = array(
                    'Token' => $token,
                    'Desc' => $Desc,
                    'Created' => $Created
                            );

            $this->db->insert('db_notifikasi.notification', $dataSave);
            $insert_id = $this->db->insert_id();

            $dataSave = array(
                    'ID_notification' => $insert_id,
                    'Div' => $ToDiv[$i],
                            );

            $this->db->insert('db_notifikasi.n_div', $dataSave);
        }
        
    }

    public function checkTglNow($tglInput)
    {
        $sql = 'select * from (
                select CURDATE() as skrg
                ) aa where "'.$tglInput.'" < skrg ';
        $query=$this->db->query($sql, array())->result_array();
        // print_r($tglInput);   
        if (count($query) > 0) {
            return false;
        }     
        else
        {
            return true;
        }
    }

    public function chkTgl($a1,$a2)
    {
        $sql = 'select * from (
                select CURDATE() as skrg
                ) aa where "'.$a1.'" < "'.$a2.'"';
        $query=$this->db->query($sql, array())->result_array();
        // print_r($sql);   
        if (count($query) > 0) {
            return true;
        }     
        else
        {
            return false;
        }
    }

    public function inserData_test($aaa)
    {
        $dataSave = array(
            'aaa' => $aaa,
        );
        $this->db->insert('test.aaa', $dataSave);
    }

    public function checkExistingTBL($field,$value,$table)
    {
        $a = $this->caribasedprimary($table,$field,$value);
        if (count($a) > 0) {
            // existing
            return false;
        }
        else
        {
            // nothing
            return true;
        }
    }

    public function getIndoBulan($date)
    {
        $bulan_arr = explode("-",$date);
        $date = $bulan_arr[1];
        switch ($date) {
            case 1: 
                $nama_bulan = "Januari";
                break;
            case 2:
                $nama_bulan = "Februari";
                break;
            case 3:
                $nama_bulan = "Maret";
                break;
            case 4:
                $nama_bulan = "April";
                break;
            case 5:
                $nama_bulan = "Mei";
                break;
            case 6:
                $nama_bulan = "Juni";
                break;
            case 7:
                $nama_bulan = "Juli";
                break;
            case 8:
                $nama_bulan = "Agustus";
                break;
            case 9:
                $nama_bulan = "September";
                break;
                // return "September";
                // break;
            case 10:
                $nama_bulan = "Oktober";
                break;
            case 11:
                $nama_bulan = "November";
                break;
            case 12:
                $nama_bulan = "Desember";
                break;
        }

       return $date = $bulan_arr[2]." ".$nama_bulan." ".$bulan_arr[0];  
    }

    public function checkDB($Database)
    {
        $sql = "show databases like '%".$Database."%'";
        $query=$this->db->query($sql, array())->result_array();
        if (count($query) > 0) {
            // existing
            return false;
        }
        else
        {
            return true;
        }
    }

    public function getLastNPM($ta,$ProdiID)
    {
        $sql = "select * from ".$ta.".students where ProdiID = ? order by ID desc limit 1";
        // print_r($ProdiID);
        $query=$this->db->query($sql, array($ProdiID))->result_array();
        return $query;
    }

    public function loadData_limit500($table,$fieldOrderby,$orderby)
    {
        $sql = "select * from ".$table." order by ".$fieldOrderby." ".$orderby." limit 500";
        //print_r($sql);
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function chkAuthDB_Base_URL($URL)
    {
        $a = explode('/', $URL);
        $b = count($a) - 1;
        $URISlug = 'and a.Slug = "'.$URL.'"';
        if ($a[$b] == 1) {
            $URISlug = '';
            for ($i=0; $i < count($b); $i++) { 
                $URISlug .= $a[$i].'/';
            }
            $URISlug = 'and a.Slug like "%'.$URISlug.'%"';
        }
        $sql = "select b.read,b.write,b.update,b.delete from db_admission.cfg_sub_menu as a join db_admission.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
        join db_admission.previleges_guser as c on c.G_user = b.cfg_group_user
        where c.NIP = ? ".$URISlug;
        $query=$this->db->query($sql, array($this->session->userdata('NIP')))->result_array();
        // print_r($query);die();
        return $query;
    }

    public function checkAuth_user()
    {
        $base_url = base_url();
        $currentURL = current_url();
        $URL = str_replace($base_url,"",$currentURL);
        
        // get Access URL
        $getDataSess  = $this->session->userdata('menu_admission_grouping');
        $access = array(
            'read' => 0,
            'write' => 0,
            'update' => 0,
            'delete' => 0,
        );

        $p = $this->chkAuthDB_Base_URL($URL);
        if (count($p) > 0 ) {
            $access = array(
                'read' => $p[0]['read'],
                'write' => $p[0]['write'],
                'update' => $p[0]['update'],
                'delete' => $p[0]['delete'],
            );
        }

        $html = '';
        if ($access['read'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-read", function() {
                   $(".btn-read").remove();
                 });

                 $(document).ready(function () {
                     $(".btn-read").remove();
                     //window.location.href = base_url_js+"vreservation/dashboard/view";
                     $(document).ajaxComplete(function () {
                         $(".btn-read").remove();
                     });
                 });
                 </script>
            ';
            echo $html;
        }

        if ($access['write'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-add", function() {
                   $(".btn-add").remove();
                 });

                 $(document).ready(function () {
                     $(".btn-add").remove();
                     $(document).ajaxComplete(function () {
                        $(".btn-add").remove();
                     });
                 });
                 </script>
            ';
            echo $html;
        }
        if ($access['update'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-edit", function() {
                   $(".btn-edit").remove();
                 });

                 $(document).ready(function () {
                     $(".btn-edit").remove();
                     $(document).ajaxComplete(function () {
                              $(".btn-edit").remove();
                     });
                 });
                 </script>
            ';
            echo $html;
        }
        if ($access['delete'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-delete", function() {
                   $(".btn-delete").remove();
                 });

                 waitForEl(".btn-Active", function() {
                   $(".btn-Active").remove();
                 });

                 $(document).ready(function () {
                    $(".btn-delete").remove();
                    $(".btn-Active").remove();
                    $(document).ajaxComplete(function () {
                        $(".btn-delete").remove();
                        $(".btn-Active").remove();
                    });
                     
                 });
                 
                 </script>
            ';
            echo $html;
        }


        // special menu & group
        $bool = true;
        foreach ($access as $key => $value) {
            if ($value == 0) {
                $bool = false;
                break;
            }
        }

        if (!$bool) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-delete-menu-auth", function() {
                    $(".btn-delete-menu-auth").remove();
                 });

                 waitForEl(".btn-edit-menu-auth", function() {
                   $(".btn-edit-menu-auth").remove();
                 });

                 waitForEl(".btn-edit-menu-auth", function() {
                   $(".btn-edit-menu-auth").remove();
                 })

                 waitForEl(".btn-add-menu-auth", function() {
                   $(".btn-add-menu-auth").remove();
                 });

                 waitForEl(".btn-delete-menu-auth", function() {
                   $(".btn-delete-menu-auth").remove();
                 });
                 
                 </script>
            ';
            echo $html;
        }
        return $html;
    }

    public function dateDifference($date1, $date2)
    {       
        $date1=strtotime($date1);
        $date2=strtotime($date2); 
        $diff = abs($date1 - $date2);
        
        $day = $diff/(60*60*24); // in day
        $dayFix = floor($day);
        $dayPen = $day - $dayFix;
        $secFix = 0;
        $minFix = 0;
        $hourFix = 0;
        if($dayPen > 0)
        {
            $hour = $dayPen*(24); // in hour (1 day = 24 hour)
            $hourFix = floor($hour);
            $hourPen = $hour - $hourFix;
            if($hourPen > 0)
            {
                $min = $hourPen*(60); // in hour (1 hour = 60 min)
                $minFix = floor($min);
                $minPen = $min - $minFix;
                if($minPen > 0)
                {
                    $sec = $minPen*(60); // in sec (1 min = 60 sec)
                    $secFix = floor($sec);
                }
            }
        }
        $str = "";
        if($dayFix > 0)
            $str.= $dayFix."_day;";
        if($hourFix > 0)
            $str.= $hourFix."_hour;";
        if($minFix > 0)
            $str.= $minFix."_min;";
        if($secFix > 0)
            $str.= $secFix."_sec ";

        return $minFix;
    }

    public function countTimeQuery($Start, $End)
    {
        $sql = 'select TIMEDIFF("'.$Start.'","'.$End.'") as time';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
        
    }

    public function getAllUserAutoComplete($Nama)
    {
        $sql = 'select CONCAT(a.Name," | ",a.NIP) as Name, a.NIP from db_employees.employees as a
          where (a.Name like "%'.$Nama.'%" or a.NIP like "%'.$Nama.'%" )
          GROUP BY a.NIP';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getDataWithoutSuperAdmin()
    {
        $sql = 'select * from db_admission.cfg_group_user where ID != 1';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function get_previleges_group_show($GroupID)
    {
        $sql = 'SELECT d.GroupAuth, b.Menu,c.SubMenu1,c.SubMenu2,c.ID_Menu,a.ID_cfg_sub_menu,a.ID as ID_previleges,a.`read`,a.`write`,a.`update`,
a.`delete`,c.`read` as readMenu,c.`update` as updateMenu,c.`write` as writeMenu,c.`delete` as deleteMenu from db_admission.cfg_rule_g_user as a
            join db_admission.cfg_group_user as d
            on a.cfg_group_user = d.ID
            join db_admission.cfg_sub_menu as c
            on a.ID_cfg_sub_menu = c.ID
            join db_admission.cfg_menu as b
            on b.ID = c.ID_Menu where d.ID = ? ';
        $query=$this->db->query($sql, array($GroupID))->result_array();
        return $query;
    }

    public function getCountAllDataAuth($table)
    {
        $sql = 'select count(*) as total from '.$table;
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['total'];
    }

    public function getDataWithoutSuperAdminGlobal($table)
    {
        $sql = 'select * from '.$table.' where ID != 1';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getShowIntervalBulan($Start,$End)
    {
        $arr = array();
        $date1 = $Start;
        $date2 = $End;

        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
        $diff = $diff+1;

        $aa = explode("-", $Start);
        $y = $aa[0];
        $m = $aa[1];
        $arr_bulan = array(
            'Jan','Feb','March','April','May','June','July','August','Sep','Oct','Nov','Des'
        );
        for ($i=0; $i < $diff; $i++) { 
            $bb = $m;
            if (strlen($bb) == 1) {
                $bb = '0'.$bb;
            }
            $keyValueFirst = $y.'-'.$bb;
            $c = (int)$m;
            $c = $c - 1;
            $month = $arr_bulan[$c];
            $arr[] = array('keyValueFirst' => $keyValueFirst,'MonthName' => $month);
            $m++;
            if ($m > 12) {
               $m = 1;
               $y++;
            }
        }
        
        return $arr;
    }

    public function getDepartementPu($field,$value)
    {
        $sql = 'select * from (
                select CONCAT("AC.",ID) as ID, NameEng as NameDepartement from db_academic.program_study
                UNION
                select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                ) aa where '.$field.' = ?';
        $query=$this->db->query($sql, array($value))->result_array();
        return $query;        
    }

    public function BulanInggris($MonthNumber)
    {
        $arr_bulan = array(
            'Jan','Feb','March','April','May','June','July','August','Sep','Oct','Nov','Des'
        );

        $MonthNumber = $MonthNumber - 1;
        $MonthName = '';
        try
        {
            $MonthName = $arr_bulan[$MonthNumber];
        }
        catch(Exception $ex)
        {
            $MonthName = '';
        }

        return $MonthName;
    }

    //============================ FORMATTER ============================
    public function moneySay($x) {
        $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        if ($x < 12)
            return " " . $abil[$x];
        elseif ($x < 20)
            return $this->moneySay($x - 10) . "belas";
        elseif ($x < 100)
            return $this->moneySay($x / 10) . " puluh" . $this->moneySay($x % 10);
        elseif ($x < 200)
            return " seratus" . $this->moneySay($x - 100);
        elseif ($x < 1000)
            return $this->moneySay($x / 100) . " ratus" . $this->moneySay($x % 100);
        elseif ($x < 2000)
            return " seribu" . $this->moneySay($x - 1000);
        elseif ($x < 1000000)
            return $this->moneySay($x / 1000) . " ribu" . $this->moneySay($x % 1000);
        elseif ($x < 1000000000)
            return $this->moneySay($x / 1000000) . " juta" . $this->moneySay($x % 1000000);
    }
    
    public function romawiNumber($get = NULL) {
        $rmw[1] = 'I';
        $rmw[2] = 'II';
        $rmw[3] = 'III';
        $rmw[4] = 'IV';
        $rmw[5] = 'V';
        $rmw[6] = 'VI';
        $rmw[7] = 'VII';
        $rmw[8] = 'VIII';
        $rmw[9] = 'IX';
        $rmw[10] = 'X';
        $rmw[11] = 'XI';
        $rmw[12] = 'XII';
        
        if (is_null($get)) {
            return $rmw;
        }
        else {
            return $rmw[intval($get)];
        }
    }

    public function dateDiffDays ($d1, $d2) {   
    // Return the number of days between the two dates:

      return round(abs(strtotime($d1)-strtotime($d2))/86400);

    }  // end function dateDiff

    public function dateDiffDays_ ($d1, $d2) {   
    // Return the number of days between the two dates:
      // check date d1 sudah melewati hari
      $result = round(abs(strtotime($d1)-strtotime($d2))/86400);
      $chktgl = $this->chktgl($d2,$d1);
      if (!$chktgl) {
            $result = $result -($result * 2);
      }  
      return $result;

    }  // end function dateDiff

    public function getEmployeesBaseStatus($Status)
    {
        $Status = implode(",", $Status);
        $sql = 'select * from db_employees.employees where StatusEmployeeID in ('.$Status.')';
        $query=$this->db->query($sql, array())->result_array();
        return $query; 
    }

    public function MasterfileStatus($Colom)
    {

        $sql = 'SELECT ID FROM db_employees.master_files where TypeFiles = "'.$Colom.'" ';
        $query=$this->db->query($sql, array())->result_array();
        return $query;   
                                      
    }

    public function AuthAPI($arr_content)
    {
        $key = 's3Cr3T-G4N';
        if(array_key_exists("auth",$arr_content))
        {    
            if ($arr_content['auth'] == $key) {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function getData_rule_service()
    {
        $sql = 'select a.ID as IDPri,a.*,b.*,c.* from db_employees.rule_service as a
                left join db_employees.division as b on 
                a.IDDivision = b.ID
                left join db_employees.service as c
                on a.IDService = c.ID
                order by b.ID asc
                ';
        $query=$this->db->query($sql, array())->result_array();
        return $query;         
    }

    public function GetSemester($Year,$SemesterSearch)
    {
        $get = $this->showData_array('db_academic.semester');
        $Semester = 0;
        for ($i=0; $i < count($get); $i++) { 
            if ($Year == $get[$i]['Year'] && $get[$i]['Code'] == 1) {
                $Semester++;
            }
            else
            {
                if ($Semester > 0) {
                    if ($get[$i]['ID'] == $SemesterSearch) {
                        $Semester++;
                        break;
                    }
                    else
                    {
                        $Semester++;
                    }
                }
            }
        }

        return $Semester;
    }

    public function apiservertoserver($url,$token = '')
    {
        $rs = array();
        $Input = $token;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                    "token=".$Input);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $pr = curl_exec($ch);
        curl_close ($ch);
        $rs = (array) json_decode($pr,true);
        return $rs;
    }

    public function UserQNA($IDDivision = '')
    {
        $arr_result = array();
        $Q_add = ($IDDivision == '') ? '' : ' where Division_ID = "'.$IDDivision.'" order by Division_ID asc,Type asc';
        $sql = 'select * from db_employees.user_qna '.$Q_add;
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) { 
            $Type1 = $query[$i]['Type'];
            $temp = array('Type' => $Type1);
            $datatemp = array();
            $datatemp[] = array(
                'Questions' => $query[$i]['Questions'],
                'Answers' => $query[$i]['Answers'],
                'File' => $query[$i]['File'],
            );

            for ($j=$i+1; $j < count($query); $j++) { 
                $Type2 = $query[$j]['Type'];
                if ($Type1 == $Type2) {
                  $datatemp[] = array(
                      'Questions' => $query[$j]['Questions'],
                      'Answers' => $query[$j]['Answers'],
                      'File' => $query[$j]['File'],
                  );  
                }
                else
                {
                    $i = $j-1;
                    break;
                }

                 $i=$j;
            }

            $temp['data'] = $datatemp;
            $arr_result[] = $temp;

        }


        return $arr_result;
    }
}