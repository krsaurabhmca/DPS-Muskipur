<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
	$fee_details = get_data('fee_details', $id);
	if ($fee_details['count'] > 0) {
		$fee_data = $fee_details['data'];
	}
} else {
	if (get_all('fee_details')['count'] < 1) {
		foreach (array_filter($class_list) as $class) {
			insert_data('fee_details', array('student_class' => $class, 'status' => 'ACTIVE'));
		}
	}
	$fee_details['count'] = 0;
}
//$class_fee_list = direct_sql("select * from fee_head where fee_type!='STUDENT' and status='ACTIVE' order by fee_order");
$class_fee_list = direct_sql("select * from fee_head where status='ACTIVE' and fee_type not in('STUDENT') order by fee_order");

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Fee Management</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Fee</a></li>
			<li class="breadcrumb-item active">Fee Amount</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Set Fee Amount</h3>

				<div class="box-tools pull-right">

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class="row">

					<div class="col-lg-12">

						<div class="table-responsive">
							<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
								<thead>
									<tr>
										<!-- <th> # </th>-->
										<th> Class Name </th>
										<?php
										foreach ($class_fee_list['data'] as $fee_list) {
											echo "<th>" . $fee_list['fee_name'] . "</th>";
										}
										?>
										<th> Action </th>
									</tr>
								</thead>
								<tbody>

									<?php
									//$all_fee = get_all('fee_details','*' , array('status'=>'ACTIVE'));
									$class_list_str = "'" . implode("', '", array_filter($class_list)) . "'";
									$sql = "select * from fee_details where student_class in($class_list_str) and status ='ACTIVE'";
									
									$all_fee = direct_sql($sql);

									if ($all_fee['count'] > 0) {
										// Class Wise Loop 
										foreach ($all_fee['data'] as $class_fee) {
											$fee_id = $class_fee['id'];
											echo "<tr>";
											echo "<td>" . $class_fee['student_class'] . "</td>";
											foreach ($class_fee_list['data'] as $fee_info2) {
												echo "<td>" . $class_fee[remove_space($fee_info2['fee_name'])] . "</td>";
											}
											echo "<td><a class='fa fa-edit text-orange' href='update_fee?id=$fee_id' ></a></td>";
											echo "</tr>";
										}
									}
									?>


								</tbody>
							</table>
						</div>
					</div>
				</div>
	</section>
</div>

<div id="feeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header with-border">
				<h4 class="modal-title" id="myModalLabel">Update Fee</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			</div>
			<div class="modal-body">
				<?php
				if ($fee_details['count'] > 0) {
				?>
					<form action='set_fee_amount' id='update_frm' enctype='multipart/form-data'>
						<div class="form-group">
							<input type='hidden' name='id' value='<?php echo $id; ?>'>
							<label>Select Class </label>
							<select class="form-control" name='student_class' required>
								<?php echo dropdown($class_list, $fee_data['student_class']); ?>
							</select>
						</div>
						<?php
						foreach ($class_fee_list['data'] as $fee_info) {
							//print_r($fee_info);
							$col_name = remove_space($fee_info['fee_name']);
						?>
							<div class="form-group">
								<label class="control-label"><?php echo $fee_info['fee_name']; ?></label>
								<input type="text" class="form-control form-control-sm" name='<?php echo $col_name; ?>' value='<?php echo $fee_data[$col_name]; ?>'>
							</div>
						<?php
						}
						?>
					</form>
					<button class="btn btn-success btn-sm" id='update_btn'><i class='fa fa-save'></i> Save </button>
				<?php } ?>

			</div>

		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<?php require_once('required/footer2.php');
if (isset($_REQUEST['id'])) {
	echo "<script> $('#feeModal').modal('show') </script>";
}
?>