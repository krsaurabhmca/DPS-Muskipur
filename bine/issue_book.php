<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Issue Book </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Library</a></li>
			<li class="breadcrumb-item active">Issue Book</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Issue A Book</h3>

				<div class="box-tools pull-right">
					<!--<a class='fa fa-table btn btn-info btn-sm' href='add_area' title='Show Student'> </a>-->

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form action='' method='post'>
					<div class='row'>
						<div class="col-lg-2 col-offset-lg-2"></div>
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Select Class</label>
								<select class="form-control" name='student_class'>
									<?php dropdown($class_list); ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Search Via</label>
								<select class="form-control" name='search_by' required>
									<option value='student_admission'>Admission No</option>
									<option value='student_roll'>Roll No.</option>
									<option value='student_name'>Name </option>
									<option value='student_father'>Father's Name</option>
									<option value='student_mobile'>Mobile No</option>
								</select>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group">

								<div class="form-group has-success">
									<label class="control-label" for="inputSuccess">Enter value</label>
									<input type="text" class='form-control' name='search_text' required autofocus>
								</div>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group">
								<label class="control-label">&nbsp; Alt +S to Search </label>
								<input type="submit" class='btn btn-success btn-md' value='Search Student' name='search' accesskey='s'>
							</div>
						</div>
					</div>
				</form>

				<div class='row'>
					<div class="col-lg-12">
						<hr>
						<!-- Advanced Tables -->
						<?php
						if (isset($_REQUEST['student_class']) and isset($_REQUEST['search_text'])) {
							$sql = "select * from student where status <>'BLOCK' and ";
							$student_class = xss_clean(trim($_REQUEST['student_class']));
							$search_by = xss_clean(trim($_REQUEST['search_by']));
							$search_text = xss_clean(trim($_REQUEST['search_text']));
							if ($student_class <> "") {
								$sql .= "student_class = '$student_class' and ";
							}
							if ($search_by == 'student_roll' or $search_by == 'student_admission' or $search_by == 'student_mobile') {
								$sql .= " $search_by = '$search_text'";
							}
							if ($search_by == 'student_father' or $search_by == 'student_name') {
								$sql .= " $search_by like '%$search_text%'";
							}
						?>

							<div class="table-responsive">
								<table rules='all' border='1' width='100%' cellpadding='5'>
									<thead>
										<tr class='bg-secondary text-light'>
											<th>Adm No.</th>
											<th>Student Name</th>
											<th>Father Name</th>
											<th>Area/Fare</th>
											<th>Class </th>
											<th>Roll No.</th>
											<th>Student Type </th>
											<th>Mobile No </th>
											<th>Dues </th>
											<th>Operation</th>
										</tr>
									</thead>
									<tbody>
									<?php

									$res = direct_sql($sql);
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$student_admission = $row['student_admission'];
											$status = $row['status'];
											echo "<tr class='odd gradeX'>";
											//echo"<td><a href='print_application.php?student_id=$stu_id' target='_blank'>".$row['student_name']."</a></td>";
											echo "<td>" . $row['student_admission'] . "</td>";
											echo "<td>" . $row['student_name'] . "</td>";
											echo "<td>" . $row['student_father'] . "</td>";
											echo "<td>" . $row['student_address1'];
											if (get_data('transport_area', $row['area_id'], 'area_fee')['data'] > 0) {
												echo " (<b>" . get_data('transport_area', $row['area_id'], 'area_fee')['data'] . ")";
											}
											echo "</td>";
											echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
											echo "<td>" . $row['student_roll'] . "</td>";
											echo "<td>" . $row['student_type'] . "</td>";
											echo "<td>" . $row['student_mobile'] . "</td>";
											echo "<td>" . get_data('student_fee', $student_admission, 'current_dues', 'student_admission')['data'] . "</td>";
											echo "<td>";
											//echo "<button title='Issue a Book ' class='issue_book btn btn-success btn-sm' name='Issue Now'>  Now </button>";
											echo "<a href='search_book.php?student_id=$id&action=show' title='Seach Book ' class='btn btn-info btn-sm' >Assign Book </a>";
											echo "</td></tr>";
										}
									}
								}
									?>
									</tbody>
								</table>
							</div>
					</div>
				</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>