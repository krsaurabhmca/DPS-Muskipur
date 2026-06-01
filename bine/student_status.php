<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
if (isset($_GET['status']) and $_GET['student_type'] == '') {
	$status = $_GET['status'];
	$res = get_all('student', '*', array('status' => $status));
} 
else if (isset($_GET['student_class']) and $_GET['student_section'] == '') {
	$res = get_all('student', '*', array('status' => 'ACTIVE', 'student_class' => $student_class));
} 
else if (isset($_GET['student_section']) != ''  and $_GET['student_class'] != '') {
	$res = get_all('student', '*', array('status' => 'ACTIVE', 'student_section' => $student_section, 'student_class' => $student_class));
}
else if (isset($_GET['student_type']) and $_GET['status'] == '') {
	$student_type = $_GET['student_type'];
	$res = get_all('student', '*', array('student_type' => $student_type));
} else if (isset($_GET['status']) and $_GET['student_type'] != '') {
	$student_type = $_GET['student_type'];
	$status = $_GET['status'];
	$res = get_all('student', '*', array('status' => $status, 'student_type' => $student_type));
} 

else {
	$res = get_all('student');
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Student Report</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Student</a></li>
			<li class="breadcrumb-item active">Student Report</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title"> Student Details ( <?php echo $status . " " . $student_type; ?> )</h3>
						<div class="box-tools pull-right">
							<form>
								<select name='status'>
									<?php dropdown($status_list, $status); ?>
								</select>
								<select name='student_type'>
									<?php dropdown($student_type_list, $student_type); ?>
								</select>
								<button class='btn btn-orange'> Show </button>
							</form>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
								<thead>
									<tr>
										<th>Adm No.</th>
										<th>Class </th>
										<th>Roll No.</th>
										<th>Student Name</th>
										<th>Father Name</th>
										<th>Mother Name</th>
										<th>DOB</th>
										<th>Addres</th>
										<th>Bus Stop</th>
										<th>Fare</th>
										<th>Bus No/ Trip</th>
										<th>Student Type </th>
										<th>Mobile No </th>
									</tr>
								</thead>
								<tbody>
									<?php
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$status = $row['status'];
											echo "<tr class='odd gradeX'>";

											echo "<td>" . $row['student_admission'] . "</td>";
											echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
											echo "<td>" . $row['student_roll'] . "</td>";
											echo "<td>" . $row['student_name'] . "</td>";
											echo "<td>" . $row['student_father'] . "</td>";
												echo "<td>" . $row['student_mother'] . "</td>";
													echo "<td>" . date('d-M-Y',strtotime($row['date_of_birth'])) . "</td>";
											echo "<td>" . $row['student_address1'] . "</td>";
											echo "<td>" . get_data('transport_area', $row['area_id'], 'area_name')['data'] . "</td>";
											echo "<td>" . get_data('transport_area', $row['area_id'], 'area_fee')['data'] . "</td>";
											echo "<td>" . get_data('trip_details', $row['trip_id'], 'trip_name')['data'] . "</td>";
											echo "<td>" . $row['student_type'] . "</td>";
											echo "<td>" . $row['student_mobile'] . "<br>" . $row['father_mobile'] . "</td>";
											echo "</tr>";
										}
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>