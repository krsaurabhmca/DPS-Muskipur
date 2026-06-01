<?php
session_start();
$CONFIG['token'] = session_id();
date_default_timezone_set("Asia/Kolkata");
$CONFIG['current_date_time'] = date('Y-m-d H:i:s');
$CONFIG['today'] = date('Y-m-d'); // '2021-11-13'; 
error_reporting(E_ERROR | E_PARSE);
/*-------Some Basic Details (Global Variables) ---------*/

$CONFIG['full_name'] = "Delhi Public School Mushkipur";
$CONFIG['inst_name'] = "DPS";
$CONFIG['school_code'] = "XXXX";
$CONFIG['aff_no'] = "XXXX";
$CONFIG['aff_to'] = "Central Board of Secondary Education";
$CONFIG['inst_managed_by'] = "OfferPlant Technologies";
$CONFIG['inst_address1'] = "Road no 14 Near Bharat Gas Godam Mushkipur";
$CONFIG['inst_address2'] = "Gogari Jamalpur Khagaria Bihar 851203  ";
$CONFIG['inst_contact'] = "8789621916";
$CONFIG['inst_email'] = "info@dpsmushkipur.com";
$CONFIG['inst_logo'] = "images/logo.png";
$CONFIG['white_logo'] = "images/logo.png";
$CONFIG['banner'] = "images/banner.jpg";
$CONFIG['inst_url'] = "www.dpsmushkipur.com";
$CONFIG['inst_type'] = "Institute";
$CONFIG['sender_id'] = "RMPSGJ"; // JIO RMPSJG
$CONFIG['noreply_email'] = "noreply@bine.morg.in";
$CONFIG['sms_auth_key'] ="cd1323469f4970988b9bff98ff49cb79"; //MSGCLUB - rmpschool
/*---------Social Link ----------*/

$CONFIG['facebook'] = 'https://www.facebook.com/dps.muskhipur?mibextid=ZbWKwL';
$CONFIG['twitter'] = 'https://twitter.com/OfferPlant';
$CONFIG['linkedin'] = 'https://linkedin.com/company/OfferPlant';
$CONFIG['youtube'] = 'https://youtube.com/@delhipublicschoolmushkipur6245?si=tmVT2qVxPuUnc2-x';
$CONFIG['pinterest'] = 'https://pinterest.com/OfferPlant';
$CONFIG['whatsapp'] = 'https://whatsapp.com/channel/0029VaC0sMt9MF98Kg4CLT0y';
$CONFIG['instagram'] = 'https://www.instagram.com/invites/contact/?igsh=khfnh7sqhybh&utm_content=ej1kf5b';

/*-------WHATSAPP CONFIG --------*/
$CONFIG['wa_api_key'] ='C5UZzGb1QmzZZx66H1n5w5VsoH5rO7';
$CONFIG['wa_sender'] ='918544450365';

$CONFIG['app_name'] = 'Bine';
$CONFIG['app_version'] = '4.0';
$CONFIG['dev_company'] = "OfferPlant Technologies Private Limited";
$CONFIG['dev_by'] = "OfferPlant";
$CONFIG['dev_url'] = "https://offerplant.com";
$CONFIG['dev_email'] = "ask@offerplant";
$CONFIG['dev_contact'] = "9431426600";
$CONFIG['api_key'] = '71c2ee1aa984518ccb3454640a12cf93'; // For APP Rmpsorg
$CONFIG['default_sms'] = 'send_sms';


//LocalHost Configuration
// $CONFIG['host_name'] = 'localhost';
// $CONFIG['db_user'] = 'root';
// $CONFIG['db_password'] = '';
// $CONFIG['db_name'] = 'bine'; // Default Database Name
// $CONFIG['base_url'] = 'http://localhost/bine/';

/* Live Configuration */
$CONFIG['host_name'] = 'localhost';
$CONFIG['db_user'] = 'u673864504_dps_2627';
$CONFIG['db_password'] = '@Dps_2001';
$CONFIG['db_name'] = 'u673864504_dps_2627'; // Default Database Name
$CONFIG['base_url'] = 'https://dpsmushkipur.com/bine/';


