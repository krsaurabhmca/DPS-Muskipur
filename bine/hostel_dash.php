<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
//$today ='2021-10-21';
// if($user_type !='Account' or $user_type !='Admin')
// {
//     echo "<script> window.location ='dashboard'  </script>";
// }
?>
<style>
	.tbl tr:last-child {
		background: #ffbf36;
	}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Hostel
			<?php if ($user_type == 'ADMIN') { ?>
				<small><a href='dashboard'>( Dashboard)</a></small>
			<?php } ?>
		</h1>
		<ol class="breadcrumb">
			<form action='required/master_process?task=change_session' method='post' role="form">
				<select name='session_year' class='form-control float-left' onchange='submit()'>
					<?php dropdown_with_key($session_list, $db_name); ?>
				</select>
			</form>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
							<h3><?php echo "0"; ?></h3>
						<p>Total Student</p>
					</div>
					<div class="icon">
						<i class="fa fa-male"></i>
					</div>
					<a href="#" class="small-box-footer"> <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
							<h3><?php echo "0"; ?></h3>

						<p>Vegiterian</p>
					</div>
					<div class="icon">
						<i class="fa fa-user"></i>
					</div>
					<a href="collection_report?txn_mode=Bank" class="small-box-footer">Bank Transaction <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
							<h3><?php echo "0"; ?></h3>

						<p>On Leave</p>
					</div>
					<div class="icon">
						<i class="fa fa-truck"></i>
					</div>
					<a href="#" class="small-box-footer">Add New<i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo "0"; ?></h3>

						<p>In House</p>
					</div>
					<div class="icon">
						<i class="fa fa-book"></i>
					</div>
					<a href="#" class="small-box-footer"><i class="fa fa-arrow-right"></i></a>
				</div>
			</div>
		</div>



		<div class="row">

			<div class="col-xl-12 connectedSortable">
				<div class="box box-info">
					<div class="box-header">
						<h3 class="box-title">Quick Access</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class='row'>
							<div class='col-md-2 text-center p-2'>
								<a href='#' title='Collect Fee'>
									<img src='icon/girl.png' height='50px' />
									<br> Release Student
								</a>
							</div>

							<div class='col-md-2 text-center p-2'>
								<a href='#' title='Demand Slip'>
									<img src='icon/girl.png' height='50px' />
									<br> Admit Student
								</a>
							</div>


							<div class='col-md-2 text-center p-2'>
								<a href='#' title='Collection Report'>
									<img src='icon/girl.png' height='50px' />
									<br> Current Status </a>
							</div>


							<div class='col-md-2 text-center p-2'>
								<a href='add_area' title=' Transport Area '>
									<img src='icon/girl.png' height='50px' />
									<br> Ward Wise Report </a>
							</div>
							<div class='col-md-2 text-center p-2'>
								<a href='#' title='Collection Report'>
									<img src='icon/girl.png' height='50px' />
									<br> In/Out History</a>
							</div>


							<div class='col-md-2 text-center p-2'>
								<span id='#' title='Add Complain'>
									<img src='icon/girl.png' height='50px' />
									<br> Complain Bok </a>
							</div>

						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
		


		<div class="row">
			<div class="col-xl-12 connectedSortable">
				<div class="box box-info">
					<div class="box-header">
						<h3 class="box-title">Student In/Out of the Month </h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">

						<div id="student_in_out" style="width: 100%; height: 250px;"></div>
					</div>
				</div>
			</div>
		</div>

	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php require_once('required/footer.php'); ?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
	google.charts.load('current', {
		'packages': ['corechart']
	});
	google.charts.setOnLoadCallback(income_exp_graph);

	function income_exp_graph() {
		var data = google.visualization.arrayToDataTable(<?php echo income_exp_graph(); ?>);

		var options = {
			title: 'Income Vs Expence',
		};

		var chart = new google.visualization.LineChart(document.getElementById('income_exp_graph'));

		chart.draw(data, options);
	}
</script>