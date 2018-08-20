<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'c_login';
$route['404_override'] = 'dashboard/c_dashboard/page404';
$route['translate_uri_dashes'] = FALSE;


$route['navigation/(:num)'] = 'c_departement/navigation/$1';
$route['profile'] = 'c_dashboard/profile';
$route['page404'] = 'dashboard/c_dashboard/page404';

// FROM PORTAL
$route['uath/__portal4SignIn'] = 'c_login/portal4SignIn';

$route['uath/authUserPassword'] = 'c_login/authUserPassword';
$route['auth/authGoogle'] = 'c_login/authGoogle';
// $route['auth/gen_pass'] = 'c_login/gen_pass';
$route['auth/logMeOut'] = 'c_login/logMeOut';
$route['sendmail'] = 'c_login/sendmail';
$route['callback'] = 'c_login/callback';



$route['db/(:any)'] = 'auth/c_auth/db/$1';
$route['foto'] = 'auth/c_auth/foto';
$route['migration-students'] = 'auth/c_auth/migrationStudent';
$route['rekap/(:num)'] = 'auth/c_rekap/rekap_/$1';



// === Dashboard ===
$route['dashboard'] = 'dashboard/c_dashboard';
$route['profile/(:any)'] = 'dashboard/c_dashboard/profile/$1';
$route['change-departement'] = 'dashboard/c_dashboard/change_departement';


// === Academic ===
$route['academic/curriculum'] = 'page/academic/c_kurikulum/kurikulum';
$route['academic/kurikulum-detail'] = 'page/academic/c_kurikulum/kurikulum_detail';
$route['academic/kurikulum/add-kurikulum'] = 'page/academic/c_kurikulum/add_kurikulum';
$route['academic/kurikulum/loadPageDetailMataKuliah'] = 'page/academic/c_kurikulum/loadPageDetailMataKuliah';

$route['academic/kurikulum/data-conf'] = 'page/academic/c_kurikulum/getDataConf';
//$route['academic/kurikulum/getClassGroup'] = 'page/academic/c_kurikulum/getClassGroup';


$route['academic/kurikulum-detail-mk'] = 'page/academic/c_kurikulum/kurikulum_detail_mk';
$route['academic/courses'] = 'page/academic/c_matakuliah/mata_kuliah';
$route['academic/dataTableMK'] = 'page/academic/c_matakuliah/dataTableMK';

$route['academic/tesdb'] = 'page/academic/c_tahun_akademik/tesdb';
$route['academic/academic-year'] = 'page/academic/c_tahun_akademik/tahun_akademik';
$route['academic/tahun-akademik-table'] = 'page/academic/c_tahun_akademik/tahun_akademik_table';
$route['academic/detail-tahun-akademik'] = 'page/academic/c_tahun_akademik/page_detail_tahun_akademik';
$route['academic/modal-tahun-akademik'] = 'page/academic/c_tahun_akademik/modal_tahun_akademik';
$route['academic/tahun-akademik/(:any)'] = 'page/academic/c_tahun_akademik/tahun_akademik_detail/$1';

$route['academic/tahun-akademik-detail'] = 'page/academic/c_tahun_akademik/tahun_akademik_detail2';
$route['academic/tahun-akademik-detail-date'] = 'page/academic/c_tahun_akademik/tahun_akademik_detail_date';

$route['academic/semester-antara'] = 'page/academic/c_semester_antara';
$route['academic/semester-antara/details/(:num)'] = 'page/academic/c_semester_antara/loadDetails/$1';


$route['academic/ketersediaan-dosen'] = 'page/academic/c_akademik/ketersediaan_dosen';
$route['academic/ModalKetersediaanDosen'] = 'page/academic/c_akademik/Modal_KetersediaanDosen';

$route['academic/timetables'] = 'page/academic/c_jadwal';

$route['academic/study-planning'] = 'page/academic/c_study_planning';

$route['academic/references'] = 'page/academic/C_reference';


// Jadwal Ujian
$route['academic/__setPageJadwalUjian'] = 'page/academic/c_jadwal_ujian/setPageJadwal';
$route['academic/__setPageJadwal'] = 'page/academic/c_jadwal/setPageJadwal';
$route['academic/exam-schedule'] = 'page/academic/c_jadwal_ujian';

// ---- Score ----
$route['academic/score'] =  'page/academic/c_score';
$route['academic/inputScore'] =  'page/academic/c_score/inputScore';

// --- Modal Academic ----
$route['academic/modal-tahun-akademik-detail-prodi'] = 'page/academic/c_akademik/modal_tahun_akademik_detail_prodi';
$route['academic/modal-tahun-akademik-detail-lecturer'] = 'page/academic/c_akademik/modal_tahun_akademik_detail_lecturer';

// ======= human-resources ======
$route['human-resources/lecturers'] = 'page/database/c_database/lecturers';
//$route['human-resources/employees'] = 'page/database/c_database/employees';
$route['human-resources/employees'] = 'page/hr/c_employees/employees';
$route['human-resources/employees/input-employees'] = 'page/hr/c_employees/input_employees';
$route['human-resources/employees/edit-employees/(:num)'] = 'page/hr/c_employees/edit_employees/$1';
$route['human-resources/upload_photo'] = 'page/hr/c_employees/upload_photo';

// ====== Database =====
$route['database/lecturers'] = 'page/database/c_database/lecturers';
$route['database/lecturer-details/(:any)'] = 'page/database/c_database/lecturersDetails/$1';
$route['database/loadpagelecturersDetails'] = 'page/database/c_database/loadpagelecturersDetails';

$route['database/students'] = 'page/database/c_database/students';
$route['database/loadPageStudents'] = 'page/database/c_database/loadPageStudents';
$route['database/showStudent'] = 'page/database/c_database/showStudent';
$route['database/employees'] = 'page/database/c_database/employees';
$route['database/employees/form_input_add'] = 'page/database/c_database/form_input_employees';
$route['database/employees/form_input_add/(:any)'] = 'page/database/c_database/form_input_employees/$1';
$route['database/employees/form_input_submit'] = 'page/database/c_database/form_input_submit_employees';
$route['database/employees/changestatus'] = 'page/database/c_database/changestatus';




$route['database/mentor-academic'] = 'page/database/c_database/mentor_academic';


// --- Presensi ---
$route['academic/attendance'] = 'page/academic/c_presensi';
$route['academic/loadPagePresensi'] = 'page/academic/c_presensi/loadPagePresensi';


