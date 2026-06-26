<?php
// ====== BASIC HEADERS ======
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle CORS preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include your required functions
require_once('required/function.php');

// ====== READ RAW JSON INPUT ======
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// ====== CHECK JSON VALIDITY ======
// if (json_last_error() !== JSON_ERROR_NONE) {
//     echo json_encode([
//         "status" => "error",
//         "message" => "Invalid JSON format"
//     ]);
//     exit;
// }

define('PHONEPE_MERCHANT_ID', 'YOUR_MERCHANT_ID');
define('PHONEPE_SALT_KEY', 'YOUR_SALT_KEY');
define('PHONEPE_SALT_INDEX', 1);
define('PHONEPE_ENV', 'PRODUCTION'); // or 'UAT' for testing
define('PHONEPE_BASE_URL', PHONEPE_ENV === 'PRODUCTION'
    ? 'https://api.phonepe.com/apis/hermes'
    : 'https://api-preprod.phonepe.com/apis/pg-sandbox');




if (isset($_GET['task'])) {
    switch ($_REQUEST['task']) {

        case "login":
            extract($data);
            $res = get_all('user', ['id', 'full_name', 'user_type', 'status'], ['user_name' => $user_name, 'user_pass' => md5($user_pass)]);
            echo json_encode($res);
            break;

        case "search_student":
        case 'search_student':
            // Safely extract only if keys exist
            $search_text = isset($data['search_text']) ? trim($data['search_text']) : '';

            if ($search_text !== '') {
                // Escape input properly
                $search_text = mysqli_real_escape_string($con, $search_text);

                // Build query
                $sql = "
                        SELECT id, student_name, student_class, student_section, student_father, student_mobile, status FROM student
                        WHERE student_name LIKE '%$search_text%'
                           OR student_mobile LIKE '%$search_text%'
                           OR student_admission LIKE '%$search_text%'
                    ";

                $res = direct_sql($sql);
                unset($res['sql']);
                echo json_encode($res);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Missing or empty search_text parameter"
                ]);
            }
            break;

        case "student_fee":
            extract($data);
            $month_list;
            $all_fee = [];
            
            // Add student profile data
            $profile_sql = "SELECT id, student_admission, student_name, student_class, student_section FROM student WHERE id = '$student_id' AND status = 'ACTIVE'";
            $profile_res = direct_sql($profile_sql);
            if ($profile_res['count'] > 0) {
                $all_fee['student_profile'] = $profile_res['data'][0];
            }
            
            $prev_dues = intval(get_data('student_fee', $student_id, 'current_dues', 'student_id')['data']);
            if ($prev_dues <> 0) {
                $all_fee['previous_dues'] = [
                    'Previous Dues' => $prev_dues,
                    'total' => $prev_dues,
                    'status' => 'UNPAID'
                ];
            }
            foreach ($month_list as $month) {
                $fee = monthly_fee($student_id, $month);
                $rid = get_data('student_fee', $student_id, remove_space($month), 'student_id')['data'];
                unset($fee['student_id']);
                unset($fee['admission']);
                unset($fee['month']);
                // $fee['fee']['status'] = (intval($rid)>0)?'PAID':'UNPAID';
                $fee['fee']['status'] = ($rid === null || $rid === '') ? 'UNPAID' : 'PAID';
                $all_fee[$month] = $fee['fee'];
                // if($month ==date('F'))
                // {
                //     break;
                // }
            }
            echo json_encode($all_fee);
            break;

        case "pay_fee":
            extract($data);
            // Month_list, totalt, Pad_amount, discount, data, payment_mode , remarks 

            $rdata = nmonth_fee_all($student_id, $months);
            $rdata['previous_dues'] = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
            $rdata['paid_month'] = implode(",", $months);
            $rdata['student_id'] = $student_id;
            $rdata['student_admission'] = get_data('student', $student_id, 'student_admission')['data'];
            $rdata['total'] = $total;
            $rdata['paid_amount'] = $paid_amount;
            $rdata['current_dues'] = $total - $paid_amount;
            $rdata['remarks'] = $remarks;
            $rdata['other_fee'] = $misc_fee;
            $rdata['discount'] = $discount;
            $rdata['paid_date'] = $payment_date;
            $rdata['created_by'] = $created_by;
            $rdata['payment_mode'] = ($payment_mode == 'cash') ? "CASH" : "BANK";


            $res = insert_data('receipt', $rdata);

            if ($res['status'] == 'success') {
                $rid = $res['id'];
                foreach ($months as $month) {
                    $old_value = get_data('student_fee', $student_id, remove_space($month), 'student_id')['data'];
                    if ($old_value != null and $month == 'other_month') {
                        $rid = $old_value . "," . $rid;
                    }
                    $res2 = update_data('student_fee', array($month => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
                }
            }
            echo json_encode($res);
            break;


        case "get_receipt":
            extract($data);
            $receipt_info = get_data('receipt', $receipt_id);

            if ($receipt_info['count'] > 0) {
                $student = get_data('student', $receipt_info['data']['student_id'])['data'];

                $receipt['student_name'] = $student['student_name'];
                $receipt['admission_no'] = $student['student_admission'];
                $receipt['student_class'] = $student['student_class'] . " " . $student['student_section'];
                $receipt['father_name'] = $student['student_father'];
                $receipt['paid_month'] = $receipt_info['data']['paid_month'];
                $receipt['payment_date'] = $receipt_info['data']['paid_date'];
                $receipt['receipt_id'] = $receipt_info['data']['id'];
                $receipt['total'] = $receipt_info['data']['total'];
                $receipt['paid_amount'] = $receipt_info['data']['paid_amount'];
                $receipt['discount'] = $receipt_info['data']['discount'];
                $receipt['misc_fee'] = $receipt_info['data']['other_fee'];
                $receipt['remarks'] = $receipt_info['data']['remarks'];
                $receipt['current_dues'] = $receipt_info['data']['current_dues'];
                $receipt['fee_details'] = nmonth_fee_all($student['id'], explode(",", $receipt_info['data']['paid_month']));
                echo json_encode($receipt);
            }
            break;

        case "collection_report":
            $from_date = $data['from_date'] ?? $today;
            $to_date = $data['to_date'] ?? $today;
            $sql = "select receipt.id as receipt_id, student.student_admission as admission_no, student.student_name, receipt.paid_amount, receipt.paid_date from receipt left join student on receipt.student_id =student.id where paid_date between '$from_date' and '$to_date' and receipt.status ='PAID' ";
            $res = direct_sql($sql);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "student_list";
            extract($data);
            $res = get_all('student', ['id', 'student_name', 'student_roll', 'student_admission'], ['student_class' => $student_class, 'student_section' => $student_section]);
            echo json_encode($res);
            break;

        case "make_att";
            extract($data);
            $att_month = strtolower(date('M_Y', strtotime($att_date)));
            $date = date('j', strtotime($att_date));
            $col_name = 'd_' . $date;
            $p = $a = 0;
            foreach ($student_list as $student_id => $status) {
                $search = get_all('student_att', '*', ['student_id' => $student_id, 'att_month' => $att_month]);
                ($status == 'P') ? $p++ : $a++;
                if ($search['count'] > 0) {
                    $res = update_data('student_att', [$col_name => $status], $search['data'][0]['id']);
                } else {
                    insert_data('student_att', ['student_id' => $student_id, 'att_month' => $att_month, $col_name => $status]);
                }
            }
            $res['present'] = $p;
            $res['absent'] = $a;
            echo json_encode($res);
            break;


        case "dues_list":
            extract($data);
            $sql = "select id, student_admission, student_mobile, student_name from student where student_class ='$student_class' and student_section ='$student_section'";
            $search = direct_sql($sql);
            foreach ($search['data'] as $row) {
                $student_id = $row['id'];
                $row['previous_dues'] = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
                $row['fee'] = nmonth_fee_all($student_id, $months);
                $res[] = $row;
            }
            echo json_encode($res);
            break;

        case "subject_list":
            extract($data);
            $hdata['hw_class'] = $hw_class;
            $sql = "SELECT id, subject_name, status FROM subject where FIND_IN_SET( '$hw_class', student_class)";
            $res = direct_sql($sql);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "home_work":
            extract($data);
            $hdata['hw_date'] = $today;
            $hdata['hw_class'] = $hw_class;
            $hdata['hw_section'] = $hw_section;
            $hdata['subject_id'] = $subject_id;
            $hdata['hw_text'] = $hw_text;
            $hdata['hw_file'] = $hw_file;
            $search = get_all('homework', '*', ['hw_date' => $hw_date, 'hw_class' => $hw_date, 'subject_id' => $subject_id]);
            if ($search['count'] > 0) {
                //$res  = update_data('homework',$hdata, $search['data'][0]['id']);  
                $res['msg'] = 'Homework Already Assigned';
                $res['status'] = 'error';
            } else {
                $res = insert_data('homework', $hdata);
            }
            echo json_encode($res);
            break;

        case "class_list":
            $sql = "select student_class, student_section , count(id) as total from student where status ='ACTIVE' group by student_class, student_section  ";
            $res = direct_sql($sql);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "upload":

            // Directory to save uploaded files
            $target_dir = "homework/";

            // Create directory if not exists
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $response = [];
            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                if (isset($_FILES["file"]) && $_FILES["file"]["error"] === 0) {
                    $file_name = basename($_FILES["file"]["name"]);
                    $target_file = $target_dir . $file_name;
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $file_size = $_FILES["file"]["size"];

                    // Allowed file types
                    $allowed_types = ["jpg", "jpeg", "png", "gif", "pdf"];

                    // Validate file type
                    if (!in_array($file_type, $allowed_types)) {
                        $response = [
                            "success" => false,
                            "error" => "Invalid file type. Only JPG, PNG, GIF, and PDF are allowed."
                        ];
                        echo json_encode($response);
                        exit;
                    }

                    // Validate file size (max 5 MB)
                    if ($file_size > 5 * 1024 * 1024) {
                        $response = [
                            "success" => false,
                            "error" => "File too large. Maximum allowed size is 5 MB."
                        ];
                        echo json_encode($response);
                        exit;
                    }

                    // Move uploaded file
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                        $response = [
                            "success" => true,
                            "message" => "File uploaded successfully.",
                            "file_name" => $file_name,
                            "file_path" => $target_file,
                            "file_type" => $file_type,
                            "file_size" => round($file_size / 1024, 2) . " KB"
                        ];
                    } else {
                        $response = [
                            "success" => false,
                            "error" => "Failed to move uploaded file."
                        ];
                    }
                } else {
                    $response = [
                        "success" => false,
                        "error" => "No file uploaded or upload error.",
                        "details" => $_FILES["file"]["error"] ?? null
                    ];
                }
            } else {
                $response = [
                    "success" => false,
                    "error" => "Invalid request method. Use POST."
                ];
            }

            // Return JSON response
            echo json_encode($response, JSON_PRETTY_PRINT);
            break;

        // case "dashboard" :
        //     $user = get_data('user', $data['user_id'])['data'];
        //     //Total ACTIVE Student
        //     $total = get_all('student',['id'],['status'=>'ACTIVE'])['count'];
        //     // Totday Present
        //     $att_month = date("M-Y");
        //     $att_date = 'd_'.date("j");
        //     $sql ="SELECT count(id) as present FROM `student_att` where $att_date ='P' and att_month ='$att_month'";
        //     $att = direct_sql($sql);
        //     $absent  = $total - $att['data'][0]['present'];

        //     // total Collection Today
        //     $coll_sql ="SELECT sum(paid_amount) as total FROM `receipt` where paid_date ='$today' and status ='PAID'";
        //     $coll = direct_sql($coll_sql);
        //     $collection = $coll['data'][0]['total'] ?? 0;
        //     echo  json_encode(
        //         [
        //             'student'=> $total,
        //             'absent'=> $absent,
        //             'collection'=> $collection,
        //             'full_name'=> $user['full_name'],
        //             'user_type'=> $user['user_type'],
        //         ]);
        //     break;

        case "dashboard":
            $user = get_data('user', $data['user_id'])['data'];
            if ($user['user_type'] == 'ADMIN' or $user['user_type'] == 'DEV' or $user['user_type'] == 'DBA') {
                $total = get_all('student', ['id'], ['status' => 'ACTIVE'])['count'];
                $att_month = date("M-Y");
                $att_date = 'd_' . date("j");

                $sql = "SELECT COUNT(id) AS present FROM student_att WHERE $att_date = 'P' AND att_month = '$att_month'";
                $att = direct_sql($sql);
                $absent = $total - ($att['data'][0]['present'] ?? 0);
                $coll_sql = "SELECT SUM(paid_amount) AS total FROM receipt WHERE paid_date = '$today' AND status = 'PAID'";
                $coll = direct_sql($coll_sql);
                $collection = $coll['data'][0]['total'] ?? 0;
                $recent_receipt_sql = "SELECT id, student_id, paid_amount, paid_date FROM receipt WHERE status='PAID' ORDER BY id DESC LIMIT 5";
                $recent_receipts = direct_sql($recent_receipt_sql);
                $recent_student_sql = "SELECT id, student_name, created_at FROM student ORDER BY id DESC LIMIT 5";
                $recent_students = direct_sql($recent_student_sql);
                echo json_encode([
                    'student' => $total,
                    'absent' => $absent,
                    'collection' => $collection,
                    'full_name' => $user['full_name'],
                    'user_type' => $user['user_type'],
                    'recent_receipts' => $recent_receipts['data'] ?? [],
                    'recent_students' => $recent_students['data'] ?? []
                ]);
            } else if ($user['user_type'] == 'TEACHER' or $user['user_type'] == 'STAFF') {
                // Teacher Dashboard: Focus ONLY on assigned classes and students
                $teacher_id = mysqli_real_escape_string($con, $data['user_id']);

                // 1. Get assignments
                $assign_sql = "SELECT DISTINCT student_class, student_section FROM teacher_assignment WHERE user_id = '$teacher_id' AND status = 'ACTIVE'";
                $assign_res = direct_sql($assign_sql);
                $assignments = $assign_res['data'] ?? [];

                $total_students = 0;
                $present_students = 0;
                $class_conditions = [];

                if (count($assignments) > 0) {
                    foreach ($assignments as $a) {
                        $cls = mysqli_real_escape_string($con, $a['student_class']);
                        $sec = mysqli_real_escape_string($con, $a['student_section']);
                        $class_conditions[] = "(student_class = '$cls' AND student_section = '$sec')";
                    }

                    $cond_str = implode(" OR ", $class_conditions);

                    // 2. Count active students in assigned classes
                    $stud_sql = "SELECT COUNT(id) AS total FROM student WHERE status = 'ACTIVE' AND ($cond_str)";
                    $stud_res = direct_sql($stud_sql);
                    $total_students = $stud_res['data'][0]['total'] ?? 0;

                    // 3. Count present today in assigned classes
                    $att_month = date("M-Y");
                    $att_date = 'd_' . date("j");

                    $att_sql = "SELECT COUNT(sa.id) AS present 
                                FROM student_att sa 
                                JOIN student s ON sa.student_id = s.id 
                                WHERE sa.$att_date = 'P' 
                                  AND sa.att_month = '$att_month' 
                                  AND s.status = 'ACTIVE'
                                  AND ($cond_str)";
                    $att_res = direct_sql($att_sql);
                    $present_students = $att_res['data'][0]['present'] ?? 0;
                }

                $absent_students = $total_students - $present_students;

                // Get recent homework assignments added
                $recent_hw_sql = "SELECT hw.id, hw.hw_class, hw.hw_section, hw.hw_text, hw.created_at, s.subject_name 
                                  FROM homework hw 
                                  LEFT JOIN subject s ON hw.subject_id = s.id 
                                  WHERE hw.hw_class IN (SELECT DISTINCT student_class FROM teacher_assignment WHERE user_id = '$teacher_id')
                                  ORDER BY hw.id DESC LIMIT 5";
                $recent_hw = direct_sql($recent_hw_sql);

                echo json_encode([
                    'student' => $total_students,
                    'absent' => $absent_students,
                    'classes_count' => count($assignments),
                    'full_name' => $user['full_name'],
                    'user_type' => $user['user_type'],
                    'recent_receipts' => [], // Empty collections for teachers
                    'recent_students' => [],
                    'recent_homework' => $recent_hw['data'] ?? [],
                    'is_teacher' => true
                ]);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'You are Not allowed']);
            }
            break;


        case "notice":
            $data['notice_date'] = $data['notice_date'] ?? $today;
            $data['status'] = 'ACTIVE';
            $res = insert_data("notice", $data);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "leave_applied":
            $res = direct_sql("SELECT id,status,student_id,from_date,to_date,cause FROM student_leave WHERE status NOT IN ('AUTO','DELETED')");
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "leave_update":
            extract($data);
            $res = update_data('student_leave', $data, $id);
            echo json_encode($res);
            break;

        case "complaints":
            extract($data);
            if ($user_type == 'ADMIN') {
                $sql = "SELECT 
                        complaints.id, 
                        complaints.complaint_to, 
                        complaints.complaint, 
                        complaints.status, 
                        student.student_name,
                        student.student_class,
                        student.student_section,
                        student.student_roll,
                        student.student_mobile
                    FROM complaints
                    JOIN student ON complaints.student_id = student.id
                    WHERE complaints.status NOT IN ('AUTO','DELETED')";
                $res = direct_sql($sql);
            } else if ($user_type == 'ACCOUNT') {
                $sql = "SELECT 
                        complaints.id, 
                        complaints.complaint_to, 
                        complaints.complaint, 
                        complaints.status, 
                        student.student_name,
                        student.student_class,
                        student.student_section,
                        student.student_roll,
                        student.student_mobile
                    FROM complaints
                    JOIN student ON complaints.student_id = student.id
                    WHERE complaints.complaint_to='ACCOUNT' AND complaints.status NOT IN ('AUTO','DELETED')";
                $res = direct_sql($sql);
            } else {
                $res = null;
                $res['status'] = 'error';
                $res['msg'] = 'You Are Not Authorised';
            }
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "update_complaints":
            extract($data);
            $res = update_data('complaints', $data, $id);
            echo json_encode($res);
            break;

        case "send_otp":
            extract($data);
            $res = get_all('student', ['id'], ['student_mobile' => $student_mobile, 'status' => 'ACTIVE']);//,'date_of_birth'=>$date_of_birth
            if ($res['status'] == 'success' and $res['count'] > 0) {
                $res['otp'] = $otp = 9999;// rand(1000,9999);
                $res['msg'] = $msg = "Thanks for interest in  $inst_name  Your App Login OTP is $otp ";
                update_data('student', ['otp' => $otp], $res['data']['0']['id']);
                //sendsms($student_mobile, $msg);
            } else {
                $res = null;
                $res['status'] = 'error';
                $res['msg'] = 'No Data Found';
            }
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "get_otp_old":
            extract($data);
            $res = get_all('student', ['id', 'otp', 'student_admission', 'student_name', 'student_class', 'student_section', 'student_roll', 'student_type', 'student_photo', 'student_sex', 'student_mobile'], ['student_mobile' => $student_mobile, 'status' => 'ACTIVE']);
            if ($res['count'] > 0) {
                if ($otp == $res['data']['0']['otp']) {
                    //   $res = $res['data'];
                    $res['msg'] = "OTP VERIFIED";
                    $res['current_dues'] = get_data('student_fee', $res['data']['0']['student_admission'], 'current_dues', 'student_admission')['data'];
                } else {
                    $res = null;
                    $res['status'] = 'error';
                    $res['msg'] = 'OTP Mismatched';
                }
            } else {
                $res = null;
                $res['status'] = 'error';
                $res['msg'] = 'OTP Mismatched';
            }
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "get_otp":
            extract($data);

            // Fetch all students with their current dues and total paid amount
            $sql = "
                        SELECT 
                            s.id,
                            s.otp,
                            s.student_admission,
                            s.student_name,
                            s.student_class,
                            s.student_section,
                            s.student_roll,
                            s.student_type,
                            s.student_photo,
                            s.student_sex,
                            s.student_mobile,
                            f.current_dues,
                            COALESCE(SUM(r.paid_amount), 0) AS total_paid
                        FROM student s
                        LEFT JOIN student_fee f ON s.id = f.student_id
                        LEFT JOIN receipt r ON s.id = r.student_id
                        WHERE s.student_mobile = '$student_mobile' AND s.status = 'ACTIVE'
                        GROUP BY 
                            s.id, s.otp, s.student_admission, s.student_name, s.student_class, 
                            s.student_section, s.student_roll, s.student_type, s.student_photo, 
                            s.student_sex, s.student_mobile, f.current_dues
                    ";

            $res = direct_sql($sql);

            if ($res['count'] > 0) {
                $res['notices'] = get_all('notice', ['id', 'notice_date', 'notice_title', 'notice_details', 'notice_attachment'], ['status' => 'ACTIVE'], 'id desc limit 3')['data'];
                $res = $res;
            } else {
                $res = [
                    'status' => 'error',
                    'msg' => 'No records found'
                ];
            }

            echo json_encode($res);
            break;


        case "get_attendance":
            extract($data);
            $res = get_all('student_att', '*', ['student_id' => $student_id])['data'];
            echo json_encode($res);
            break;


        case "get_homework":
            //   print_r($data);
            extract($data);
            $sinfo = get_data('student', $student_id)['data'];
            $res = get_all('homework', ['id', 'hw_date', 'hw_class', 'hw_section', 'subject_id', 'hw_text', 'hw_file'], ['hw_class' => $sinfo['student_class'], 'hw_section' => $sinfo['student_section'], 'hw_date' => $hw_date]);
            if ($res['count'] > 0) {
                $res = $res['data'];
            } else {
                $res = null;
                $res['status'] = 'error';
                $res['msg'] = 'No data found';
            }
            echo json_encode($res);
            break;

        case "get_notice":
            $res = get_all('notice', ['id', 'notice_date', 'notice_title', 'notice_details', 'notice_attachment'], ['status' => 'ACTIVE'])['data'];
            echo json_encode($res);
            break;


        case "send_complaint":
            $data['complaint_date'] = $data['complaint_date'] ?? $today;
            $res = insert_data("complaints", $data);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "send_review":
            $res = insert_data("review", $data);
            unset($res['sql']);
            if ($res['status'] == 'success') {
                $res['msg'] == 'Review Submitted';
            }
            echo json_encode($res);
            break;

        case "get_student_profile":
            extract($data);
            $sql = "
                        SELECT 
                            s.id,
                            s.otp,
                            s.student_admission,
                            s.student_name,
                            s.student_class,
                            s.student_section,
                            s.student_roll,
                            s.student_type,
                            s.student_photo,
                            s.student_sex,
                            s.student_father,
                            s.student_mother,
                            s.student_mobile,
                            f.current_dues,
                            COALESCE(SUM(r.paid_amount), 0) AS total_paid
                        FROM student s
                        LEFT JOIN student_fee f ON s.id = f.student_id
                        LEFT JOIN receipt r ON s.id = r.student_id
                        WHERE s.id = '$student_id' AND s.status = 'ACTIVE'
                        GROUP BY 
                            s.id, s.otp, s.student_admission, s.student_name, s.student_class, 
                            s.student_section, s.student_roll, s.student_type, s.student_photo, 
                            s.student_sex, s.student_mobile, f.current_dues
                    ";
            $res = direct_sql($sql);
            if ($res['count'] > 0) {
                $res = $res['data'];
            } else {
                $res = null;
                $res['status'] = 'error';
                $res['msg'] = 'No data found';
            }
            echo json_encode($res);
            break;

        case "help_and_support":
            $res['address'] = $inst_address1 . ', ' . $inst_address2;
            $res['contact'] = $inst_contact;
            $res['email'] = $inst_email;
            $res['website'] = $inst_url;
            $res['wp_channel'] = 'https://wa.me/+918809117872';
            echo json_encode($res);
            break;

        case "student_leave_apply":
            extract($data);
            $data['status'] = 'PENDING';
            $res = insert_data("student_leave", $data);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "holiday_list":
            $res = get_all('holiday', ['id', 'holiday_name', 'holiday_date'], ['status' => 'ACTIVE']);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "payment_history":
            extract($data);
            $fee_head = get_all('fee_head', '*', ['status' => 'ACTIVE']);
            $fee_columns = [];

            foreach ($fee_head['data'] as $fee) {
                $fee_columns[] = remove_space($fee['fee_name']);
            }
            $default_columns = [
                'id AS receipt_id',
                'paid_date',
                'status',
                'paid_month',
                'other_fee',
                'total',
                'paid_amount',
                'current_dues',
                'remarks'
            ];

            $all_columns = array_merge($default_columns, $fee_columns);
            $columns_str = implode(", ", $all_columns);
            $sql = "SELECT $columns_str FROM receipt WHERE student_id = '$student_id' ORDER BY paid_date DESC";
            $res = direct_sql($sql);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "get_exam":
            echo json_encode($exam_list);
            break;

        case "get_marks":
            extract($data);

            // Fetch student info
            $studentData = get_data('student', $student_id);
            if (empty($studentData['data'])) {
                echo json_encode(['status' => 'error', 'message' => 'Student not found']);
                exit;
            }
            $student = $studentData['data'];
            $sid = $student['id'];

            // Get list of subjects for the student's class
            $subjects = subject_list($student['student_class']);
            $examResults = get_all('exam', '*', ['student_id' => $sid, 'exam_name' => $exam_name]);
            $examData = !empty($examResults['data']) ? $examResults['data'][0] : [];

            // Initialize totals
            $totalMarks = 0;
            $responseData = [];

            foreach ($subjects as $subject_id) {
                // Get subject info
                $subjectInfo = get_data('subject', $subject_id);
                $subjectName = $subjectInfo['data']['subject_name'] ?? 'Unknown';
                $subjectCol = $subjectInfo['data']['subject_column'] ?? '';

                // Get marks
                $marks = get_marks($student['student_admission'], $exam_name, $subjectCol);
                // $pt = $marks['pt'] ?? 0;
                $nb = $marks['nb'] ?? 0;
                $se = $marks['se'] ?? 0;
                $mo = $marks['mo'] ?? 0;

                $total = $nb + $se + $mo;
                $totalMarks += $total;

                // Build subject record
                $responseData[] = [
                    'subject_id' => $subject_id,
                    'subject_name' => $subjectName,
                    'marks' => [
                        'nb' => $nb,
                        'se' => $se,
                        'mo' => $mo,
                        'total' => $total
                    ]
                ];
            }

            // ✅ Handle Co-Scholastic Areas
            $coScholasticData = [];
            if (!empty($co_scholastic_list)) {
                foreach ($co_scholastic_list as $co) {
                    $key = remove_space($co);
                    $coScholasticData[] = [
                        'area' => $co,
                        'grade' => $co_marks[$key] ?? 'N/A'
                    ];
                }
            }

            // Final response
            echo json_encode([
                'status' => 'success',
                'student' => [
                    'id' => $sid,
                    'name' => $student['student_name'] ?? '',
                    'class' => $student['student_class'] ?? ''
                ],
                'exam_name' => $exam_name,
                'subjects' => $responseData,
                'co_scholastic' => $coScholasticData, // ✅ Added section
                'grand_total' => $totalMarks
            ]);

            break;


        case "st_complaints":
            extract($data);
            $sql = "SELECT 
                        complaints.id, 
                        complaints.complaint_to, 
                        complaints.complaint, 
                        complaints.status, 
                        student.student_name,
                        student.student_class,
                        student.student_section,
                        student.student_roll,
                        student.student_mobile
                    FROM complaints
                    JOIN student ON complaints.student_id = student.id
                    WHERE complaints.student_id='$student_id' AND complaints.status NOT IN ('AUTO','DELETED')";
            $res = direct_sql($sql);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case "st_leave_applications":
            $res = direct_sql("SELECT id,status,student_id,from_date,to_date,cause FROM student_leave WHERE status NOT IN ('AUTO','DELETED')");
            extract($data);
            $sql = "SELECT id,status,student_id,from_date,to_date,cause 
                    FROM student_leave
                    WHERE student_id='$student_id' AND student_leave.status NOT IN ('AUTO','DELETED')";
            $res = direct_sql($sql);
            unset($res['sql']);
            echo json_encode($res);
            break;

        case 'initiate_phonepe_payment':
            $input = json_decode(file_get_contents('php://input'), true);

            $student_id = $input['student_id'] ?? '';
            $sinfo = get_data('student', $student_id);
            $student_name = $sinfo['student_name'] ?? '';
            $student_mobile = $sinfo['student_mobile'] ?? '';
            $amount = $input['amount'] ?? 0;
            $transaction_id = $input['transaction_id'] ?? '';
            $selected_months = $input['selected_months'] ?? [];
            $payment_method = $input['payment_method'] ?? 'PhonePe';

            // Validate inputs
            if (empty($student_id) || empty($amount) || empty($transaction_id)) {
                echo json_encode(['error' => 'Missing required fields']);
                exit;
            }

            // Convert selected months to JSON string
            $months_json = json_encode($selected_months);

            // ✅ INSERT into phonepe_transactions table (matching your table structure)
            $sql = "INSERT INTO phonepe_transactions (
                    merchant_transaction_id,
                    transaction_id,
                    student_id,
                    amount,
                    months,
                    status,
                    payment_data,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())";

            $stmt = $con->prepare($sql);

            // Store payment request data as JSON
            $payment_data = json_encode([
                'student_name' => $student_name,
                'payment_method' => $payment_method,
                'selected_months' => $selected_months
            ]);

            $stmt->bind_param(
                "sssdss",
                $transaction_id,           // merchant_transaction_id
                $transaction_id,           // transaction_id (same for now)
                $student_id,              // student_id
                $amount,                  // amount
                $months_json,             // months (JSON array)
                $payment_data             // payment_data (JSON object)
            );

            if (!$stmt->execute()) {
                echo json_encode(['error' => 'Error in Inserting Data: ' . $stmt->error]);
                exit;
            }

            $payment_id = $con->insert_id;

            // ✅ Now initiate PhonePe payment
            // PhonePe API configuration
            $merchant_id = "YOUR_MERCHANT_ID";
            $salt_key = "YOUR_SALT_KEY";
            $salt_index = "1";

            // PhonePe payment request
            $phonepe_data = [
                'merchantId' => $merchant_id,
                'merchantTransactionId' => $transaction_id,
                'merchantUserId' => 'MUID' . $student_id,
                'amount' => $amount * 100, // Convert to paise
                'redirectUrl' => 'https://dpsmushkipur.com/bine/api.php?action=phonepe_callback',
                'redirectMode' => 'POST',
                'callbackUrl' => 'https://dpsmushkipur.com/bine/api.php?action=phonepe_callback',
                'mobileNumber' => $student_mobile, // Get from student if available
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE'
                ]
            ];

            // Encode payload
            $json_encode = json_encode($phonepe_data);
            $payload = base64_encode($json_encode);

            // Generate checksum
            $checksum_string = $payload . '/pg/v1/pay' . $salt_key;
            $checksum = hash('sha256', $checksum_string) . '###' . $salt_index;

            // Make API call to PhonePe
            $url = 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay'; // Use production URL for live

            $headers = [
                'Content-Type: application/json',
                'X-VERIFY: ' . $checksum
            ];

            $request_data = [
                'request' => $payload
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response_data = json_decode($response, true);

            // Update payment_response in database
            $update_sql = "UPDATE phonepe_transactions SET payment_response = ? WHERE id = ?";
            $update_stmt = $con->prepare($update_sql);
            $update_stmt->bind_param("si", $response, $payment_id);
            $update_stmt->execute();

            if ($http_code == 200 && $response_data['success'] === true) {
                $payment_url = $response_data['data']['instrumentResponse']['redirectInfo']['url'];

                echo json_encode([
                    'success' => true,
                    'payment_url' => $payment_url,
                    'merchant_transaction_id' => $transaction_id,
                    'payment_id' => $payment_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => $response_data['message'] ?? 'PhonePe payment initiation failed'
                ]);
            }

            break;

        case "check_phonepe_payment":
            extract($data);

            // PhonePe Configuration
            $merchant_id = "SU2509191800322669510083";
            $salt_key = "5c5a0b98-69ee-4edc-a248-cc23a74b8862";
            $salt_index = 1;

            // Get transaction details from database
            $transaction = get_data('phonepe_transactions', $merchant_transaction_id, 'merchant_transaction_id', 'merchant_transaction_id');

            if ($transaction['count'] > 0) {
                $txn_data = $transaction['data'];

                // Check payment status with PhonePe
                $status_url = "https://api.phonepe.com/apis/hermes/pg/v1/status/" . $merchant_id . "/" . $merchant_transaction_id;

                // Generate X-VERIFY for status check
                $string_to_hash = '/pg/v1/status/' . $merchant_id . '/' . $merchant_transaction_id . $salt_key;
                $sha256 = hash('sha256', $string_to_hash);
                $x_verify = $sha256 . '###' . $salt_index;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $status_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'X-VERIFY: ' . $x_verify,
                        'X-MERCHANT-ID: ' . $merchant_id,
                        'accept: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                $response_data = json_decode($response, true);

                if ($response_data['success'] == true && $response_data['code'] == 'PAYMENT_SUCCESS') {
                    // Payment successful - create receipt
                    $student_id = $txn_data['student_id'];
                    $months = explode(",", $txn_data['months']);
                    $total_amount = $txn_data['amount'];

                    // Prepare receipt data similar to admin pay_fee
                    $rdata = nmonth_fee_all($student_id, $months);
                    $rdata['previous_dues'] = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
                    $rdata['paid_month'] = implode(",", $months);
                    $rdata['student_id'] = $student_id;
                    $rdata['student_admission'] = get_data('student', $student_id, 'student_admission')['data'];
                    $rdata['total'] = $total_amount;
                    $rdata['paid_amount'] = $total_amount;
                    $rdata['current_dues'] = 0; // Fully paid
                    $rdata['remarks'] = "Online Payment via PhonePe - TxnID: " . $merchant_transaction_id;
                    $rdata['other_fee'] = 0;
                    $rdata['discount'] = 0;
                    $rdata['paid_date'] = date('Y-m-d');
                    $rdata['created_by'] = $student_id; // Student made payment
                    $rdata['payment_mode'] = "ONLINE";
                    $rdata['phonepe_txn_id'] = $merchant_transaction_id;

                    $res = insert_data('receipt', $rdata);

                    if ($res['status'] == 'success') {
                        $rid = $res['id'];
                        $current_dues = 0;

                        // Update student_fee table
                        foreach ($months as $month) {
                            $old_value = get_data('student_fee', $student_id, remove_space($month), 'student_id')['data'];
                            if ($old_value != null && $month == 'other_month') {
                                $rid = $old_value . "," . $rid;
                            }
                            $res2 = update_data('student_fee', array(remove_space($month) => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
                        }

                        // Update transaction status
                        update_data('phonepe_transactions', array(
                            'status' => 'COMPLETED',
                            'receipt_id' => $rid,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'payment_response' => json_encode($response_data)
                        ), $merchant_transaction_id, 'merchant_transaction_id');

                        echo json_encode(array(
                            'status' => 'COMPLETED',
                            'receipt_id' => $rid,
                            'message' => 'Payment successful'
                        ));
                    }
                } else if ($response_data['code'] == 'PAYMENT_PENDING') {
                    echo json_encode(array(
                        'status' => 'PENDING',
                        'message' => 'Payment is still pending'
                    ));
                } else {
                    // Update transaction as failed
                    update_data('phonepe_transactions', array(
                        'status' => 'FAILED',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'payment_response' => json_encode($response_data)
                    ), $merchant_transaction_id, 'merchant_transaction_id');

                    echo json_encode(array(
                        'status' => 'FAILED',
                        'message' => 'Payment failed'
                    ));
                }
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ));
            }
            break;

        case "get_teacher_assignments":
            extract($data);
            $teacher_id = $data['teacher_id'] ?? $data['user_id'] ?? '';
            if (!empty($teacher_id)) {
                $teacher_id = mysqli_real_escape_string($con, $teacher_id);
                $sql = "SELECT ta.id, ta.student_class, ta.student_section, ta.subject_id, s.subject_name, s.subject_column 
                            FROM teacher_assignment ta 
                            LEFT JOIN subject s ON ta.subject_id = s.id 
                            WHERE ta.user_id = '$teacher_id' AND ta.status = 'ACTIVE'";
                $res = direct_sql($sql);
                unset($res['sql']);
                echo json_encode($res);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing teacher_id']);
            }
            break;

        case "get_teacher_homework_history":
            extract($data);
            $role = $data['role'] ?? 'TEACHER';
            $teacher_id = $data['teacher_id'] ?? $data['user_id'] ?? '';

            if ($role === 'ADMIN' || $role === 'DBA') {
                $sql_hw = "SELECT hw.id, hw.hw_date, hw.hw_class, hw.hw_section, hw.subject_id, hw.hw_text, hw.hw_file, s.subject_name 
                           FROM homework hw 
                           LEFT JOIN subject s ON hw.subject_id = s.id 
                           ORDER BY hw.hw_date DESC, hw.id DESC LIMIT 100";
                $res = direct_sql($sql_hw);
                unset($res['sql']);
                echo json_encode($res);
                break;
            }

            if (empty($teacher_id)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing teacher_id']);
                break;
            }

            $teacher_id = mysqli_real_escape_string($con, $teacher_id);
            $sql_assign = "SELECT student_class, student_section, subject_id FROM teacher_assignment WHERE user_id = '$teacher_id' AND status = 'ACTIVE'";
            $assign_res = direct_sql($sql_assign);

            if ($assign_res['count'] == 0) {
                echo json_encode(['status' => 'success', 'data' => []]);
                break;
            }

            $conditions = [];
            foreach ($assign_res['data'] as $assign) {
                $cls = mysqli_real_escape_string($con, $assign['student_class']);
                $sec = mysqli_real_escape_string($con, $assign['student_section']);
                $sub = mysqli_real_escape_string($con, $assign['subject_id']);
                $conditions[] = "(hw.hw_class = '$cls' AND hw.hw_section = '$sec' AND hw.subject_id = '$sub')";
            }

            $where_clause = implode(" OR ", $conditions);

            $sql_hw = "SELECT hw.id, hw.hw_date, hw.hw_class, hw.hw_section, hw.subject_id, hw.hw_text, hw.hw_file, s.subject_name 
                       FROM homework hw 
                       LEFT JOIN subject s ON hw.subject_id = s.id 
                       WHERE $where_clause 
                       ORDER BY hw.hw_date DESC, hw.id DESC LIMIT 100";
            $res = direct_sql($sql_hw);
            unset($res['sql']);
            echo json_encode($res);
            break;


        case "get_class_marks":
            extract($data);
            if (empty($student_class) || empty($student_section) || empty($exam_name) || empty($subject)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                break;
            }
            $subject = remove_space($subject);

            $students_res = get_all('student', '*', array(
                'student_class' => $student_class,
                'student_section' => $student_section,
                'status' => 'ACTIVE'
            ), 'student_roll ASC');

            $students_list = [];
            if ($students_res['count'] > 0) {
                foreach ($students_res['data'] as $row) {
                    $marks = get_marks($row['student_admission'], $exam_name, $subject);
                    $students_list[] = [
                        'id' => $row['id'],
                        'student_name' => $row['student_name'],
                        'student_roll' => $row['student_roll'],
                        'student_admission' => $row['student_admission'],
                        'marks' => [
                            'nb' => ($marks['nb'] !== null && $marks['nb'] !== '') ? $marks['nb'] : '',
                            'se' => ($marks['se'] !== null && $marks['se'] !== '') ? $marks['se'] : '',
                            'mo' => ($marks['mo'] !== null && $marks['mo'] !== '') ? $marks['mo'] : ''
                        ]
                    ];
                }
            }
            echo json_encode([
                'status' => 'success',
                'data' => $students_list
            ]);
            break;

        case "marks_entry":
            extract($data);
            if (empty($exam_name) || empty($subject) || empty($marks_data)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                break;
            }
            $subject = remove_space($subject);
            $se = $subject . "_se";
            $mo = $subject . "_mo";
            $nb = $subject . "_nb";

            $p = 0;
            foreach ($marks_data as $row) {
                $student_id = $row['student_id'];
                $student_admission = $row['student_admission'];
                $nb_val = isset($row['nb']) ? floatval($row['nb']) : 0.0;
                $se_val = isset($row['se']) ? floatval($row['se']) : 0.0;
                $mo_val = isset($row['mo']) ? floatval($row['mo']) : 0.0;

                $adata = array('student_admission' => $student_admission, 'student_id' => $student_id, 'exam_name' => $exam_name);
                insert_data('exam', $adata);

                $u_data = array($se => $se_val, $mo => $mo_val, $nb => $nb_val, 'student_id' => $student_id, 'status' => 'updated');
                $where = array('student_admission' => $student_admission, 'exam_name' => $exam_name);
                $res = update_multi_data('exam', $u_data, $where);
                if ($res['status'] === 'success') {
                    $p++;
                }
            }
            echo json_encode(['status' => 'success', 'message' => "Successfully saved marks for $p students", 'count' => $p]);
            break;

        case "check_gps_boundary":
            extract($data);
            if (empty($latitude) || empty($longitude)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing latitude or longitude']);
                break;
            }

            // Ensure settings table exists
            $create_settings_table_sql = "CREATE TABLE IF NOT EXISTS attendance_settings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    latitude VARCHAR(50) NOT NULL,
                    longitude VARCHAR(50) NOT NULL,
                    radius DOUBLE NOT NULL DEFAULT 0.0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
            mysqli_query($con, $create_settings_table_sql);

            $gps_settings = direct_sql("SELECT * FROM attendance_settings LIMIT 1");
            if ($gps_settings['count'] > 0) {
                $allowed_lat = floatval($gps_settings['data'][0]['latitude']);
                $allowed_lng = floatval($gps_settings['data'][0]['longitude']);
                $radius = floatval($gps_settings['data'][0]['radius']);

                if ($allowed_lat != 0 && $allowed_lng != 0 && $radius > 0) {
                    $emp_lat = floatval($latitude);
                    $emp_lng = floatval($longitude);

                    // Haversine distance calculation (in meters)
                    $earth_radius = 6371000;
                    $latFrom = deg2rad($emp_lat);
                    $lonFrom = deg2rad($emp_lng);
                    $latTo = deg2rad($allowed_lat);
                    $lonTo = deg2rad($allowed_lng);

                    $latDelta = $latTo - $latFrom;
                    $lonDelta = $lonTo - $lonFrom;

                    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                    $distance = $angle * $earth_radius;

                    if ($distance <= $radius) {
                        echo json_encode([
                            'status' => 'success',
                            'in_radius' => true,
                            'distance' => round($distance),
                            'allowed_radius' => round($radius),
                            'message' => 'You are within the school boundary.'
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'success',
                            'in_radius' => false,
                            'distance' => round($distance),
                            'allowed_radius' => round($radius),
                            'message' => 'You are outside the school boundary (Distance: ' . round($distance) . 'm, Allowed: ' . round($radius) . 'm).'
                        ]);
                    }
                    break;
                }
            }

            // If not configured, default to true (within boundary)
            echo json_encode([
                'status' => 'success',
                'in_radius' => true,
                'distance' => 0,
                'allowed_radius' => 0,
                'message' => 'GPS boundary settings not configured by admin. Allowed by default.'
            ]);
            break;

        case "get_all_employee_attendance":
            extract($data);
            $att_date = $att_date ?? date('Y-m-d');

            // selfie_attendance.emp_id = employee.id (via user.created_by)
            $employees = direct_sql("SELECT id, e_name, designation, e_category AS department FROM employee WHERE status NOT IN('BLOCK','AUTO') ORDER BY e_name ASC");
            $list = [];

            if ($employees['count'] > 0) {
                foreach ($employees['data'] as $emp) {
                    $emp_id = $emp['id'];

                    // Query selfie check-in/out for this date
                    $selfie_sql = "SELECT latitude, longitude, selfie_file, created_at,
                                          checkout_latitude, checkout_longitude, checkout_file, checkout_time
                                   FROM selfie_attendance
                                   WHERE emp_id = '$emp_id' AND att_date = '$att_date' LIMIT 1";
                    $selfie_res = direct_sql($selfie_sql);

                    $checkin_status = 'ABSENT';
                    $checkin_info = null;

                    if ($selfie_res['count'] > 0) {
                        $sr = $selfie_res['data'][0];
                        $checkin_status = 'PRESENT';
                        $checkin_info = [
                            'latitude' => $sr['latitude'],
                            'longitude' => $sr['longitude'],
                            'selfie_file' => $sr['selfie_file'],
                            'checkin_time' => $sr['created_at'] ? date('h:i A', strtotime($sr['created_at'])) : null,
                            'checkout_latitude' => $sr['checkout_latitude'] ?? null,
                            'checkout_longitude' => $sr['checkout_longitude'] ?? null,
                            'checkout_file' => $sr['checkout_file'] ?? null,
                            'checkout_time' => $sr['checkout_time'] ? date('h:i A', strtotime($sr['checkout_time'])) : null
                        ];
                    } else {
                        // Fallback: check employee_att grid (P/L/A)
                        $col_name = 'd_' . date('j', strtotime($att_date));
                        $mvalue = remove_space(date('M_Y', strtotime($att_date)));
                        $att_check = direct_sql("SELECT $col_name as day_val FROM employee_att WHERE emp_id = '$emp_id' AND att_month = '$mvalue' LIMIT 1");
                        if ($att_check['count'] > 0) {
                            $status_val = $att_check['data'][0]['day_val'];
                            if ($status_val === 'P') {
                                $checkin_status = 'PRESENT';
                            } elseif ($status_val === 'L') {
                                $checkin_status = 'LEAVE';
                            }
                        }
                    }

                    $list[] = [
                        'emp_id' => $emp_id,
                        'name' => $emp['e_name'],
                        'department' => $emp['department'],
                        'designation' => $emp['designation'],
                        'status' => $checkin_status,
                        'checkin_info' => $checkin_info
                    ];
                }
            }

            echo json_encode([
                'status' => 'success',
                'date' => $att_date,
                'data' => $list
            ]);
            break;


        case "submit_selfie_attendance":
            extract($data);
            if (empty($user_id) || empty($latitude) || empty($longitude) || empty($selfie_image)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
                break;
            }

            $gps_settings = direct_sql("SELECT * FROM attendance_settings LIMIT 1");
            if ($gps_settings['count'] > 0) {
                $allowed_lat = floatval($gps_settings['data'][0]['latitude']);
                $allowed_lng = floatval($gps_settings['data'][0]['longitude']);
                $radius = floatval($gps_settings['data'][0]['radius']);

                if ($allowed_lat != 0 && $allowed_lng != 0 && $radius > 0) {
                    $emp_lat = floatval($latitude);
                    $emp_lng = floatval($longitude);

                    // Haversine distance calculation (in meters)
                    $earth_radius = 6371000;
                    $latFrom = deg2rad($emp_lat);
                    $lonFrom = deg2rad($emp_lng);
                    $latTo = deg2rad($allowed_lat);
                    $lonTo = deg2rad($allowed_lng);

                    $latDelta = $latTo - $latFrom;
                    $lonDelta = $lonTo - $lonFrom;

                    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                    $distance = $angle * $earth_radius;

                    if ($distance > $radius) {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Attendance marking blocked! You are outside the permitted radius (Distance: ' . round($distance) . 'm, Allowed: ' . round($radius) . 'm).'
                        ]);
                        break;
                    }
                }
            }

            $user_info = get_data('user', $user_id);
            if (empty($user_info['data'])) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                break;
            }
            $emp_id = $user_info['data']['created_by'];
            if (empty($emp_id)) {
                echo json_encode(['status' => 'error', 'message' => 'Employee record not associated with this user']);
                break;
            }

            // Check if checkout columns exist, if not add them
            $chk_col = mysqli_query($con, "SHOW COLUMNS FROM selfie_attendance LIKE 'checkout_file'");
            if (mysqli_num_rows($chk_col) == 0) {
                mysqli_query($con, "ALTER TABLE selfie_attendance 
                    ADD checkout_latitude VARCHAR(50) NULL,
                    ADD checkout_longitude VARCHAR(50) NULL,
                    ADD checkout_file VARCHAR(255) NULL,
                    ADD checkout_time TIMESTAMP NULL");
            }

            $action = $data['action'] ?? 'checkin'; // 'checkin' or 'checkout'

            $base_to_php = explode(',', $selfie_image);
            $img_data = base64_decode(end($base_to_php));
            $file_name = "selfie_" . $emp_id . "_" . date('ymdhis') . "_" . rnd_str(5) . ".png";
            $filepath = "upload/" . $file_name;
            file_put_contents($filepath, $img_data);

            $att_date = $att_date ?? date('Y-m-d');
            $lat = mysqli_real_escape_string($con, $latitude);
            $lng = mysqli_real_escape_string($con, $longitude);

            if ($action === 'checkout') {
                $ins_sql = "INSERT INTO selfie_attendance (emp_id, att_date, checkout_latitude, checkout_longitude, checkout_file, checkout_time) 
                                VALUES ('$emp_id', '$att_date', '$lat', '$lng', '$file_name', CURRENT_TIMESTAMP)
                                ON DUPLICATE KEY UPDATE checkout_latitude='$lat', checkout_longitude='$lng', checkout_file='$file_name', checkout_time=CURRENT_TIMESTAMP";
            } else {
                $ins_sql = "INSERT INTO selfie_attendance (emp_id, att_date, latitude, longitude, selfie_file, created_at) 
                                VALUES ('$emp_id', '$att_date', '$lat', '$lng', '$file_name', CURRENT_TIMESTAMP)
                                ON DUPLICATE KEY UPDATE latitude='$lat', longitude='$lng', selfie_file='$file_name', created_at=CURRENT_TIMESTAMP";
            }
            mysqli_query($con, $ins_sql);

            $mvalue = date('M_Y', strtotime($att_date));
            $mvalue = remove_space($mvalue);
            $col_name = 'd_' . date('j', strtotime($att_date));
            $tbl_name = 'employee_att';

            // Use direct SQL to bypass the regex bug in op_lib.php that strips underscores
            $check_sql = "SELECT id FROM $tbl_name WHERE emp_id = '$emp_id' AND att_month = '$mvalue'";
            $search = direct_sql($check_sql);

            if ($search['count'] > 0) {
                $update_sql = "UPDATE $tbl_name SET $col_name = 'P', status = 'ACTIVE' WHERE emp_id = '$emp_id' AND att_month = '$mvalue'";
                direct_sql($update_sql, 'set');
            } else {
                insert_data($tbl_name, ['emp_id' => $emp_id, 'att_month' => $mvalue, $col_name => 'P', 'status' => 'ACTIVE']);
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Attendance marked successfully with GPS & Selfie!',
                'selfie_url' => 'https://dpsmushkipur.com/bine/upload/' . $file_name
            ]);
            break;

        case "get_my_attendance":
            extract($data);
            if (empty($user_id)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
                break;
            }
            $user_info = get_data('user', $user_id);
            if (empty($user_info['data'])) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                break;
            }
            $emp_id = $user_info['data']['created_by'];
            if (empty($emp_id)) {
                echo json_encode(['status' => 'error', 'message' => 'Employee record not associated']);
                break;
            }

            $mnth = remove_space(date('M_Y'));
            $att_search = get_multi_data('employee_att', array('att_month' => $mnth, 'emp_id' => $emp_id));
            $all_data = $att_search['data'] ?? null;

            $selfie_sql = "SELECT id, att_date, latitude, longitude, selfie_file, created_at, checkout_latitude, checkout_longitude, checkout_file, checkout_time FROM selfie_attendance WHERE emp_id='$emp_id' ORDER BY att_date DESC LIMIT 50";
            $selfie_res = direct_sql($selfie_sql);

            // School GPS coordinates from settings
            $gps_settings = direct_sql("SELECT latitude, longitude FROM attendance_settings LIMIT 1");
            $school_lat = $gps_settings['data'][0]['latitude'] ?? null;
            $school_lng = $gps_settings['data'][0]['longitude'] ?? null;

            echo json_encode([
                'status' => 'success',
                'attendance' => $all_data,
                'selfie_logs' => $selfie_res['data'] ?? [],
                'school_lat' => $school_lat,
                'school_lng' => $school_lng
            ]);
            break;

        case "get_emp_attendance":
            // Admin audit: fetch attendance logs directly by employee.id
            extract($data);
            if (empty($emp_id)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing emp_id']);
                break;
            }

            // All selfie logs for this employee
            $selfie_sql = "SELECT id, att_date, latitude, longitude, selfie_file, created_at,
                                  checkout_latitude, checkout_longitude, checkout_file, checkout_time
                           FROM selfie_attendance
                           WHERE emp_id = '$emp_id'
                           ORDER BY att_date DESC LIMIT 100";
            $selfie_res = direct_sql($selfie_sql);

            // All monthly attendance_att grid rows for this employee
            $grid_sql = "SELECT * FROM employee_att WHERE emp_id = '$emp_id' ORDER BY att_month DESC LIMIT 24";
            $grid_res = direct_sql($grid_sql);

            // Employee details
            $emp_info = direct_sql("SELECT id, e_name, e_category AS department, designation FROM employee WHERE id = '$emp_id' LIMIT 1");

            // School GPS coordinates from settings
            $gps_settings = direct_sql("SELECT latitude, longitude FROM attendance_settings LIMIT 1");
            $school_lat = $gps_settings['data'][0]['latitude'] ?? null;
            $school_lng = $gps_settings['data'][0]['longitude'] ?? null;

            echo json_encode([
                'status' => 'success',
                'emp' => $emp_info['data'][0] ?? null,
                'selfie_logs' => $selfie_res['data'] ?? [],
                'attendance' => $grid_res['data'] ?? [],
                'school_lat' => $school_lat,
                'school_lng' => $school_lng
            ]);
            break;



        // 		    case "app_version" :
