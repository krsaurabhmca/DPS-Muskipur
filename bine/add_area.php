<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'transport_area';
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('transport_area');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('transport_area', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Transport Area</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Transport</a></li>
			<li class="breadcrumb-item active">Transport Area</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add and Update Transport Area <?php // echo $user_id; 
																	?> </h3>

				<div class="box-tools pull-right">
					<a class='fa fa-plus btn btn-info btn-sm' href='add_area' title='Add Fee Head'> </a>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class='row'>

					<div class="col-lg-3 col-sm-6">

						<form action='update_area' id='update_frm' method='post' enctype='multipart/form-data'>
							<input type='hidden' value='<?php echo $id; ?>' name='id'>
							<div class="form-group">
								<label> Area List*</label>
								<input class="form-control" required name='area_name' value='<?php echo $area_name; ?>'>


							</div>
							<div class="form-group">
								<label>Transportation Fee* (in Rs.)</label>
								<input class="form-control" name='area_fee' type='number' value='<?php echo $area_fee; ?>'>

							</div>
							<div class="form-group">

								<!--<input name='update_all' value='true' type='checkbox'> Apply this change to all student related to this area-->

							</div>
						</form>
						<button class="btn btn-primary" id='update_btn'> Save </button>



					</div>

					<div class="col-lg-9">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th> # </th>
										<th> Area Name</th>
										<th> Tranportion Fee </th>
										<th> Student Count </th>
										<th> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$query = "select * from transport_area where status='ACTIVE' order by area_name";
									$i = 1;
									$res = mysqli_query($con, $query) or die(" Transport Error : " . mysqli_error($con));
									while ($row = mysqli_fetch_array($res)) {
										$id = $row['id'];
										$sql5 = "select * from student where area_id=$id and student_type='TRANSPORT'";
										$res5 = mysqli_query($con, $sql5);
										$count = mysqli_num_rows($res5);
										echo "<tr><td>";
										echo "<input type='hidden' value='" . $id . "'  name='id'>" . $id . "</td>";
										echo "<td>" . $row['area_name'] . "</td>";
										echo "<td>" . $row['area_fee'] . "</td>";
										echo "<td><a href='area_wise_report.php?area_id=$id'>" . $count . "</a></td>";

									?>
										<td>
											<?php echo btn_edit('add_area', $id); ?>
											<?php echo btn_delete($table_name, $id); ?>
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