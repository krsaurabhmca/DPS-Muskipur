<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Report Card </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Exam</a></li>
			<li class="breadcrumb-item active">Report Card</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title"> Exam Result Details </h3>

				<div class="box-tools pull-right">
					<form action='report_card.php' action='get'>
						Filter By
						<select name="student_class">
							<?php dropdown($class_list, $student_class) ?>
						</select>
						<select name="student_section" onchange='submit()' accesskey='l'>
							<?php dropdown($section_list, $student_section) ?>
						</select>
					</form>
				</div>
			</div>
			<div class="box-body">

				<div class="table-responsive">
					<form action='midterm_print.php' method='post' target='icard'>
						<table id="example" class="table table-bordered table-striped">
							<thead>
								<tr>

									<th>Adm. No.</th>
									<th>Student Name</th>
									<th>Father's Name</th>
									<th>Date of Birth</th>
									<th>Type </th>
									<th>Class/Sec </th>
									<th>Roll</th>
									<th>Mobile</th>

								</tr>
							</thead>
							<tbody>

								<?php
								//$sql ="select * from student where student_photo <> 'no_image.jpg'";
								if (isset($_GET['student_class'])) {
									$student_class = trim($_GET['student_class']);
									$student_section = trim($_GET['student_section']);
									$sql = "select * from student where student_class = '$student_class' and student_section = '$student_section' and status <>'BLOCK' order by student_roll ";
								} else {
									$sql = "select * from student where status <>'BLOCK'";
								}

								$res = mysqli_query($con, $sql) or die("Error in selecting Student" . mysqli_error($con));

								while ($row = mysqli_fetch_array($res)) {
									$stu_id = $row['id'];
									$status = $row['student_status'];
									echo "<tr class='odd gradeX'>";


									echo "<td>" . "</td>";
									//echo"<td><input type='checkbox' value ='$stu_id' name='sel_id[]'>".$row['student_admission']."</td>";
									echo "<td>" . $row['student_name'] . "</td>";
									echo "<td>" . $row['student_father'] . "</td>";
									if (date('d-M-y', strtotime($row['date_of_birth'])) <> '01-Jan-70') {
										echo "<td>" . date('d-M-y', strtotime($row['date_of_birth'])) . "</td>";
									} else {
										echo "<td></td>";
									}
									echo "<td>" . trim($row['student_type']) . "</td>";
									echo "<td>" . trim($row['student_class']) . '/' . trim($row['student_section']) . "</td>";
									echo "<td>" . trim($row['student_roll']) . "</td>";
									echo "<td>" . $row['student_mobile'] . "</td>";
								}
								?>
								</tr>
							</tbody>
							<!--<a href='print_id.php?student_id=$stu_id' title='Print I Card' >-->
							<tfoot>
								<tr>
									<td colspan='13'>
										<center>
											<input type='submit' class='btn btn-danger btn-xs' value='Print Report Card'>
										</center>
									</td>
								</tr>
							</tfoot>
						</table>

					</form>
				</div>


			</div>

		</div>
		<!-- end page-wrapper -->

</div>

<!-- end wrapper -->

<?php require_once('required/footer2.php'); ?>
<script language="JavaScript">
	function selectAll(source) {
		checkboxes = document.getElementsByName('sel_id[]');
		for (var i in checkboxes)
			checkboxes[i].checked = source.checked;
	}
</script>

</body>

</html>