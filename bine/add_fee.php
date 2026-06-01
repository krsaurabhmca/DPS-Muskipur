<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('fee_head');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('fee_head', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Fee Management</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Fee</a></li>
			<li class="breadcrumb-item active">Fee Management</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add and Update Fee Details </h3>

				<div class="box-tools pull-right">
					<a class="btn btn-info btn-sm" href='add_fee' title='Add Fee Head'><i class='fa fa-plus'></i> New </a>
					<button class="btn btn-success btn-sm" id='update_btn'><i class='fa fa-save'></i> Save </button>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class="row">
					<div class="col-lg-3 col-sm-6">

						<form action='update_fee' id='update_frm' enctype='multipart/form-data'>
							<div class="form-group">
								<label>Fee Head Name</label>
								<input type='hidden' name='id' value='<?php echo $id; ?>' required>
								<input class="form-control" name='fee_name' value='<?php echo $fee_name; ?>' <?php if ($status != 'AUTO') {
																													echo "readonly";
																												} ?> required>
								<p> Like Late Fine, Discount Etc.</p>
							</div>
							<div class="form-group">
								<label>Display Order (1,2..)</label>
								<input class="form-control" type='number' value='<?php echo $fee_order; ?>' name='fee_order' required>
							</div>
							<div class="form-group">
								<label>Fee Nature</label>
								<select class="form-control" name='fee_type' required id='fee_type'>
									<?php dropdown_with_key($fee_nature_list, $fee_type); ?>
								</select>
							</div>

							<div class="form-group" id='fee_amount_area' <?php if ($fee_type != 'FIXED') {
																				echo "style='display:none'";
																			} ?>>
								<label>Fee Amount </label>
								<input class="form-control" type='number' value='<?php echo $fee_amount; ?>' name='fee_amount' required>

							</div>
							<div class="form-group">
								<label>Student Type</label>
								<?php check_list('student_type', $student_type_list, $student_type); ?>
							</div>

					</div>
					<div class="col-lg-3 col-sm-6">


						<div class="form-group">
							<label>Applicable Month(s) </label>
							<?php check_list('fee_month', $month_list, $fee_month, '400px'); ?>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">


						<div class="form-group">
							<label>Applicable Class(s) </label>
							<?php check_list('student_class', $class_list, $student_class, '400px'); ?>
						</div>
					</div>
					<div class="col-lg-3 col-sm-6">
						<div class="form-group">
							<label>Finance Type</label>
							<?php check_list('finance_type', $finance_list, $finance_type); ?>
						</div>
						<div class="form-group">
							<label>Admission Type </label>
							<?php check_list('admission_type', $admission_list, $admission_type, '100px'); ?>
						</div>

						<div class="form-group">
							<label>Status </label>
							<select class="form-control" name='status' required>
								<?php dropdown($status_list, $status); ?>
							</select>

						</div>

						</form>

					</div>
				</div>
				<div class='row'>
					<div class="col-lg-12">
						<hr>
						<div class="table-responsive">
							<table id="example" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th> # </th>
										<th> Fee Name</th>
										<th> Months</th>
										<th> Class</th>
										<th>Student Type</th>
										<th> Fee Type </th>
										<!--<th> Fee Mode </th>-->
										<th> Finance</th>
										<th> Admission</th>
										<th align='right'> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$query = "select * from fee_head where status <>'AUTO' ";
									$i = 1;
									$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
									while ($row = mysqli_fetch_array($res)) {
										$id = $row['id'];
										echo "<tr>";
										echo "<td>" . $row['fee_order'] . "</td>";
										echo "<td>" . $row['fee_name'] . "<br> <span class='badge badge-secondary'>" . $row['status'] . "</span></td>";
										echo "<td>" . str_replace(',', ', ', $row['fee_month']) . "</td>";
										echo "<td>" . str_replace(',', ', ', $row['student_class']) . "</td>";
										echo "<td>" . $row['student_type'] . "</td>";
										echo "<td>" . $row['fee_type'] . "</td>";
										//echo "<td>".$row['fee_mode'] ."</td>";	
										echo "<td>" . str_replace(',', ', ', $row['finance_type']) . "</td>";
										echo "<td>" . str_replace(',', ', ', $row['admission_type']) . "</td>";

									?>
										<td align='right'>
											<a class='fa fa-edit fa-1x text-info' href='add_fee?link=<?php echo encode('id=' . $id); ?>' data-table='fee_head' data-pkey='id'></a>
											<?php if ($row['created_by'] != 0) { ?>
												<span class='delete_fee fa fa-trash fa-1x text-orange' data-id='<?php echo $id; ?>'>
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
					<!--  End  Basic Table  -->
				</div>
			</div>
			<!-- /.row -->
		</div>
		<!-- /.box-body -->
	</section>
</div>
<!-- /.content-wrapper -->
<?php require_once('required/footer2.php'); ?>