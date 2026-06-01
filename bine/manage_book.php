<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));

if (isset($_GET['cat_id']) and $_GET['cat_id'] <> '') {
	$res = get_all('book_list', '*', array('cat_id' => $_GET['cat_id']));
} else if (isset($_GET['pub_id']) and $_GET['pub_id'] <> '') {
	$res = get_all('book_list', '*', array('pub_id' => $_GET['pub_id']));
} else {
	$res = get_all('book_list');
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Manage Books
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Library</li>
			<li class="breadcrumb-item active">Manage Book</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Details of Books (<?php echo get_data('book_cat', $_GET['cat_id'], 'cat_name')['data']; ?> ) </h3>
						<div class="box-tools pull-right">
							<a class='fa fa-plus btn btn-success btn-sm' title='New Book' href='add_book'> </a>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Category</th>
										<th>Book No.</th>
										<th>Name</th>
										<th>Author</th>
										<th>Publisher</th>
										<th>Price</th>
										<th>Remarks</th>
										<th class='text-right'>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if ($res['count'] > 0) {
										$fee = 0;
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$cat_name = get_data('book_cat', $row['cat_id'], 'cat_name')['data'];
									?>
											<tr>
												<td><?php echo $cat_name; ?></td>
												<td><?php echo $row['accession_no']; ?></td>
												<td><?php echo $row['book_name']; ?></td>
												<td><?php echo $row['author_name']; ?></td>
												<td><?php echo $row['publisher_name']; ?></td>
												<td><?php echo $row['book_price']; ?></td>
												<td><?php echo $row['remarks']; ?></td>
												<td class='text-right'>
													<?php echo btn_view('book_list', $id, $row['book_name']); ?>
													<?php echo btn_edit('add_book', $id); ?>
													<?php echo btn_delete('book_list', $id); ?>
												</td>
											</tr>
									<?php }
									} ?>
								</tbody>
							</table>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php require_once('required/footer2.php'); ?>