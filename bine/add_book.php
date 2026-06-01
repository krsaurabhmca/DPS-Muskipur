<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'book_list';
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
		<h1> Book Details </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Library</a></li>
			<li class="breadcrumb-item active">New Book</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add /Update Book Details </h3>

				<div class="box-tools pull-right">
					<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form id='update_frm' action='update_book'>
					<div class="row">

						<div class="col-lg-6">
							<div class="form-group row">

								<label for="example-text-input" class="col-sm-4 col-form-label">Book Name</label>
								<div class="col-sm-8">
									<input type='hidden' name='id' value='<?php echo $id; ?>' />
									<input class="form-control border-warning" type="text" value='<?php echo $book_name; ?>' name="book_name" required>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">ISBN No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $isbn_no; ?>' name='isbn_no'>
								</div>
							</div>


							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Select Publisher </label>
								<div class="col-sm-8">
									<select name='pub_id' class='form-control' required>
										<?php dropdown_list('book_pub', 'id', 'pub_name', $pub_id); ?>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Select Category </label>
								<div class="col-sm-8">
									<select name='cat_id' class='form-control' required>
										<?php dropdown_list('book_cat', 'id', 'cat_name', $cat_id); ?>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Author Name</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $author_name; ?>' name="author_name">
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Accession No. </label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $accession_no; ?>' name="accession_no" required>
								</div>
							</div>
						</div>


						<div class="col-lg-6">



							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Book No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $book_no; ?>' name="book_no" required>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Publisher Year</label>
								<div class="col-sm-8">
									<input class="form-control" type="number" value='<?php echo $publish_year; ?>' pattern="[0-9]{4}" name="publish_year">
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Edition </label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $edition; ?>' name="edition">
								</div>
							</div>


							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Book Price</label>
								<div class="col-sm-8">
									<input class="form-control" type="number" value='<?php echo $book_price; ?>' pattern="[6789][0-9]{9}" min='0' name="book_price" required>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Status </label>
								<div class="col-sm-8">
									<select name='status' class='form-control' required>
										<?php dropdown($book_status); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label"> Remarks (if Any)</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="2" name='remarks'><?php echo $remarks; ?></textarea>
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