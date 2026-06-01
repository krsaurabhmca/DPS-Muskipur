<?php require_once('required/header.php');
require_once('required/menu.php');
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

?>
<script src="https://cdn.jsdelivr.net/gh/linways/table-to-excel@v1.0.4/dist/tableToExcel.js"></script>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> New Consolidated Grand Total</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#exam">Exam</a></li>
			<li class="breadcrumb-item active">Consolidated Marks</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Consolidated Grand Total</h3>
				<div class="box-tools pull-right">
					<!--<i class='fa fa-file-excel-o btn btn-orange text-light btn-sm' onclick='exportxls()' title='Export to Excel'> </i>-->
					<i class='fa fa-file-excel-o btn btn-orange text-light btn-sm' id='btnExport' title='Export to Excel'> </i>

				</div>
			</div>
			<div class="box-body">
				<form action='' method='post'>
					<div class='row'>

						<div class="col-lg-3 col-offset-lg-2">
							<div class="form-group">
								<label>Select Class</label>
								<select class="form-control" name='student_class' required onChange='selsub(this.value)'>
									<?php dropdown($class_list, $student_class); ?>
								</select>
							</div>
						</div>
						<div class="col-lg-3">

							<div class="form-group">
								<label>Select Section</label>
								<select class="form-control" name='student_section' required>
									<?php dropdown($section_list, $student_section); ?>
								</select>
							</div>

						</div>
						<div class="col-lg-3">

							<div class="form-group">
								<label>Select Exam</label>
								<select class="form-control" name='exam_name' required>
									<?php dropdown($exam_list, $exam_name); ?>
								</select>
							</div>

						</div>
						<div class="col-lg-3">
							<label>&nbsp; </label>
							<input type="submit" class="btn btn-primary btn-block" value='Show Details'>
						</div>
				</form>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<!-- Advanced Tables -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<?php
							if (isset($_REQUEST['exam_name']) and isset($_REQUEST['student_class'])) {
								//extract($_POST);
								$subject_list = subject_list($student_class);
								$sl = 1;
								$cols = 3 + (count($subject_list) * $sl);
							?>

						</div>
						<div class='table-responsive'>
							<!--<table id="data_tbl" class="table table-bordered table-striped">-->
							    <table id="example" class="table table-bordered table-striped">
								<thead>
								
									<tr bgcolor='lightyellow' align='center'>
										<th  valign='middle'> Roll No. </th>
										<th> Name </th>
										<th> Class Section </th>
										<?php foreach ($subject_list as $subject) {
											echo "<td colspan='1' align='center'><b>" . get_data('subject', $subject, 'subject_name')['data'] . "</b></td>";
										}
										?>

										<th> Total </th>
									</tr>
									
								</thead>

								<tbody>
									<?php

									$sql = "SELECT * FROM exam, student WHERE student.student_admission = exam.student_admission and  student.status ='ACTIVE' and student.student_class='$student_class' and student.student_section='$student_section' and exam_name ='$exam_name' order by student.student_roll";
									$subject = remove_space($subject);
									$res = mysqli_query($con, $sql);
									while ($row = mysqli_fetch_array($res)) {
										$total = 0;

										$id = $row['id'];
										$student_id = $row['id'];
										$student_admission = $row['student_admission'];
										
										//print_r($marks);


										echo "<tr class='default'>";

										echo "<td>" . $row['student_roll'] . "</td>";
										echo "<td>" . $row['student_name'] . "</td>";
										echo "<td>" . $row['student_class'] ."-". $row['student_section'] . "</td>";
										foreach ($subject_list as $subject_id) {
											$subject = get_data('subject', $subject_id, 'subject_column')['data'];
											$subtotal = 0;
											// $percentile = get_percentile($student_admission,'PT1', $subject);
											$marks = get_marks($student_admission, $exam_name, $subject);
											$PT1marks = get_marks($student_admission, "PT1", $subject);
											//print_r($marks);
											$subtotal = $marks['pt'] + $marks['nb'] + $marks['se'] + $marks['mo'] + ($PT1marks['total']*2/10);
											$total = $total + $subtotal;
											//echo "<td bgcolor='lightyellow' >".$percentile."</td>";
										//	echo "<td>" . $marks['pt'] . "</td>";
										//	echo "<td>" . $marks['nb'] . "</td>";
										//	echo "<td>" . $marks['se'] . "</td>";
										//	echo "<td>" . $marks['mo'] . "</td>";
											echo "<td>" . $subtotal . "</td>";
										}

										echo "<td>" . $total . "</td>";
										echo "</tr> ";
									}
									?>
								
								</tbody>
							</table>
						<?php } ?>

						</div>
					</div>


				</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>

<script>
    $(document).ready(function(){
    $("#btnExport").click(function() {
        let table = document.getElementsByTagName("table");
        TableToExcel.convert(table[0], { // html code may contain multiple tables so here we are refering to 1st table tag
           name: `export.xlsx`, // fileName you could use any name
           sheet: {
              name: 'Sheet 1' // sheetName
           }
        });
    });
    });
</script>