// -- master student -- 
$route['academic/master/student'] = 'page/academic/c_m_student';
$route['academic/master/showStudent'] = 'page/academic/c_m_student/showStudent';
$route['academic/master/loadPageStudents'] = 'page/academic/c_m_student/loadPageStudents';
$route['academic/master/FormStudents'] = 'page/academic/c_m_student/FormStudents';
$route['academic/master/form_input_student/(:any)'] = 'page/academic/c_m_student/form_input_student';


// --- Admission ----
// --- Master ----

// test routes from db
require_once( BASEPATH .'database/DB.php' );
$db =& DB();
$query = $db->get('db_admission.cfg_sub_menu');
$result = $query->result();
foreach( $result as $row )
{
	$Slug = $row->Slug;
	$Slug = explode('/', $Slug);
	if (in_array('(:any)', $Slug)) {
	   $a = count($Slug) - 1;
	   $URI = '';
	   for ($i=0; $i < $a; $i++) { 
	   	$URI .= $Slug[$i].'/';
	   }
	   $route[ $URI.'(:any)' ] = $row->Controller;
	}
	elseif(in_array('(:num)', $Slug)) {
		$a = count($Slug) - 1;
		$URI = '';
		for ($i=0; $i < $a; $i++) { 
			$URI .= $Slug[$i].'/';
		}
		$route[ $URI.'(:num)' ] = $row->Controller;
	}
	else
	{
		$route[ $row->Slug ] = $row->Controller;
	}

}
// test routes from db

$route['admission/config/set-tgl-register-online'] = 'page/admission/c_master/page_set_tgl_register'; // db menu

$route['admission/master/data_cfg_deadline'] = 'page/admission/c_master/data_cfg_deadline';
$route['admission/master/modalform_set_tgl_register'] = 'page/admission/c_master/modalform_set_tgl_register';
$route['admission/master/modalform_set_tgl_register/save'] = 'page/admission/c_master/submit_cfg_deadline';

$route['admission/config/set-max-cicilan'] = 'page/admission/c_master/page_set_max_cicilan'; // db menu

$route['admission/master/modalform_set_max_cicilan'] = 'page/admission/c_master/modalform_set_max_cicilan';
$route['admission/master/modalform_set_max_cicilan/save'] = 'page/admission/c_master/submit_cfg_cicilan';
$route['admission/master/data_cfg_cicilan'] = 'page/admission/c_master/data_cfg_cicilan';
$route['admission/master-calon-mahasiswa/showAutoComplete'] = 'page/admission/c_master/load_data_autocomplete_calon_mahasiswa';

$route['admission/master/modalform_sekolah'] = 'page/admission/c_master/modalform_sekolah';
$route['admission/master/modalform_sekolah/save'] = 'page/admission/c_master/submit_sekolah';


$route['admission/config/sdaerah/master-sma'] = 'page/admission/c_master/sma'; // db menu
$route['admission/config/sdaerah/master-sma/(:any)'] = 'page/admission/c_master/sma/$1'; // db menu
$route['admission/config/sdaerah/integration'] = 'page/admission/c_master/sma_integration';

$route['admission/master-sma/table'] = 'page/admission/c_master/sma_table';

$route['admission/config/set-email'] = 'page/admission/c_master/config_set_email'; // db menu
$route['admission/master-config/testing_email'] = 'page/admission/c_master/testing_email';
$route['admission/master-config/save_email'] = 'page/admission/c_master/save_email';
$route['admission/config/total-account'] = 'page/admission/c_master/total_account'; // db menu
$route['admission/master-config/loadTableTotalAccount'] = 'page/admission/c_master/load_table_total_account';
$route['admission/master-config/modalform/(:any)'] = 'page/admission/c_master/modalform/$1';
$route['admission/master-config/submit_count_account'] = 'page/admission/c_master/submit_count_account';

$route['admission/config/email-to'] = 'page/admission/c_master/email_to'; // db menu

$route['admission/master-config/loadTableEmailTo'] = 'page/admission/c_master/load_table_email_to';
$route['admission/master-config/submit_email_to'] = 'page/admission/c_master/submit_email_to';
$route['admission/master-config/lama-pembayaran'] = 'page/admission/c_master/lama_pembayaran';
$route['admission/master-config/loadTableMaster/(:any)'] = 'page/admission/c_master/load_table_master/$1';
$route['admission/master-config/submit_lama_pembayaran'] = 'page/admission/c_master/submit_lama_pembayaran';
$route['admission/config/harga-formulir/online'] = 'page/admission/c_master/harga_formulir_online'; // db menu
$route['admission/master-config/submit_harga_formulir_online'] = 'page/admission/c_master/submit_harga_formulir_online'; // db menu
$route['admission/config/harga-formulir/offline'] = 'page/admission/c_master/harga_formulir_offline';
$route['admission/master-config/submit_harga_formulir_offline'] = 'page/admission/c_master/submit_harga_formulir_offline';
$route['admission/config/sdaerah/wilayah'] = 'page/admission/c_master/global_wilayah'; // dbmenu
$route['admission/master-config/loadTableMasterNoAction/(:any)'] = 'page/admission/c_master/loadTableMasterNoAction/$1';
$route['admission/master/jenis-tempat-tinggal'] = 'page/admission/c_master/jenis_tempat_tinggal'; // db menu
$route['admission/master-config/submit_jenis_tempat_tinggal'] = 'page/admission/c_master/submit_jenis_tempat_tinggal';
$route['admission/master/pendapatan'] = 'page/admission/c_master/pendapatan'; // db menu
$route['admission/master-config/submit_Pendapatan'] = 'page/admission/c_master/submit_pendapatan';
$route['admission/config/set-print-label'] = 'page/admission/c_master/set_print_label'; // db menu
$route['admission/master-config/testing_print_label_token'] = 'page/admission/c_master/testing_print_label_token';
$route['admission/master-config/save_set_print_label'] = 'page/admission/c_master/save_set_print_label';



$route['admission/master/agama'] = 'page/admission/c_master/agama'; // db menu

$route['admission/master-global/loadTableMasterAgama'] = 'page/admission/c_master/load_table_master_agama';
$route['admission/master/tipe-sekolah'] = 'page/admission/c_master/tipe_sekolah'; // db menu
$route['admission/master-global/loadTableMasterTipeSekolah'] = 'page/admission/c_master/load_table_tipe_sekolah';
$route['admission/master/document-checklist'] = 'page/admission/c_master/document_checklist'; // db menu
$route['admission/master-registration/submit_document_checklist'] = 'page/admission/c_master/submit_document_checklist';

