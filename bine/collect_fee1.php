<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Collect Fee</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Fee</a></li>
			<li class="breadcrumb-item active">Collect fee</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			
			<!-- /.box-header -->
            <form action='collect_fee1.php' method='post' >
                <div class="form-group">
					<label>Select Class</label>
					<select class="form-control" name='student_class'>
						<?php dropdown($class_list); ?>
					</select>
				</div>
               
				<div class="form-group has-success">
					<label class="control-label" for="inputSuccess">Enter value</label>
					<input type="text" name='search_text' required>
				</div>
	
            </form>
            
			<div class="box-body">
				
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
									<input type="text" class='form-control' name='search_text' required>
								</div>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group">
								<label class="control-label">&nbsp; Enter to Search</label>
								<input type="submit" class='btn btn-success btn-md' value='Search Student' id='searchbtn' name='search' >
							</div>
						</div>
					</div>
				</form>
            </div>
				
	</section>
</div>
<?php require_once('required/footer.php'); ?>
