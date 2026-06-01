<?php
require_once('op_lib.php');
if (isset($_SESSION['db_name'])) {
	$db_name = $_SESSION['db_name'];
}
//Employee Attendance Strength
function get_emp_pre_attendance($id,$att_month)
{
	$count = 0;
	$m_split = strtolower(substr($att_month, 0, 3));
	$y =  date('Y');
	$month = $m_split . "_" . $y;
	$mvalue = remove_space($month);
	$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
	$get_emp = direct_sql($sql);
	if ($get_emp['count'] > 0) {
		foreach ($get_emp['data'] as $row) {
			for ($i = 1; $i <= 31; $i++) {
				$day = "d_" . $i;
				if ($row[$day] ==  "P") {
					$count++;
				}
			}
		}
	}
    return $count;
}
function get_emp_abs_attendance($id,$att_month)
{
	$count = 0;
	$m_split = strtolower(substr($att_month, 0, 3));
	$y =  date('Y');
	$month = $m_split . "_" . $y;
	$mvalue = remove_space($month);
	$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
	$get_emp = direct_sql($sql);
	if ($get_emp['count'] > 0) {
		foreach ($get_emp['data'] as $row) {
			for ($i = 1; $i <= 31; $i++) {
				$day = "d_" . $i;
				if ($row[$day] ==  "A") {
					$count++;
				}
			}
		}
	}
    return $count;
}
function get_emp_leave_attendance($id,$att_month)
{
	$count = 0;
	$m_split = strtolower(substr($att_month, 0, 3));
	$y =  date('Y');
	$month = $m_split . "_" . $y;
	$mvalue = remove_space($month);
	$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
	$get_emp = direct_sql($sql);
	if ($get_emp['count'] > 0) {
		foreach ($get_emp['data'] as $row) {
			for ($i = 1; $i <= 31; $i++) {
				$day = "d_" . $i;
				if ($row[$day] ==  "L") {
					$count++;
				}
			}
		}
	}
    return $count;
}

function get_present_emp($id)
{
	global $att_month_list;
	foreach ($att_month_list as $att_month) {
		$count = 0;
		$m_split = strtolower(substr($att_month, 0, 3));
		$y =  date('Y');
		$month = $m_split . "_" . $y;
		$mvalue = remove_space($month);
		$sql = "SELECT * from employee_att where emp_id = $id and att_month like '$mvalue' ";
		$get_emp = direct_sql($sql);
		if ($get_emp['count'] > 0) {
			foreach ($get_emp['data'] as $row) {
				for ($i = 1; $i <= 31; $i++) {
					$day = "d_" . $i;
					if ($row[$day] ==  "P") {
						$count++;
					}
				}
			}
		}
		echo "<td><b>" . $count . "</b></td>";
	}
}
//get present in a month overall
function get_total_present_in_month($table_name)
{
	global $att_month_list;
	foreach ($att_month_list as $month) {
		$count = 0;
		$m_split = strtolower(substr($month, 0, 3));
		$y =  date('Y');
		$d_m = $m_split . "_" . $y;
		$sql =  "SELECT * from $table_name where att_month like '$d_m' ";
		$student = direct_sql($sql);
		if ($student['count'] > 0) {
			foreach ($student['data'] as $stu_row) {
				for ($i = 1; $i <= 31; $i++) {
					$day = "d_" . $i;
					if ($stu_row[$day] ==  "P") {
						$count++;
					}
				}
			}
		}
		echo "<td><b>" . $count . "</b></td>";
	}
}

//get present in a month class and sectionwise
function get_present_in_month($class, $section)
{
	global $att_month_list;
	foreach ($att_month_list as $month) {
		$count = 0;
		$month_split = strtolower(substr($month, 0, 3));
		$year = date('Y');
		$att_month = $month_split . "_" . $year;
		$mvalue = remove_space($att_month);
		$get_stu = get_all('student', '*', array('student_class' => $class, 'student_section' => $section));
		if ($get_stu['count'] > 0) {
			foreach ($get_stu['data'] as $row) {
				$id = $row['id'];
				$sql = "SELECT * from student_att where student_id = $id and att_month like '$mvalue' ";
				$res = direct_sql($sql);
				if ($res['count'] >  0) {
					foreach ($res['data'] as $att_data) {
						for ($i = 1; $i < 31; $i++) {
							$day = "d_" . $i;
							if ($att_data[$day] == "P") {
								$count++;
							}
						}
					}
				}
			}
		}
		echo  "<td>" . $count . "</td>";
	}
}

function courseinfo($id, $code)
{
	$course_code = get_data('student', $id, 'course_code')['data'];
	$course = get_all('course', '*', array('course_code' => $code))['data'][0];
	return $course;
}
function studentcount($class, $section = null)
{
	if ($section == null) {
		$res = get_all('student', '*', array('status' => 'ACTIVE', 'student_class' => $class));
	} else {
		$res = get_all('student', '*', array('status' => 'ACTIVE', 'student_class' => $class, 'student_section' => $section));
	}
	return $res['count'];
}


