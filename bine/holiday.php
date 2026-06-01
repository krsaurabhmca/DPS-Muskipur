<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('holiday');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('holiday', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>
<div class="content-wrapper">
	<section class="content">
		<div class='box box-default'>

			<div class="box-header with-border">
				<h3 class="box-title">Mange Holiday </h3>

				<div class="box-tools pull-right">
					<div class='float-right'><a class='fa fa-plus text-orange' href='holiday' title='Add Fee Head'></a></div>
				</div>
			</div>
			<div class="box-body">

				<div class="row">
					<div class="col-lg-4">
						<form action='update_holiday' method='post' id='update_frm' enctype='multipart/form-data'>

							<div class="form-group">
								<label>Holiday Date</label>

								<input class="form-control" value='<?php echo $id; ?>' name='id' type='hidden'>
								<input class="form-control" value='<?php echo $holiday_date; ?>' name='holiday_date' type='date'>
							</div>
							<div class="form-group">
								<label>Holiday Name</label>

								<input class="form-control" value='<?php echo $holiday_name; ?>' name='holiday_name' required>
							</div>


							<div class="form-group">
								<label>Status</label>
								<select class="form-control" name='status' required>
									<option value='PUBLIC'>PUBLIC (To all)</option>
									<option value='PRIVATE'>PRIVATE(Only Staff)</option>
									<option value='HIDE'>HIDE (Remove)</option>
								</select>
							</div>
							<!--<div class="checkbox">
                            <label>
                            <input type="checkbox" value="yes" name='checksms'> Send Email to All Clients
                            </label>
                        </div>-->
						</form>
						<input type="submit" class="btn btn-info" value='Publish Holiday ' id='update_btn'>
					</div>


					<div class="col-lg-8">

						<div class="table-responsive">
							<table class="table" id='example1'>
								<thead>
									<tr>

										<th>Date</th>
										<th>Name</th>
										<th>For </th>
										<th>Operation</th>



									</tr>
								</thead>
								<tbody>
									<?php
									$res = get_all('holiday');
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											echo "<tr>";
											$id = $row['id'];
											echo "<td> " . date('d M Y', strtotime($row['holiday_date'])) . "</td>";
											echo "<td> " . $row['holiday_name'] . "</td>";

											echo "<td> " . $row['status'] . "</td>";
									?>
											<td align='right'>
												<a href='?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
												<span class='delete_btn btn btn-danger btn-sm' data-table='transport_area' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
												</tr>
										<?php }
									} ?>
								</tbody>
							</table>
						</div>
					</div>



				</div>
			</div>
	</section>
</div>
<?php require_once('required/footer2.php'); ?>