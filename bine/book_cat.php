<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table = 'book_cat';
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row($table);
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data($table, $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Book Category </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Libarary</a></li>
			<li class="breadcrumb-item active">Book Category </li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add and Update Book category <?php // echo $user_id; 
																	?> </h3>

				<div class="box-tools pull-right">
					<a class='fa fa-plus btn btn-info btn-sm' href='book_cat' title='Add Fee Head'> </a>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class='row'>

					<div class="col-lg-3 col-sm-6">

						<form action='update_book_cat' id='update_frm' method='post' enctype='multipart/form-data'>
							<input type='hidden' value='<?php echo $id; ?>' name='id'>
							<div class="form-group">
								<label> Book Category Name </label>
								<input class="form-control" required name='cat_name' value='<?php echo $cat_name; ?>'>
							</div>
							<div class="form-group">
								<label>Status </label>
								<select name='status' class='form-control'>
									<?php dropdown($status_list, $status); ?>
								</select>
							</div>

						</form>
						<button class="btn btn-primary" id='update_btn'> Save </button>
					</div>

					<div class="col-lg-9">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th> Category Name</th>
										<th> Book Count </th>
										<th> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$res = get_all($table);
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$ct = get_all('book_list', '*', array('cat_id' => $id))['count'];

											echo "<tr>";

											echo "<td>" . $row['cat_name'] . "</td>";
											echo "<td><a href='manage_book?cat_id=$id'>" . $ct . "</a></td>";
											echo "<td>" .
												btn_view('book_cat', $id, $row['cat_name']) .
												btn_edit('book_cat', $id) .
												btn_delete($table, $id);
											echo  "</td>";
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