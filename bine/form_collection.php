<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Form Collection Report</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Enquiry</a></li>
			<li class="breadcrumb-item active"> Form Sale Report</li>
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
				<?php if (isset($_POST['submit']) && isset($_POST['from_date'])) {
					$fromdate = $_POST['from_date'];
					$todate = $_POST['to_date'];
					$status = $_POST['status'];
				?>
					<div class="table-responsive">
						<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
							<thead>
								<tr>
									<th>Enquiry Id</th>
									<th>Student Name</th>
									<th>Class</th>
									<th>Address</th>
									<th>Paid date</th>
									<th>Amount</th>
									<th>Received By</th>

								</tr>
							</thead>
							<tbody>
								<?php
								$total = 0;
								$all_discount = 0;

								$query = "select * from enquiry where DATE_FORMAT(enquiry_date,'%Y-%m-%d') between '$fromdate' and '$todate' and status<>'AUTO' order by id desc";

								$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
								while ($row = mysqli_fetch_array($res)) {
									$rid = $row['id'];
									$link = encode('student_name=' . $row['student_name'] . '&id=' . $row['id']);
									$total = $total + $row['form_fee'];
									echo "<tr class='odd gradeX'>";
									echo "<td> 
											<a href='form_receipt.php?link=$link' title='Form Receipt' target='_blank'>" . $rid . "</a> </td>";
									echo "<td> " . $row['student_name'] . "</td>";
									echo "<td> " . $row['student_class'] . "</td>";
									echo "<td> " . $row['student_address'] . "</td>";
									echo "<td> " . date('d-M-Y', strtotime($row['created_at'])) . "</td>";

									echo "<td> " . $row['form_fee'] . "</td>";
									echo "<td align='right'> " . get_data('user', $row['created_by'], 'user_name')['data'] . "</td>";
									echo "</td></tr>";
								}



								?>

							</tbody>
							<tfoot>
								<tr>
									<th colspan='5' align='right'> Total </td>
									<th align='right'><b> <?php echo $total; ?> </b></td>
									<th align='right'>
										</td>
								</tr>
							</tfoot>
						</table>
					<?php } ?>
					</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>