<?php
require_once('op_config.php');
if (isset($_SESSION['db_name']) and $_SESSION['db_name'] != '') {
	$db_name = $_SESSION['db_name'];
	$con = mysqli_connect($host_name, $db_user, $db_password, $db_name)
		or die("Unable to Connect, Check the Connection Parameter. " . mysqli_error($con));
} else {
	$con = mysqli_connect($host_name, $db_user, $db_password, $db_name)
		or die("Unable to Connect, Check the Connection Parameter. " . mysqli_error($con));
}

// === OFFERPLANT MASTER FUNTION FOR EVERY WHERE ==== //

//  INSERT ( insert_row, insert_data, insert_html )
// 	UPDATE (update_date, update_multi_data)
// 	REMOVE (remove_data, remove_multi_data)
// 	DELETE (delete_data, delete_multi_data)
//	FETCH	(get_data, get_all, get_multi_data, get_not, direct_sql)
//	CRYPTO (encode, decode)
//	STRING (rnd_str, add_space, remove_space)
//	SECURITY (xss_clean, post_clean)
//	ACCESS	(verify, verify_request)
//	EXCEL 	(csv_import, csv_export)
//	YOUTUBE ( ytid, get_vid)
// 	COMM	(send_msg, send_sms, rtf_mail ,wa_send )
//	API 	(api_call)
//	QRcode	(qrcode)
//	IMAGE 	(uploadimg, remote_file_size)
// 	DATABASE Sturucture (table_list, Create_table, direct_sql_file,add_column, remove_column)
// 	CONFIG 	(set_config, update_config,delete_config, all_config, get_config)
//	HTML 	(input_text, input_date, btn_view, btn_edit, btn_delete, check_list) 
//	UI DROPDOWN (dropdown, dropdown_list, dropdown_list_multiple, dropdown_list_where,  create_list, )
// DATE DIFFERENCE(date_difference)

// Create Table with Basic Structure  

function create_table($table_name)
{
	global $con;
	$sql1 = "CREATE TABLE IF NOT EXISTS $table_name (
	  id int(11) NOT NULL,
	  status varchar(25) DEFAULT NULL,
	  created_at timestamp NULL DEFAULT NULL,
	  created_by int(11) DEFAULT NULL,
	  updated_at timestamp NULL DEFAULT NULL,
	  updated_by int(11) DEFAULT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

	$res[] = mysqli_query($con, $sql1) or die("Error In Createting Table : " . mysqli_error($con));

	$sql2 = "ALTER TABLE $table_name  ADD PRIMARY KEY (id)";
	$res[] = mysqli_query($con, $sql2) or die("Error In Assigning Primary Key : " . mysqli_error($con));

	$sql3 = " ALTER TABLE $table_name  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";

	$res[] = mysqli_query($con, $sql3) or die("Error In Creating Auto Increment ID  : " . mysqli_error($con));

	$sql4 = "ALTER TABLE $table_name CHANGE `updated_at` `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
	$res[] = mysqli_query($con, $sql4) or die("Error In Assign Updated at Default Value as Current Timestamp : " . mysqli_error($con));
	return $res;
}


// List of all Table Exist in databse 
function table_list()
{
	global $con;
	global $db_name;
	$result = array();
	$res = mysqli_query($con, "show tables") or die("Error in Creating Table List" . mysqli_error($con));
	$ct = mysqli_num_rows($res);
	if ($ct >= 1) {
		while ($row = mysqli_fetch_assoc($res)) {
			//$data[] = $row['Tables_in_'.$db_name];
			$data[] = $row['Tables_in_' . $db_name];
		}
		$result['count'] = $ct;
		$result['status'] = 'success';
		$result['data'] = $data;
	} else {
		$result['count'] = 0;
		$result['status'] = 'error';
		$result['data'] = null;
	}
	return $result;
}

function column_list($table_name = 'users')
{
	global $con;
	global $db_name;
	$result = array();
	$sql = "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, COLUMN_DEFAULT,  EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$db_name' AND TABLE_NAME='$table_name'";
	$res = mysqli_query($con, $sql) or die("Error in Creating Table List" . mysqli_error($con));
	$ct = mysqli_num_rows($res);
	if ($ct >= 1) {
		while ($row = mysqli_fetch_assoc($res)) {
			$data[] = $row;
		}
		$result['count'] = $ct;
		$result['status'] = 'success';
		$result['data'] = $data;
	} else {
		$result['count'] = 0;
		$result['status'] = 'error';
		$result['data'] = null;
	}
	return $result;
}


// ENCODE STRING INTO NON READABLE STRING  
function encode($input)
{
	return strtr(base64_encode($input), '+/=', '._-');
}

// DECODE STRING FROM NON READABLE STRING TO READABLE
function decode($input)
{
	$url = base64_decode(strtr($input, '._-', '+/='));
	//$parts = parse_url($url);
	parse_str($url, $query);
	return $query;
}

// USE TO CREATE STRING REPLACE SPACE WITH UNDERSCORE FORM STRING 

function remove_space($str)
{
	$str = trim($str);
	return strtolower(preg_replace("/[^a-zA-Z0-9]+/", "_", $str));
}

// USE TO CREATE STRING REPLACE UNDERSCORE WITH SPACE FORM STRING 

function add_space($str)
{
	$str = trim($str);
	return ucwords(str_replace('_', ' ', $str));
}

// GET VIDEO ID FROM YOUTUBE LINK 

function get_vid($url)
{
	parse_str(parse_url($url, PHP_URL_QUERY), $my_array_of_vars);
	return $my_array_of_vars['v'];
}

// USE To CREATE A RANDOM STRING OF SPECIFIC LINK 
function rnd_str($length_of_string)
{
	// String of all alphanumeric character 
	$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

	// Shufle the $str_result and returns substring of specified length 
	return strtoupper(substr(str_shuffle($str_result), 0, $length_of_string));
}

// USE TO CLEAN DATE AND REMOVE HAKABLE CODE 
function xss_clean($data)
{
	// Fix &entity\n;
	$data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

	// Remove any attribute starting with "on" or xmlns
	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

	// Remove javascript: and vbscript: protocols
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

	// Remove namespaced elements (we do not need them)
	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

	do {
		// Remove really unwanted tags
		$old_data = $data;
		$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
	} while ($old_data !== $data);

	// we are done...
	return $data;
}

// USE TO CLEAN MULTI LEVEL ARRAY DATA
function post_clean($arr_data)
{
	if (is_array($arr_data)) {
		foreach ($arr_data as $data) {

			$key = array_search($data, $arr_data);
			if (is_array($data)) {
				post_clean($data);
			} else {
				$arr_data[$key] = xss_clean($data);
			}
		}
	} else {
		xss_clean($arr_data);
	}
	return $arr_data;
}

