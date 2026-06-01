<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
//print_r($_REQUEST);
extract($_REQUEST);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Route Wise Demand List </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Fee</a></li>
			<li class="breadcrumb-item active">Demand List</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Demand List</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">


				<div class="table-responsive">
					<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
						<thead>
							<th> Route </th>
							<th> Adm No. </th>
							<th> Class/Sec </th>
							<th> Roll No. </th>
							<th> Name </th>
							<th> Father's Name </th>
							<th> Mobile No. </th>
							<th> Till Month</th>
							<th> Prev. Dues </th>
							<th> Tuition Fee </th>
							<th> Transport Fee </th>
							<th> Hostel Fee </th>
							<th> Total </th>
							<th> Remarks</th>
						</thead>
						<tbody>
							<?php
							foreach ($area_id as $area) {
								$data = get_data('transport_area', $area, null, 'area_name')['data'];
								$id[] = $data['id'];
							}
							$area_id = "'" . implode("','", $id) . "'";
							$sql = "select * from student where area_id in($area_id) and status ='ACTIVE'";
							$all_data = direct_sql($sql);
							if ($all_data['count'] > 0)
								foreach ($all_data['data'] as $student) {
									$student_id = $student['id'];
									$dues_details = nmonth_fee($student_id, $fee_month);
									$prev_dues = get_data('student_fee', $student_id, 'current_dues')['data'];
									$total = $dues_details['total'] + $prev_dues;
							?>


								<tr class='odd gradeX'>

									<td><?php echo get_data('transport_area', $student['area_id'], 'area_name')['data']; ?></td>
									<td><?php echo $student['student_admission']; ?></td>
									<td><?php echo $student['student_class'] . "-" . $student['student_section']; ?></td>
									<td><?php echo $student['student_roll']; ?></td>
									<td><?php echo $student['student_name']; ?></td>
									<td><?php echo $student['student_father']; ?></td>
									<td><?php echo $student['student_mobile']; ?></td>
									<td><?php echo implode(',', $fee_month); ?></td>
									<td><?php echo $prev_dues; ?></td>
									<td> <?php echo $dues_details['tuition_fee']; ?> </td>
									<td> <?php echo $dues_details['transport_fee']; ?> </td>
									<td> <?php echo $dues_details['hostel_fee']; ?> </td>
									<td> <?php echo $total; ?> </td>
									<td> <?php echo $remarks; ?> </td>


								</tr>



							<?php } ?>
						</tbody>
					</table>

				</div>


			</div>
		</div>
</div>

</div>
<!-- end page-wrapper -->

</div>
<!-- end wrapper -->
</section>
</div>
<?php require_once('required/footer2.php'); ?>