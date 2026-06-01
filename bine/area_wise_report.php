<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$filter['student_type']  = 'TRANSPORT';
if (isset($_GET['trip_id']) and $_GET['trip_id'] != '') {
	$filter['trip_id'] = $_GET['trip_id'];
}
if (isset($_GET['area_id']) and $_GET['area_id'] != '') {
	$filter['area_id'] = $_GET['area_id'];
}
$res = get_all('student', '*', $filter);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Trip Management</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Transport</a></li>
			<li class="breadcrumb-item active">Trip Management</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Trip and Area Wise Student Details</h3>
						<div class="box-tools pull-right">
							<form>
								<select name='area_id'>
									<option value=''> Select Area </option>
									<?php dropdown_list('transport_area', 'id', 'area_name', $area_id); ?>
								</select>
								<select name='trip_id'>
									<option value=''> Select Trip </option>
									<?php dropdown_list('trip_details', 'id', 'trip_name', $trip_id); ?>
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
										<th>Addres</th>
										<th>Bus Stop o</th>
										<th>Bus Stop</th>
										<th>Fare</th>
										<th>Bus No/ Trip</th>
										<th>Student Type </th>
										<th>Mobile No </th>
										<th>Action</th>

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
											echo "<td>" . $row['student_address1'] . "</td>";
											echo "<td>" . $row['bus_stop'] . $row['transport_fee'] . "</td>";

											echo "<td>" . get_data('transport_area', $row['area_id'], 'area_name')['data'] . "</td>";
											echo "<td>" . get_data('transport_area', $row['area_id'], 'area_fee')['data'] . "</td>";
											echo "<td>" . get_data('trip_details', $row['trip_id'], 'trip_name')['data'] . "</td>";
											echo "<td>" . $row['student_type'] . "</td>";
											echo "<td>" . $row['student_mobile'] . "</td>";
											echo "<td>" . btn_edit('add_student', $id) . "</td>";
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