<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'notice';
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('notice');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('notice', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Add Notice </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Notice</a></li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add Notice </h3>

				<div class="box-tools pull-right">
					<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form id='update_frm' action='update_notice'  method="post">
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label for="example-text-input" class="col-sm-4 col-form-label">Notice Date</label>
								<div class="col-md-12">
								    <input type='hidden' name='id' value='<?php echo $id; ?>' >
									<input class="form-control" type="date"  data-table="notice" name='notice_date' value='<?= $notice_date ?>'>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">

								<label for="example-text-input" class="col-sm-4 col-form-label">Notice Title</label>
								<div class="col-sm-8">
									<input class="form-control border-warning" type="text" value='<?php echo $notice_title; ?>' name="notice_title" required>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label for="example-text-input" class="col-sm-4 col-form-label">Attachment</label>
								<div class="col-sm-8">
								    <!--<input type="hidden" name="notice_attachment" id="target_image" value="<?= $notice_attachment ?>">-->
									<input class="upload_img form-control" type="file" id="image" accept="image" name='notice_attachment' data-table="gallery" data-field="image">
								</div>
							</div>
						</div>

						<div class="col-lg-8">
							<div class="form-group">
								<label for="example-text-input" class="col-sm-4 col-form-label">Notice Details</label>
								<div class="col-sm-8">
									<textarea name="notice_details"  rows="3" class="form-control"><?php echo $notice_details; ?></textarea>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label for="example-text-input" class="col-sm-4 col-form-label">Status </label>
								<div class="col-sm-8">
									<select name='status' class='form-control' required>
										<?php dropdown($status_list, $status); ?>
									</select>
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
<?php require_once('required/footer2.php'); ?>