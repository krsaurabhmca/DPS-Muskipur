<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Route Wise Demand</h1>
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
				<h3 class="box-title">Route Wise List </h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class='row justify-content-center'>
					<div class='col-md-4'>
						<form action='route_reminder.php' method='post' target='otpl' id='reminder_frm'>
							<div class="form-group">
								<label>Select Route </label>
								<?php
								$tlist = create_list('transport_area', 'area_name');
								check_list('area_id', $tlist, null, '280px');
								?>

							</div>

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
			$("#reminder_frm").attr('action', 'route_reminder_list.php');
		} else {
			$("#reminder_frm").attr('action', 'route_reminder.php');
		}
	});
</script>