$CONFIG['month_list']       = array('Admission_month', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March');
$CONFIG['att_month_list']   = array('April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March');
$CONFIG['fee_month']        = array('April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March');

// $CONFIG['class_list']       = array('', 'NUR', 'LKG', 'UKG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X','XI','XII');
$CONFIG['class_list']       = array('','PRE' , 'NUR', 'LKG', 'UKG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X');
// $CONFIG['class_list']       = array('', 'NUR', 'LKG', 'UKG','I', 'II', 'III', 'IV', 'V', 'VI');
$CONFIG['section_list']     = array('', 'A', 'B','C');
$CONFIG['session_list']     = array(''=>'Select Session', 'u673864504_dps_2526'=>'2025-26','u673864504_dps_2627'=>'2026-2027');
$CONFIG['gender_list']      = array('MALE', 'FEMALE', 'OTHER');
$CONFIG['status_list']      = array('ACTIVE', 'BLOCK', 'INACTIVE');
$CONFIG['vehicle_status_list'] = array('ASSIGNED', 'NOT ASSIGNED');
$CONFIG['religion_list']    = array('', 'HINDU', 'MUSLIM', 'SHIKH', 'CHRISTIAN');
$CONFIG['caste_list']       = array('', 'General', 'SC', 'ST', 'OBC');
$CONFIG['student_type_list']= array('', 'GENERAL', 'TRANSPORT', 'HOSTELER');
$CONFIG['bloodgroup_list']  = array('', 'A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-');
$CONFIG['admission_list']   = array('', 'NEW', 'OLD');
$CONFIG['finance_list']     = array('NORMAL', 'BPL', 'FREE', 'WARD');
$CONFIG['e_type_list']      = array('', 'Teaching', 'Non Teaching');
$CONFIG['qualification_list'] = array('', 'No Formal Education', 'Primary Education', 'Secondary Education or High School', 'Vocational Qualification', 'Bachelor degree', 'Master degree', 'Doctorate or higher');
$CONFIG['occupation_list']  = array('', 'Unemployed', 'Private Job', 'Govt. Job', 'Self Employed', 'Professional', 'Farmer', 'Student', 'House Wife');
$CONFIG['income_list']      = array('', 'No Income', 'Less Than 1,00,000', '1,00,001 to 3,00,000', '3,00,001 to 10,00,000', '10,00,001 to 25,00,000', '25,00,001 or above');
$CONFIG['account_list']     = array('Tuition', 'Hostel', 'Transport', 'Director Cash', 'School A/c', 'Staff Salary', 'Office Expense', 'Miscellenous');
$CONFIG['user_type_list']   = array('ACCOUNT','DBA','STAFF','ADMIN');
$CONFIG['subject_list']     = array('Hindi', 'English', 'Mathematics', 'EVS', 'SST', 'Science', 'Infomation_Technology', 'General_Knowledge', 'Sanskrit_Urdu','Urdu');
$CONFIG['co_scholastic_list'] = array('Work Education','Health & Physical Education','Discipline');
$CONFIG['discipline_list']  = array('', 'Regularity & Puntuality', 'Sincerity', 'Behaviour & Value','Respectfulness for Rules & Regulation','Attitude toward Teachers','Attitude toward School-mates','Attitude toward Sociaty', 'Attitude toward Nation');
$CONFIG['co_grade_list']    = array('', 'A', 'B', 'C');
$CONFIG['house_list']       =array('','Hope House','Courage House','Peace House','Faith House');
$CONFIG['neet_status_list'] =array('NO','YES');
$CONFIG['current_session']  = date('Y')."-". (date('Y')+1);
$CONFIG['adm_suffix'] = '/2021-22';
$CONFIG['fee_mode_list']    = array('Monthly', 'Annual', 'OneTime');
$CONFIG['fee_nature_list']  = array('CLASS' => 'Based On Class', 'STUDENT' => 'Based On Student', 'FIXED' => 'Fixed For All');
$CONFIG['subject_category_list'] = array('', 'Scholastic', 'Non Scholastic');
$CONFIG['smc_list']         = array('PDF', 'YOUTUBE', 'GOOGLE MEET', 'RICH TEXT', 'OTHER');
$CONFIG['day_list']         = array('SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY');
$CONFIG['source_list']      = array('FRIENDS', 'NEWSPAPER', 'POSTER', 'LOUDSPEAKER', 'GOOGLE SEARCH', 'SOCIAL MEDIA', 'CALL/MSG','WEBSITE');

$CONFIG['login_as_list']    = array('' =>  'Login As Role','TRANSPORT' =>  'Transport In Charge', 'Inventory' => 'Inventory In Charge','ACCOUNT'=>'Account In Charge', 'TEACHER'=>'Teacher/ Class Teacher','DBA' =>
'Database Administrator', 'LIBRARY' =>'Library In Charge');
/*-------End of Basic Details ---------*/


$CONFIG['menu_list']            = array();
$CONFIG['menu_list']['student'] = array('student_add', 'student_manage');
$CONFIG['menu_list']['fee']     = array('collect_fee', 'collection_report', 'demand_print', 'fee_chart', 'class_wise_ledger');
$CONFIG['menu_list']['transport'] = array('add_area', 'add_trip', 'area_wise_report');
$CONFIG['menu_list']['exam']    = array('admit_card', 'exam_sheet', 'marks_entry', 'report_card', 'print_report_card', 'consolidated_marks');
$CONFIG['menu_list']['website'] = array('notice_board', 'gallery', 'enquiry', 'holiday', 'online_admission', 'online_payment');
$CONFIG['menu_list']['extra']   = array('send_sms', 'sms_report', 'identity_card', 'certificate_print');
$CONFIG['menu_list']['settings']= array('create_fee', 'set_fee', 'subject_settings', 'manage_user');



/* LIBRARY CONFIGRATION */
$CONFIG['book_status'] = array('AVAILABLE', 'ISSUED', 'MISSING', 'REMOVED');
$CONFIG['book_txn_status'] = array('ISSUED', 'RETURN', 'MISSED');
$CONFIG['fine_per_day'] = 1;
$CONFIG['max_book_allow'] = 3;
$CONFIG['max_day_allow'] = 15;

/* Inventory Configuration */
$CONFIG['item_status'] = array('IN STOCK', 'OUT OF STOCK', 'REMOVED');
$CONFIG['payment_mode_list'] = array('CASH', 'BANK', 'UPI');
$CONFIG['gst_percent_list'] = array(0, 5, 12, 18, 28);


/* Account Configuration */

$CONFIG['account_head_list'] = array('', 'SALARY', 'INFRASTRUCTURE', 'ENERGY', 'RENT', 'TRANSPORT', 'HOSTEL', 'STATIONARY', 'LIBRARY', 'LABORATORY', 'ACTIVITY', 'DAILY EXPENSES', 'MISCELLANEOUS', 'PRINCIPAL', 'BANK DEPOSIT');

$CONFIG['txn_mode_list'] = array('CASH', 'BANK', 'UNPAID');

$CONFIG['allow_status'] = array('', 'YES', 'NO');
$CONFIG['task_list'] = array('student' => 'Manage Student', 'enquiry' => 'Manage Enquiry', 'book_cat' => 'Manage Book Category');
$CONFIG['attendence_status'] = array('' => 'Select', 'P' => 'PRESENT', 'A' => 'ABSENT', 'L' => 'LEAVE');

/* Vehicle Configuration */
$CONFIG['vehicle_type_list'] = array('', 'BUS', 'VAN', 'MAGIC');

/* Lesson Plan Configuration */
$CONFIG['timeslot_status_list'] = array('PENDING', 'ONGOING', 'COMPLETED');

/* Leave Configuration */
$CONFIG['leave_type_list'] = array('Paid', 'Unpaid');
$CONFIG['salary_status_list'] = array('Paid', 'Unpaid','Partially Paid');



/* Exam Configuration */
$CONFIG['exam_list'] = array('PT-1','PT-2','PT3','PT4','MID-TREM','ANNUAL');
$CONFIG['answer_list'] =array('','A','B','C','D');
$CONFIG['neet_status'] =array('YES','NO');
$CONFIG['extra_subject'] =array('NO','YES');

/* Role Configuration */
$CONFIG['admin_role'] =array("account_head.php", "account_summary.php", "add_area.php", "add_employee.php", "log_sheet.php","show_logsheet.php", "add_trip.php", "add_user.php", "admin_block_student.php", "admin_download_student.php", "admin_view_student.php", "annual_pay_process.php", "annual_receipt.php", "api.php", "api_docs.txt", "assets", "att_report.php", "att_summary.php", "birthday.php", "cancel_receipt.php", "change_password.php", "class_wise_ledger.php", "collect.php", "collect_fee.php", "collection_report.php", "config.php", "conn.php", "delete_fee.php", "demand_bill.php", "edit_employee.php", "edit_process.php", "edit_student.php", "employee_idcard.php", "event.php", "expense_entry.php", "fee_collection.php", "fee_list.php", "fee_setting.php", "fee_setup.php", "fee_status.php", "footer.php", "forgot_password.php", "forgot_password_process.php", "function.php", "gallery.php", "gallery_process.php", "generate_demand.php", "generate_demand_list.php", "get_student_info.php", "gps.php", "header.php", "hostel_demand_list.php", "hostel_reminder_list.php", "index.php", "ivr_data.php", "login.php", "login_process.php", "logout.php", "make_att.php", "manage_employee.php", "manage_hw.php", "master_process.php", "menu.php", "multiple_receipt.php", "new_student.php", "notice.php", "notice_edit.php", "notice_process.php", "pay_annual.php", "pay_fee.php", "pay_process.php", "post_data.php", "print_id.php", "print_receipt.php", "receipt.php", "reminder.php", "reminder_list.php", "resize.php", "search_student.php", "search_to_pay.php", "send_sms.php", "show_enquery.php", "student_ledger.php", "student_process.php", "test.php", "towords.js", "trip_process.php", "update_fee.php");
$CONFIG['dba_role'] =array('add_employee.php','add_hw.php','add_trip.php','add_user.php','admin_block_student.php',"log_sheet.php","show_logsheet.php",'admin_download_student.php','admin_view_student.php','att_report.php','att_summary.php','cancel_receipt.php','change_password.php','class_wise_ledger.php','config.php','conn.php','edit_employee.php','edit_process.php','edit_student.php','employee_idcard.php','event.php','fee_status.php','footer.php','forgot_password.php','forgot_password_process.php','function.php','gallery.php','gallery_process.php','get_account.php','get_student_info.php','header.php','index.php','login.php','login_process.php','logout.php','make_att.php','manage_employee.php','manage_hw.php','master_process.php','menu.php','multiple_receipt.php','new_student.php','notice.php','notice_edit.php','notice_process.php','print_id.php','resize.php','search_student.php','send_sms.php','show_enquery.php','student_ledger.php','student_process.php','towords.js','trip_process.php');
$CONFIG['account_role'] =array("account_head.php", "account_summary.php", "add_area.php", "add_employee.php", "add_hw.php", "add_trip.php", "add_user.php", "admin_block_student.php", "admin_download_student.php", "admin_view_student.php", "annual_pay_process.php", "annual_receipt.php", "api.php", "api_docs.txt", "assets", "att_report.php", "att_summary.php", "birthday.php", "cancel_receipt.php", "change_password.php", "class_wise_ledger.php", "collect.php", "collect_fee.php", "collection_report.php", "config.php", "conn.php", "delete_fee.php", "demand_bill.php", "edit_employee.php", "edit_process.php", "edit_student.php", "employee_idcard.php", "event.php", "expense_entry.php", "fee_collection.php", "fee_list.php", "fee_setting.php", "fee_setup.php", "fee_status.php", "footer.php", "forgot_password.php", "forgot_password_process.php", "function.php", "gallery.php", "gallery_process.php", "generate_demand.php", "generate_demand_list.php", "get_student_info.php", "gps.php", "header.php", "hostel_demand_list.php", "hostel_reminder_list.php", "index.php", "ivr_data.php", "login.php", "login_process.php", "logout.php", "make_att.php", "manage_employee.php", "manage_hw.php", "master_process.php", "menu.php", "multiple_receipt.php", "new_student.php", "notice.php", "notice_edit.php", "notice_process.php", "pay_annual.php", "pay_fee.php", "pay_process.php", "post_data.php", "print_id.php", "print_receipt.php", "receipt.php", "reminder.php", "reminder_list.php", "resize.php", "search_student.php", "search_to_pay.php", "send_sms.php", "show_enquery.php", "student_ledger.php", "student_process.php", "test.php", "towords.js", "trip_process.php", "update_fee.php");

$CONFIG['sms_content_type_list'] = array('unicode'=>'Hindi','english' =>'English');

// STUDYLANT Configuration // 
 
//$CONFIG['course_list'] = $class_list = $class_list =array('','NUR','LKG','UKG','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
$CONFIG['question_type_list'] =array('TEXT' =>'Multiple Choice Question','IMAGE' =>'Image Multiple Choice Question','SAQ'=>'Short Answer Question','LAQ' => 'Long Answer Question','VLAQ' => 'Very Long Answer Question');
$CONFIG['day_list'] =array('','SUN','MON','TUE','WED','THU','FRI','SAT');
$CONFIG['meeting_list'] =array(''=>'Select','meet_code'=>'GOOGLE MEET','zoom_code'=>'ZOOM MEETING','jitsi_code'=>'JITSI MEET',); 
$CONFIG['period_list'] = array('p1','p2','p3','p4','p5','p6','p7');
// $CONFIG['exam_list'] = array('TERM-1','TERM-2','PT1','PT2');

$CONFIG['e_type_list']      = array('', 'Teaching', 'Non Teaching');
$CONFIG['designation_list'] = array('','PRT','TGT','PET','Librarian','PGT','Special Educator','Wellness Teacher','Manager Accounts','Accountant','Transport Incharge','Peon','Guard');
$CONFIG['department_list']  = array('','Account Section','Transport Section','Security','Supporting Staff','Hindi','English','Social Studies','Science','Information Technology',
'Artificial Intelligence','Sanskrit','Urdu','Mathematics','Library');

extract($CONFIG);