// CHECK ORIGIN OF REQUESTED URL 
function verify_request()
{
	$ref = parse_url($_SERVER["HTTP_REFERER"]);
	$rh = $ref['host'];
	$mh = $_SERVER['HTTP_HOST'];

	if ($rh <> $mh) {
		return false;
	} else {
		return true;
	}
}


function verify($user_type)
{
	$actual_link = "http://" . $_SERVER['HTTP_HOST']; //$_SERVER['REQUEST_URI'];
	//die($actual_link);
	$current_page = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
	if ($user_type == "ADMIN") {
		global $admin_role;
		$all_page = $admin_role;
	} else if ($user_type == 'CLIENT') {
		global $client_role;
		$all_page = $client_role;
	} else {
		die("Invalid User ! Don't Have Permission");
	}

	if (!array_search($current_page, $all_page)) {
		die("Don't have Permission");
	}
}

// TO ADD COLUMN IN TABLE 

function add_column($table_name, $col_name, $data_type = 'varchar(255)', $default = null)
{
	global $con;
	$sql = "SHOW COLUMNS FROM $table_name LIKE '$col_name'";
	$result = direct_sql($sql);

	if ($result['count'] == 0) {
		$sql = "alter table $table_name add column $col_name $data_type $default";
		$res = mysqli_query($con, $sql) or die("Error in Adding Column" . mysqli_error($con));
	}
	create_log($sql);
}

// TO REMOVE COLUMN IN TABLE 

function remove_column($table_name, $col_name)
{
	global $con;
	//global $user_type;
	$user_type = $_SESSION['user_type'];
	if ($user_type == "ADMIN") {
		echo $sql = "alter table $table_name drop column $col_name ";
		$res = mysqli_query($con, $sql) or die("Error in Removing Column" . mysqli_error($con));
	}
	create_log($sql);
}

// TO INSERT BLANK ROW IN A TABLE  	
function insert_row($table_name)
{
	global $con;
	global $user_id;
	global $current_date_time;
	$result = get_multi_data($table_name, array('created_by' => $user_id, 'status' => 'AUTO'), ' order by id desc limit 1');
	if ($result['count'] < 1) {
		$result = insert_data($table_name, array('status' => 'AUTO', 'created_at' => $current_date_time));
		$id = $result['id'];
	} else {
		$id = $result['data'][0]['id'];
	}
	return array('table' => $table_name, 'id' => $id);
	create_log($sql);
}

// TO INSERT DATA IN A TABLE  		
function insert_data($table_name, $ArrayData)
{
	global $con;
	global $user_id;
	global $current_date_time;
	//echo"<pre>";
	//print_r($ArrayData);
	$ArrayData['created_by'] = $ArrayData['created_by'] ?? $user_id;
	$ArrayData['created_at'] = $current_date_time;

	$columns = implode(", ", array_keys($ArrayData));
	$escaped_values = array_values($ArrayData);
	foreach ($escaped_values as $newvalue) {
		$newvalues[] = "'" . post_clean($newvalue) . "'";
	}
	//$data = mysqli_escape_string ($escaped_values);
	$values = implode(", ", $newvalues);

	$sql = "INSERT IGNORE INTO $table_name ($columns) VALUES ($values)";

	$res = mysqli_query($con, $sql) or die("Error in Inserting Data" . mysqli_error($con));
	$id = mysqli_insert_id($con);
	if (mysqli_affected_rows($con) > 0) {
		$result['id'] = $id;
		$result['status'] = 'success';
		$result['msg'] = " Data Added Successfully";
	} else {
		$result['id'] = 0;
		$result['status'] = 'error';
		$result['msg'] = mysqli_error($con);
	}
	//create_log($sql);
	return $result;
}

// TO INSERT DATA FROM RTF TEXTAREA 

function insert_html($table_name, $ArrayData)
{
	global $con;
	global $user_id;
	global $current_date_time;
	//echo"<pre>";
	//print_r($ArrayData);
	$ArrayData['created_by'] = $user_id;
	$ArrayData['created_at'] = $current_date_time;

	$columns = implode(", ", array_keys($ArrayData));
	$escaped_values = array_values($ArrayData);
	foreach ($escaped_values as $newvalue) {
		$newvalues[] = "'" . htmlspecialchars($newvalue) . "'";
	}
	//$data = mysqli_escape_string ($escaped_values);
	$values = implode(", ", $newvalues);

	$sql = "INSERT IGNORE INTO $table_name ($columns) VALUES ($values)";

	$res = mysqli_query($con, $sql) or die("Error in Inserting Data" . mysqli_error($con));
	$id = mysqli_insert_id($con);
	if (mysqli_affected_rows($con) > 0) {
		$result['id'] = $id;
		$result['status'] = 'success';
		$result['msg'] = " RTF Data Added Successfully";
	} else {
		$result['id'] = 0;
		$result['status'] = 'error';
		$result['msg'] = mysqli_error($con);
	}
	//create_log($sql);
	return $result;
}

// TO UPDATE SINGLE RECORD OF TABLE 
function update_data($table_name, $ArrayData, $id, $pkey = 'id')
{
	global $con;
	global $user_id;
	global $current_date_time;

	$ArrayData['updated_at'] = $current_date_time;
	$ArrayData['updated_by'] = $ArrayData['updated_by'] ?? $user_id;

	$cols = array();
	foreach ($ArrayData as $key => $value) {
		if ($value == '') {
			unset($ArrayData[$key]);
		} else {
			$newvalue = post_clean($value);
			$cols[] = "$key = '$newvalue'";
		}
	}
	$sql = "UPDATE $table_name SET " . implode(', ', $cols) . " WHERE $pkey  ='" . $id . "'";
	$res = mysqli_query($con, $sql) or mysqli_error($con);
	$num = mysqli_affected_rows($con);
	if ($num > 0) {
		$result['id'] = $id;
		$result['status'] = 'success';
		$result['msg'] = $num . " Record Updated Successfully";
	} else {
		$result['id'] = $id;
		$result['status'] = 'error';
		$result['msg'] = "Sorry ! No Update Found" . mysqli_error($con);
	}
	//create_log($sql);
	return $result;
}

// TO UPDATE MULTIPLE RECORD OF TABLE BASED ON CONDITION

