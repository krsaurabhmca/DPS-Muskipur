<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('user');
	print_r($fee);
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('user', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Manage User</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item active">User </li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add & manage user </h3>

				<div class="box-tools pull-right">
					<a class='fa fa-plus btn btn-info btn-sm' href='add_user' title='Add New user'> </a>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class='row'>

					<div class="col-lg-3 col-sm-6">

						<form action='update_user' id='update_frm' method='post' enctype='multipart/form-data'>
							<input type='hidden' value='<?php echo $id; ?>' name='id'>
							<div class="form-group">
								<label>Full name</label>
								<input class="form-control" required name='full_name' value='<?php echo $full_name; ?>'>
							</div>
							<div class="form-group">
								<label>User Type </label>
								<select name='user_type' class='form-control' required>
									<?php dropdown($user_type_list, $user_type); ?>
								</select>
							</div>
							<div class="form-group">
								<label>User name</label>
								<input class="form-control" required name='user_name' value='<?php echo $user_name; ?>'>
							</div>
							<div class="form-group">
								<label>User Password</label>
								<input class="form-control" name='user_pass' value='<?php // echo $user_pass; ?>'>
								<small> if don't want to change leave it blank</small>
							</div>
							<div class="form-group">
								<label>User Mobile</label>
								<input class="form-control" required name='user_mobile' value='<?php echo $user_mobile; ?>'>
							</div>
							<div class="form-group">
								<label>User Email</label>
								<input class="form-control" type='email' name='user_email' value='<?php echo $user_email; ?>'>
							</div>

							<div class="form-group">
								<label>Status </label>
								<select name='user_status' class='form-control'>
									<?php dropdown($status_list, $status); ?>
								</select>
							</div>
						</form>
						<?php if ($_SESSION['user_type'] == 'ADMIN') { ?>
							<button class="btn btn-primary btn-block" id='update_btn'> Save </button>
						<?php } else { ?>
							<button class="btn btn-border border-danger"> Don't Have Permission </button>
						<?php } ?>


					</div>

					<div class="col-lg-9">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th> User Type</th>
										<th> Username </th>
										<th> Mobile</th>
										<th> Email </th>
										<th> status </th>
										<th> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$res = get_all('user');
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											echo "<tr>";
											echo "<td>" . $row['user_type'] . "</td>";
											echo "<td>" . $row['user_name'] . "</td>";
											echo "<td>" . $row['user_mobile'] . "</td>";
											echo "<td>" . $row['user_email'] . "</td>";
											echo "<td>" . $row['user_status'] . "</td>";
									?>
											<td>
												<a href='add_user.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
												<span class='delete_btn btn btn-danger btn-sm' data-table='user' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
											</td>
									<?php
											$i++;
										}
									}
									?>


								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>



<?php require_once('required/footer2.php'); ?>