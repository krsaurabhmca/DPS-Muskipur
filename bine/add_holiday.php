<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'holiday';
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row($table_name);
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data($table_name, $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Holiday </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Holiday</a></li>
			<li class="breadcrumb-item active">Add Holiday</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add and Update Holiday</h3>

				<div class="box-tools pull-right">
					<a class='fa fa-plus btn btn-info btn-sm' href='add_holiday' title='Add Holiday '> </a>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class='row'>

					<div class="col-lg-3 col-sm-6">

						<form action='update_holiday' id='update_frm' method='post' enctype='multipart/form-data'>
							<input type='hidden' value='<?php echo $id; ?>' name='id'>
							<div class="form-group">
								<label> Holiday Name*</label>
								<input class="form-control" required name='holiday_name' value='<?php echo $area_name; ?>'>


							</div>
							<div class="form-group">
								<label>Holiday Date*</label>
								<input class="form-control" name='holiday_date' type='date' value='<?php echo $area_fee; ?>' required>

							</div>
						</form>
						<button class="btn btn-primary btn-block" id='update_btn'> Save </button>



					</div>

					<div class="col-lg-9">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th> Name</th>
										<th> Date </th>
										<th> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$res = get_all('holiday');
									foreach ($res['data'] as $row) {
										$id = $row['id'];
										echo "<tr>";
										echo "<td>" . $row['holiday_name'] . "</td>";
										echo "<td>" . $row['holiday_date'] . "</td>";
									?>
										<td align='right'>
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