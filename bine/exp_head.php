<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'account_head';
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row($table_name);
	$id = $fee['id'];
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
		<h1> Expense Head </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Account</a></li>
			<li class="breadcrumb-item active">Exp. Head </li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Create a Expense Head </h3>

				<div class="box-tools pull-right">
					<a class='fa fa-plus btn btn-info btn-sm' href='exp_head' title='Add New Account Head'> </a>
					<button class='ls-modal btn btn-dark btn-xs' data-id='$id' data-account='$account_name' data-txn='$account_type'><i class='fa fa-plus'></i> Expense </button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class='row'>

					<div class="col-lg-3 col-sm-6">

						<form action='update_account_head' id='update_frm' method='post' enctype='multipart/form-data'>
							<input type='hidden' value='<?php echo $id; ?>' name='id'>

							<div class="form-group">
								<label>Expense Type </label>
								<select name='account_type' class='form-control'>
									<?php dropdown($account_head_list, $account_type); ?>
								</select>
							</div>


							<div class="form-group">
								<label> Account Name </label>
								<input class="form-control" required name='account_name' value='<?php echo $account_name; ?>'>
							</div>
							<div class="form-group">
								<label>Status </label>
								<select name='status' class='form-control'>
									<?php dropdown($status_list, $status); ?>
								</select>
							</div>

						</form>
						<button class="btn btn-primary" id='update_btn'> Save </button>
					</div>

					<div class="col-lg-9">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th> Account Type </th>
										<th> Account Name </th>
										<th> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$res = get_all($table_name);
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$account_type = $row['account_type'];
											$account_name = $row['account_name'];
											echo "<tr>";

											echo "<td>" . $account_type . "</td>";
											echo "<td>" . $account_name . "</td>";

											echo "<td align='right'>";

											//   echo "<button class='ls-modal btn btn-dark btn-xs' data-id='$id'   data-account='$account_name'  data-txn='$account_type' ><i class='fa fa-plus'></i></button> ";

											echo btn_view($table_name, $id, $row['cat_name']) .
												btn_edit('exp_head', $id) .
												btn_delete($table_name, $id);

											echo  "</td>";
										}
									}
									?>


								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>



<?php require_once('required/footer2.php'); ?>


<div class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id='appmodal'>
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalCenterTitle"> Expense Entry </h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action='exp_entry' method='post' id='insert_frm' enctype='multipart/form-data'>

					<div class="form-group">
						<label>Expense Type </label>
						<select id='account_type' class='form-control'>
							<?php dropdown($account_head_list, $account_type); ?>
						</select>
					</div>

					<div class="form-group">
						<label> Account Name </label>
						<select name='account_id' class="form-control" id='account_id'>
						</select>
					</div>


					<div class="form-group">
						<label>Txn Date</label>
						<input class="form-control" type='date' value='<?php echo date('Y-m-d'); ?>' name='txn_date' required>
					</div>
					<div class="form-group">
						<label>Txn Amount</label>
						<input class="form-control" type='number' value='' name='txn_amount' required autofocus>
					</div>

					<div class="form-group">
						<label>Txn Mode</label>
						<select name='txn_mode' class='form-control'>
							<?php dropdown($txn_mode_list); ?>
						</select>
					</div>

					<div class="form-group">
						<label>Remarks </label>
						<input class="form-control" placeholder="Details of Transaction" name='txn_remarks' required>
					</div>
				</form>

				<button class="btn btn-success" id='insert_btn'>Save Txn </button>

			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click', '.ls-modal', function(e) {
		e.preventDefault();
		$('#appmodal').modal('show');
		var txn_type = $(this).attr("data-txn");
		$("#account_id").val($(this).attr("data-id"));
		$("#account_name").val($(this).attr("data-account"));
		$("#txn_type").val(txn_type);
		$("#exampleModalCenterTitle").html("Expense " + txn_type);

	});

	$(document).on('change blur', '#account_type', function() {

		var acc_type = $(this).val();
		$.ajax({
			type: "GET",
			url: "required/master_process.php?task=get_account",
			data: 'account_type=' + acc_type,
			success: function(data) {
				console.log(data);
				$("#account_id").html(data);
			}
		});

	});
</script>