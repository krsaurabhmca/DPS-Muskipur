<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
	$student = decode($_GET['link']);
	$id = $student['id'];
} else {

	$student = insert_row('enquiry');
	$id = $student['id'];
}

if ($id != '') {
	$res = get_data('enquiry', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
		if ($student_admission == '') {
			$student_admission = $student['id'];
		}
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> New Enquiry</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Enquiry</a></li>
			<li class="breadcrumb-item active">New Enquiry</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Enquiry Details </h3>

				<div class="box-tools pull-right">
					<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form id='update_frm' action='update_enquiry'>
					<div class="row">

						<div class="col-lg-6">
							<div class="form-group row">

								<label for="example-text-input" class="col-sm-4 col-form-label">Name</label>
								<div class="col-sm-8">
									<input type='hidden' name='id' value='<?php echo $id; ?>' />
									<input class="form-control border-warning" type="text" value='<?php echo $student_name; ?>' name="student_name" required>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Father's Name</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $student_father; ?>' name='student_father' required>
								</div>
							</div>


							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Gender</label>
								<div class="col-sm-8">
									<select name='student_sex' class='form-control' required>
										<?php dropdown($gender_list, $gender); ?>
									</select>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Lead Source</label>
								<div class="col-sm-8">
									<select name='source' class='form-control' required>
										<?php dropdown($source_list, $source); ?>
									</select>
								</div>
							</div>
							
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Desired Class </label>
								<div class="col-sm-8">
									<select name='student_class' class='form-control' required>
										<?php dropdown($class_list, $student_class); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Date</label>
								<div class="col-sm-8">
									<?php if ($enquiry_date != "") { ?>
										<input type="date" name="enquiry_date" class="form-control" value="<?php echo  date('Y-m-d', strtotime($enquiry_date)); ?>" required>
									<?php } else { ?>
										<input type="date" name="enquiry_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
									<?php } ?>
								</div>
							</div>
						</div>


						<div class="col-lg-6">

							<div class="form-group row">
								<label class="col-sm-4 col-form-label"> Address</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" name='student_address'><?php echo $student_address; ?></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Mobile No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="tel" value='<?php echo $student_mobile; ?>' pattern="[6789][0-9]{9}" name="student_mobile" required maxlength="10" minlength="10">
								</div>
							</div>
							
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Whatsapp No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="tel" value='<?php echo $student_whatsapp; ?>' pattern="[6789][0-9]{9}" name="student_whatsapp" required maxlength="10" minlength="10">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label"> Remarks (if Any)</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" name='remarks'><?php echo $remarks; ?></textarea>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Prospectus Fee</label>
								<div class="col-sm-8">
									<input class="form-control" type="number" value='<?php echo $form_fee; ?>' pattern="[6789][0-9]{9}" min='0' name="form_fee" required>
								<p class='text-muted'>In case of Prospectus Sale </p>
								</div>
							
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