<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_save_to_pdf extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('JWT');
        $this->load->library('pdf');

        $this->load->model('report/m_save_to_pdf');

        date_default_timezone_set("Asia/Jakarta");
    }

    private function getInputToken($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    // ==== PDF Schedule ====
    public function schedulePDF(){

        $token = $this->input->get('token');
        $data_arr = $this->getInputToken($token);




        $pdf = new FPDF('l','mm','A4');

        $data_sch = $this->m_save_to_pdf->getScheduleByDay($data_arr['SemesterID'],$data_arr['DayID'],date("Y-m-d"));

        if(count($data_sch)>0){

            $DayNameEng = $data_sch['DetailsSemester']['DayNameEng'];
            $SemesterDetails = $data_sch['DetailsSemester']['SemesterName'];

            $pdf->AddPage();
            $this->header_schedule($pdf,$SemesterDetails,$DayNameEng);

            $dataCourse = $data_sch['DetailsCourse'];


            $deft_w = 10;
            if(count($dataCourse)>0){

                $no = 1;
                for($m=0;$m<count($dataCourse);$m++){

                    $d_course = $dataCourse[$m];


                    $w = (count($d_course['detailTeamTeaching'])>0) ? (count($d_course['detailTeamTeaching'])+1) * $deft_w : $deft_w;

                    $pdf->SetFillColor(255, 206, 206);
                    $lb = ($d_course['Label']=='Ex') ? true : false;

                    $pdf->Cell(10,$w,$no,1,0,'C',$lb);
                    $pdf->Cell(20,$w,$d_course['ClassGroup'],1,0,'C');

                    $course = (strlen(trim($d_course['Course']))>=55) ? substr(trim($d_course['Course']),0,55).'_' : trim($d_course['Course']);
                    $pdf->Cell(90,$w,' '.$course,1,0,'L');

                    $pdf->Cell(20,$w,substr($d_course['StartSessions'],0,5).' - '.substr($d_course['EndSessions'],0,5),1,0,'C');
                    $pdf->Cell(25,$w,$d_course['ClassRoom'],1,0,'C');
                    $pdf->SetFont('Arial','B',8);
                    $coor = (strlen(trim($d_course['Coordinator']))>20) ? substr(trim($d_course['Coordinator']),0,19).'_' : trim($d_course['Coordinator']) ;
                    $pdf->Cell(42,$deft_w,' (Co) '.$coor,1,0,'L');

                    $pdf->Cell(20,$deft_w,'',1,0,'L');
                    $pdf->Cell(15,$deft_w,'',1,0,'L');
                    $pdf->Cell(20,$deft_w,'',1,0,'L');
                    $pdf->Cell(15,$deft_w,'',1,1,'L');

                    $pdf->SetFont('Arial','',8);
                    if(count($d_course['detailTeamTeaching'])>0){
                        for($t=0;$t<count($d_course['detailTeamTeaching']);$t++){
                            $teamT = $d_course['detailTeamTeaching'][$t];
                            $teamT2 = (strlen(trim($teamT))>26) ? substr(trim($teamT),0,25).'_' : trim($teamT) ;
                            $pdf->Cell(165,$deft_w,'',0,0,'L');
                            $pdf->Cell(42,$deft_w,' '.$teamT2,1,0,'L');
                            $pdf->Cell(20,$deft_w,'',1,0,'L');
                            $pdf->Cell(15,$deft_w,'',1,0,'L');
                            $pdf->Cell(20,$deft_w,'',1,0,'L');
                            $pdf->Cell(15,$deft_w,'',1,1,'L');
                        }
                    }

                    if($pdf->GetY()>173){
                        $pdf->AddPage();
                        $this->header_schedule($pdf,$SemesterDetails,$DayNameEng);
                    }

                    $no += 1;
                }
            }
            else {
                $pdf->Cell(275,7,'Schedule Not yet',1,1,'C');
            }

        }

        $pdf->Output('I',$DayNameEng.'_schedule.pdf');
    }

    private function header_schedule($pdf,$SemesterDetails,$DayNameEng){
        $pdf->Image(base_url('images/icon/logo-hr.png'),10,10,50);

        $pdf->SetFont('Arial','B',10);

        $pdf->Cell(60,9,'',0,0,'C');
        $pdf->Cell(0,9,'Semester - '.$SemesterDetails,1,1,'C');


        $pdf->Ln(5);
        $pdf->Cell(0,5,''.$DayNameEng,0,1,'C');

        $pdf->SetFont('Arial','i',7);
        $pdf->Cell(275,5,'FM-UAP/AKD-08-05',0,1,'R');
//        $pdf->Cell(275,5,''.$DayNameEng,0,1,'R');
//        $pdf->Ln(5);

        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10,7,'No',1,0,'C',true);
        $pdf->Cell(20,7,'Group',1,0,'C',true);
        $pdf->Cell(90,7,'Course',1,0,'C',true);
        $pdf->Cell(20,7,'Time',1,0,'C',true);
        $pdf->Cell(25,7,'Room',1,0,'C',true);
        $pdf->Cell(42,7,'Lecturers',1,0,'C',true);
        $pdf->Cell(20,7,'Time',1,0,'C',true);
        $pdf->Cell(15,7,'Taken',1,0,'C',true);
        $pdf->Cell(20,7,'Time',1,0,'C',true);
        $pdf->Cell(15,7,'Returned',1,1,'C',true);

        $pdf->SetFont('Arial','',8);
    }
    //===========

    // ==== PDF Monitoring Attendance Lecturer ====

    public function monitoringAttdLecturer(){
        $token = $this->input->post('token');

        $data_arr = $this->getInputToken($token);

        $pdf = new FPDF('l','mm','A4');

        $pdf->AddPage();

        $pdf->Image(base_url('images/icon/logo-hr.png'),10,10,50);

        $pdf->SetFont('Times','B',10);

        $pdf->Ln(1);

        $h = 4;

        $pdf->Cell(296,$h,'Universitas Agung Podomoro',0,1,'C');

        $pdf->SetFont('Times','',8);
        $pdf->Cell(296,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28 Tel: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(296,$h,'website : www.podomorouniversity.ac.id email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(10,27,296,27);

        $pdf->Ln(7);

        $pdf->SetFont('Times','B',8);
        $pdf->Cell(296,$h,'ATTENDANCE REPORT SEMESTER '.strtoupper($data_arr['Semester']),0,1,'C');
        $pdf->Cell(296,$h,'PROGRAMME STUDY : '.strtoupper($data_arr['Prodi']),0,1,'C');

        $pdf->Ln(5);

        $h = 5.5;

        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Times','B',8);
        $pdf->Cell(8,$h + $h,'No',1,0,'C',true);
        $pdf->Cell(22,$h + $h,'Code',1,0,'C',true);
        $pdf->Cell(85,$h + $h,'Course',1,0,'C',true);
        $pdf->Cell(15,$h + $h,'Group',1,0,'C',true);
        $pdf->Cell(50,$h + $h,'Lecturer',1,0,'C',true);

        $pdf->Cell(20,$h + $h,'Day',1,0,'C',true);
        $pdf->Cell(20,$h + $h,'Time',1,0,'C',true);
        $pdf->Cell(24,$h + $h,'Room',1,0,'C',true);
        $pdf->Cell(43,$h,'Session',1,1,'C',true);

        $pdf->Cell(244,$h,'',0,0,'C');
        $pdf->Cell(14,$h,'Target',1,0,'C',true);
        $pdf->Cell(14,$h,'Real',1,0,'C',true);
        $pdf->Cell(15,$h,'%',1,1,'C',true);

        $pdf->SetFont('Times','',8);
        $save2PDF = (array) $data_arr['save2PDF'];
        if(count($save2PDF)>0){
            $no = 1;
            for($s=0;$s<count($save2PDF);$s++){
                $d = (array) $save2PDF[$s];

                $newH = $h * count($d['Schedule']);

                $pdf->Cell(8,$newH,($no++),1,0,'C');
                $pdf->Cell(22,$newH,$d['MKCode'],1,0,'C');
                $pdf->Cell(85,$newH,' '.$d['MKNameEng'],1,0);
                $pdf->Cell(15,$newH,$d['ClassGroup'],1,0,'C');
                $pdf->Cell(50,$newH,' '.$d['Lecturer'],1,0);

                for($c=0;$c<count($d['Schedule']);$c++){
                    $dt = (array) $d['Schedule'][$c];

                    if($c!=0){
                        $pdf->Cell(180,$h,'',0,0,'C');
                    }

                    $pdf->Cell(20,$h,$dt['Day'],1,0,'C');
                    $pdf->Cell(20,$h,$dt['Time'],1,0,'C');
                    $pdf->Cell(24,$h,$dt['Room'],1,0,'C');

                    $pdf->Cell(14,$h,$dt['Target'],1,0,'C');
                    $pdf->Cell(14,$h,$dt['Real'],1,0,'C');
                    $pdf->Cell(15,$h,$dt['Percent'],1,1,'C');

                }



            }
        }



        $pdf->Output('I','Monitoring_Attendance_Lecturer.pdf');


    }

    // ============================================

    // ==== PDF Schedule Exchange =====

    public function scheduleExchange(){
        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

//        print_r($data_arr);
//        exit;

        $pdf = new FPDF('l','mm','A4');

        $pdf->AddPage();

        $pdf->Image(base_url('images/icon/logo-hr.png'),10,10,50);

        $pdf->SetFont('Times','B',10);

        $pdf->Ln(1);

        $h = 5;

        $pdf->Cell(296,$h,'Universitas Agung Podomoro',0,1,'C');

        $pdf->SetFont('Times','',8);
        $pdf->Cell(296,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28 Tel: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(296,$h,'website : www.podomorouniversity.ac.id email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(10,27,296,27);

        $pdf->Ln(7);

        $pdf->SetFont('Times','B',8);
        $pdf->Cell(296,$h,'SCHEDULE EXCHANGE',0,1,'C');

        $pdf->Ln(5);

        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Times','B',8);
        $pdf->Cell(8,$h + $h,'No',1,0,'C',true);
//        $pdf->Cell(22,$h + $h,'Code',1,0,'C',true);
        $pdf->Cell(86,$h + $h,'Course',1,0,'C',true);
        $pdf->Cell(15,$h + $h,'Group',1,0,'C',true);
        $pdf->Cell(10,$h + $h,'Sesi',1,0,'C',true);

        $pdf->Cell(38,$h + $h,'Reason',1,0,'C',true);
        $pdf->Cell(10,$h + $h,'Status',1,0,'C',true);


        $pdf->SetFillColor(202, 215, 255);
        $pdf->Cell(60,$h,'Schedule Exist',1,0,'C',true);

        $pdf->SetFillColor(255, 226, 226);
        $pdf->Cell(60,$h,'Exchange to',1,1,'C',true);

        $pdf->SetFillColor(202, 215, 255);
        $pdf->Cell(167,$h,'',0,0,'C');
        $pdf->Cell(34,$h,'Day, Time',1,0,'C',true);
        $pdf->Cell(26,$h,'Room',1,0,'C',true);

        $pdf->SetFillColor(255, 226, 226);
        $pdf->Cell(34,$h,'Day, Time',1,0,'C',true);
        $pdf->Cell(26,$h,'Room',1,1,'C',true);



        $pdf->SetFont('Times','',8);

        $ch_atas = 4;
        $ch = 4;

        $no = 1;
        for($i=0;$i<count($data_arr);$i++){

            $d = (array) $data_arr[$i];

            //27
            if(strlen($d['Reason'])>27){
                $r1 = substr($d['Reason'],0,26).'-';
                $r_2 = substr($d['Reason'],26,26);

                $r2 = (strlen($r_2)>27) ? $r_2.'_' : $r_2;


            } else {
                $r1 = $d['Reason'];
                $r2 = '';
            }

            $pdf->Cell(8,$ch_atas,($no++),'LRT',0,'C');
            $pdf->SetFont('Times','B',8);
            $pdf->Cell(86,$ch_atas,$d['Course'],'LRT',0,'L');
            $pdf->SetFont('Times','',8);
            $pdf->Cell(15,$ch_atas,$d['ClassGroup'],'LRT',0,'C');
            $pdf->Cell(10,$ch_atas,$d['A_Sesi'],'LRT',0,'C');
            $pdf->Cell(38,$ch_atas,$r1,'LRT',0,'L');

            if($d['A_Sesi']=='1'){
                $pdf->SetFont('ZapfDingbats');
                $pdf->Cell(10,$ch_atas,chr(52),'LRT',0,'C');
            } else {
                $pdf->Cell(10,$ch_atas,'-','LRT',0,'C');
            }



            $pdf->SetFont('Times','',8);
            $pdf->Cell(34,$ch_atas,$d['A_Date'],'LRT',0,'C');
            $pdf->Cell(26,$ch_atas,$d['A_Room'],'LRT',0,'C');
            $pdf->Cell(34,$ch_atas,$d['T_Date'],'LRT',0,'C');
            $pdf->Cell(26,$ch_atas,$d['T_Room'],'LRT',1,'C');


            $pdf->Cell(8,$ch,'','LRB',0,'C');
            $pdf->Cell(86,$ch,$d['Lecturer'],'LRB',0,'L');
            $pdf->Cell(15,$ch,'','LRB',0,'C');
            $pdf->Cell(10,$ch,'','LRB',0,'C');
            $pdf->Cell(38,$ch,$r2,'LRB',0,'L');
            $pdf->Cell(10,$ch,'','LRB',0,'C');
            $pdf->Cell(34,$ch,$d['A_Time'],'LRB',0,'C');
            $pdf->Cell(26,$ch,'','LRB',0,'C');
            $pdf->Cell(34,$ch,$d['T_Time'],'LRB',0,'C');
            $pdf->Cell(26,$ch,'','LRB',1,'C');
        }




        $pdf->Output('I','Schedule_Exchange.pdf');
    }

    // ===============================

    public function listStudentsFromCourse(){

        $token = $this->input->get('token');
        $data_arr = $this->getInputToken($token);


        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();

        $this->header_exam_layout($pdf);


        $pdf->Output();
    }

    private function header_exam_layout($pdf){

        $pdf->Image(base_url('images/icon/logo-l.png'),10,10,30);

        $pdf->SetFont('Arial','B',10);

        $pdf->Cell(45,9,'',0,0,'C');
        $pdf->Cell(230,9,'SEATING MAP FINAL EXAM (UAS) - 2017/2018 GANJIL',1,1,'C');

        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(0,5,'Page : '.$pdf->PageNo().' of {nb}',0,1,'R');

        $pdf->SetFont('Arial','',10);

//        $pdf->Cell(190,3,'',0,1);

        $space_header = 5;
        $pdf->Cell(45,$space_header,'',0,0);
        $pdf->Cell(20,$space_header,'Course',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(122,$space_header,'Studio 4',0,0);
        $pdf->Cell(20,$space_header,'Date',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(62,$space_header,'Tuesday, 8 Jun 2018',0,1);

        $pdf->Cell(45,$space_header,'',0,0);
        $pdf->Cell(20,$space_header,'Pengawas 1',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(122,$space_header,'Nandang M',0,0);
        $pdf->Cell(20,$space_header,'Time',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(62,$space_header,'08:00 - 09:00',0,1);

        $pdf->Cell(45,$space_header,'',0,0);
        $pdf->Cell(20,$space_header,'Pengawas 2',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(122,$space_header,'-',0,0);
        $pdf->Cell(20,$space_header,'Room',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(62,$space_header,'503',0,1);

        $pdf->Cell(275,7,'',0,1);
        $pdf->Cell(275,0.3,'',1,1);

        // Lecturer
        $mejaGuru = 'kiri2';
        if($mejaGuru=='kiri'){
            $pdf->Image(base_url('images/icon/lecturer.png'),25,50,15);
            $pdf->Image(base_url('images/icon/door.png'),250,50,15);
        } else {
            $pdf->Image(base_url('images/icon/door.png'),25,50,15);
            $pdf->Image(base_url('images/icon/lecturer.png'),250,50,15);
        }


        $pdf->SetFillColor(226, 226, 226);
        $pdf->Cell(275,7,'',0,1);
        $pdf->Cell(45,5,'',0,0);
        $pdf->Cell(180,7,'Board',1,1,'C',true);

        $pdf->Cell(275,15,'',0,1);

        $pdf->AliasNbPages();

    }

    public function exam_layout(){
        $pdf = new FPDF('l','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();

        $this->header_exam_layout($pdf);

        $base_x = 10;
        $base_y = 75;
        $koor_x = $base_x;
        $koor_y = $base_y;
        $no=1;

        $jml_deret = 7; // Dinamis
        $total_w = 275;
        $space = 2;
        $width = ($total_w - (($jml_deret - 1) * $space)) / $jml_deret;

        $height_panel = 7;
        $pdf->SetFont('Arial','',10);
        $pdf->setFillColor(255,255,102);

        for($m=1;$m<=170;$m++){

            // NIM
            $pdf->SetXY($koor_x,$koor_y);
            $pdf->Cell($width,$height_panel,'NPM '.$m,1,0,'C',true);

            // Name
            $pdf->SetXY($koor_x,($koor_y+7));
            $nm = $m."_Nandang M";
            $exp_name = explode(" ",$nm);
            $name_fil = (count($exp_name)>3) ? $exp_name[0].' '.$exp_name[1] : $nm;
            $name = (strlen($name_fil)<=15) ? $name_fil : $exp_name[0];
            $pdf->Cell($width,7,$name,1,0,'C');

            $koor_x = $koor_x + $space;
            if($no%$jml_deret==0){
                $koor_x = $base_x;
                $koor_y = $koor_y + (($height_panel * 2) + 5);

                if($pdf->GetY()>170){
                    // membuat halaman baru
                    $pdf->AddPage();
                    $this->header_exam_layout($pdf);
                    $pdf->SetFont('Arial','',10);
                    $pdf->setFillColor(255,255,102);
                    $koor_x = $base_x;
                    $koor_y = $base_y;
                }
            } else {
                $koor_x = $koor_x+$width;
            }

            $no++;
        }

//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();
    }

    private function header_exam($pdf){

        $pdf->Image(base_url('images/icon/logo-l.png'),10,10,25);

        $pdf->SetFont('Arial','B',12);

        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(150,5,'UNIVERSITAS AGUNG PODOMORO',0,1,'C');

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(35,5,'',0,0,'C');
        $pdf->Cell(150,5,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');
        $pdf->Cell(35,5,'',0,0,'C');
        $pdf->Cell(150,5,'Tel: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(35,5,'',0,0,'C');
        $pdf->Cell(150,5,'website : www.podomorouniversity.ac.id email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Cell(185,7,'',0,1);
        $pdf->Cell(185,0.3,'',1,1);

    }

    // Naskah Soal
    public function draft_questions(){

        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();

        $this->header_exam($pdf);

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(185,7,'',0,1);
        $pdf->Cell(185,5,'DRAFT QUESTIONS',0,1,'C');
        $pdf->Cell(185,5,'ATTENDANCE FINAL EXAMINATION',0,1,'C');
        $pdf->Cell(185,5,'EVEN SEMESTER',0,1,'C');
        $pdf->Cell(185,5,'ACADEMIC YEAR 2017/2018',0,1,'C');

        $pdf->Cell(185,7,'',0,1);

        $pdf->Rect(10, 76, 185, 93);
        $pdf->Rect(10, 184, 185, 23);

        $pdf->SetFont('Arial','I',8);
        $pdf->Cell(185,5,'Exam Scheduled / Supplementary *)',0,1,'R');

        $height_fill = 10;
        $pdf->Cell(185,5,'',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Information Exam',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'UAS',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Study Program',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'HBP',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Code | Course',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'HOT0014 | Food and Beverage - Service Theory',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Class Group',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'A',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Lecturer/s',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'MICHAEL YADISAPUTRA, MICHAEL YADISAPUTRA',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Day, Date',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'Monday, 25 June 2018',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Time, Room',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'08:00 - 09:00, 503',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Total Script',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'______________________',0,1);

        $pdf->Cell(185,7,'',0,1);
        $pdf->SetFont('Arial','I',8);
        $pdf->Cell(185,7,'Note : Cross out unnecessary *)',0,1,'L');


        $pdf->Cell(185,10,'',0,1);
        $pdf->SetFont('Arial','',10);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Pengawas 1',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'Nandang Mulyadi',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(40,$height_fill,'Pengawas 2',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(135,$height_fill,'-',0,1);

        $pdf->Cell(185,10,'',0,1);
        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(185,5,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' IT Podomoro University',0,1,'R');

//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();

    }

    // Lembar Jawaban
    public function answer_sheet(){

        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();

        $this->header_exam($pdf);

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(185,7,'',0,1);
        $pdf->Cell(185,5,'ANSWER SHEET',0,1,'C');
        $pdf->Cell(185,5,'ATTENDANCE FINAL EXAMINATION',0,1,'C');
        $pdf->Cell(185,5,'EVEN SEMESTER',0,1,'C');
        $pdf->Cell(185,5,'ACADEMIC YEAR 2017/2018',0,1,'C');

        $pdf->Cell(185,7,'',0,1);

        $pdf->Rect(10, 76, 185, 93);
        $pdf->Rect(10, 184, 185, 23);

        $pdf->SetFont('Arial','I',8);
        $pdf->Cell(185,5,'Exam Scheduled / Supplementary *)',0,1,'R');

        $height_fill = 10;
        $pdf->Cell(185,5,'',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Information Exam',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'UAS',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Study Program',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'HBP',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Code | Course',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'HOT0014 | Food and Beverage - Service Theory',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Class Group',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'A',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Lecturer/s',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'MICHAEL YADISAPUTRA, MICHAEL YADISAPUTRA',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Day, Date',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'Monday, 25 June 2018',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Time, Room',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'08:00 - 09:00, 503',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Number of Answer Sheet',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'______________________',0,1);

        $pdf->Cell(185,7,'',0,1);
        $pdf->SetFont('Arial','I',8);
        $pdf->Cell(185,7,'Note : Cross out unnecessary *)',0,1,'L');


        $pdf->Cell(185,10,'',0,1);
        $pdf->SetFont('Arial','',10);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Pengawas 1',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'Nandang Mulyadi',0,1);

        $pdf->Cell(5,$height_fill,'',0,0);
        $pdf->Cell(45,$height_fill,'Pengawas 2',0,0,'L');
        $pdf->Cell(5,$height_fill,':',0,0);
        $pdf->Cell(130,$height_fill,'-',0,1);

        $pdf->Cell(185,10,'',0,1);
        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(185,5,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' IT Podomoro University',0,1,'R');

//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();

    }

    // Berita Acara
    public function news_event(){

        $pdf = new FPDF('l','mm','A5');
        // membuat halaman baru
        $pdf->AddPage();

        $pdf->Rect(10, 7, 35, 25);
        $pdf->Rect(45, 7, 115, 25);
        $pdf->Rect(160, 7, 35, 25);

        $pdf->Image(base_url('images/icon/logo-l.png'),15,8,25);

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(115,6,'FINAL EXAMINATION',0,0,'C');
        $pdf->Cell(35,6,'',0,1,'C');

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(115,6,'EVEN SEMESTER',0,0,'C');
        $pdf->Cell(35,6,'',0,1,'C');

        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(115,6,'ACADEMIC YEAR 2017/2018',0,0,'C');
        $pdf->Cell(35,6,'',0,1,'C');

        $pdf->Cell(185,7,'',0,1);
//        $pdf->Cell(185,0.3,'',1,1);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(185,7,'',0,1);

        $pdf->Cell(20,7,'Code',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'HOT0014',0,0);

        $pdf->Cell(25,7,'Study Program',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'HBP',0,1);

        // ---

        $pdf->Cell(20,7,'Subject',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'Food and Beverage - Service Theory',0,0);

        $pdf->Cell(25,7,'Group',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'HBP8A',0,1);

        // ---

        $pdf->Cell(20,7,'Day, date',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'Monday, 25 June 2018',0,0);

        $pdf->Cell(25,7,'Exam Type',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'Open / Close',0,1);

        // ---

        $pdf->Cell(20,7,'Time',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'08:00 - 09:40',0,0);

        $pdf->Cell(25,7,'Calculator',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'Yes / No',0,1);

        // ---

        $pdf->Cell(20,7,'Room',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'504',0,0);

        $pdf->Cell(25,7,'Dictionary',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'Yes / No',0,1);


        // ------------
        $pdf->Rect(10, 85, 185, 30);
        $pdf->Cell(185,10,'',0,1);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(185,10,'Note test execution',0,1,'C');

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(5,7,'',0,0);
        $pdf->Cell(45,7,'Number of Participants',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(130,7,'_____________',0,1);

        $pdf->Cell(5,7,'',0,0);
        $pdf->Cell(45,7,'The number in attendance',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(130,7,'_____________',0,1);

        $pdf->SetFont('Arial','I',9);
        $pdf->Cell(185,13,'Note : *) Strikethrough the unnecessary items',0,1,'L');


        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(185,3,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' IT Podomoro University',0,1,'R');

//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();

    }

    private function header_attendance($pdf){
        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(0,7,'Page : '.$pdf->PageNo().' of {nb}',0,0,'R');
        $pdf->AliasNbPages();

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(185,7,'',0,1);
        $pdf->Cell(185,5,'ATTENDANCE FINAL EXAMINATION',0,1,'C');
        $pdf->Cell(185,5,'EVEN SEMESTER ACADEMIC YEAR 2017/2018',0,1,'C');

        $pdf->Cell(185,7,'',0,1,'');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(25,5,'Code',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HOT0014',0,0);

        $pdf->Cell(25,5,'Study Program',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'HBP',0,1);

        // ---

        $pdf->Cell(25,5,'Course',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'Food and Beverage - Service Theory',0,0);

        $pdf->Cell(25,5,'Day, date',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'Monday, 25 Desember 2018',10,1);

        // ---

        $pdf->Cell(25,5,'Group',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HBP8A',0,0);

        $pdf->Cell(25,5,'Time',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'08:00 - 09:40',0,1);

        // ---

        $pdf->Cell(25,5,'Lecturer/s',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'Michael Yadisaputra',0,0);

        $pdf->Cell(25,5,'Room',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'504',0,1);
    }

    private function header_attd_table($pdf){
        $pdf->Cell(185,7,'',0,1,'');
        $width_attd = 9;
        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,$width_attd,'No',1,0,'C',true);
        $pdf->Cell(25,$width_attd,'NPM',1,0,'C',true);
        $pdf->Cell(95,$width_attd,'Name',1,0,'C',true);
        $pdf->Cell(35,$width_attd,'Sign',1,0,'C',true);
        $pdf->Cell(20,$width_attd,'Score',1,1,'C',true);
        $pdf->SetFont('Arial','',10);
    }

    // Daftar Hadir
    public function attendance_list(){

        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();
        $this->header_exam($pdf);

        $this->header_attendance($pdf);

        $this->header_attd_table($pdf);


        $width_attd = 9;

        $no = 1;
        for($s=0;$s<26;$s++){
            $pdf->Cell(10,$width_attd,$no++,1,0,'C');
            $pdf->Cell(25,$width_attd,'21140008',1,0,'C');
            $pdf->Cell(95,$width_attd,'Name',1,0,'L');
            $pdf->Cell(35,$width_attd,'Sign',1,0,'C');
            $pdf->Cell(20,$width_attd,''.$pdf->GetY(),1,1,'C');

            if($pdf->GetY()>=265){
                $pdf->AddPage();
                $this->header_exam($pdf);
                $this->header_attendance($pdf);
                $this->header_attd_table($pdf);
            }

        }



        $pdf->Cell(185,7,'',0,1,'');
        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(185,3,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' IT Podomoro University',0,1,'R');



        $pdf->SetFont('Arial','I',9);
        $pdf->Cell(185,17,'',0,1,'');
        $pdf->Cell(130,3,'',0,0,'R');
        $pdf->Cell(55,3,'Sign by Lecturer : ',0,1,'C');

        $pdf->Cell(185,17,'',0,1,'');

        $pdf->Cell(130,3,'',0,0,'R');
        $pdf->Cell(55,3,'( ...................... )',0,1,'C');




//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();

    }


    // ========= Score ==========

    private function header_report_exam($pdf,$exam){
        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(0,7,'Page : '.$pdf->PageNo().' of {nb}',0,0,'R');
        $pdf->AliasNbPages();

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(185,7,'',0,1);
        $pdf->Cell(185,5,'Food and Beverage - Service Theory',0,1,'C');
        $pdf->Cell(185,5,'HOT0014',0,1,'C');
//        $pdf->Cell(185,5,'EVEN SEMESTER ACADEMIC YEAR 2017/2018',0,1,'C');

        $pdf->Cell(185,7,'',0,1,'');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(25,5,'Code',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HOT0014',0,0);

        $pdf->Cell(25,5,'Study Program',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'HBP',0,1);

        // ---

        $pdf->Cell(25,5,'Course',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'Food and Beverage - Service Theory',0,0);

        $pdf->Cell(25,5,'Day, date',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'Monday, 25 Desember 2018',10,1);

        // ---

        $pdf->Cell(25,5,'Code',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HOT0014',0,0);

        $pdf->Cell(25,5,'Study Program',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'HBP',0,1);

        // ---

        $pdf->Cell(25,5,'Group',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HBP8A',0,0);

        $pdf->Cell(25,5,'Time',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'08:00 - 09:40',0,1);

        // ---

        $pdf->Cell(25,5,'Lecturer/s',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'Michael Yadisaputra',0,0);

        $pdf->Cell(25,5,'Room',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'504',0,1);
    }

    // UTS
    public function report_uts(){

        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();
        $this->header_exam($pdf);

        $this->header_report_exam($pdf,'UTS');


//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();

    }

    public function getpdfkwitansi($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        // print_r($data_arr);

        $pdf = new FPDF('l','mm','A5');
        // membuat halaman baru
        $pdf->AddPage();

        $pdf->SetMargins(10,10,10,10);
        $pdf->SetAutoPageBreak(true, 0);
        
        // Logo
        $pdf->Image('./images/logo_tr.png',5,5,50);
        
        $pdf->SetFont('Arial','',17);
        $pdf->Cell(200, 0, 'Tanda Terima Pembayaran Mahasiswa', 0, 1, 'C', 0);
        
        $pdf->SetFont('Arial','',15);
        $pdf->Cell(200, 15, 'Tahun Akademik', 0, 1, 'C', 0);

        $pdf->SetFont('Arial','',15);
        $pdf->Cell(200, 0, $data_arr['semester'], 0, 1, 'C', 0);

        $pdf->Line(10, 30, 200, 30);

        $pdf->Cell(200, 7, '', 0, 1, 'C', 0);
        
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(100, 5, '', 0, 0, 'C');
        $this->load->model('master/m_master');
        $date = date('Y-m-d');
        $date = $this->m_master->getIndoBulan($date);
        $pdf->Cell(90, 5, 'Tanggal Cetak : '.$date, 0, 1, 'R');

        $pdf->Cell(35, 7, 'Nama', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['nama'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'NPM', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['npm'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Tipe Pembayaran', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['ptid'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Program Studi', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['prodi'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Virtual Account', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['va'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'BilingID', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['bilingid'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Invoice', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['invoice'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Waktu Pembayaran', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['Time'], 0, 1, 'L');

        $pdf->Cell(200, 7, '', 0, 1, 'C', 0);
        $pdf->Cell(190, 7, 'Note : ', 0, 1, 'L');
        $pdf->Cell(190, 7, 'Tanda terima ini merupakan bukti pembayaran yang diterbitkan oleh Podomoro University.', 0, 1, 'L');
        $pdf->Cell(190, 7, 'Jika terdapat kekeliruan harap hubungi bagian Admisi dibawah ini.', 0, 1, 'L');

        $pdf->Line(10, 135, 200, 135);
        $setY = 135;
        $pdf->SetFont('Arial','',6);
        $pdf->SetXY(40,$setY);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(190, 5, 'Admission Office :  Central Park Mall, Lantai 3, Unit 112, Podomoro City, JL Letjen S. Parman Kav.28, Jakarta Barat 11470', 0, 1, 'L', 0);

        $setY = 138;
        $pdf->SetFont('Arial','',6);
        $pdf->SetXY(43,$setY);
        $pdf->SetTextColor(0,0,0);
        // $this->mypdf->SetFillColor(0,0,0);
        $pdf->Cell(190, 5, 'Telp : (021) 292 00 456    Email : admission@podomorouniversity.ac.id   Website : www.podomorouniversity.ac.id', 0, 1, 'L', 0);

        $pdf->AliasNbPages();    

        $pdf->Output();
    }
}
