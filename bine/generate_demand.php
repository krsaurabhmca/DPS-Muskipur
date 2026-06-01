<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Generate Demand</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Fee</a></li>
			<li class="breadcrumb-item active">Generate Demand</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Generate Demand List </h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class='row justify-content-center'>
					<div class='col-md-4'>
						<form action='class_reminder.php' method='post' target='otpl' id='reminder_frm'>
							<div class="form-group">
								<label>Select Class </label>
								<select class="form-control" name='student_class'>
									<?php dropdown($class_list); ?>
								</select>
							</div>
							<div class="form-group">
								<label>Select Section </label>
								<select class="form-control" name='student_section'>
									<?php dropdown($section_list); ?>
								</select>
							</div>

							<div class="form-group">
								<label>Select Admission (Ex. 1003,1005,1016.. etc) </label>
								<input type='text' class="form-control" name='student_admission'>
							</div>

							<div class="form-group">
								<label>Select Student Type </label>
								<?php check_list('student_type', $student_type_list, null, '120px'); ?>

							</div>
							<!--<div class="form-group">
									 <label>Select Finance Type </label>
										<?php check_list('finance_type', $finance_list, null, '120px'); ?>
								
								</div>-->
							<div class="form-group">
								<label>Collection Date </label>
								<input class="form-control" type='date' name='collection_date' min='<?php echo date('Y-m-d'); ?>' value='<?php echo date('Y-m-d'); ?>'>
							</div>


					</div>
					<div class='col-md-4'>
						<div class="form-group">
							<label>Select Month (multiple) </label>
							<?php check_list('fee_month', $month_list, null, '270px'); ?>

						</div>
						<hr>
						<div class="row">
							<div class="checkbox col-6">
								<input type="checkbox" id="send_sms">
								<label for="send_sms">Send SMS Also </label>
							</div>

							<div class="checkbox col-6">
								<input type="checkbox" id="demand_list">
								<label for="demand_list"> Generate List </label>
							</div>
						</div>
						<div class="form-group">

							<input type="submit" class="btn btn-orange btn-block" value='CREATE DEMAND'>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>
<script>
	$("#demand_list").on('click', function() {
		var x = $(this).prop('checked');
		if (x == true) {
			$("#reminder_frm").attr('action', 'reminder_list.php');
		} else {
			$("#reminder_frm").attr('action', 'class_reminder.php');
		}
	});
</script>