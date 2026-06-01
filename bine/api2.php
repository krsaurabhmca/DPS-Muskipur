<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');
require('function.php');

function direct_query($sql, $type = 'get')
{
    global $con;
    
    
    // Check if the query is a SELECT and branch_id exists
    if (strtoupper(substr(trim($sql), 0, 6)) === 'SELECT' && $branch_id && $allow_branch ) {
        // Check if WHERE clause exists
        if (stripos($sql, 'WHERE') === false) {
            // No WHERE clause, add branch_id filter
            $sql = preg_replace('/ORDER BY|LIMIT|GROUP BY/i', "WHERE $0", $sql, 1);
        } else {
            // WHERE clause exists, append branch_id filter
            $sql = preg_replace('/ORDER BY|LIMIT|GROUP BY/i', " $0", $sql, 1);
        }
    }
	//echo $sql;
    // Execute the query
    $res = mysqli_query($con, $sql);
    
    // Handle errors
    if (!$res) {
        return [
            'count' => 0,
            'status' => 'error',
            'data' => null,
            'message' => mysqli_error($con),
            'sql' => $sql
        ];
    }

    // Process query based on type
    if ($type === 'set') { // For INSERT, UPDATE, DELETE
        $affected_rows = mysqli_affected_rows($con);
        return [
            'count' => $affected_rows,
            'status' => $affected_rows > 0 ? 'success' : 'error',
            'data' => null,
            'sql' => $sql
        ];
    } else { // For SELECT
        $rows = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $rows[] = $row;
        }
        return [
            'count' => count($rows),
            'status' => count($rows) > 0 ? 'success' : 'error',
            'data' => count($rows) > 0 ? $rows : null,
            'sql' => $sql
        ];
    }
}


