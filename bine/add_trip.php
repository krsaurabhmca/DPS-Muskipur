<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('trip_details');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('trip_details', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Trip managment</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Transport</a></li>
			<li class="breadcrumb-item active">Trip Management</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add and Update Fee Details </h3>

				<div class="box-tools pull-right">
					<a class="btn btn-info btn-sm" href='add_trip' title='Add New Trip'><i class='fa fa-plus'></i> New </a>
					<button class="btn btn-success btn-sm" id='update_btn'><i class='fa fa-save'></i> Save </button>

				</div>
			</div>
			<div class='box-body'>
				<div class='row'>
					<div class="col-lg-4 col-sm-6">

						<form action='update_trip' method='post' enctype='multipart/form-data' id='update_frm'>
							<div class="form-group">
								<label>Trip Name / Bus Name</label>
								<input class="form-control" type='hidden' name='id' value='<?php echo $id; ?>'>
								<input class="form-control" name='trip_name' value='<?php echo $trip_name; ?>'>
								<p>Sector 1 , Near By Pass</p>
							</div>
							<div class="form-group">
								<label> Departure Time</label>
								<input class="form-control" value='<?php echo $departure_time; ?>' name='departure_time' type='time'>
							</div>
							<div class="form-group">
								<label>Arrival Time</label>
								<input class="form-control" value='<?php echo $arrival_time; ?>' name='arrival_time' type='time'>
							</div>

							<div class="form-group">
								<label>Vehicle No</label>
								<input class="form-control" value='<?php echo $vehicle_no; ?>' name='vehicle_no' type='text'>
							</div>
							<div class="form-group">
								<label>Driver Name</label>
								<input class="form-control" value='<?php echo $driver_name; ?>' name='driver_name' type='text'>
							</div>

							<div class="form-group">
								<label>GPS Key</label>
								<input class="form-control" name='gps_key' value='<?php echo $gps_key; ?>' type='text' placeholder='Like C692A590159BDB726BE8'>

							</div>
							<!--<div class="form-group">
													<label>Area List </label>
													<select class="form-control" name='area_list[]'  multiple required>
														<?php echo dropdown_list('transport_area', 'id', 'area_name'); ?>
														
													</select>
												</div>-->

						</form>


					</div>

					<div class="col-lg-8">

						<div class="table-responsive">
							<table class="table" id='example1'>
								<thead>
									<tr>
										<th> # </th>
										<th> Trip Name</th>
										<th> Vehicle No</th>
										<th> Driver </th>
										<th> Timing </th>
										<th> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$query = "select * from trip_details where status='ACTIVE'";
									$i = 1;
									$res = mysqli_query($con, $query) or die(" Transport Error : " . mysqli_error($con));
									while ($row = mysqli_fetch_array($res)) {
										$id = $row['id'];
										$gps_key = $row['gps_key'];
										echo "<tr><td>";
										echo "<input type='hidden' value='" . $id . "'  name='id'>" . $i . "</td>";
										echo "<td>" . $row['trip_name'] . "</td>";
										echo "<td>" . $row['vehicle_no'] . "</td>";
										echo "<td>" . $row['driver_name'] . "</td>";
										echo "<td>" . $row['departure_time'] . "-" . $row['arrival_time'] . "</td>";
									?>
										<td align='center'>
											<a class='fa fa-edit fa-2x text-info' href='add_trip?link=<?php echo encode('id=' . $id); ?>' data-table='trip_details' data-pkey='id'></a>
											<?php if ($row['created_by'] == 0) { ?>
												<span class='delete_btn fa fa-trash fa-2x text-orange' data-id='<?php echo $id; ?>' data-table='trip_details' data-pkey='id'>
												<?php } ?>
										</td>
									<?php
										$i++;
									}
									?>


								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>


	</section>
</div>
<?php require_once('required/footer2.php'); ?>