function get_fee($student_id, $fee_id)
{
	global $con;
	$fee = 0.00;
	$student = get_data('student', $student_id, null)['data'];
	$fee = get_data('fee_head', $fee_id)['data'];
	$col_name = remove_space($fee['fee_name']);

	if ($col_name == 'transport_fee') {
		if ($student['student_type'] == 'TRANSPORT') {
			$fee = get_data('transport_area', $student['area_id'], 'area_fee')['data'];
		} else {
			$fee = 0;
		}
	} else if ($col_name == 'previous_dues') {
		$fee = 0;
	} 
	else if ($col_name == 'hostel_fee') {
		if ($student['student_type'] == 'HOSTELER') {
			$fee = get_data('fee_details', $student['student_class'], $col_name, 'student_class')['data'];
		} else {
			$fee = 0;
		}
	} 
	
	else if ($col_name == 'annual_fee' or $col_name =='exam_fee_lab_fee_lib_fee') {
	     if(get_data('student_fee',$student_id,'annual_fee','student_id')['data'] =='' )
	     {
	         $fee = get_data('fee_details', $student['student_class'], $col_name, 'student_class')['data'];
	     }
	     else{
	        $fee = 0; 
	     }
	} 
	
	else if ($fee['fee_type'] == 'STUDENT')
	{
	    if( get_data('student', $student_id, $col_name)['data'] =='YES') {
		//$fee = get_data('student_fee', $student_id, $col_name, 'student_id')['data'];
		$fee = get_data('fee_details', $student['student_class'], $col_name, 'student_class')['data'];
	    }
	    else{
	    $fee = 0;   
	    }
	} 
	else {
		$fee = get_data('fee_details', $student['student_class'], $col_name, 'student_class')['data'];
	}
	return $fee;
}


function get_fee_by_name($student_id, $col_name)
{
	global $con;
	$fee = 0.00;
	$student = get_data('student', $student_id, null)['data'];
	//	$fee = get_data('fee_head',$fee_id)['data'];
	//	$col_name = remove_space($fee['fee_name']);

	if ($col_name == 'transport_fee') {
		if ($student['student_type'] == 'TRANSPORT') {
			$fee = get_data('transport_area', $student['area_id'], 'area_fee')['data'];
		} else {
			$fee = 0;
		}
	} else if ($col_name == 'previous_dues') {
		$fee = 0;
	} else if ($col_name == 'hostel_fee') {
		if ($student['student_type'] == 'HOSTELER') {
			$fee = get_data('fee_details', $student['student_class'], $col_name, 'student_class')['data'];
		} else {
			$fee = 0;
		}
	} else if ($fee['fee_type'] == 'STUDENT') {
		$fee = get_data('student_fee', $student_id, $col_name, 'student_id')['data'];
	} else {
		$fee = get_data('fee_details', $student['student_class'], $col_name, 'student_class')['data'];
	}
	return $fee;
}

function monthly_fee($student_id, $month_name)
{
	global $con;
	//$fee['total']=0;
	$student = get_data('student', $student_id, null)['data'];
	//print_r($student);
	extract($student);
	$sql = "select * from fee_head where find_in_set ('$finance_type', finance_type) and find_in_set( '$admission_type',admission_type) and find_in_set('$month_name', fee_month) and find_in_set('$student_class', student_class) and find_in_set('$student_type', student_type) and status='ACTIVE'";
	//$sql ="select * from fee_head where finance_type ='NORMAL' and find_in_set( '$admission_type',admission_type) and find_in_set('$month_name', fee_month) and find_in_set('$student_type', student_type) and status='ACTIVE'";
	$res = direct_sql($sql);

	if ($res['count'] > 0) {
		$total = 0;
		foreach ($res['data'] as $row) {
			$fee['id'] = $row['id'];
			$fee['name'] = $row['fee_name'];
			$fee['col_name'] = $col_name = remove_space($row['fee_name']);
			$fee['amount'] = $fee_amount = get_fee($student_id, $row['id']);
			$total = $total + $fee['amount'];
			$all_fee['fee'][$col_name] = $fee_amount;
		}
		$all_fee['fee']['total'] = $total;
		$all_fee['student_id'] = $student_id;
		$all_fee['admission'] = $student['student_admission'];
		$all_fee['month'] = $month_name;
	}
	return $all_fee;
}


function create_check($name, $value = null, $class = 'fee-month', $checked = null)
{
	$id = remove_space($name);
	$check = "<div class='checkbox'> <input type='checkbox' name ='$id' id='$id' value='$value' class='$class' $checked > <label for='$id'> $name </label></div>";
	echo $check;
}


// function array_add($all)
// {
// 	$total = array();
// 	$key_list = array();
// 	foreach ($all as $tmp) {
// 		$key_list = array_merge($key_list, array_keys($tmp));
// 	}

// 	$col_list = array_unique($key_list);

// 	foreach ($col_list as $col_name) {
// 		$total[$col_name] = array_sum(array_column($all, $col_name));
// 	}
// 	return $total;
// }

function array_add($all)
{
    if (!is_array($all) || empty($all)) return [];

    $total = [];

    foreach ($all as $row) {
        foreach ($row as $key => $val) {
            $total[$key] = ($total[$key] ?? 0) + (is_numeric($val) ? $val : 0);
        }
    }

    return $total;
}


function subject_list($class_name)
{
	$scls = xss_clean($class_name);
	//$list =array();
	$sql = "select * from subject where find_in_set('$scls',student_class) and  status ='ACTIVE' and is_extra ='NO' ";
	$sub = direct_sql($sql);
	foreach ($sub['data'] as $sub) {
		$list[] = $sub['id'];
	}
	return $list;
}