$route['admission/config/number-formulir/online'] = 'page/admission/c_master/formulir_online'; // db menu

$route['admission/master-registration/loadDataFormulirOnline'] = 'page/admission/c_master/loadDataFormulirOnline';
$route['admission/master-registration/getJsonFormulirOnline'] = 'page/admission/c_master/get_json_formulir_online';
$route['admission/master-registration/GenerateFormulirOnline'] = 'page/admission/c_master/generate_formulir_online';

$route['admission/config/number-formulir/offline'] = 'page/admission/c_master/formulir_offline';

$route['admission/master-registration/loadDataFormulirOffline'] = 'page/admission/c_master/loadDataFormulirOffline';
$route['admission/master-registration/getJsonFormulirOffline'] = 'page/admission/c_master/get_json_formulir_offline';
$route['admission/master-registration/GenerateFormulirOffline'] = 'page/admission/c_master/generate_formulir_offline';
$route['admission/master/jacket-size'] = 'page/admission/c_master/jacket_size'; // db menu
$route['admission/master-register/submit_jacket_size'] = 'page/admission/c_master/submit_jacket_size';
$route['admission/master/jurusan-sekolah'] = 'page/admission/c_master/jurusan_sekolah'; // db menu
$route['admission/master-config/submit_jurusan_sekolah'] = 'page/admission/c_master/submit_jurusan_sekolah';
$route['admission/master/ujian-masuk-per-prody'] = 'page/admission/c_master/ujian_masuk_per_prody'; // db menu
$route['admission/master-registration/ujian-masuk-per-prody/modalform'] = 'page/admission/c_master/modalform_ujian_masuk_per_prody';
$route['admission/master-registration/ujian-masuk-per-prody/loadTable'] = 'page/admission/c_master/table_ujian_masuk_per_prody';
$route['admission/master-registration/ujian-masuk-per-prody/submit'] = 'page/admission/c_master/submit_ujian_masuk_per_prody';

$route['admission/config/virtual-account/page-create-va'] = 'page/admission/c_master/page_create_va'; // db menu

$route['admission/master-registration/generate_va'] = 'page/admission/c_master/generate_va';
$route['admission/master-registration/loadDataVA-available'] = 'page/admission/c_master/loadDataVA_available';
$route['admission/master/event'] = 'page/admission/c_master/event'; // db menu
$route['admission/master-registration/modalform_event'] = 'page/admission/c_master/modalform_event';
$route['admission/master-registration/event/table_event'] = 'page/admission/c_master/table_event';
$route['admission/master-registration/modalform_event/save'] = 'page/admission/c_master/modalform_event_save';
$route['admission/master/sumber-iklan'] = 'page/admission/c_master/sumber_iklan'; // db menu
$route['admission/master-register/submit_source_from_event'] = 'page/admission/c_master/submit_source_from_event';
$route['admission/config/virtual-account/page-recycle-va'] = 'page/admission/c_master/page_recycle_va'; // db menu
$route['admission/master-registration/loadDataVA-deleted/(:num)'] = 'page/admission/c_master/loadDataVA_deleted/$1';
$route['admission/master-registration/virtual-account/page-recycle-va/submit_recycle_va'] = 'page/admission/c_master/submit_recycle_va';
$route['admission/master/program-beasiswa/jalur-prestasi-akademik'] = 'page/admission/c_master/jalur_prestasi_akademik'; // db menu
$route['admission/master-registration/jpa/table_jpa'] = 'page/admission/c_master/table_jpa';
$route['admission/master-registration/modalform_jpa'] = 'page/admission/c_master/modalform_jpa';
$route['admission/master-registration/submit_jpa'] = 'page/admission/c_master/submit_jpa';
$route['admission/master/program-beasiswa/jalur-prestasi-akademik-umum'] = 'page/admission/c_master/jalur_prestasi_akademik_umum'; // db menu
$route['admission/master-registration/modalform_jpau'] = 'page/admission/c_master/modalform_jpau';
$route['admission/master-registration/jpau/table_jpau'] = 'page/admission/c_master/table_jpau';
$route['admission/master-registration/submit_jpau'] = 'page/admission/c_master/submit_jpau';
$route['admission/master/program-beasiswa/jalur-prestasi-bidang-or-seni'] = 'page/admission/c_master/jalur_prestasi_bidang_or_seni';
$route['admission/master-registration/jpok/table_jpok'] = 'page/admission/c_master/table_jpok';
$route['admission/master-registration/modalform_jpok'] = 'page/admission/c_master/modalform_jpok';
$route['admission/master-registration/submit_jpok'] = 'page/admission/c_master/submit_jpok';




$route['admission/master/sales-koordinator-wilayah'] = 'page/admission/c_master/sales_koordinator_wilayah_page'; // db menu
$route['admission/master-registration/modalform_sales_koordinator'] = 'page/admission/c_master/sales_koordinator_wilayah_modal_form';
$route['admission/master-registration/modalform_sales_koordinator/save'] = 'page/admission/c_master/modalform_sales_koordinator_save';
$route['admission/master-registration/sales_koordinator/pagination/(:num)'] = 'page/admission/c_master/sales_koordinator_pagination/$1';
$route['admission/master-registration/DataFormulirOffline/downloadPDFToken'] = 'page/admission/c_master/downloadPDFToken';
$route['admission/config/upload-pdf-per-pengumuman'] = 'page/admission/c_master/upload_pengumuman';

$route['fileGet/(:any)'] = 'api/c_global/fileGet/$1';
$route['download/(:any)'] = 'api/c_global/download/$1';
$route['download_template/(:any)'] = 'api/c_global/download_template/$1';
$route['download_anypath'] = 'api/c_global/download_anypath';
$route['fileGetAny/(:any)'] = 'api/c_global/fileGetAny/$1';
$route['autocompleteAllUser'] = 'api/c_global/autocompleteAllUser';



