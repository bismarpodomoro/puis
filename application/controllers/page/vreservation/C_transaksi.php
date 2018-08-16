<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_transaksi extends Vreservation_Controler {

    private $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_sendemail');
        $this->load->model('m_api');
        $this->load->library('JWT');
        // $this->load->model('master/m_master');
        // $this->load->model('vreservation/m_reservation');
    }


    public function booking_create()
    {
        $content = $this->load->view($this->pathView.'transaksi/booking','',true);
        $this->temp($content);
    }

    public function add_save_transaksi()
    {
        $input = $this->getInputToken();
        $uploadFile = $this->uploadFfile(mt_rand());
        $filename = '';
        if (is_array($uploadFile)) {
            $filename = $uploadFile['file_name'];
        }

        // print_r($filename);die();

        $Start = date("Y-m-d H:i:s", strtotime($input['date'].$input['Start']));
        $End = date("Y-m-d H:i:s", strtotime($input['date'].$input['End']));
        $time = $this->m_master->countTimeQuery($End, $Start);
        $time = $time[0]['time'];
        $time = explode(':', $time);
        $time = ($time[0] * 60) + $time[1];
        $Colspan = $time / 30;
        $Colspan = (int)$Colspan;
        $a = $time % 30;
        if ($a > 0) {
            $Colspan++;
        }

        // $ID_equipment_add = '';
        // if (is_array($input['chk_e_additional'])) {
        //     $ID_equipment_add = implode(',', $input['chk_e_additional']);
        // }

        $ID_equipment_add = '';
        if (is_array($input['chk_e_additional'])) {
            $xx = $input['chk_e_additional'];
            $yy = array();
            for ($i=0; $i < count($xx); $i++) { 
                $yy[] = $xx[$i]->ID_equipment_add;
            }
            $ID_equipment_add = implode(',', $yy);
        }
        
        $ID_add_personel = '';
        if (is_array($input['chk_person_support'])) {
            $ID_add_personel = implode(',', $input['chk_person_support']);
        }

        // check data bentrok dengan jam lain
        // $chk = $this->m_reservation->checkBentrok($Start,$End,$input['chk_e_multiple'],$input['Room']);
        $chk = $this->m_reservation->checkBentrok($Start,$End,'',$input['Room']);
        if ($chk) {
            $Multiple = '';
            // if (is_array($input['chk_e_multiple'])) {
            $boolArray = false;
            if ($boolArray) {
                for ($i=0; $i < count($input['chk_e_multiple']); $i++) { 
                   if ($i == 0) {
                        $dataSave = array(
                            'Start' => $Start,
                            'End' => $End,
                            'Time' => $time,
                            'Colspan' => $Colspan,
                            'Agenda' => $input['Agenda'],
                            'Room' => $input['Room'],
                            'ID_equipment_add' => $ID_equipment_add,
                            'ID_add_personel' => $ID_add_personel,
                            'Req_date' => date('Y-m-d'),
                            'CreatedBy' => $this->session->userdata('NIP'),
                            'Req_layout' => $filename,
                        );
                        $this->db->insert('db_reservation.t_booking', $dataSave);
                        $insert_id = $this->db->insert_id();
                        $Multiple = $insert_id;

                        $dataSave = array(
                            'Multiple' => $insert_id,
                        );
                        $this->db->where('ID', $insert_id);
                        $this->db->update('db_reservation.t_booking', $dataSave);

                       $get = $input['chk_e_multiple'][$i];
                       $Start = date("Y-m-d H:i:s", strtotime($get.$input['Start']));
                       $End = date("Y-m-d H:i:s", strtotime($get.$input['End']));
                        
                       $dataSave = array(
                           'Start' => $Start,
                           'End' => $End,
                           'Time' => $time,
                           'Colspan' => $Colspan,
                           'Agenda' => $input['Agenda'],
                           'Room' => $input['Room'],
                           'ID_equipment_add' => $ID_equipment_add,
                           'ID_add_personel' => $ID_add_personel,
                           'Req_date' => date('Y-m-d'),
                           'CreatedBy' => $this->session->userdata('NIP'),
                           'Multiple' => $Multiple,
                           'Req_layout' => $filename,
                       );
                       $this->db->insert('db_reservation.t_booking', $dataSave);

                    }
                    else
                    {
                        $get = $input['chk_e_multiple'][$i];
                        $Start = date("Y-m-d H:i:s", strtotime($get.$input['Start']));
                        $End = date("Y-m-d H:i:s", strtotime($get.$input['End']));
                        $dataSave = array(
                            'Start' => $Start,
                            'End' => $End,
                            'Time' => $time,
                            'Colspan' => $Colspan,
                            'Agenda' => $input['Agenda'],
                            'Room' => $input['Room'],
                            'ID_equipment_add' => $ID_equipment_add,
                            'ID_add_personel' => $ID_add_personel,
                            'Req_date' => date('Y-m-d'),
                            'CreatedBy' => $this->session->userdata('NIP'),
                            'Multiple' => $Multiple,
                            'Req_layout' => $filename,
                        );
                        $this->db->insert('db_reservation.t_booking', $dataSave);
                    }
                }

            }
            else
            {
                $dataSave = array(
                    'Start' => $Start,
                    'End' => $End,
                    'Time' => $time,
                    'Colspan' => $Colspan,
                    'Agenda' => $input['Agenda'],
                    'Room' => $input['Room'],
                    'ID_equipment_add' => $ID_equipment_add,
                    'ID_add_personel' => $ID_add_personel,
                    'Req_date' => date('Y-m-d'),
                    'CreatedBy' => $this->session->userdata('NIP'),
                    'Req_layout' => $filename,
                    'ParticipantQty' => $input['Participant']
                );
                $this->db->insert('db_reservation.t_booking', $dataSave);
                $ID_t_booking = $this->db->insert_id();

                if (is_array($input['chk_e_additional'])) {
                    // save data t_booking_eq_additional
                    $xx = $input['chk_e_additional'];
                    $yy = array();
                    for ($i=0; $i < count($xx); $i++) { 
                        $yy[] = array('ID_t_booking' =>$ID_t_booking,'ID_equipment_additional' => $xx[$i]->ID_equipment_add,'Qty' =>  $xx[$i]->Qty);
                    }
                    // $this->db->insert('db_reservation.t_booking_eq_additional', $yy);
                    $this->db->insert_batch('db_reservation.t_booking_eq_additional', $yy);
                }
            }
            echo json_encode(array('msg' => 'The Proses Finish','status' => 1));
        }
        else
        {
            echo json_encode(array('msg' => 'Your schedule is Conflict Please check.','status' => 0));
        }
    }

    // mt_rand()

    public function uploadFfile($name)
    {
         // upload file
         $filename = md5($name);
         $config['upload_path']   = './uploads/vreservation/';
         $config['overwrite'] = TRUE; 
         $config['allowed_types'] = '*'; 
         $config['file_name'] = $filename;
         //$config['max_size']      = 100; 
         //$config['max_width']     = 300; 
         //$config['max_height']    = 300;  
         $this->load->library('upload', $config);
            
         if ( ! $this->upload->do_upload('fileData')) {
            return $error = $this->upload->display_errors(); 
            //$this->load->view('upload_form', $error); 
         }
            
         else { 
           return $data =  $this->upload->data(); 
            //$this->load->view('upload_success', $data); 
         }
    }

    public function vr_request()
    {
        $content = $this->load->view($this->pathView.'transaksi/page_approve','',true);
        $this->temp($content);
    }

    public function json_list_approve()
    {
        $getData = $this->m_reservation->getDataT_booking();
        echo json_encode($getData);
    }

    public function json_list_booking_by_user()
    {
        $getData = $this->m_reservation->getDataT_bookingByUser(null,'',2);
        echo json_encode($getData);
    }

    public function json_list_booking()
    {
        $getData = $this->m_reservation->getDataT_booking(null,'',2);
        echo json_encode($getData);
    }

    public function approve_submit()
    {
        $msg = '';
        $input = $this->getInputToken();
        $ID = $input['ID_tbl'];
        // check approve bentrok
        $get = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$ID);
        $Start = $get[0]['Start'];$End = $get[0]['End'];$chk_e_multiple = '';$Room = $get[0]['Room'];
        $chk = $this->m_reservation->checkBentrok($Start,$End,$chk_e_multiple,$Room,$ID);
        if ($chk) {
            $dataSave = array(
                    'Status' => 1,
                    'ApprovedBy' => $this->session->userdata('NIP'),
                    'ApprovedBy' => date('Y-m-d H:i:s'),
                            );
            $this->db->where('ID',$ID);
            $this->db->update('db_reservation.t_booking', $dataSave);

            // // add Qty
            // $getE_additional = $this->m_master->caribasedprimary('db_reservation.t_booking_eq_additional','ID_t_booking',$ID);
            // if (count($getE_additional) > 0) {
            //     $bool = true; // check qty ready
            //     for ($i=0; $i < count($getE_additional); $i++) { 
            //         // add Qty
            //         $ID_equipment_additional = $getE_additional[$i]['ID_equipment_additional'];
            //         $Qty_T = $getE_additional[$i]['Qty'];
            //         $getM_equip_add = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
            //         if ($getM_equip_add < $Qty_T || $getM_equip_add ==  0) {
            //             $bool = false;
            //             break;
            //         }
            //     }

            //     if ($bool) {
            //         for ($i=0; $i < count($getE_additional); $i++) { 
            //             // add Qty
            //             $ID_equipment_additional = $getE_additional[$i]['ID_equipment_additional'];
            //             $Qty_T = $getE_additional[$i]['Qty'];
            //             $getM_equip_add = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
            //             $QTY_Upd = $getM_equip_add[0]['Qty'] - $Qty_T;
            //             $dataSave = array(
            //                 'Qty' => $QTY_Upd,
            //             );
            //             $this->db->where('ID', $ID_equipment_additional);
            //             $this->db->update('db_reservation.m_equipment_additional', $dataSave);
            //         }
            //     }
            //     else
            //     {
            //         $msg = 'This Equipment Additional isnot enough to quantity, Please check';
            //     }
                

            // }

        }
        else
        {
            $msg = 'This schedule conflict, Please check';
        }

        echo json_encode($msg);
        
    }

    public function cancel_submit()
    {
        $msg = '';
            $input = $this->getInputToken();

            $get = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$input['ID_tbl']);
            $getUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$get[0]['CreatedBy']);
            $getE_additional = $this->m_master->caribasedprimary('db_reservation.t_booking_eq_additional','ID_t_booking',$get[0]['ID']);

            for ($i=0; $i < count($getE_additional); $i++) { 
                $dataSave = array(
                    'ID_t_booking_eq_add' => $getE_additional[$i]['ID'],
                    'ID_t_booking' => $get[0]['ID'],
                    'ID_equipment_additional' => $getE_additional[$i]['ID_equipment_additional'],
                    'Qty' => $getE_additional[$i]['Qty'],
                );
                $this->db->insert('db_reservation.t_booking_eq_additional_delete', $dataSave); 
            }
            
            $sql = "delete from db_reservation.t_booking_eq_additional where ID_t_booking = ".$get[0]['ID'];
            $query=$this->db->query($sql, array());

            // if (count($getE_additional) > 0) {
            //     // cek status approve atau tidak
            //     if ($get[0]['Status'] == 1) {
            //         for ($i=0; $i < count($getE_additional); $i++) { 
            //             // add Qty
            //             $ID_equipment_additional = $getE_additional[$i]['ID_equipment_additional'];
            //             $Qty_T = $getE_additional[$i]['Qty'];
            //             $getM_equip_add = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
            //             $QTY_Upd = $Qty_T + $getM_equip_add[0]['Qty'];
            //             $dataSave = array(
            //                 'Qty' => $QTY_Upd,
            //             );
            //             $this->db->where('ID', $ID_equipment_additional);
            //             $this->db->update('db_reservation.m_equipment_additional', $dataSave);

            //             $dataSave = array(
            //                 'ID_t_booking_eq_add' => $getE_additional[$i]['ID'],
            //                 'ID_t_booking' => $get[0]['ID'],
            //                 'ID_equipment_additional' => $getE_additional[$i]['ID_equipment_additional'],
            //                 'Qty' => $getE_additional[$i]['Qty'],
            //             );
            //             $this->db->insert('db_reservation.t_booking_eq_additional_delete', $dataSave); 
            //         }
            //     }
            //     else
            //     {
            //         for ($i=0; $i < count($getE_additional); $i++) { 
            //             $dataSave = array(
            //                 'ID_t_booking_eq_add' => $getE_additional[$i]['ID'],
            //                 'ID_t_booking' => $get[0]['ID'],
            //                 'ID_equipment_additional' => $getE_additional[$i]['ID_equipment_additional'],
            //                 'Qty' => $getE_additional[$i]['Qty'],
            //             );
            //             $this->db->insert('db_reservation.t_booking_eq_additional_delete', $dataSave); 
            //         }
            //     }

            //     $sql = "delete from db_reservation.t_booking_eq_additional where ID_t_booking = ".$get[0]['ID'];
            //     $query=$this->db->query($sql, array());

            // }

            $dataSave = array(
                'Start' => $get[0]['Start'],
                'End' => $get[0]['End'],
                'Time' => $get[0]['Time'],
                'Colspan' => $get[0]['Colspan'],
                'Agenda' => $get[0]['Agenda'],
                'Room' => $get[0]['Room'],
                'ID_equipment_add' => $get[0]['ID_equipment_add'],
                'ID_add_personel' => $get[0]['ID_add_personel'],
                'Req_date' => $get[0]['Req_date'],
                'CreatedBy' => $get[0]['CreatedBy'],
                'ID_t_booking' => $get[0]['ID'],
                'Note_deleted' => 'Cancel By User',
                'DeletedBy' => $this->session->userdata('NIP'),
                'Req_layout' => $get[0]['Req_layout'],
                'Status' => $get[0]['Status'],
            );
            $this->db->insert('db_reservation.t_booking_delete', $dataSave); 

            $this->m_master->delete_id_table_all_db($get[0]['ID'],'db_reservation.t_booking');
// send email
            
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');

            $Email = $getUser[0]['EmailPU'];
            $text = 'Dear '.$getUser[0]['Name'].',<br><br>
                        Your Venue Reservation was Cancel by '.$this->session->userdata('Name').',<br><br>
                        Details Schedule : <br><ul>
                        <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                        <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                        <li>Room  : '.$get[0]['Room'].'</li>
                        </ul>
                    ';        
            $to = $Email;
            $subject = "Podomoro University Venue Reservation";
            $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
        echo json_encode($msg);    
    }

    public function booking_cancel()
    {
        $content = $this->load->view($this->pathView.'transaksi/page_booking_cancel','',true);
        $this->temp($content);
    }
    public function cancel_reservation()
    {
        $content = $this->load->view($this->pathView.'transaksi/page_cancel_reservation','',true);
        $this->temp($content);
    }


}