function extra_list($class_name)
{
	$scls = xss_clean($class_name);
	//$list =array();
	$sql = "select * from subject where find_in_set('$scls',student_class) and status ='ACTIVE' and is_extra ='YES' ";
	$sub = direct_sql($sql);
	foreach ($sub['data'] as $sub) {
		$list[] = $sub['id'];
	}
	return $list;
}

function nmonth_fee($sid, $marr)
{
	//$marr = explode(',',$mlist);
	$all = array();
	foreach ($marr as $month) {
		if (remove_space($month) <> 'previous_dues') {
			if (get_data('student_fee', $sid, remove_space($month), 'student_id')['data'] == null) {
				$all[$month] = monthly_fee($sid, $month)['fee'];
			}
		}
	}
	//return $all;
	return array_add($all);
}


function nmonth_fee_all($sid, $marr)
{
	$all = array();
	foreach ($marr as $month) {
		if (remove_space($month) <> 'previous_dues') {
				$all[$month] = monthly_fee($sid, $month)['fee'];
		}
	}
	return array_add($all);
}

function get_marks($student_admission, $exam_name, $subject)
{
	global $con;
	$sql = "select * from exam where student_admission  ='$student_admission' and exam_name like '$exam_name' ";
	$res = direct_sql($sql);
	if ($res['count'] == 1) {
		$row = $res['data'][0];
		$data['pt']	 = $row[$subject . '_pt'];
		$data['mo']	 = $row[$subject . '_mo'];
		$data['se']	 = $row[$subject . '_se'];
		$data['nb']	 = $row[$subject . '_nb'];
		$data['total'] = $data['nb'] + $data['se'] + $data['mo'] + $data['pt'];
	} else {
		$data['pt']	 = 0;
		$data['mo']	 = 0;
		$data['se']	 = 0;
		$data['nb']	 = 0;
		$data['total'] = 0;
	}
	return $data;
}


function get_unit_marks($student_admission, $unit_test_id, $subject)
{
	global $con;
	$sql ="select * from unit_exam where student_admission  ='$student_admission' and unit_test_id = '$unit_test_id' ";
	$res = direct_sql($sql);
	return $res['data'][0][$subject.'_mo'];
}

function marksimport($table, $pkey = 'id', $remove = null) // Import CSV FILE to Table
{
	// Allowed mime types
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

	// Validate whether selected file is a CSV file
	$change = $new = 0;
	if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
		if (is_uploaded_file($_FILES['file']['tmp_name'])) {

			// Open uploaded CSV file with read-only mode
			echo $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
			echo $col_list = array_map('remove_space', fgetcsv($csvFile));

			while (($line = fgetcsv($csvFile)) !== FALSE) {
				$all_data = array_combine($col_list, $line);

				$search[$pkey] = trim($all_data[$pkey]);
				$search_result = get_all($table, '*', $search, $pkey);
				foreach (explode(',', $remove) as $rcol) {
					unset($all_data[$rcol]);
				}

				echo $table;
				echo $all_data[$pkey];
				$search_result = get_data($table, $all_data[$pkey], null, $pkey);
				echo "<pre>";
				//	print_r($search_result);
				if ($search_result['count'] < 1) {
					$res = insert_data($table, $all_data);
					if ($res['id'] != 0) {
						$new++;
					}
				} else {
					//echo $all_data[$pkey];
					$res = update_data($table, $all_data, $all_data[$pkey], $pkey);
					if ($res['status'] == 'success') {
						$change++;
					}
				}
				print_r($res);
				$res = array('status' => 'success', 'change' => $change, 'new' => $new, 'msg' => 'Data has been imported successfully.');
			}
		}
	} else {
		$res = array('status' => 'error', 'change' => $change, 'new' => $new, 'msg' => 'Please upload a valid CSV file.');
	}
	return  $res;
}

function grade($per)
{
	if ($per <= 100 && $per >= 91) {
		$grade = 'A1';
	} elseif ($per < 91 && $per >= 81) {
		$grade = 'A2';
	} elseif ($per < 81 && $per >= 71) {
		$grade = 'B1';
	} elseif ($per < 71 && $per >= 61) {
		$grade = 'B2';
	} elseif ($per < 61 && $per >= 51) {
		$grade = 'C1';
	} elseif ($per < 51 && $per >= 41) {
		$grade = 'C2';
	} elseif ($per < 41 && $per >= 33) {
		$grade = 'D';
	} else {
		$grade = 'E';
	}
	//A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32)
	return $grade;
}

function duesmonthcount($student_id)
{
	$ct = 0;
	$cmonth = date('F');
// 	$cmonth = 'March';
	$prev_dues = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
	if ($prev_dues <> 0) {
		$list[] = 'Previous Dues';
	}

	$student = get_data('student', $student_id)['data'];
	global $fee_month; // Month Allowed to Collect Fee
	foreach ($fee_month as $month) {
		$x = get_data('student_fee', $student_id, remove_space($month), 'student_id')['data'];
		if ($x == '' || $x == null) {
			$ct++;
			$list[] = $month;
			//$list =$list.", ".$month;
		}
		if ($month == $cmonth) {
			break;
		}
	}
	$res['count'] = $ct;
	$res['list'] = $list;
	return $res;
}