// 		        $res = array("new_version"=>$app_version);
// 		        //echo json_encode($res);
// 		        echo "9.0.0";
// 		        break;

        // 		    case "get_student_by_mobile":
// 		    case "get_mobile":
// 				$mob_no =$_REQUEST['student_mobile'];
// 				$sql ="select id,student_name, student_admission, student_class, student_roll, student_section from student where student_mobile='$mob_no' and status ='ACTIVE'";

        // 				$res =direct_sql($sql);
// 				if($res['count']>0)
// 				{
// 				    $res['otp'] = $otp= rand(1000,9999);
// 				    $sms ="Thanks for choosing $inst_name Your login OTP is $otp Regards $inst_url Via Bine";
// 				    $template_id = '1507163688631788984';
// 				    send_msg($mob_no, $sms, $template_id);
// 				}
// 				echo json_encode($res);
// 				break;

        // 			 case "op_history":
// 				$adm =$_REQUEST['adm_no'];
// 				$sql ="select * from online_payment where student_id='$adm' and amount > 0";
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				break;

        // 			case "complain_list":
// 				$adm =$_REQUEST['req_by'];
// 				$sql ="select * from app_request where student_admission='$adm'";
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				break;
// 			case "add_logsheet" :
// 				extract($_POST);
// 				$res = insert_data('logsheet',$_POST);
// 				echo json_encode($res);
// 				break;

        // 			case "make_att" :
