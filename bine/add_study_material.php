<?php require_once('required/header.phpa'); ?>
<?php require_once('required/menu.php');
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('study_material');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('study_material', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.css" rel="stylesheet">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Study Material</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item active">Study Material</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Subject Details</h3>

				<div class="box-tools pull-right">
					<a class='fa fa-plus btn btn-info btn-sm' href='subject_setting' title='Add Fee Head'> </a>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<div class='row'>
					<div class='col-lg-3'>
						<form action='update_subject' id='update_frm' enctype='multipart/form-data'>
							<div class="form-group">
								<label>Select Subject Name</label>
								<input class="form-control" type='hidden' name='id' value='<?php echo $id; ?>' required>
								<select class="form-control" name='subject_type' required>
									<?php echo dropdown_list('subject', 'id', 'subject_name'); ?>'
								</select>
							</div>
					</div>
					<div class='col-lg-3'>
						<div class="form-group">
							<label>Select Class</label>
							<select class="form-control" name='subject_type' required>
								<?php echo dropdown($class_list); ?>'
							</select>
						</div>
					</div>
					<div class='col-lg-3'>
						<div class="form-group">
							<label>Material Type</label>
							<select class="form-control" name='category' required>
								<?php echo dropdown($smc_list, $category); ?>'
							</select>
						</div>
					</div>

					<div class="col-lg-8 col-sm-6">
						<div class="form-group">
							<label>Details </label>
							<textarea id="summernote" name='docs_details'> </textarea>
						</div>



					</div>
					<div class="col-lg-4 col-sm-6">
						<div class="form-group">
							<label>Youtube Link <small> Use Unlisted Video </small></label>
							<input type='url' class="form-control" placeholder="Youtube Video Lecture " name='yt_link' required>

						</div>

						<div class="form-group">
							<label>Google Meet <small> (Live Link) </small> </label>
							<input type='url' class="form-control" placeholder="Meet Live Link" name='gm_link' required>

						</div>

						<div class="form-group">
							<label>Material Status </label>
							<select class="form-control" name='status' required>
								<?php echo dropdown($status_list, $status); ?>'
							</select>
						</div>
						</form>
						<form id='uploadForm' enctype='multipart/form-data'>
							<div id='display'></div>
							<div class="form-group">
								<label>Upload Photograph (Max 50 KB) </label>
								<input type='file' name='uploadimg' id='uploadimg' accept='image'>
								<br><small> Only Jpg and Png image upto 50KB. </small>
							</div>
						</form>


						<button class="btn btn-primary" id='update_btn'> Add Material </button>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.js"></script>

</body>
<script>
	$('#summernote').summernote({
		placeholder: 'Type your text here',
		tabsize: 1,
		height: 300
	});
</script>

</html>