<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Generate Admit card </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="breadcrumb-item"><a href="#transport">Exam</a></li>
			<li class="breadcrumb-item active">Admit card</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Generate Admit Card</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
					<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class='row justify-content-center'>
					<div class='col-md-2'></div>
					<div class='col-md-4'>
						<form action='print_admit.php' method='post' target='otpl' id='reminder_frm'>
							<div class="form-group">
								<label>Select Class </label>
								<select class="form-control" name='student_class'>
									<?php dropdown($class_list); ?>
								</select>
							</div>
							<div class="form-group">
								<label>Select Section </label>
								<select class="form-control" name='student_section'>
									<?php dropdown($section_list); ?>
								</select>
							</div>
							<div class="form-group text-center">
								<label> Roll Number (1,2,6..) </label>
								<input type='text' class="form-control" name='student_roll' placeholder='12,58,79'>
							</div>
							<!--<div class="form-group">-->
							<!--	 <label>Select Student Type </label>-->
							<!--		<?php check_list('student_type', $student_type_list, null, '120px'); ?>-->

							<!--</div>-->

        <!--                    <div class="form-group">-->
								<!--<label>Exam Name </label>-->
								<!--<input class="form-control"  type='text'  name='exam_name' >-->
					   <!--     </div>	-->
							<div class="form-group">

								<input type="submit" class="btn btn-orange btn-block " value=' Submit ' name='submit'>

							</div>
						</form>
					</div>
					<div class='col-md-6'>

						
						<!--<div class="form-group">-->
						<!--                                    <label>Note (Exam Date)</label>-->
						<!--                                    <input class="form-control"  type='text' placeholder='Examination Date (23-09-2019 to 28-09-2019)' name='exam_note' >-->
						<!--</div>-->
						<!--<div class="form-group">-->
						<!--                                    <label>Instructions </label>-->
						<!--                                    <textarea class="form-control" id='summernote' type='text' rows='8' name='exam_remarks' id='instruction' ></textarea>-->
						<!--</div>-->
						<!--<div class="form-group">-->
						<!--		<label>Select Task </label>-->
						<!--		<select class="form-control" required id='task'>-->
						<!--		   <option value='print_admit'> Print Admit card </option>-->
						<!--		   <option value='print_exam_sheet'> Print Exam Sheet </option>-->
						<!--		</select>-->
						<!--</div>-->


					</div>
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