$route['admission/master-registration/biaya-kuliah'] = 'page/admission/c_master/biaya_kuliah';
$route['admission/config/menu-previleges'] = 'page/admission/c_master/menu_previleges'; // db menu
$route['admission/master-config/modalform_previleges'] = 'page/admission/c_master/modal_form_previleges';
$route['admission/master-config/menu-previleges/get_menu'] = 'page/admission/c_master/get_menu';
$route['admission/master-config/menu-previleges/get_menu/save'] = 'page/admission/c_master/get_menu_save_menu';
$route['admission/master-config/menu-previleges/get_submenu/save'] = 'page/admission/c_master/get_submenu_save_menu';
$route['admission/master-config/menu-previleges/get_submenu/show'] = 'page/admission/c_master/get_submenu_show';
$route['admission/master-config/menu-previleges/get_submenu/update'] = 'page/admission/c_master/get_submenu_update';
$route['admission/master-config/menu-previleges/get_submenu/delete'] = 'page/admission/c_master/get_submenu_delete';
$route['admission/master-config/autocompleteuser'] = 'page/admission/c_master/autocompleteuser';
$route['admission/master-config/menu-previleges/get_submenu_by_menu'] = 'page/admission/c_master/get_submenu_by_menu';
$route['admission/master-config/menu-previleges/user/save'] = 'page/admission/c_master/save_user_previleges';
$route['admission/master-config/menu-previleges/get_previleges_user/show'] = 'page/admission/c_master/get_previleges_user_show';
$route['admission/master-config/menu-previleges/previleges_user/update'] = 'page/admission/c_master/previleges_user_update';
$route['admission/master-config/menu-previleges/previleges_user/delete'] = 'page/admission/c_master/previleges_user_delete';


$route['admission/dashboard'] = 'page/admission/c_admission/dashboard';
$route['readNotificationDivision'] = 'dashboard/c_dashboard/readNotificationDivision';


// proses
$route['admission/proses-calon-mahasiswa/dokumen/dokumen-upload'] = 'page/admission/c_admission/verifikasi_dokumen_calon_mahasiswa'; // db menu
$route['admission/proses-calon-mahasiswa/verifikasi-dokument/register_document_table/pagination/(:num)'] = 'page/admission/c_admission/pagination_calon_mahasiswa/$1';
$route['admission/proses-calon-mahasiswa/verifikasi-dokument/proses_document'] = 'page/admission/c_admission/proses_document';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-jadwal-ujian'] = 'page/admission/c_admission/set_jadwal_ujian';
$route['admission/proses-calon-mahasiswa/set-jadwal-ujian/load_table'] = 'page/admission/c_admission/set_jadwal_ujian_load_table';
$route['admission/proses-calon-mahasiswa/set-jadwal-ujian/load_table_getjsonApi'] = 'page/admission/c_admission/set_jadwal_ujian_load_table_getJsonApi';
$route['admission/proses-calon-mahasiswa/set-jadwal-ujian/save'] = 'page/admission/c_admission/set_jadwal_ujian_save';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/daftar-jadwal-ujian'] = 'page/admission/c_admission/daftar_jadwal_ujian';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/daftar-jadwal-ujian/load-data-now'] = 'page/admission/c_admission/daftar_jadwal_ujian_load_data_now';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/daftar-jadwal-ujian/pagination/(:num)'] = 'page/admission/c_admission/daftar_jadwal_ujian_load_data_paging/$1';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-nilai-ujian'] = 'page/admission/c_admission/set_nilai_ujian';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-nilai-ujian/pagination/(:num)'] = 'page/admission/c_admission/set_nilai_ujian_load_data_paging/$1';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-nilai-ujian/save'] = 'page/admission/c_admission/set_nilai_ujian_save';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-ujian'] = 'page/admission/c_admission/set_ujian'; // db menu
$route['admission/proses-calon-mahasiswa/loadData_calon_mahasiswa/(:num)'] = 'page/admission/c_admission/loadData_calon_mahasiswa/$1';
$route['admission/proses-calon-mahasiswa/submit_ikut_ujian'] = 'page/admission/c_admission/submit_ikut_ujian';
$route['admission/proses-calon-mahasiswa/dokumen/input-nilai-rapor'] = 'page/admission/c_admission/input_nilai_rapor'; // db menu

$route['admission/proses-calon-mahasiswa/set-nilai-rapor/pagination/(:num)'] = 'page/admission/c_admission/set_nilai_rapor_load_data_paging/$1';
$route['admission/proses-calon-mahasiswa/set-nilai-rapor/save'] = 'page/admission/c_admission/set_nilai_rapor_save';
$route['admission/proses-calon-mahasiswa/dokumen/cancel-nilai-rapor'] = 'page/admission/c_admission/cancel_nilai_lapor'; // db menu
$route['admission/proses-calon-mahasiswa/loaddata_nilai_calon_mahasiswa/(:num)'] = 'page/admission/c_admission/loaddata_nilai_calon_mahasiswa/$1';
$route['admission/proses-calon-mahasiswa/submit_cancel_nilai_rapor'] = 'page/admission/c_admission/submit_cancel_nilai_rapor';
$route['admission/proses-calon-mahasiswa/set_tuition_fee'] = 'page/admission/c_admission/set_tuition_fee'; // db menu
$route['admission/proses-calon-mahasiswa/set_tuition_fee/input/(:num)'] = 'page/admission/c_admission/set_tuition_fee_input/$1';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/save'] = 'page/admission/c_admission/set_tuition_fee_save';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/delete/(:num)'] = 'page/admission/c_admission/set_tuition_fee_delete/$1';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/delete_data'] = 'page/admission/c_admission/set_tuition_fee_delete_data';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/approved/(:num)'] = 'page/admission/c_admission/set_tuition_fee_approved/$1';
$route['admission/proses-calon-mahasiswa/cicilan'] = 'page/admission/c_admission/cicilan'; // db menu
$route['admission/proses-calon-mahasiswa/cicilan_data/(:num)'] = 'page/admission/c_admission/cicilan_data/$1';
$route['admission/proses-calon-mahasiswa/submit_edit_deadline_cicilan'] = 'page/admission/c_admission/submit_edit_deadline_cicilan';
$route['admission/proses-calon-mahasiswa/checkdata-calon-mahasiswa'] = 'page/admission/c_admission/page_data_calon_mahasiswa'; // db menu
$route['admission/proses-calon-mahasiswa/data-calon-mhs/(:num)'] = 'page/admission/c_admission/data_calon_mahasiswa/$1';
$route['admission/detailPayment'] = 'page/admission/c_admission/detailPayment';

// --- update admisi setelah request 20180731
$route['admission/proses-calon-mahasiswa/set_input_tuition_fee_submit'] = 'page/admission/c_admission/set_input_tuition_fee_submit';




