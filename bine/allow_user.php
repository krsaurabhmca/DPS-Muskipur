<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
//$res = get_all('student');
if (isset($_GET['sel_user_id']) and $_GET['sel_user_id'] != '') {
	$sel_user_id = $_GET['sel_user_id'];
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Manage Permission
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">User</li>
			<li class="breadcrumb-item active">Permission</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Manage Permisson of <?php echo  get_data('user', $sel_user_id, 'full_name')['data']; ?> </h3>
						<div class="box-tools pull-right">
							<form>
								<select name='sel_user_id' onchange='submit()'>
									<?php dropdown_list('user', 'id', 'full_name', @$sel_user_id); ?>
								</select>
							</form>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Task Name</th>
										<th>Can View</th>
										<th>Can Add</th>
										<th>Can Edit </th>
										<th>Can Delete</th>
										<th>Show in Menu</th>
										<th class='text-right'>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php

									foreach ($task_list as $task_value => $task_name) {

										$allow_data  = get_multi_data('allow_user', array('user_id' => $sel_user_id, 'table_name' => $task_value))['data'][0];
										extract($allow_data);
									?>
										<tr>
											<td><?php echo $task_name; //print_r($allow_data); 
												?></td>
											<td><select name='can_view'>
													<?php dropdown($allow_status, $in_menu); ?>
												</select>
											</td>
											<td><select name='can_add'>
													<?php dropdown($allow_status, $allow_data['can_add']); ?>
												</select>
											</td>
											<td><select name='can_edit'>
													<?php dropdown($allow_status, $allow_data['can_edit']); ?>
												</select>
											</td>
											<td><select name='can_delete'>
													<?php dropdown($allow_status, $allow_data['can_delete']); ?>
												</select>
											</td>
											<td>
												<?php //echo $in_menu; 
												?>
												<select name='in_menu'>
													<?php dropdown($allow_status, $in_menu); ?>
												</select>
											</td>

											<td class='text-right'>

												<button class='btn btn-success btn-sm'> SAVE </button>
											</td>
										</tr>
									<?php } ?>
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