// 				$stu_list =$_POST['sel_id'];
// 				$att_date =$_POST['att_date'];
// 				$mvalue =date('M Y',strtotime($att_date));
// 				$mvalue = removespace($mvalue);

        // 				$col_name ='d_'.date('j',strtotime($att_date));
// 				$tbl_name ='student_att'; 

        // 				foreach($stu_list as $adm_no)
// 				{
// 				    $stu_id =studentid($adm_no);
// 				    $res = insert_data($tbl_name,array('id'=>$stu_id,'att_month'=>$mvalue));
// 					$post = array( $col_name=>'P','student_admission'=>$adm_no);
// 					$res2 = update_multi_data($tbl_name,$post,array('id'=>$stu_id,'att_month'=>$mvalue));

        // 				}
// 				echo json_encode($res2);
// 				break;

        // 			case "upload" :
// 				$result =uploadapp('uploadimg');
// 				echo json_encode($result);
// 				break;

        // 			case "get_dues":
// 				//$mob_no =$_REQUEST['mobile'];
// 				//$res =duesonapp($mob_no);
// 				$adm_no =$_REQUEST['adm_no'];
// 				$res =duesviaadm($adm_no);
// 				//print_r($res);
// 				echo json_encode($res);
// 				break;

        // 			case "student_list":
