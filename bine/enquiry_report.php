<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$from_date = (isset($_REQUEST['from_date']))?$_REQUEST['from_date']:$today;
$to_date = (isset($_REQUEST['to_date']))?$_REQUEST['to_date']:$today;

?>
<script>
	document.title = "Collection Report From  <?php echo $from_date; ?>  to <?php echo $end_date; ?>";
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Enquiry Report </h1>
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
								<input type='date' value='<?= $from_date; ?>' name='from_date' >
							<?php } else { ?>
								<input type='date' value='<?= $from_date; ?>' name='from_date' placehplder='from date' min='<?php echo date('Y-m-d', strtotime('-1 months')); ?>'>
							<?php } ?>
						</div>
						<div class="col-lg-3 col-sm-6 float-left">

							<input type='date' value='<?= $to_date; ?>' name='to_date' >
						</div>
						<div class="col-lg-2  col-sm-4">
							<select name='source'>
							    <option value=''> Select Source </option>
								<?php dropdown($source_list, $_REQUEST['source']) ?>
							</select>
						</div>
						<div class="col-lg-2 col-sm-4 ">
							<select name='created_by'>
							     <option value=''> Select Receiptionist </option>
								<?php dropdown_list('user', 'id', 'user_name', $_REQUEST['created_by']); ?>
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
					$from_date = $_POST['from_date'];
					$to_date = $_POST['to_date'];
					$source = $_POST['source'];
					$created_by = $_POST['created_by'];
					
				?>
					<div class="table-responsive">
						<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
							<thead>
								<tr>
									<th>Enquiry ID</th>
									<th>Student Name</th>
									<th>Father Name</th>
									<th>Class</th>
									<th>Source</th>
									<th>Enquiry Date</th>
									<th>Mobile</th>
									<th>Whatsapp</th>
									<th>Receptionist</th>
								
								</tr>
							</thead>
							<tbody>
								<?php
							
								if (trim($source) != '') {
									$query = "select * from enquiry where enquiry_date between '$from_date' and '$to_date' and source ='$source' and created_by ='$created_by' order by enquiry_date desc";
							
								$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
								while ($row = mysqli_fetch_array($res)) {
									$rid = $row['id'];
									$sid = $row['student_id'];
							
									echo "<td> " . $row['id'] . "</td>";
									echo "<td> " . $row['student_name'] . "</td>";
									echo "<td> " . $row['student_father'] . "</td>";
									echo "<td> " . $row['student_class'] . "</td>";
									echo "<td> " . $row['source'] . "</td>";
									echo "<td> ". date('d-M-y',strtotime($row['enquiry_date'])) ."</td>";
								
									echo "<td align='right'> " . $row['student_mobile'] . "</td>";
									echo "<td align='right'> " . $row['student_whatsapp'] . "</td>";
									echo "<td align='right'> " . get_data('user', $row['created_by'], 'user_name')['data'] . "</td>";

								
									echo "</tr>";
								}

                                }

									?>
							</tbody>
						
						</table>
					<?php } ?>
					</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>