function update_multi_data($table_name, $ArrayData, $whereArr)
{
	global $con;
	$cols = array();
	foreach ($ArrayData as $key => $value) {
		$newvalue = post_clean($value);
		$cols[] = "$key = '$newvalue'";
	}

	foreach ($whereArr as $key => $value) {
		$newvalue = post_clean($value);
		$where[] = "$key = '$newvalue'";
	}

	$sql = "UPDATE $table_name SET " . implode(', ', $cols) . " WHERE " . implode('and ', $where);
	$res = mysqli_query($con, $sql) or mysqli_error($con);
	$num = mysqli_affected_rows($con);
	if ($num > 0) {
		$result['count'] = $num;
		$result['status'] = 'success';
		$result['msg'] = $num . " Multi Record Updated Successfully";
	} else {
		$result['status'] = 'error';
		$result['msg'] = "Sorry ! No Update Found" . mysqli_error($con);
	}
	create_log($sql);
	return $result;
}

// SOFT DELETE SINGLE RECORD FROM TABLE

function remove_data($table_name, $id, $pkey = 'id')
{
	global $con;
	global $user_id;
	global $current_date_time;

	$sql = "UPDATE $table_name SET status = 'DELETED' , updated_by = '$user_id', updated_at ='$current_date_time' WHERE $pkey  ='" . $id . "'";
	$res = mysqli_query($con, $sql) or die("Error in Deleting Data" . mysqli_error($con));
	$num = mysqli_affected_rows($con);
	if ($num >= 1) {
		$result['id'] = $id;
		$result['status'] = 'success';
		$result['msg'] = $num . " Record removed successfully";
	} else {
		$result['id'] = $id;
		$result['status'] = 'error';
		$result['msg'] = "Sorry ! No record found to delete";
	}
	create_log($sql);
	return $result;
}

// SOFT DELETE MULTIPLE RECORD BASED ON CONDITION 

function remove_multi_data($table_name, $whereArr)
{
	global $con;
	global $user_id;
	global $current_date_time;
	foreach ($whereArr as $key => $value) {
		$newvalue = preg_replace('/[^A-Za-z._@,:+0-9\-]/', ' ', $value);
		$where[] = "$key = '$newvalue'";
	}
	$sql = "update " . $table_name . " set status ='DELETED' updated_by = '$user_id', updated_at ='$current_date_time' WHERE " . implode('and ', $where);
	$res = mysqli_query($con, $sql) or die("Error in Deleting Data" . mysqli_error($con));
	$num = mysqli_affected_rows($con);
	if ($num >= 1) {
		// $result['count'] = ;
		$result['status'] = 'success';
		$result['msg'] = $num . " Record deleted successfully";
	} else {
		// $result['id'] = $id;
		$result['status'] = 'error';
		$result['msg'] = "Soory ! No Record found to delete";
	}
	create_log($sql);
	return $result;
}


// HARD DELETE SINGLE RECORD FROM TABLE

function delete_data($table_name, $id, $pkey = 'id')
{
	global $con;
	$sql = "delete from $table_name WHERE $pkey  ='$id'";
	$res = mysqli_query($con, $sql) or die("Error in Deleting Data" . mysqli_error($con));
	$num = mysqli_affected_rows($con);
	if ($num >= 1) {
		$result['id'] = $id;
		$result['status'] = 'success';
		$result['msg'] = $num . " Record deleted successfully";
	} else {
		$result['id'] = $id;
		$result['status'] = 'error';
		$result['msg'] = "Sorry ! No record found to delete";
	}
	create_log($sql);
	return $result;
}

// HARD DELETE MULTIPLE RECORD BASED ON CONDITION 

function delete_multi_data($table_name, $whereArr)
{
	global $con;
	foreach ($whereArr as $key => $value) {
		$newvalue = preg_replace('/[^A-Za-z._@,:+0-9\-]/', ' ', $value);
		$where[] = "$key = '$newvalue'";
	}
	$sql = "delete from" . $table_name . " WHERE " . implode('and ', $where);
	$res = mysqli_query($con, $sql) or die("Error in Deleting Data" . mysqli_error($con));
	$num = mysqli_affected_rows($con);
	if ($num >= 1) {
		// $result['id'] = $id;
		$result['status'] = 'success';
		$result['msg'] = $num . " Record deleted successfully";
	} else {
		// $result['id'] = $id;
		$result['status'] = 'error';
		$result['msg'] = "Soory ! No Record found to delete";
	}
	create_log($sql);
	return $result;
}


// FETCH ALL DATA BASED On CONDITION (Optional)	

function get_all($table_name, $column_list = '*', $whereArr = null, $orderby = 'id DESC')
{
	global $con;
	$orderby = ' order by ' . $orderby;
	if ($column_list <> '*') {
		$column_list = implode(',', $column_list);
	}

	if ($whereArr <> null) {
		foreach ($whereArr as $key => $value) {
			$key = trim($key);
			$newvalue = preg_replace('/[^A-Za-z._@,:+0-9\-]/', ' ', $value);
			$where[] = "$key = '$newvalue'";
		}
		$sql = "SELECT $column_list FROM $table_name where " . implode('and ', $where);
	} else {
		$sql = "SELECT $column_list FROM $table_name where status not in ('AUTO','DELETED')  ";
	}

	$res = mysqli_query($con, $sql . $orderby) or die("Error In Loading Data : " . mysqli_error($con));
	$ct = mysqli_num_rows($res);
	if ($ct >= 1) {
		while ($row = mysqli_fetch_assoc($res)) {
			$data[] = $row;
		}
		$result['count'] = $ct;
		$result['status'] = 'success';
		$result['data'] = $data;
	} else {
		$result['count'] = 0;
		$result['status'] = 'error';
		$result['data'] = null;
	}
	return $result;
}

// FETCH ALL DATA NOT On CONDITION (Optional)	

function get_not($table_name, $column_list = '*', $whereArr = null, $orderby = 'id DESC')
{
	global $con;
	$orderby = ' order by ' . $orderby;
	if ($column_list <> '*') {
		$column_list = implode(',', $column_list);
	}

	if ($whereArr <> null) {
		foreach ($whereArr as $key => $value) {
			$key = trim($key);
			$newvalue = preg_replace('/[^A-Za-z._@,:+0-9\-]/', ' ', $value);
			$where[] = "$key <> '$newvalue'";
		}
		$sql = "SELECT $column_list FROM $table_name where " . implode('and ', $where);
	} else {
		$sql = "SELECT $column_list FROM $table_name where status <>'AUTO' ";
	}

	$res = mysqli_query($con, $sql . $orderby) or die("Error In Loading Data : " . mysqli_error($con));
	$ct = mysqli_num_rows($res);
	if ($ct >= 1) {
		while ($row = mysqli_fetch_assoc($res)) {
			$data[] = $row;
		}
		$result['count'] = $ct;
		$result['status'] = 'success';
		$result['data'] = $data;
	} else {
		$result['count'] = 0;
		$result['status'] = 'error';
		$result['data'] = null;
	}
	return $result;
}

