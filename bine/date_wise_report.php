<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'account_txn';
?>
<style>
	.tbl tr:last-child {
		background: #ffbf36;
	}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Date wise Balance Report </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Enquiry</a></li>
			<li class="breadcrumb-item active"> Account Txn Report</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<!-- <h3 class="box-title">Add and Update Transport Area </h3>-->
				<form method='post'>
					<div class='row'>


						<div class="col-lg-4 text-right">
							<label>From Date </label>
							<?php if ($user_type != 'Admin') { ?>
								<input type='date' value='<?php echo date('Y-m-d'); ?>' name='from_date'>
							<?php } else { ?>
								<input type='date' value='<?php echo date('Y-m-d'); ?>' name='from_date'>
							<?php } ?>
						</div>
						<div class="col-lg-4 text-center">
							<label>To Date </label>
							<input type='date' value='<?php echo date('Y-m-d'); ?>' name='to_date' min='<?php echo date('Y-m-d', strtotime('-1 months')); ?>'>
						</div>

						<div class="col-lg-2 text-center">
							<input type="submit" class="btn btn-lg btn-success btn-sm" name='submit' value='Generate Report'>
						</div>

					</div>
				</form>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<?php if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
					extract($_POST);
				?>
					<div class='row'>
						<div class='col-md-6'>
							<h2> Collection (Cash & Bank)</h2>
							<table class='tbl table'>
								<?php
								$income = all_income($from_date, $to_date);
								foreach ($income as $ed => $i_amt) {
								?>
									<tr>
										<td><?php echo add_space($ed); ?> </td>
										<td align='right'> <?php echo $i_amt; ?></td>
									</tr>
								<?php } ?>

							</table>
						</div>

						<div class='col-md-6'>
							<h2> Payment /Expense </h2>
							<table class='tbl table'>
								<?php
								$payment = all_exp($from_date, $to_date);
								foreach ($payment as $hd => $e_amt) {
								?>
									<tr>
										<td><?php echo add_space($hd); ?> </td>
										<td align='right'> <?php echo $e_amt; ?></td>
									</tr>
								<?php } ?>
							</table>
						</div>
					</div>

			</div>
			<div class='box-footer text-center'>
				<h2>
					<button class='btn btn-success text-light'>Bank Collection : <?php echo all_income($from_date, $to_date, 'bank')['total']; ?>
					</button>


					<button class='btn btn-dark text-light'>Balance Amount : <?php echo $income['total'] - $payment['total']; ?>
					</button>

					<!--  Form <?php echo date('d-M-Y', strtotime($from_date)); ?>  -->
					<!--<?php echo date('d-M-Y', strtotime($to_date)); ?>  :  <span class='badge badge-warning p-2'> <?php echo $income['total'] - $payment['total']; ?> </span></h2>-->

			</div>
		<?php } ?>
		</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>