$route['admission/distribusi-formulir/formulir-offline'] = 'page/admission/c_admission/distribusi_formulir_offline'; // db menu
$route['admission/distribusi-formulir/formulir-offline/pagination/(:num)'] = 'page/admission/c_admission/pagination_formulir_offline/$1';
$route['admission/distribusi-formulir/formulir-offline/submit_sellout'] = 'page/admission/c_admission/submit_sellout_formulir_offline/$1';

$route['admission/distribusi-formulir/formulir-online'] = 'page/admission/c_admission/distribusi_formulir_online'; // db menu
$route['admission/distribusi-formulir/formulir-online/pagination/(:num)'] = 'page/admission/c_admission/pagination_formulir_online/$1';
$route['admission/distribusi-formulir/formulir-offline/save'] = 'page/admission/c_admission/formulir_offline_sale_save';
$route['admission/distribusi-formulir/formulir-offline/selectPIC'] = 'page/admission/c_admission/formulir_offline_salect_PIC';

$route['admission/mastercalonmahasiswa/generate-nim'] = 'page/admission/c_admission/generatenim';
$route['admission/mastercalonmahasiswa/submit_import_excel_File_generate_nim'] = 'page/admission/c_admission/submit_import_excel_File_generate_nim';




// ---Finance----
$route['finance/master/tagihan-mhs'] =  'page/finance/c_tuition_fee/tuition_fee';
$route['finance/master/modal-tagihan-mhs'] =  'page/finance/c_tuition_fee/modal_tagihan_mhs';
$route['finance/master/modal-tagihan-mhs-submit'] =  'page/finance/c_tuition_fee/modal_tagihan_mhs_submit';
$route['finance/master/edited-tagihan-mhs-submit'] =  'page/finance/c_tuition_fee/edited_tagihan_mhs_submit';
$route['finance/master/deleted-tagihan-mhs-submit'] =  'page/finance/c_tuition_fee/deleted_tagihan_mhs_submit';
$route['finance/master/mahasiswa'] =  'page/finance/c_finance/mahasiswa';
$route['finance/master/import_price_list_mhs'] =  'page/finance/c_finance/import_price_list_mhs';
$route['finance/master/import_beasiswa_mahasiswa'] =  'page/finance/c_finance/import_beasiswa_mahasiswa';
$route['finance/master/mahasiswa_list/(:num)'] =  'page/finance/c_finance/mahasiswa_list/$1';
$route['finance/master/edited-bea-bpp'] =  'page/finance/c_finance/edited_bea_bpp';
$route['finance/master/edited-bea-credit'] =  'page/finance/c_finance/edited_bea_credit';
$route['finance/master/edited-pay-cond'] =  'page/finance/c_finance/edited_pay_cond';
$route['finance/master/discount'] =  'page/finance/c_finance/page_master_discount';
$route['finance/master/load_discount'] =  'page/finance/c_finance/load_discount';
$route['finance/master/modalform_discount'] =  'page/finance/c_finance/modalform_discount';
$route['finance/master/sbmt_discount'] =  'page/finance/c_finance/sbmt_discount';



$route['finance/admission/penerimaan-pembayaran/formulir-registration/online'] =  'page/finance/c_finance/formulir_registration_online_page';
$route['finance/confirmed-verifikasi-pembayaran-registration_online'] =  'page/finance/c_finance/confirmed_verfikasi_pembayaran_registration_online';
$route['finance/admission/penerimaan-pembayaran/formulir-registration/offline'] =  'page/finance/c_finance/formulir_registration_offline_page';
$route['finance/admission/approved/nilai-rapor'] =  'page/finance/c_finance/nilai_rapor_page';
$route['finance/approved/loaddata_nilai_calon_mahasiswa_verified/(:num)'] =  'page/finance/c_finance/loaddata_nilai_calon_mahasiswa_verified/$1';
$route['finance/approved/submit_approved_nilai_rapor'] =  'page/finance/c_finance/submit_approved_nilai_rapor';
$route['finance/admission/approved/tuition-fee'] =  'page/finance/c_finance/tuition_fee';
$route['finance/approved/tuition-fee/approve/(:num)'] =  'page/finance/c_finance/tuition_fee_approve/$1';
$route['finance/approved/tuition-fee/approve_save'] =  'page/finance/c_finance/approve_save';
$route['finance/approved/tuition-fee/approved/(:num)'] =  'page/finance/c_finance/tuition_fee_approved/$1';
$route['finance/tagihan-mhs/set-tagihan-mhs'] =  'page/finance/c_finance/page_set_tagihan_mhs';
$route['finance/get_tagihan_mhs/(:num)'] =  'page/finance/c_finance/get_tagihan_mhs/$1';
$route['finance/submit_tagihan_mhs'] =  'page/finance/c_finance/submit_tagihan_mhs';
$route['finance/tagihan-mhs/cek-tagihan-mhs/(:num)'] =  'page/finance/c_finance/page_cek_tagihan_mhs/$1';
$route['finance/tagihan-mhs/cek-tagihan-mhs'] =  'page/finance/c_finance/page_cek_tagihan_mhs';
$route['finance/get_created_tagihan_mhs/(:num)'] =  'page/finance/c_finance/get_created_tagihan_mhs/$1';
$route['finance/get_created_tagihan_mhs_not_approved/(:num)'] =  'page/finance/c_finance/get_created_tagihan_mhs_not_approved/$1';
$route['finance/approved_created_tagihan_mhs'] =  'page/finance/c_finance/approved_created_tagihan_mhs';
$route['finance/unapproved_created_tagihan_mhs'] =  'page/finance/c_finance/unapproved_created_tagihan_mhs';
$route['finance/tagihan-mhs/cancel-tagihan-mhs'] =  'page/finance/c_finance/cancel_tagihan_mhs';
$route['finance/cancel_created_tagihan_mhs'] =  'page/finance/c_finance/cancel_created_tagihan_mhs';
$route['finance/tagihan-mhs/set-cicilan-tagihan-mhs'] =  'page/finance/c_finance/set_cicilan_tagihan_mhs';
$route['finance/tagihan-mhs/set-cicilan-tagihan-mhs/submit'] =  'page/finance/c_finance/set_cicilan_tagihan_mhs_submit';
$route['finance/tagihan-mhs/edit-cicilan-tagihan-mhs'] =  'page/finance/c_finance/edit_cicilan_tagihan_mhs';
$route['finance/tagihan-mhs/set-edit-cicilan-tagihan-mhs/submit'] =  'page/finance/c_finance/edit_cicilan_tagihan_mhs_submit';
$route['finance/tagihan-mhs/set-delete-cicilan-tagihan-mhs/submit'] =  'page/finance/c_finance/delete_cicilan_tagihan_mhs_submit';
$route['finance/tagihan-mhs/penerimaan-tagihan-mhs'] =  'page/finance/c_finance/penerimaan_tagihan_mhs';
$route['finance/get_pembayaran_mhs/(:num)'] =  'page/finance/c_finance/get_pembayaran_mhs/$1';
//$route['finance/export_excel'] =  'page/finance/c_finance/export_excel';
$route['finance/export_excel'] =  'C_save_to_excel/export_excel_payment_received';
$route['finance/check-va'] =  'page/finance/c_finance/check_va';
$route['finance/check-va-cari'] =  'page/finance/c_finance/check_va_cari';