function finaldues($student_id)
{
	$total = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
	$marr = duesmonthcount($student_id)['list'];

	foreach ($marr as $mnth) {
		$res = monthly_fee($student_id, remove_space($mnth));
		//print_r($res);
		$total = $total + $res['fee']['total'];
	}

	return $total;
}

//echo finaldues(20);

function all_income($from_date = '', $to_date = '', $mode = '')
{
	global $today;
	if ($from_date == '') {
		$from_date = $today;
	}
	if ($to_date == '') {
		$to_date = $today;
	}
	if ($mode <> '') {
		$mode = " and payment_mode ='$mode'";
	}
	$res = get_all('fee_head', '*', array('status' => 'ACTIVE'))['data'];
	$gt = 0;
	//print_r($res);
	$res[]['fee_name'] = 'previous_dues';
	$res[]['fee_name'] = 'paid_amount';
	$res[]['fee_name'] = 'current_dues';
	foreach ($res as $row) {
		$fee_name = remove_space($row['fee_name']);

		$sql = "select sum($fee_name) as total from receipt  where status ='PAID' and paid_date between '$from_date' and '$to_date' " . $mode;
		$gt = $gt + direct_sql($sql)['data'][0]['total'];
		$total[$fee_name] = direct_sql($sql)['data'][0]['total'];
	}
	$a2['total'] = $total['paid_amount']; // Last Position
	$diff = $gt - $total['paid_amount'];
	$a1['previous_dues'] = $total['paid_amount'] - $diff; //Calculate Unmateched amount
	unset($total['paid_amount']); // To Remove from All Fee List
	$total = $a1 + $total + $a2;
	return $total;
}


function collection_of_month($month_name)
{
	global $today;

	$res = get_all('fee_head', '*', array('status' => 'ACTIVE'))['data'];
	$gt = 0;
	//print_r($res);
	$res[]['fee_name'] = 'previous_dues';
	$res[]['fee_name'] = 'paid_amount';
	$res[]['fee_name'] = 'current_dues';
	foreach ($res as $row) {
		$fee_name = remove_space($row['fee_name']);
		$sql = "select sum($fee_name) as total from receipt  where status ='PAID' and monthname(paid_date) ='$month_name' ";
		$gt = $gt + direct_sql($sql)['data'][0]['total'];
		$total[$fee_name] = direct_sql($sql)['data'][0]['total'];
	}
// 	$a2['total'] = $total['paid_amount']; // Last Position
// 	unset($total['paid_amount']); // To Remove from All Fee List
// 	$total = $a1 + $total + $a2;
	return $total;
}


function all_exp($from_date = '', $to_date = '', $mode = '')
{
	global $today;
	if ($from_date == '') {
		$from_date = $today;
	}
	if ($to_date == '') {
		$to_date = $today;
	}
	if ($mode <> '') {
		$mode = " and txn_mode ='$mode'";
	}
	//$fee_name = remove_space($row['fee_name']);
	$sql = "SELECT account_id, sum(txn_amount) as total FROM `account_txn` where status = 'ACTIVE' and txn_date BETWEEN '$from_date' and '$to_date' $mode group by account_id ";

	//$sql = "select account_type, sum(txn_amount) as total from exp_details where status = 'ACTIVE' and txn_date BETWEEN '$from_date' and '$to_date' $mode GROUP by account_type";

	$res = direct_sql($sql);
	$gt = 0;
	foreach ($res['data'] as $row) {
		$acc_type = $row['account_type'];
		$gt = $gt + $row['total'];
		$total[$acc_type] = $row['total'];
	}
	$total['total'] = $gt;
	return $total;
}

function admin_income($from_date = '', $to_date = '', $mode = '')
{
	global $today;
	if ($from_date == '') {
		$from_date = $today;
	}
	if ($to_date == '') {
		$to_date = $today;
	}
	if ($mode <> '') {
		$mode = " and payment_mode ='$mode'";
	}
	$res = get_all('fee_head', '*', array('status' => 'ACTIVE'))['data'];
	$gt = 0;
	//print_r($res);
	$res[]['fee_name'] = 'paid_amount';
	foreach ($res as $row) {
		$fee_name = remove_space($row['fee_name']);

		$sql = "select sum($fee_name) as total from receipt  where status ='PAID' and paid_date between '$from_date' and '$to_date' " . $mode;
		$gt = $gt + direct_sql($sql)['data'][0]['total'];
		$total[$fee_name] = direct_sql($sql)['data'][0]['total'];
	}
	$a2['total'] = $total['paid_amount']; // Last Position
	$diff = $gt - $total['paid_amount'];
	$a1['previous_dues'] = $total['paid_amount'] - $diff; //Calculate Unmateched amount
	unset($total['paid_amount']); // To Remove from All Fee List
	$total = $a1 + $total + $a2;
	return $total;
}


function admin_exp($from_date = '', $to_date = '', $mode = '')
{
	global $today;
	if ($from_date == '') {
		$from_date = $today;
	}
	if ($to_date == '') {
		$to_date = $today;
	}
	if ($mode <> '') {
		$mode = " and txn_mode ='$mode'";
	}
	//$fee_name = remove_space($row['fee_name']);
	//$sql = "SELECT account_id, sum(txn_amount) as total FROM `account_txn` where status = 'ACTIVE' and txn_date BETWEEN '$from_date' and '$to_date' $mode group by account_id ";

	$sql = "select account_type, sum(txn_amount) as total from exp_details where status = 'ACTIVE' and txn_date BETWEEN '$from_date' and '$to_date' $mode GROUP by account_type";

	$res = direct_sql($sql);
	$gt = 0;
	foreach ($res['data'] as $row) {
		$acc_type = $row['account_type'];
		$gt = $gt + $row['total'];
		$total[$acc_type] = $row['total'];
	}
	$total['total'] = $gt;
	return $total;
}

