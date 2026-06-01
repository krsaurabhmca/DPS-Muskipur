<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'account_txn';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Head Wise Expense Details </h1>
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


						<div class="col-lg-3 text-right">
							<!--<label>From Date </label>-->

							<input type='date' value='<?php echo date('Y-m-d'); ?>' name='from_date'>

						</div>
						<div class="col-lg-2 text-center">
							<!--<label>To Date </label>-->
							<input type='date' value='<?php echo date('Y-m-d'); ?>' name='to_date'>
						</div>

						<div class="col-lg-2 text-center">
							<!--<label>Expense Type </label>-->
							<select name='account_type' id='account_type'>
								<?php dropdown($account_head_list, $_POST['account_type']); ?>
							</select>
						</div>
						<!-- <div class="col-lg-3 text-center">
							<select name='account_id' id='account_id'>
								<?php  ?>
							</select>

						</div> -->
						<div class="col-lg-2 text-center">
							<input type="submit" class="btn btn-lg btn-success btn-sm" name='submit' value='Generate Report'>
						</div>

					</div>
				</form>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<?php if (isset($_POST['submit']) && isset($_POST['account_id'])) {
					$fromdate = $_POST['from_date'];
					$todate = $_POST['to_date'];
					$account_type = $_POST['account_type'];
					$account_id = $_POST['account_id'];
					$status = $_POST['status'];
				?>
					<div class="table-responsive">
						<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
							<thead>
								<tr>
									<th>Account Name</th>
									<th>Txn Date</th>
									<th>Amount</th>
									<th>Remarks</th>
									<th>Status</th>

								</tr>
							</thead>
							<tbody>
								<?php
								$total = 0;
								$q = '';
								if ($_POST['account_id'] <> '') {
									$q = 'and account_id=' . $account_id;
								}
								$sql = "SELECT * FROM `exp_details` where  txn_date between '$fromdate' and '$todate' and account_type ='$account_type' and status ='ACTIVE' $q order by txn_date";


								$res = direct_sql($sql);
								foreach ($res['data'] as $row) {
									$rid = $row['id'];
									$account = get_data('account_head', $row['account_id'])['data'];

									$total = $total + $row['txn_amount'];

									echo "<tr class='odd gradeX'>";
									echo "<td> " . $account['account_name'] . "</td>";
									//	echo "<td> ". date('d-M-Y',strtotime($row['txn_date'])) ."</td>";
									echo "<td> " . $row['txn_date'] . "</td>";
									echo "<td align='right'> " . $row['txn_amount'] . "</td>";
									echo "<td align='right'> " . $row['txn_remarks'] . "</td>";
									echo "<td align='right'> " . $row['status'] . "</td>";

									echo "</tr>";
								}



								?>

							</tbody>
							<tfoot>
								<tr>
									<th colspan='2' align='right'> Total </td>
									<th align='right'><b> <?php echo $total; ?> </b></td>

								</tr>
							</tfoot>
						</table>
					<?php } ?>
					</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>

<script>
	$(document).on('click', '#account_type', function() {

		var acc_type = $(this).val();
		$.ajax({
			type: "GET",
			url: "master_process.php?task=get_account",
			data: 'account_type=' + acc_type,
			success: function(data) {
				console.log(data);
				$("#account_id").html("<option value=''></option>" + data);
			}
		});

	});
</script>