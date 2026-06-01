<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
$res = get_all('enquiry', '*', array('status' => 'ACTIVE'));
//$res = get_all('student');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Manage Enquiry
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Enquiry</li>
			<li class="breadcrumb-item active">Manage Enquiry</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Manage Enquiry </h3>
						<div class="box-tools pull-right">
							<a class='fa fa-plus btn btn-success btn-sm' title='New Enquiry' href='add_enquiry'> </a>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Sl. No.</th>
										<th>Name</th>
										<th>Father's Name</th>
										<th>Address</th>
										<th>Class </th>
										<th>Mobile No</th>
										<th>Whatsapp</th>
										<th>Remarks</th>
										<th class='text-right'>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i=1;
									if ($res['count'] > 0) {
										$fee = 0;
										foreach ($res['data'] as $row) {
											$link = encode('student_name=' . $row['student_name'] . '&id=' . $row['id']);
											$fee = $fee + $row['form_fee'];
									?>
											<tr>
											    	<td><?= $i; ?></td>
												<td><?php echo $row['student_name'] ?></td>
												<td><?php echo $row['student_father']; ?></td>
												<td><?php echo $row['student_address']; ?></td>
												<td><?php echo $row['student_class']; ?></td>
												<td><?php echo $row['student_mobile'] ?></td>
												<td><?php echo $row['student_whatsapp'] ?></td>
												<td><?php echo $row['remarks'] ?></td>
												<td class='text-right'>
													<a class='fa fa-edit btn btn-info btn-xs' href='add_enquiry?link=<?php echo $link; ?>'></a>
													<span class='delete_btn btn btn-danger btn-xs fa fa-trash' data-table='enquiry' data-id='<?php echo $row['id']; ?>' data-pkey='id'></span>
												</td>
											</tr>
									<?php $i++;
										    
										}
									} ?>
								</tbody>
								<!--<tfoot>
						<tr>
							<th>Name</th>
							<th>Position</th>
							<th>Office</th>
							<th>Age</th>
							<th>Start date</th>
							<th>Salary</th>
						</tr>
					</tfoot>-->
							</table>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php require_once('required/footer2.php'); ?>