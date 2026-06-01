<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
$table_name = 'admit_card';

if (isset($_GET['cat_id']) and $_GET['cat_id'] <> '') {
	$res = get_all($table_name, '*', array('cat_id' => $_GET['cat_id']));
} else if (isset($_GET['pub_id']) and $_GET['pub_id'] <> '') {
	$res = get_all($table_name, '*', array('pub_id' => $_GET['pub_id']));
} else {
	$res = get_all($table_name);
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Manage Admit Card
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Exam</li>
			<li class="breadcrumb-item active">Admit Card</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Details of Admit Card </h3>
						<div class="box-tools pull-right">
							<a class='fa fa-plus btn btn-success btn-sm' title='New Book' href='admit_card'> </a>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Exam Name</th>
										<th>Class</th>
										<th>Exam Date</th>
										<th>Subject </th>
										<th>In Time </th>
										<th>Out Time </th>
										<th class='text-right'>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if ($res['count'] > 0) {
										$fee = 0;
										foreach ($res['data'] as $row) {
											$id = $row['id'];
									?>
											<tr>
												<td><?php echo $row['exam_name'] ?></td>
												<td><?php echo $row['student_class'] ?></td>
												<td><?php echo $row['exam_date']; ?></td>
												<td><?php echo $row['subject']; ?></td>
												<td><?php echo date('h:i A', strtotime($row['in_time'])); ?></td>
												<td><?php echo date('h:i A', strtotime($row['out_time'])); ?></td>
												<td class='text-right'>
												<?php echo btn_view($table_name, $id, $row['student_class']); ?>
												<?php echo btn_edit('admit_card', $id); ?>
												<?php echo btn_delete($table_name, $id); ?>
												</td>
											</tr>
									<?php }
									} ?>
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