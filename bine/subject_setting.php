<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row('subject');
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data('subject', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Subject Setting</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Subject</a></li>
			<li class="breadcrumb-item active">Add Subject</li>
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
					<div class='col-lg-4'>
						<form action='update_subject' id='update_frm' enctype='multipart/form-data'>
							<div class="form-group">
								<label>Subject Name</label>
								<input class="form-control" type='hidden' name='id' value='<?php echo $id; ?>' required>
								<input class="form-control" name='subject_name' value='<?php echo $subject_name; ?>' required>
							</div>
							<div class="form-group">
								<label>Display Order (1,2..)</label>
								<input class="form-control" type='number' name='subject_order' value='<?php echo $subject_order; ?>' required min='0'>
							</div>
						<!--		<div class="form-group">-->
						<!--	<label> Half Yearly(Full Marks)</label>-->
						<!--	<input class="form-control" name='hy_fm' value='<?php echo $hy_fm; ?>' type='number' min='0'>-->
						<!--</div>-->
						<!--<div class="form-group">-->
						<!--	<label> Annual(Full Marks) </label>-->
						<!--	<input class="form-control" name='annual_fm' value='<?php echo $annual_fm; ?>' type='number' required>-->
						<!--</div>-->
      <!--                  <div class="form-group">-->
						<!--	<label>PT1(Full Marks) </label>-->
						<!--	<input class="form-control" name='pt1' value='<?php echo $pt1; ?>' type='number' required>-->
						<!--</div>-->
					</div>
					<div class="col-lg-4 col-sm-6">
						
						<!--<div class="form-group">-->
						<!--	<label> PT2(Full Marks) </label>-->
						<!--	<input class="form-control" name='pt2' value='<?php echo $pt2; ?>' type='number' required>-->
						<!--</div>-->
						<!--<div class="form-group">-->
						<!--	<label> PT3(Full Marks) </label>-->
						<!--	<input class="form-control" name='pt3' value='<?php echo $pt3; ?>' type='number' required>-->
						<!--</div>-->
						<!--<div class="form-group">-->
						<!--	<label> PT4(Full Marks) </label>-->
						<!--	<input class="form-control" name='pt4' value='<?php echo $pt4; ?>' type='number' required>-->
						<!--</div>-->
						<!--<div class="form-group">-->
						<!--	<label> PT5(Full Marks) </label>-->
						<!--	<input class="form-control" name='pt5' value='<?php echo $pt5; ?>' type='number' required>-->
						<!--</div>-->
						<!--<div class="form-group">-->
						<!--	<label> PT6(Full Marks) </label>-->
						<!--	<input class="form-control" name='pt6' value='<?php echo $pt6; ?>' type='number' required>-->
						<!--</div>-->


					</div>
					<div class="col-lg-4 col-sm-6">



						<div class="form-group">
							<label>Applicable Class (Select Multiple)</label>
							<?php check_list('student_class', $class_list, $student_class, '200px'); ?>

						</div>
						<div class="form-group">
							<div class="checkbox text-sm text-danger">
								<?php if ($is_extra == 1) {
									$checked = 'checked';
								} ?>
								<input type="checkbox" id="extra_subject" value='<?php echo $is_extra; ?>' name='is_extra' <?php echo $checked; ?>>
								<label for="extra_subject">Marks As Additional</label>
							</div>
						</div>
						</form>

						<button class="btn btn-primary" id='update_btn'> Add New Subject </button>




					</div>
				</div>
				<hr>
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th> # </th>
								<!--<th> Category </th>-->
								<th> Subject Name </th>
								<th> Class</th>
								<!--<th> SE </th>-->
								<!--<th> NB </th>-->
								<!--<th> PT </th>-->
								<!--<th> Term-1 </th>-->
								<!--<th> Term-2 </th>-->
								<!--<th> Annual </th>-->
								<th> Action </td>
							</tr>
						</thead>
						<tbody>

							<?php
							$query = "select * from subject where status='ACTIVE'";

							$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
							while ($row = mysqli_fetch_array($res)) {
								$ord = $row['subject_order'];
								$id = $row['id'];
								echo "<tr><td>" . $row['subject_order'] . "</td>";
								// echo "<td>" . $row['subject_type'] . "</td>";
								echo "<td>" . $row['subject_name'] . "</td>";
								echo "<td>" . $row['student_class'] . "</td>";
								// echo "<td>" . $row['se_fm'] . "</td>";
								// echo "<td>" . $row['nb_fm'] . "</td>";
								//	echo "<td>".$row['pt_fm'] ."</td>";	
								// echo "<td>" . $row['hy_fm'] . "</td>";
								// echo "<td>" . $row['annual_fm'] . "</td>";
								// echo "<td>" . $row['annual'] . "</td>";

							?>
								<td>
									<a href='subject_setting.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
									<span class='delete_subject btn btn-danger btn-sm' data-table='subject' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
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
	</section>
</div>
<?php require_once('required/footer2.php'); ?>