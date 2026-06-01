<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Bulk Marks Import</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Exam</a></li>
			<li class="breadcrumb-item active">Marks Import</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Marks Upload</h3>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<?php if (isset($_GET['res'])) { ?>
					<div class='alert alert-success'>
						<?php
						$info = json_decode($_GET['res'], true);
						echo $info['new'] . " New record added and " . $info['change'] . " Updated " . $info['msg'];
						?>
					</div>
				<?php } ?>

				<div class='row'>
					<div class="col-lg-3 col-offset-lg-4">
						<form action='required/master_process?task=marks_upload' method='post' enctype="multipart/form-data">
							<div class="form-group pt-1 text-right">
								<label class="control-label" for="inputSuccess">Update Marks </label>
								<input type="hidden" class='form-control' name='table' value='exam'>
								<input type="hidden" class='form-control' name='pkey' value='student_admission'>
								<input type="hidden" class='form-control' name='remove' value='name,roll,class'>
							</div>
					</div>

					<div class="col-lg-3">
						<div class="form-group">
							<div class="form-group has-success">

								<input type="file" name='file' class='form-control' required>
							</div>
						</div>
					</div>
					<div class="col-lg-2">
						<div class="form-group">
							<label class="control-label">&nbsp;</label>
							<button type="submit" class='btn btn-success btn-md'> Upload Marks</button>
						</div>
						</form>
					</div>

					<div class="col-lg-2">
						<a href='marks_entry' class='btn text-danger btn-md'> <i class='fa fa-download'></i> Download CSV from Exam-> Marks Entry </a>
					</div>
				</div>

			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>