// 				$s_class =$_REQUEST['student_class'];
// 				$s_sec =$_REQUEST['student_section'];
// 				$sql ="select * from student where student_class='$s_class' and student_section ='$s_sec' and status <>'BLOCK' order by student_roll";
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				break;

        // 		    case "student_list2":
// 				$s_class =$_REQUEST['student_class'];
// 				$s_sec =$_REQUEST['student_section'];

        // 				$res =get_all2('student','*',array('student_class'=>$s_class,'student_section'=>$s_sec));
// 				echo $res;
// 				break;

        // 		    case "staffdetails":
// 				$mob_no =$_REQUEST['mobile'];
// 				$sql ="select * from staff_details where e_mobile='$mob_no' and status <>'BLOCK'";
// 				update_data('staff_details',array('last_app_login'=>date('Y-m-d h:i:s')),$mob_no, 'e_mobile');
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				break;

        // 			 case "verify_student":
// 				$mob_no =$_REQUEST['mobile'];
// 				$data = verify_student($mob_no);
// 				echo $data;
// 				break;

        // 			 case "verify_staff":
// 				$mob_no =$_REQUEST['mobile'];
// 				$data = verify_staff($mob_no);
// 				echo $data;
// 				break;

        // 			case "get_student" :
// 			 //  if($utable=='student')
// 			 //  {
// 			 //  }
// 			    extract($_REQUEST);

        // 			    $sql ="select * from student where student_admission ='$adm_no' and status <>'BLOCK'";
