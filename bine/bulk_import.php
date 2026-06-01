<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Bulk Import</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Extra</a></li>
			<li class="breadcrumb-item active">Bulk Import</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Bulk Action</h3>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<?php if (isset($_GET['res'])) { ?>
					<div class="alert alert-warning alert-dismissible fade show" role="alert">
						<?php
						$info = json_decode($_GET['res'], true);
						echo $info['new'] . " New record added and " . $info['change'] . " Updated ";
						?>
					</div>
				<?php } ?>
				<div class='row'>
					<div class="col-lg-3 col-offset-lg-4">
						<form action='required/master_process?task=bulk_import' method='post' enctype="multipart/form-data">
							<div class="form-group pt-1 text-right">
								<label class="control-label" for="inputSuccess">Select CSV (Student File)</label>
								<input type="hidden" class='form-control' name='table' value='student'>
								<!--<input type="hidden" class='form-control' name='table' value='student_new'>-->
								<input type="hidden" class='form-control' name='pkey' value='student_admission'>
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
							<button type="submit" class='btn btn-success btn-md'> Import Student Data </button>
						</div>
						</form>
					</div>

					<div class="col-lg-4">

						<a href='master_process.php?task=bulk_export&table=student&status=ACTIVE' class='btn btn-info btn-md'> <i class='fa fa-download'></i> All Student with Template </a>

						<!--<a href='master_process.php?task=bulk_export&table=student&status=INACTIVE' class='btn btn-danger btn-md'> <i class='fa fa-download'></i> Block Student </a>-->
					</div>
				</div>

				<div class='row'>
					<div class="col-lg-3 col-offset-lg-4">
						<form action='master_process?task=bulk_import' method='post' enctype="multipart/form-data">
							<div class="form-group pt-1 text-right">
								<label class="control-label" for="inputSuccess">Select CSV (Transport Area)</label>
								<input type="hidden" class='form-control' name='table' value='transport_area'>
								<input type="hidden" class='form-control' name='pkey' value='id'>
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
							<button type="submit" class='btn btn-success btn-md'> Import Area List </button>
						</div>
						</form>
					</div>

					<div class="col-lg-2">

						<a href='master_process.php?task=bulk_export&table=transport_area' class='btn btn-danger btn-md'> <i class='fa fa-download'></i> Download Transport Template </a>
					</div>
				</div>


				<div class='row'>
					<div class="col-lg-3 col-offset-lg-4">
						<form action='master_process?task=bulk_import' method='post' enctype="multipart/form-data">
							<div class="form-group pt-1 text-right">
								<label class="control-label" for="inputSuccess">Update Previous Dues</label>
								<input type="hidden" class='form-control' name='table' value='student_fee'>
								<input type="hidden" class='form-control' name='pkey' value='student_admission'>
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
							<button type="submit" class='btn btn-success btn-md'> Update Dues</button>
						</div>
						</form>
					</div>

					<div class="col-lg-2">

						<a href='master_process.php?task=bulk_export&table=student_fee' class='btn btn-danger btn-md'> <i class='fa fa-download'></i> Download Transport Template </a>
					</div>
				</div>



			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>