function last_roll($student_class, $student_section)
{
	$roll_no = 1;
	$sql = "select max(student_roll) as student_roll from  student where student_class ='$student_class' and student_section ='$student_section' ";
	$res = direct_sql($sql);
	if ($res['count'] == 1) {
		$roll_no =  $res['data'][0]['student_roll'];
	}
	return $roll_no + 1;
}

function monthly_collection_graph()
{
	$cmonth = date('m');
	$sql = "SELECT student_class, count(id) as ct FROM `payment_book` where month(paid_date) ='$cmonth' group by student_class ";
	$result = direct_sql($sql);
	$rows = array();
	$table[] = array("Class", "Paid", "Total","Attendance");

	foreach ($result['data'] as $row) {
		$data = array();
		$data[] = $row['student_class'];
		$data[] = intval($row['ct']);
		$data[] = get_all('student', '*', array('student_class' => $row['student_class']))['count'];
		$data[] =monthly_avg_att($row['student_class']);
        
		$table[] = $data;
	}
	$jsonTable = json_encode($table);
	return $jsonTable;
}

function new_admission_graph()
{
	$cmonth = date('m');
	$cyear = date('Y');
	$sql = "SELECT student_class, count(id) as ct FROM student where month(created_at) ='$cmonth' and year(created_at) ='$cyear' and student_class <>'' and admission_type='NEW' group by month(created_at), student_class";
	$result = direct_sql($sql);
	$rows = array();
	$table[] = array("Class", "Admission");

	foreach ($result['data'] as $row) {
		$data = array();
		$data[] = $row['student_class'];
		$data[] = intval($row['ct']);
		$table[] = $data;
	}
	$jsonTable = json_encode($table);
	return $jsonTable;
}

function income_exp_graph()
{
	$td = date('d');

	$rows = array();
	$table[] = array("Date", "Income", "Expence", "Balance");

	for ($i = 1; $i <= $td; $i++) {
		$dt = date('Y-m-' . $i);
		$data = array();
		$data[] = date($i . ' M');
		$data[] = intval(all_income($dt, $dt)['total']);
		$data[] = intval(all_exp($dt, $dt)['total']);
		$data[] = intval(all_income($dt, $dt)['total']) - intval(all_exp($dt, $dt)['total']);
		$table[] = $data;
	}
	$jsonTable = json_encode($table);
	return $jsonTable;
}

function short_url($long_url, $keyword = '')
{
	if ($keyword != '') {
		$keyword = "&keyword=" . remove_space($keyword);
	}
	$timestamp = time();
	$signature = hash('sha512', $timestamp . 'lskfhz7yajzbtwpsh6j9cdnuhaadklwk');

	//$api_url ="https://sl.morg.in/yourls-api.php?timestamp=$timestamp&signature=$signature&hash=sha512&action=shorturl&format=json&url=". $long_url.$keyword;
	$api_url = "https://sl.morg.in/yourls-api.php?username=offerplant&password=Plant!2017&action=shorturl&format=json&url=" . $long_url . $keyword;

	$res = api_call($api_url);

	$data = json_decode($res, true);
	return $data['shorturl'];
	//return $data;
}

function mask($text, $cr = 2)
{
	return substr($text, 0, $cr) . '****' . substr($text, -$cr);
}


function update_payment($student_id, $amount) // for online payment 
{
	$student = get_data('student', $student_id)['data'];
	$rdata['student_admission'] = $student['student_admission'];
	$rdata['student_id'] = $student['id'];
	$rdata['previous_dues'] = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
	$dues_month = duesmonthcount($student_id);
	$paid_month = duesmonthcount($student_id)['list'];
	$all_fee = nmonth_fee($student_id, $dues_month['list']);
	foreach ($all_fee as $key => $value) {
		$rdata[$key] = intval($value);
	}
	$total_dues = finaldues($student_id);
	$current_dues = $total_dues - $amount;
	$rdata['paid_month'] = implode(',', $dues_month['list']);
	$rdata['paid_amount'] = $amount;
	$rdata['current_dues'] = $current_dues = sprintf("%.2f", $current_dues);
	$rdata['paid_date'] = date('Y-m-d');
	$rdata['payment_mode'] = 'ONLINE';
	$rdata['status'] = 'PAID';

	$res = insert_data('receipt', $rdata);

	print_r($rdata);
	if ($res['status'] == 'success') {
		$rid = $res['id'];
		foreach ($paid_month as $month) {
			if (remove_space($month) <> 'previous_dues') {
				$old_value = get_data('student_fee', $student_id, $month)['data'];
				if ($old_value != null) {
					$rid = $old_value . "," . $rid;
				}
				$res2 = update_data('student_fee', array($month => $rid, 'current_dues' => $current_dues), $student_id, 'student_id');
				print_r($res2);
			}
		}
	}
	update_data('student_fee', array('current_dues' => $current_dues), $student_id, 'student_id');
	return $res['id'];
}


