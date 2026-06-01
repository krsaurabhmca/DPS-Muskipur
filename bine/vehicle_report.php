<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
if (isset($_GET['vehicle_status']) and $_GET['vehicle_type'] == '') {
	$status = $_GET['vehicle_status'];
	$res = get_all('vehicle', '*', array('vehicle_status' => $status));
} else if (isset($_GET['vehicle_type']) and $_GET['status'] == '') {
	$vehicle_type = get_data('vehicle_cat', $_GET['vehicle_type'], 'cat_name')['data'];
	$res = get_all('vehicle', '*', array('vehicle_type' => $vehicle_type));
} else if (isset($_GET['vehicle_status']) and $_GET['vehicle_type'] != '') {
	$vehicle_type = get_data('vehicle_cat', $_GET['vehicle_type'], 'cat_name')['data'];
	$status = $_GET['vehicle_status'];
	$res = get_all('vehicle', '*', array('vehicle_status' => $status, 'vehicle_type' => $vehicle_type));
} else {
	$res = get_all('vehicle');
}
?>
<script>
	document.title = "Vehicle Report ";
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Vehicle Report</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Vehicle</a></li>
			<li class="breadcrumb-item active">Vehicle Report</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title"> Vehicle Details ( <?php echo $status . " " . $vehicle_type; ?> )</h3>
						<div class="box-tools pull-right">
							<form>
								<select name='vehicle_status'>
									<?php dropdown($vehicle_status_list, $vehicle_status); ?>
								</select>
								<select name='vehicle_type'>
									<?php
									dropdown_list('vehicle_cat', 'id', 'cat_name', $vehicle_type);
									?>
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

										<th>Vehicle No.</th>
										<th>Vehicle Model</th>
										<th>Vehicle Type</th>
										<th>Purchase Date</th>
										<th>Driver Name</th>
										<th>Driver Status</th>
										<th>Pollution Expiry</th>
										<th>Insurance Expiry</th>
										<th>Road Tax Expiry</th>
										<th>EMI Start Date</th>
										<th>EMI Last For</th>

									</tr>
								</thead>
								<tbody>
									<?php

									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$status = $row['status'];
											echo "<tr class='odd gradeX'>";

											echo "<td>" . $row['vehicle_no'] . "</td>";
											echo "<td>" . $row['model'] . "</td>";
											echo "<td>" . $row['vehicle_type'] . "</td>";
											echo "<td>" . $row['purchase_date'] . "</td>";
											echo "<td>" . get_data('employee', $row['driver_id'], 'e_name')['data'] . "</td>";
											echo "<td>" . $row['vehicle_status'] . "</td>";

											echo "<td>" . date('d M Y', strtotime($row['pollution_expiry'])) . "</td>";
											echo "<td>" . date('d M Y', strtotime($row['insurance_expiry'])) . "</td>";
											echo "<td>" . date('d M Y', strtotime($row['road_tax_expiry'])) . "</td>";
											echo "<td>" . date('d M Y', strtotime($row['emi_start_date'])) . "</td>";
											$yr = date('Y', strtotime($emi_start_date));
											$period = $row['emi_period'];
											$end_yr = $yr + $period;
											echo "<td>" . date('d M ', strtotime($emi_start_date)) . $end_yr  . "</td>";

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