<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$str = '';
if (isset($_GET['txn_mode']) and $_GET['txn_mode'] != '') {

	$pmode = $_GET['txn_mode'];

	$str = "and payment_mode ='$pmode'";
}

?>
<script>
	document.title = "Collection Report From  <?php echo $from_date; ?>  to <?php echo $end_date; ?>";
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Collection Report </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Fee</a></li>
			<li class="breadcrumb-item active">Collection Report</li>
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
                        <div class="col-lg-3 col-sm-6 float-left">

							<?php if ($user_type != 'Admin') { ?>
								<input type='date' value='<?php echo date('Y-m-d'); ?>' name='from_date' min='<?php //echo date('Y-m-d'); 
																												?>'>
							<?php } else { ?>
								<input type='date' value='<?php echo date('Y-m-d'); ?>' name='from_date' placehplder='from date'>
							<?php } ?>
						</div>
						<div class="col-lg-3 col-sm-6 float-left">

							<input type='date' value='<?php echo date('Y-m-d'); ?>' name='to_date' min='<?php echo date('Y-m-d', strtotime('-6 months')); ?>'>
						</div>
						<div class="col-lg-2  col-sm-4">
							<!--<label>Receipt Type</label>-->
							<select name='status'>
								<option value='PAID'> APPROVED</option>
								<option value='CANCEL'> CANCELED</option>
							</select>
						</div>
						<div class="col-lg-2 col-sm-4 ">
							<!--<label>Receipt Type</label>-->
							<select name='collected_by'>
								<option value=''> Collected By</option>
								<?php dropdown_list('user', 'id', 'user_name', $collected_by); ?>
							</select>
						</div>
						<div class="col-lg-2 col-sm-4 ">
							<input type="submit" class="btn btn-lg btn-success btn-sm" name='submit' value='Generate Report'>
						</div>

					</div>
				</form>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<?php

				if (isset($_POST['submit']) && isset($_POST['from_date'])) {
					$fromdate = $_POST['from_date'];
					$todate = $_POST['to_date'];
					$status = $_POST['status'];
					if (isset($_POST['collected_by']) and $_POST['collected_by'] != '') {
						$collected_by = $_POST['collected_by'];
					}
				?>
					<div class="table-responsive">
						<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
							<thead>
								<tr>
									<th>Receipt Id</th>
									<th>Adm No.</th>
									<th>Student Name</th>
									<th>Class</th>
									<th>Roll No.</th>
									<th>Paid date</th>
									<th>Mode</th>
									<th width='80px'>Payment For</th>
									<th>Total</th>
									<th>Discount</th>
									<th>Paid Amount</th>
									<th>Received By</th>
									<?php if ($status == 'CANCEL') {
										echo "<th>Cancel By</th>
                                            <th>Cancel At</th>
                                            <th>Cancel Remarks</th>";
									} else {
										echo "<th>#</th>";
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?php
								$total = 0;
								$cash = 0;
								$bank = 0;
								$all_discount = 0;
								if (trim($collected_by) != '') {
									$query = "select * from receipt where paid_date between '$fromdate' and '$todate' and status ='$status' $str and created_by ='$collected_by' order by id desc";
								} else {
									$query = "select * from receipt where paid_date between '$fromdate' and '$todate' and status ='$status' $str order by paid_date desc";
								}
								//echo $query;
								$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
								while ($row = mysqli_fetch_array($res)) {
									$rid = $row['id'];
									$sid = $row['student_id'];
									$pmode = $row['payment_mode'];
									$student = get_data('student', $sid)['data'];
									$paid_month = remove_space($row['paid_month']);

									$total = $total + $row['paid_amount'];
									if ($row['payment_mode'] == 'Cash') {
										$cash = $cash + $row['paid_amount'];
									}
									if ($row['payment_mode'] == 'Bank') {
										$bank = $bank + $row['paid_amount'];
									}
									$all_discount = $all_discount + $row['discount'];
									echo "<tr class='odd gradeX'>";
									if ($paid_month == 'annual_fee') {
										echo "<td> 
											<a href='annual_receipt.php?receipt_id=$rid' title='Annual Receipt' target='_blank' class='text-primary'>" . $rid . "</a> </td>";
									} else {
										echo "<td> 
											<a href='receipt.php?receipt_id=$rid' title='Monthly Receipt' target='_blank' class='text-primary'>" . $rid . "</a> </td>";
									}


									echo "<td> " . $student['student_admission'] . "</td>";
									echo "<td> " . $student['student_name'] . "</td>";
									echo "<td> " . $student['student_class'] . "-" . $student['student_section'] . "</td>";
									echo "<td> " . $student['student_roll'] . "</td>";
									//		echo "<td> ". date('d-M-y',strtotime($row['paid_date'])) ."</td>";
									echo "<td> " . $row['paid_date'] . "</td>";

								//	echo "<td align='right'> " . $row['payment_mode'] . "</td>";
									echo "<td align='right'> <span class='payment_mode text-primary' data-id='$rid' data-mode='$pmode'>". $pmode ."</span></td>";
									echo "<td> " . ucwords(str_replace(',', ', ', $row['paid_month'])) . "</td>";
									echo "<td> " . $row['total'] . "</td>";
									echo "<td align='right'> " . $row['discount'] . "</td>";
									echo "<td align='right'> " . $row['paid_amount'] . "</td>";
									echo "<td align='right'> " . get_data('user', $row['created_by'], 'user_name')['data'] . "</td>";

									if ($status == 'CANCEL') {
										echo "<td>" . get_data('user', $row['cancel_by'], 'user_name')['data'] . "</td>
                                            <td>" . $row['cancel_at'] . "</td>
                                            <td>" . $row['cancel_remarks'] . "</td>";
									} else {
								?>
										<td width='80px'>
											<button data-id='<?php echo $rid; ?>' Value='Cancel' title='Cancel Receipt' class='cancelreceipt btn btn-danger btn-sm'>
												<i class="fa fa-times-circle-o" aria-hidden="true"></i></button>

									<?php
										echo btn_view('receipt', $rid, $student['student_name']);
									}
									echo "</td></tr>";
								}



									?>
							</tbody>
							<tfoot>
								<tr>
									<th> </th>
									<th>CASH </th>
									<th><?php echo $cash; ?> </th>
									<th>BANK</th>
									<th><?php echo $bank; ?></th>
									<th></th>
									<th></th>
									<th></th>
									<th>Total</th>



									<th align='right'><b> <?php echo $all_discount; ?> </b></th>
									<th align='right'><b> <?php echo $total; ?> </b></th>
									<?php
									if ($status == 'CANCEL') {
										//	echo "<th colspan='5'></th>";
										echo "<th></th><th></th><th></th><th></th><th></th>";
									} else {
										//echo "<th colspan='3'></th>";
										echo "<th></th><th></th>";
									}
									?>
								</tr>
							</tfoot>
						</table>
					<?php } ?>
					</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>