// EXECUTE ANY SQL STATMENT DIRECTLY AND GET FORMATED RESULT

function direct_sql($sql, $type = 'get')
{
	global $con;
	$res = mysqli_query($con, $sql) or die("Error In Loding Data : " . mysqli_error($con));
	if ($type == 'set') {
		$ct = mysqli_affected_rows($con);
	} else {
		$ct = mysqli_num_rows($res);
	}
	if ($ct >= 1) {
		while ($row = mysqli_fetch_assoc($res)) {
			$data[] = $row;
		}
		$result['count'] = $ct;
		$result['status'] = 'success';
		$result['data'] = $data;
	} else {
		$result['count'] = 0;
		$result['status'] = 'error';
		$result['data'] = null;
	}
	$result['sql'] = $sql;
	return $result;
}

function direct_sql_file($filename)
{
	global $con;
	// Temporary variable, used to store current query
	$templine = '';
	// Read in entire file
	$lines = file($filename);
	// Loop through each line
	foreach ($lines as $line) {
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;

		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';') {
			// Perform the query
			$con->query($templine) or print ('Error performing query \'<strong>' . $templine . '\': ' . $con->error() . '<br /><br />');
			// Reset temp variable to empty
			$templine = '';
		}
	}
	$res['msg'] = $filename . " imported successfully";
	$res['status'] = "success";
	return $res;
}

// GET SINGLE DATA FORM TABLE BASED ON CONDITION

function get_data($table_name, $id, $field_name = null, $pkey = 'id')
{
	global $con;
	$result['count'] = 0;
	$result['status'] = 'error';
	$sql = "SELECT * FROM $table_name where $pkey ='$id' ";
	$res = mysqli_query($con, $sql) or die(" Data Information Error : " . mysqli_error($con));
	$ct = mysqli_num_rows($res);
	$result['count'] = $ct;
	if ($ct >= 1) {
		$row = mysqli_fetch_assoc($res);
		extract($row);
		if ($field_name) {
			$result['status'] = 'success';
			$result['data'] = $row[$field_name];
		} else {
			$result['status'] = 'success';
			$result['data'] = $row;
		}
	} else {
		$result['count'] = 0;
		$result['status'] = 'success';
		$result['data'] = null;
	}
	return $result;
}

// GET DATA FORM TABLE BASED ON MULTIPLE CONDITION

function get_multi_data($table_name, $whereArr, $order = null)
{
	global $con;

	foreach ($whereArr as $key => $value) {
		$newvalue = preg_replace('/[^A-Za-z.@_,:+0-9\-]/', ' ', $value);
		$where[] = "$key = '$newvalue'";
	}

	$sql = "select * from " . $table_name . " WHERE " . implode('and ', $where) . $order;
	$res = mysqli_query($con, $sql) or mysqli_error($con);
	$num = mysqli_num_rows($res);
	if ($num > 0) {
		while ($row = mysqli_fetch_assoc($res)) {
			$data[] = $row;
		}
		$result['status'] = 'success';
		$result['count'] = $num;
		$result['data'] = $data;
	} else {
		$result['status'] = 'error';
		$result['count'] = 0;
		$result['data'] = mysqli_error($con);
	}
	return $result;
}

function upload_aadhar($file_name, $imgkey = 'rand', $target_dir = "upload")
{
	if (!file_exists($target_dir)) {
		mkdir($target_dir, 0755, true);
	}
	if ($imgkey == 'rand') {
		$imgkey = rand(10000, 99999);
	}
	$target_file = $imgkey . "_" . basename($_FILES[$file_name]["name"]);
	$target_file = strtolower(preg_replace("/[^a-zA-Z0-9.]+/", "", $target_file));
	$uploadOk = 1;

	$res['id'] = 0;
	$res['status'] = 'error';
	$res['msg'] = '';
	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image

	$check = getimagesize($_FILES[$file_name]["tmp_name"]);
	if ($check !== false) {
		$res['msg'] = "File is an image - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		$res['msg'] = "File is not an image.";
		$uploadOk = 0;
	}

	// Check if file already exists
	if (file_exists($target_file)) {
		unlink($target_file);
		$res['msg'] = "Sorry, file already exists.";
		$uploadOk = 1;
	}
	// Check file size
	// if ($_FILES[$file_name]["size"] > 5000000) {
	//     $res['msg']= "Sorry, your file is too large.";
	//     $uploadOk = 0;
	// }
	// Allow certain file formats
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf") {
		$res['msg'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		$msg = "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES[$file_name]["tmp_name"], $target_dir . "/" . $target_file)) {
			$res['msg'] = "The file " . basename($_FILES[$file_name]["name"]) . " has been uploaded.";
			$res['id'] = $target_file;
			$res['status'] = 'success';
		} else {
			$res['msg'] = "Sorry, there was an error uploading your file.";
		}
	}
	return $res;
}
function upload_dl($file_name, $imgkey = 'rand', $target_dir = "upload")
{
	if (!file_exists($target_dir)) {
		mkdir($target_dir, 0755, true);
	}
	if ($imgkey == 'rand') {
		$imgkey = rand(10000, 99999);
	}
	$target_file = $imgkey . "_" . basename($_FILES[$file_name]["name"]);
	$target_file = strtolower(preg_replace("/[^a-zA-Z0-9.]+/", "", $target_file));
	$uploadOk = 1;

	$res['id'] = 0;
	$res['status'] = 'error';
	$res['msg'] = '';
	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image

	$check = getimagesize($_FILES[$file_name]["tmp_name"]);
	if ($check !== false) {
		$res['msg'] = "File is an image - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		$res['msg'] = "File is not an image.";
		$uploadOk = 0;
	}

	// Check if file already exists
	if (file_exists($target_file)) {
		unlink($target_file);
		$res['msg'] = "Sorry, file already exists.";
		$uploadOk = 1;
	}
	// Check file size
	// if ($_FILES[$file_name]["size"] > 5000000) {
	//     $res['msg']= "Sorry, your file is too large.";
	//     $uploadOk = 0;
	// }
	// Allow certain file formats
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf") {
		$res['msg'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		$msg = "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES[$file_name]["tmp_name"], $target_dir . "/" . $target_file)) {
			$res['msg'] = "The file " . basename($_FILES[$file_name]["name"]) . " has been uploaded.";
			$res['id'] = $target_file;
			$res['status'] = 'success';
		} else {
			$res['msg'] = "Sorry, there was an error uploading your file.";
		}
	}
	return $res;
}
function upload_img($file_name, $imgkey = 'rand', $target_dir = "upload")
{
	if (!file_exists($target_dir)) {
		mkdir($target_dir, 0755, true);
	}
	if ($imgkey == 'rand') {
		$imgkey = rand(10000, 99999);
	}
	$target_file = $imgkey . "_" . basename($_FILES[$file_name]["name"]);
	$target_file = strtolower(preg_replace("/[^a-zA-Z0-9.]+/", "", $target_file));
	$uploadOk = 1;

	$res['id'] = 0;
	$res['status'] = 'error';
	$res['msg'] = '';
	$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image

	$check = getimagesize($_FILES[$file_name]["tmp_name"]);
	if ($check !== false) {
		$res['msg'] = "File is an image - " . $check["mime"] . ".";
		$uploadOk = 1;
	} else {
		$res['msg'] = "File is not an image.";
		$uploadOk = 0;
	}
	// Check if file already exists
	if (file_exists($target_file)) {
		unlink($target_file);
		$res['msg'] = "Sorry, file already exists.";
		$uploadOk = 1;
	}
	// Allow certain file formats
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf") {
		$res['msg'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		$msg = "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES[$file_name]["tmp_name"], $target_dir . "/" . $target_file)) {
			$res['msg'] = "The file " . basename($_FILES[$file_name]["name"]) . " has been uploaded.";
			$res['id'] = $target_file;
			$res['status'] = 'success';
		} else {
			$res['msg'] = "Sorry, there was an error uploading your file.";
		}
	}
	return $res;
}