//========Attendance Summary ===============//

function present_count($att_date, $student_class, $student_section ='')
{
   //  echo "<pre>";
    if($student_section !='')
    {
        $student_section = " and student_section= '".$student_section."'";
    }
	$att_month =remove_space(date('M_Y',strtotime($att_date)));
	$d =remove_space(date('j',strtotime($att_date)));
	$sql ="SELECT count(student_att.id) as total FROM student_att, student WHERE student_att.student_id =student.id and d_$d ='P' and  att_month='$att_month' and student.student_class='$student_class'".$student_section;
	$res = direct_sql($sql);
	
	return $res['data'][0]['total'];
}

//======== Notification =============//

function add_notice($msg)
    {
        global $user_id;
        $res = insert_data('notification', array('message'=>$msg));
        return $res;
    }

function update_notice_viwer($notice_id)
    {
        global $user_id;
        $viewer  = get_data('notification',$notice_id,'viewer')['data'];
        
        if($viewer ==null)
        {
            update_data('notification', array('viewer'=>$user_id), $notice_id);
        }
        else{
           
            if(is_notice_viwed($notice_id)=='NO')
            {
            $viewer_arr = explode(",",$viewer);
            array_push($viewer_arr, $user_id);
            $viewer_arr = array_unique($viewer_arr);
            $viewers = implode(",", $viewer_arr);
            update_data('notification', array('viewer'=>$viewers), $notice_id);
            }
        }
    }
    
function is_notice_viwed($notice_id)
    {
        global $user_id;
        $viewer  = get_data('notification',$notice_id,'viewer')['data'];
        
            $viewer_arr = explode(",",$viewer);
            if (array_search($user_id,$viewer_arr)=='')
            {
                $res ='NO';
            }
            else{
                $res ='YES';
            }
        return $res;
    }
    
function all_notice()
    {
        global $user_id;
        $anc =get_all('notification')['count'];
        $sql ="SELECT * FROM notification where not find_in_set('$user_id',viewer)  or viewer is NULL order by id desc limit 50";
        return $res =direct_sql($sql);
    }
   
function mark_notice_as_viewed()
    {
        global $user_id;
        $res = get_all('notification');
        foreach($res['data'] as $notice)
        {
        $notice_id = $notice['id'];
        $viewer  = get_data('notification',$notice_id,'viewer')['data'];
        
            if($viewer ==null)
            {
                update_data('notification', array('viewer'=>$user_id), $notice_id);
            }
            else{
               
                if(is_notice_viwed($notice_id)=='NO')
                {
                $viewer_arr = explode(",",$viewer);
                array_push($viewer_arr, $user_id);
                $viewer_arr = array_unique($viewer_arr);
                $viewers = implode(",", $viewer_arr);
                update_data('notification', array('viewer'=>$viewers), $notice_id);
                }
            }
        }
    }


// ATT DETAILS  
 function today_att_status($stu_class, $stu_section ,$status ='P' )
    {
        $att_month = strtolower(date('M'))."_".date('Y');
        $att_date ="d_".date('j');
        $sql ="SELECT student_class, student_section, count(student_id) as count FROM `student_att`, student where student_att.student_id = student.id and att_month ='$att_month' and $att_date ='$status' and student_class ='$stu_class' and student_section ='$stu_section' group by student_class, student_section";
        $res = direct_sql($sql);
       return $res['data'][0]['count'];
    }
    

function monthly_avg_att($class, $section='')
{
		$mvalue = remove_space(date('M'))."_".date('Y');
		
		if($section==''){
		    	$get_stu = get_all('student', '*', array('student_class' => $class));
		}
		else{
		$get_stu = get_all('student', '*', array('student_class' => $class, 'student_section' => $section));
		}
		
		if ($get_stu['count'] > 0) {
			foreach ($get_stu['data'] as $row) {
				$id = $row['id'];
				$sql = "SELECT * from student_att where student_id = $id and att_month like '$mvalue' ";
				$res = direct_sql($sql);
				if ($res['count'] >  0) {
					foreach ($res['data'] as $att_data) {
						for ($i = 1; $i < 31; $i++) {
							$day = "d_" . $i;
							if ($att_data[$day] == "P") {
								$count++;
							}
						}
					}
				}
			}
		}
	return $x= round($count/date('d'));
}