// 				$all_data = direct_sql($sql,false);
// 				$data = json_decode($all_data,true);
// 				//print_r($data);
// 				$data[0]['date_of_birth'] =date('d-M-Y',strtotime($data[0]['date_of_birth']));
// 				echo json_encode($data);
// 			    break;

        // 			case "get_student2" :
// 			    if($utable=='student')
// 			    {
// 			    extract($_REQUEST);
// 			    $sql ="select * from student where student_admission ='$adm_no' and status <>'BLOCK'";
// 				$data = direct_sql2($sql);
// 				$data[0]['date_of_birth'] =date('d-M-Y',strtotime($data[0]['date_of_birth']));

        // 			   }
// 			   else{
// 			       $data =array("count"=>0,"status"=>"error","msg"=>"Your are Not Allowed to Access");
// 			   }
// 			   echo json_encode($data);
// 			   break;

        // 			case "get_att" :
// 			    $adm_no =$_REQUEST['adm_no'];
// 				$id =studentid($adm_no);
// 				$d=cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
// 				$mnth =removespace(date('M_Y'));
// 				$ct = get_multi_data('student_att',array('att_month'=>$mnth,'id'=>$id))['count'];
// 				if($ct>0)
// 				{
// 				$all_data = get_multi_data('student_att',array('att_month'=>$mnth,'id'=>$id))['data'];
// 				$all_data['month_name'] =$mnth;
// 				$all_data['total_day'] =$d;
// 				$all_data['today'] =date('d');
// 				echo json_encode($all_data);
// 				}
// 				else{
// 				    echo 'No Record Found';
// 				}

        // 				break;

        // 			case "emp_att" :
