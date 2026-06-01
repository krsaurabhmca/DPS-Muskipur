<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
if ($user_type == 'ACCOUNT') {
	echo "<script> window.location ='acc_dash'  </script>";
}
if ($user_type == 'STAFF') {
	echo "<script> window.location ='teacher_dashboard.php'  </script>";
}
?>
<style>
	.tbl tr:last-child {
		background:purple;
		color:#e5e5e5;
	}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Dashboard 
			<?php if($user_type=='ADMIN') { ?>
			<small>
			<a class="btn btn-secondary" href="acc_dash"> Account </a>

			<a class="btn btn-secondary" href="studyplant" > Teacher </a>
            </small>
            <?php } ?> 
            <?php if($user_type=='DBA') { ?>
			<small>
			    <a class="dropdown-item" href="studyplant">Teacher</a>
            </small>
            <?php } ?> 
		</h1>
		<ol class="breadcrumb">
			<!--<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li> -->
            <form action='required/master_process.php?task=change_session' method='post' role="form">

				<select name='session_year' class='form-control float-left' onchange='submit()'>
					<?php dropdown_with_key($session_list, $db_name); ?>
				</select>
			</form>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
	    <div class="alert alert-warning alert-dismissable">
					<button type="button" class="close text-light" data-dismiss="alert" aria-hidden="true">×</button> 
					Welcome to <?=$inst_name; ?> Thanks for Choosing Bine PRO 4.0
		</div>
		<div class="row">
			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo get_all('student', '*', array('status' => 'ACTIVE'))['count']; ?></h3>

						<p>Student</p>
					</div>
					<div class="icon">
						<i class="fa fa-male"></i>
					</div>
					<a href="add_student" class="small-box-footer">New Registration <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo get_all('employee', '*', array('status' => 'ACTIVE'))['count']; ?></h3>

						<p>Employee </p>
					</div>
					<div class="icon">
						<i class="fa fa-user"></i>
					</div>
					<a href="manage_salary" class="small-box-footer">Pay Salary <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo get_all('transport_area', '*', array('status' => 'ACTIVE'))['count']; ?></h3>

						<p>Transport Area</p>
					</div>
					<div class="icon">
						<i class="fa fa-truck"></i>
					</div>
					<a href="add_area" class="small-box-footer">Add New Area <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>

			<div class="col-xl-3 col-md-6 col-6">
				<!-- small box -->
				<div class="small-box bg-yellow">
					<div class="inner">
						<h3><?php echo get_all('enquiry', '*', array('status' => 'ACTIVE'))['count']; ?></h3>

						<p>Enquiry </p>
					</div>
					<div class="icon">
						<i class="fa fa-question"></i>
					</div>
					<a href="add_enquiry" class="small-box-footer">New Enquiry <i class="fa fa-arrow-right"></i></a>
				</div>
			</div>
		</div>

		<div class='row' >
			<div class="col-xl-3 col-md-6 col-12">
				<div class="info-box">
					<span class="info-box-icon push-bottom bg-yellow"><i class="ion ion-ios-pricetag-outline"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">Student</span>
						<span class="info-box-number"></span>

						<div class="progress">
							<div class="progress-bar bg-yellow" style="width: 45%"></div>
						</div>
						<span class="progress-description text-muted">
							<a href='student_status?status=&student_type=HOSTELER'><?php echo get_all('student', '*', array('student_type' => 'HOSTELER'))['count']; ?> Hostel</a> |
							<a href='student_status?status=INACTIVE&student_type='><?php echo get_all('student', '*', array('status' => 'INACTIVE'))['count']; ?> Inactive </a>
						</span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
			<div class="col-xl-3 col-md-6 col-12">
				<div class="info-box">
					<span class="info-box-icon push-bottom bg-yellow"><i class="ion ion-ios-eye-outline"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">Transport Area </span>
					

						<div class="progress">
							<div class="progress-bar bg-yellow" style="width: 40%"></div>
						</div>
						<span class="progress-description text-muted">
						    	<span class="info-box-number"><a href='add_area'><?php echo get_all('transport_area', '*', array('status' => 'ACTIVE'))['count']; ?></a></span>
							<!--<a href='student_status?status=&student_type=TRANSPORT'><?php echo get_all('student', '*', array('student_type' => 'TRANSPORT'))['count']; ?> Students </a>-->
						</span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
			<div class="col-xl-3 col-md-6 col-12">
				<div class="info-box">
					<span class="info-box-icon push-bottom bg-yellow"><i class="ion ion-ios-cloud-download-outline"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">Employee</span>
					

						<div class="progress">
							<div class="progress-bar bg-yellow" style="width: 85%"></div>
						</div>
						<span class="info-box-number"><?php echo get_all('employee', '*', array('status' => 'ACTIVE'))['count']; ?></span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
			<div class="col-xl-3 col-md-6 col-12">
				<div class="info-box">
					<span class="info-box-icon push-bottom bg-yellow"><i class="ion-ios-chatbubble-outline"></i></span>

					<div class="info-box-content">
						<span class="info-box-text">Certificate Issued </span>
						
						<div class="progress">
							<div class="progress-bar bg-yellow" style="width: 50%"></div>
						</div>
						<span class="progress-description text-muted">
						<span class="info-box-number"><?php echo get_all('tbl_tc')['count']; ?></span>
						</span>
					</div>
					<!-- /.info-box-content -->
				</div>
				<!-- /.info-box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<div class="row">
		

			<div class="col-xl-9 connectedSortable" >
				<div class="box box-info" style="background-image: url(../images/user_info2.jpg);background-size:cover;">
					<div class="box-header">
						<h4 class="box-title text-light">Quick Access</h4>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class='row text-light'>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='add_student' title='New Admission'>
									<img src='icon/student.png' height='50px' />
									<br> New Admission
								</a>
							</div>

							<div class='col-md-2 col-6 text-center p-2'>
								<a href='collect_fee' title='Collect Fee'>
									<img src='icon/printer.png' height='50px' />
									<br> Pay Fee
								</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='generate_demand' title='Demand Slip'>
									<img src='icon/calculation.png' height='50px' />
									<br> Create Demand
								</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='send_sms' title='Send SMS'>
									<img src='icon/email.png' height='50px' />
									<br> Send SMS
								</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='collect_fee' title='Student Ledger'>
									<img src='icon/girl.png' height='50px' />
									<br> Student Ledger</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='collection_report' title='Collection Report'>
									<img src='icon/calc.png' height='50px' />
									<br> Collection Report</a>
							</div>

							<div class='col-md-2 col-6 text-center p-2'>
								<a href='create_certificate' title=' Create Certificate'>
									<img src='icon/test.png' height='50px' />
									<br> Certificate</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='add_area' title=' Transport Area '>
									<img src='icon/school-bus.png' height='50px' />
									<br> Transport Area </a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='manage_account' title='Collection Report'>
									<img src='icon/ereader.png' height='50px' />
									<br> Expense Report</a>
							</div>

							<div class='col-md-2 col-6 text-center p-2'>
								<a href='issue_book' title=' Issue A Book'>
									<img src='icon/book.png' height='50px' />
									<br> Issue A Book </a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='book_return' title=' Book Return'>
									<img src='icon/book_1.png' height='50px' />
									<br> Book Return</a>
							</div>
							<div class='col-md-2 col-6 text-center p-2'>
								<a href='date_wise_report' title='Date with Report'>
									<img src='icon/writing.png' height='50px' />
									<br> Day Book </a>
							</div>

						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			
				<div class="col-xl-3 col-lg-6 col-12">
				<!-- Widget: user widget style 1 -->
				<div class="box box-widget widget-user-2">
					<!-- Add the bg color to the header using any of the bg-* classes -->
					<div class="widget-user-header bg-yellow p-2">
						<b class='text-center'><i class='fa fa-dashboard'></i> Student Statics </b>
					</div>
					<div class="box-footer no-padding">
						<ul class="nav d-block nav-stacked">
							<li class="nav-item"><a href="student_status?status=&student_type=TRANSPORT" class="nav-link">Transport <span class="pull-right badge bg-blue"><?php echo get_all('student', '*', array('student_type' => 'TRANSPORT'))['count']; ?></span></a></li>
							<li class="nav-item"><a href="student_status?status=&student_type=HOSTELER" class="nav-link">Hostel <span class="pull-right badge bg-green"><?php echo get_all('student', '*', array('student_type' => 'HOSTELER'))['count']; ?></span></a></li>
							<li class="nav-item"><a href="#" class="nav-link">Boys <span class="pull-right badge bg-yellow"><?php echo get_all('student', '*', array('student_sex' => 'MALE'))['count']; ?></span></a></li>
							<li class="nav-item"><a href="#" class="nav-link">Girls <span class="pull-right badge bg-red"><?php echo get_all('student', '*', array('student_sex' => 'FEMALE'))['count']; ?></span></a></li>
							<li class="nav-item"><a href="student_status?status=INACTIVE&student_type=" class="nav-link">Inactive <span class="pull-right badge bg-black"><?php echo get_all('student', '*', array('status' => 'INACTIVE'))['count']; ?></span></a></li>
						</ul>
					</div>
				</div>
				<!-- /.widget-user -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->


		<div class="row">

			<div class="col-xl-12 connectedSortable">
				<div class="box box-info">
					<div class="box-header">
						<h3 class="box-title">Student Analysis & Absent Student</h3>
                        
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body" style='overflow-x:scroll'>
						<table id="example1" class="table table-bordered table-striped">
							<tr class='bg-dark text-light'>
								<th> Section / Class</th>
								<?php
								foreach (array_filter($class_list) as $class) {
									echo "<th>" . $class . " </th>";
								}
								?>
							</tr>
							<?php
							foreach (array_filter($section_list) as $section) {
								echo "<tr><th>" . $section . "</th>";

								foreach (array_filter($class_list) as $class) {
									$link = encode('student_class=' . $class . '&student_section=' . $section);
									
									$att = today_att_status($class, $section,'A');
									echo "<th><a href='manage_student?link=$link'>" . studentcount($class, $section) ." <span class='badge badge-danger'>". $att. "</span></a></th>";
									//echo "<th><a href='student_status?link=$link'>" . studentcount($class, $section) . "</a></th>";
								}
								echo "</tr>";
							}
							?>
							<tr>
								<th> Total </th>
								<?php
								foreach (array_filter($class_list) as $class) {
									echo "<th><a href='manage_student?student_class=$class&student_section='>" . studentcount($class) . "</a></th>";
									//echo "<th><a href='student_status?student_class=$class&student_section='>" . studentcount($class) . "</a></th>";
								}
								?>
							</tr>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->

		<div class="row">
			<div class="col-xl-8 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Collection of Month & Avg Attendance</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div id="payment_chart" style="width: 100%; height: 250px;"></div>
					</div>
					<!-- /.box-body -->

				</div>
			</div>
			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Admission of Month</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div id="admission_chart" style="width: 100%; height: 250px;"></div>
					</div>
					<!-- /.box-body -->

				</div>
			</div>

		</div>
		<div class="row">

			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Last 3 Payments </h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<ul class="products-list product-list-in-box">
							<?php $respay = get_multi_data('receipt', array('paid_date' => $today,'status' =>'PAID'), 'order by id desc limit 3');

							if ($respay['count'] > 0) {
								foreach ($respay['data'] as $pd) {
							?>
									<li class="item">
										<div class="product-img">
											<?php
											$rid = $pd['id'];
											$img =  get_data('student', $pd['student_id'], 'student_photo')['data']; ?>
											<img src="required/upload/<?php echo $img; ?>" alt="Student Image">
										</div>
										<div class="product-info">
											<a href="receipt.php?receipt_id=<?php echo  $rid; ?>" target='_blank' class="product-title"> <?php echo get_data('student', $pd['student_id'], 'student_name')['data']; ?> <span class="label bg-yellow pull-right"><?php echo $pd['paid_amount']; ?> </span></a>
											<span class="product-description">
												<?php echo $pd['paid_month']; ?>
											</span>
											<span class="product-description">
												At <?php echo date('h:i A', strtotime($pd['created_at'])); ?>
											</span>
										</div>
									</li>
							<?php }
							} ?>
						</ul>
					</div>
					<!-- /.box-body -->
					<div class="box-footer text-center">
						<a href="collection_report" class="uppercase">View All Collection</a>
					</div>
					<!-- /.box-footer -->
				</div>
			</div>

			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Today Payment</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<ul class="products-list product-list-in-box">
							<table class='tbl table'>
								<?php
								$payment = all_exp($today, $today);
								foreach ($payment as $hd => $e_amt) {
								?>
									<tr>
										<td><?php echo add_space($hd); ?> </td>
										<td align='right'> <?php echo $e_amt; ?></td>
									</tr>
								<?php } ?>
							</table>
						</ul>
					</div>
					<!-- /.box-body -->
					<div class="box-footer text-center">
						<a href="manage_account" class="uppercase">View All Payments</a>
					</div>
					<!-- /.box-footer -->
				</div>
			</div>

			<div class="col-xl-4 connectedSortable">
				<!-- PRODUCT LIST -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Today Collection</h3>

						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
							<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<ul2 class="products-list product-list-in-box">
							<table class='tbl table'>
								<?php
								$income = all_income($today, $today);
								foreach ($income as $ed => $i_amt) {
									if ($i_amt <> '') {
								?>
										<tr>
											<td><?php echo add_space($ed); ?> </td>
											<td align='right'> <?php echo $i_amt; ?></td>
										</tr>
								<?php }
								} ?>
							</table>
							</ul>
					</div>
					<!-- /.box-body -->
					<div class="box-footer text-center">
						<a href="collection_report" class="uppercase">View All Collection</a>
					</div>
					<!-- /.box-footer -->
				</div>
			</div>

		</div>
		<!-- /.row -->



	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php require_once('required/footer.php'); ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
	window.addEventListener('load', () => {
		registerSW();
	});

	// Register the Service Worker
	async function registerSW() {
		if ('serviceWorker' in navigator) {
			try {
				await navigator
					.serviceWorker
					.register('sw.js');
			} catch (e) {
				console.log('SW registration failed');
			}
		}
	}
</script>

<script type="text/javascript">
	google.charts.load('current', {
		'packages': ['corechart']
	});
	google.charts.setOnLoadCallback(paymentChart);
	google.charts.setOnLoadCallback(admissionChart);

	function paymentChart() {
		var data = google.visualization.arrayToDataTable(<?php echo monthly_collection_graph(); ?>);

		var options = {
			title: 'Total Vs Paid',
			chartArea: {
				left: 30,
				top: 20,
				width: "80%",
				height: "80%"
			}
		};

		var chart = new google.visualization.LineChart(document.getElementById('payment_chart'));

		chart.draw(data, options);
	}

	function admissionChart() {
		var data = google.visualization.arrayToDataTable(<?php echo new_admission_graph(); ?>);

		var options = {
			title: 'Class Vs New Admission',
			//chartArea:{left:30,top:20,width:"70%",height:"80%"}
		};

		var chart = new google.visualization.PieChart(document.getElementById('admission_chart'));

		chart.draw(data, options);
	}
</script>