//  STUDY PLANT

		function active_class_list()
		{
			$res = direct_sql('SELECT student_class, student_section FROM `student` group by student_class, student_section');
			
			foreach($res['data'] as $row)
			{
				  if($row['student_class'] !='' and $row['student_section'] !='' )
				  {
				   $all_class_list[] = $row['student_class'] ."-".$row['student_section'];
				  }
			}
			return array_filter($all_class_list);
		}
		
		function allowed_class_list($user_name)
		{
			
		   $res = get_data('instructor',$user_name, null, 'mobile');
		   $list = $res['data']['allow_class'];
		   
		   $arr = explode(",",$list);
		   return $arr;
		}
		
		function allowed_subject($user_name)
		{
			
		   $res = get_data('instructor',$user_name, null, 'mobile');
		   $list = $res['data']['allow_subject'];
		   $arr = explode(",",$list);
		   
		   //$str ='';
		   foreach($arr as $id)
		   {
			  // $subject_name =get_data('subject',$id,'subject_name')['data'];
			  // $str =$str. "<option value='".$id."'>". $subject_name ."</option>";
			  // $str =$str.  $subject_name ;
			  $str[$id] = get_data('subject_book',$id,'subject_name')['data'];
		   }
		   return $str;
		}
		function create_list_with_id($table_name, $field,  $whereArr =null)
		{
		global $con;
		$cols = array();
 		
		
		if($whereArr != null)
		{
			foreach($whereArr as $key=>$value) 
				{
					$newvalue = preg_replace('/[^A-Za-z.@,:+0-9\-_]/', ' ', $value);
					$where[] = "$key = '$newvalue'";
				}
			$sql = "select distinct($field),id from ". $table_name ." WHERE " .implode('and ', $where);
				
		}
		else{
			$sql = "select distinct($field),id from ". $table_name;
		}
	
		$res = mysqli_query($con,$sql) or die(" Error IN create List : ".mysqli_error($con));
			if (mysqli_num_rows($res)>=1)
			{
				while($row =mysqli_fetch_assoc($res))
				{
				    $id = $row['id'];
					$list[$id] =$row[$field];
				}
			}
			else{
				return null;
			}
		return $list;
		}
	
function check_list_with_id($name, $array_list, $selected=null, $height='160px' )
{
	$selected = explode(',',$selected);
	echo "<div style='overflow-y:auto;height:$height'>";
	?>
	<span class='btn btn-sm btn-info float-right'  onclick="selectcheck('<?php echo $name;?>')" ><i class='fa fa-check'></i></span><hr>
	<?php
	foreach(array_filter($array_list) as $id=>$list)
	{
		$checked =null;
		$x = array_search(trim($id), array_map('trim', $selected));
		
		if( $x >=-1){ $checked ='checked';}
		?>
		<div class="checkbox">
			  <input type="checkbox" value="<?php echo $id;?>" id="Checkbox_<?php echo $list;?>" <?php echo $checked; ?> name='<?php echo $name.'[]';?>'> 
			  <label for="Checkbox_<?php echo $list;?>"><?php echo $list?></label>
		</div>
		<?php
	}
	echo "</div>";
}

		function meeting_option ($ins_id)
		{
			$meet ='';
			$meetarr =array();
			$res = get_data('instructor',$ins_id);
			if($res['count']>0)
			{
				extract($res['data']);
				
				if($zoom_host_link !='')
				{
					$meet = $meet ."<option value='zoom_code'>Zoom Meeting</option>";
					$meetarr['zoom_code'] = 'Zoom Meeting';
				}
				if($gm_code !='')
				{
					$meet = $meet ."<option value='gm_code'>Google Meet</option>";
					$meetarr['meet_code'] = 'Google Meet';
				}
				if($jitsi_code !='')
				{
					$meet = $meet ."<option value='jitsi_code'>Jitsi Meeting</option>";
					$meetarr['jitsi_code'] = 'Jitsi Meeting';
				}
				
			}
			$res['html'] =$meet;
			$res['list'] =$meetarr;
			return $res;
		}

		// Logout Any Where
function logout()
{
	global $base_url;
	$user_id = $_SESSION['id'];
	unset($_SESSION['user_name']);
	unset($_SESSION['user_type']);
	unset($_SESSION['id']);
	session_destroy();
	$udata = array('token' => '', 'status' => 'LOGOUT');
	$result = update_data('user', $udata, $user_id, 'id');
	$base_url . 'login.php';
	echo "<script> window.location ='".$base_url ."' </script>";
}


//=================Payment Gateway Function Start============
function initiatePhonePePayment() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $studentId = $data['student_id'];
    $studentName = $data['student_name'];
    $amount = $data['amount'];
    $transactionId = $data['transaction_id'];
    $selectedMonths = $data['selected_months'];
    $couponCode = $data['coupon_code'] ?? null;
    $discount = $data['discount'] ?? 0;
    
    // Store transaction in database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $sql = "INSERT INTO fee_transactions 
            (transaction_id, student_id, amount, selected_months, coupon_code, discount, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 'PENDING', NOW())";
    
    $stmt = $conn->prepare($sql);
    $monthsJson = json_encode($selectedMonths);
    $stmt->bind_param("sidssd", $transactionId, $studentId, $amount, $monthsJson, $couponCode, $discount);
    $stmt->execute();
    
    // Prepare PhonePe payment request
    $amountInPaise = $amount * 100; // Convert to paise
    
    $payload = [
        "merchantId" => PHONEPE_MERCHANT_ID,
        "merchantTransactionId" => $transactionId,
        "merchantUserId" => "MUID" . $studentId,
        "amount" => $amountInPaise,
        "redirectUrl" => "https://dpsmushkipur.com/bine/api.php?task=phonepe_callback&txn_id=" . $transactionId,
        "redirectMode" => "POST",
        "callbackUrl" => "https://dpsmushkipur.com/bine/api.php?task=phonepe_callback",
        "mobileNumber" => "", // Add student mobile if available
        "paymentInstrument" => [
            "type" => "PAY_PAGE"
        ]
    ];
    
    $jsonPayload = json_encode($payload);
    $base64Payload = base64_encode($jsonPayload);
    
    // Generate checksum
    $checksum = hash('sha256', $base64Payload . "/pg/v1/pay" . PHONEPE_SALT_KEY) . "###" . PHONEPE_SALT_INDEX;
    
    // Make API request to PhonePe
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PHONEPE_BASE_URL . '/pg/v1/pay');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['request' => $base64Payload]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-VERIFY: ' . $checksum
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $responseData = json_decode($response, true);
    
    if ($responseData['success']) {
        echo json_encode([
            'success' => true,
            'payment_url' => $responseData['data']['instrumentResponse']['redirectInfo']['url'],
            'phonepe_scheme' => $responseData['data']['instrumentResponse']['redirectInfo']['url'] ?? null
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $responseData['message'] ?? 'Failed to initiate payment'
        ]);
    }
}