// 			    $emp_id =$_REQUEST['emp_id'];
// 			   // print_r($_REQUEST);
// 				$d=cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
// 				$mnth =removespace(date('M_Y'));
// 				$all_data = get_multi_data('emp_att',array('att_month'=>$mnth,'id'=>$emp_id))['data'];
// 				$all_data = get_array('emp_att',$emp_id);
// 				$all_data['month_name'] =$mnth;
// 				$all_data['total_day'] =$d;
// 				$all_data['today'] =date('d');
// 				echo json_encode($all_data);
// 				break;

        // 			case "get_hw" :
// 				$adm_no =$_REQUEST['adm_no'];
// 				$hw_date=$_REQUEST['hw_date'];
// 				$id =studentid($adm_no);
// 				$sclass =get_data('student',$id,'student_class');
// 				$ssec =get_data('student',$id,'student_section');
// 				//$sql ="select p1_hw,p2_hw,p3_hw,p4_hw,p5_hw,p6_hw,p7_hw, hw_file from logsheet where hw_class='$sclass' and hw_section='$ssec' and hw_date ='$hw_date'";
// 				$sql ="select * from logsheet where hw_class='$sclass' and hw_section='$ssec' and hw_date ='$hw_date'";
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				break;

        // 			case "get_fee" :
