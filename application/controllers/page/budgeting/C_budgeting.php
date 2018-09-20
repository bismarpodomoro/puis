<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_budgeting extends Budgeting_Controler {
    public $Msg = array(
            'Duplicate' => 'The data duplicate, Please check',
            'NotAction' => 'The data has been used for transaction, Cannot be action',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('master/m_master');
        $this->data['department'] = parent::__getDepartement(); 
    }

    public function index()
    {
        $this->session->unset_userdata('auth_budgeting_sess');
        $this->session->unset_userdata('menu_budgeting_sess');
        $this->session->unset_userdata('menu_budgeting_grouping');
        $IDdepartementNavigation = $this->session->userdata('IDdepartementNavigation');
        switch ($IDdepartementNavigation) {
            case 12: // IT
                // print_r($IDdepartementNavigation);
                $this->BudgetingIT();
                break;
            case 9: // IT
                // print_r($IDdepartementNavigation);
                $this->BudgetingFinance();
                break;    
            default:
                # code...
                break;
        }
        
    }

    public function BudgetingIT()
    {
         echo __FUNCTION__;
    }

    public function BudgetingFinance()
    {
        // get previleges for menu and content
        $MenuDepartement= 'NA.'.$this->session->userdata('IDdepartementNavigation');
        $this->getAuthSession($MenuDepartement);
        // $content = '<pre>'.print_r($this->session->userdata('menu_budgeting_grouping')).'</pre>';
        $content = $this->load->view('page/'.$this->data['department'].'/budgeting/dashboard',$this->data,true);
        $this->temp($content);
    }

    public function configfinance($Request = null)
    {
        $arr_menuConfig = array('CodePrefix',
                                'TimePeriod',
                                'MasterPost',
                                'SetPostDepartement',
                                'MasterUserRole',
                                'UserRole',
                                'Catalog',
                                'Supplier',
                                null
                            );
        if (in_array($Request, $arr_menuConfig))
          {
            $this->data['request'] = $Request;
            $content = $this->load->view('page/'.$this->data['department'].'/budgeting/configfinance',$this->data,true);
            $this->temp($content);
          }
        else
          {
            show_404($log_error = TRUE);
          }
    }

    public function pageLoadTimePeriod()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/pageLoadTimePeriod',$this->data,true);
        echo json_encode($arr_result);
    }

    public function modal_pageLoadTimePeriod()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
            $query=$this->db->query($sql, array($this->data['id']))->result_array();
            $this->data['getData'] = $query;
        }
        echo $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/modalform_timeperiod',$this->data,true);
    }

    public function modal_pageLoadTimePeriod_save()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';
        switch ($input['Action']) {
            case 'add':
                $dateStart = cal_days_in_month(CAL_GREGORIAN, $input['MonthStart'], $input['Year']); 
                $dateEnd= cal_days_in_month(CAL_GREGORIAN, $input['MonthEnd'], $input['Year']);
                $Year = $input['Year'];
                $StartPeriod = $Year.'-'.$input['MonthStart'].'-'.$dateStart;
                $EndPeriod = ($Year + 1).'-'.$input['MonthEnd'].'-'.$dateEnd;
                $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
                $query=$this->db->query($sql, array($Year))->result_array();
                if (count($query) > 0) {
                    $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                    $dataSave = array(
                        'Year' => $Year,
                        'StartPeriod' => $StartPeriod,
                        'EndPeriod' => $EndPeriod
                    );
                    $this->db->insert('db_budgeting.cfg_dateperiod', $dataSave);
                }

                break;
            case 'edit':
                $dateStart = cal_days_in_month(CAL_GREGORIAN, $input['MonthStart'], $input['Year']); 
                $dateEnd= cal_days_in_month(CAL_GREGORIAN, $input['MonthEnd'], $input['Year']);
                $Year = $input['Year'];
                $StartPeriod = $Year.'-'.$input['MonthStart'].'-'.$dateStart;
                $EndPeriod = ($Year + 1).'-'.$input['MonthEnd'].'-'.$dateEnd;
                $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
                $query=$this->db->query($sql, array($Year))->result_array();

                $Status = $query[0]['Status']; // check can be delete
                if ($Status == 1) {
                    try {
                        $dataSave = array(
                            'Year' => $Year,
                            'StartPeriod' => $StartPeriod,
                            'EndPeriod' => $EndPeriod
                        );
                        $this->db->where('Year', $Year);
                        $this->db->where('Active', 1);
                        $this->db->update('db_budgeting.cfg_dateperiod', $dataSave);
                    } catch (Exception $e) {
                         $Msg = $this->Msg['Duplicate'];
                    }
                }
                else
                {
                    $Msg = $this->Msg['NotAction'];
                }
                break;
            case 'delete':
                $Year = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
                $query=$this->db->query($sql, array($Year))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       $dataSave = array(
                           'Year' => $Year,
                           'StartPeriod' => $query[0]['StartPeriod'],
                           'EndPeriod' => $query[0]['EndPeriod'],
                           'Active' => 0
                       );
                       $this->db->where('Year', $Year);
                       $this->db->where('Active', 1);
                       $this->db->update('db_budgeting.cfg_dateperiod', $dataSave);
                   }
                   else
                   {
                       $Msg = $this->Msg['NotAction'];
                   }
                break;
            default:
                # code...
                break;
        }

        echo json_encode($Msg);
    }

    public function LoadTable_db_budgeting_cari($table,$field,$fieldValue,$Active = null)
    {
        $this->auth_ajax();
        $query = array();
        if ($Active == null) {
            $sql = 'select * from db_budgeting.'.$table.' where '.$field.' = ?';
            $query=$this->db->query($sql, array($fieldValue))->result_array();
        }
        else
        {
            $sql = 'select * from db_budgeting.'.$table.' where '.$field.' = ? and Active = ?';
            $query=$this->db->query($sql, array($fieldValue,$Active))->result_array();
        }

        echo json_encode($query);
    }

    public function LoadTable_db_budgeting_all($table,$Active = null)
    {
        $this->auth_ajax();
        $query = array();
        if ($Active == null) {
            $sql = 'select * from db_budgeting.'.$table;
            $query=$this->db->query($sql, array())->result_array();
        }
        else
        {
            $sql = 'select * from db_budgeting.'.$table.' where Active = ?';
            $query=$this->db->query($sql, array($Active))->result_array();
        }

        echo json_encode($query);
    }

    public function loadCodePrefix()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $this->data['loadData'] = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
        $this->data['loadData'] = json_encode($this->data['loadData']);
        $arr_result['html'] = $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/pageloadCodePrefix',$this->data,true);
        echo json_encode($arr_result);
    }

    public function pageloadMasterPost()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/pageloadMasterPost',$this->data,true);
        echo json_encode($arr_result);
    }

    public function save_codeprefix()
    {
        $this->auth_ajax();
        $input =  $this->getInputToken();
        if(array_key_exists("CodePost",$input))
        {
            $dataSave = array(
                'CodePost' => $input['CodePost'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodePost",$input))
        {
            $dataSave = array(
                'LengthCodePost' => $input['LengthCodePost'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodePostRealisasi",$input))
        {
            $dataSave = array(
                'CodePostRealisasi' => $input['CodePostRealisasi'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodePostRealisasi",$input))
        {
            $dataSave = array(
                'LengthCodePostRealisasi' => $input['LengthCodePostRealisasi'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodePostBudget",$input))
        {
            $dataSave = array(
                'CodePostBudget' => $input['CodePostBudget'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("YearCodePostBudget",$input))
        {
            $dataSave = array(
                'YearCodePostBudget' => $input['YearCodePostBudget'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodePostBudget",$input))
        {
            $dataSave = array(
                'LengthCodePostBudget' => $input['LengthCodePostBudget'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodeCatalog",$input))
        {
            $dataSave = array(
                'CodeCatalog' => $input['CodeCatalog'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodeCatalog",$input))
        {
            $dataSave = array(
                'LengthCodeCatalog' => $input['LengthCodeCatalog'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodeSupplier",$input))
        {
            $dataSave = array(
                'CodeSupplier' => $input['CodeSupplier'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodeSupplier",$input))
        {
            $dataSave = array(
                'LengthCodeSupplier' => $input['LengthCodeSupplier'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }
    }

    public function get_cfg_postrealisasi()
    {
        $this->auth_ajax();
        $getData = $this->m_budgeting->getData_cfg_postrealisasi(1);
        echo json_encode($getData);
    }

    public function modal_pageloadMasterPost()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        // print_r($this->data);
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
            $query=$this->db->query($sql, array($this->data['id']))->result_array();
            $this->data['getData'] = $query;
        }
        echo $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/modalform_masterpost',$this->data,true);

    }

    public function modal_pageloadMasterPost_save()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';
        switch ($input['Action']) {
            case 'add':
                $NeedPrefix = $input['NeedPrefix'];
                $CodePost = $input['CodePost'];
                if ($NeedPrefix == 1) { // get the code
                    $CfgCode = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
                    $CodePostPrefix = $CfgCode[0]['CodePost'];
                    $LengthCode = $CfgCode[0]['LengthCodePost'];
                    $tbl = 'db_budgeting.cfg_post';
                    $fieldCode = 'CodePost';
                    $CodePost = $this->m_budgeting->getTheCode($tbl,$fieldCode,$CodePostPrefix,$LengthCode);
                }


                $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePost))->result_array();
                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                   $dataSave = array(
                       'CodePost' => $CodePost,
                       'PostName' => trim(ucwords($input['PostName'])),
                       'CreatedBy' => $this->session->userdata('NIP'),
                       'CreatedAt' => date('Y-m-d'),
                   );
                   $this->db->insert('db_budgeting.cfg_post', $dataSave);
                }
                break;
            case 'edit':
                $CodePost = $input['CodePost'];
                $query = array();
                if ($CodePost != $input['CDID']) {
                    $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                    $query=$this->db->query($sql, array($CodePost))->result_array();
                }

                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                    $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                    $query=$this->db->query($sql, array($input['CDID']))->result_array();
                    $Status = $query[0]['Status'];
                    if ($Status == 1) {
                        try {
                           $dataSave = array(
                               'CodePost' => $CodePost,
                               'PostName' => trim(ucwords($input['PostName'])),
                           );
                           $this->db->where('CodePost', $input['CDID']);
                           $this->db->where('Active', 1);
                           $this->db->update('db_budgeting.cfg_post', $dataSave);
                        } catch (Exception $e) {
                             $Msg = $this->Msg['Duplicate'];
                        }   
                    }
                    else
                    {
                        $Msg = $this->Msg['NotAction'];
                    }
                }
                break;
            case 'delete':
                $CodePost = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePost))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       $dataSave = array(
                           'Active' => 0
                       );
                       $this->db->where('CodePost', $CodePost);
                       $this->db->where('Active', 1);
                       $this->db->update('db_budgeting.cfg_post', $dataSave);
                   }
                   else
                   {
                       $Msg = $this->Msg['NotAction'];
                   }
                break;
            default:
                # code...
                break;
        }

        echo json_encode($Msg);
    }

    public function modal_postrealisasi()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
            $query=$this->db->query($sql, array($this->data['id']))->result_array();
            $this->data['getData'] = $query;
        }
        echo $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/modal_postrealisasi',$this->data,true);
    }

    public function save_postrealisasi()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';
        switch ($input['Action']) {
            case 'add':
                $NeedPrefix = $input['NeedPrefix'];
                $CodePostRealisasi = $input['CodePostRealisasi'];
                if ($NeedPrefix == 1) { // get the code
                    $CfgCode = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
                    $CodePostPrefix = $CfgCode[0]['CodePostRealisasi'];
                    $LengthCode = $CfgCode[0]['LengthCodePostRealisasi'];
                    $tbl = 'db_budgeting.cfg_postrealisasi';
                    $fieldCode = 'CodePostRealisasi';
                    $CodePostRealisasi = $this->m_budgeting->getTheCode($tbl,$fieldCode,$CodePostPrefix,$LengthCode);
                }


                $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePostRealisasi))->result_array();
                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                   $dataSave = array(
                       'CodePostRealisasi' => $CodePostRealisasi,
                       'CodePost' => $input['PostItem'],
                       'RealisasiPostName' => trim(ucwords($input['RealisasiPostName'])),
                       'Departement' => $input['Departement'],
                       'CreatedBy' => $this->session->userdata('NIP'),
                       'CreatedAt' => date('Y-m-d'),
                   );
                   $this->db->insert('db_budgeting.cfg_postrealisasi', $dataSave);
                }
                break;
            case 'edit':
                $CodePostRealisasi = $input['CodePostRealisasi'];
                $query = array();
                if ($CodePostRealisasi != $input['CDID']) {
                    $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                    $query=$this->db->query($sql, array($CodePostRealisasi))->result_array();
                }

                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                    $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                    $query=$this->db->query($sql, array($input['CDID']))->result_array();
                    $Status = $query[0]['Status'];
                    if ($Status == 1) {
                        try {
                           $dataSave = array(
                               'CodePostRealisasi' => $CodePostRealisasi,
                               'RealisasiPostName' => trim(ucwords($input['RealisasiPostName'])),
                               'CodePost' => $input['PostItem'],
                               'Departement' => $input['Departement'],
                           );
                           $this->db->where('CodePostRealisasi', $input['CDID']);
                           $this->db->where('Active', 1);
                           $this->db->update('db_budgeting.cfg_postrealisasi', $dataSave);
                        } catch (Exception $e) {
                             $Msg = $this->Msg['Duplicate'];
                        }   
                    }
                    else
                    {
                        $Msg = $this->Msg['NotAction'];
                    }
                }
                break;
            case 'delete':
                $CodePostRealisasi = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePostRealisasi))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       $dataSave = array(
                           'Active' => 0
                       );
                       $this->db->where('CodePostRealisasi', $CodePostRealisasi);
                       $this->db->where('Active', 1);
                       $this->db->update('db_budgeting.cfg_postrealisasi', $dataSave);
                   }
                   else
                   {
                       $Msg = $this->Msg['NotAction'];
                   }
                break;
            default:
                # code...
                break;
        }

        echo json_encode($Msg);

    }

    public function LoadSetPostDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/pageSetPostDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function LoadInputsetPostDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/'.$this->data['department'].'/budgeting/configuration/setpostdepartement/pageInputsetPostDepartement',$this->data,true);
        echo json_encode($arr_result);
    }


}
