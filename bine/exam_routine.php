<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Generate Routine</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Exam</a></li>
			<li class="breadcrumb-item active">Routine</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Generate Exam Routine</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class='row justify-content-center'>
					<div class='col-md-3'></div>
					<div class='col-md-6'>
						<form action='print_exam_routine.php' method='post'>
							<div class="form-group">
								<label>Select Class </label>
								<select class="form-control" name='student_class'>
									<?php dropdown($class_list); ?>
								</select>
							</div>
							<div class="form-group">
								<input type="submit" class="btn btn-orange btn-block " value='Submit' name='submit'>
							</div>
						</form>
					</div>
					<div class='col-md-3'></div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
	$("#task").on('change', function() {
		var task = $(this).children("option:selected").val();
		console.log(task);
		$("#reminder_frm").attr('action', task);

	});
	$('#summernote').summernote({
		placeholder: 'Enter Details',
		tabsize: 2,
		height: 150
	});
</script>