// 				$adm_no =$_REQUEST['adm_no'];
// 				$id =studentid($adm_no);
// 				$sql ="select * from receipt where student_id ='$id' and status ='PAID'";
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				break;

        // 			case "get_gps" :
// 				$adm_no =$_REQUEST['adm_no'];
// 				$id =studentid($adm_no);
// 				$trip_id =get_data('student',$id,'trip_id');
// 				$gps_key =get_data('trip_details',$trip_id,'gps_key');
// 				echo json_encode(array('gps_key'=>$gps_key));
// 				break;

        // 			case "get_holiday" :
// 			    $sql ="select * from holiday where status <>'HIDE' order by id desc ";
// 			    $res =direct_sql($sql,false);
// 				echo $res;
// 				break;

        // 			case "get_notice" :
// 			    $bine_type =$_REQUEST['bine_type'];
// 			    if($bine_type =='verify_staff')
// 			    {
// 			        $sql ="select * from notice where status <>'HIDE' order by id desc limit 10";
// 			    }
// 				else{
// 				    $sql ="select * from notice where status ='PUBLIC' order by id desc limit 10"; 
// 				}
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				break;

        // 			case "app_request" :
// 			     //print_r($_FILES);
// 			     //$cfile = uploadimg('cfile');
// 			     //$_POST['cfile'] =$cfile;
// 			     // Takes raw data from the request
//                  //print_r($_REQUEST);
//                  $_POST['status'] ='PENDING';
//                  $_POST['req_date_time'] =date('Y-m-d h:i:s');
// 			     $res = insert_data('app_request',$_POST);
// 			     echo "Request Accepted with Request Id : " .$res['id'];
// 			     break;

        // 			 case "app_status" :
// 			     $link_id =$_REQUEST['link_id'];
// 			     $adm_no =$_REQUEST['adm_no'];
// 			     $id =studentid($adm_no);
// 			     $viewer = get_data('study_material',$link_id,'viewer');
// 			     if($viewer =='')
// 			     {
// 			         $data = array('viewer'=>$id);
// 			         update_data('study_material',$data,$link_id);
// 			     }
// 			     else{
// 			         $id = $viewer.",".$id;
// 			         $data = array('viewer'=>$id);
// 			         update_data('study_material',$data,$link_id); 
// 			     }
//                  break;

        // 			case "get_request" :
// 			     extract($_REQUEST);
// 			     //print_r($_REQUEST);
// 			     $res = get_all('app_request','*', $req_mobile,'req_mobile');
// 			     echo $res;
// 			     break;

        // 			 case "get_study" :
