<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_finance extends CI_Model {

   private $data = array();
   function __construct()
   {
       parent::__construct();
   }

   public function getEmailnURLCheckbox($arr,$delimiter)
   {
    $this->load->library('JWT');
    $key = "UAP)(*";
    $arr_temp = array();
    for ($i=0; $i < count($arr); $i++) { 
      $temp = explode($delimiter, $arr[$i]);
      $url = $this->jwt->encode($temp[0].";".$temp[2],$key);
      $arr_temp[] = array('email'=>$temp[2],'url' => $url);
    }
    return $arr_temp;
   }

   public function ProcessSaveDataVerification($arrData)
   {
      $arrData = explode(",", $arrData);
      for ($i=0; $i < count($arrData); $i++) { 
        $temp = explode(";", $arrData[$i]);
        if ($temp[0] != 'nothing') {
          if ($temp[1] == 'null') {
            // insert data ke db register_verification
            $this->saveData_register_verification($temp[0]);
            $this->load->model('master/m_master');
            $query = $this->m_master->caribasedprimary('db_admission.register_verification','RegisterID',$temp[0]);
            $id_register_verification = $query[0]['ID'];
            $this->saveDataRegisterVerified($id_register_verification);
          }
          else
          {
            // dapatkan id register verification dahulu
            $this->load->model('master/m_master');
            $query = $this->m_master->caribasedprimary('db_admission.register_verification','RegisterID',$temp[0]);
            $id_register_verification = $query[0]['ID'];
            $this->saveDataRegisterVerified($id_register_verification);
          }
        }
      }
   }

   public function saveData_register_verification($registerID)
   {
    $dataSave = array(
            'RegisterID' => $registerID,
                    );

    $this->db->insert('db_admission.register_verification', $dataSave);
   }

   public function saveDataRegisterVerified($register_verified)
   {
    $getFormulirCode = $this->getFormulirCode('online');
    $dataSave = array(
            'RegVerificationID' => $register_verified,
            'FormulirCode' => $getFormulirCode,
            'VerificationBY' => $this->session->userdata('NIP'),
            'VerificationAT' => date('Y-m-d H:i:s'),
                    );
    $this->db->insert('db_admission.register_verified', $dataSave);
   }

   public function getFormulirCode($tipeFormulirCode = null)
   {
    if ($tipeFormulirCode == 'online') { // online
        $sql = "select FormulirCode from db_admission.formulir_number_online_m where Status = 0 and Years ='".date('Y')."' order by ID asc limit 1";
    }
    else{
      $sql = "select FormulirCode from db_admission.formulir_number_offline_m where Status = 0 and Years ='".date('Y')."' order by ID asc limit 1";
    }
    $query=$this->db->query($sql, array())->result_array();
    $FormulirCode = $query[0]['FormulirCode'];
    
    if ($tipeFormulirCode == 'online') { // online
      $this->updateStatusFormulirCodeOnline($FormulirCode);
    }
    else
    {
      $this->updateStatusFormulirCodeOffline($FormulirCode);
    }

    return $FormulirCode;
   }

   public function updateStatusFormulirCodeOnline($FormulirCode)
   {
    $sql = "update db_admission.formulir_number_online_m set Status = 1 where FormulirCode = '".$FormulirCode."'";
    $query=$this->db->query($sql, array());
   }

   public function updateStatusFormulirCodeOffline($FormulirCode)
   {
    $sql = "update db_admission.formulir_number_offline_m set Status = 1 where FormulirCode = '".$FormulirCode."'";
    $query=$this->db->query($sql, array());
   }

   public function loadData_calon_mahasiswa_created($limit, $start,$Nama,$selectProgramStudy,$Sekolah)
   {
     $arr_temp = array('data' => array());
     if($Nama != '%') {
         $Nama = '"%'.$Nama.'%"'; 
     }
     else
     {
       $Nama = '"%"'; 
     }
     
     if($selectProgramStudy != '%') {
       $selectProgramStudy = '"%'.$selectProgramStudy.'%"'; 
     }
     else
     {
       $selectProgramStudy = '"%"'; 
     }

     if($Sekolah != '%') {
       $Sekolah = '"%'.$Sekolah.'%"'; 
     }
     else
     {
       $Sekolah = '"%"'; 
     }

       $sql = 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
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
           where d.Name like '.$Nama.' and d.SchoolID like '.$Sekolah.' and a.ID_program_study like '.$selectProgramStudy.' and a.ID in (select ID_register_formulir from db_admission.register_nilai where Status = "Verified") LIMIT '.$start. ', '.$limit;
          $query=$this->db->query($sql, array())->result_array();
          return $query;
   }

   public function getVerified_Nilai($ID_register_formulir)
   {
    $sql = 'select a.*,b.Name from db_admission.register_nilai as a
            join db_employees.employees as b
            on b.NIP = a.VerifiedBY
            where a.ID_register_formulir = ? limit 1';
    $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
    return $query;
   }

   public function submit_approved_nilai_rapor($input)
   {
    for ($i=0; $i < count($input); $i++) {
      $dataSave = array(
              'ApprovedBY' => $this->session->userdata('NIP'),
              'Status' => 'Approved',
              'ApprovedAT' => date('Y-m-d'),
                      );
      $this->db->where('ID_register_formulir',$input[$i]);
      $this->db->update('db_admission.register_nilai', $dataSave);
    }
   }

   public function set_tuition_fee_approve($input)
   {
    for ($i=0; $i < count($input); $i++) { 
      $dataSave = array(
              'ApprovedBY' => $this->session->userdata('NIP'),
              'Status' => 'Approved',
              'ApprovedAT' => date('Y-m-d'),
                      );
      $this->db->where('ID_register_formulir',$input[$i]);
      $this->db->update('db_finance.payment_register', $dataSave);
      // generate pdf
      $this->PDF_tuition_fee_approved($input[$i]);
    }
   }

   public function update_payment_pre($BilingID)
   {
      $dataSave = array(
              'Status' => 1,
              'UpdateAt' => date('Y-m-d H:i:s'),
                      );
      $this->db->where('BilingID',$BilingID);
      $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function proses_cicilan($ID_register_formulir,$data_register)
   {
    $msg = 'Your Payment is complete.';
    $checkCicilan = $this->checkCicilan($ID_register_formulir,0);
    if (count($checkCicilan) > 0) {
      $Invoice = $checkCicilan[0]['Invoice'];
      $Deadline = $checkCicilan[0]['Deadline'];
      $ID = $checkCicilan[0]['ID'];
      $p = $this->create_va_Payment($Invoice,$Deadline, $data_register[0]['Name'], $data_register[0]['Email'],$data_register[0]['VA_number']);
      $this->updateCicilanBiling($p,$ID);
      $msg = 'Please continue to pay the next installment with VA Number : '.$data_register[0]['VA_number']. ' <br> as much as Rp '.number_format($checkCicilan[0]['Invoice'],2,',','.');
    }

    return $msg;

   }

   public function updateCicilanBiling($data,$ID)
   {
      $dataSave = array(
              'BilingID' => $data['msg']['trx_id'],
                      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function checkCicilan($ID_register_formulir,$status)
   {
    $sql = 'select * from db_finance.payment_pre where ID_register_formulir = ? and Status = ? order by ID asc limit 1';
    $query=$this->db->query($sql, array($ID_register_formulir,$status))->result_array();
    return $query;
   }

   public function create_va_Payment($payment = null,$DeadLinePayment = null, $Name = null, $Email = null,$VA_number = null,$description = 'Pembayaran Uang Kuliah',$tableRoutes = 'db_finance.payment_pre')
   {
       $arr = array();
       $arr['status'] = false;
       $arr['msg'] = '';
       if ($payment != null) {
           include_once APPPATH.'third_party/bni/BniEnc.php';
           // FROM BNI
           $this->load->model('master/m_master');
           $aa = $this->m_master->showData_array('db_va.cfg_bank');
            $client_id = '00202';
           // $client_id = $aa[0]['client_id'];
            $secret_key = '8ef738df0433c674e6663f3f7f5e6b68';
           // $secret_key = $aa[0]['secret_key'];
            $url = 'https://apibeta.bni-ecollection.com/';
           // $url = $aa[0]['url'];
           $getVANumber = $VA_number;
           $datetime_expired = $DeadLinePayment;
           // $payment = str_replace('.', '', $payment);

           if ($getVANumber != null) {
               $data_asli = array(
                   'client_id' => $client_id,
                   'trx_id' => mt_rand(), // fill with Billing ID
                   'trx_amount' => $payment,
                   'billing_type' => 'c',
                   'datetime_expired' => $datetime_expired, // billing will be expired in 2 hours
                   'virtual_account' => $getVANumber,
                   'customer_name' => $Name,
                   'customer_email' => $Email,
                   'customer_phone' => '+622129200456',
                   'description' => $description,
                   'type' => 'createbilling',
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
                   $arr['status'] = false;
                   $arr['msg'] = '';
               }
               else {
                   $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
                   $this->insert_va_log($data_asli,$tableRoutes);
                   $arr['status'] = true;
                   $arr['msg'] = $data_asli;
               }
           }
           else
           {
               $arr['status'] = false;
               $arr['msg'] = '';
           }
       }
       
       return $arr;
   }

   private function get_content($url, $post = '') {
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

   public function insert_va_log($data,$routes_table = 'db_admission.register')
   {
       $dataSave = array(
               'trx_id' => $data['trx_id'],
               'virtual_account' => $data['virtual_account'],
               'customer_name' => $data['customer_name'],
               'customer_email' => $data['customer_email'],
               'customer_phone' => $data['customer_phone'],
               'billing_type' => $data['billing_type'],
               'trx_amount' => $data['trx_amount'],
               'datetime_expired' => $data['datetime_expired'],
               'description' => $data['description'],
               'Status' => 0,
               'Created' => date('Y-m-d H:i:s'),
               'routes_table' => $routes_table,
                       );

       $this->db->insert('db_va.va_log', $dataSave);
   }

   public function updateCicilanDeadline($data,$ID)
   {
      $dataSave = array(
              'BilingID' => $data['trx_id'],
              'Deadline' => $data['datetime_expired'],
                      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function update_va_log($data,$routes_table = 'db_admission.register')
   {
       $dataSave = array(
               'customer_name' => $data['customer_name'],
               'customer_email' => $data['customer_email'],
               'customer_phone' => $data['customer_phone'],
               'trx_amount' => $data['trx_amount'],
               'datetime_expired' => $data['datetime_expired'],
               'description' => $data['description'],
               'Status' => 2,
               'Created' => date('Y-m-d H:i:s'),
               'routes_table' => $routes_table,
                       );
       $this->db->where('trx_id',$data['trx_id']);
       $this->db->update('db_va.va_log', $dataSave);
   }

   public function update_va_Payment($payment = null,$DeadLinePayment = null, $Name = null, $Email = null,$BilingID = null,$routes_table = 'db_finance.payment_pre')
   {
       $arr = array();
       $arr['status'] = false;
       $arr['msg'] = '';
       if ($payment != null) {
           include_once APPPATH.'third_party/bni/BniEnc.php';
           // FROM BNI
           $client_id = '00202';
           $secret_key = '8ef738df0433c674e6663f3f7f5e6b68';
           $url = 'https://apibeta.bni-ecollection.com/';
           $datetime_expired = $DeadLinePayment;

           if ($BilingID != null) {
               $data_asli = array(
                   'client_id' => $client_id,
                   'trx_id' => $BilingID, // fill with Billing ID
                   'trx_amount' => $payment,
                   'customer_name' => $Name,
                   'customer_email' => $Email,
                   'customer_phone' => '+622129200456',
                   'datetime_expired' => $datetime_expired, // billing will be expired in 2 hours
                   'description' => 'Pembayaran Uang Kuliah',
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
                   
                   $arr['status'] = false;
                   $arr['msg'] = '';
               }
               else {
                   $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
                   $this->update_va_log($data_asli,$routes_table);
                   $arr['status'] = true;
                   $arr['msg'] = $data_asli;
               }
           }
           else
           {
               $arr['status'] = false;
               $arr['msg'] = '';
           }
       }
       
       return $arr;
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

   public function checkBiling($biling)
   {
       include_once APPPATH.'third_party/bni/BniEnc.php';
       $arr_temp = array();
       // include_once APPPATH.'third_party/bni/BniEnc.php';
       $client_id = '00202';
       $secret_key = '8ef738df0433c674e6663f3f7f5e6b68';
       $url = 'https://apibeta.bni-ecollection.com/';
       
           $data_asli = array(
               'client_id' => $client_id,
               'trx_id' => $biling, // fill with Billing ID
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
               $arr_temp['msg'] = $response_json;
               $arr_temp['trx_id'] = $biling;

           }
           else {
               $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
               // $arr_temp['msg'] = $response_json['status'];
               $arr_temp['msg'] = $data_response;
               $arr_temp['trx_id'] = $biling;
           }

       return $arr_temp;
   }

   public function getTuitionFee($ID_register_formulir)
   {
    $sql = 'select d.id as ID_register_formulir,b.ProdiID,a.Description,b.Cost,c.Discount,c.Pay_tuition_fee from db_finance.payment_type as a
            join db_finance.tuition_fee as b on a.ID = b.PTID
            join db_admission.register_formulir as d
            on d.ID_program_study = b.ProdiID
            join db_finance.payment_register as c
            on c.ID_register_formulir = d.ID
            where c.PTID = a.ID and d.ID = ? and b.ClassOf = YEAR(CURDATE())';
      $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
      return $query;      
   }

   private function PDF_tuition_fee_approved($ID_register_formulir)
   {
       //error_reporting(0);
       $this->load->model('master/m_master');
       $this->load->model('admission/m_admission');

       $query = $this->m_admission->getDataPersonal($ID_register_formulir);
       $query2 = $this->m_master->caribasedprimary('db_admission.school','ID',$query[0]['SchoolID']);

       $data = $this->getTuitionFee($ID_register_formulir);
       $query3 = $this->m_master->caribasedprimary('db_academic.program_study','ID',$data[0]['ProdiID']);

       $arr_temp = array('filename' => '');
       $filename = 'Tuition_fee.pdf';
       $getData = $this->m_master->showData_array('db_admission.set_label_token_off');
       $setXAwal = 10;
       $setYAwal = 18;
       $setJarakY = 5;
       $setJarakX = 40;
       $setFontIsian = 12;
           $config=array('orientation'=>'P','size'=>'A4');
           $this->load->library('mypdf',$config);
           $this->mypdf->SetMargins(10,10,10,10);
           $this->mypdf->SetAutoPageBreak(true, 0);
           $this->mypdf->AddPage();
           // Logo
           $this->mypdf->Image('./images/logo_tr.png',10,10,50);
           $this->mypdf->SetFont('Arial','B',10);
           $this->mypdf->Text(150, 17, 'Formulir Number : '.$query[0]['FormulirCode']);

           // Line break
           $this->mypdf->Ln(20);

           // isian
           $setY = $setYAwal + 20;
           $setX = $setXAwal; 

           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Nama', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query[0]['Name'], 0, 1, 'L', 0);

           $setY = $setY + 8;

           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Sekolah', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query2[0]['SchoolName'], 0, 1, 'L', 0);

           $setY = $setY + 8;
           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Program Studi', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query3[0]['Name'], 0, 1, 'L', 0);

           $setY = $setY + 8;
           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Virtual Account', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query[0]['VA_number'], 0, 1, 'L', 0);
          
            $setY = $setY + 10;

           $this->mypdf->SetXY($setX,$setY); 
           $this->mypdf->SetFillColor(226, 226, 226);
           $this->mypdf->Cell(8,9,'No',1,0,'C',true);
           $this->mypdf->Cell(45,9,'Type Biaya Kuliah',1,0,'C',true);
           $this->mypdf->Cell(60,9,'Harga',1,0,'C',true);
           $this->mypdf->Cell(25,9,'Diskon',1,0,'C',true);
           $this->mypdf->Cell(50,9,'Pembayaran',1,1,'C',true);

          $t = 0; 
          for ($i=0; $i < count($data); $i++) { 
            $no = $i + 1;
            $this->mypdf->SetFillColor(255, 255, 255);
            $this->mypdf->Cell(8,9,$no,1,0,'C',true);
            $this->mypdf->Cell(45,9,$data[$i]['Description'],1,0,'L',true);
            $this->mypdf->Cell(60,9,'Rp '.number_format($data[$i]['Cost'],2,',','.'),1,0,'L',true);
            $this->mypdf->Cell(25,9,$data[$i]['Discount'].'%',1,0,'L',true);
            $this->mypdf->Cell(50,9,'Rp '.number_format($data[$i]['Pay_tuition_fee'],2,',','.'),1,1,'L',true);
            $t = $t + $data[$i]['Pay_tuition_fee'];
          }

          $this->mypdf->Cell(8,9,'',0,0,'C',true);
          $this->mypdf->Cell(45,9,'',0,0,'L',true);
          $this->mypdf->Cell(60,9,'',0,0,'L',true);
          $this->mypdf->Cell(25,9,'',0,0,'L',true);
          $this->mypdf->Cell(50,9,'Rp '.number_format($t,2,',','.'),0,1,'L',true);

          $this->mypdf->Cell(25,5,'Note : ',0,1,'L',true);
          $this->mypdf->SetFont('Arial','',9);
          $this->mypdf->Cell(100,5,'* Biaya kuliah per semester : Biaya BPP + (Biaya per SKS (Credit) * Jumlah SKS) +  Biaya lain-lain persemester,',0,1,'L',true);
          $this->mypdf->Cell(100,5,'* Jika calon mahasiswa tidak lulus Ujian Nasional (UN) maka biaya yang telah dibayarkan akan dikembalikan dan ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  dipotong biaya administrasi sebesar Rp 500.000,00 setelah menunjukan surat keterangan dari sekolah, ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'* Apabila diterima di Perguruan Tinggi Negri (PTN) yaitu UI,ITB,UNPAD,UNDIP,IPB,UGM,UNAIR,ITS melalui ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  jalur SNMPTN & SBMPTN (tidak termasuk jalur Ujian Mandiri, program diploma & politeknik negri) maka biaya yang telah',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  dibayarkan akan dikembalikan & dipotong biaya administrasi Rp 1.500.000,00 ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  (dengan menunjukan surat penerimaan dari universitas terkait) ',0,1,'L',true);
          
           
           $this->mypdf->Line(20, 280, 190, 280);
           $setY = 282;
           $this->mypdf->SetFont('Arial','',6);
           $this->mypdf->SetXY(40,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           // $this->mypdf->SetFillColor(0,0,0);
           $this->mypdf->Cell(190, 5, 'Admission Office :  Central Park Mall, Lantai 3, Unit 112, Podomoro City, JL Letjen S. Parman Kav.28, Jakarta Barat 11470', 0, 1, 'L', 0);
           $setY = 285;
           $this->mypdf->SetFont('Arial','',6);
           $this->mypdf->SetXY(43,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           // $this->mypdf->SetFillColor(0,0,0);
           $this->mypdf->Cell(190, 5, 'Telp : (021) 292 00 456    Email : admission@podomorouniversity.ac.id   Website : www.podomorouniversity.ac.id', 0, 1, 'L', 0);

           $path = './document';
           $path = $path.'/'.$filename;
           $this->mypdf->Output($path,'F');

           $this->load->model('m_sendemail');
           $text = 'Dear '.$query[0]['Name'].',<br><br>
                       Plase find attached your payment.<br>
                       Please login to your portal ('.$this->GlobalVariableAdi['url_registration']."login/".') to set up tuition installments.
                   ';
           $to = $query[0]['Email'];
           $subject = "Podomoro University Tuition Fee";
           $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text,$path);
   }

   public function get_tagihan_mhs($ta,$prodi,$PTID,$NPM,$limit, $start)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');
    $ta1 = explode('.', $ta);
    $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    $field = 'StatusStudentID';
    $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';
    $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
    if ($prodi == '') {
     $sql = 'select a.*,b.EmailPU,c.Cost from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.tuition_fee as c
             on a.ProdiID = c.ProdiID
             where a.StatusStudentID = ?  and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.ClassOf = ? and c.PTID = ? '.$NPM.'
             order by a.NPM asc
             LIMIT '.$start. ', '.$limit;
      // print_r($sql);       
     $Data_mhs=$this->db->query($sql, array($value,$PTID,$SemesterID[0]['ID'],$ta,$PTID))->result_array();
    }
    else
    {
      $sql = 'select a.*,b.EmailPU,c.Cost from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.tuition_fee as c
              on a.ProdiID = c.ProdiID
              where a.StatusStudentID = ? and a.ProdiID = ? and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.ClassOf = ? and c.PTID = ? '.$NPM.'
              order by a.NPM asc 
              LIMIT '.$start. ', '.$limit;
      $Data_mhs=$this->db->query($sql, array($value,$prodi,$PTID,$SemesterID[0]['ID'],$ta,$PTID))->result_array();
    }

    // get Number VA Mahasiswa
    $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // $SemesterID = $SemesterID[0]['ID'];
    $Discount = $this->m_master->showData_array('db_finance.discount');
    for ($i=0; $i < count($Data_mhs); $i++) { 
      $array = array('SemesterID' => $SemesterID[0]['ID'], 'SemesterName' => $SemesterID[0]['Name']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;
      $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$Data_mhs[$i]['ProdiID']);
      $array = array('ProdiEng' => $ProdiEng[0]['NameEng']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;

      // get IPS Mahasiswa
        $IPS = $this->getIPSMahasiswa($db2,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPS' => $IPS);

      // get IPS Mahasiswa
        $IPK = $this->getIPKMahasiswa($db2,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPK' => $IPK);

      // get VA Mahasiwa
        $VA = $Const_VA[0]['Const_VA'].$Data_mhs[$i]['NPM'];
        $Data_mhs[$i] = $Data_mhs[$i] + array('VA' => $VA);

      // get sks yang diambil
         $Credit = $this->getSKSMahasiswa($db2,$Data_mhs[$i]['NPM']);
         $Data_mhs[$i] = $Data_mhs[$i] + array('Credit' => $Credit);

    }
    $arr['Data_mhs'] = $Data_mhs;
    $arr['Discount'] = $Discount;
    return $arr;
   }

   public function getSKSMahasiswa($db,$NPM)
   {
     // get semester desc
        $sql = 'select ID from db_academic.semester where Status = 0 order by ID desc Limit 1';
        $query = $this->db->query($sql, array())->result_array();
        $SemesterID = $query[0]['ID'];

      $sql = 'select * from '.$db.'.study_planning where NPM = ? and SemesterID = ?';
      $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();

      $Credit = 0;
      for ($j=0; $j < count($query); $j++) { 
       $CreditSub = $query[$j]['Credit'];
       $Credit = $Credit + $CreditSub;
      }

      return $Credit;

   }

  public function getIPKMahasiswa($db,$NPM)
  {
    error_reporting(0);
    $IPK = 0;
    // hitung IPK
      // get query IPK
        $sql = 'select * from '.$db.'.study_planning where NPM = ?';
        $query = $this->db->query($sql, array($NPM))->result_array();

      // proses perhitungan IPK
        $GradeValueCredit = 0;
        $Credit = 0;
        for ($j=0; $j < count($query); $j++) { 
         $GradeValue = $query[$j]['GradeValue'];
         $CreditSub = $query[$j]['Credit'];
         $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
         $Credit = $Credit + $CreditSub;
        }

      $IPK = $GradeValueCredit / $Credit;
      return $IPK;  
  }

   public function getIPSMahasiswa($db,$NPM)
   {
    error_reporting(0);
    $IPS = 0;
    // hitung IPS
      // get semester desc
        $sql = 'select ID from db_academic.semester where Status = 0 order by ID desc Limit 1';
        $query = $this->db->query($sql, array())->result_array();
        $SemesterID = $query[0]['ID'];

      // get query IPS
        $sql = 'select * from '.$db.'.study_planning where NPM = ? and SemesterID = ? ';

        // print_r($sql);
        $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();
        if (count($query) == 0) {
          $IPS = 0;
          return $IPS;
        }

      // proses perhitungan IPS
        $GradeValueCredit = 0;
        $Credit = 0;
        for ($j=0; $j < count($query); $j++) { 
         $GradeValue = $query[$j]['GradeValue'];
         $CreditSub = $query[$j]['Credit'];
         $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
         $Credit = $Credit + $CreditSub;
        }

      $IPS = $GradeValueCredit / $Credit;
      return $IPS;  
   }

   public function getDeadlineTagihanDB($field,$SemesterID)
   {
    $sql = 'select '.$field.' from db_academic.academic_years where SemesterID = ?';
    $query=$this->db->query($sql, array($SemesterID))->result_array();
    return $query[0][$field];
   }

   public function getVANumberMHS($NPM)
   {
    $this->load->model('master/m_master');
    $a = $this->m_master->showData_array('db_va.master_va');
    $Const_VA = $a[0]['Const_VA'].$NPM;
    return $Const_VA;
   }

                  
   public function insertaDataPayment($PTID,$SemesterID,$NPM,$Invoice,$Discount,$Status = "0",$UpdatedBy = null)
   {
    $dataSave = array(
        'PTID' => $PTID,
        'SemesterID' => $SemesterID,
        'NPM' => $NPM,
        'Invoice' => $Invoice,
        'Discount' => $Discount,
        'Status' => $Status,
        'UpdatedBy' => $UpdatedBy
    );
      $this->db->insert('db_finance.payment', $dataSave);
      $insertId = $this->db->insert_id();
      return  $insertId;
   }

   public function insertaDataPaymentStudents($ID_payment,$Invoice,$BilingID,$Deadline,$Status = 0)
   {
    $dataSave = array(
        'ID_payment' => $ID_payment,
        'Invoice' => $Invoice,
        'BilingID' => $BilingID,
        'Deadline' => $Deadline,
        'Status' => $Status
    );
      $this->db->insert('db_finance.payment_students', $dataSave);
   }

   public function get_created_tagihan_mhs($ta,$prodi,$PTID,$NIM,$limit, $start)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');

    // join dengan table auth terlebih dahulu
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
    $SemesterID = $SemesterID[0]['ID'];
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    if ($ta1 == '') {
      $sql = 'select a.*, b.Year,b.EmailPU,c.Name as NameSemester, d.Description 
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM 
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.' and c.ID = ? order by a.Status asc LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array($SemesterID))->result_array();
      
    }
    else
    {
      $sql = 'select a.*, b.Year,b.EmailPU,c.Name as NameSemester, d.Description 
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM 
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.' and b.Year = ? and c.ID = ? order by a.Status asc LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array($ta1,$SemesterID))->result_array();
    }

    // get Number VA Mahasiswa
        $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // get all data to join db ta
    for ($i=0; $i < count($query); $i++) { 
      $Year = $query[$i]['Year'];
      $db = 'ta_'.$Year.'.students';
      $dt = $this->m_master->caribasedprimary($db,'NPM',$query[$i]['NPM']);
      // get IPS Mahasiswa
         $IPS = $this->getIPSMahasiswa('ta_'.$Year,$query[$i]['NPM']);

      // get IPS Mahasiswa
         $IPK = $this->getIPKMahasiswa('ta_'.$Year,$query[$i]['NPM']);

      // ge VA Mahasiwa
         $VA = $Const_VA[0]['Const_VA'].$query[$i]['NPM'];

      // get sks yang diambil
         $Credit = $this->getSKSMahasiswa('ta_'.$Year,$query[$i]['NPM']);

      if($prodi == '' || $prodi == Null){
        $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);

        $arr[] = array(
            'PaymentID' => $query[$i]['ID'],
            'PTID'  => $query[$i]['PTID'],
            'PTIDDesc' => $query[$i]['Description'],
            'SemesterID' => $query[$i]['SemesterID'],
            'SemesterName' => $query[$i]['NameSemester'],
            'NPM' => $query[$i]['NPM'],
            'Nama' => $dt[0]['Name'],
            'EmailPU' => $query[$i]['EmailPU'],
            'InvoicePayment' => $query[$i]['Invoice'],
            'Discount' => $query[$i]['Discount'],
            'StatusPayment' => $query[$i]['Status'],
            'ProdiID' => $dt[0]['ProdiID'],
            'ProdiEng' => $ProdiEng[0]['NameEng'],
            'Year' => $Year,
            'IPS' => $IPS,
            'IPK' => $IPK,
            'DetailPayment' => $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$query[$i]['ID']),
            'VA' => $VA,
            'Credit' => $Credit,
        );
      }
      else
      {
        $prodi = explode('.', $prodi);
        $prodi = $prodi[0];
        if ($prodi == $dt[0]['ProdiID']) {
          $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
          $arr[] = array(
              'PaymentID' => $query[$i]['ID'],
              'PTID'  => $query[$i]['PTID'],
              'PTIDDesc' => $query[$i]['Description'],
              'SemesterID' => $query[$i]['SemesterID'],
              'SemesterName' => $query[$i]['NameSemester'],
              'NPM' => $query[$i]['NPM'],
              'Nama' => $dt[0]['Name'],
              'EmailPU' => $query[$i]['EmailPU'],
              'InvoicePayment' => $query[$i]['Invoice'],
              'Discount' => $query[$i]['Discount'],
              'StatusPayment' => $query[$i]['Status'],
              'ProdiID' => $dt[0]['ProdiID'],
              'ProdiEng' => $ProdiEng[0]['NameEng'],
              'Year' => $Year,
              'DetailPayment' => $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$query[$i]['ID']),
              'VA' => $VA,
              'Credit' => $Credit,
          );
        }
      }
      
    }
    return $arr;
   }

   public function updatePaymentApprove($Input)
   {
    for ($i=0; $i < count($Input); $i++) { 
      $dataSave = array(
              'Status' =>"1",
              'UpdateAt' => date('Y-m-d H:i:s'),
              'UpdatedBy' => $this->session->userdata('NIP')
                      );
      $this->db->where('ID',$Input[$i]->PaymentID);
      $this->db->update('db_finance.payment', $dataSave);
    }
   }

   public function updatePaymentunApprove($Input)
   {
    $msg = '';
    for ($i=0; $i < count($Input); $i++) {
      // check Mahasiswa telah melakukan transaksi atau belum
       $NPM = $Input[$i]->NPM;
       $SemesterID = $Input[$i]->semester;
       $sql = 'select count(*) as total from db_academic.std_krs where SemesterID = ? and NPM = ?';
       $query=$this->db->query($sql, array($SemesterID,$NPM))->result_array();
       $count = $query[0]['total'];
       if ($count == 0) {
         $dataSave = array(
                 'Status' =>"0",
                 'UpdateAt' => null,
                 'UpdatedBy' => null
                         );
         $this->db->where('ID',$Input[$i]->PaymentID);
         $this->db->update('db_finance.payment', $dataSave);
       }
       else
       {
        if ($msg == '') {
          $msg = '<ul>';
        }
        $msg .= '<li>Proses UnApprove ditolak, Mohon cek Transaksi KRS pada Mahasiswa dengan NPM : '.$Input[$i]->NPM.'</li>';
       }
    }

    return $msg;
   }

   public function update_payment_MHS($BilingID,$ID_payment)
   {
    // update payment_students
        $dataSave = array(
                'Status' =>1,
                'UpdateAt' => date('Y-m-d H:i:s'),
                        );
        $this->db->where('BilingID',$BilingID);
        $this->db->update('db_finance.payment_students', $dataSave);

      $getData3 = $this->findDatapayment_studentsBaseID_payment($ID_payment);
      if (count($getData3) == 0) {
        $sql = 'select count(*) as total from db_finance.payment where Status = 0 and ID = ?';
        $query=$this->db->query($sql, array($ID_payment))->result_array();
        if ($query[0]['total'] == 0) {
            $dataSave = array(
                       'Status' =>"1",
                       'UpdateAt' => date('Y-m-d H:i:s'),
                       'UpdatedBy' => "0"
                               );
               $this->db->where('ID',$ID_payment);
               $this->db->update('db_finance.payment', $dataSave);
        }
      }
        
   }

   public function delete_id_table($ID,$table)
   {
       $sql = "delete from db_finance.".$table." where ID = ".$ID;
       $query=$this->db->query($sql, array());
   }

   public function inserData_master_tagihan_mhs($TypePembayaran,$Prodi,$Cost,$ClassOf)
   {
    $dataSave = array(
        'PTID' => $TypePembayaran,
        'ProdiID' => $Prodi,
        'ClassOf' => $ClassOf,
        'Cost' => $Cost,
    );
      $this->db->insert('db_finance.tuition_fee', $dataSave);
   }

   public function editData_master_tagihan_mhs($TypePembayaran,$Prodi,$Cost,$ClassOf,$ID)
   {
      $dataSave = array(
          'PTID' => $TypePembayaran,
          'ProdiID' => $Prodi,
          'ClassOf' => $ClassOf,
          'Cost' => $Cost,
      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.tuition_fee', $dataSave);
   }

   public function updateTagihanMhsList($input)
   {
    for ($i=0; $i < count($input); $i++) { 
      $ID = $input[$i]->id;
      $Cost = $input[$i]->Cost;
      $dataSave = array(
          'Cost' => $Cost,
      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.tuition_fee', $dataSave);
    }
   }

   public function deleteTagihanMHSByProdiYear($input)
   {
    $ProdiID = $input['ProdiID'];
    $ClassOf = $input['ClassOf'];
    $sql = "delete from db_finance.tuition_fee where ProdiID = ".$ProdiID.' and ClassOf = "'.$ClassOf.'"';
    $query=$this->db->query($sql, array());
   }

   public function cancel_created_tagihan_mhs($input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg'] = '';
    $now = date('Y-m-d H:i:s');
    for ($i=0; $i < count($input); $i++) { 
      $PTID = $input[$i]->PTID;
      $SemesterID = $input[$i]->semester;
      $NPM = $input[$i]->NPM;
      // Closed VA dahulu
          // check Status VA
              // cari Biling ID
                $sql = 'select * from db_finance.payment as a join db_finance.payment_students as b
                        on a.ID = b.ID_payment where a.NPM = ? and a.SemesterID = ? and a.PTID = ? and b.Status  = 0 order by b.ID asc limit 1';
                $query=$this->db->query($sql, array($NPM,$SemesterID,$PTID))->result_array();
                if (count($query) > 0 ) {
                  $BilingID = $query[0]['BilingID'];
                  $checkVa = $this->checkBiling($BilingID);
                  // print_r($checkVa);
                  // die();
                  // va status  = 1 => active
                  // va status = 2 => Inactive
                  if ($checkVa['msg']['va_status'] != 2) {
                      // cancel VA 
                     $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
                     $trx_amount = $getData[0]['trx_amount'];
                     $datetime_expired = $now;
                     $customer_name = $getData[0]['customer_name'];
                     $customer_email = $getData[0]['customer_email'];
                     $update = $this->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students');
                     if ($update['status'] == 1) {
                       // triger VA closed berhasil, update va_log status = 2 // auto dari update_va_Payment
                       // delete data pada table payment dan payment_students
                          // action delete belum benar
                       $this->delete_id_table($query[0]['ID_payment'],'payment_students');
                       $this->delete_id_table($query[0]['ID_payment'],'payment');
                       
                     }
                     else
                     {
                       $arr['msg'] .= 'Va tidak bisa di cancel, error koneksi ke BNI <br>';
                     }
                  }
                  else
                  {
                       $this->delete_id_table($query[0]['ID_payment'],'payment_students');  
                       $this->delete_id_table($query[0]['ID_payment'],'payment');
                      
                  }
                }
        }
        return $arr;
   }

   public function findDatapayment_studentsBaseID_payment($ID_payment,$Status = 0)
   {
    $sql = 'select * from db_finance.payment_students where ID_payment = ? and Status = ? order by ID asc';
    $query=$this->db->query($sql, array($ID_payment,$Status))->result_array();
    return $query;
   }

   public function updateCicilanMHS($BilingID,$trx_amount,$datetime_expired)
   {
    $dataSave = array(
            'Invoice' => $trx_amount,
            'Deadline' => $datetime_expired,
            'UpdateAt' => date('Y-m-d H:i:s'),
                    );
    $this->db->where('BilingID',$BilingID);
    $this->db->update('db_finance.payment_students', $dataSave);
   }

   public function updatePaymentStudentsFromCicilan($BilingID,$ID)
   {
    $dataSave = array(
            'BilingID' => $BilingID,
                    );
    $this->db->where('ID',$ID);
    $this->db->update('db_finance.payment_students', $dataSave);
   }

   public function edit_cicilan_tagihan_mhs_submit($Input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg']  = '';
    for ($i=0; $i < count($Input); $i++) { 
      // check yang memiliki bilingId
      // jika memiliki bilingID maka update VA, jika tidak maka update database aja
      if ($Input[$i]->BilingID != 0) {
        // update VA
        $BilingID = $Input[$i]->BilingID;
        $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $customer_name = $getData[0]['customer_name'];
        $customer_email = $getData[0]['customer_email'];
        $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students');
        if ($update['status'] == 1) {
          // update data pada table db_finance.payment_students
            $this->m_finance->updateCicilanMHS($BilingID,$trx_amount,$datetime_expired);
        }
        else
        {
          $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI with Name : '.$customer_name.'<br>';
        }
      }
      else
      {
        $BilingID = $Input[$i]->BilingID;
        $ID = $Input[$i]->ID;
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $this->m_finance->UpdateCicilanbyID($ID,$BilingID,$trx_amount,$datetime_expired);
      }
    }

    return $arr;

   }

   public function UpdateCicilanbyID($ID,$BilingID,$trx_amount,$datetime_expired)
   {
    $dataSave = array(
            'Invoice' => $trx_amount,
            'Deadline' => $datetime_expired,
            'UpdateAt' => date('Y-m-d H:i:s'),
            'BilingID' => $BilingID,
                    );
    $this->db->where('ID',$ID);
    $this->db->update('db_finance.payment_students', $dataSave);
   }

   public function delete_cicilan_tagihan_mhs_submit($Input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg']  = '';
    $ID_payment = '';
    for ($i=0; $i < count($Input); $i++) { 
      // check yang memiliki bilingId
      // jika memiliki bilingID maka update VA, jika tidak maka update database aja
      if ($Input[$i]->BilingID != 0) {
        $BilingID = $Input[$i]->BilingID;
        $getData0= $this->m_master->caribasedprimary('db_finance.payment_students','BilingID',$BilingID);
        $ID_payment = $getData0[0]['ID_payment'];
        $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = date('Y-m-d H:i:s');
        $customer_name = $getData[0]['customer_name'];
        $customer_email = $getData[0]['customer_email'];
        $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students');
        if ($update['status'] == 1) {
          // delete data pada table db_finance.payment_students
            $ID = $Input[$i]->ID;
            $this->m_finance->delete_id_table($ID,'payment_students');
        }
        else
        {
          $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI with Name : '.$customer_name.'<br>';
        }
      }
      else
      {
        $BilingID = $Input[$i]->BilingID;
        $ID = $Input[$i]->ID;
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $this->m_finance->delete_id_table($ID,'payment_students');
      }
    }
    $this->m_finance->delete_id_table($ID_payment,'payment');
    return $arr;
   }

   public function get_pembayaran_mhs($ta,$prodi,$PTID,$NIM,$limit, $start)
   {
    error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');

    // join dengan table auth terlebih dahulu
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    /*$SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
    $SemesterID = $SemesterID[0]['ID'];*/
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    if ($ta1 == '') {
      $sql = 'select a.*, b.Year,b.EmailPU,c.Name as NameSemester, d.Description,e.ID as ID_payment_students,e.BilingID,e.Invoice as InvoiceStudents
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM 
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e on a.ID = e.ID_payment '.$NIM.$PTID.' 
              and e.Status = 1 order by e.ID asc LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array())->result_array();
      
    }
    else
    {
      $sql = 'select a.*, b.Year,b.EmailPU,c.Name as NameSemester, d.Description,e.ID as ID_payment_students,e.BilingID,e.Invoice as InvoiceStudents
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM 
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e on a.ID = e.ID_payment '.$NIM.$PTID.' and b.Year = ? and e.Status = 1 
              order by e.ID asc LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array($ta1))->result_array();
    }

    for ($i=0; $i < count($query); $i++) { 
      $Year = $query[$i]['Year'];
      $db = 'ta_'.$Year.'.students';
      $dt = $this->m_master->caribasedprimary($db,'NPM',$query[$i]['NPM']);
      if($prodi == '' || $prodi == Null){
        $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);

        // get IPS Mahasiswa
          $IPS = $this->getIPSMahasiswa('ta_'.$Year,$query[$i]['NPM']);

        // get IPS Mahasiswa
          $IPK = $this->getIPKMahasiswa('ta_'.$Year,$query[$i]['NPM']);

        // cek cicilan atau tidak
          $DetailPayment = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$query[$i]['ID']);
          if (count($DetailPayment) == 1) {
            $Cicilan = 'Tidak Cicilan';
          }
          else
          {
            $a = 1;
            for ($j=0; $j < count($DetailPayment); $j++) { 
              if ($DetailPayment[$j]['ID'] == $query[$i]['ID_payment_students']) {
                // get all data to join db ta
                $Cicilan = 'Cicilan ke ';
                // print_r($sql);
                  $Cicilan .= $a;
                  break;
              }  
              $a++;
            }
          }
          

        $arr[] = array(
            'PaymentID' => $query[$i]['ID'],
            'PTID'  => $query[$i]['PTID'],
            'PTIDDesc' => $query[$i]['Description'],
            'SemesterID' => $query[$i]['SemesterID'],
            'SemesterName' => $query[$i]['NameSemester'],
            'NPM' => $query[$i]['NPM'],
            'Nama' => $dt[0]['Name'],
            'EmailPU' => $query[$i]['EmailPU'],
            'InvoicePayment' => $query[$i]['Invoice'],
            'Discount' => $query[$i]['Discount'],
            'StatusPayment' => $query[$i]['Status'],
            'ProdiID' => $dt[0]['ProdiID'],
            'ProdiEng' => $ProdiEng[0]['NameEng'],
            'Year' => $Year,
            'IPS' => $IPS,
            'IPK' => $IPK,
            'Cicilan' => $Cicilan,
            'BilingID' => $query[$i]['BilingID'],
            'InvoiceStudents' => $query[$i]['InvoiceStudents'],
            'ID_payment_students' => $query[$i]['ID_payment_students'],
        );
      }
      else
      {
        $prodi = explode('.', $prodi);
        $prodi = $prodi[0];
        if ($prodi == $dt[0]['ProdiID']) {
          $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
          $arr[] = array(
              'PaymentID' => $query[$i]['ID'],
              'PTID'  => $query[$i]['PTID'],
              'PTIDDesc' => $query[$i]['Description'],
              'SemesterID' => $query[$i]['SemesterID'],
              'SemesterName' => $query[$i]['NameSemester'],
              'NPM' => $query[$i]['NPM'],
              'Nama' => $dt[0]['Name'],
              'EmailPU' => $query[$i]['EmailPU'],
              'InvoicePayment' => $query[$i]['Invoice'],
              'Discount' => $query[$i]['Discount'],
              'StatusPayment' => $query[$i]['Status'],
              'ProdiID' => $dt[0]['ProdiID'],
              'ProdiEng' => $ProdiEng[0]['NameEng'],
              'Year' => $Year,
              'Cicilan' => $Cicilan,
              'BilingID' => $query[$i]['BilingID'],
              'InvoiceStudents' => $query[$i]['InvoiceStudents'],
              'ID_payment_students' => $query[$i]['ID_payment_students'],
          );
        }
      }
      
    }
    return $arr;
   }

   public function checkMasterTagihanExisting($TypePembayaran,$Prodi,$ClassOf)
   {
    $sql= 'select * from db_finance.tuition_fee where PTID = ? and ProdiID = ? and ClassOf = ?';
    $query=$this->db->query($sql, array($TypePembayaran,$Prodi,$ClassOf))->result_array();
    if (count($query) > 0) {
      // existing
      return false;
    }
    else
    {
      // nothing
      return true;
    }
   }

   public function cari_va($VA)
   {
    $rs = array('msg' => '');
    $sql = 'select * from db_va.va_log where virtual_account = ? order by ID desc limit 1';
    $query=$this->db->query($sql, array($VA))->result_array();
    if (count($query) > 0) {
      $sql1 = 'select * from db_va.va_log where virtual_account = ? and Status != 1 order by ID desc limit 1';
      $query1=$this->db->query($sql1, array($VA))->result_array();
      if (count($query1) > 0) {
        // check datetime expired sudah melewati waktu atau belum
        $datetime_expired = $query1[0]['datetime_expired'];
        $sql2 = 'select * from (
                  select now() as ac
                )aa
                where ? > ac';
                $query2=$this->db->query($sql2, array($datetime_expired))->result_array();
            if (count($query2) > 0) {
                  $rs['data'] = $query1;
            }
            else{
              $rs['msg'] = 'VA dengan number '.$VA.' Inactive';
            } 
      }
    }
    else
    {
      $rs['msg'] = 'VA dengan number '.$VA.' belum pernah digunakan';
    }

    return $rs;

   }

}