$route['finance/tagihan-mhs/submit_import_price_list_mhs'] =  'page/finance/c_finance/submit_import_price_list_mhs';
$route['finance/tagihan-mhs/list-telat-bayar'] =  'page/finance/c_finance/list_telat_bayar';
$route['finance/get_list_telat_bayar/(:num)'] =  'page/finance/c_finance/get_list_telat_bayar/$1';
$route['finance/edit_telat_bayar/(:any)'] =  'page/finance/c_finance/edit_telat_bayar/$1';
$route['finance/tagihan-mhs/import_pembayaran_manual'] =  'page/finance/c_finance/import_pembayaran_manual';
$route['finance/tagihan-mhs/submit_import_pembayaran_manual'] =  'page/finance/c_finance/submit_import_pembayaran_manual';

$route['finance/tagihan-mhs/import_pembayaran_lain'] =  'page/finance/c_finance/import_pembayaran_lain';
$route['finance/tagihan-mhs/submit_import_pembayaran_lain'] =  'page/finance/c_finance/submit_import_pembayaran_lain';




$route['finance/tagihan-mhs/submit_import_beasiswa_mahasiswa'] =  'page/finance/c_finance/submit_import_beasiswa_mahasiswa';
$route['finance/download-log-va'] =  'page/finance/c_finance/download_log_va';
$route['finance/listfile_va'] =  'page/finance/c_finance/listfile_va';



// ---global---
$route['loadDataRegistrationBelumBayar'] =  'api/C_global/loadDataRegistrationBelumBayar';
$route['loadDataRegistrationTelahBayar'] =  'api/C_global/load_data_registration_telah_bayar';
$route['loadDataRegistrationFormulirOffline'] =  'api/C_global/load_data_registration_formulir_offline';
$route['get_detail_cicilan_fee_admisi'] =  'api/C_global/get_detail_cicilan_fee_admisi';


// ---- Save to PDF ---
$route['save2pdf/schedule-pdf'] =  'c_save_to_pdf/schedulePDF';

$route['save2pdf/listStudentsFromCourse'] =  'c_save_to_pdf/listStudentsFromCourse';

$route['save2pdf/exam-layout'] =  'c_save_to_pdf/exam_layout';
$route['save2pdf/draft-questions'] =  'c_save_to_pdf/draft_questions';
$route['save2pdf/answer-sheet'] =  'c_save_to_pdf/answer_sheet';
$route['save2pdf/news-event'] =  'c_save_to_pdf/news_event';
$route['save2pdf/attendance-list'] =  'c_save_to_pdf/attendance_list';

$route['save2pdf/report-uts'] =  'c_save_to_pdf/report_uts';

$route['save2pdf/getpdfkwitansi/(:any)'] =  'c_save_to_pdf/getpdfkwitansi/$1';

// ---- Save to EXCEL
$route['save2excel/test'] =  'c_save_to_excel/test2';


// ====== API ======
$route['api/__getKurikulumByYear'] = 'api/c_api/getKurikulumByYear';
$route['api/__getBaseProdi'] = 'api/c_api/getProdi';
$route['api/__getBaseProdiSelectOption'] = 'api/c_api/getProdiSelectOption';
$route['api/__getBaseProdiSelectOptionAll'] = 'api/c_api/getProdiSelectOptionAll';
$route['api/__geteducationLevel'] = 'api/c_api/geteducationLevel';

$route['api/__getMKByID'] = 'api/c_api/getMKByID';
$route['api/__getSemester'] = 'api/c_api/getSemester';
$route['api/__getLecturer'] = 'api/c_api/getLecturer';
$route['api/__getStudents'] = 'api/c_api/getStudents';
$route['api/__getAllMK'] = 'api/c_api/getAllMK';

$route['api/__getEmployees'] = 'api/c_api/getEmployees';
$route['api/employees/searchnip/(:any)'] = 'api/c_api/searchnip_employees/$1';

$route['api/__getEmployeesHR'] = 'api/c_api/getEmployeesHR';

$route['api/__setLecturersAvailability'] = 'api/c_api/setLecturersAvailability';
$route['api/__setLecturersAvailabilityDetail/(:any)'] = 'api/c_api/setLecturersAvailabilityDetail/$1';

$route['api/__changeTahunAkademik'] = 'api/c_api/changeTahunAkademik';

$route['api/__insertKurikulum'] = 'api/c_api/insertKurikulum';
$route['api/__getKurikulumSelectOption'] = 'api/c_api/getKurikulumSelectOption';


$route['api/__getDosenSelectOption'] = 'api/c_api/getDosenSelectOption';
$route['api/__crudYearAcademic'] = 'api/c_api/crudYearAcademic';

$route['api/__crudKurikulum'] = 'api/c_api/crudKurikulum';
$route['api/__crudDetailMK'] = 'api/c_api/crudDetailMK';

$route['api/__getdetailKurikulum'] = 'api/c_api/getdetailKurikulum';
$route['api/__genrateMKCode'] = 'api/c_api/genrateMKCode';
$route['api/__cekMKCode'] = 'api/c_api/cekMKCode';

$route['api/__crudMataKuliah'] = 'api/c_api/crudMataKuliah';

$route['api/__crudTahunAkademik'] = 'api/c_api/crudTahunAkademik';
$route['api/__crudStatusStudents'] = 'api/c_api/crudStatusStudents';

$route['api/__crudDataDetailTahunAkademik'] = 'api/c_api/crudDataDetailTahunAkademik';

$route['api/__getAcademicYearOnPublish'] = 'api/c_api/getAcademicYearOnPublish';
$route['api/__getTimePerCredits'] = 'api/c_api/getTimePerCredits';