function checkPaymentStatus() {
    $data = json_decode(file_get_contents('php://input'), true);
    $transactionId = $data['transaction_id'];
    
    // Check PhonePe status
    $checksum = hash('sha256', "/pg/v1/status/" . PHONEPE_MERCHANT_ID . "/" . $transactionId . PHONEPE_SALT_KEY) . "###" . PHONEPE_SALT_INDEX;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PHONEPE_BASE_URL . '/pg/v1/status/' . PHONEPE_MERCHANT_ID . '/' . $transactionId);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-VERIFY: ' . $checksum,
        'X-MERCHANT-ID: ' . PHONEPE_MERCHANT_ID
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $responseData = json_decode($response, true);
    
    if ($responseData['success'] && $responseData['code'] === 'PAYMENT_SUCCESS') {
        // Update database
        updatePaymentStatus($transactionId, 'SUCCESS', $responseData);
        
        echo json_encode([
            'status' => 'SUCCESS',
            'data' => $responseData['data']
        ]);
    } elseif ($responseData['code'] === 'PAYMENT_PENDING') {
        echo json_encode(['status' => 'PENDING']);
    } else {
        updatePaymentStatus($transactionId, 'FAILED', $responseData);
        echo json_encode([
            'status' => 'FAILED',
            'message' => $responseData['message'] ?? 'Payment failed'
        ]);
    }
}

function updatePaymentStatus($transactionId, $status, $responseData) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Update transaction status
    $sql = "UPDATE fee_transactions SET status = ?, phonepe_response = ?, updated_at = NOW() WHERE transaction_id = ?";
    $stmt = $conn->prepare($sql);
    $responseJson = json_encode($responseData);
    $stmt->bind_param("sss", $status, $responseJson, $transactionId);
    $stmt->execute();
    
    if ($status === 'SUCCESS') {
        // Get transaction details
        $sql = "SELECT * FROM fee_transactions WHERE transaction_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $transaction = $result->fetch_assoc();
        
        // Update fee status for selected months
        $selectedMonths = json_decode($transaction['selected_months'], true);
        foreach ($selectedMonths as $month) {
            $sql = "UPDATE student_fees SET status = 'PAID', paid_date = NOW(), transaction_id = ? 
                    WHERE student_id = ? AND month = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sis", $transactionId, $transaction['student_id'], $month);
            $stmt->execute();
        }
    }
}

function handlePhonePeCallback() {
    $response = $_POST['response'] ?? '';
    
    if ($response) {
        $decodedResponse = json_decode(base64_decode($response), true);
        
        // Verify checksum
        $receivedChecksum = $_SERVER['HTTP_X_VERIFY'] ?? '';
        $calculatedChecksum = hash('sha256', $response . PHONEPE_SALT_KEY) . "###" . PHONEPE_SALT_INDEX;
        
        if ($receivedChecksum === $calculatedChecksum) {
            $transactionId = $decodedResponse['data']['merchantTransactionId'];
            $status = $decodedResponse['code'] === 'PAYMENT_SUCCESS' ? 'SUCCESS' : 'FAILED';
            
            updatePaymentStatus($transactionId, $status, $decodedResponse);
        }
    }
    
    // Redirect user back to app
    $txnId = $_GET['txn_id'] ?? '';
    header("Location: dpsmushkipur://payment-complete?txn_id=" . $txnId);
    exit;
}

function validateCoupon() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $couponCode = $data['coupon_code'];
    $studentId = $data['student_id'];
    $amount = $data['amount'];
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    $sql = "SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND expiry_date >= CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($coupon = $result->fetch_assoc()) {
        // Check usage limit
        $sql = "SELECT COUNT(*) as usage_count FROM coupon_usage WHERE coupon_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $coupon['id']);
        $stmt->execute();
        $usageResult = $stmt->get_result();
        $usage = $usageResult->fetch_assoc();
        
        if ($usage['usage_count'] < $coupon['max_usage']) {
            // Calculate discount
            $discount = 0;
            if ($coupon['discount_type'] === 'PERCENTAGE') {
                $discount = ($amount * $coupon['discount_value']) / 100;
                if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                    $discount = $coupon['max_discount'];
                }
            } else {
                $discount = $coupon['discount_value'];
            }
            
            echo json_encode([
                'valid' => true,
                'discount' => $discount,
                'message' => 'Coupon applied successfully'
            ]);
        } else {
            echo json_encode([
                'valid' => false,
                'message' => 'Coupon usage limit exceeded'
            ]);
        }
    } else {
        echo json_encode([
            'valid' => false,
            'message' => 'Invalid or expired coupon'
        ]);
    }
}
//=================Payment Gateway End==========