function rtf_mail($to, $subject, $msg)
{
	extract($post);
	global $CONFIG;
	extract($CONFIG);
	$from = $noreply_email;

	// To send HTML mail, the Content-type header must be set
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// Create email headers
	$headers .= 'From: ' . $from . "\r\n" .
		'Reply-To: ' . $from . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	// Compose a simple HTML email message
	$message = '<html><body>';
	$message .= "<table width='700px' cellpadding='20px' cellspacing='0px' style='border:solid 1px #ffc000;' align='center'>";
	$message .= "<tr><td colspan='3' aling='center' valign='middle'><img src='" . $base_url . $inst_logo . "' alt='" . $inst_name . "' height='80px' /></td><td colspan='2' align='right'> <h3>$inst_name </h3> Planted by $app_name</td></tr>";
	$message .= "<tr><td colspan='5' valign='middle' height='30px' style='background:#ffc000;text-align:center;padding:5px;'>$subject </td></tr>";
	$message .= "<tr><td colspan='5' aling='center' valign='top' height='350px'><p> $msg </p></td></tr>";
	$message .= "<tr><td colspan='5' bgcolor='#ffc000' align='left'>Regards, <br> $inst_name <br> $inst_address1 $inst_address2 <br> $inst_email  | $inst_url | $app_link </td></tr>";
	$message .= '</table>';
	$message .= '</body></html>';

	// Sending email
	if (mail($to, $subject, $message, $headers)) {
		$res['msg'] = 'Your mail has been sent successfully.';
		$res['status'] == 'success';
	} else {
		$res['msg'] = 'Unable to send email. Please try again.';
		$res['status'] == 'error';
	}
	create_log($res['msg']);
	return $res;
}

function api_call($api_url)
{
	//  Initiate curl
	$ch = curl_init();
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL, $api_url);
	// Execute
	$result = curl_exec($ch);
	// Closing
	curl_close($ch);
	return $result;
}

function csv_export($table_name, $col_list = '*')
{
	global $con;
	global $db_name;
	$filename = $table_name . ".csv";
	$fp = fopen('php://output', 'w');

	if ($col_list == '*') {
		$query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$db_name' AND TABLE_NAME='$table_name'";
		$result = mysqli_query($con, $query);
		while ($row = mysqli_fetch_row($result)) {
			$header[] = $row[0];
		}
	} else {
		$header = explode(',', $col_list);
	}
	// unset($header['created_by']);
	// unset($header['created_at']);
	// unset($header['updated_by']);
	// unset($header['updated_at']);
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename=' . $filename);
	fputcsv($fp, $header);

	$query = "SELECT $col_list FROM $table_name";
	$result = mysqli_query($con, $query);
	while ($row = mysqli_fetch_row($result)) {
		// unset($row['created_by']);
		// unset($row['created_at']);
		// unset($row['updated_by']);
		// unset($row['updated_at']);
		fputcsv($fp, $row);
	}
	//exit;
}


function csv_import($table, $pkey = 'id') // Import CSV FILE to Table
{
	// Allowed mime types
	$csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

	// Validate whether selected file is a CSV file
	$change = $new = 0;
	if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)) {
		if (is_uploaded_file($_FILES['file']['tmp_name'])) {

			// Open uploaded CSV file with read-only mode
			$csvFile = fopen($_FILES['file']['tmp_name'], 'r');
			$col_list = array_map('trim', fgetcsv($csvFile));
			print_r($col_list);
			while (($line = fgetcsv($csvFile)) !== FALSE) {
				$all_data = array_combine($col_list, $line);
				//$search[$pkey] =trim($all_data[$pkey]);
				//$search_result = get_all($table,'*', $search, $pkey);
				$search_result = get_data($table, $all_data[$pkey], null, $pkey);
				echo "<pre>";
				print_r($search_result);
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
				$res = array('status' => 'success', 'change' => $change, 'new' => $new, 'msg' => " $new New Data and $change change found and updated.");
			}
		}
	} else {
		$res = array('status' => 'error', 'change' => $change, 'new' => $new, 'msg' => 'Please upload a valid CSV file.');
	}
	return $res;
}

function qrcode($data)
{

	$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR;

	//html PNG location prefix
	$PNG_WEB_DIR = 'qrcode/';

	require_once "assets/lib/qrlib.php";
	//ofcourse we need rights to create temp dir
	if (!file_exists($PNG_TEMP_DIR))
		mkdir($PNG_TEMP_DIR);
	$filename = $PNG_TEMP_DIR . 'test.png';
	$errorCorrectionLevel = 'H';
	$matrixPointSize = 4;

	if (isset($data)) {
		//it's very important!
		if (trim($data) == '')
			die('data cannot be empty! <a href="?">back</a>');

		// user data
		$filename = $PNG_TEMP_DIR . 'OFFERPLANT' . md5($data . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
		QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
	} else {

		//default data
		echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';
		QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
	}
	//display generated file
	//echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';  
	return $PNG_WEB_DIR . basename($filename);
}



function get_bal_msg()
{
	global $auth_key_msg;
	$api_url = 'http://mysms.msgclub.net/rest/services/send_sms/getClientRouteBalance?AUTH_KEY=' . $auth_key_msg;
	//  Initiate curl
	$ch = curl_init();
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL, $api_url);
	// Execute
	$result = curl_exec($ch);
	// Closing
	curl_close($ch);
	$data = json_decode($result, true);
	return $data[0]['routeBalance'];
}