// 			    //print_r($_REQUEST);
// 			    $adm_no =$_REQUEST['adm_no'];
// 				$study_date=$_REQUEST['study_date'];
// 				$id =studentid($adm_no);
// 				$sclass =get_data('student',$id,'student_class');
// 				$allow_oc =get_data2('student',$id,'allow_oc')['data'];
// 				if($allow_oc=='yes')
// 				{
// 				$sql ="select * from study_material where student_class='$sclass' and study_date ='$study_date' and status ='Active'";
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 				}
// 				else{
// 				    echo "No Record Found";
// 				}
// 			     break;

        // 			 case "staff_get_study" :
// 			    //print_r($_REQUEST);
// 			   	$study_date=$_REQUEST['study_date'];
// 			    $sql ="select * from study_material where study_date='$study_date' and status ='Active' order by student_class";
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 			    break;


        // 			/*--------------ONLINE EXAM ------------*/

        // 			case "get_exam" :
// 			    //print_r($_REQUEST);
// 			    $adm_no =$_REQUEST['adm_no'];
// 				$id =studentid($adm_no);
// 				$sclass =get_data('student',$id,'student_class');
// 			    $exam_st = get_data('student',$id,'allow_exam'); //'DEMO';

        // 			    if($exam_st =='yes')
// 			    {
// 				  //$sql ="select * from set_details where student_class='$sclass' and status <> 'PENDING'"; 
// 				  $sql ="select * from set_details where set_name like 'DEMO%'"; 
// 				  $res =direct_sql($sql,false);
// 				  echo $res;
// 			     }
// 			break;


        // 			 case "get_exam2" :
// 			   // print_r($_REQUEST);
// 			   if($utable=='student')
// 			   {
//     			    $adm_no =$_REQUEST['adm_no'];
//     				$id =studentid($adm_no);
//     				$sclass =get_data('student',$id,'student_class');
//     				$exam_st = get_data('student',$id,'allow_exam'); //'DEMO';

        //     				if(trim($exam_st) =='yes')
//     				{
//     				  $sql ="select * from set_details where student_class='$sclass' and status <> 'PENDING'"; 
//     				  //$sql ="select * from set_details where set_name like 'DEMO%'";
//     				  $res =direct_sql2($sql,false);
//     				  //echo $res;
//     				}
//     				else{
//     				   $res = array("status"=>"error","count"=>0,"msg"=>"You are not eligible contact to acount office");
//     				}
// 			   }
// 			   else{
// 			       $res = array("status"=>"error","count"=>0,"msg"=>"You are not allowed to access this information"); 
// 			   }

        // 				echo json_encode($res);
// 			 break;

        // 			 case "get_question":
// 			       //print_r($_REQUEST);
//     			     extract($_REQUEST);
//     		         $set_id = $_REQUEST['set_id'];
//     		        $adm_no =$_REQUEST['adm_no'];
//     				$id =studentid($adm_no);
//     				$sclass =get_data('student',$id,'student_class');

        //     		         $answer = insert_data('answer',array('set_id'=>$set_id, 'student_id'=>$id, 'entry_time'=>date('Y-m-d h:i:s'),'status'=>'PENDING'));

        //     		         //print_r($answer);
//     		         $all_question['ans_id'] =$answer['id'];
//         		     $res = get_data('set_details',$set_id, 'question_list');

        //         		         $qnos = explode(',',$res);
//         		         $all_question['count'] =count($qnos);
//         		         $all_question['status'] ='success';
//         		         foreach($qnos as $qno)
//         		         {
//         		            $qdetails = get_data2('qbank',$qno)['data'];

        //         		             $all_question['data'][] = $qdetails; 

        //         		         }

        //     		     // update_data('api_history',array('created_by'=>$req_by,'status'=>$res['status'],'res_data'=>json_encode($res['data'])),$req_id);
//     		        //echo "<pre>";
//     		        //print_r($all_question);
//     			    echo json_encode($all_question);
//     				break;

        //     		case "save_answer" :
//     		   // print_r($_REQUEST);
// 				extract($_REQUEST);
// 				$res = update_data('answer',array($q_id=>$yans),$ans_id);
// 				echo json_encode($res);
// 				break;

        //           	case "final_submit" :
// 				//print_r($_POST);
// 				extract($_REQUEST);
// 				$res = update_data('answer',array('status'=>'FINISH','exit_time'=>date('Y-m-d h:i:s')),$ans_id);
// 				$res['msg'] ="Thanks for Joining $inst_name Online Mock Test";
// 				//$res['url'] =$inst_url;
// 				echo json_encode($res);
// 				break;

        // 			case "set_result" :
// 			    //print_r($_REQUEST);
// 			    $set_id = $_REQUEST['set_id'];
//     		    $adm_no =$_REQUEST['adm_no'];
//     			$id =studentid($adm_no);
//     			$res =set_result($id, $set_id);
//     			echo json_encode($res);
// 				break;

        // 			case "class_result" :
// 			    //print_r($_REQUEST);
// 			     $set_id = $_REQUEST['set_id'];
// 			    $student_class= $_REQUEST['student_class'];
// 			    //$student_section= $_REQUEST['student_section'];
// 			    $sql ="Select * from student where student_class='$student_class' order by student_section, student_roll";


        // 			    $res = direct_sql2($sql);

        // 			    foreach ($res['data'] as $student)
// 			    {
//     		        $id =$student['id'];

        //     		        $all[]=set_result($id, $set_id);
// 			    }
//     			echo json_encode($all);
// 				break;

        // 		    case "exam_list" :
// 			  	$sclass =$_REQUEST['student_class'];
// 				$sql ="select * from set_details where student_class='$sclass' and status <> 'PENDING' "; 
// 				$res =direct_sql($sql,false);
// 				echo $res;
// 			 break;


        // 			 case "exam_report" :
// 			     extract($_POST);
// 			    $adm_no =$_REQUEST['adm_no'];
//     			$id = studentid($adm_no);
// 			    $student_class =get_data2('student',$id,'student_class')['data'];
// 			    $result['<b>Name </b>'] ="<b style='color:red'>".get_data2('student',$id,'student_name')['data'] ."<b>";
// 			    $result['<b>Class</b>'] ="<b>". get_data2('student',$id,'student_class')['data'] ."-". get_data2('student',$id,'student_section')['data'] ."</b>";

        // 			    $result['<b>Roll</b>'] ="<b>" .get_data2('student',$id,'student_roll')['data'] ."</b>";
// 			 	$lst1 =	create_list('set_details','id',array('student_class'=> $student_class));
// 				$total =0;
// 				$ct = count($lst1);

        // 				foreach($lst1 as $single)
// 				{
// 				    $name=get_data2('set_details', $single,'subject')['data'];
// 				    $marks=set_result($id, $single)['marks'];
// 				    $result[$name]= $marks;
// 				    $total =$total +$marks;
// 				}    
// 				$result['<big>Total</big>'] ="<big>".$total ."</big>";
// 				$per  = round($total /$ct,2) ;
// 				$result['Percentage'] ="<i>".$per ." % <i>";
// 				echo json_encode($result);
// 			  break;   

        // 			case "class_report" :
// 			     extract($_POST);
// 			    $student_class =$_REQUEST['student_class'];

        // 			    $all_student = get_all2("student",'*',array("student_class"=>$student_class))['data'];
// 			    foreach($all_student as $student)
// 			    {

        //         			$id = $student['id'];
//         			$all_result[] =$student['student_admission'];
//         			$all_result[] =$student['student_name'];
//     			   	$lst1 =	create_list('set_details','id',array('student_class'=> $student_class));
//     				$total =0;
//     				$ct = count($lst1);

        //     				foreach($lst1 as $single)
//     				{
//     				    $name=get_data2('set_details', $single,'subject')['data'];
//     				    $marks=set_result($id, $single)['marks'];
//     				    $result[$name]= $marks;
//     				}    

        // 			        $all_result[] =$result;    
// 			    }
// 			    echo json_encode($all_result);
// 			  break;   

        // 		case "marks_entry" :
// 				extract($_REQUEST);
// 				parse_str($_POST['marks_data'], $marks);
// 			    extract($marks);
// 			    $student_list= array_keys($marks);
// 				foreach($student_list as $student)
// 				{
// 				    $student_admission = substr($student,6);
// 				    $marks = $$student;

        // 					/*//$student_id = $_POST['student_id'][$i];
// 					 $_POST['student_admission'][$i];
// 					$exam_name=$_POST['exam_name'];
// 					$subject =removespace($_POST['subject_name']);
// 					$se = $subject."_se";

        // 					$nb = $subject."_nb";*/

        // 					$mo = $subject_name."_mo";
// 					$where =array('student_admission' =>$student_admission, 'exam_name' =>$exam_name,'status'=>'PENDING'); 
// 					$res2 = get_all('exam','*',$where);
// 					if($res2['count']==0)
// 					{
// 					$adata =array('student_admission' => $student_admission, 'student_id'=>studentid($student_admission), 'exam_name'=>$exam_name);
// 					insert_data('exam',$adata);
// 					}

        // 					$res = update_multi_data('exam',array($mo=>$marks),$where);
// 				}
// 				echo json_encode($res);
// 				break;	  

        default:
            echo " No Task Selected or Invalid Task";
    }
}


?>