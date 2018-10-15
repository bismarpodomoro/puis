<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_save_to_excel extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('JWT');
//        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('report/m_save_to_excel');

    }

    private function getInputToken($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    public function test2()
    {

//        echo 'ok';
//        exit;

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();

        $pr = 'REKAP NILAI';

        // Settingan awal fil excel
        $excel->getProperties()->setCreator('IT PU')
            ->setLastModifiedBy('IT PU')
            ->setTitle($pr)
            ->setSubject($pr)
            ->setDescription($pr)
            ->setKeywords($pr);

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        $excel->setActiveSheetIndex(0)->setCellValue('A1', $pr); // Set kolom A1 dengan tulisan "DATA KARYAWAN"
        $excel->getActiveSheet()->mergeCells('A1:O1'); // Set Merge Cell pada kolom A1 sampai O1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

        // Buat header tabel nya pada baris ke 3
        $excel->setActiveSheetIndex(0)->setCellValue('A3', "NIM"); // Set kolom A3 dengan tulisan "NIK"
        $excel->setActiveSheetIndex(0)->setCellValue('B3', "Nama");
        $excel->setActiveSheetIndex(0)->setCellValue('C3', "Prodi");
        $excel->setActiveSheetIndex(0)->setCellValue('D3', "Code");
        $excel->setActiveSheetIndex(0)->setCellValue('E3', "Course");
        $excel->setActiveSheetIndex(0)->setCellValue('F3', "Group");
        $excel->setActiveSheetIndex(0)->setCellValue('G3', "Coordinator");
        $excel->setActiveSheetIndex(0)->setCellValue('H3', "Assignment 1");
        $excel->setActiveSheetIndex(0)->setCellValue('H3', "Assignment 2");
        $excel->setActiveSheetIndex(0)->setCellValue('I3', "Assignment 3");
        $excel->setActiveSheetIndex(0)->setCellValue('J3', "Assignment 4");
        $excel->setActiveSheetIndex(0)->setCellValue('K3', "Assignment 5");
        $excel->setActiveSheetIndex(0)->setCellValue('L3', "UTS");
        $excel->setActiveSheetIndex(0)->setCellValue('M3', "UAS");
        $excel->setActiveSheetIndex(0)->setCellValue('N3', "Score");
        $excel->setActiveSheetIndex(0)->setCellValue('O3', "Grade");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $excel->getActiveSheet()->getStyle('A3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('B3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('C3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('D3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('E3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('F3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('G3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('H3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('I3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('J3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('K3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('L3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('M3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('N3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('O3')->applyFromArray($style_col);

        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4


        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $excel->getActiveSheet(0)->setTitle("Rekap Data Karyawan");
        $excel->setActiveSheetIndex(0);

        // Proses file excel
        $filename = "Rekap_Data_Karyawan.xlsx";
        //$FILEpath = "./dokument/".$filename;
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=test.xlsx'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
//            $write->save($FILEpath);

        //echo json_encode(array('file' => $filename));

        // exit else ajax
    }


    function test(){

//        $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
//        $excel2 = $excel2->load('./uploads/finance/TemplatePembayaran.xlsx'); // Empty Sheet
//
//        $excel = new PHPExcel();
//
//        $excel->getProperties()->setCreator('Alhadi Rahman')
//            ->setLastModifiedBy('Alhadi Rahman')
//            ->setTitle("Data Karyawan Produksi")
//            ->setSubject("Data Karyawan Produksi")
//            ->setDescription("Rekap Data Karyawan Produksi")
//            ->setKeywords("Data Karyawan Produksi");
//
//        $excel2->setActiveSheetIndex(0);
//
//        $excel3 = $excel2->getActiveSheet();
        $excel3 =  new PHPExcel();;
        $excel3->setCellValue('A2', 'Rekap Penerimaan & AGING ');

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // start dari A7
        $d = ' Nandang';
        $a = 8;
        for ($i=0; $i < 5; $i++) {
            $no = $i + 1;
            $excel3->setCellValue('A'.$a, $no);
            $excel3->setCellValue('B'.$a, $d);
            $excel3->setCellValue('C'.$a, $d);
            $excel3->setCellValue('D'.$a, $d);
            $excel3->setCellValue('E'.$a, $d);
            $excel3->setCellValue('F'.$a, $d);
            $excel3->setCellValue('G'.$a, $d);
            $excel3->setCellValue('H'.$a, $d);
            $excel3->setCellValue('I'.$a, $d);
            $excel3->setCellValue('J'.$a, $d);

            // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
            $excel3->getStyle('A'.$a)->applyFromArray($style_row);
            $excel3->getStyle('B'.$a)->applyFromArray($style_row);
            $excel3->getStyle('C'.$a)->applyFromArray($style_row);
            $excel3->getStyle('D'.$a)->applyFromArray($style_row);
            $excel3->getStyle('E'.$a)->applyFromArray($style_row);
            $excel3->getStyle('F'.$a)->applyFromArray($style_row);
            $excel3->getStyle('G'.$a)->applyFromArray($style_row);
            $excel3->getStyle('H'.$a)->applyFromArray($style_row);
            $excel3->getStyle('I'.$a)->applyFromArray($style_row);
            $excel3->getStyle('J'.$a)->applyFromArray($style_row);
            $excel3->getStyle('K'.$a)->applyFromArray($style_row);

            $a = $a + 1;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');

        $filename = 'PenerimaanPembayaran.xlsx';
//        $objWriter->save('./document/'.$filename);
        $objWriter->save('php://output'); // jalan ketika tidak menggunakan ajax
    }

    public function export_excel_report_finance()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $input = (array) $this->jwt->decode($token,$key);
        $GetDateNow = date('Y-m-d');
        $this->load->model('master/m_master');
        $this->load->model('finance/m_finance');
        $GetDateNow = $this->m_master->getIndoBulan($GetDateNow);
        // print_r($input['Data']);die();

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
        $excel2 = $excel2->load('./uploads/finance/Template_report.xlsx'); // Empty Sheet
        $excel2->setActiveSheetIndex(0);

        $excel3 = $excel2->getActiveSheet();
        $excel3->setCellValue('A3', $GetDateNow.' Jam '.date('H:i'));

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // start dari A7
        $dataGenerate = $input['Data'];
        $summary = $input['summary'];
        $PostPassing = $input['PostPassing'];
        $a = 7;
        $sumTagihanAll = 0;
        $sumPembayaranAll = 0;
        $sumPiutangAll = 0;

        for ($i=0; $i < count($dataGenerate); $i++) {
            $no = $i + 1;
            $excel3->setCellValue('A'.$a, $dataGenerate[$i][0]);
            $excel3->setCellValue('B'.$a, $dataGenerate[$i][1]);
            $excel3->setCellValue('C'.$a, $dataGenerate[$i][2]);
            $excel3->setCellValue('D'.$a, $dataGenerate[$i][3]);
            $excel3->setCellValue('E'.$a, $dataGenerate[$i][4]);
            $excel3->setCellValue('F'.$a, $dataGenerate[$i][5]);
            $excel3->setCellValue('G'.$a, $dataGenerate[$i][6]);
            $excel3->setCellValue('H'.$a, $dataGenerate[$i][7]);
            $excel3->setCellValue('I'.$a, $dataGenerate[$i][8]);

            // $ket = "adi\nresa";

            $excel3->setCellValue('J'.$a, $dataGenerate[$i][9]);

            // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
            $excel3->getStyle('A'.$a)->applyFromArray($style_row);
            $excel3->getStyle('B'.$a)->applyFromArray($style_row);
            $excel3->getStyle('C'.$a)->applyFromArray($style_row);
            $excel3->getStyle('D'.$a)->applyFromArray($style_row);
            $excel3->getStyle('E'.$a)->applyFromArray($style_row);
            $excel3->getStyle('F'.$a)->applyFromArray($style_row);
            $excel3->getStyle('G'.$a)->applyFromArray($style_row);
            $excel3->getStyle('H'.$a)->applyFromArray($style_row);
            $excel3->getStyle('I'.$a)->applyFromArray($style_row);
            $excel3->getStyle('J'.$a)->applyFromArray($style_row);
            $excel3->getStyle('J'.$a)->getAlignment()->setWrapText(true);
            // $excel3->getStyle('K'.$a)->applyFromArray($style_row);

            $a = $a + 1;
        }

        $excel3->mergeCells('A'.$a.':F'.$a); // Set Merge Cell pada kolom A1 sampai E1
        $setTA = $summary->taShow;
        $excel3->setCellValue('A'.$a, $setTA);
        $excel3->getStyle('A'.$a)->applyFromArray($style_row);
        $excel3->getStyle('B'.$a)->applyFromArray($style_row);
        $excel3->getStyle('C'.$a)->applyFromArray($style_row);
        $excel3->getStyle('D'.$a)->applyFromArray($style_row);
        $excel3->getStyle('E'.$a)->applyFromArray($style_row);
        $excel3->getStyle('F'.$a)->applyFromArray($style_row);
        // $excel3->getStyle('A'.$a)->applyFromArray($style_row);
        $excel3->setCellValue('G'.$a, $summary->sumTagihan);
        $excel3->setCellValue('H'.$a, $summary->sumPembayaran);
        $excel3->setCellValue('I'.$a, $summary->sumPiutang);
        $excel3->setCellValue('J'.$a, '');

        $excel3->getStyle('G'.$a)->applyFromArray($style_row);
        $excel3->getStyle('H'.$a)->applyFromArray($style_row);
        $excel3->getStyle('I'.$a)->applyFromArray($style_row);
        $excel3->getStyle('J'.$a)->applyFromArray($style_row);

        $sumTagihanAll = $sumTagihanAll + $summary->sumTagihan;
        $sumPembayaranAll = $sumPembayaranAll +$summary->sumPembayaran;
        $sumPiutangAll  = $sumPiutangAll +$summary->sumPiutang;

        $a = $a + 1;
        // get all mahasiswa
        // per page 2 database
        if ($PostPassing->ta == '' && $PostPassing->NIM == '') {
            $sqlCount = 'show databases like "%ta_2%"';
            $queryCount=$this->db->query($sqlCount, array())->result_array();
            $bigData = array();
            foreach ($queryCount as $key) {
                foreach ($key as $keyB ) {
                    $bigData[] = $keyB;
                }

            }

            rsort($bigData);

            for ($zz=0; $zz < count($bigData); $zz++) {
                # code...

                $dbTA = explode('_', $bigData[$zz]);
                $dbTA = $dbTA[1];

                if($dbTA != $dataGenerate[0][10])
                {

                    $a = $a + 2;
                    $aa = $a + 1;
                    $dbPass = '0.'.$dbTA;
                    $data = $this->m_finance->get_report_pembayaran_mhs($dbPass,$PostPassing->prodi,$PostPassing->NIM,$PostPassing->Semester,$PostPassing->Status,1, 0);
                    if (count($data) > 0) {
                        // make header table
                        $excel3->mergeCells('A'.$a.':A'.$aa); // Set Merge Cell pada kolom A1 sampai E1
                        $excel3->mergeCells('B'.$a.':B'.$aa); // Set Merge Cell pada kolom A1 sampai E1
                        $excel3->mergeCells('C'.$a.':C'.$aa); // Set Merge Cell pada kolom A1 sampai E1
                        $excel3->mergeCells('E'.$a.':F'.$a); // Set Merge Cell pada kolom A1 sampai E1
                        $excel3->mergeCells('G'.$a.':G'.$aa); // Set Merge Cell pada kolom A1 sampai E1
                        $excel3->mergeCells('H'.$a.':H'.$aa); // Set Merge Cell pada kolom A1 sampai E1
                        $excel3->mergeCells('I'.$a.':I'.$aa); // Set Merge Cell pada kolom A1 sampai E1
                        $excel3->mergeCells('J'.$a.':J'.$aa); // Set Merge Cell pada kolom A1 sampai E1

                        $excel3->setCellValue('A'.$a, 'No');
                        $excel3->setCellValue('B'.$a, 'NAMA');
                        $excel3->setCellValue('C'.$a, 'NPM');
                        $excel3->setCellValue('D'.$a, 'JURUSAN');
                        $excel3->setCellValue('E'.$a, 'TAGIHAN');
                        $excel3->setCellValue('E'.$aa, 'BPP');
                        $excel3->setCellValue('F'.$aa, 'SKS');
                        $excel3->setCellValue('G'.$a, 'TOTAL TAGIHAN');
                        $excel3->setCellValue('H'.$a, 'TOTAL PEMBAYARAN');
                        $excel3->setCellValue('I'.$a, 'PIUTANG');
                        $excel3->setCellValue('J'.$a, 'KETERANGAN');

                        $excel3->getStyle('A'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('A'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('B'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('B'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('C'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('C'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('D'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('D'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('E'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('E'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('F'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('F'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('G'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('G'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('H'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('H'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('I'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('I'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('J'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('J'.$aa)->applyFromArray($style_row);
                        $excel3->getStyle('J'.$a)->getAlignment()->setWrapText(true);

                        $a = $aa + 1;

                        // $bigData[] = $data;
                        $sumTagihan = 0;
                        $sumPembayaran = 0;
                        $sumPiutang = 0;
                        for ($z=0; $z < count($data); $z++) {
                            $no = $z+1;
                            $Total_tagihan = $data[$z]['BPP']  + $data[$z]['Cr'] ;
                            $sumTagihan = $sumTagihan + $Total_tagihan;
                            $Total_pembayaran = $data[$z]['PayBPP']  + $data[$z]['PayCr'] ;
                            $sumPembayaran = $sumPembayaran + $Total_pembayaran;
                            $Piutang = $Data_mhs[$z]['SisaCr']  + $Data_mhs[$z]['SisaBPP'] ;
                            $sumPiutang = $sumPiutang + $Piutang;
                            $ketEXcel = "";

                            if ($Piutang > 0) {
                                if($data[$z]['DetailPaymentBPP'] != '')
                                {
                                    $DetailPaymentBPP = $data[$z]['DetailPaymentBPP'];
                                    $keteranganBPPEX = "BPP\n";
                                    for ($l = 0; $l < count($DetailPaymentBPP); $l++) {
                                        $lno = $l + 1;
                                        $StatusPay = ($DetailPaymentBPP[$l]['Status'] == 1) ? 'Sudah Bayar' : 'Belum Bayar';
                                        if ($DetailPaymentBPP[$l]['Status'] == 0) {
                                            $keteranganBPPEX .= "Pembayaran : ".$lno." \n";
                                            $keteranganBPPEX .= "Deadline : ".$DetailPaymentBPP[$l]['Deadline']."\n";
                                            $keteranganBPPEX .= "Status : ".$StatusPay."\n";
                                        }

                                    }
                                    $keteranganBPPEX .= "\n";
                                }
                                else{
                                    $keteranganBPPEX = "Tagihan BPP belum diset\n";
                                }

                                if($data[$z]['DetailPaymentCr'] != '')
                                {
                                    $DetailPaymentCr = $data[$z]['DetailPaymentCr'];
                                    $keteranganCrEX = "Credit\n";
                                    for ($l = 0; $l < count($DetailPaymentCr); $l++) {
                                        $lno = $l + 1;
                                        $StatusPay = ($DetailPaymentCr[$l]['Status'] == 1)? 'Sudah Bayar' : 'Belum Bayar';
                                        if($DetailPaymentCr[$l]['Status'] == 0)
                                        {
                                            $keteranganCrEX .= "Pembayaran : ".$lno."\n";
                                            $keteranganCrEX .= "Deadline : ".$DetailPaymentCr[$l]['Deadline']."\n";
                                            $keteranganCrEX .= "Status : ".$StatusPay."\n";
                                        }

                                    }
                                    $keteranganCrEX .= "\n";

                                }
                                else
                                {
                                    $keteranganCrEX .= "Tagihan Credit belum diset\n";
                                }
                            }
                            else if($Piutang == 0 && ($data[$z]['DetailPaymentCr'] == '' || $data[$z]['DetailPaymentBPP'] == '') ) // belum diset
                            {
                                if ($data[$z]['DetailPaymentBPP'] == '') {
                                    $keteranganBPPEX = "Tagihan BPP belum diset\n";
                                }

                                if ($data[$z]['DetailPaymentCr'] == '') {
                                    $keteranganCrEX = "Tagihan Credit belum diset\n";
                                }

                            }
                            $ketEXcel = $keteranganBPPEX.$keteranganCrEX;

                            $excel3->setCellValue('A'.$a, $no);
                            $excel3->setCellValue('B'.$a, $data[$z]['Name']);
                            $excel3->setCellValue('C'.$a, $data[$z]['NPM']);
                            $excel3->setCellValue('D'.$a, $data[$z]['ProdiENG']);
                            $excel3->setCellValue('E'.$a, $data[$z]['BPP']);
                            $excel3->setCellValue('F'.$a, $data[$z]['Cr']);
                            $excel3->setCellValue('G'.$a, $Total_tagihan);
                            $excel3->setCellValue('H'.$a, $Total_pembayaran);
                            $excel3->setCellValue('I'.$a, $Piutang);

                            // $ket = "adi\nresa";

                            $excel3->setCellValue('J'.$a, $ketEXcel);

                            // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                            $excel3->getStyle('A'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('B'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('C'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('D'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('E'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('F'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('G'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('H'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('I'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('J'.$a)->applyFromArray($style_row);
                            $excel3->getStyle('J'.$a)->getAlignment()->setWrapText(true);
                            // $excel3->getStyle('K'.$a)->applyFromArray($style_row);

                            $a = $a + 1;

                        }

                        $taShow = "Mahasiswa TA ".$data[0]['Year'];

                        $excel3->mergeCells('A'.$a.':F'.$a); // Set Merge Cell pada kolom A1 sampai E1
                        $setTA = $taShow;
                        $excel3->setCellValue('A'.$a, $setTA);
                        $excel3->getStyle('A'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('B'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('C'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('D'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('E'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('F'.$a)->applyFromArray($style_row);
                        // $excel3->getStyle('A'.$a)->applyFromArray($style_row);
                        $excel3->setCellValue('G'.$a, $sumTagihan);
                        $excel3->setCellValue('H'.$a, $sumPembayaran);
                        $excel3->setCellValue('I'.$a, $sumPiutang);
                        $excel3->setCellValue('J'.$a, '');

                        $excel3->getStyle('G'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('H'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('I'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('J'.$a)->applyFromArray($style_row);

                        $sumTagihanAll = $sumTagihanAll + $sumTagihan;
                        $sumPembayaranAll = $sumPembayaranAll +$sumPembayaran;
                        $sumPiutangAll  = $sumPiutangAll +$sumPiutang;

                        $a = $a + 1;
                    } // exit if data


                }


            }
        } // exit if not search

        // summary All
        $a = $a + 1;
        $taShow = "Summary All";

        $excel3->mergeCells('A'.$a.':F'.$a); // Set Merge Cell pada kolom A1 sampai E1
        $setTA = $taShow;
        $excel3->setCellValue('A'.$a, $setTA);
        $excel3->getStyle('A'.$a)->applyFromArray($style_row);
        $excel3->getStyle('B'.$a)->applyFromArray($style_row);
        $excel3->getStyle('C'.$a)->applyFromArray($style_row);
        $excel3->getStyle('D'.$a)->applyFromArray($style_row);
        $excel3->getStyle('E'.$a)->applyFromArray($style_row);
        $excel3->getStyle('F'.$a)->applyFromArray($style_row);
        // $excel3->getStyle('A'.$a)->applyFromArray($style_row);
        $excel3->setCellValue('G'.$a, $sumTagihanAll);
        $excel3->setCellValue('H'.$a, $sumPembayaranAll);
        $excel3->setCellValue('I'.$a, $sumPiutangAll);
        $excel3->setCellValue('J'.$a, '');

        $excel3->getStyle('G'.$a)->applyFromArray($style_row);
        $excel3->getStyle('H'.$a)->applyFromArray($style_row);
        $excel3->getStyle('I'.$a)->applyFromArray($style_row);
        $excel3->getStyle('J'.$a)->applyFromArray($style_row);

        // print_r($bigData);
        // die();


        $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel'); // jalan ketika tidak menggunakan ajax
        // It will be called file.xlss
        header('Content-Disposition: attachment; filename="file.xlsx"'); // jalan ketika tidak menggunakan ajax
        //$filename = 'PenerimaanPembayaran.xlsx';
        //$objWriter->save('./document/'.$filename);
        $objWriter->save('php://output'); // jalan ketika tidak menggunakan ajax

        // print_r($input['summary']);
    }

    public function export_excel_payment_received()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $input = (array) $this->jwt->decode($token,$key);
        // print_r($input);
        $Semester = $input['Semester'];
        $Semester = explode('.', $Semester);
        $Semester = $Semester[1];
        $data = $input['Data'];
        $this->load->model('finance/m_finance');
        $dataGenerate = $this->m_finance->GroupingNPM($data);

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
        $excel2 = $excel2->load('./uploads/finance/TemplatePembayaran.xlsx'); // Empty Sheet
        $excel2->setActiveSheetIndex(0);

        $excel3 = $excel2->getActiveSheet();
        $excel3->setCellValue('A2', 'Rekap Penerimaan & AGING '.$Semester);

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // start dari A7
        $a = 8;
        for ($i=0; $i < count($dataGenerate); $i++) {
            $no = $i + 1;
            $excel3->setCellValue('A'.$a, $no);
            $excel3->setCellValue('B'.$a, $dataGenerate[$i]['Nama']);
            $excel3->setCellValue('C'.$a, $dataGenerate[$i]['NPM']);
            $excel3->setCellValue('D'.$a, $dataGenerate[$i]['ProdiEng']);
            $excel3->setCellValue('E'.$a, $dataGenerate[$i]['SPP']);
            $excel3->setCellValue('F'.$a, $dataGenerate[$i]['Another']);
            $excel3->setCellValue('G'.$a, $dataGenerate[$i]['BPP']);
            $excel3->setCellValue('H'.$a, $dataGenerate[$i]['BPPKet']);
            $excel3->setCellValue('I'.$a, $dataGenerate[$i]['Credit']);
            $excel3->setCellValue('J'.$a, $dataGenerate[$i]['CreditKet']);

            // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
            $excel3->getStyle('A'.$a)->applyFromArray($style_row);
            $excel3->getStyle('B'.$a)->applyFromArray($style_row);
            $excel3->getStyle('C'.$a)->applyFromArray($style_row);
            $excel3->getStyle('D'.$a)->applyFromArray($style_row);
            $excel3->getStyle('E'.$a)->applyFromArray($style_row);
            $excel3->getStyle('F'.$a)->applyFromArray($style_row);
            $excel3->getStyle('G'.$a)->applyFromArray($style_row);
            $excel3->getStyle('H'.$a)->applyFromArray($style_row);
            $excel3->getStyle('I'.$a)->applyFromArray($style_row);
            $excel3->getStyle('J'.$a)->applyFromArray($style_row);
            $excel3->getStyle('K'.$a)->applyFromArray($style_row);

            $a = $a + 1;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
        // We'll be outputting an excel file
        header('Content-type: application/vnd.ms-excel'); // jalan ketika tidak menggunakan ajax
        // It will be called file.xlss
        header('Content-Disposition: attachment; filename="file.xlsx"'); // jalan ketika tidak menggunakan ajax
        //$filename = 'PenerimaanPembayaran.xlsx';
        //$objWriter->save('./document/'.$filename);
        $objWriter->save('php://output'); // jalan ketika tidak menggunakan ajax

    }

    public function export_excel_budget_creator()
    {
        $this->load->model('master/m_master');
        $this->load->model('budgeting/m_budgeting');
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $Input = (array) $this->jwt->decode($token,$key);
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes
        $NameDepartement = $this->m_master->getDepartementPu('ID',$Input['Departement']);
        $NameDepartement = $NameDepartement[0]['NameDepartement'];

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        // Settingan awal fil excel
        $excel->getProperties()->setCreator('Alhadi Rahman')
            ->setLastModifiedBy('Alhadi Rahman')
            ->setTitle("Podomoro University Budgeting")
            ->setSubject("Budgeting ".$NameDepartement)
            ->setDescription("Budgeting ".$NameDepartement)
            ->setKeywords("Budgeting ".$NameDepartement);

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Podomoro University Budgeting"); // Set kolom A1 dengan tulisan "DATA KARYAWAN"
        // $excel->getActiveSheet()->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
        // $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

        $excel->setActiveSheetIndex(0)->setCellValue('B2', $NameDepartement);
        $excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);

        $excel->setActiveSheetIndex(0)->setCellValue('A2', "Departement : ");
        $excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);



        $getData = $this->m_budgeting->get_creator_budget($Input['Year'] , $Input['Departement'] );
        // get Month
        $month = $getData[0]['DetailMonth'];
        $month = json_decode($month);


        $St = 5;
        $excel->setActiveSheetIndex(0)->setCellValue('A'.$St, "Post Budget Name");
        $excel->getActiveSheet()->mergeCells('A'.$St.':A'.($St+1));
        $excel->setActiveSheetIndex(0)->setCellValue('B'.$St, "Month");
        $excel->getActiveSheet()->getStyle('A'.$St)->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('A'.($St+1))->applyFromArray($style_col);
        $keyM = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $excel->getActiveSheet()->mergeCells('B'.$St.':'.$keyM[count($month)].$St);
        $excel->getActiveSheet()->getStyle('B'.$St.':'.$keyM[count($month)].$St)->applyFromArray($style_col);
        $excel->setActiveSheetIndex(0)->setCellValue($keyM[(count($month) + 1)].$St, "Sub Total");
        $excel->getActiveSheet()->mergeCells($keyM[(count($month) + 1)].$St.':'.$keyM[(count($month) + 1)].($St+1));
        $excel->getActiveSheet()->getStyle($keyM[(count($month) + 1)].$St.':'.$keyM[(count($month) + 1)].($St+1))->applyFromArray($style_col);
        $St = $St + 1;
        $StH = 1;
        for ($i=0; $i < count($month); $i++) {
            $a = $month[$i]->month;
            $a = explode('-', $a);
            $NameBulan = $this->m_master->BulanInggris($a[1]);
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $NameBulan);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_col);
            $StH = $StH + 1;
        }
        $St = $St + 1;
        $arr_subMonth = array();
        $GrandTotal = 0;
        for ($i=0; $i < count($getData); $i++) {
            $StH = 1;
            $excel->setActiveSheetIndex(0)->setCellValue('A'.$St, $getData[$i]['PostName'].'-'.$getData[$i]['RealisasiPostName']);
            $excel->getActiveSheet()->getStyle('A'.$St)->applyFromArray($style_row);
            $UnitCost = $getData[$i]['UnitCost'];
            $month1 = $getData[$i]['DetailMonth'];
            $month1 = json_decode($month1);
            $sub_Total = 0;
            for ($j=0; $j < count($month); $j++) {
                $value = $UnitCost * $month1[$j]->value;
                $value = (int)$value;
                if ($i == 0) {
                    $arr_subMonth[$j] = $value;
                }
                else
                {
                    $arr_subMonth[$j] = $arr_subMonth[$j] + $value;
                }

                $sub_Total = $sub_Total + $value;
                $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $value);
                $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
                $StH++;
            }
            $GrandTotal = $GrandTotal + $sub_Total;
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $sub_Total);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
            $St++;
        }

        $StH = 1;
        for ($i=0; $i < count($arr_subMonth); $i++) {
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $arr_subMonth[$i]);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
            $StH++;
        }
        $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $GrandTotal);
        $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);


        foreach(range('A','Z') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

        // Set judul file excel nya
        $excel->getActiveSheet()->setTitle("Podomoro University Budgeting");
        $excel->setActiveSheetIndex(0);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=PodomoroUniversityBudgeting.xlsx'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }

    public function export_excel_budget_creator_all()
    {
        $this->load->model('master/m_master');
        $this->load->model('budgeting/m_budgeting');
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $Input = (array) $this->jwt->decode($token,$key);
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        // Settingan awal fil excel
        $excel->getProperties()->setCreator('Alhadi Rahman')
            ->setLastModifiedBy('Alhadi Rahman')
            ->setTitle("Podomoro University Budgeting")
            ->setSubject("Budgeting ".'All')
            ->setDescription("Budgeting ".'All')
            ->setKeywords("Budgeting ".'All');

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Podomoro University Budgeting"); // Set kolom A1 dengan tulisan "DATA KARYAWAN"
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1

        $get = $this->m_budgeting->getListBudgetingDepartement($Input['Year']);
        $getData = $this->m_budgeting->get_creator_budget($Input['Year'] , $get[0]['ID'] );
        // get Month
        $month = $getData[0]['DetailMonth'];
        $month = json_decode($month);
        $St = 4;
        $excel->setActiveSheetIndex(0)->setCellValue('A'.$St, "Departement");
        $excel->getActiveSheet()->mergeCells('A'.$St.':A'.($St+1));
        $excel->setActiveSheetIndex(0)->setCellValue('B'.$St, "Month");
        $excel->getActiveSheet()->getStyle('A'.$St)->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('A'.($St+1))->applyFromArray($style_col);
        $keyM = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $excel->getActiveSheet()->mergeCells('B'.$St.':'.$keyM[count($month)].$St);
        $excel->getActiveSheet()->getStyle('B'.$St.':'.$keyM[count($month)].$St)->applyFromArray($style_col);
        $excel->setActiveSheetIndex(0)->setCellValue($keyM[(count($month) + 1)].$St, "Sub Total");
        $excel->getActiveSheet()->mergeCells($keyM[(count($month) + 1)].$St.':'.$keyM[(count($month) + 1)].($St+1));
        $excel->getActiveSheet()->getStyle($keyM[(count($month) + 1)].$St.':'.$keyM[(count($month) + 1)].($St+1))->applyFromArray($style_col);
        $St = $St + 1;
        $StH = 1;
        for ($i=0; $i < count($month); $i++) {
            $a = $month[$i]->month;
            $a = explode('-', $a);
            $NameBulan = $this->m_master->BulanInggris($a[1]);
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $NameBulan);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_col);
            $StH = $StH + 1;
        }

        $arr_subMonth = array();
        $GrandTotal = 0;
        $St = $St + 1;
        for ($i=0; $i < count($get); $i++) {
            $getData = $this->m_budgeting->get_creator_budget($Input['Year'] , $get[$i]['ID'] );
            $excel->setActiveSheetIndex(0)->setCellValue('A'.$St, $get[$i]['NameDepartement']);
            $excel->getActiveSheet()->getStyle('A'.$St)->applyFromArray($style_row);
            $arr_sub_sum_total = array();
            for ($x=0; $x < count($getData); $x++) {
                $UnitCost = $getData[$x]['UnitCost'];
                $month1 = $getData[$x]['DetailMonth'];
                $month1 = json_decode($month1);
                for ($j=0; $j < count($month); $j++) {
                    $value = $UnitCost * $month1[$j]->value;
                    $value = (int)$value;
                    if ($x == 0) {
                        $arr_sub_sum_total[$j] = $value;
                    }
                    else
                    {
                        $arr_sub_sum_total[$j] = $arr_sub_sum_total[$j] + $value;
                    }
                }


            } // exit loop get data per Div

            $sub_Total = 0;
            $StH = 1;
            for ($y=0; $y < count($month); $y++) {
                if (array_key_exists($y, $arr_sub_sum_total)) {
                    $value = $arr_sub_sum_total[$y];
                }
                else
                {
                    $value = 0;
                }

                $sub_Total = $sub_Total + $value;
                if ($i == 0) {
                    $arr_subMonth[$y] = $value;
                }
                else
                {
                    if (array_key_exists($y, $arr_subMonth)) {
                        $arr_subMonth[$y] = $arr_subMonth[$y] + $value;

                    }
                    else
                    {
                        $arr_subMonth[$y] = 0;
                    }

                }
                $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $value);
                $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
                $StH++;
            } // loop for horizontal

            // print_r($arr_sub_sum_total);

            $GrandTotal = $GrandTotal + $sub_Total;
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $sub_Total);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
            $St++;
        } // exit loop per Div

        $StH = 1;
        for ($i=0; $i < count($arr_subMonth); $i++) {
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $arr_subMonth[$i]);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
            $StH++;
        }
        $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $GrandTotal);
        $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);

        foreach(range('A','Z') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

        // Set judul file excel nya
        $excel->getActiveSheet()->setTitle("Podomoro University Budgeting");
        $excel->setActiveSheetIndex(0);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=PodomoroUniversityBudgeting_AllDepart.xlsx'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }

    public function export_excel_budget_remaining()
    {
        $this->load->model('master/m_master');
        $this->load->model('budgeting/m_budgeting');
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $Input = (array) $this->jwt->decode($token,$key);
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes
        $NameDepartement = $this->m_master->getDepartementPu('ID',$Input['Departement']);
        $NameDepartement = $NameDepartement[0]['NameDepartement'];

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        // Settingan awal fil excel
        $excel->getProperties()->setCreator('Alhadi Rahman')
            ->setLastModifiedBy('Alhadi Rahman')
            ->setTitle("Podomoro University Budgeting")
            ->setSubject("Budgeting ".$NameDepartement)
            ->setDescription("Budgeting ".$NameDepartement)
            ->setKeywords("Budgeting ".$NameDepartement);

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Podomoro University Budgeting-Remaining"); // Set kolom A1 dengan tulisan "DATA KARYAWAN"
        // $excel->getActiveSheet()->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
        // $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

        $excel->setActiveSheetIndex(0)->setCellValue('B2', $NameDepartement);
        $excel->getActiveSheet()->getStyle('B2')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(15);

        $excel->setActiveSheetIndex(0)->setCellValue('A2', "Departement : ");
        $excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(15);



        $getData = $this->m_budgeting->get_budget_remaining($Input['Year'] , $Input['Departement'] );
        $getData = $this->m_budgeting->Grouping_PostBudget($getData);

        // get Month
        $month = $getData[0]['DetailMonth'];
        $month = json_decode($month);


        $St = 5;
        $excel->setActiveSheetIndex(0)->setCellValue('A'.$St, "Post Budget Name");
        $excel->getActiveSheet()->mergeCells('A'.$St.':A'.($St+1));
        $excel->setActiveSheetIndex(0)->setCellValue('B'.$St, "Month");
        $excel->getActiveSheet()->getStyle('A'.$St)->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('A'.($St+1))->applyFromArray($style_col);
        $keyM = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $excel->getActiveSheet()->mergeCells('B'.$St.':'.$keyM[count($month)].$St);
        $excel->getActiveSheet()->getStyle('B'.$St.':'.$keyM[count($month)].$St)->applyFromArray($style_col);
        $excel->setActiveSheetIndex(0)->setCellValue($keyM[(count($month) + 1)].$St, "Sub Total");
        $excel->getActiveSheet()->mergeCells($keyM[(count($month) + 1)].$St.':'.$keyM[(count($month) + 1)].($St+1));
        $excel->getActiveSheet()->getStyle($keyM[(count($month) + 1)].$St.':'.$keyM[(count($month) + 1)].($St+1))->applyFromArray($style_col);
        $St = $St + 1;
        $StH = 1;
        for ($i=0; $i < count($month); $i++) {
            $a = $month[$i]->month;
            $a = explode('-', $a);
            $NameBulan = $this->m_master->BulanInggris($a[1]);
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $NameBulan);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_col);
            $StH = $StH + 1;
        }
        $St = $St + 1;
        $arr_subMonth = array();
        $GrandTotal = 0;
        for ($i=0; $i < count($getData); $i++) {
            $StH = 1;
            $excel->setActiveSheetIndex(0)->setCellValue('A'.$St, $getData[$i]['PostName'].'-'.$getData[$i]['RealisasiPostName']);
            $excel->getActiveSheet()->getStyle('A'.$St)->applyFromArray($style_row);
            $UnitCost = $getData[$i]['UnitCost'];
            $month1 = $getData[$i]['DetailMonth'];
            $month1 = json_decode($month1);
            $sub_Total = 0;
            for ($j=0; $j < count($month); $j++) {
                $value = $UnitCost * $month1[$j]->value;
                $value = (int)$value;
                if ($i == 0) {
                    $arr_subMonth[$j] = $value;
                }
                else
                {
                    $arr_subMonth[$j] = $arr_subMonth[$j] + $value;
                }

                $sub_Total = $sub_Total + $value;
                $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $value);
                $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
                $StH++;
            }
            $GrandTotal = $GrandTotal + $sub_Total;
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $sub_Total);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
            $St++;
        }

        $StH = 1;
        for ($i=0; $i < count($arr_subMonth); $i++) {
            $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $arr_subMonth[$i]);
            $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);
            $StH++;
        }
        $excel->setActiveSheetIndex(0)->setCellValue($keyM[$StH].$St, $GrandTotal);
        $excel->getActiveSheet()->getStyle($keyM[$StH].$St)->applyFromArray($style_row);


        foreach(range('A','Z') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

        // Set judul file excel nya
        $excel->getActiveSheet()->setTitle("Podomoro University Budgeting");
        $excel->setActiveSheetIndex(0);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=PodomoroUniversityBudgeting.xlsx'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }

    public function monitoring_score()
    {

        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

        $dataM = $this->m_save_to_excel->getMonitoringScore($data_arr);

//        print_r($dataM);
//        exit;


        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();

        $pr = 'REKAP NILAI';

        // Settingan awal fil excel
        $excel->getProperties()->setCreator('IT PU')
            ->setLastModifiedBy('IT PU')
            ->setTitle($pr)
            ->setSubject($pr)
            ->setDescription($pr)
            ->setKeywords($pr);

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        $excel->setActiveSheetIndex(0)->setCellValue('A1', $pr); // Set kolom A1 dengan tulisan "DATA KARYAWAN"
        $excel->getActiveSheet()->mergeCells('A1:O1'); // Set Merge Cell pada kolom A1 sampai O1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
        $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

        // Buat header tabel nya pada baris ke 3
        $excel->setActiveSheetIndex(0)->setCellValue('A3', "NIM"); // Set kolom A3 dengan tulisan "NIK"
        $excel->setActiveSheetIndex(0)->setCellValue('B3', "Nama");
        $excel->setActiveSheetIndex(0)->setCellValue('C3', "Prodi");
        $excel->setActiveSheetIndex(0)->setCellValue('D3', "Code");
        $excel->setActiveSheetIndex(0)->setCellValue('E3', "Course");
        $excel->setActiveSheetIndex(0)->setCellValue('F3', "Group");
        $excel->setActiveSheetIndex(0)->setCellValue('G3', "Coordinator");
        $excel->setActiveSheetIndex(0)->setCellValue('H3', "Assignment 1");
        $excel->setActiveSheetIndex(0)->setCellValue('H3', "Assignment 2");
        $excel->setActiveSheetIndex(0)->setCellValue('I3', "Assignment 3");
        $excel->setActiveSheetIndex(0)->setCellValue('J3', "Assignment 4");
        $excel->setActiveSheetIndex(0)->setCellValue('K3', "Assignment 5");
        $excel->setActiveSheetIndex(0)->setCellValue('L3', "UTS");
        $excel->setActiveSheetIndex(0)->setCellValue('M3', "UAS");
        $excel->setActiveSheetIndex(0)->setCellValue('N3', "Score");
        $excel->setActiveSheetIndex(0)->setCellValue('O3', "Grade");

        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $excel->getActiveSheet()->getStyle('A3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('B3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('C3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('D3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('E3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('F3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('G3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('H3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('I3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('J3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('K3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('L3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('M3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('N3')->applyFromArray($style_col);
        $excel->getActiveSheet()->getStyle('O3')->applyFromArray($style_col);

        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4

        if(count($dataM)>0){
            for($i=0;$i<count($dataM);$i++){
                $d = $dataM[$i];
                $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $d['NPM']);
                $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $d['Name']);
                $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $d['ProdiName']);
                $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $d['MKCode']);
                $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $d['MKNameEng']);
                $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $d['ClassGroup']);
                $excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $d['CoordinatorName']);
                $excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $d['Evaluasi1']);
                $excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $d['Evaluasi2']);
                $excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $d['Evaluasi3']);
                $excel->setActiveSheetIndex(0)->setCellValue('J'.$numrow, $d['Evaluasi4']);
                $excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $d['Evaluasi5']);
                $excel->setActiveSheetIndex(0)->setCellValue('L'.$numrow, $d['UTS']);
                $excel->setActiveSheetIndex(0)->setCellValue('M'.$numrow, $d['UAS']);
                $excel->setActiveSheetIndex(0)->setCellValue('N'.$numrow, $d['Score']);
                $excel->setActiveSheetIndex(0)->setCellValue('O'.$numrow, $d['Grade']);

                $numrow += 1;
            }
        }





        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

        // Set orientasi kertas jadi LANDSCAPE
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

        // Set judul file excel nya
        $excel->getActiveSheet(0)->setTitle("Rekap Data Karyawan");
        $excel->setActiveSheetIndex(0);

        foreach(range('A','Z') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        // Proses file excel
        $filename = "Rekap_Data_Karyawan.xlsx";
        //$FILEpath = "./dokument/".$filename;
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=test.xlsx'); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
//            $write->save($FILEpath);

        //echo json_encode(array('file' => $filename));

        // exit else ajax
    }



}