function get_bal_sms()
{
	global $auth_key_sms;
	$api_url = 'http://sms.morg.in/api/balance.php?&type=4&authkey=' . $auth_key_sms;
	//  Initiate curl
	$ch = curl_init();
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL, $api_url);
	// Execute
	$result = curl_exec($ch);
	// Closing
	curl_close($ch);
	$data = json_decode($result, true);
	return $data;
}

function send_msg($number, $sms, $templateid, $ctype = 'Unicode')
{
	$res = null;
	$numarr = explode(',', $number);
	//if(preg_match('/^[6-9]{1}[0-9]{9}+$/', $number) ==1)
	foreach ($numarr as $num) {
		global $user_id;
		global $sender_id;
		global $sms_auth_key;
		if ($templateid == '1007161789213385513') {
			$sender_id = 'OFFSMS';
			$ctype = "Unicode";
		}

		$no = '91' . urlencode($num);
		$msg = substr(urlencode($sms), 0, 3000);
		//$sms_res = insert_data('sms_report',array('sms_text'=>$msg,'created_by'=>$req_by,'created_at'=>$current_date_time));
		$smsdata = insert_data('op_sms', array('mobile' => $no, 'text' => $msg, 'created_by' => $user_id, 'sender_id' => $sender_id, 'created_at' => $current_date_time));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$url = "http://msg.morg.in/rest/services/sendSMS/sendGroupSms?AUTH_KEY=$sms_auth_key&message=$msg&senderId=$sender_id&routeId=1&mobileNos=$no&smsContentType=$ctype&entityid=1201159513052830502&tmid=140200000022&templateid=$templateid";
		curl_setopt($ch, CURLOPT_URL, $url);
		$res = curl_exec($ch);
		$data = json_decode($res, true);
		update_data('op_sms', array('request_id' => $data['response']), $smsdata['id']);
		curl_close($ch);
	}
	create_log($url);
	return $res;
}

function send_sms($number, $sms)
{
	global $req_by;
	global $current_date_time;
	$res = null;
	if (preg_match('/^[6-9]{1}[0-9]{9}+$/', $number) == 1) {
		$no = urlencode($number);
		$msg = substr(urlencode($sms), 0, 3000);
		insert_data('tbl_sms', array('mobile' => $no, 'text' => $msg, 'created_by' => $req_by, 'created_at' => $current_date_time));
		global $sender_id;
		global $auth_key_sms;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$url = "http://sms.morg.in/api/sendhttp.php?authkey=$auth_key_sms&mobiles=$no&message=$msg&sender=$sender_id&route=4&country=91";
		curl_setopt($ch, CURLOPT_URL, $url);
		$res = curl_exec($ch);
		create_log($url);
		curl_close($ch);
	}
	return $res;
}

function date_range($gap = 15)
{

	$startDate = date('Y-m-d');
	$endDate = date("Y-m-d", strtotime("+$gap days", strtotime($startDate)));
	$startStamp = strtotime($startDate);
	$endStamp = strtotime($endDate);

	if ($endStamp > $startStamp) {
		while ($endStamp >= $startStamp) {

			$data['dv'] = date('Y-m-d', $startStamp);
			$data['dd'] = date('d M D', $startStamp);
			$data['day'] = date('D', $startStamp);
			$dateArr[] = $data; // date( 'Y-m-d', $startStamp );

			$startStamp = strtotime(' +1 day ', $startStamp);
		}
		return $dateArr;
	} else {
		return $startDate;
	}
}

// HTML UI CREATE


function input_text($name, $value, $display = null)
{
	if ($display == null) {
		$display = add_space($name);
	}
	$str = "<div class='form-group'>
                            <label> $display</label>
                            <input type ='text' class='form-control' value='$value' name='$name' id ='$name'  >
                   </div>";
	return $str;
}

function input_date($name, $value = '', $display = null)
{
	if ($value == '') {
		$value = date('Y-m-d');
	}
	if ($display == null) {
		$display = remove_space($name);
	}
	$str = "<div class='form-group'>
						<label> $display</label>
                        <input type ='date' class='form-control' value='$value' name='$name' id ='$name'>
                   </div>";
	return $str;
}

function btn_delete($table, $id, $disabled = "")
{
	global $user_type;
	if ($user_type == "ADMIN") {
		$str = "<button class='delete_btn btn btn-danger btn-xs' data-table='$table' data-id='$id' data-pkey='id' title='Detete This Permanently' $disabled > <i class='fa fa-trash'></i> </button> ";
		return $str;
	}
}

function btn_edit($page_url, $id)
{
	global $user_type;
	if ($user_type == "ADMIN" or $user_type == "DBA" or $user_type == "ACCOUNT") {
		$link = $page_url . "?link=" . encode("id=" . $id);
		$str = "<a href='$link' class=' btn btn-info btn-xs text-light' title='Edit Information '> <i class='fa fa-edit'></i> </a> ";
		return $str;
	}
}

function btn_view($table, $id, $title = '')
{
	$view_link = 'view_data.php?link=' . encode('table=' . $table . '&id=' . $id);
	$str = "<a data-href='$view_link' class='view_data btn btn-success btn-xs text-light' data-title='$title'><i class='fa fa-eye'></i></a> ";
	return $str;
}

function display_img($url, $width = '100px', $height = '100px')
{
	$photo = $_POST['photo'];
	$base_url = $url;
	$str = "<img src='$base_url/upload/$photo' width='$width'  height='$height'  class='img-thumbnail d-self-centered'>";
	return $str;
}



function dropdown($array_list, $selected = null)
{
	foreach ($array_list as $list) {
		?>
		<option value='<?php echo $list; ?>' <?php if ($list == $selected)
			   echo "selected"; ?>><?php echo $list; ?></option>
		<?php
	}
}

function dropdown_with_key($array_list, $selected = null)
{
	foreach ($array_list as $list) {
		$key = array_search($list, $array_list);
		?>
		<option value='<?php echo $key; ?>' <?php if ($key == $selected)
			   echo "selected"; ?>><?php echo $list; ?></option>
		<?php
	}
}

function dropdown_where($table_name, $id, $list, $whereArr, $selected = null)
{
	global $con;
	foreach ($whereArr as $key => $value) {
		$newvalue = post_clean($value);
		$where[] = "$key = '$newvalue'";
	}

	$sql = "select * from " . $table_name . " WHERE " . implode('and ', $where);
	$res = mysqli_query($con, $sql) or mysqli_error($con);
	while ($row = mysqli_fetch_array($res)) {
		$id_inner = $row[$id];
		$show = $row[$list];
		?>
		<option value='<?php echo $id_inner; ?>' <?php if ($id_inner == $selected)
			   echo "selected"; ?>><?php echo $show; ?>
		</option>
		<?php
	}
}

