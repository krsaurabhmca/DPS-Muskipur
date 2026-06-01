<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$student_id = $_GET['student_id'];
$student = get_data('student', $student_id, null)['data'];
$prev_dues = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Fee Collection <span class='badge badge-danger'><?php echo $student['student_admission']; ?></span></h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Fee</a></li>
			<li class="breadcrumb-item active">Collect Fee</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo $student['student_name']; ?><b> 
				<?php echo $student['student_type']; ?>
				[<?php echo $student['student_class']; ?>-<?php echo $student['student_section']; ?></b>] 
				</h3>

				<div class="box-tools pull-right">
					<a href='collect_fee.php' class='btn btn-success btn-sm'> Press Alt+C For New</a>
					<a href='student_ledger.php?student_id=<?php echo $student_id; ?>&action=show' title='Pay Fee ' class='btn btn-info btn-sm' name='Pay_fee'>Ledger </a>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class="row">

					<div class="col-lg-3 col-sm-12 " id='month_list'>
						<b> Months List </b>
						<ul class='list-group'>
							<?php

							foreach ($month_list as $month) {
								$value = monthly_fee($student_id, $month)['total'];

								$pst = get_data('student_fee', $student_id,  remove_space($month), 'student_id')['data'];
								$pst = end(explode(',',$pst));
								if ($pst == '') {
									//echo "<li class='list-group-item' >";
									create_check($month, remove_space($month), 'fee-month');
									//echo "</li>";
								} else {
									//create_check($month, remove_space($month), 'fee-month', 'checked disabled' );
									
									echo "<a class='border-light text-success p-1' href='receipt?receipt_id=$pst' target='_blank' >" . $month . "<span class='float-right badge-success p-1'> $pst </span></a></li>";
								}
							}
							?>
						</ul>
					</div>
					<div class="col-lg-3 col-sm-12" style='overflow-y:auto;height:430px'>
						<form id='pay_frm' action='pay_fee'>
							<?php
							$fee_list = get_all('fee_head', '*', array('status' => 'ACTIVE') ,'fee_order');
							if ($fee_list['count'] > 0) {
								foreach ($fee_list['data'] as $fee_info) {
									$col_name = remove_space($fee_info['fee_name']);
									
									if($col_name =='annual_fee' or 	$col_name =='exam_fee_lab_fee_lib_fee')
									{
									    if(get_data('student_fee',$student_id,'annual_fee','student_id')['data'] =='' )
    									{
									    echo '<div class="form-group m-0"><label>' . $fee_info['fee_name'] . '</label>';
									    echo "<input type='number' name='$col_name' id='$col_name' class='form-control fee-value' placeholder='" . $fee_info['fee_name'] . "' value='0' readonly ></div>";
									    }
									}
									else{
									echo '<div class="form-group m-0"><label>' . $fee_info['fee_name'] . '</label>';
									echo "<input type='number' name='$col_name' id='$col_name' class='form-control fee-value' placeholder='" . $fee_info['fee_name'] . "' value='0' readonly ></div>";
									}
								}
							}
							?>
					</div>

					<div class="col-lg-3">

						<div class="form-group">
							<label>Date of Payment</label>
							<input class="form-control" value='<?php echo $student['student_admission']; ?>' name='student_admission' type='hidden'>
							<input class="form-control" value='<?php echo $student_id; ?>' name='student_id' id='student_id' type='hidden'>
							<input class="form-control" value='other_month' id='fee_month' name='paid_month' type='hidden' required>
							<input class="form-control" value='<?php echo date('Y-m-d'); ?>' max='<?php echo date('Y-m-d'); ?>' name='paid_date' type='date'>
						</div>
						<div class="form-group">
							<label>Previous Dues</label>
							<input class="form-control text-right" value='<?php echo $prev_dues; ?>' name='previous_dues' type='text' readonly id="previous_dues">
						</div>

						<div class="form-group">
							<label>Miscellaneous Fee</label>
							<input class="form-control text-right" value='0' name='other_fee' type='number' id="other_fee">
						</div>
						
						<div class="form-group">
							<label>Late Fine</label>
							<input class="form-control text-right" value='0' name='late_fine' type='number' id="late_fine">
						</div>
						
						<div class="form-group has-error">
							<label class="control-label" for="inputError">Discount</label>
							<input type="number" class='form-control' name='discount' id='discount1' value='0' required>
						</div>

					</div>

					<div class="col-lg-3">
						<div class="form-group">
							<label>Net Payable</label>
							<input class="form-control text-right" name='total' type='text' id="total" required readonly>
						</div>
						<div class="form-group has-success">
							<label class="control-label" for="inputSuccess">Amount to be Paid</label>
							<input type="number" class="form-control" name='paid_amount' id='paid_amount' required >
						</div>
						<div class="form-group">
							<label>Payment Mode</label>
							<select class="form-control" name='payment_mode' required>
								<option value='Cash'>Cash</option>
								<option value='Bank'>Bank</option>
							</select>
						</div>
						<div class="form-group">
							<label>Remarks (Payment Details)</label>
							<input class="form-control" name='remarks' required type='text'>
						</div>

						<div class="checkbox text-sm">
							<input type="checkbox" id="send_sms" value='yes' checked>
							<label for="send_sms">Send SMS </label>
						</div>
						</form>
						<span class="btn btn-success" id='pay_btn'><i class='fa fa-save'></i> Make Payment</span>
					</div>

				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>

<script>
	function addall() {
		var sum = monthly_fee = 0;
		$(".fee-value").each(function() {
			monthly_fee = monthly_fee + parseInt($(this).val());
		});
		var prev_dues = parseInt($("#previous_dues").val());
		var other_fee = parseInt($("#other_fee").val());
		var discount = parseInt($("#discount1").val());
		var late_fine = parseInt($("#late_fine").val());
		console.log(discount);
		sum = (sum + monthly_fee + prev_dues + other_fee + late_fine) - discount;
		$("#total").val(parseFloat(sum));
	}

	$("#total, #paid_amount, #previous_dues, #other_fee, #discount1, #late_fine").on('keyup', function() {
		addall();
	});
</script>