<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
$table_name = 'tbl_tc';
$res = get_all($table_name);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Manage Issued TC
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Certificate</li>
			<li class="breadcrumb-item active">TC</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
							<h3 class="box-title">Manage Student
							</h3>
						
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Adm. No.</th>
										<th>Name</th>
										<th>Father's Name</th>
										<th>Certificate No.</th>
										<th>Date of Apply</th>
										<th>Date of Issue</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) 
										{
										$id = $row['id'];
										$student_admission = $row['student_admission'];
										$link = encode('student_name=' . $row['student_name'] . '&id=' . $row['id']);
										$tc_link = encode('student_admission=' . $student_admission);
									?>
									        <tr>
									        	<td><?php echo $row['student_admission'] ?></td>
												<td><?php echo $row['student_name'] ?></td>
												<td><?php echo $row['student_father'] ?></td>
												<td><?php echo $row['tc_no'] ?></td>
												<td><?php echo $row['doa_certificate'] ?></td>
												<td><?php echo $row['doi_certificate'] ?></td>
												<td>
												<a class='fa fa-print btn btn-dark btn-xs' href='print_tc?link=<?php echo $tc_link;?>' target='_blank'></a>
												<a href='create_tc?student_admission=<?php echo $student_admission;?>&action=show' class='btn btn-info btn-sm' ><i class='fa fa-edit'></i></a>
												<?php echo btn_delete($table_name, $id); ?>
												</td>
											</tr>
									<?php 
									    } }
									?>
								</tbody>
						
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