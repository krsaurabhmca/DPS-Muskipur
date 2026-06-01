<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'admin_txn';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Admin Transaction Details </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Enquiry</a></li>
			<li class="breadcrumb-item active"> Admin Txn Report</li>
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


						<div class="col-lg-2 text-right">

							<input type='date' value='<?php echo date('Y-m-d'); ?>' name='from_date'>

						</div>
						<div class="col-lg-2 text-center">

							<input type='date' value='<?php echo date('Y-m-d'); ?>' name='to_date' min='<?php echo date('Y-m-d', strtotime('-6 months')); ?>'>
						</div>
						<div class="col-lg-2">
							<!--<label>Receipt Type</label>-->
							<select name='txn_type'>
								<option value='Income'> INCOME</option>
								<option value='Expense'> EXPENSE</option>
							</select>
						</div>
						<div class="col-lg-2">
							<!--<label>Receipt Type</label>-->
							<select name='status'>
								<option value='ACTIVE'> APPROVED</option>
								<option value='CANCEL'> CANCELED</option>
							</select>
						</div>
						<div class="col-lg-2">
							<!--<label>Receipt Type</label>-->
							<select name='account_name'>
								<option value=''></option>
								<option value='Director'>Director</option>
								<option value='Principal'>Principal</option>
							</select>
						</div>
						<div class="col-lg-2">
							<input type="submit" class="btn btn-lg btn-success btn-sm" name='submit' value='Generate Report'>
						</div>

					</div>
				</form>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<?php if (isset($_POST['submit']) && isset($_POST['from_date'])) {
					$fromdate = $_POST['from_date'];
					$todate = $_POST['to_date'];
					$status = $_POST['status'];
					$account_name = $_POST['account_name'];
				?>
					<div class="table-responsive">
						<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
							<thead>
								<tr>
									<th>#</th>
									<th>Type</th>
									<th>Exp By</th>
									<th>Account Type</th>
									<th>Account Name</th>
									<th>Date</th>
									<th>Amount</th>
									<th>Mode</th>
									<th>Remarks</th>
									<?php if ($status == 'CANCEL') {
										echo "<th>Cancel By</th>
                                            <th>Cancel At</th>
                                            <th>Cancel Remarks</th>";
									} else {
										echo "<th>Action</th>";
									}
									?>

								</tr>
							</thead>
							<tbody>
								<?php
								$total = 0;
								$all_discount = 0;
								$i = 1;
								if ($account_name == '') {
									$query = "select * from $table_name where txn_date between '$fromdate' and '$todate' and status='$status' order by id desc";
								} else {
									$query = "select * from $table_name where txn_date between '$fromdate' and '$todate' and status='$status' and account_name ='$acount_name' order by id desc";
								}
								//echo $query;
								$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
								while ($row = mysqli_fetch_array($res)) {
									$rid = $row['id'];

									$total = $total + $row['txn_amount'];
									$account_type = get_data('account_head', $row['account_id'], 'account_type')['data'];
									$account_name = get_data('account_head', $row['account_id'], 'account_name')['data'];

									echo "<tr class='odd gradeX'>";
									echo "<td> " . $i . "</td>";
									echo "<td> " . $row['txn_type'] . "</td>";
									echo "<td> " . $row['account_name'] . "</td>";
									echo "<td> " . $account_type . "</td>";
									echo "<td> " . $account_name . "</td>";
									echo "<td> " . date('d-M-Y', strtotime($row['txn_date'])) . "</td>";

									echo "<td> " . $row['txn_amount'] . "</td>";
									echo "<td> " . $row['txn_mode'] . "</td>";
									echo "<td> " . $row['txn_remarks'] . "</td>";

									if ($status == 'CANCEL') {
										echo "<td>" . get_data('user', $row['cancel_by'], 'user_name')['data'] . "</td>
                                        <td>" . $row['cancel_at'] . "</td>
                                        <td>" . $row['cancel_remarks'] . "</td>";
									} else {
								?>
										<td width='80px'>

									<?php

										echo btn_view($table_name, $rid, $account_name);


										echo "</td></tr>";
										$i++;
									}
								}
									?>
									<tr>
										<th align='right'> <?php echo $i; ?> </th>
										<th align='right'> Total </th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th align='right'><b> <?php echo $total; ?> </b></th>
										<th></th>
										<th></th>
										<th></th>
										<?php
										if ($status == 'CANCEL') {
											echo "<th></th>";
											echo "<th></th>";
										}
										?>

									</tr>
							</tbody>
						</table>
					<?php } ?>
					</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>