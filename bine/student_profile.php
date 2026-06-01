<?php
require_once('required/function.php');
$data = decode($_GET['link']);

$student_id = $data['id'];
$res = get_data('student', $student_id);

if ($res['count'] > 0) {
	extract($res['data']);
?>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap');

		body {
			font-family: 'Roboto', sans-serif;
		}
	</style>

	<table rules='rows' width='950px' align='center' border='1' cellpadding='6'>

		<tr>
			<td> <img src='images/logo.png' height='100px'> </td>
			<td colspan='4' align='center'>
				<span style='font-size:28px;font-weight:600;font-family:times new roman;'> <?php echo $full_name; ?> </span><br>
				<b>(Affiliated to CBSE, New Delhi upto 10+2)
					Affiliation No. : <?php echo $aff_no; ?> School Code : <?php echo $school_code; ?></b><br>
				<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
				Contact No.: <?php echo $inst_contact; ?><br>
				Email : <?php echo $inst_email; ?>, Website : <?php echo $inst_url; ?>
			</td>
		</tr>


		<tr bgcolor='#f9f9f9'>
			<td colspan='2'> Student Name </td>
			<td colspan='2'> <?php echo $student_name; ?> </td>
			<td rowspan='6' align='center' width='180px'><img src='required/upload/<?php echo $student_photo; ?>' height='160px'> </td>
		</tr>

		<tr>
			<td colspan='2'> Class </td>
			<td colspan='2'> <?php echo $student_class; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Section </td>
			<td colspan='2'> <?php echo $student_section; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Roll No. </td>
			<td colspan='2'> <?php echo $student_roll; ?> </td>

		</tr>
		<tr>
			<td colspan='2'> Admission No. </td>
			<td colspan='2'> <?php echo $student_admission; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> CBSE Reg. No. </td>
			<td colspan='2'> <?php echo $cbse_reg_no; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Student Type </td>
			<td colspan='3'> <?php echo $student_type; ?></td>
		</tr>

		<tr>
			<td colspan='2'> Finance Type </td>
			<td colspan='3'> <?php echo $finance_type; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Admission Type</td>
			<td colspan='3'> <?php echo $admission_type; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Date of Birth </td>
			<td colspan='3'> <?php echo date('d-M-Y', strtotime($date_of_birth)); ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Gender </td>
			<td colspan='3'> <?php echo $student_sex; ?> </td>
		</tr>
		<tr>
			<th colspan='5' bgcolor='#f4f4f4'> Family Information </th>
		</tr>
		<tr>
			<td> Father's Name </td>
			<td> <?php echo $student_father; ?> </td>
			<td></td>
			<td> Mother's Name </td>
			<td> <?php echo $student_mother; ?> </td>
		</tr>

		<tr>
			<td> Father's Qualification </td>
			<td> <?php echo $father_qualification; ?> </td>
			<td></td>
			<td> Mother's Qualification </td>
			<td> <?php echo $mother_qualification; ?> </td>
		</tr>
		<tr>
			<td> Father's Occupation </td>
			<td> <?php echo $father_occupation; ?> </td>
			<td></td>
			<td> Mother's Occupation </td>
			<td> <?php echo $mother_occupation; ?> </td>
		</tr>
		<tr>
			<td> Father's Mobile No. </td>
			<td> <?php echo $father_mobile; ?> </td>
			<td></td>
			<td> Mother's Mobile No. </td>
			<td> <?php echo $mother_mobile; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Family Income </td>
			<td colspan='3'> <?php echo $family_income; ?> </td>
		</tr>

		<tr>
			<th colspan='5' bgcolor='#f4f4f4'> Basic Details </th>
		</tr>

		<tr>
			<td> Religion </td>
			<td colspan='2'> <?php echo $student_religion; ?> </td>
			<td> Caste </td>
			<td> <?php echo $student_caste; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Present Address </td>
			<td colspan='3'> <?php echo $student_address1; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Permanent Address </td>
			<td colspan='3'> <?php echo $student_address2;  ?> </td>
		</tr>
		<tr>
			<td colspan='2'> District </td>
			<td colspan='3'> <?php echo get_data('district', $district_code, 'name', 'code')['data']; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> State </td>
			<td colspan='3'> <?php echo get_data('state', $state_code, 'name', 'code')['data']; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Pin Code </td>
			<td colspan='3'> <?php echo $pin_code; ?> </td>
		</tr>
		<tr>
			<td> Bus Stop </td>
			<td colspan='2'> <?php echo get_data('transport_area', $area_id, 'area_name')['data']; ?></td>
			<td> Trip </td>
			<td> <?php echo get_data('trip_details', $trip_id, 'trip_name')['data']; ?></td>
		</tr>
		<tr>
			<th colspan='5' bgcolor='#f4f4f4'> Contact Details </th>
		</tr>


		<tr>
			<td colspan='2'> Mobile No. </td>
			<td colspan='3'> <?php echo $student_mobile; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Whatsapp </td>
			<td colspan='3'> <?php echo $student_whatsapp; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Email </td>
			<td colspan='3'> <?php echo $student_email; ?> </td>
		</tr>

		<tr>
			<td colspan='5'> <input type='button' onclick='window.print()' value='Print'>
		</tr>
	</table>
<?php
}
?>