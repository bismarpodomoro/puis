<?php
if ($this->uri->segment(1) == 'dashboard') {
    redirect(base_url().'admission/dashboard');
}

?>
<script type="text/javascript">
    <?php if ($this->uri->segment(1) == 'dashboard'): ?>
    window.location.href = base_url_js+'admission/dashboard';
    <?php endif ?>
</script>
<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">
        <!--=== Navigation ===-->
        <ul id="nav">
            <?php 
                $getData  = $this->session->userdata('menu_admission_grouping');
                for ($i=0; $i < count($getData); $i++) {
                    $temp = array();
                    $chkSubMenu1 = 0;
                    $Slug = '';
                    $uri = '';
                    $uriSubMenu1 = ''; 
                    $uriSubMenu2 = ''; 
                    // check data  memiliki submenu1 dan submenu2
                        #Submenu 1
                        $SubMenu1_arr = $getData[$i]['Submenu'];
                        for ($j=0; $j < count($SubMenu1_arr); $j++) {
                            $temp2 = array(); 
                            if ($SubMenu1_arr[$j]['SubMenu1']  != 'Empty') {
                                $chkSubMenu1++;
                                $temp2 = array('SubMenu1' => $SubMenu1_arr[$j]['SubMenu1']);
                            }

                            #Submenu2
                            $chkSubMenu2 = 0;
                            $SubMenu2_arr = $SubMenu1_arr[$j]['Submenu'];
                            $data = array();
                            for ($k=0; $k < count($SubMenu2_arr); $k++) { 
                                if ($SubMenu2_arr[$k]['SubMenu2'] != 'Empty') {
                                    $data[] = array(
                                        'ID' => $SubMenu2_arr[$k]['ID'],
                                        'ID_Menu' => $SubMenu2_arr[$k]['ID_Menu'],
                                        'SubMenu1' => $SubMenu2_arr[$k]['SubMenu1'],
                                        'SubMenu2' => $SubMenu2_arr[$k]['SubMenu2'],
                                        'Slug' => $SubMenu2_arr[$k]['Slug'],
                                        'Controller' => $SubMenu2_arr[$k]['Controller'],
                                        'read' => $SubMenu2_arr[$k]['read'],
                                        'write' => $SubMenu2_arr[$k]['write'],
                                        'update' => $SubMenu2_arr[$k]['update'],
                                        'delete' => $SubMenu2_arr[$k]['delete'],

                                    );
                                    $uri = $SubMenu2_arr[$k]['Slug'];
                                    $t = explode('/', $uri);
                                    $uriSubMenu1 = $t[1];
                                    $uriSubMenu2 = $t[2];
                                    $chkSubMenu2++;
                                }
                            }

                            if ($chkSubMenu2 > 0) {
                               $temp2 = $temp2 + array('CountSubMenu2' => $chkSubMenu2,'data' =>$data);
                            }
                            else
                            {
                                $data[] = array(
                                        'ID' => $SubMenu2_arr[0]['ID'],
                                        'ID_Menu' => $SubMenu2_arr[0]['ID_Menu'],
                                        'SubMenu1' => $SubMenu2_arr[0]['SubMenu1'],
                                        'SubMenu2' => $SubMenu2_arr[0]['SubMenu2'],
                                        'Slug' => $SubMenu2_arr[0]['Slug'],
                                        'Controller' => $SubMenu2_arr[0]['Controller'],
                                        'read' => $SubMenu2_arr[0]['read'],
                                        'write' => $SubMenu2_arr[0]['write'],
                                        'update' => $SubMenu2_arr[0]['update'],
                                        'delete' => $SubMenu2_arr[0]['delete'],

                                );
                                $temp2 = $temp2 + array('CountSubMenu2' => $chkSubMenu2,'data' =>$data);
                            }

                            $temp[] = $temp2;   
                            
                        }

                        $open = (count($temp) > 0) ? 'open' : '';
                    // closed php tag    
                    ?>
                    <?php $uriSubMenu1 = $SubMenu1_arr[0]['Submenu'][0]['Slug'] ?>
                    <?php $uriSubMenu1 = explode('/', $uriSubMenu1)  ?>
                    <?php $uriSubMenu1 = $uriSubMenu1[1]  ?>
                    <li segment2 = "<?php echo $uriSubMenu1 ?>" class="<?php if($this->uri->segment(2)==$uriSubMenu1){echo "current ".$open;} ?>">
                        <a href="<?php echo $a = ($open == 'open') ? '#' : $uri ?>">
                            <i class="<?php echo ($getData[$i]['Menu'] == '' || $getData[$i]['Menu'] == null) ? 'fa fa-globe' :  $getData[$i]['Icon'] ?>" aria-hidden="true"></i>
                                <?php echo $getData[$i]['Menu'] ?>
                        </a>
                        <?php if (count($temp) > 0 ): ?>
                            <ul class="sub-menu">
                                <?php for($z = 0; $z < count($temp); $z++): ?>
                                    <?php if ($temp[$z]['CountSubMenu2'] > 0): ?>
                                        <?php $uriSubMenu2 = $temp[$z]['data'][0]['Slug'] ?>
                                        <?php $uriSubMenu2 = explode('/', $temp[$z]['data'][0]['Slug'])  ?>
                                        <?php $uriSubMenu2 = $uriSubMenu2[2]  ?>
                                        <li class="<?php if($this->uri->segment(2)==$uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2 ){echo "open-default";} ?>">
                                            <a href="javascript:void(0);">
                                                <i class="icon-angle-right"></i>
                                                    <?php echo $temp[$z]['SubMenu1'] ?>
                                            </a>
                                            <ul class="sub-menu">
                                                <?php $countS2 = $temp[$z]['data'] ?>
                                                <?php for($x = 0; $x < count($countS2); $x++): ?>
                                                    <?php $Uri3 = $countS2[$x]['Slug'] ?>
                                                    <?php $t = explode('/', $Uri3)  ?>
                                                    <?php $Uri3 = $t[3]  ?>
                                                    <li segment2 = "<?php echo $uriSubMenu1 ?>" segment3  = "<?php echo $uriSubMenu2 ?>" class="<?php if($this->uri->segment(2)==$uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2 && $this->uri->segment(4) == $Uri3 && $this->uri->segment(5) == ""){echo "current";} ?>">
                                                        <a href="<?php echo base_url($countS2[$x]['Slug']); ?>">
                                                            <i class="icon-angle-right"></i>
                                                            <?php echo $countS2[$x]['SubMenu2'] ?>
                                                        </a>
                                                    </li>
                                                <?php endfor ?>      
                                            </ul>
                                        </li>
                                    <?php else: ?>
                                        <?php $uriSubMenu2 = $temp[$z]['data'][0]['Slug'] ?>
                                        <?php $uriSubMenu2 = explode('/', $temp[$z]['data'][0]['Slug'])  ?>
                                        <?php $uriSubMenu2 = $uriSubMenu2[2]  ?>
                                        <li segment2 = "<?php echo $uriSubMenu1 ?>" segment3  = "<?php echo $uriSubMenu2 ?>" class="<?php if($this->uri->segment(2)==$uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2 ){echo "current";} ?>">
                                            <a href="<?php echo base_url($temp[$z]['data'][0]['Slug']); ?>"> 
                                                <i class="icon-angle-right"></i>
                                                    <?php echo $temp[$z]['SubMenu1'] ?>
                                            </a>
                                        </li>                           
                                    <?php endif ?>
                                <?php endfor ?>    
                            </ul>        
                        <?php endif ?>        
            <?php         

                }    

             ?>
            <li class="<?php if($this->uri->segment(2)=='config'){echo "current open";} ?>">
                <a href="#">
                    <i class="fa fa-wrench" aria-hidden="true"></i>
                    Configuration
                </a>
                <ul class="sub-menu">
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "set-tgl-register-online" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/set-tgl-register-online'); ?>">
                            <i class="icon-angle-right"></i>
                            Set Tanggal Pendaftaran
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "set-max-cicilan" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/set-max-cicilan'); ?>">
                            <i class="icon-angle-right"></i>
                            Set Max Cicilan
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == 'sdaerah' ){echo "open-default";} ?>">
                        <a href="javascript:void(0);">
                            <i class="icon-angle-right"></i>
                            Sekolah & Wilayah
                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "sdaerah" && $this->uri->segment(4) == "master-sma" && $this->uri->segment(5) == ""){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/sdaerah/master-sma'); ?>">
                                    <i class="icon-angle-right"></i>
                                    SMA / SMK
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "sdaerah" && $this->uri->segment(4) == "master-sma" && $this->uri->segment(5) != ""){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/sdaerah/master-sma/1'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Approve Sekolah
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "sdaerah" && $this->uri->segment(4) == "wilayah"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/sdaerah/wilayah'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Wilayah
                                </a>
                            </li>
                            <!--<li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "sdaerah" && $this->uri->segment(4) == "integration"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/sdaerah/integration'); ?>">
                                <i class="icon-angle-right"></i>
                                Integration
                                </a>
                            </li>-->
                        </ul>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "set-email" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/set-email'); ?>">
                            <i class="icon-angle-right"></i>
                            Set Email
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "email-to" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/email-to'); ?>">
                            <i class="icon-angle-right"></i>
                            Set Email To
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "set-print-label" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/set-print-label'); ?>">
                            <i class="icon-angle-right"></i>
                            Set Print Label
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "total-account" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/total-account'); ?>">
                            <i class="icon-angle-right"></i>
                            Total Account
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "number-formulir" ){echo "open-default";} ?>">
                        <a href="javascript:void(0);">
                            <i class="icon-angle-right"></i>
                            Number Formulir
                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "number-formulir" && $this->uri->segment(4) == "online"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/number-formulir/online'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Online
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "number-formulir" && $this->uri->segment(4) == "offline"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/number-formulir/offline'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Offline
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "virtual-account" ){echo "open-default";} ?>">
                        <a href="javascript:void(0);">
                            <i class="icon-angle-right"></i>
                            Virtual Account
                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "virtual-account" && $this->uri->segment(4) == "page-create-va"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/virtual-account/page-create-va'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Create VA
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "virtual-account" && $this->uri->segment(4) == "page-recycle-va"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/virtual-account/page-recycle-va'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Recycle VA
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "upload-pdf-per-pengumuman" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/upload-pdf-per-pengumuman'); ?>">
                            <i class="icon-angle-right"></i>
                            Upload PDF Pengumuman
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "harga-formulir" ){echo "open-default";} ?>">
                        <a href="javascript:void(0);">
                            <i class="icon-angle-right"></i>
                            Harga Formulir
                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "harga-formulir" && $this->uri->segment(4) == "online"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/harga-formulir/online'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Online
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "harga-formulir" && $this->uri->segment(4) == "offline"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/config/harga-formulir/offline'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Offline
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='config' && $this->uri->segment(3) == "menu-previleges" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/config/menu-previleges'); ?>">
                            <i class="icon-angle-right"></i>
                            Menu & Previleges
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php if($this->uri->segment(2)=='master'){echo "current open";} ?>">
                <a href="javascript:void(0);">
                    <i class="fa fa-globe"></i>
                    Master Data
                </a>
                <ul class="sub-menu">
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "agama" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/agama'); ?>">
                            <i class="icon-angle-right"></i>
                            Agama
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "jenis-tempat-tinggal" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/jenis-tempat-tinggal'); ?>">
                            <i class="icon-angle-right"></i>
                            Jenis Tempat Tinggal
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "pendapatan" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/pendapatan'); ?>">
                            <i class="icon-angle-right"></i>
                            Pendapatan
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "tipe-sekolah" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/tipe-sekolah'); ?>">
                            <i class="icon-angle-right"></i>
                            Tipe Sekolah
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "jurusan-sekolah" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/jurusan-sekolah'); ?>">
                            <i class="icon-angle-right"></i>
                            Jurusan Sekolah
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "ujian-masuk-per-prody" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/ujian-masuk-per-prody'); ?>">
                            <i class="icon-angle-right"></i>
                            Ujian Masuk Per Prody
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "sales-koordinator-wilayah" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/sales-koordinator-wilayah'); ?>">
                            <i class="icon-angle-right"></i>
                            Sales Koordinator Wilayah
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "event" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/event'); ?>">
                            <i class="icon-angle-right"></i>
                            Event
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "sumber-iklan" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/sumber-iklan'); ?>">
                            <i class="icon-angle-right"></i>
                            Sumber Iklan
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "jacket-size" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/jacket-size'); ?>">
                            <i class="icon-angle-right"></i>
                            Jacket Size
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "document-checklist" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/master/document-checklist'); ?>">
                            <i class="icon-angle-right"></i>
                            Document Checklist
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "program-beasiswa" ){echo "open-default";} ?>">
                        <a href="javascript:void(0);">
                            <i class="icon-angle-right"></i>
                            Program Beasiswa
                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "program-beasiswa" && $this->uri->segment(4) == "jalur-prestasi-akademik"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/master/program-beasiswa/jalur-prestasi-akademik'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Jalur Prestasi Akademik
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "program-beasiswa" && $this->uri->segment(4) == "jalur-prestasi-akademik-umum"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/master/program-beasiswa/jalur-prestasi-akademik-umum'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Jalur Prestasi Akademik Umum
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='master' && $this->uri->segment(3) == "program-beasiswa" && $this->uri->segment(4) == "jalur-prestasi-bidang-or-seni"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/master/program-beasiswa/jalur-prestasi-bidang-or-seni'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Jalur Prestasi Olah Raga dan Kesenian
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="<?php if($this->uri->segment(2)=='distribusi-formulir'){echo "current open";} ?>">
                <a href="javascript:void(0);">
                    <i class="fa fa-list-alt"></i>
                    Distribusi Formulir
                </a>
                <ul class="sub-menu">
                    <li class="<?php if($this->uri->segment(2)=='distribusi-formulir' && $this->uri->segment(3) == "formulir-online" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/distribusi-formulir/formulir-online'); ?>">
                            <i class="icon-angle-right"></i>
                            Formulir Online
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='distribusi-formulir' && $this->uri->segment(3) == "formulir-offline" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/distribusi-formulir/formulir-offline'); ?>">
                            <i class="icon-angle-right"></i>
                            Formulir Offline
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa'){echo "current open";} ?>">
                <a href="#">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    Proses Calon Mahasiswa
                </a>
                <ul class="sub-menu">
                    <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == 'jadwal-ujian' ){echo "open-default";} ?>">
                        <a href="javascript:void(0);">
                            <!-- <a href="<?php echo base_url('admission/proses-calon-mahasiswa/set-jadwal-ujian'); ?>"> <i class="icon-angle-right"></i> -->
                            <i class="icon-angle-right"></i>
                            Ujian
                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "jadwal-ujian" && $this->uri->segment(4) == "set-ujian"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/proses-calon-mahasiswa/jadwal-ujian/set-ujian'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Set Ujian
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "jadwal-ujian" && $this->uri->segment(4) == "set-jadwal-ujian"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/proses-calon-mahasiswa/jadwal-ujian/set-jadwal-ujian'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Set Jadwal Ujian
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "jadwal-ujian" && $this->uri->segment(4) == "daftar-jadwal-ujian"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/proses-calon-mahasiswa/jadwal-ujian/daftar-jadwal-ujian'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Daftar Jadwal Ujian Calon Mahasiswa
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "jadwal-ujian" && $this->uri->segment(4) == "set-nilai-ujian"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/proses-calon-mahasiswa/jadwal-ujian/set-nilai-ujian'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Set Nilai Ujian
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "dokumen" ){echo "open-default";} ?>">
                        <a href="javascript:void(0);">
                            <i class="icon-angle-right"></i>
                            Dokumen
                        </a>
                        <ul class="sub-menu">
                            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "dokumen" && $this->uri->segment(4) == "dokumen-upload"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/proses-calon-mahasiswa/dokumen/dokumen-upload'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Dokumen Upload
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "dokumen" && $this->uri->segment(4) == "input-nilai-rapor"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/proses-calon-mahasiswa/dokumen/input-nilai-rapor'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Input Nilai Rapor
                                </a>
                            </li>
                            <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "dokumen" && $this->uri->segment(4) == "cancel-nilai-rapor"){echo "current";} ?>">
                                <a href="<?php echo base_url('admission/proses-calon-mahasiswa/dokumen/cancel-nilai-rapor'); ?>">
                                    <i class="icon-angle-right"></i>
                                    Cancel Nilai Rapor
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "set_tuition_fee" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/proses-calon-mahasiswa/set_tuition_fee'); ?>">
                            <i class="icon-angle-right"></i>
                            Set Tuition Fee
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "cicilan" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/proses-calon-mahasiswa/cicilan'); ?>">
                            <i class="icon-angle-right"></i>
                            Cicilan
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "checkdata-calon-mahasiswa" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/proses-calon-mahasiswa/checkdata-calon-mahasiswa'); ?>">
                            <i class="icon-angle-right"></i>
                            Proses
                        </a>
                    </li>
                    <li class="<?php if($this->uri->segment(2)=='proses-calon-mahasiswa' && $this->uri->segment(3) == "close_period" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/proses-calon-mahasiswa/close_period'); ?>">
                            <i class="icon-angle-right"></i>
                            Close Period
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php if($this->uri->segment(2)=='mastercalonmahasiswa'){echo "current open";} ?>">
                <a href="javascript:void(0);">
                    <i class="fa fa-user-circle"></i>
                    Master Calon Mahasiswa
                </a>
                <ul class="sub-menu">
                    <li class="<?php if($this->uri->segment(2)=='mastercalonmahasiswa' && $this->uri->segment(3) == "generate-nim" ){echo "current";} ?>">
                        <a href="<?php echo base_url('admission/mastercalonmahasiswa/generate-nim'); ?>">
                            <i class="icon-angle-right"></i>
                            Generate NIM
                        </a>
                    </li>
                </ul>
            </li>
            <li class="">
                <a href="#">
                    <i class="fa fa-exchange" aria-hidden="true"></i>
                    Koreksi Calon Mahasiswa
                </a>
            </li>
        </ul>
        <div class="sidebar-widget align-center">
            <div class="btn-group" data-toggle="buttons" id="theme-switcher">
                <label class="btn active">
                    <input type="radio" name="theme-switcher" data-theme="bright"><i class="fa fa-sun-o"></i> Bright
                </label>
                <label class="btn">
                    <input type="radio" name="theme-switcher" data-theme="dark"><i class="fa fa-moon-o"></i> Dark
                </label>
            </div>
        </div>
    </div>
    <div id="divider" class="resizeable"></div>
</div>
<!-- /Sidebar -->