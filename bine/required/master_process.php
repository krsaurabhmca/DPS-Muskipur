<?php
require_once('function.php');
$_POST = post_clean($_POST);
$_GET = post_clean($_GET);
if (isset($_GET['task'])) {
	$task = xss_clean($_GET['task']);
	$user_id = $_SESSION['user_id'];
	switch ($task) {
	case "change_session":
			//print_r($_POST);
			//$db_name = array_search( $_REQUEST['session_year'], $session_list );
    		$_SESSION['db_name'] = $_POST['session_year'];
			if($_SESSION['db_name']== ""){
			    echo "<script> window.location='".$_SERVER["HTTP_REFERER"]."' </script>";
			}else{
    			$db_name = $_POST['session_year'];
			    $res = update_data($db_name . '.user', array('token' => $token), $user_id);
			    echo "<script> window.location='../dashboard' </script>";
			}
			break;

		case "verify_login": // Delete Any Data From Table 
			extract($_POST);
			$res = get_all('user', '*', array('user_name' => $user_name, 'user_pass' => md5($user_pass),'user_status'=>'ACTIVE'));
			if ($res['count'] == 1) {
				if (!isset($_SESSION['initiated'])) {
					session_regenerate_id();
					$_SESSION['initiated'] = TRUE;
					$_SESSION['bine_token'] = $token  = md5(uniqid(rand(), TRUE));
					$_SESSION['token_time'] = time();
					$_SESSION['user_agent'] = 'bine_' . $_SERVER['HTTP_USER_AGENT'];
				}
				$_SESSION['user_id'] = $user_id = $res['data'][0]['id'];
				$_SESSION['user_name'] = $res['data'][0]['user_name'];
				$_SESSION['user_type'] = $user_type = $res['data'][0]['user_type'];
				update_data('user', array('token' => $token, 'status' => 'ACTIVE'), $user_id);
				if($user_type =='STAFF')
				{
				  $res['url'] = 'teacher_dashboard.php';  
				}
				else
				{
				$res['url'] = 'dashboard';
			    }
				//setcookie('bine_token', $token, time() + 600, '/', $inst_url);
				//void session_set_cookie_params ( int $lifetime [, string $path [, string $domain [, bool $secure = false [, bool $httponly = false ]]]] )
				//session_set_cookie_params( 60, '/' , $inst_name , true , true );
				session_regenerate_id();
			} else {
				$res['url'] = 'login';
			}
			$notice  = "$user_name logged in as $user_type";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "change_password": // Change Password of Logged in User
			$current_pass = md5($_POST['current_password']);
			$new_password = md5($_POST['new_password']);
			$where = array('id' => $user_id, 'user_pass' => $current_pass);
			$res = update_multi_data('user', array('user_pass' => $new_password), $where);
			$notice  = "$user_name changed their password";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "master_delete": // Delete Any Data From Table 
			extract($_POST);
			if ($_SESSION['user_type'] == 'ADMIN') {
				$searchdata  = get_data($table, $id);
				if ($searchdata['count'] > 0) {
					$res = delete_data($table, $id, $pkey);
				}
			} else {
				$res = array('msg' => "Don't  have permission", 'status' => 'error');
			}
			$notice  = "Record id $id is deleted from $table by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

// 		case "restore": // Delete Any Data From Table 
// 			extract($_POST);
// 			if ($_SESSION['user_type'] == 'ADMIN') {
// 				$rdata  = get_data('rbin', $id);
// 				if ($rdata['count'] > 0) {
// 					$arrdata = json_decode($rdata['data']);
// 					$idata  = insert_data($arrdata['table_name'], $arrdata['deleted_name']);
// 					if ($idata['id'] != 0) {
// 						$res = update_data('rbin', $id, array('status' => 'restored'));
// 					}
// 				}
// 			} else {
// 				$res = array('msg' => "Don't  have permission", 'status' => 'error');
// 			}
// 			echo json_encode($res);
// 			break;

		case "add_template": // SMS SMS any TIME
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = insert_data('sms_template', $_POST);
			echo json_encode($res);
			$notice  = "New SMS Template is added by {$_SESSION['user_name']}";
			add_notice($notice);
			break;

		case "send_sms" : // SMS any TIME
		    //$_POST['mobile'] = preg_replace('#\s+#',',',trim($mobile));
		   	//print_r($_POST);
		   	extract($_POST);
		   	$templateid = get_data('sms_template',$template_id, 'template_id')['data'];
		   	$ctype = get_data('sms_template',$template_id, 'content_type')['data'];
			$res = send_msg($mobile,$sms,$templateid,$ctype);
			//wa_text($mobile,$sms);
			$notice  = "SMS send by {$_SESSION['user_name']} on $mobile";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "group_sms" : // SMS SMS any TIME
				//print_r($_POST);
				$template_id = $_POST['template_id'];
				$templateid = get_data('sms_template',$template_id, 'template_id')['data'];
				$ctype = get_data('sms_template',$template_id, 'content_type')['data'];
				$msg_no = $_POST['msg_no'];
				$section = $_POST['section'];
				$sms = $_POST['msg_text'];
			
				if($section !='')
				{
					$mobile =get_all('student',array('student_mobile'),array('status'=>'ACTIVE','student_class'=>$msg_no,'student_section'=>$section))['data'];
				}
				else{
					if($msg_no =='ALL_STUDENT')
					{
						$mobile =get_all('student',array('student_mobile'),array('status'=>'ACTIVE'))['data'];
					}
					
					else if($msg_no =='ALL_STAFF')
					{
						$mobile =get_all('employee',array('e_mobile'),array('status'=>'ACTIVE'))['data'];
					}
					else{
						$mobile =get_all('student',array('student_mobile'), array('student_class'=>$msg_no))['data'];
					}
				}
				$mobile = implode(',',array_column($mobile,'student_mobile'));
				$notice  = "Group SMS Send by {$_SESSION['user_name']}";
			    add_notice($notice);
				$res = send_msg($mobile,$sms, $templateid,$ctype);
				echo json_encode($res);
				break;

		case "master_block": // BLOCK Any Data From Table 
			extract($_POST);
			//print_r($_POST);
			$bdata = array('status' => 'BLOCK');
			$res = update_data($table, $bdata, $id, $pkey);
			$notice  = "Record Id $id of $table is blocked by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

// 		case "block_user": // BLOCK Any Data From Table 
// 			extract($_POST);
// 			//print_r($_POST);
// 			$bdata = array('status' => $data_status);
// 			$res2 = update_data('center_details', $bdata, $id, 'center_code');
// 			$res = update_data('user', $bdata, $id, 'user_name');
// 			$res['msg']  = 'User and Center ' . $data_status . ' Successfully';
// 			$res['url'] = 'show_user';
			
// 			echo json_encode($res);
// 			break;

		case "update_status": // Update Status Data From Table 
			extract($_POST);
			$st = $_POST['data_status'];
			$sid = $_POST['sid'];
			$bdata = array('status' => $st);
			foreach ($sid as $id) {
				if ($st == 'DELETE') {
					$res = delete_data('student', $id, 'student_id');
				} else {
					$res = update_data('student', $bdata, $id, 'student_id');
					if ($st == 'VERIFIED') {
						$data = get_data('student', $id, null, 'student_id')['data'];
						$course = courseinfo($id, 'course_code');
						$sms = "Hello " . $data['student_name'] . " Your registration for course " . $course . " is accepted. Now you can check your admission status. \n Regards \n " . $inst_name . $inst_url;
						$default_sms($data['student_mobile'], $sms);
					}
				}
			}
			$notice  = "Status updated as $st of {$data['student_name']} by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;


		case "logout":
			$rtype = 'direct';
			extract($_POST);
			if ($_SESSION['bine_token'] != '') {
				$user_id = $_SESSION['user_id'];
				$user_type = $_SESSION['user_type'];
				$result = update_data('user', array('token' => '', 'status' => 'LOGOUT'), $user_id);
				echo json_encode($result);
				if ($result['status'] == 'success') {
					unset($_SESSION['user_name']);
					unset($_SESSION['user_type']);
					unset($_SESSION['user_id']);
					unset($_SESSION['bine_token']);
					session_destroy();
					$url = $base_url.'login';
				} else {
					$url = $base_url.'dashboard';
				}
			} else {
				$url = 'login';
			}
			if ($rtype == 'AJAX') {
				$result['url'] = $url;
				json_encode($result);
			} else {
				$result['url'] = 'login';
				echo "<script> window.location ='".$base_url."login.php' </script>";
				json_encode($result);
			}
			$notice  = "{$_SESSION['user_name']} logout";
			add_notice($notice);
			break;

		case "forget_password":
			$user_name  = $_POST['user_name'];
			$sql = "select * from user where user_name ='$user_name' and status not in ('AUTO','DELETED')";
			$res = direct_sql($sql);
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
			$notice  = "$name try to recover their password";
			add_notice($notice);
			break;

		case  "get_dist":
			$state_code = $_GET['state_code'];
			$res = get_all('district', '*', array('state_code' => $state_code), 'name');
			foreach ($res['data'] as $data) {
				$id = $data['id'];
				echo "<option value='" . $data['code'] . "'>" . $data['name'] . "</option>";
			}
			break;

		case "get_account":
		  //  print_r($_REQUEST);
			$account_type = $_REQUEST['account_type'];
			$res = get_all('account_head', '*', array('account_type' => $account_type));
		 //	print_r($res);
			foreach ($res['data'] as $data) {
				$id = $data['id'];
				echo "<option value='" . $data['id'] . "'>" . $data['account_name'] . "</option>";
			}
			break;


		case "upload":
			$result = upload_img('uploadimg', 'rand', 'upload');
			echo json_encode($result);
			break;
			/*============= STANDARD TASK END ===============*/

		case "update_student":
			//print_r($_POST);
			extract($_POST);
			$res = update_data('student', $_POST, $_POST['id']);
			//$res['url'] ='add_student?link='.encode('student_name='.$student_name.'&id='.$id);

			$stu_new  = array('student_id' => $_POST['id'], 'student_admission' => $_POST['student_admission']);
			
			$findstudent = get_data('student_fee', $id, null, 'student_id');
			//print_r($findstudent);
			if ($findstudent['count'] == 0) {
				if ($base_dues <> 0) {
					$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				}
				$res0 = insert_data('student_fee', $stu_new);
				//print_r($res0);
			} else {
				$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				//update_data('student_fee',$stu_new,$id,'student_id');
			}
			$res['url'] = 'manage_student';
			$notice  = "$student_name profile updated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;
			
		case "new_adm":
			//print_r($_POST);
			extract($_POST);
			$res = update_data('rmpsorg_2324.student', $_POST, $_POST['id']);
			//$res['url'] ='add_student?link='.encode('student_name='.$student_name.'&id='.$id);

			$stu_new  = array('student_id' => $_POST['id'], 'student_admission' => $_POST['student_admission']);
			
			$findstudent = get_data('rmpsorg_2324.student_fee', $id, null, 'student_id');
			//print_r($findstudent);
			if ($findstudent['count'] == 0) {
				if ($base_dues <> 0) {
					$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				}
				$res0 = insert_data('rmpsorg_2324.student_fee', $stu_new);
				//print_r($res0);
			} else {
				$stu_new['current_dues'] = sprintf("%.2f", $base_dues);
				//update_data('student_fee',$stu_new,$id,'student_id');
			}
			$res['url'] = 'manage_student';
			$notice  = "$student_name profile updated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;
		
		case "parent_update_student":
			extract($_POST);
			$res = update_data('student', $_POST, $_POST['id']);
			$res['msg'] ='Student Information Submitted successfully';
			$notice  = "$student_name profile updated by their parent";
			add_notice($notice);
			echo json_encode($res);
			break;


// 		case "update_fee_old":
// 			//print_r($_POST);
// 			extract($_POST);
// 			$col_name = remove_space($fee_name);
// 			if (isset($_POST['fee_month'])) {
// 				$_POST['fee_month'] = implode(',', $_POST['fee_month']);
// 			}
// 			if (isset($_POST['student_class'])) {
// 				$_POST['student_class'] = implode(',', $_POST['student_class']);
// 			}
// 			if (isset($_POST['finance_type'])) {
// 				$_POST['finance_type'] = implode(',', $_POST['finance_type']);
// 			}
// 			if (isset($_POST['admission_type'])) {
// 				$_POST['admission_type'] = implode(',', $_POST['admission_type']);
// 			}
// 			if (isset($_POST['student_type'])) {
// 				$_POST['student_type'] = implode(',', $_POST['student_type']);
// 			}
// 			$res = update_data('fee_head', $_POST, $id);
// 			add_column('receipt', $col_name, 'float(12,2)', ' DEFAULT 0.00');
// 			if ($fee_type == 'STUDENT') {
// 				add_column('student_fee', $col_name, 'float(12,2)', 'DEFAULT 0.00');
// 				if ($fee_type == 'FIXED') {
// 					direct_sql("update student_fee set $col_name ='$fee_amount'", 'set');
// 				}
// 			} else {
// 				add_column('fee_details', remove_space($fee_name), 'float(12,2)', 'DEFAULT 0.00');
// 				if ($fee_type == 'FIXED') {
// 					direct_sql("update fee_details set $col_name ='$fee_amount'", 'set');
// 				}
// 			}
// 			$res['url'] = 'add_fee';
// 			echo json_encode($res);
// 			break;

        case "update_fee":
			//print_r($_POST);
			extract($_POST);
			$_POST['col_name'] = $col_name = remove_space($fee_name);
			if (isset($_POST['fee_month'])) {
				$_POST['fee_month'] = implode(',', $_POST['fee_month']);
			}
			if (isset($_POST['student_class'])) {
				$_POST['student_class'] = implode(',', $_POST['student_class']);
			}
			if (isset($_POST['finance_type'])) {
				$_POST['finance_type'] = implode(',', $_POST['finance_type']);
			}
			if (isset($_POST['admission_type'])) {
				$_POST['admission_type'] = implode(',', $_POST['admission_type']);
			}
			if (isset($_POST['student_type'])) {
				$_POST['student_type'] = implode(',', $_POST['student_type']);
			}
			$res = update_data('fee_head', $_POST, $id);
			add_column('receipt', $col_name, 'float(12,2)', ' DEFAULT 0.00');
			add_column('fee_details', remove_space($fee_name), 'float(12,2)', 'DEFAULT 0.00');
			if ($fee_type == 'FIXED') {
				direct_sql("update fee_details set $col_name ='$fee_amount'", 'set');
			}
			if ($fee_type == 'STUDENT') {
				add_column('student', $col_name, 'float(12,2)', 'DEFAULT 0.00');
			} 
			$res['url'] = 'add_fee';
			echo json_encode($res);
			break;



// 		case "delete_fee_old":
// 			if ($_SESSION['user_type'] == 'ADMIN') {
// 				$id = $_POST['id'];
// 				$col_name = remove_space(get_data('fee_head', $id, 'fee_name')['data']);
// 				$fee_type = get_data('fee_head', $id, 'fee_type')['data'];
// 				if ($fee_type == 'STUDENT') {
// 					remove_column('student_fee', $col_name);
// 				} else {
// 					remove_column('fee_details', $col_name);
// 				}
// 				remove_column('receipt', $col_name);
// 				$res = delete_data('fee_head', $id);
// 			} else {
// 				$res = array('msg' => "Don't  have permission", 'status' => 'error');
// 			}
// 			echo json_encode($res);
// 			break;
			
		case "delete_fee":
			if ($_SESSION['user_type'] == 'ADMIN') {
				$id = $_POST['id'];
				//$col_name = remove_space(get_data('fee_head', $id, 'fee_name')['data']);
				$col_name = get_data('fee_head', $id, 'col_name')['data'];
				$fee_type = get_data('fee_head', $id, 'fee_type')['data'];
				remove_column('fee_details', $col_name);
				//remove_column('receipt', $col_name);
				if ($fee_type == 'STUDENT') {
					remove_column('student', $col_name);
				}
				$res = delete_data('fee_head', $id);
			} else {
				$res = array('msg' => "Don't  have permission", 'status' => 'error');
			}
			echo json_encode($res);
			break;

		case "set_fee_amount":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('fee_details', $_POST, $id);
			$res['url'] = 'update_fee';
			echo json_encode($res);
			break;

		case "get_month_fee":
			extract($_POST);
			$res = monthly_fee($student_id, $month_name);
			$res['url'] = 'update_fee';
			echo json_encode($res);
			break;

		case "nmonth_fee":
			extract($_POST);
			//print_r($_POST);
			$res = nmonth_fee($student_id, $month_list);
			echo json_encode($res);
			break;

		case "pay_fee_old":
			extract($_POST);
			$current_dues = $total - $paid_amount;
			$_POST['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
			$res = insert_data('receipt', $_POST);

			if ($res['status'] == 'success') {
				$rid = $res['id'];
				foreach (explode(',', $paid_month) as $month) {
					$old_value = get_data('student_fee', $student_id, $month)['data'];
					if ($old_value != null) {
						$rid = $old_value . "," . $rid;
					}
					$res2 = update_data('student_fee', array($month => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
				}
			}
			$link = encode("task=view&receipt_id=".$res['id']);
			$res['url'] = "rcpt.php?link=$link";
		//	$res['url'] = 'receipt.php?receipt_id=' . $res['id'];
			echo json_encode($res);
			break;
        
        	case "pay_fee" :
				extract($_POST);
				unset($_POST['send_sms']);
				$current_dues = $total-$paid_amount;
				$student =get_data('student', $student_id)['data'];				
				$student_name =$student['student_name'];				
				$student_mobile =$student['student_mobile'];				
				$_POST['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
				$res = insert_data('receipt',$_POST);
			
				if($res['status']=='success')
				{
					$rid = $res['id'];
					foreach(explode(',',$paid_month) as $month)
					{
						$old_value = get_data('student_fee',$student_id,$month,'student_id')['data'];
						if($old_value!=null and $month=='other_month')
						{
							$rid =$old_value.",".$rid;	
						}
						$res2 = update_data('student_fee',array($month=>$rid,'current_dues'=>$current_dues),$student_id,'student_id');
					}
				}
				$res['url'] ='receipt.php?receipt_id='.$res['id'];
				if($send_sms=='yes')
                {
				$sms = "Dear $student_name \nRs. $paid_amount received successfully against $paid_month Your current due is $current_dues. \nRegards $inst_name Via Bine";
                $te_id = '1507163688594295451';
                send_msg($student_mobile, $sms, $te_id);
                }
                $notice  = "Payment Recieved from $student_name with receipt No. $rid ";
			    add_notice($notice);
				echo json_encode($res);
				break;
			
			
			
        case "online_fee" :
				extract($_POST);
				unset($_POST['send_sms']);
				$current_dues = $total-$paid_amount;
				$student =get_data('student', $student_id)['data'];				
				$student_name =$student['student_name'];				
				$student_mobile =$student['student_mobile'];				
				$_POST['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
				$res = insert_data('receipt',$_POST);
			
				if($res['status']=='success')
				{
					$rid = $res['id'];
					foreach(explode(',',$paid_month) as $month)
					{
						$old_value = get_data('student_fee',$student_id,$month)['data'];
						if($old_value!=null)
						{
							$rid =$old_value.",".$rid;	
						}
						$res2 = update_data('student_fee',array($month=>$rid,'current_dues'=>$current_dues),$student_id,'student_id');
					}
				}
				$res['url'] ='receipt.php?receipt_id='.$res['id'];
				if($send_sms=='yes')
                {
				$sms = "Dear $student_name \nRs. $paid_amount received successfully against $paid_month Your current due is $current_dues. \nRegards $inst_name Via Bine";
                $te_id = '1507163688594295451';
                send_msg($student_mobile, $sms, $te_id);
                }
				echo json_encode($res);
				break;
        
		case "online_pay":
			extract($_POST);
			$current_dues = $total - $paid_amount;
			$_POST['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
			$res = insert_data('receipt', $_POST);

			if ($res['status'] == 'success') {
				$rid = $res['id'];
				foreach (explode(',', $paid_month) as $month) {
					$old_value = get_data('student_fee', $student_id, $month)['data'];
					if ($old_value != null) {
						$rid = $old_value . "," . $rid;
					}
					$res2 = update_data('student_fee', array($month => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
				}
			}
			
			$res['url'] = 'receipt.php?receipt_id=' . $res['id'];
			echo json_encode($res);
			break;

		case "cancel_receipt":
			extract($_POST);
			//print_r($_POST);
			$rid = $_POST['receipt_id'];
			$cancel_remarks = $_POST['cancel_remarks'];
			$cancel_at = date('Y-m-d h:i:s');
			$i = 1;
			$student_id = get_data('receipt', $rid, 'student_id')['data'];
			$paid_month = get_data('receipt', $rid, 'paid_month')['data'];
			$new_dues = get_data('receipt', $rid, 'previous_dues')['data'];
			$month_list = explode(",", $paid_month);
			foreach ($month_list as $colname) {
			    if($colname =='other_month')
			    {
			      $old_rid = get_data('student_fee', $student_id, 'other_month','student_id')['data']; 
			      $rid_arr = explode(",",$old_rid);
			      if (($key = array_search($rid, $rid_arr)) !== false) {
                        unset($rid_arr[$key]);
                    }
			      $colvalue =implode(",",$rid_arr);
			    }
			    else{
			      $colvalue =null;  
			    }
				$sql2 = "update student_fee set $colname = '$colvalue', current_dues ='$new_dues' where student_id ='$student_id'";
				$res = mysqli_query($con, $sql2) or die("Update Student Month Error : " . mysqli_error($con));
				$i = $i + 1;
			}
			if ($i > 1) {
				$cancel_data  = array('status' => 'CANCEL', 'cancel_by' => $user_id, 'cancel_at' => $cancel_at, 'cancel_remarks' => $cancel_remarks);
				$res2 = update_data('receipt', $cancel_data, $rid);
				if ($res2['status'] == 'success') {
					$res2['msg'] = "Receipt No. $rid Cancelled Successfully";
				}
				echo json_encode($res2);
			}
			$notice  = "Receipt No. $rid is cancelled by {$_SESSION['user_name']}";
			add_notice($notice);
			break;
        
        case "payment_mode" :
            extract($_POST);
            //print_r($_POST);
            $rid =$_POST['receipt_id'];
            $payment_mode =$_POST['payment_mode'];
               $payment_mode = ($payment_mode =='Cash')?'Bank':'Cash'; 
            $udata  = array('payment_mode'=>$payment_mode);
            //print_r($udata);
            $res2 = update_data('receipt',$udata,$rid);
            if($res2['status'] =='success'){
            $res2['msg'] = "Payment mode changed successfully";
            }
            echo json_encode($res2);
            break;
        
		case "cancel_acctxn":
			extract($_POST);
			//print_r($_POST);
			$txn_id = $_POST['txn_id'];
			$cancel_remarks = $_POST['cancel_remarks'];
			$cancel_at = date('Y-m-d h:i:s');

			$cancel_data  = array('status' => 'CANCEL', 'cancel_by' => $user_id, 'cancel_at' => $cancel_at, 'cancel_remarks' => $cancel_remarks);
			$res = update_data('account_txn', $cancel_data, $txn_id);
			if ($res['status'] == 'success') {
				$res['msg'] = "Txn No. $txn_id Cancelled Successfully";
			}
			echo json_encode($res);
			break;


		case "save_attendance_settings":
			extract($_POST);
			if (empty($latitude) || empty($longitude) || empty($radius)) {
				echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
				break;
			}
			$create_settings_table_sql = "CREATE TABLE IF NOT EXISTS attendance_settings (
					id INT AUTO_INCREMENT PRIMARY KEY,
					latitude VARCHAR(50) NOT NULL,
					longitude VARCHAR(50) NOT NULL,
					radius DOUBLE NOT NULL DEFAULT 0.0,
					created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
					updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				)";
			mysqli_query($con, $create_settings_table_sql);

			// Check if setting already exists
			$check = direct_sql("SELECT id FROM attendance_settings LIMIT 1");
			if ($check['count'] > 0) {
				$id = $check['data'][0]['id'];
				$res = update_data('attendance_settings', [
					'latitude' => $latitude,
					'longitude' => $longitude,
					'radius' => $radius
				], $id);
			} else {
				$res = insert_data('attendance_settings', [
					'latitude' => $latitude,
					'longitude' => $longitude,
					'radius' => $radius
				]);
			}
			if ($res['status'] == 'success' || $res['id'] > 0) {
				$res['status'] = 'success';
				$res['msg'] = 'GPS boundary settings saved successfully!';
			}
			$res['url'] = 'employee_attendance.php';
			echo json_encode($res);
			break;

		case "update_area":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('transport_area', $_POST, $id);
			$res['url'] = 'add_area';
			$notice  = "Transport Area Info updated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "update_trip":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('trip_details', $_POST, $id);
			$notice  = "Trip Info updated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "update_holiday":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('holiday', $_POST, $id);
			$notice  = "Holiday Info updated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "bulk_import":
			extract($_POST);
			echo "<pre>";
			$res = csv_import($table, $pkey);
			print_r($res);
			//echo "<script> window.location='bulk_import.php?res=".json_encode($res)."' </script>";
			break;

		case "bulk_export":
			if ($_SESSION['user_type'] == 'Admin') {
				$status = $_GET['status'];
				csv_export($_REQUEST['table']);
			}
			break;

		case "create_tc":
			extract($_POST);
			$_POST['tc_no'] = 'DPS/TC/'.$current_session."/".(100+$id);
			$res = update_data('tbl_tc', $_POST, $student_admission, 'student_admission');
			$link = encode('student_admission=' . $student_admission);
			//print_r($res);
			
			if ($res['status'] == 'success') {
				update_data('student', array('status' => 'BLOCK'), $_POST['student_admission'], 'student_admission');
				$res['url'] = 'print_tc?link=' . $link;
			}
			$notice  = "TC of Admission No. {$_POST['student_admission']} is generated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "update_user":
			$_POST['user_name'] = remove_space($_POST['user_name']);
// 			print_r($_POST);
			$_POST['status'] = $_POST['user_status'];
			$_POST['user_pass'] = md5($_POST['user_pass']);
			$res = update_data('user', $_POST, $_POST['id']);
			$res['url'] = 'add_user';
			$notice  = "User Info of {$_POST['user_name']} updated  by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "update_subject":
// 			print_r($_POST);
			extract($_POST);
			$sub = remove_space($subject_name);
			$_POST['subject_column'] = $sub;
			$_POST['status'] = 'ACTIVE';
			$_POST['student_class'] = implode(',', $_POST['student_class']);
			$res = update_data('subject', $_POST, $id);
			if ($res['status'] == 'success' and $subject_type == 'Scholastic') {
				add_column('exam', $sub . '_pt', 'float(6,2)', 'default 0.00');
				add_column('exam', $sub . '_nb', 'float(6,2)', 'default 0.00');
				add_column('exam', $sub . '_se', 'float(6,2)', 'default 0.00');
				add_column('exam', $sub . '_mo', 'float(6,2)', 'default 0.00');
			} else {
				add_column('co_scholastic', $sub, 'varchar(255)', '');
			}
			$res['url'] = 'subject_setting';
			$notice  = "Subject Info of $subject_name updated  by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

		case "assign_teacher_subject":
			extract($_POST);
			// Check if duplicate assignment exists
			$check = get_multi_data('teacher_assignment', array(
				'user_id' => $user_id,
				'student_class' => $student_class,
				'student_section' => $student_section,
				'subject_id' => $subject_id,
				'status' => 'ACTIVE'
			));
			if ($check['count'] > 0) {
				$res = array('status' => 'error', 'msg' => 'This assignment already exists!');
			} else {
				$adata = array(
					'user_id' => $user_id,
					'student_class' => $student_class,
					'student_section' => $student_section,
					'subject_id' => $subject_id,
					'status' => 'ACTIVE'
				);
				$res = insert_data('teacher_assignment', $adata);
				if ($res['status'] == 'success') {
					$res['msg'] = 'Subject assigned to teacher successfully!';
				}
			}
			$res['url'] = 'assign_teacher_subject.php';
			echo json_encode($res);
			break;

		case "delete_teacher_assignment":
			extract($_POST);
			if ($_SESSION['user_type'] == 'ADMIN') {
				$res = delete_data('teacher_assignment', $id);
				if ($res['status'] == 'success') {
					$res['msg'] = 'Assignment deleted successfully!';
				}
			} else {
				$res = array('status' => 'error', 'msg' => "Don't have permission");
			}
			echo json_encode($res);
			break;
			
		case "update_exam":
        	extract($_POST);
            $_POST['status'] = 'ACTIVE';
            $res = update_data('exam_setting', $_POST, $id);
            $res1['url'] = 'create_exam';
            echo json_encode($res);
        	break;

		case "delete_subject":
			if ($user_type == 'Admin') {
				extract($_POST);
				$sub_id = $_POST['id'];
				$sub = get_data('subject', $sub_id, 'subject_column')['data'];
				$sub_type = get_data('subject', $sub_id, 'subject_type')['data'];
				if ($sub_type == 'Scholastic') {
					remove_column('exam', $sub . '_pt');
					remove_column('exam', $sub . '_nb');
					remove_column('exam', $sub . '_se');
					remove_column('exam', $sub . '_mo');
				} else {
					remove_column('co_scholastic', $sub);
				}
				$res = delete_data('subject', $sub_id);
				echo json_encode($res);
			}
			$notice  = "Subject deleted  by {$_SESSION['user_name']}";
			add_notice($notice);
			break;

		case "select_subject":
			extract($_POST);
			$scls = xss_clean($class_name);
			$sql = "select * from subject where find_in_set('$scls',student_class)";
			$sub = direct_sql($sql);
			foreach ($sub['data'] as $sub) {
				echo "<option value='" . $sub['id'] . "'>" . $sub['subject_name'] . "</option>";
			}
			break;

		case "marks_entry":
			echo "<pre>";
			print_r($_POST);
			extract($_POST);
			$ct = count($_POST['student_id']);
			for ($i = 0; $i < $ct; $i++) {
				$student_id = $_POST['student_id'][$i];
				$student_admission = $_POST['student_admission'][$i];
				$exam_name = $_POST['exam_name'];
				$subject = remove_space($_POST['subject']);
				$se = $subject . "_se";
				$mo = $subject . "_mo";
				$nb = $subject . "_nb";
				$adata = array('student_admission' => $student_admission, 'student_id' => $student_id, 'exam_name' => $exam_name);
				insert_data('exam', $adata);

				$data = array($se => $_POST[$se][$i], $mo => $_POST[$mo][$i], $nb => $_POST[$nb][$i], 'student_id' => $_POST['student_id'][$i], 'status' => 'updated');
				//print_r($data);
				$where = array('student_admission' => $student_admission, 'exam_name' => $exam_name);
				$res = update_multi_data('exam', $data, $where);
			}
			$notice  = "Marks updated by {$_SESSION['user_name']}";
			add_notice($notice);
			break;

		case "marks_upload":
			extract($_POST);
			//print_r($_POST);
			if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
				$change = $new = 0;
				if (is_uploaded_file($_FILES['file']['tmp_name'])) {

					// Open uploaded CSV file with read-only mode
					echo $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
					echo $col_list = array_map('remove_space', fgetcsv($csvFile));
				}
			}
			$notice  = "Marks uploaded by {$_SESSION['user_name']}";
// 			add_notice($notice);
			$res = marksimport($table,$pkey,$remove);
			print_r($res);
			echo "<script> window.location='../marks_upload.php?res=".json_encode($res)."' </script>";
			break;


		case "update_enquiry":
		    extract($_POST);
		    print_r($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('enquiry', $_POST, $_POST['id']);
// 			$res['url'] = 'manage_enquiry';
// 			$sms = "Dear $student_name, \nYour Application for Admission in class $student_class submitted successfully with application no is $app_no \nRegards \n$inst_name Via Bine";
//             $te_id = '1507163688616454544';
//             send_msg($student_mobile, $sms,$te_id);
// $wa_sms ="Dear *$student_name*,
// Thanks for showing interest in admission in class *$student_class* of Our School. Have a Nice Day.
// $full_link

// Regards
// *$full_name*
// $inst_contact
// $inst_url
// ";

//  $wa_sms = urlencode($wa_sms);

//  $wa_link ="http://148.251.129.118/wapp/api/send?apikey=38c8df57e046494ea97daa6394802c6a&mobile=$student_whatsapp&msg=$wa_sms";

$st = api_call($wa_link);
            wa_text($student_mobile, $sms);
            $notice  = "Enquiry data updated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;
			
		case "update_contact_enquiry":
		    extract($_POST);
		    print_r($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = insert_data('contact_form', $_POST);
// 			$res['url'] = 'manage_enquiry';
// 			$sms = "Dear $student_name, \nYour Application for Admission in class $student_class submitted successfully with application no is $app_no \nRegards \n$inst_name Via Bine";
//             $te_id = '1507163688616454544';
//             send_msg($student_mobile, $sms,$te_id);
// $wa_sms ="Dear *$student_name*,
// Thanks for showing interest in admission in class *$student_class* of Our School. Have a Nice Day.
// $full_link

// Regards
// *$full_name*
// $inst_contact
// $inst_url
// ";

//  $wa_sms = urlencode($wa_sms);

//  $wa_link ="http://148.251.129.118/wapp/api/send?apikey=38c8df57e046494ea97daa6394802c6a&mobile=$student_whatsapp&msg=$wa_sms";

//$st = api_call($wa_link);
  //          wa_text($student_mobile, $sms);
    //        $notice  = "Enquiry data updated by {$_SESSION['user_name']}";
	//		add_notice($notice);
			echo json_encode($res);
			break;

		case "update_material":
			$_POST['status'] = 'ACTIVE';
			$res = update_data('study_material', $_POST, $_POST['id']);
			$res['url'] = 'add_study_material';
			echo json_encode($res);
			break;

		case "save_photo_old":
			$baseFromJavascript = $_POST['student_photo']; //your data in base64 'data:image/png....';
			$base_to_php = explode(',', $baseFromJavascript);
			$data = base64_decode($base_to_php[1]);
			$file_name = date('ymdhis') . "_" . rnd_str(5) . ".png";
			$filepath = "required/upload/no_image.jpg"; //.$file_name; // or image.jpg
			file_put_contents($filepath, $data);
			rename($filepath, 'required/upload/' . $file_name);
			$res['filepath'] = $filepath;
			$res['msg'] = "The file " . $file_name . " has been uploaded.";
			$res['id'] = $file_name;
			$res['status'] = 'success';
			echo json_encode($res);
			break;
    
    	case "save_photo" :
				$baseFromJavascript = $_POST['student_photo']; //your data in base64 'data:image/png....';
                $base_to_php = explode(',', $baseFromJavascript);
                $data = base64_decode($base_to_php[1]);
                $file_name = date('ymdhis')."_".rnd_str(5).".png";	
                $filepath = "upload/image.png "; //.$file_name; // or image.jpg
                file_put_contents($filepath,$data);
                rename($filepath, 'upload/'.$file_name);
                $res['msg'] = "The file ". $file_name. " has been uploaded.";
                $res['id'] = $file_name;
				$res['status'] ='success';
				echo json_encode($res);
				break;

			/*---------------LIBRARY MANAGMENT ----------------*/


		case "update_book_cat":
			$res = update_data('book_cat', $_POST, $_POST['id']);
			$res['url'] = 'book_cat';
			echo json_encode($res);
			break;

		case "update_book_pub":
			$res = update_data('book_pub', $_POST, $_POST['id']);
			$res['url'] = 'book_cat';
			echo json_encode($res);
			break;

		case "update_book":
			$res = update_data('book_list', $_POST, $_POST['id']);
			//$res['url'] ='book_list';
			echo json_encode($res);
			break;

		case "search_book":
			extract($_POST);
			$sql = "select accession_no, book_name, book_no from book_list where cat_id = '$cat_id' and book_name like '%$search_value%' or accession_no like '%$search_value%' or book_no like '%$search_value%'";
			$res = direct_sql($sql);
			echo json_encode($res);
			break;

		case "issue_book":
			extract($_POST);
			$_POST['issue_date'] = $today =  date('Y-m-d');
			$_POST['issue_by'] = $user_id;
			$_POST['status'] = 'ISSUED';
			$_POST['valid_till'] = date("Y-m-d", strtotime($today . " +$max_day_allow day"));
			$res = insert_data('book_txn', $_POST);
			update_data('book_list', array('status' => 'ISSUED'), $book_id);
			$res['url'] = 'issue_book.php';
			echo json_encode($res);
			break;

		case "return_book":
			extract($_POST);
			$_POST['status'] = 'RETURN';
			if ($book_fine !== '') {
				$_POST['fine_date'] = date('Y-m-d');
			}
			$res = update_data('book_txn', $_POST, $id);
			update_data('book_list', array('status' => 'AVAILABLE'), $book_id);
			$res['url'] = 'book_return.php';
			echo json_encode($res);
			break;

			/*---------------Account MANAGMENT ----------------*/


		case "update_account_head":
			$res = update_data('account_head', $_POST, $_POST['id']);
			$res['url'] = 'exp_head';
			echo json_encode($res);
			$notice  = "Account Head Added by {$_SESSION['user_name']}";
			add_notice($notice);
			break;

		case "exp_entry":
			$res = insert_data('account_txn', $_POST);
			$res['url'] = 'exp_head';
			$notice  = "Expense info Added by {$_SESSION['user_name']}";
			echo json_encode($res);
			break;

		case "exp_update":
			extract($_POST);
			$res = update_data('account_txn', $_POST, $id);
			$res['url'] = 'manage_account';
			echo json_encode($res);
			break;

		case "update_admit_card" :
		     extract($_POST);
		     $ct = count($subject_id);
		    for($i=0; $i<$ct; $i++)
		    {
		        
		        $find = get_all('admit_card', '*', array('student_class'=>$student_class, 'subject_id'=> $subject_id[$i]));
		        
		        $data['student_class'] = $student_class;
		        $data['subject_id'] = $subject_id[$i];
		        $data['exam_date'] = $exam_date[$i];
		        $data['start_time'] = $start_time[$i];
		        $data['end_time'] = $end_time[$i];
		        
		        if($find['count'] ==0)
		        {
		        $res =insert_data('admit_card',$data);
		        }
		        else{
		          $res =update_multi_data('admit_card',$data, array('student_class'=>$student_class, 'subject_id'=> $subject_id[$i]));  
		        }
		    }
		    $re_url = $base_url.'admit_card?student_class='.$student_class;
			echo "<script> window.location = '$re_url' </script>";	
			break;

		case "admin_txn":
			extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = update_data('admin_txn', $_POST, $_POST['id']);
			$res['url'] = 'admin_txn';
			echo json_encode($res);
			break;

		case "cancel_admin_txn":
			extract($_POST);
			//print_r($_POST);
			$txn_id = $_POST['txn_id'];
			$cancel_remarks = $_POST['cancel_remarks'];
			$cancel_at = date('Y-m-d h:i:s');

			$cancel_data  = array('status' => 'CANCEL', 'cancel_by' => $user_id, 'cancel_at' => $cancel_at, 'cancel_remarks' => $cancel_remarks);
			$res = update_data('admin_txn', $cancel_data, $txn_id);
			if ($res['status'] == 'success') {
				$res['msg'] = "Txn No. $txn_id Cancelled Successfully";
			}
			echo json_encode($res);
			break;

		case "update_holiday":
			$res = update_data('holiday', $_POST, $_POST['id']);
			$res['url'] = 'add_holiday';
			echo json_encode($res);
			break;

		case "last_roll":
			extract($_REQUEST);
			echo $res = last_roll($student_class, $student_section);
			//echo json_encode($res);
			break;

		case "sl":
			extract($_REQUEST);
			$sl = short_url($url);
			echo $sl;
			break;

			// ===============Vehicle Management ==================

		case "update_vehicle":
			$res = update_data('vehicle', $_POST, $_POST['id']);
			$res['url'] = 'add_vehicle';
			echo json_encode($res);
			break;

		case "assign_driver":
			extract($_POST);
			$res = update_data('vehicle', $_POST, $id);
			$res['url'] = 'add_vehicle';
			echo json_encode($res);
			break;

		case "change_driver":
			extract($_POST);
			$res = update_data('vehicle', $_POST, $id);
			$res['url'] = 'add_vehicle';
			echo json_encode($res);
			break;

		case "create_vehicle_type":
			extract($_POST);
			// $cat_name = strtoupper($_POST['cat_name']);
			$get_category = get_all("vehicle_cat", '*', array("cat_name" => $cat_name));
			if ($get_category['count'] > 0) {
				$res['msg'] = "Vehicle Type Already Exist,Please Create New Type";
				$res['status'] = "error";
			} else {
				$res = update_data('vehicle_cat', $_POST, $id);
				$res['url'] = $_SERVER['HTTP_REFERER'];
			}
			echo json_encode($res);
			break;
			// ===============Employee Management ==================
		case "save_emp_photo":
			$baseFromJavascript = $_POST['e_pic']; //your data in base64 'data:image/png....';
			$base_to_php = explode(',', $baseFromJavascript);
			$data = base64_decode($base_to_php[1]);
			$file_name = date('ymdhis') . "_" . rnd_str(5) . ".png";
			$filepath = "required/upload/"; // or image.jpg
			file_put_contents($filepath, $data);
			rename($filepath, 'required/upload/' . $file_name);
			$res['filepath'] = $filepath;
			$res['msg'] = "The file " . $file_name . " has been uploaded.";
			$res['id'] = $file_name;
			$res['status'] = 'success';
			echo json_encode($res);
			break;

        case "update_employee":
            $ename  = trim($_POST['e_name']);
            $mobile = trim($_POST['mobile']);
            $email  = trim($_POST['email']);
            $u_type  = trim($_POST['e_category']);
            $id     = $_POST['id'];
            $e_code = get_data('employee', $id, 'e_code')['data'];
            if ($e_code == "") {
                $_POST['e_code'] = strtoupper(substr($ename, 0, 3)) . rand(11111, 99999) . $id;
            }
            $res = update_data('employee', $_POST, $id);
            $username = strtolower(str_replace(' ', '', $ename)) . $id;
            $password = md5($mobile);
            $check_user = direct_sql("SELECT id FROM user WHERE created_by='$id'");
            $user_data = array('user_type'=>$u_type,'full_name'=>$ename,'user_mobile'=>$mobile,'user_name'=>$username,'user_pass'=>$password,'user_email'=> $email,'status'=> $_POST['status'],'user_status' => 'ACTIVE');
            if ($check_user['count'] == 0) {
                $new_user = insert_row('user');
                update_data('user', $user_data, $new_user['id']);
            } else {
                $user_id = $check_user['data'][0]['id'];
                update_data('user', $user_data, $user_id);
            }
            $res['url'] = 'add_employee';
            echo json_encode($res);
        break;
        
        case "delete_employee":
            $id = $_POST['id'];
            delete_data('employee', $id);
            direct_sql("DELETE FROM user WHERE created_by='$id'");
            echo json_encode(['status' => 'success','message' => 'Employee Deleted Successfully']);
        break;

		case "update_driver":
		    $e_code = get_data('employee', $_POST['id'], 'e_code')['data'];
			if ($e_code == "") {
				$_POST['e_code'] = strtoupper(substr($ename, 0, 3)) . rand('11111', '99999') . $_POST['id'];
			}
			$res = update_data('employee', $_POST, $_POST['id']);
			$res['url'] = 'add_driver';
			echo json_encode($res);
			break;

		case "add_category":
			extract($_POST);
			$cat_name_upper = $_POST['cat_name'];
			unset($_POST['cat_name']);
			$_POST['cat_name'] = strtoupper($cat_name_upper);
			$get_category = get_all("emp_cat", '*', array("cat_name" => $cat_name));
			if ($get_category['count'] > 0) {
				$res['msg'] = "Category Already Exist,Please Create New Category";
				$res['status'] = "error";
			} else {
				$res = update_data('emp_cat', $_POST, $_POST['id']);
				$res['url'] = $_SERVER['HTTP_REFERER'];
			}
			echo json_encode($res);
			break;
		case "select_class":
			extract($_POST);
			$select = "<option value=''>--Select Subject--</option>";
			$res = get_all("subject");
			if ($res['count'] > 0) {
				foreach ($res['data'] as $row) {
					$arr_list[$row['subject_column']] =  $row['student_class'];
				}
			}
			foreach ($arr_list as $key => $value) {
				$arr_value = explode(",", $value);
				if (in_array($str, $arr_value)) {
					$key_list[add_space($key)] = add_space(strtoupper($key));
					// $select .= "<option value='" . add_space($key) . "'>" . add_space(strtoupper($key)) . "</option>";
				}
			}
			$select .= dropdown($key_list, $subject);
			echo $select;
			break;

		case "select_lesson":
			extract($_POST);
			$select = "<option value=''>-Select Lesson-</option>";
			$res = get_all("lesson_plan", '*', array('subject' => $str));
			if ($res['count'] >  0) {
				foreach ($res['data'] as $row) {
					extract($row);
					$id_list[$id] = $lesson;
				}
				$select .= dropdown_with_key($id_list, $lesson_id);
			}
			echo $select;
			break;

		case "add_to_att":
			$stu_list = $_POST['sel_id'];
			$mvalue = remove_space(date('M_Y'));
			foreach ($stu_list as $stu_id) {
				$student_roll = get_data('student', $stu_id, 'student_roll', 'id')['data'];
				$post = array('att_month' => $mvalue, 'status' => 'ACTIVE', 'id' => $stu_id, 'student_roll' => $student_roll);
				//print_r($post);
				$res = insert_data('student_att', $post);
			}
			echo json_encode($res);
			break;

		case "make_att":
			//print_r($_POST);
			$stu_list = $_POST['sel_id'];
			$att_date = $_POST['att_date'];
			$abs_stu_list = $_POST['unsel_id'];
			$mvalue = date('M_Y', strtotime($att_date));
			$mvalue = remove_space($mvalue);
			$col_name = 'd_' . date('j', strtotime($att_date));

			$tbl_name = 'student_att'; //removespace(date('F_Y',strtotime($att_date)));

			foreach ($stu_list as $stu_id) {
				$post = array($col_name => 'P', 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array('student_id' => $stu_id, 'att_month' => $mvalue));
			}
			foreach ($abs_stu_list as $stu_id) {
				$post2 = array($col_name => 'A', 'status' => 'ACTIVE');
				$res2 = update_multi_data($tbl_name, $post2, array('student_id' => $stu_id, 'att_month' => $mvalue));
			}
			$res['status'] = 'success';
			break;

			//upload driver details
		case "uploadProfile":
			$result = upload_img('e_pic');
			echo json_encode($result);
			break;
		case "uploadAadhar":
			$result = upload_img('e_aadhar_profile');
			echo json_encode($result);
			break;
		case "uploadDL":
			$result = upload_img('e_dl_proof');
			echo json_encode($result);
			break;
			//Upload Homework
		case "uploadHomework":
			$result = upload_img("homework");
			echo json_encode($result);
			break;
			//Leave Form Details
		case "uploadLeaveApp":
		  //  print_r($_FILE);
			$result = upload_img('leave_app');
			echo json_encode($result);
			break;
		case "leave_details":
			extract($_POST);
			$f_d = $from_date;
			$t_d = $to_date;
			$res = update_data('leave_details', $_POST, $id);
			$e_id = get_data("leave_details", $id, 'emp_id')['data'];
			$res['url'] = "employee_attendance";
			echo json_encode($res);
			break;

			// Employee Attendance Management 
		case "make_emp_att":
			$emp_list = $_POST['sel_id'];
			$att_date = $_POST['att_date'];
			$abs_emp_list = $_POST['unsel_id'];
			$mvalue = date('M_Y', strtotime($att_date));
			$mvalue = remove_space($mvalue);
			$col_name = 'd_' . date('j', strtotime($att_date));

			$tbl_name = 'employee_att'; //removespace(date('F_Y',strtotime($att_date)));

			foreach ($emp_list as $emp_id) {
				$post = array($col_name => 'P', 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array('emp_id' => $emp_id, 'att_month' => $mvalue));
			}
			foreach ($abs_emp_list as $emp_id) {
				$post2 = array($col_name => 'A', 'status' => 'ACTIVE');
				$res2 = update_multi_data($tbl_name, $post2, array('emp_id' => $emp_id, 'att_month' => $mvalue));
			}
			$res['status'] = 'success';
			break;

		case "make_emp_abs":
			$emp_list = $_POST['sel_id'];
			$att_date = $_POST['att_date'];
			$present_emp_list = $_POST['unsel_id'];
			$mvalue = date('M_Y', strtotime($att_date));
			$mvalue = remove_space($mvalue);
			$col_name = 'd_' . date('j', strtotime($att_date));
			$tbl_name = 'employee_att'; //removespace(date('F_Y',strtotime($att_date)));

			foreach ($emp_list as $emp_id) {
				$post = array($col_name => 'A', 'status' => 'ACTIVE');
				$res = update_multi_data($tbl_name, $post, array('emp_id' => $emp_id, 'att_month' => $mvalue));
			}
			foreach ($present_emp_list as $emp_id) {
				$post2 = array($col_name => 'P', 'status' => 'ACTIVE');
				$res2 = update_multi_data($tbl_name, $post2, array('emp_id' => $emp_id, 'att_month' => $mvalue));
			}
			$res['status'] = 'success';
			break;
			//====================Inventory management==========================
		case "update_item":
			$res = update_data('inventory_item', $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'manage_item';
			}
			echo json_encode($res);
			break;
		case "update_item_cat":
			$res = update_data('item_cat', $_POST, $_POST['id']);
			if ($res['status'] == 'success') {
				$res['url'] = 'add_item';
			}
			echo json_encode($res);
			$notice  = "Account Head Added by {$_SESSION['user_name']}";
			break;

		case "update_vendor":
			$res = update_data('vendor', $_POST, $_POST['id']);
			if ($res['status'] == 'success') {
				$res['url'] = 'manage_vendor';
			}
			echo json_encode($res);
			break;

		case "update_vendor_cat":
			$res = update_data('vendor_cat', $_POST, $_POST['id']);
			if ($res['status'] == 'success') {
				$res['url'] = 'add_vendor';
			}
			echo json_encode($res);
			break;

		case "update_distribute_item":
			extract($_POST);
			$_SESSION['class'] = $student_class;
			$_SESSION['section'] = $student_section;
			$_SESSION['inv_no'] = $inv_no;
			$_SESSION['inv_id'] = $inv_id;
			$_SESSION['txn_date'] = $date;
			$_SESSION['student_id'] = $student_id;
			unset($_POST['student_class']);
			unset($_POST['student_section']);
			$_POST['status'] = "ACTIVE";
			$res = update_data('distribute_item', $_POST, $id);
			if ($res['status'] == 'success') {
				$res['url'] = $_SERVER['HTTP_REFERER'];
				$cur_stock = get_data('inventory_item', $item_id, 'current_stock')['data'];
				$rem_stock['current_stock'] = $cur_stock - $qty;
				update_data('inventory_item', $rem_stock, $item_id);
			}
			echo json_encode($res);
			break;

		case "close_invoice":
			// print_r($_POST);
			extract($_POST);
			// $total = $_POST['total'];
			if ($total <= 0) {
				$res['msg'] = "Total must be greater than 0";
				$res['status'] = "error";
			} else {
				$_POST['status'] = "CLOSED";
				$res = update_data("invoice", $_POST, $id);
				if ($res['status'] == "success") {
					unset($_SESSION['inv_id']);
					unset($_SESSION['class']);
					unset($_SESSION['section']);
					unset($_SESSION['inv_no']);
					unset($_SESSION['txn_date']);
					unset($_SESSION['student_id']);
					$res['msg'] = "Successfully Invoice is Closed";
					$res['url'] = "distribute_item";
				}
			}
			echo json_encode($res);
			break;
			
		case "cancel_invoice_receipt":
			$stu_id = $_POST['student_id'];
			$inv_id = $_POST['inv_id'];
			$post['status'] = 'CANCELLED';
			$res = update_multi_data('distribute_item', $post, array('student_id' => $stu_id, 'inv_id' => $inv_id));
			if ($res['status'] == 'error') {
				$_SESSION['msg'] = "Sorry! Something went wrong";
			} else {
				$_SESSION['msg'] = "Successfully! Invoice is cancelled";
				update_data('invoice', $post, $inv_id);
				$get_item =  get_all('distribute_item', '*', array('student_id' => $stu_id, 'inv_id' => $inv_id));
				if ($get_item['count'] > 0) {
					foreach ($get_item['data'] as $item) {
						$item_id = $item['item_id'];
						$cur_stock = get_data('inventory_item', $item_id, 'current_stock')['data'];
						$distribute_stock = $item['qty'];
						$update_stock['current_stock'] = $cur_stock  + $distribute_stock;
						update_data('inventory_item', $update_stock, $item_id);
					}
				}
				$res['url'] = "manage_distribute_item";
			}
			echo json_encode($res);
			break;

		case "get_student":
			extract($_POST);
			$res = get_all('student', '*', array('student_class' => $stu_class, 'student_section' => $stu_section));
			$stu_list = "<option value=''>--Select Student--</option>";
			if ($res['count'] > 0) {
				foreach ($res['data'] as $row) {
					$id = $row['id'];
					$stu_list .= "<option value='$id'>" . $row['student_name']." (".$row['student_admission'] . ") </option>";
				}
			}
			echo $stu_list;
			break;
		case "get_teacher":
			extract($_POST);
			$res = get_all('timetable', '*', array('subject' => $subject));
			$stu_list = "<option value=''>--Select Faculty--</option>";
			if ($res['count'] > 0) {
				foreach ($res['data'] as $row) {
					$id = $row['id'];
					$fac_id = $row['faculty_id'];
					$fac_name = get_data('employee', $fac_id, 'e_name')['data'];
					$faculty_list .= "<option value='$fac_id'>" . $fac_name . "</option>";
				}
			}
			echo $faculty_list;
			break;

		case "check_qty":
			extract($_POST);
			$q = get_data('inventory_item', $item_id, 'current_stock')['data'];
			if ($q > $qty) {
				$res['status'] = "success";
				$res['msg'] = "Available in stock";
			} else {
				$res['status'] = "error";
				$res['msg'] = "Quantity must be less than $q";
			}
			echo json_encode($res);
			break;
			//==================Lesson Plan========================
		case "update_lesson_plan":
			$res = update_data('lesson_plan', $_POST, $_POST['id']);
			if ($res['status'] == 'success') {
				$res['url'] = 'manage_lesson';
			}
			echo json_encode($res);
			break;

		case "update_question":
			$res = update_data("question_bank", $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'manage_question';
			}
			echo json_encode($res);
			break;

			//===================Salary API=========================
		case "update_salary":
		    unset($_POST['emp_name']);
			$res = update_data('salary', $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'manage_salary';
			}
			echo json_encode($res);
			break;

			//======================Homework Management===============
		case "update_homework":
			$res = update_data('homework', $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'manage_homework';
			}
			$notice  = "Homework updated by {$_SESSION['user_name']}";
			add_notice($notice);
			echo json_encode($res);
			break;

			//=====================TIMETABLE MANAGEMENT===============
		case "update_timeslot":
			$res = update_data('timeslot_table', $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'create_timeslot';
			}
			echo json_encode($res);
			break;

		case "update_timetable":
			$res = update_data('timetable', $_POST, $_POST['id']);
			if ($res['status'] == "success") {
				$res['url'] = 'create_timetable';
			}
			echo json_encode($res);
			break;
            
            //==========EMPLOYEE DATA FETCHED BY SALARY ===============//
        case "get_emp_data":
            extract($_POST);
            $res = get_all("employee","*",array('e_code'=>$e_code));
            if($res['count'] > 0){
                foreach($res['data'] as $row){
                    extract($row);
                    $res['id'] = $id;
                    $res['e_name'] = $e_name;
                    $res['e_salary'] = $e_salary;
                }
            }
            echo json_encode($res);
            break;
            
        case "get_emp_att":
            extract($_POST);
            // $month_in_number = date_parse($month);
            // $year = date("Y");
            $res['tp'] = get_emp_pre_attendance($id,$month);
            $res['ta'] = get_emp_abs_attendance($id,$month);
            $res['tl'] = get_emp_leave_attendance($id,$month);
            echo json_encode($res);
            break;
        
        case "update_role":
		       // print_r($_POST);
		        extract($_POST);
			    if($task =='add')
			    {
			        $res= add_role($table_name, $user_id, $role_name);
			        $res['msg'] ='Role Added Successfully';
			    }
			    else{
			        $res = remove_role($table_name, $user_id, $role_name);
			        $res['msg'] ='Role Removed Successfully';
			    }
			    $res['url'] ="add_role?user_id=$user_id";
				echo json_encode($res);
				break;
				
		case "update_notice_viewer":
            extract($_POST);
            $res = update_notice_viwer($notice_id);
            echo json_encode($res);
            break;
        
        case "update_adm_payment":
            extract($_POST);
            unset($_POST['student_name']);
            $_POST['pay_status'] ='PAID';
            $res = update_data('admission',$_POST, $_POST['id']);
            $student_whatsapp = get_data('admission',$id,'student_whatsapp')['data'];
            $link = encode('student_name=' . $student_name . '&id=' . $id);
            $full_link = $base_url.'form_receipt?link='.$link;
            
            $wa_sms ="Dear *$student_name*,
            Your Registration Payment Rs. 500 Received Successfully. Have a Nice Day.
            
            Click to View Receipt :
            $full_link
            
            Regards
            *$full_name*
            $inst_contact
            $inst_url
            ";
            
            $wa_sms = urlencode($wa_sms);
            
            $wa_link ="http://148.251.129.118/wapp/api/send?apikey=38c8df57e046494ea97daa6394802c6a&mobile=$student_whatsapp&msg=$wa_sms";
            
            $st = api_call($wa_link);
            $res['url'] = $full_link;
            echo json_encode($res);
            break;
           
        case "update_notice":
             extract($_POST);
             $res =update_data('notice',$_POST,$_POST['id']);
			 $res['url'] ='add_notice';
			 echo json_encode($res);
            
            break;
            
        case "update_gallery":
             
            //  print_r($_FILES);
             extract($_POST);
             $_POST['image'] = upload_img('image')['id'];
            //  print_r($_POST);
             $res =insert_data('gallery',$_POST);
			 $res['url'] ='add_gallery';
			 echo json_encode($res);
			 header("Location: https://dpsmushkipur.com/bine/add_gallery.php");
             exit();
            
            break;
            
		case "add_enquiry":
		    extract($_POST);
			$_POST['status'] = 'ACTIVE';
			$res = insert_data('enquiry', $_POST);
		    echo json_encode($res);
            break;
        		
		default:
			echo "<script> alert('Invalid Action'); window.location ='" . $_SERVER['HTTP_REFERER'] . "' </script>";
	}
}
