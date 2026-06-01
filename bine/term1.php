<?php
require_once('required/function.php');
// echo "<pre>";
// print_r($_POST);
extract($_POST);

// // echo $student_admission;
// // if($student_admission !='')
// // {
// // $student_list = implode(',', $_POST['student_admission']);
// // }
// // else{
$student_type = "'" . implode("','", $student_type) . "'";
$sql = "select student_admission from student where student_class ='$student_class'  and student_section = '$student_section' and student_type in ($student_type) and status='ACTIVE'";
$res = direct_sql($sql);
foreach ($res['data'] as $row) {
	$student_list[] = $row['student_admission'];
}
// //}

foreach ($student_list as $student) {
	$student = get_data('student', $student, null, 'student_admission')['data'];
	$sid = $student['id'];
	$gtotal = 0;
?>
	<link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow&display=swap" rel="stylesheet">
	<style>
		body {
			/*font-family: 'PT Sans Narrow', sans-serif;*/
			font-family: 'Lato', sans-serif;
		}

		.h2 {
			display: inline;
			font-family: arial black;
			font-size: 20px;
			margin: 0px;
			padding: 0px;
		}

		td {
			padding: 2px;
			font-size: 11px;
		}

		p {
			font-size: 10px;
		}

		.data {
			color: maroon;
			font-family: time new roman;
		}

		.photo {
			border-radius: 50%;
		}

		.reportcard {
			margin-bottom: 10px;
		}

		@media print {
			#printbtn {
				display: none;
			}

			.reportcard {
				page-break-inside: avoid;
			}

			@page {
				size: portrait;
			}
		}
	</style>
	<table align='center' rules='none' border='1' width='780px' class='reportcard'>
		<tr>
			<td align='center' colspan='7'>
				<table>
					<td align='center'>
						<img src='images/cbse.png' height='80px'><br>
						<div style='border:solid 1px #ddd;border-radius:5px;'>Affiliation No. <?php echo $aff_no; ?></div>
					</td>
					<td align='center' width='600px' style='letter-spacing:1px'>
						<div class='h2'> <?php echo $full_name; ?> </div><br>
						(Affiliated to CBSE, New Delhi upto 10+2) <br>
						<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
						Contact No.: <?php echo $inst_contact; ?><br> Email: <?php echo $inst_email; ?>, Website: <?php echo $inst_url; ?>
					</td>
					<td align='center'>
						<img src='images/logo.png' height='80px'><br>
						<div style='border:solid 1px #ddd;border-radius:5px;'>School Code <?php echo $school_code; ?></div>
					</td>
				</table>
		</tr>
		<tr>
			<td colspan='7' align='center' style='background:#8c0023;color:#fff;height:30px;'>
				Record of Academic Performance <?php echo $current_session; ?>
			</td>
		</tr>
		<tr>
			<td colspan='7' align='center'>
				<b><?php echo $student['student_name']; ?> </b>
			</td>
		</tr>
		<tr>
			<td colspan='7'>
				<table rules='none' width='100%' border='0'>
					<td colspan='4'> Admission No.: <?php echo $student['student_admission']; ?></td>
					<td rowspan='3' align='center'> <img src='upload/<?php echo $student['student_photo']; ?>' width='60' height='60' class='photo'></td>
					<td align='left' colspan='3'>Father's Name : <?php echo $student['student_father']; ?></td>
		</tr>
		<tr>
			<td colspan='4'> Class : <?php echo $student['student_class']; ?>-<?php echo $student['student_section']; ?></td>
			<td align='left' colspan='3'>Mother's Name : <?php echo $student['student_mother']; ?></td>
		</tr>
		<tr>
			<td colspan='4'> Roll No. : <?php echo $student['student_roll']; ?></td>
			<td align='left' colspan='3'>Date of Birth : <?php echo date('d-M-Y', strtotime($student['date_of_birth'])); ?></td>
		</tr>
	</table>
	</td>
	<tr>
	<tr>
		<td colspan='7' align='center' style='background:#8c0023;color:#fff;height:30px;'>
			SCHOLASTIC AREAS
		</td>
	</tr>
	<tr>
		<td colspan='7' align='center' height='180px' valign='top'>
			<table rules='all' width='100%' border='0' bordercolor='#c5c5c5'>
				<tr align='center'>

					<th colspan='7'> Term 1 (100 Marks) </th>
				</tr>
				<tr>
					<th> Subject </th>
					<th> PT1 (10)</th>
					<th> NB (5)</th>
					<th> SE(5) </th>
					<th> HY (80)</th>
					<th> Marks Obtained (100)</th>
					<th> Grade</th>
				</tr>

				<?php
				$sub_list = subject_list($student['student_class']); //.'_subject';
				//$extra_list =extra_list($st_class); //.'_extra';
				//$subject_list = array_diff($sub_list,$extra_list);
				//print_r($extra_list);
				//$graph[] =array('Subject','Marks');
				foreach ($sub_list as $subject_id) {
					$garr = array();
					$subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
					$subject_col = get_data('subject', $subject_id, 'subject_column')['data'];
					$marks  = get_marks($student['student_admission'], $exam_name, $subject_col);
					$total = $marks['pt'] + $marks['nb'] + $marks['se'] + $marks['mo'];
					$gtotal = $gtotal + $total;
				?>
					<tr align='center'>
						<td align='left'> <?php echo $subject_name; ?> </td>
						<td> <?php echo $marks['pt']; ?> </td>
						<td> <?php echo $marks['nb']; ?> </td>
						<td> <?php echo $marks['se']; ?> </td>
						<td> <?php echo $marks['mo']; ?> </td>
						<td> <?php echo $total; ?> </td>
						<td> <?php echo grade($total); ?> </td>
					</tr>
				<?php } ?>
			</table>

		</td>
	</tr>
	<tr>
		<td colspan='6' align=='center'>

			<b>Instruction <br> Grading Scales for scholastic areas : Grades are awarded on a 9 -point grading scale as follows:- </b><br>
			A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32) <br>
			PT-Periodic Test | NB - Note Book | SEA - Subject Enrichment Activity | HY - Half Yearly
		</td>
		<td rowspan='2' align='center'>
			<img src='images/sign.jpg' height='60px'><br>
			<b>Principal <br> Seal </b>
		</td>
	</tr>
	<tr align='center' valign='middle' height='60px'>
		<th colspan='3' align='left'>
			<small>Date: <?php echo date('d-M-Y'); ?></small>
		</th>
		<th colspan='3' align='left'>
			<small>Sign. of Class Teacher</small>
		</th>
		</th>
	</tr>
	<!--<tr> 
	<td colspan='6'>
	<p align='center' ><b>Instruction:</b>  Grading Scales for schlostic areas : Grades are awarded on a 9 -point grading scale as follows </p> 
	<table rules='all' align='center' border='1' cellpadding='5px'>
	<tr><td> Marks Range </td><td> Grade </td> </tr>
	<tr><td> 91-100 </td><td> A1 </td> </tr>
	<tr><td> 81-90 </td><td> A2 </td> </tr>
	<tr><td> 71-80 </td><td> B1 </td> </tr>
	<tr><td> 61-70 </td><td> B2 </td> </tr>
	<tr><td> 51-60 </td><td> C1 </td> </tr>
	<tr><td> 41-50 </td><td> C2 </td> </tr>
	<tr><td> 33-40 </td><td> D </td> </tr>
	<tr><td> 32 & Below </td><td> E (Need Improvement) </td></tr>
	</table>
	</td>
 </tr>-->
	</table>

<?php } ?>