$route['api/__crudSchedule'] = 'api/c_api/crudSchedule';
$route['api/__getSchedulePerDay'] = 'api/c_api/getSchedulePerDay';
$route['api/__getSchedulePerSemester'] = 'api/c_api/getSchedulePerSemester';

$route['api/__crudProgramCampus'] = 'api/c_api/crudProgramCampus';
$route['api/__crudSemester'] = 'api/c_api/crudSemester';

$route['api/__getAllStudents'] = 'api/c_api/getAllStudents';

$route['api/__crudeStudent'] = 'api/c_api/crudeStudent';
$route['api/__getClassGroup'] = 'api/c_api/getClassGroup';
$route['api/__getClassGroupParalel'] = 'api/c_api/getClassGroupParalel';

$route['api/__crudClassroom'] = 'api/c_api/crudClassroom';
$route['api/__crudGrade'] = 'api/c_api/crudGrade';
$route['api/__crudRangeCredits'] = 'api/c_api/crudRangeCredits';
//$route['api/__crudStdSemester'] = 'api/c_api/crudStdSemester';
$route['api/__crudTimePerCredit'] = 'api/c_api/crudTimePerCredit';
$route['api/__checkSchedule'] = 'api/c_api/checkSchedule';

$route['api/__crudCourseOfferings'] = 'api/c_api/crudCourseOfferings';
$route['api/__crudLecturer'] = 'api/c_api/crudLecturer';
$route['api/__crudStudyPlanning'] = 'api/c_api/crudStudyPlanning';
$route['api/__getClassGroupAutoComplete/(:num)'] = 'api/c_api/getClassGroupAutoComplete/$1';
$route['api/__getScheduleIDByClassGroup/(:num)/(:any)'] = 'api/c_api/getScheduleIDByClassGroup/$1/$2';


$route['api/__filterStudents'] = 'api/c_api/filterStudents';
$route['api/__getFormulirOfflineAvailable'] = 'api/c_api/getFormulirOfflineAvailable';
$route['api/__getAutoCompleteSchool'] = 'api/c_api/AutoCompleteSchool';
$route['api/__getSumberIklan'] = 'api/c_api/getSumberIklan';
$route['api/__getPriceFormulirOffline'] = 'api/c_api/getPriceFormulirOffline';
$route['api/__getEvent'] = 'api/c_api/getEvent';
$route['api/__getDocument'] = 'api/c_api/getDocument';


// get data SMA dan SMK per Wilayah
$route['api/__insertWilayahURLJson'] = 'api/c_api/insertWilayahURLJson';
$route['api/__insertSchoolURLJson'] = 'api/c_api/insertSchoolURLJson';
$route['api/__getWilayahURLJson'] = 'api/c_api/getWilayahURLJson';
$route['api/__getSMAWilayah'] = 'api/c_api/getSMAWilayah';

// get data untuk finance
$route['api/__getDataRegisterBelumBayar'] = 'api/c_api/getDataRegisterBelumBayar';
$route['api/__getDataRegisterTelahBayar'] = 'api/c_api/getDataRegisterTelahBayar';
$route['api/__cek_deadlineBPPSKS'] = 'api/c_api/cek_deadlineBPPSKS';
$route['api/__cek_deadline_paymentNPM'] = 'api/c_api/cek_deadline_paymentNPM';


$route['api/__crudTuitionFee'] = 'api/c_api/crudTuitionFee';
$route['api/__getEmployees/(:any)/(:any)'] = 'api/c_api/getEmployeesBy/$1/$2';

$route['api/__crudJadwalUjian'] = 'api/c_api/crudJadwalUjian';
$route['api/__crudEmployees'] = 'api/c_api/crudEmployees';
$route['api/__crudScore'] = 'api/c_api/crudScore';
$route['api/__crudAttendance'] = 'api/c_api/crudAttendance';
$route['api/__crudScheduleExchange'] = 'api/c_api/crudScheduleExchange';
$route['api/__crudLimitCredit'] = 'api/c_api/crudLimitCredit';

$route['rest/__checkDateKRS'] = 'api/c_rest/checkDateKRS';
$route['rest/__getDetailKRS'] = 'api/c_rest/getDetailKRS';

$route['rest/__geTimetable'] = 'api/c_rest/geTimetable';
$route['rest/__getKSM'] = 'api/c_rest/getKSM';
$route['rest/__cek_deadline_paymentNPM'] = 'api/c_rest/cek_deadline_paymentNPM';




$route['api/__getProvinsi'] = 'api/c_api/getProvinsi';
$route['api/__getRegionByProv'] = 'api/c_api/getRegionByProv';
$route['api/__getDistrictByRegion'] = 'api/c_api/getDistrictByRegion';
$route['api/__getTypeSekolah'] = 'api/c_api/getTypeSekolah';
$route['api/__getNotification'] = 'api/c_api/getNotification';
$route['api/__getBasePaymentTypeSelectOption'] = 'api/c_api/getBasePaymentTypeSelectOption';
$route['api/__getBaseDiscountSelectOption'] = 'api/c_api/getBaseDiscountSelectOption';


$route['api/__getSMAWilayahApproval'] = 'api/c_api/getSMAWilayahApproval';
$route['api/__getNotification_divisi'] = 'api/c_api/getNotification_divisi';
$route['api/__getAgama'] = 'api/c_api/getAgama';
$route['api/__getDivision'] = 'api/c_api/getDivision';
$route['api/__getPosition'] = 'api/c_api/getPosition';
$route['api/__getStatusEmployee'] = 'api/c_api/getStatusEmployee';

$route['api/__crudKRSOnline'] = 'api/c_api/crudKRSOnline';

$route['api/__crudCombinedClass'] = 'api/c_api/crudCombinedClass';
$route['api/__getSimpleSearch'] = 'api/c_api/getSimpleSearch';

//v_reservation
$route['api/__m_equipment_additional'] = 'api/c_api/m_equipment_additional';
$route['api/get_time_opt_reservation'] = 'api/c_api/get_time_opt_reservation';
$route['api/__m_additional_personel'] = 'api/c_api/m_additional_personel';
$route['api/__room_equipment'] = 'api/c_api/room_equipment';
$route['api/__checkBentrokScheduleAPI'] = 'api/c_api/checkBentrokScheduleAPI';
$route['api/__crudClassroomVreservation'] = 'api/c_api/crudClassroomVreservation';


// for inject //
$route['testadi'] = 'dashboard/c_dashboard/testadi';
$route['testadi2'] = 'c_login/testadi2';

$route['__resetPasswordUser'] = 'c_login/resetPasswordUser';
// for inject //