function dropdown_multiple($array_list, $selectedArr = null)
{
	foreach ($array_list as $list) {
		//$key=-1;
		$key = array_search($list, $selectedArr);
		?>
		<option value='<?php echo $list; ?>' <?php if ($key != '')
			   echo "selected"; ?>><?php echo $list . "-" . $key; ?></option>
		<?php
	}
}

function dropdown_list($tablename, $value, $list, $selected = null, $list2 = null)
{
	global $con;
	$i = 0;
	$query = "select * from $tablename where status not in('AUTO','BLOCK','DELETED') order by $list";
	$res = mysqli_query($con, $query) or die(" Creating Drop down Error : " . mysqli_error($con));
	while ($row = mysqli_fetch_array($res)) {
		$key = $row[$value];
		$show = $row[$list];
		$col2 = '';
		if ($list2 <> null) {
			$col2 = "[ " . $row[$list2] . " ]";
		}

		?>
		<option value='<?php echo $key; ?>' <?php if ($key == $selected)
			   echo "selected"; ?>><?php echo $show . " " . $col2; ?>
		</option>
		<?php
	}
}

function dropdown_list_multiple($tablename, $value, $list, $selectedArr = null)
{
	global $con;
	$i = 0;
	$query = "select * from $tablename where status ='ACTIVE' order by $list";
	$res = mysqli_query($con, $query) or die(" Creating Drop down Error : " . mysqli_error($con));
	while ($row = mysqli_fetch_array($res)) {
		$key = $row[$value];
		$show = $row[$list];
		$found = array_search($key, $selectedArr);
		?>
		<option value='<?php echo $key; ?>' <?php if ($found != '')
			   echo "selected"; ?>><?php echo $show; ?></option>
		<?php
	}
}

function check_list($name, $array_list, $selected = null, $height = '160px')
{
	$selected = explode(',', $selected);
	echo "<div style='overflow-y:auto;height:$height'>";
	?>
	<span class='btn btn-xs btn-info float-right' onclick="selectcheck('<?php echo $name; ?>')"><i
			class='fa fa-check'></i></span>
	<hr>
	<?php
	foreach (array_filter($array_list) as $list) {
		$checked = null;
		$x = array_search(trim($list), array_map('trim', $selected));

		if ($x >= -1) {
			$checked = 'checked';
		}
		?>
		<div class="checkbox">
			<input type="checkbox" value="<?php echo $list; ?>" id="Checkbox_<?php echo $list; ?>" <?php echo $checked; ?>
				name='<?php echo $name . '[]'; ?>'>
			<label for="Checkbox_<?php echo $list; ?>"><?php echo $list ?></label>
		</div>
		<?php
	}
	echo "</div>";
}

function create_list($table_name, $field, $whereArr = null)
{
	global $con;
	$cols = array();
	if ($whereArr != null) {
		foreach ($whereArr as $key => $value) {
			$newvalue = preg_replace('/[^A-Za-z._@,:+0-9\-]/', ' ', $value);
			$where[] = "$key = '$newvalue'";
		}
		$sql = "select distinct($field) from " . $table_name . " WHERE " . implode('and ', $where);
	} else {
		$sql = "select distinct($field) from " . $table_name;
	}

	$res = mysqli_query($con, $sql) or die(" Error in creating List : " . mysqli_error($con));
	if (mysqli_num_rows($res) >= 1) {
		while ($row = mysqli_fetch_assoc($res)) {
			$list[] = $row[$field];
		}
	} else {
		return null;
	}
	return $list;
}

function html_table($array, $isedit = false, $isdelete = false, $edit_link = '', $table = '')
{
	// start table
	$html = "<table class='table'  rules='all'>";
	// header row
	$html .= '<tr>';
	foreach ($array[0] as $key => $value) {
		$html .= '<th>' . add_space(htmlspecialchars($key)) . '</th>';
	}
	if ($isedit == true) {
		$html .= '<th> Edit </th>';
	}
	if ($isdelete == true) {
		$html .= '<th> Delete </th>';
	}
	$html .= '</tr>';

	// data rows
	foreach ($array as $key => $value) {
		$html .= '<tr>';
		foreach ($value as $key2 => $value2) {
			$html .= '<td>' . htmlspecialchars($value2) . '</td>';
		}
		if ($isedit == true) {
			$html .= '<td>' . btn_edit($edit_link, $value['id']) . '</td>';
		}
		if ($isdelete == true) {
			$html .= '<td>' . btn_delete('student', $value['id']) . '</td>';
		}
		$html .= '</tr>';
	}

	// finish table and return it

	$html .= '</table>';
	return $html;
}

// GET REMOTE FILE SIZE 
function remote_file_size($url)
{
	// Assume failure.
	$result = -1;

	$curl = curl_init($url);

	// Issue a HEAD request and follow any redirects.
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	//curl_setopt( $curl, CURLOPT_USERAGENT, get_user_agent_string() );

	$data = curl_exec($curl);
	curl_close($curl);

	if ($data) {
		$content_length = "unknown";
		$status = "unknown";

		if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches)) {
			$status = (int) $matches[1];
		}

		if (preg_match("/Content-Length: (\d+)/", $data, $matches)) {
			$content_length = (int) $matches[1];
		}

		// http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
		if ($status == 200 || ($status > 300 && $status <= 308)) {
			$result = $content_length;
		}
	}
	$filesize = round($result / (1024 * 1024), 2); // kilobytes with two digits
	return $filesize;
}
/*=============== CONFIG MANAGAMENT ===========*/

function update_config()
{
	global $CONFIG;
	foreach ($CONFIG as $key => $value) {
		$arr['option_name'] = $key;
		if (is_array($value)) {
			$arr['option_value'] = json_encode($value);
		} else {
			$arr['option_value'] = $value;
		}
		$rescheck = get_data('op_config', $key, null, 'option_name');
		if ($rescheck['count'] == 0) {
			$res = insert_data('op_config', $arr);
		} else {
			$res = update_data('op_config', $arr, $key, 'option_name');
		}
	}
	return $res;
}

function set_config($key, $value = null)
{
	$arr['option_name'] = $key;
	if (is_array($value)) {
		$arr['option_value'] = json_encode($value);
	} else {
		$arr['option_value'] = $value;
	}
	$rescheck = get_data('op_config', $key, null, 'option_name');
	if ($rescheck['count'] == 0) {
		$res = insert_data('op_config', $arr);
	} else {
		$res = update_data('op_config', $arr, $key, 'option_name');
	}
	return $res;
}

