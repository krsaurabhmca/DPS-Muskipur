<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'gallery';
if (isset($_GET['link']) and $_GET['link'] != '') {
	$student = decode($_GET['link']);
	$id = $student['id'];
} else {

	$student = insert_row($table_name);
	$id = $student['id'];

}

if ($id != '') {
	$res = get_data($table_name, $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Add Photos to Gallery </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Gallery</a></li>
			<li class="breadcrumb-item active">Add Photo</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add /Update Photos </h3>

				<div class="box-tools pull-right">
					<!--<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>-->
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form  action='required/master_process.php?task=update_gallery'  method="post" enctype="multipart/form-data" >
					<div class="row">
						<div class="col-lg-5">
							<div class="form-group">

								<label for="example-text-input" class="col-sm-4 col-form-label">Title</label>
								<div class="col-sm-8">
									<input type='hidden' name='status' value='ACTIVE' />
									<input class="form-control border-warning" type="text" value='<?php echo $photo_title; ?>' name="photo_title" required>
								</div>
							</div>
						</div>
						<div class="col-lg-5">
							<div class="form-group">
								<label for="example-text-input" class="col-sm-4 col-form-label">Photo</label>
								<div class="col-sm-8">
								    <!--<input type="hidden" name="image" id="target_image" value="">-->
									<input class="upload_img form-control" type="file" id="image" accept="image" name='image' data-table="gallery" data-field="image">
								</div>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group" style='margin-top:35px;'>
						        <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
							</div>
						</div>
					</div>
					<!-- /.col -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /.box-body -->
		</form>
	</section>
</div>
<!-- /.content-wrapper -->
<?php require_once('required/footer2.php'); ?>


