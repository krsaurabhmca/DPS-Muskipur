<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$student_id = xss_clean($_GET['student_id']);
$student = get_data('student', $student_id, null)['data'];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Student Ledger</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="manage_student">Student</a></li>
			<li class="breadcrumb-item active">Ledger</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title"> Payment History of <b><?php echo $student['student_name']; ?></b> [<?php echo $student['student_class']; ?>-<?php echo $student['student_section']; ?>] <?php echo $student['student_roll']; ?> <b><?php echo $student['student_type']; ?></b> </h3>
                
                <a href="pay_fee.php?student_id=<?=$student_id ?>&action=pay" class='btn btn-success float-right btn-sm' > PAY NOW </a>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="example">
						<thead>
							<tr>
								<th>Date</th>
								<th>R. No.</th>
								<th>Paid For</th>


								<?php
								$fee_head = get_all('fee_head', '*', array('status' => 'ACTIVE'));
								foreach ($fee_head['data'] as $fee) {
									//$col_name5 =remove_space($fee['fee_name']);
									//print_r($fee['fee_name']);
									echo "<th>" . $fee['fee_name'] . "</th>";
								}
								?>

								<th>Other Fee</th>
								<th>Total</th>
								<th>Paid</th>
								<th>Dues</th>
								<!--<th>Status</th>-->
								<th>Remarks</th>

							</tr>
						</thead>
						<tbody>
							<?php
							$sql = "select * from receipt where student_id ='$student_id' order by paid_date desc";
							$res = mysqli_query($con, $sql) or die("Error in selecting Student" . mysqli_error($con));

							while ($row = mysqli_fetch_array($res)) {
								$rid = $row['id'];
								$paid_month = remove_space($row['paid_month']);
								echo "<tr class='odd gradeX'>";
								echo "<td>" . date('d-M-Y', strtotime($row['paid_date'])) . "</td>";
								echo "<td> 
											<a href='receipt.php?receipt_id=$rid' title='Monthly Receipt' class='badge badge-danger' target='_blank'>" . $rid . "</a> ".$row['status']."</td>";

								echo "<td>" . $row['paid_month'] . "</td>";

								foreach ($fee_head['data'] as $fee) {
									$col_name5 = remove_space($fee['fee_name']);
									echo "<td>" . $row[$col_name5] . "</td>";
								}

								echo "<td>" . $row['other_fee'] . "</td>";
								echo "<td>" . $row['total'] . "</td>";
								echo "<td>" . $row['paid_amount'] . "</td>";
								echo "<td>" . $row['current_dues'] . "</td>";
								//echo "<td>" . $row['status'] . "</td>";
								echo "<td>" . $row['remarks'] . "</td>";
								echo "</tr>";
							}
							?>

						</tbody>
					</table>

				</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>