$_POST = $data = json_decode(file_get_contents('php://input'), true);
$_GET =post_clean($_GET);
	$task =xss_clean($_GET['task']);
	switch($task)
		{
			
	    case "app_version" :
	        $res = array("app_version"=>"1.0.0", "app_name"=>"com.offerplant.bine");
	        echo json_encode($res);
	        break;
        
		case "get_otp":
			extract($_POST);
			$sql ="select id from student where student_mobile='$student_mobile' and status ='ACTIVE'";
			$res =direct_query($sql, 'get', false);
			if($res['status'] =='success' and $res['count']>0)
			{
				$res['otp'] = $otp = 9999;// rand(1000,9999);
    	        $res['msg'] = $msg = "Thanks for interest in  $inst_name  Your App Login OTP is $otp ";
    	        //sendsms($student_mobile, $msg);
			}else{
			    $res = null;
			}
			unset($res['sql']);
			echo json_encode($res);
			break;
			
		case "get_student_by_mobile" :
		    extract($_POST);
		    $sql = "SELECT id, student_name, student_photo, student_admission,student_class, student_roll, student_section, student_mobile FROM student WHERE student_mobile='$student_mobile' AND status='ACTIVE'";
			$res =direct_query($sql, 'get', false);
			if($res['status'] =='success' and $res['count']>0){
			    $res = $res;
			}else{
			    $res = null;
			}
			unset($res['sql']);
			echo json_encode($res);
		    break;
		    
		case "get_homework" :
		    extract($_POST);
		    $sql = "SELECT 
                student.id AS sid,
                student.student_class,
                student.student_section,
                home_work.id AS hid,
                home_work.hw_date,
                home_work.class,
                home_work.section,
                home_work.hw_title,
                home_work.hw_details,
                home_work.hw_attachment,
                home_work.status
            FROM student
            INNER JOIN home_work 
                ON student.student_class = home_work.class 
                AND student.student_section = home_work.section
            WHERE home_work.hw_date = '$today'
                AND student.id = $student_id";
	        $res = direct_query($sql,'get','false');
            echo json_encode($res);
		    break;
		    
		case "payment_history" :
		    extract($_POST);
		    $sql ="select id,student_id,student_admission,paid_date,paid_month,previous_dues,discount,other_fee,total,paid_amount,current_dues,payment_mode from receipt where student_id ='$student_id'  order by paid_date desc";
		    $res = direct_query($sql,'get');
            echo json_encode($res);
		    break;
		    
		case "get_fee" :
		    extract($_POST);
		    $sql ="select id,student_name,student_class,student_section,student_mobile from student where id= '$student_id'";
		    $res = direct_query($sql,'get');
		    $res['data']['0']['till_date_dues']= finaldues($student_id);
            echo json_encode($res);
		    break;
		    
		case "pay_fee" :
		    extract($_POST);
		    $_POST['sttus'] = 'PENDING';
		    $res = insert_data('payment_request',$_POST);
		    echo json_encode($res);
		    break;
	    
		case "get_holiday" :
		    extract($_POST);
		    $sql ="select id,holiday_name,holiday_date from holiday where status ='ACTIVE'";
		    $res = direct_query($sql,'get');
            echo json_encode($res);
		    break;
		
		case "attendance" :
		    extract($_POST);
		    $res = get_all('student_att','*',['student_id'=>$student_id])['data'];
		    echo json_encode($res);
		    break;
		    
		case "get_result" :
		    extract($_POST);
		    $sql = "SELECT * FROM exam WHERE student_id='$student_id' AND exam_id='$exam_id'";
		    $res = direct_query($sql,'get');
            echo json_encode($res);
		    break;
		 
		case "admit_card" :
		    extract($_POST);
		    $sql = "SELECT 
                student.id AS sid,
                student.student_class AS student_class,
                admit_card.id AS aid,
                admit_card.student_class AS admit_card_class,
                admit_card.exam_date,
                admit_card.subject_id,
                admit_card.sitting_name,
                admit_card.start_time,
                admit_card.end_time,
                admit_card.exam_id,
                admit_card.status,
                exam_list.exam_name
            FROM student
            INNER JOIN admit_card 
                ON student.student_class = admit_card.student_class
            INNER JOIN exam_list
                ON admit_card.exam_id = exam_list.id
            WHERE admit_card.exam_id = '$exam_id'";
		    $res = direct_query($sql,'get');
            echo json_encode($res);
		    break;
		    
		case "get_notice" : 
		    $sql = "SELECT id,notice_date,notice_title,notice_details,notice_attachment FROM notice WHERE status='ACTIVE'";
		    $res = direct_query($sql,'get');
            echo json_encode($res);
		    break;
		    
		case "gallery" :
		    extract($_POST);
		    $sql = "SELECT id,image_title,image_url FROM gallery WHERE image_status='APP'";
		    $res = direct_query($sql,'get');
            echo json_encode($res);
		    break;
		    
		case "get_transport" :
		    extract($_POST);
		    $sql = "SELECT 
		        student.id AS sid,
		        student.student_name,
		        student.trip_id,
		        student.area_id,
		        transport_area.area_name,
		        trip_details.trip_name,
		        trip_details.arrival_time,
		        trip_details.departure_time,
		        trip_details.vehicle_no,
		        trip_details.driver_name
		    FROM student
	        INNER JOIN transport_area
		        ON student.area_id=transport_area.id
		    INNER JOIN trip_details
		        ON student.trip_id = trip_details.id
		    WHERE student.id = '$student_id' AND student_type = 'TRANSPORT'";
		    $res = direct_query($sql,'get');
            echo json_encode($res);
		    break;
		    
		case "complaint" :
		    extract($_POST);
		    $_POST['ref_no'] = 'CMP'.date('ymdhis');
		    $_POST['status'] = 'PENDING';
		    $res = insert_data('complaint',$_POST);
		    echo json_encode($res);
		    break;
		   
	    case "apply_leave" :
	        extract($_POST);
	        $_POST['status'] = 'PENDING';
	        $res = insert_data('complaint',$_POST);
	        echo json_encode($res);
	        break;
        
		case "student_profile" :
			// Recive student_id 
			extract($_POST);
			$sql = "SELECT id, student_name, student_photo, student_father, student_admission, student_class, student_roll, student_section, student_mobile from student where id = '$student_id' ";
			$res = direct_query($sql)['data']['0'];
			echo json_encode($res);
			break;
			
		case "get_receipt" :
		    extract($_POST);
		    $sql ="SELECT id as ID , paid_date as Date, total, paid_amount as paid , current_dues as dues FROM `receipt` WHERE status ='PAID' and student_id ='$student_id'";
		    $res =direct_query($sql);
		    unset($res['sql']);
		    echo json_encode($res);
		    break;
			
		case "get_notice":
	        $res =get_all("notice", "*",array('status'=>'ACTIVE')); 
			echo json_encode($res);
			break;
			
		case "get_dues":
		    extract($_POST);
		    $res['till_date_dues'] = finaldues($student_id);
			// $res =duesviaadm($adm_no,'december');
			echo json_encode($res);
			break;	
		
		case "dues_till_month":
		    extract($_POST);
		    $sid =get_data('student',$_POST['adm_no'],'id','student_admission');
		    $marr = duesmonthcount($sid)['list'];
			$marr = explode(',',$mlist);
			$res =nmonth_fee($sid,$marr);
			echo json_encode($res);
			break;
			
		case "forget_password":
			$user_name  = $_POST['user_name'];
			$sql = "select * from user where user_name ='$user_name' and status not in ('AUTO','DELETED')";
			$res = direct_query($sql);
			//print_r($res);
			if ($res['count'] > 0) {
				$id = $res['data'][0]['id'];
				$user_type = $res['data'][0]['user_type'];
				$email = $res['data'][0]['user_email'];
				$mobile = $res['data'][0]['user_mobile'];
				$name = $res['data'][0]['full_name'];

				$np = rnd_str(6);
				$up = array('password' => md5($np));
				$res = update_data($user_type, $up, $id, 'id');
				$sms = "Dear " . $name . " Your new password is " . $np . " kindly change after login " . $inst_name;
				rtf_mail($email, "Password Recover of $inst_name ", $sms, $noreply_email);
				//bulk_sms($mobile,$sms);
				$data['id'] = $id;
				$data['status'] = 'success';
				$data['msg'] = "New Password Successfully Send to $email";
			} else {
				$data['id'] = 0;
				$data['status'] = 'error';
				$data['msg'] = 'No any user exist with this ID. Try Again';
			}
			echo json_encode($data);
			break;
    				
			
		default :
				echo "<script> alert('Invalid Action'); window.location ='index.php'; </script>";	
				
		}

?>