//Venue Reservation // 
$route['venue_reservation'] = 'page/vreservation/c_global';
$query = $db->get('db_reservation.cfg_sub_menu');
$result = $query->result();
foreach( $result as $row )
{
	$Slug = $row->Slug;
	$Slug = explode('/', $Slug);
	if (in_array('(:any)', $Slug)) {
	   $a = count($Slug) - 1;
	   $URI = '';
	   for ($i=0; $i < $a; $i++) { 
	   	$URI .= $Slug[$i].'/';
	   }
	   $route[ $URI.'(:any)' ] = $row->Controller;
	}
	elseif(in_array('(:num)', $Slug)) {
		$a = count($Slug) - 1;
		$URI = '';
		for ($i=0; $i < $a; $i++) { 
			$URI .= $Slug[$i].'/';
		}
		$route[ $URI.'(:num)' ] = $row->Controller;
	}
	else
	{
		$route[ $row->Slug ] = $row->Controller;
	}

}

$route['venue_reservation'] = 'page/vreservation/c_global';
$route['vreservation/getroom'] = 'page/vreservation/c_global/getroom';
$route['vreservation/getschedule'] = 'page/vreservation/c_global/getschedule';
$route['vreservation/getschedule/(:any)'] = 'page/vreservation/c_global/getschedule/$1';
$route['vreservation/modal_form'] = 'page/vreservation/c_global/modal_form';
$route['vreservation/add_save_transaksi'] = 'page/vreservation/c_transaksi/add_save_transaksi';
$route['vreservation/getCountApprove'] = 'page/vreservation/c_global/getCountApprove';
$route['vreservation/vr_request'] = 'page/vreservation/c_transaksi/vr_request';
$route['vreservation/json_list_approve'] = 'page/vreservation/c_transaksi/json_list_approve';
$route['vreservation/approve_submit'] = 'page/vreservation/c_transaksi/approve_submit';
$route['vreservation/json_list_booking_by_user'] = 'page/vreservation/c_transaksi/json_list_booking_by_user';
$route['vreservation/cancel_submit'] = 'page/vreservation/c_transaksi/cancel_submit';
$route['vreservation/json_list_booking'] = 'page/vreservation/c_transaksi/json_list_booking';

$route['vreservation/config/modalform_previleges'] = 'page/vreservation/c_config/modal_form_previleges';
$route['vreservation/config/menu-previleges/get_menu'] = 'page/vreservation/c_config/get_menu';
$route['vreservation/config/menu-previleges/get_menu/save'] = 'page/vreservation/c_config/get_menu_save_menu';
$route['vreservation/config/menu-previleges/get_submenu/save'] = 'page/vreservation/c_config/get_submenu_save_menu';
$route['vreservation/config/menu-previleges/get_submenu/show'] = 'page/vreservation/c_config/get_submenu_show';
$route['vreservation/config/menu-previleges/get_submenu/update'] = 'page/vreservation/c_config/get_submenu_update';
$route['vreservation/config/menu-previleges/get_submenu/delete'] = 'page/vreservation/c_config/get_submenu_delete';
$route['vreservation/getGroupPrevileges'] = 'page/vreservation/c_config/getGroupPrevileges';
$route['vreservation/getMenu'] = 'page/vreservation/c_config/getMenu';
$route['vreservation/getMenu'] = 'page/vreservation/c_config/getMenu';
$route['vreservation/config/menu-previleges/get_submenu_by_menu'] = 'page/vreservation/c_config/get_submenu_by_menu';
$route['vreservation/config/menu-previleges/get_previleges_group/show'] = 'page/vreservation/c_config/get_previleges_group_show';
$route['vreservation/config/menu-previleges/groupuser/save'] = 'page/vreservation/c_config/save_groupuser_previleges';
$route['vreservation/config/menu-previleges/groupuser/update'] = 'page/vreservation/c_config/previleges_groupuser_update';
$route['vreservation/config/menu-previleges/groupuser/delete'] = 'page/vreservation/c_config/previleges_groupuser_delete';
$route['vreservation/config/modalform_group_user'] = 'page/vreservation/c_config/modalform_group_user';
$route['vreservation/config/groupuser/save'] = 'page/vreservation/c_config/save_group_user';
$route['vreservation/config/groupuser/update'] = 'page/vreservation/c_config/update_group_user';
$route['vreservation/config/groupuser/delete'] = 'page/vreservation/c_config/delete_group_user';
$route['vreservation/config/getAuthDataTables'] = 'page/vreservation/c_config/getAuthDataTables';
$route['vreservation/add_auth_user'] = 'page/vreservation/c_config/add_auth_user';
$route['vreservation/delete_authUser'] = 'page/vreservation/c_config/delete_authUser';
$route['vreservation/edit_auth_user'] = 'page/vreservation/c_config/edit_auth_user';
$route['vreservation/master/modalform/(:any)'] = 'page/vreservation/c_master/modalform/$1';
$route['vreservation/master/loadTableMaster/(:any)'] = 'page/vreservation/c_master/load_table_master/$1';
$route['vreservation/master/submit_m_equipment'] = 'page/vreservation/c_master/submit_m_equipment';
$route['vreservation/master/additional_personel_json_data'] = 'page/vreservation/c_master/additional_personel_json_data';
$route['vreservation/master/additional_personel/modalform'] = 'page/vreservation/c_master/additional_personel_modal_form';
$route['vreservation/master/additional_personel/submit'] = 'page/vreservation/c_master/additional_personel_submit';
$route['vreservation/master/loaddataJSonEquipmentRoom'] = 'page/vreservation/c_master/loaddataJSonEquipmentRoom';
$route['vreservation/master/modal_form_equipmentroom'] = 'page/vreservation/c_master/modal_form_equipmentroom';
$route['vreservation/master/getDataEquipmentMaster'] = 'page/vreservation/c_master/getDataEquipmentMaster';
$route['vreservation/master/EquipmentRoom/submit'] = 'page/vreservation/c_master/EquipmentRoom_submit';
$route['vreservation/master/loaddataJSonEquipment_additional'] = 'page/vreservation/c_master/loaddataJSonEquipment_additional';
$route['vreservation/master/modal_form_equipmentadditional'] = 'page/vreservation/c_master/modal_form_equipmentadditional';
$route['vreservation/master/EquipmentAdditional/submit'] = 'page/vreservation/c_master/EquipmentAdditional_submit';

// test
$route['testApprove'] = 'page/finance/c_finance/testApprove';
