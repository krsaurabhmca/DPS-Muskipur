<?php
require_once('required/function.php');
extract($_POST);

$student_type = "'" . implode("','", $student_type) . "'";
$sql = "select student_admission from student where student_class ='$student_class'  and student_section = '$student_section' and student_type in ($student_type) and status='ACTIVE'";
$res = direct_sql($sql);
foreach ($res['data'] as $row) {
	$student_list[] = $row['student_admission'];
}
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
			font-size: 24px;
			margin: 0px;
			padding: 0px;
		}

		td {
			padding: 2px;
		}

		p {
			font-size: 14px;
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
						<img src='images/cbse.png' height='100px'><br>
						<div style='border:solid 1px #ddd;border-radius:5px;'>Affiliation No. <?php echo $aff_no; ?></div>
					</td>
					<td align='center' width='600px' style='letter-spacing:1px'>
						<div class='h2'> <?php echo $full_name; ?> </div><br>
						(Affiliated to CBSE, New Delhi upto 10+2) <br>
						<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
						Contact No.: <?php echo $inst_contact; ?><br> Email: <?php echo $inst_email; ?>, Website: <?php echo $inst_url; ?>
					</td>
					<td align='center'>
						<img src='images/logo.png' height='100px'><br>
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
		<td colspan='7' align='center' valign='top' height='300px'>
			<table rules='all' width='100%' border='0' bordercolor='#c5c5c5' height='200px' align='center'>
				<tr align='center'>

					<th colspan='7'> Term 1 (100 Marks) </th>
					<th colspan='6'> Term 2 (100 Marks) </th>
				</tr>
				<tr>
					<th> Subject </th>
					<th> PT1 (10)</th>
					<th> NB (5)</th>
					<th> SE (5) </th>
					<th> HY (80)</th>
					<th> Marks Obtained (100)</th>
					<th> Grade</th>
					<th> PT2 (10)</th>
					<th> NB (5)</th>
					<th> SE (5) </th>
					<th> HY (80)</th>
					<th> Marks Obtained (100)</th>
					<th> Grade</th>
				</tr>

				<?php
				$sub_list = subject_list($student['student_class']); //.'_subject';
				foreach ($sub_list as $subject_id) {
					$garr = array();
					$subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
					$subject_col = get_data('subject', $subject_id, 'subject_column')['data'];
					$marks  = get_marks($student['student_admission'], 'term1', $subject_col);
					$total = $marks['pt'] + $marks['nb'] + $marks['se'] + $marks['mo'];
					$marks2  = get_marks($student['student_admission'], 'term2', $subject_col);
					$total2 = $marks2['pt'] + $marks2['nb'] + $marks2['se'] + $marks2['mo'];

				?>
					<tr>
						<td align='left'> <?php echo $subject_name; ?> </td>
						<td align='center'> <?php echo $marks['pt']; ?> </td>
						<td align='center'> <?php echo $marks['nb']; ?> </td>
						<td align='center'> <?php echo $marks['se']; ?> </td>
						<td align='center'> <?php echo $marks['mo']; ?> </td>
						<td align='center'> <?php echo $total; ?> </td>
						<td align='center'> <?php echo grade($total); ?> </td>
						<td align='center'> <?php echo $marks2['pt']; ?> </td>
						<td align='center'> <?php echo $marks2['nb']; ?> </td>
						<td align='center'> <?php echo $marks2['se']; ?> </td>
						<td align='center'> <?php echo $marks2['mo']; ?> </td>
						<td align='center'> <?php echo $total2; ?> </td>
						<td align='center'> <?php echo grade($total2); ?> </td>
					</tr>
				<?php } ?>

			</table>
		</td>
	</tr>

	<tr align='center' valign='bottom' height='100px'>
		<th colspan='2' align='center'>
			<small>Date: <?php echo date('d-M-Y'); ?></small>
		</th>
		<th colspan='3' align='left'>
			<small>Sign. of Class Teacher</small>
		</th>
		<th colspan='2' align='center'>
			<img src='images/sign.jpg' height='60px'><br>
			<small>Principal <br> Seal </small>
		</th>
	</tr>
	<tr height='60px'>
		<td colspan='7'></td>
	</tr>
	<tr>
		<td colspan='7'>
			<p align='center'><b>Instruction:</b> Grading Scales for scholastic areas : Grades are awarded on a 9 -point grading scale as follows:- </p>
			<table rules='all' align='center' border='1' cellpadding='5px'>
				<tr>
					<td align='center'> Marks Range </td>
					<td align='center'> Grade </td>
				</tr>
				<tr>
					<td align='center'> 91-100 </td>
					<td align='center'> A1 </td>
				</tr>
				<tr>
					<td align='center'> 81-90 </td>
					<td align='center'> A2 </td>
				</tr>
				<tr>
					<td align='center'> 71-80 </td>
					<td align='center'> B1 </td>
				</tr>
				<tr>
					<td align='center'> 61-70 </td>
					<td align='center'> B2 </td>
				</tr>
				<tr>
					<td align='center'> 51-60 </td>
					<td align='center'> C1 </td>
				</tr>
				<tr>
					<td align='center'> 41-50 </td>
					<td align='center'> C2 </td>
				</tr>
				<tr>
					<td align='center'> 33-40 </td>
					<td align='center'> D </td>
				</tr>
				<tr>
					<td align='center'> 32 & Below </td>
					<td align='center'> E (Need Improvement) </td>
				</tr>
			</table>
		</td>
	</tr>
	</table>

<?php } ?>