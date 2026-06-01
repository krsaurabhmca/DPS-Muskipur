<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Identity Card</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Extra</a></li>
			<li class="breadcrumb-item active">Generate ID</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Select Student to Create ID Card </h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class='row justify-content-center'>
					<div class='col-md-4'>
						<form action='stu_print_id.php' method='post' target='otpl' id='reminder_frm'>
							<div class="form-group">
								<label>Select Student Type </label>
								<?php check_list('student_type', $student_type_list, null, '130px'); ?>

							</div>
					</div>
					<div class='col-md-4'>
						<div class="form-group">
							<label>Select Class </label>
							<select class="form-control" name='student_class' required>
								<?php dropdown($class_list); ?>
							</select>
						</div>
						<div class="form-group">
							<label>Select Section </label>
							<select class="form-control" name='student_section' required>
								<?php dropdown($section_list); ?>
							</select>
						</div>
						<div class="form-group">

							<input type="submit" class="btn btn-orange " value=' CREATE DEMAND' name='submit'>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>