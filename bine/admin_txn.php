<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'admin_txn';
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
		<h1> Admin Transaction </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Account</a></li>
			<li class="breadcrumb-item active">Admin Transaction </li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Add and Update Transaction <?php // echo $user_id; 
																	?> </h3>

				<div class="box-tools pull-right">
					<a class='fa fa-plus btn btn-info btn-sm' href='admin_txn' title='Add Fee Head'> </a>

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">

				<div class='row'>

					<div class="col-lg-3 col-sm-6">

						<form action='admin_txn' id='update_frm' method='post' enctype='multipart/form-data'>
							<input type='hidden' value='<?php echo $id; ?>' name='id'>

							<div class="form-group">
								<label>Transaction Type </label>
								<select class="form-control" name='txn_type' id='txn_type' required>
									<option value='Expense'>Expense</option>
									<option value='Income'>Income</option>
								</select>
							</div>

							<div class="form-group">
								<label>Transcation By</label>
								<select class="form-control" name='account_name' required>
									<option value='Director'>Director</option>
									<option value='Principal'>Principal</option>
								</select>
							</div>
							<div id='exp_area'>
								<div class="form-group">
									<label class='text-danger'>Expense Type </label>
									<select id='account_type' class='form-control'>
										<?php dropdown($account_head_list, $account_type); ?>
									</select>
								</div>

								<div class="form-group">
									<label class='text-danger'> Account Name </label>
									<select name='account_id' class="form-control" id='account_id'>

									</select>
								</div>
							</div>
							<div class="form-group">
								<label>Txn Date </label>
								<input class="form-control" name='txn_date' type='date' value='<?php echo $txn_date; ?>'>

							</div>

							<div class="form-group">
								<label>Txn Amount </label>
								<input class="form-control" name='txn_amount' type='number' value='<?php echo $txn_amount; ?>'>

							</div>

							<div class="form-group">
								<label>Payment Mode</label>
								<select class="form-control" name='txn_mode' required>
									<option value='Cash'>Cash</option>
									<option value='Bank'>Bank</option>
								</select>
							</div>

							<div class="form-group">
								<label>Remarks (if Any) </label>
								<input class="form-control" name='txn_remarks' type='text' value='<?php echo $txn_remarks; ?>'>

							</div>



						</form>
						<button class="btn btn-primary" id='update_btn'> Save </button>



					</div>

					<div class="col-lg-9">
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th> Type </th>
										<th> Account </th>
										<th> Date </th>
										<th> Amount </th>
										<th> Mode </th>
										<th> Remarks </th>
										<th> Operation </td>
									</tr>
								</thead>
								<tbody>

									<?php
									$query = "select * from $table_name where status='ACTIVE' order by txn_date";
									$i = 1;
									$res = mysqli_query($con, $query) or die(" Transport Error : " . mysqli_error($con));
									while ($row = mysqli_fetch_array($res)) {
										$id = $row['id'];
										echo "<td>" . $row['txn_type'] . "</td>";
										echo "<td>" . $row['account_name'] . "</td>";
										echo "<td>" . $row['txn_date'] . "</td>";
										echo "<td>" . $row['txn_amount'] . "</td>";
										echo "<td>" . $row['txn_mode'] . "</td>";
										echo "<td>" . $row['txn_remarks'] . "</td>";

									?>
										<td>
											<?php echo btn_view($table_name, $id, $row['account_name']); ?>

											<button data-id='<?php echo $id; ?>' Value='Cancel' title='Cancel Transaction' class='cancel_admin_txn btn btn-danger btn-sm'>
												<i class="fa fa-times-circle-o" aria-hidden="true"></i></button>

										</td>
										</tr>
									<?php
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

	$(document).on('click', '#account_type', function() {

		var acc_type = $(this).val();
		$.ajax({
			type: "GET",
			url: "master_process.php?task=get_account",
			data: 'account_type=' + acc_type,
			success: function(data) {
				console.log(data);
				$("#account_id").html(data);
			}
		});

	});

	$(document).on('change', "#txn_type", function() {
		var txn_type = $(this).val();
		if (txn_type == 'Expense') {
			$("#exp_area").css('display', 'BLOCK');
		} else {
			$("#exp_area").css('display', 'NONE');
		}
	});
</script>