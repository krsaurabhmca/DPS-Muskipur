<?php
require_once('required/function.php');
$param = decode($_GET['link']);
$table = $param['table'];
$id = $param['id'];

$res = get_data($table, $id);
if ($res['status'] == 'success') {
	$data = $res['data'];
	unset($data['id']);
	unset($data['created_by']);
	unset($data['created_at']);
	unset($data['updated_at']);
	unset($data['updated_by']);
	unset($data['category']);
	unset($data['publisher_name']);
}
?>

<style>
	/*body {*/
	/*	-webkit-user-select: none;*/
	/*	-moz-user-select: -moz-none;*/
	/*	-ms-user-select: none;*/
	/*	user-select: none;*/
	/*}*/

	/*@media print {*/
	/*	body {*/
	/*		display: none;*/
	/*	}*/
	/*}*/
</style>
<script>
	document.addEventListener('contextmenu', event => event.preventDefault());
</script>
<div class="content p-2 bg-light ">

	<?php
	$info = "<div class='table-responsive-sm'><table width='100%' cellpadding='3' bordercolor='#ccc' rules='rows' border='0'>";
	foreach ($data as $key => $value) {
		if ($key == 'photo') {
			$display_key = add_space($key);
			$display_val = "<img src='upload/" . $value . "' width='100px' class='img-thumbnail'>";
		} else if ($key == 'student_id') {
			$display_key = 'Student Name';
			$display_val = get_data('student', $value, 'student_name')['data'];;
		}
		else if ($key == 'student_photo' and $table =='student') {
			$display_key = 'Student Photo';
			$display_val = "<img src='required/upload/$value' height='80px' width='60px' class='img-thumbnail'>";
		}
		else if ($key == 'student_photo' and $table =='admission') {
			$display_key = 'Applicant Photo';
			$display_val = "<img src='upload/$value' height='80px' width='60px' class='img-thumbnail'>";
		}
		else if ($key == 'area_id') {
			$display_key = 'Area Name';
			$display_val = get_data('transport_area', $value, 'area_name')['data'];;
		} else if ($key == 'cat_id') {
			$display_key = 'Category Name';
			$display_val = get_data('book_cat', $value, 'cat_name')['data'];;
		} else if ($key == 'pub_id') {
			$display_key = 'Publisher Name';
			$display_val = get_data('book_pub', $value, 'pub_name')['data'];;
		} else if ($key == 'issue_by') {
			$display_key = 'Issue By';
			$display_val = get_data('user', $value, 'full_name')['data'];;
		} else if (strpos($key, 'date') && $value != null) {
			$display_key = add_space($key);
			$display_val = date('d-M-Y', strtotime($value));
		} else if ($key == 'valid_till') {
			$display_key = 'Valid Till';
			$display_val = date('d-M-Y', strtotime($value));
		} else if ($key == 'pollution_expiry') {
			$display_key = 'Pollution Expiry Date';
			$display_val = date('d-M-Y', strtotime($value));
		} else if ($key == 'insurance_expiry') {
			$display_key = 'Insurance Expiry Date';
			$display_val = date('d-M-Y', strtotime($value));
		} else if ($key == 'road_tax_expiry') {
			$display_key = 'Road Tax Expiry Date';
			$display_val = date('d-M-Y', strtotime($value));
		} else if ($key == 'fitness_expiry') {
			$display_key = 'Fitness Expiry Date';
			$display_val = date('d-M-Y', strtotime($value));
		} else if ($key == 'emi_start_date') {
			$display_key = 'EMI Start Date';
			$display_val = date('d-M-Y', strtotime($value));
		} else if ($key == 'driver_id') {
			$display_key = 'Driver Name';
			$display_val = get_data('employee', $value, 'e_name')['data'];
		} else {
			$display_key = add_space($key);
			$display_val = wordwrap($value, 55, '<br>', true);
		}

		$info = $info . "<tr><td><b>" . $display_key . "</b></td><td>:</td><td>" . $display_val . "</td></tr>";
	}
	$info = $info . "</table></div>";

	echo $info;
	?>

</div>