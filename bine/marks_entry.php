<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$exam_name = $student_class = $student_section = $subject = null;
if (isset($_REQUEST['exam_name'])) {
	$exam_name = $_REQUEST['exam_name'];
}
if (isset($_REQUEST['student_class'])) {
	$student_class = $_REQUEST['student_class'];
}
if (isset($_REQUEST['student_section'])) {
	$student_section = $_REQUEST['student_section'];
}
if (isset($_REQUEST['subject_id'])) {
	$subject = $_REQUEST['subject_id'];
}
?>
<style>
	@media print {
		body * {
			visibility: hidden;
		}

		#tbl_a,
		#tbl_a * {
			visibility: visible;
		}

		#tbl_a {
			page-break-inside: avoid;
			position: absolute;
			left: 0;
			top: 20;
		}

		input[type='text'] {
			border: solid 1px #ddd;
		}

	}
</style>
<title> <?php echo $inst_name . " " . $student_class . " " . $student_roll; ?> </title>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Marks Entry </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Exam</a></li>
			<li class="breadcrumb-item active">Marks Entry</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Create Marks Entry Sheet (Download CSV)</h3>

				<div class="box-tools pull-right">
				</div>
			</div>
			<div class="box-body">
				<form action='' method='post'>
					<div class="row">
						<!--  page header -->
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Select Session</label>
								<select class="form-control" name='session_year' required>
									<?php dropdown($session_list, $current_session); ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Select Class</label>
								<select class="form-control" name='student_class' required onChange='selsub(this.value)'>
									<?php dropdown($class_list, $student_class); ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2">

							<div class="form-group">
								<label>Enter Section</label>
								<select class="form-control" name='student_section' required>
									<?php dropdown($section_list, $student_section); ?>
								</select>
							</div>

						</div>
						<div class="col-lg-2">

							<div class="form-group">
								<label>Select Exam</label>
								<select class="form-control" name='exam_name' required>
									<?php dropdown($exam_list, $exam_name); ?>
								</select>
							</div>

						</div>
						<div class="col-lg-2">

							<div class="form-group">
								<label>Select Subject</label>
								<select class="form-control" name='subject_id' required id='subject_list'>

								</select>
							</div>

						</div>

						<div class="col-lg-2">
							<label>&nbsp; </label>
							<input type="submit" class="btn btn-primary btn-block" value='Show Details'>
						</div>
				</form>
			</div>

			<div class="table-responsive">

				<?php
				if (isset($_REQUEST['exam_name']) and isset($_REQUEST['subject_id'])) {
					$subject_name = get_data('subject', $_REQUEST['subject_id'], 'subject_column')['data'];
				?>
					<table id="example" class="table table-bordered table-striped">
						<thead>


							<th> Exam Name </th>
							<th> Session Year</th>
							<th> Student Admission </th>
							<!--<th> Student Id </th>-->
							<th> Class </th>
							<th> Roll </th>
							<th> Name </th>
							<!--<th> <?php echo $subject_name; ?> PT</th>-->
							<th> <?php echo $subject_name; ?> NB</th>
							<th> <?php echo $subject_name; ?> SE</th>
							<th> <?php echo $subject_name; ?> MO</th>

							</tr>
							</th>

						<tbody>
							<?php
							$res = get_all('student', '*', array('student_class' => $student_class, 'student_section' => $student_section, 'status' => 'ACTIVE'));
							foreach ($res['data'] as $row) {
								$marks = get_marks($row['student_admission'], $exam_name, $subject_name);
								
								// Print_r($marks);
								
								echo "<tr><td>" . $_REQUEST['exam_name'] . "</td>";
								echo "<td>" . $_REQUEST['session_year'] . "</td>";
								echo "<td>" . $row['student_admission'] . "</td>";
								//echo "<td>".$row['id']."</td>";
								echo "<td>" . $row['student_class'] . '-' . $row['student_section'] . "</td>";
								echo "<td>" . $row['student_roll'] . "</td>";
								echo "<td>" . $row['student_name'] . "</td>";
								//	echo "<td>".$marks['pt']."</td>";
								echo "<td>" . $marks['nb'] . "</td>";
								echo "<td>" . $marks['se'] . "</td>";
								echo "<td>" . $marks['mo'] . "</td>";
								echo "</tr> ";
							}
							?>
							</tr>

						</tbody>
					</table>
				<?php } ?>
			</div>


		</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>
<script>
	function selsub(sel) {
		console.log(sel);
		$.ajax({
			type: 'post',
			data: {
				'class_name': sel
			},
			url: 'required/master_process?task=select_subject',
			success: function(data) {
				//console.log(data);
				$("#subject_list").html(data);
			}
		})
	}
</script>