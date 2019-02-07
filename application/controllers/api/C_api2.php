<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once APPPATH.'vendor/autoload.php';
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;

class C_api2 extends CI_Controller {

    var $DummyEmail = 'nandang.mulyadi@podomorouniversity.ac.id';

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('master/m_master');
        $this->load->model('hr/m_hr');
        $this->load->model('vreservation/m_reservation');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('notification/m_log');
        $this->load->library('JWT');
        $this->load->library('google');

    }

    private function dateTimeNow(){
        $dataTime = date('Y-m-d H:i:s');
        return $dataTime;
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    private function getInputToken2($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    public function is_url_exist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    private function getInputTokenGet($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    private function sendMailRest($data){
        $url = ($_SERVER['SERVER_NAME']=='localhost')
            ? 'http://pcam.podomorouniversity.ac.id/rest/__sendEmail'
            : base_url('rest/__sendEmail');
        $Input = $this->jwt->encode($data,"UAP)(*");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$Input);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $pr = curl_exec($ch);
        curl_close ($ch);
    }


    public function crudScheduleExchage(){
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if($data_arr['action']=='approveExchange'){

                $dataSceduleExchange = $this->db->query('SELECT exc.*,em.Name AS Kaprodi, em2.Name AS Adum FROM db_academic.schedule_exchange exc
                                                                    LEFT JOIN db_employees.employees em ON (em.NIP = exc.Updated1By)
                                                                    LEFT JOIN db_employees.employees em2 ON (em2.NIP = exc.Updated2By)
                                                                    WHERE exc.ID = "'.$data_arr['EXID'].'" LIMIT 1 ')
                                        ->result_array();

                // Cek apakah sudah update atau belum jika sudah maka
                // 1. tidak perlu update lagi
                // 2. tidak perlu kirim email ke adum

                if(count($dataSceduleExchange)>0){

                    $d = $dataSceduleExchange[0];
                    if($d['Status']=='0' || $d['Status']==0){

                        $key = 's3Cr3T-G4N';
                        $token = $dataSceduleExchange[0]['Token'];
                        $dataToken = (array) $this->jwt->decode($token,$key);

                        // Get Name Kaprodi
                        $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                            array('NIP' => $data_arr['ApprovedBy']))->result_array();

                        $bodyEmail = '<div>
                                            Dear <span style="color: #333;">General Affair</span>,
                                            <br/>
                                            Perihal : <b>Permohonan Ruangan Untuk Kuliah Pengganti</b>
                        
                                            <br/>
                                            <br/>
                                            <div style="font-size: 14px;">
                                                <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="width: 20%;">Dosen</td>
                                                        <td style="width: 2%;">:</td>
                                                        <td style="width: 40%;">'.$dataToken['Lecturer'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Program Studi</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Prodi'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk kuliah pengganti</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mata Kuliah</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Course'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Group Kelas</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ClassGroup'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sesi (Pertemuan ke)</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Meeting'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jadwal Semula</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ScheduleExist'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Diganti Pada</td>
                                                        <td>:</td>
                                                        <td style="color: green;font-weight: bolder;">'.$dataToken['ScheduleExchange'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alasan</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Reason'].'</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <br/>
                                                <p>
                                                    Demikian permohonan ini kami ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                                                </p>
                                                <br/>
                                                <br/>
                                                <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                                                    <tr>
                                                        <td style="width: 100%;" align="center">
                                                            Approved By
                                                            <br/>
                                                            <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                                                <br/>
                                                            <small>'.$data_arr['ApprovedBy'].'</small>
                                                            </h3>
                                                        </td>
                        
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>';

                        $emailGa = $this->db->select('Email')->get_where('db_employees.division',array('ID' => 8))->result_array();

                        $mailTo = (count($emailGa)>0)
                            ? $emailGa[0]['Email']
                            : $this->DummyEmail;


                        $data = array(
                            'to' => $mailTo,
                            'subject' => 'Kaprodi : Permohonan Ruangan Kelas Pengganti',
                            'text' => $bodyEmail,
                            'auth' => 's3Cr3T-G4N'
                        );

                        $this->sendMailRest($data);

                        // Update Status
                        $dataUpdate = array(
                            'Updated1At' => $data_arr['ApprovedAt'],
                            'Updated1By' => $data_arr['ApprovedBy'],
                            'Status' => '1',
                        );
                        $this->db->where('ID', $data_arr['EXID']);
                        $this->db->update('db_academic.schedule_exchange',$dataUpdate);


                        //============= Logging ==========
                        // Insert Logging
                        $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                        $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                            ? $url
                            : url_server_ws.'/images/icon/lecturer.png';

                        $Log_dataInsert = array(
                            'Icon' => $img_profile,
                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Schedule Exchange Approved',
                            'Description' => 'Kaprodi : Schedule Exchange Approved',
                            'URLDirect' => 'ga_schedule_exchange',
                            'URLDirectLecturer' => 'attendance/schedule-exchange',
                            'CreatedBy' => $data_arr['ApprovedBy'],
                            'CreatedName' => $dataKaprodi[0]['Name'],
                            'CreatedAt' => $data_arr['ApprovedAt'],
                        );

                        $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                        $insert_id_logging = $this->db->insert_id();

                        // insert ke user
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id_logging,
                            'UserID' => $d['NIP']
                        );
                        $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                        // Get Member Adum
                        $dataUserAdum = $this->db->select('NIP')->get_where('db_employees.rule_users',
                            array('IDDivision' => 8))->result_array();

                        if(count($dataUserAdum)>0){
                            foreach ($dataUserAdum as $item){
                                $Log_arr_ins = array(
                                    'IDLogging' => $insert_id_logging,
                                    'UserID' => $item['NIP']
                                );
                                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                            }
                        }



                        $result = array(
                            'Status' => '1',
                            'Message' => 'Approved',
                            'By' => $dataKaprodi[0]['Name']
                        );

                    }
                    else if($d['Status']=='1' || $d['Status']==1 || $d['Status']=='2' || $d['Status']==2) {
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Approved',
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-1' || $d['Status']==-1){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-2' || $d['Status']==-2){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Adum']
                        );
                    }



                } else {
                    $result = array(
                      'Message' => 'Data Not Yet'
                    );
                }

                return print_r(json_encode($result));


            }
            else if($data_arr['action']=='rejectedExchange'){

                $dataSceduleExchange = $this->db->query('SELECT exc.*,em.Name AS Lecturer, em.EmailPU FROM db_academic.schedule_exchange exc
                                                                    LEFT JOIN db_employees.employees em ON (em.NIP = exc.NIP)
                                                                    WHERE exc.ID = "'.$data_arr['EXID'].'" LIMIT 1 ')
                    ->result_array();

                if(count($dataSceduleExchange)>0){

                    $key = 's3Cr3T-G4N';
                    $token = $dataSceduleExchange[0]['Token'];
                    $dataToken = (array) $this->jwt->decode($token,$key);

                    // Get Name Kaprodi
                    $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                        array('NIP' => $data_arr['ApprovedBy']))->result_array();

                    $bodyEmail = '<div>
                                            Dear <span style="color: #333;">'.$dataSceduleExchange[0]['Lecturer'].'</span>,
                                            <br/>
                                            Perihal : <b>Permohonan Ruangan Untuk Kuliah Pengganti</b>
                        
                                            <br/>
                                            <br/>
                                            
                                            <div style="background: lightyellow;color: red;border: 1px solid red; text-align: center;padding: 7px;margin-bottom: 10px;">
                                                <h2 style="margin-top: 7px;margin-bottom: 0px;">Permohonan Ditolak</h2>
                                                <p style="color: blue;margin-top: 3px;">'.$data_arr['Comment'].'</p>
                                            </div>
                                            
                                            <div style="text-align: center;">
                                                <p>--- Detail permohonan ---</p>
                                            </div>
                                            
                                            <div style="font-size: 14px;">
                                                <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="width: 20%;">Dosen</td>
                                                        <td style="width: 2%;">:</td>
                                                        <td style="width: 40%;">'.$dataToken['Lecturer'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Program Studi</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Prodi'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk kuliah pengganti</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mata Kuliah</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Course'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Group Kelas</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ClassGroup'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sesi (Pertemuan ke)</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Meeting'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jadwal Semula</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ScheduleExist'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Diganti Pada</td>
                                                        <td>:</td>
                                                        <td style="color: green;font-weight: bolder;">'.$dataToken['ScheduleExchange'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alasan</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Reason'].'</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <br/>
                                                <p>
                                                    Demikian permohonan ini kami ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                                                </p>
                                                <br/>
                                                <br/>
                                                <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                                                    <tr>
                                                        <td style="width: 100%;" align="center">
                                                            Rejected By
                                                            <br/>
                                                            <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                                                <br/>
                                                            <small>'.$data_arr['ApprovedBy'].'</small>
                                                            </h3>
                                                        </td>
                        
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>';

                    $mailTo = (count($dataSceduleExchange)>0)
                        ? $dataSceduleExchange[0]['EmailPU']
                        : $this->DummyEmail;

                    $data = array(
                        'to' => $mailTo,
                        'subject' => 'Kaprodi : Permohonan Ruangan Kelas Pengganti',
                        'text' => $bodyEmail,
                        'auth' => 's3Cr3T-G4N'
                    );

                    $this->sendMailRest($data);


                    $dataUpdate =  array(
                        'Updated1At' => $data_arr['ApprovedAt'],
                        'Updated1By' => $data_arr['ApprovedBy'],
                        'Comment' => $data_arr['Comment'],
                        'Status' => '-1',
                    );
                    $this->db->where('ID', $data_arr['EXID']);
                    $this->db->update('db_academic.schedule_exchange',$dataUpdate);


                    //============= Logging ==========
                    // Insert Logging

                    $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                    $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                        ? $url
                        : url_server_ws.'/images/icon/lecturer.png';

                    $Log_dataInsert = array(
                        'Icon' => $img_profile,
                        'Title' => '<i class="fa fa-times-circle margin-right" style="color:darkred;"></i> Schedule Exchange Rejected',
                        'Description' => 'Kaprodi : Schedule Exchange Rejected',
                        'URLDirect' => 'ga_schedule_exchange',
                        'URLDirectLecturer' => 'attendance/schedule-exchange',
                        'CreatedBy' => $data_arr['ApprovedBy'],
                        'CreatedName' => $dataKaprodi[0]['Name'],
                        'CreatedAt' => $data_arr['ApprovedAt'],
                    );


                    $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                    $insert_id_logging = $this->db->insert_id();

                    // insert ke user
                    $Log_arr_ins = array(
                        'IDLogging' => $insert_id_logging,
                        'UserID' => $dataSceduleExchange[0]['NIP']
                    );
                    $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                }



                return print_r(1);

            }
            else if($data_arr['action']=='readStatusExchange'){
                // Cek apakah sudah di approve atau belum
                $dataSceduleExchange = $this->db->query('SELECT exc.*,em.Name AS Kaprodi, em2.Name AS Adum FROM db_academic.schedule_exchange exc
                                                                    LEFT JOIN db_employees.employees em ON (em.NIP = exc.Updated1By)
                                                                    LEFT JOIN db_employees.employees em2 ON (em2.NIP = exc.Updated2By)
                                                                    WHERE exc.ID = "'.$data_arr['EXID'].'" LIMIT 1 ')
                    ->result_array();

                if(count($dataSceduleExchange)>0){
                    $d = $dataSceduleExchange[0];
                    if($d['Status']=='0' || $d['Status']==0){

                        $result = array(
                            'Status' => $d['Status']
                        );

                    }
                    else if($d['Status']=='1' || $d['Status']==1 || $d['Status']=='2' || $d['Status']==2){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Approved',
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-1' || $d['Status']==-1){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-2' || $d['Status']==-2){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Adum']
                        );
                    }
                }
                else {
                    $result = array(
                        'Message' => 'Data Not Yet'
                    );
                }

                return print_r(json_encode($result));
            }


        }

    }

    public function crudModifyAttendance(){
        $data_arr = $this->getInputToken();
        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'checkStatusModifyAttd') {

                $IDAM = $data_arr['IDAM'];

                $data = $this->db->query('SELECT am.Status, em.Name AS Lecturer, am.Reason_Reject AS Reason FROM db_academic.attendance_modify am 
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = am.Updated1By)
                                                    WHERE am.ID = "'.$IDAM.'" ')->result_array();


                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='rejectedModifyAttd'){

                $dataRequsted = $this->db->query('SELECT am.RequestBy, em.EmailPU, em.Name AS Lecturer, em.Gender, am.DataEmail, am.TokenURL FROM db_academic.attendance_modify am 
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = am.RequestBy)
                                                            WHERE am.ID = "'.$data_arr['IDAM'].'" ')->result_array();


                $dataUpdate = array(
                    'Reason_Reject' => $data_arr['Reason'],
                    'Updated1By' => $data_arr['Updated1By'],
                    'Updated1At' => $data_arr['Updated1At'],
                    'Status' => '-1'
                );
                $this->db->where('ID', $data_arr['IDAM']);
                $this->db->update('db_academic.attendance_modify',$dataUpdate);


                $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                    array('NIP' => $data_arr['Updated1By']))->result_array();

                if(count($dataKaprodi)>0){

                    $DataEmail = $this->getInputToken2($dataRequsted[0]['DataEmail']);

                    //============= Logging ==========
                    // Insert Logging
                    $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                    $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                        ? $url
                        : url_server_ws.'/images/icon/lecturer.png';

                    $Log_dataInsert = array(
                        'Icon' => $img_profile,
                        'Title' => '<i class="fa fa-times-circle margin-right" style="color:darkred;"></i> Modify Attendance Rejected',
                        'Description' => $DataEmail['Code'].' - '.$DataEmail['CourseEng'].' | Group : '.$DataEmail['Group'].' | Session : '.$DataEmail['Session'],
                        'URLDirectLecturer' => 'attendance/modify-attendance/'.$dataRequsted[0]['TokenURL'],
                        'CreatedBy' => $data_arr['Updated1By'],
                        'CreatedName' => $dataKaprodi[0]['Name'],
                        'CreatedAt' => $data_arr['Updated1At'],
                    );

                    $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                    $insert_id_logging = $this->db->insert_id();

                    // insert ke user
                    $Log_arr_ins = array(
                        'IDLogging' => $insert_id_logging,
                        'UserID' => $dataRequsted[0]['RequestBy']
                    );
                    $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                    // Send Email
                    $mailTo = (count($dataRequsted)>0)
                        ? $dataRequsted[0]['EmailPU']
                        : $this->DummyEmail;




                    $greating = ($dataRequsted[0]['Gender']=='L')  ? 'Bapak' : 'Ibu';
                    $bodyEmail = '<div>
                    Dear <span style="color: #333;">'.$greating.' '.ucwords($dataRequsted[0]['Lecturer']).'</span>,
                    <br/>
                    Perihal : <b>Perubahan Data Absensi Mahasiswa</b>

                    <br/>
                    <br/>

                    <div style="background: lightyellow;color: red;border: 1px solid red; text-align: center;padding: 7px;margin-bottom: 10px;">
                        <h2 style="margin-top: 7px;margin-bottom: 0px;">Permohonan Ditolak</h2>
                        <p style="color: blue;margin-top: 3px;">
                            '.$data_arr['Reason'].'
                        </p>
                    </div>

                    <div style="text-align: center;">
                        <p>--- Detail permohonan ---</p>
                    </div>

                    <div style="font-size: 14px;">
                        <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                            <tbody>
                            <tr>
                                <td style="width: 20%;">Dosen</td>
                                <td style="width: 2%;">:</td>
                                <td style="width: 40%;">'.ucwords($dataRequsted[0]['Lecturer']).'</td>
                            </tr>
                            <tr>
                                <td>Kode</td>
                                <td>:</td>
                                <td>'.$DataEmail['Code'].'</td>
                            </tr>
                            <tr>
                                <td>Mata Kuliah</td>
                                <td>:</td>
                                <td>'.$DataEmail['CourseEng'].'</td>
                            </tr>
                            <tr>
                                <td>Group Kelas</td>
                                <td>:</td>
                                <td>'.$DataEmail['Group'].'</td>
                            </tr>
                            <tr>
                                <td>Sesi (Pertemuan ke)</td>
                                <td>:</td>
                                <td>'.$DataEmail['Session'].'</td>
                            </tr>
                            <tr>
                                <td>Alasan</td>
                                <td>:</td>
                                <td>'.$DataEmail['Reason'].'</td>
                            </tr>


                            <tr>
                                <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk perubahan daftar hadir mahasiswa</td>
                            </tr>

                            <tr>
                                <td>Sebelumnya</td>
                                <td>:</td>
                                <td>'.$DataEmail['Before'].'</td>
                            </tr>
                            <tr>
                                <td>Menjadi</td>
                                <td>:</td>
                                <td>'.$DataEmail['After'].'</td>
                            </tr>

                            </tbody>
                        </table>
                        <br/>
                        <p>
                            Demikian permohonan ini saya ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                        </p>
                        <br/>
                        <br/>

                        <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                            <tr>
                                <td style="width: 100%;" align="center">
                                    Rejected By
                                    <br/>
                                    <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                        <br/>
                                        <small>'.$data_arr['Updated1By'].'</small>
                                    </h3>
                                </td>

                            </tr>
                        </table>

                    </div>
                </div>';
                    $data = array(
                        'to' => $mailTo,
                        'subject' => 'Kaprodi : Modify Attendance Rejected',
                        'text' => $bodyEmail,
                        'auth' => 's3Cr3T-G4N'
                    );

                    $this->sendMailRest($data);
                }


                return print_r(1);
            }
            else if($data_arr['action']=='approvedModifyAttd'){
                $dataRequsted = $this->db->query('SELECT am.RequestBy, em.EmailPU, em.Name AS Lecturer, em.Gender, am.DataEmail, am.TokenURL FROM db_academic.attendance_modify am 
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = am.RequestBy)
                                                            WHERE am.ID = "'.$data_arr['IDAM'].'" ')->result_array();


                $dataUpdate = array(
                    'Updated1By' => $data_arr['Updated1By'],
                    'Updated1At' => $data_arr['Updated1At'],
                    'Status' => '1'
                );

                $this->db->where('ID', $data_arr['IDAM']);
                $this->db->update('db_academic.attendance_modify',$dataUpdate);
                $this->db->reset_query();

                // Get Detail Modify Atttendance
                $dataStd = $this->db->get_where('db_academic.attendance_modify_details'
                    ,array('IDAM' => $data_arr['IDAM']))->result_array();

                if(count($dataStd)>0){
                    foreach ($dataStd AS $item){

                        $dataUpdate = array(
                            'M'.$item['Sesi'] => ''.$item['Meet'],
                            'D'.$item['Sesi'] => $item['Reason']
                        );
                        $this->db->where('ID', $item['IDAS']);
                        $this->db->update('db_academic.attendance_students',$dataUpdate);
                    }
                }


                $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                    array('NIP' => $data_arr['Updated1By']))->result_array();

                if(count($dataKaprodi)>0){

                    $DataEmail = $this->getInputToken2($dataRequsted[0]['DataEmail']);

                    //============= Logging ==========
                    // Insert Logging
                    $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                    $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                        ? $url
                        : url_server_ws.'/images/icon/lecturer.png';

                    $Log_dataInsert = array(
                        'Icon' => $img_profile,
                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> Modify Attendance Approved',
                        'Description' => $DataEmail['Code'].' - '.$DataEmail['CourseEng'].' | Group : '.$DataEmail['Group'].' | Session : '.$DataEmail['Session'],
                        'URLDirectLecturer' => 'attendance/modify-attendance/'.$dataRequsted[0]['TokenURL'],
                        'CreatedBy' => $data_arr['Updated1By'],
                        'CreatedName' => $dataKaprodi[0]['Name'],
                        'CreatedAt' => $data_arr['Updated1At'],
                    );

                    $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                    $insert_id_logging = $this->db->insert_id();

                    // insert ke user
                    $Log_arr_ins = array(
                        'IDLogging' => $insert_id_logging,
                        'UserID' => $dataRequsted[0]['RequestBy']
                    );
                    $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                    // Send Email
                    $mailTo = (count($dataRequsted)>0)
                        ? $dataRequsted[0]['EmailPU']
                        : $this->DummyEmail;


                    $greating = ($dataRequsted[0]['Gender']=='L')  ? 'Bapak' : 'Ibu';
                    $bodyEmail = '<div>
                    Dear <span style="color: #333;">'.$greating.' '.ucwords($dataRequsted[0]['Lecturer']).'</span>,
                    <br/>
                    Perihal : <b>Perubahan Data Absensi Mahasiswa</b>

                    <br/>
                    <br/>

                    <div style="background: lightyellow;border: 1px solid green;color: green;text-align: center;padding: 7px;margin-bottom: 10px;">
                        <h2 style="margin-top: 7px;margin-bottom: 10px;">Permohonan diterima</h2>
                    </div>

                    <div style="text-align: center;">
                        <p>--- Detail permohonan ---</p>
                    </div>

                    <div style="font-size: 14px;">
                        <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                            <tbody>
                            <tr>
                                <td style="width: 20%;">Dosen</td>
                                <td style="width: 2%;">:</td>
                                <td style="width: 40%;">'.ucwords($dataRequsted[0]['Lecturer']).'</td>
                            </tr>
                            <tr>
                                <td>Kode</td>
                                <td>:</td>
                                <td>'.$DataEmail['Code'].'</td>
                            </tr>
                            <tr>
                                <td>Mata Kuliah</td>
                                <td>:</td>
                                <td>'.$DataEmail['CourseEng'].'</td>
                            </tr>
                            <tr>
                                <td>Group Kelas</td>
                                <td>:</td>
                                <td>'.$DataEmail['Group'].'</td>
                            </tr>
                            <tr>
                                <td>Sesi (Pertemuan ke)</td>
                                <td>:</td>
                                <td>'.$DataEmail['Session'].'</td>
                            </tr>
                            <tr>
                                <td>Alasan</td>
                                <td>:</td>
                                <td>'.$DataEmail['Reason'].'</td>
                            </tr>


                            <tr>
                                <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk perubahan daftar hadir mahasiswa</td>
                            </tr>

                            <tr>
                                <td>Sebelumnya</td>
                                <td>:</td>
                                <td>'.$DataEmail['Before'].'</td>
                            </tr>
                            <tr>
                                <td>Menjadi</td>
                                <td>:</td>
                                <td>'.$DataEmail['After'].'</td>
                            </tr>

                            </tbody>
                        </table>
                        <br/>
                        <p>
                            Demikian permohonan ini saya ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                        </p>
                        <br/>
                        <br/>

                        <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                            <tr>
                                <td style="width: 100%;" align="center">
                                    Approved By
                                    <br/>
                                    <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                        <br/>
                                        <small>'.$data_arr['Updated1By'].'</small>
                                    </h3>
                                </td>

                            </tr>
                        </table>

                    </div>
                </div>';
                    $data = array(
                        'to' => $mailTo,
                        'subject' => 'Kaprodi : Modify Attendance Approved',
                        'text' => $bodyEmail,
                        'auth' => 's3Cr3T-G4N'
                    );

                    $this->sendMailRest($data);
                }


                return print_r(1);
            }
            else if($data_arr['action']=='approveAllModifyAttd'){
                $ProdiID = $data_arr['ProdiID'];
                $SemesterID = $data_arr['SemesterID'];

                $dataAttd = $this->db->query('SELECT am.* 
                                                            FROM db_academic.attendance_modify_prodi amp 
                                                            LEFT JOIN db_academic.attendance_modify am ON (am.ID = amp.IDAM)
                                                            LEFT JOIN db_academic.attendance attd ON (attd.ID = am.ID_Attd)
                                                            WHERE amp.ProdiID = "'.$ProdiID.'" AND attd.SemesterID = "'.$SemesterID.'"
                                                            AND am.Status = "0" ')->result_array();

                if(count($dataAttd)>0){
                    foreach ($dataAttd AS $item){

                        // Update Attendance Student
                        $dataStd = $this->db->get_where('db_academic.attendance_modify_details',array('IDAM' => $item['ID']))->result_array();
                        if(count($dataStd)>0){
                            foreach ($dataStd AS $itemStd){
                                $dataUpdate = array(
                                    'M'.$itemStd['Sesi'] => ''.$itemStd['Meet'],
                                    'D'.$itemStd['Sesi'] => $itemStd['Reason']
                                );
                                $this->db->where('ID', $itemStd['IDAS']);
                                $this->db->update('db_academic.attendance_students',$dataUpdate);
                                $this->db->reset_query();
                            }
                        }

                        // Update Status
                        $this->db->where('ID', $item['ID']);
                        $this->db->update('db_academic.attendance_modify',array(
                            'Status' => '1',
                            'Updated1By' => $data_arr['Updated1By'],
                            'Updated1At' => $data_arr['Updated1At']
                        ));
                        $this->db->reset_query();

                        $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                            array('NIP' => $data_arr['Updated1By']))->result_array();

                        if(count($dataKaprodi)>0){
                            $DataEmail = $this->getInputToken2($item['DataEmail']);

                            //============= Logging ==========
                            // Insert Logging
                            $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                            $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                                ? $url
                                : url_server_ws.'/images/icon/lecturer.png';

                            $Log_dataInsert = array(
                                'Icon' => $img_profile,
                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> Modify Attendance Approved',
                                'Description' => $DataEmail['Code'].' - '.$DataEmail['CourseEng'].' | Group : '.$DataEmail['Group'].' | Session : '.$DataEmail['Session'],
                                'URLDirectLecturer' => 'attendance/modify-attendance/'.$item['TokenURL'],
                                'CreatedBy' => $data_arr['Updated1By'],
                                'CreatedName' => $dataKaprodi[0]['Name'],
                                'CreatedAt' => $data_arr['Updated1At']
                            );
                            $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                            $insert_id_logging = $this->db->insert_id();

                            // insert ke user
                            $Log_arr_ins = array(
                                'IDLogging' => $insert_id_logging,
                                'UserID' => $item['RequestBy']
                            );
                            $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                        }

                    }
                }

                return print_r(1);

            }

        }
    }



}