function get_config($key)
{
	$res = get_data('op_config', $key, 'option_value', 'option_name');
	if ($res['count'] > 0) {
		return $res['data'];
	} else {
		return null;
	}
}

function delete_config($key)
{
	$res = delete_data('op_config', $key, 'option_name');
	return $res;
}

function all_config()
{
	$res = get_all('op_config');
	foreach ($res['data'] as $data) {
		$vardata = array($data['option_name'] => $data['option_value']);
		extract($vardata);
	}
}

function create_log($arMsg)
{
	global $user_name;
	global $base_url;
	//define empty string                                 
	$stEntry = "";
	//get the event occur date time,when it will happened  
	$arLogData['event_datetime'] = '[' . date('D Y-m-d h:i:s A') . '] [client ' . $_SERVER['REMOTE_ADDR'] . ']';
	//if message is array type  
	if (is_array($arMsg)) {
		//concatenate msg with datetime  
		foreach ($arMsg as $msg)
			$stEntry .= $arLogData['event_datetime'] . " by " . $user_name . " " . $msg . "\r\n";
		$notice = $msg . " by " . $user_name;
	} else {   //concatenate msg with datetime  

		$stEntry .= $arLogData['event_datetime'] . " by " . $user_name . " " . $arMsg . "\r\n";
		$notice = $arMsg . " by " . $user_name;
	}
	//create file with current date name  
	$stCurLogFileName = 'logs/log_' . date('Ymd') . '.txt';
	//open the file append mode,dats the log file will create day wise  
	$fHandler = fopen($stCurLogFileName, 'a+');
	//write the info into the file  
	fwrite($fHandler, $stEntry);
	//close handler  
	fclose($fHandler);
}

//DATE DIFFERENCE FUNCTION

function date_difference($startDate, $endDate)
{
	$diff = strtotime($startDate) - strtotime($endDate);
	return ceil(abs($diff / 86400));
}

function wa_text($wa_list, $wa_sms)
{
	global $wa_api_key;
	global $wa_sender;
	$wa_arr = explode(",", $wa_list);

	foreach ($wa_arr as $mobile) {
		$data = [
			'api_key' => $wa_api_key,
			'sender' => $wa_sender,
			'number' => "91" . $mobile,
			'message' => $wa_sms
		];

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://wp.biharapp.com/send-message",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
		));
		//echo $curl;
		$response = curl_exec($curl);
		curl_close($curl);
		create_log($curl);
		echo $response;
	}
}

// Permission & Role // 
function check_role($table_name, $user_id, $role_name = 'can_view')
{
	$result['data'] = '<input type="checkbox" class="update_role" value="add" data-table="' . $table_name . '"  data-id="' . $user_id . '" data-role="' . $role_name . '">';
	$result['status'] = 'no';
	$row = get_data('meta_table', $table_name, null, 'table_name')['data'];
	$data = $row[$role_name];
	$user_arr = explode(',', $data);
	if (in_array($user_id, $user_arr)) {
		$result['data'] = '<input type="checkbox" class="update_role" value="remove" data-table="' . $table_name . '"  data-id="' . $user_id . '" data-role="' . $role_name . '" checked>';
		$result['status'] = 'yes';
	}
	return $result;
}

function add_role($table_name, $user_id, $role_name = 'can_view')
{

	$row = get_data('meta_table', $table_name, null, 'table_name')['data'];
	$old_list = $row[$role_name];
	if ($old_list != '') {
		// $user_arr = explode(',',$old_list);
		// array_push($user_arr, $user_id);
		// $new_list = implode(',',$user_arr);
		// $new_list =  array_unique($new_list);
		$new_list = $old_list . "," . $user_id;
	} else {
		$new_list = $user_id;
	}
	//echo $new_list;
	$res = update_data('meta_table', array($role_name => $new_list), $table_name, 'table_name');
	//print_r($res);
	return $res;
}

function remove_role($table_name, $user_id, $role_name = 'can_view')
{
	$old_list = get_data('meta_table', $table_name, $role_name, 'table_name')['data'];
	$parts = explode(',', $old_list);
	while (($i = array_search($user_id, $parts)) !== false) {
		unset($parts[$i]);
	}
	if (count($parts) == 0) {
		$new_list = 0;
	} else {
		$new_list = implode(',', $parts);
	}
	// echo $new_list =  implode(',', $parts);
	$res = update_data('meta_table', array($role_name => $new_list), $table_name, 'table_name');
	//print_r($res);
	return $res;
}

// Time Managment  //

function format_interval(DateInterval $interval)
{
	$result = "";
	if ($interval->y) {
		$result .= $interval->format("%yy ");
	}
	if ($interval->m and $interval->y < 1) {
		$result .= $interval->format("%mmonth ");
	}
	if ($interval->d and $interval->m < 1) {
		$result .= $interval->format("%dd ");
	}
	if ($interval->h and $interval->d < 1) {
		$result .= $interval->format("%hh ");
	}
	if ($interval->i and $interval->h < 1) {
		$result .= $interval->format("%im ");
	}
	if ($interval->s and $interval->i < 1) {
		$result .= $interval->format("%ss");
	}

	return $result;
}

function time_gap($action_time)
{
	$cdate = date('Y-m-d H:i:s');
	$first_date = new DateTime($action_time);
	$second_date = new DateTime($cdate);
	$difference = $first_date->diff($second_date);
	return format_interval($difference);
}

function amount_in_word(float $number)
{
	$decimal = round($number - ($no = floor($number)), 2) * 100;
	$hundred = null;
	$digits_length = strlen($no);
	$i = 0;
	$str = array();
	$words = array(
		0 => '',
		1 => 'one',
		2 => 'two',
		3 => 'three',
		4 => 'four',
		5 => 'five',
		6 => 'six',
		7 => 'seven',
		8 => 'eight',
		9 => 'nine',
		10 => 'ten',
		11 => 'eleven',
		12 => 'twelve',
		13 => 'thirteen',
		14 => 'fourteen',
		15 => 'fifteen',
		16 => 'sixteen',
		17 => 'seventeen',
		18 => 'eighteen',
		19 => 'nineteen',
		20 => 'twenty',
		30 => 'thirty',
		40 => 'forty',
		50 => 'fifty',
		60 => 'sixty',
		70 => 'seventy',
		80 => 'eighty',
		90 => 'ninety'
	);
	$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
	while ($i < $digits_length) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += $divider == 10 ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
		} else
			$str[] = null;
	}
	$Rupees = implode('', array_reverse($str));
	$paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	$netamt = ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
	return ucwords($netamt);
}
?>