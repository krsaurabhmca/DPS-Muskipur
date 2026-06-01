<aside class="main-sidebar">
	<section class="sidebar">
		<div class="user-panel">
			<div class="image float-left">
				<img src="images/user2-160x160.jpg" class="rounded-circle" alt="User Image">
			</div>
			<div class="info float-left">
				<p>Welcome <?php echo $user_name; ?></p> <a href="#"><i class="fa fa-circle text-success"></i><?php echo $user_type; ?></a>
			</div>
			<!--<form action="collect_fee" method="get" class="sidebar-form">-->
			<!--	<input name="student_class" type='hidden'>-->
			<!--	<input name="search_by" type='hidden' value='student_name'>-->
			<!--	<div class="input-group">-->
			<!--		<input type="text" name="search_text" class="form-control" id='search_text' placeholder="Student Name"> <span class="input-group-btn"> <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i> </button> </span>-->
			<!--	</div>-->
			<!--</form>-->
			<form action="manage_student" method="get" class="sidebar-form">
				<div class="input-group">
					<input type="text" name="search" class="form-control vi" id='search_text' placeholder="Search Anything" > <span class="input-group-btn">
					<button type="submit" accesskey='s' id="search-btn" class="btn btn-flat" title=' Press Alt +S to Search'><i class="fa fa-search"></i> </button> </span>
				</div>
			</form>
		</div>
		<ul class="sidebar-menu" data-widget="tree" id='nav'>
			<?php if ($user_type == 'STAFF') { ?>
				<li>
					<a href="teacher_dashboard.php"> <i class="fa fa-dashboard"></i> Dashboard</a>
				</li>
				<li>
					<a href="teacher_marks_entry.php"> <i class="fa fa-graduation-cap"></i> Marks Entry</a>
				</li>
			<?php } else { ?>
				<li>
					<a href="dashboard"> <i class="fa fa-dashboard"></i> Dashboard</a>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-info-circle"></i> <span>Enquiry Mgmt.</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li><a href="add_enquiry">Add Enquiry</a></li>
						<li><a href="manage_enquiry">Manage Enquiry </a></li>
						<li><a href="enquiry_report">Enquiry Report </a></li>
						<li><a href="form_collection">Form Sale Report </a></li>
						<li><a href="application_list">Online Application <span class='badge bg-success'>New</span></a></li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN" or $user_type == "ACCOUNT") { ?>
			<li class="treeview">
				<a href="#"> <i class="fa fa-male"></i> <span>Student Mgmt.</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
				</a>
				<ul class="treeview-menu">
					<li><a href="add_student">Add Student</a></li>
					<li><a href="manage_student">Manage Student </a></li>
					<li><a href="student_status">Student Report</a></li>
					<li><a href="manage_homework">Manage Homework</a></li>
					
				</li>
				</ul>
			</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-edit"></i> <span>Attendance Mgmt.</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li><a href="employee_attendance">Employee Attendance</a></li>
						<li><a href="emp_att_report">Employee Att Report</a></li>
						<li><a href="student_att">Student Attendance</a></li>
						<!--<li><a href="student_datewise_report"> Classwise Report</a></li>-->
						<li><a href="student_monthwise_att_report">Class & Monthwise Report</a></li>
						<li><a href="att_summary">Student Attendance Report</a></li>
						<li class="treeview">
							<a href="#"><i class="fa fa-mobile"></i> Staff GPS Attendance <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
							<ul class="treeview-menu">
								<li><a href="staff_attendance_today.php"><i class="fa fa-calendar-check-o"></i> Today's Attendance</a></li>
								<li><a href="staff_attendance_history.php"><i class="fa fa-history"></i> Attendance History</a></li>
								<li><a href="staff_attendance_report_matrix.php"><i class="fa fa-th"></i> Monthly Matrix</a></li>
								<li><a href="staff_attendance_report_timesheet.php"><i class="fa fa-clock-o"></i> Timesheet Report</a></li>
							</ul>
						</li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-user-circle"></i> <span>Employee Mgmt.</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li><a href="manage_employee">Manage Employee</a></li>
						<li><a href="manage_driver">Manage Driver</a></li>
						<li><a href="manage_salary">Manage Salary</a></li>
					</ul>
				</li>
			<?php } ?>
		
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<!--<li class="treeview">-->
				<!--	<a href="#"> <i class="fa fa-list"></i> <span>Timetable Managment</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>-->
				<!--	</a>-->
				<!--	<ul class="treeview-menu">-->
				<!--		<li><a href="create_timeslot">Create TimeSlot</a></li>-->
				<!--		<li><a href="create_timetable">Create TimeTable</a></li>-->
				<!--		<li><a href="manage_timetable">Manage TimeTable</a></li>-->
				<!--		<li><a href="classroom_timetable">Classroom TimeTable</a></li>-->
				<!--		<li><a href="faculty_timetable">All Faculty TimeTable</a></li>-->
				<!--		<li><a href="student_timetable">Student TimeTable</a></li>-->
				<!--		<li><a href="teacher_timetable">Teacher Timetable</a></li>-->
				<!--	</ul>-->
				<!--</li>-->
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN" ) { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-list"></i> <span>Lesson Plan</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li><a href="create_lesson">Create Lesson</a></li>
						<li><a href="manage_lesson">Manage Lesson</a></li>
						<li><a href="classroomwise_topic_status">Classroom-wise Topic</a></li>
						<li><a href="subjectwise_topic_status">Subject-wise Topic</a></li>
						<li><a href="manage_question">Manage Question</a></li>
						<li><a href="lesson_tracker">Lesson Tracker</a></li>
					</ul>
				</li>
			<?php } ?>
			    <?php if ($user_type == "ADMIN" or $user_type == "ACCOUNT") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-inr"></i> <span>Fee Management</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="collect_fee.php" accesskey='c'>
								<akey>C</akey>ollect Fee
							</a>
						</li>
						<li> <a href="collection_report.php" accesskey='s'>
								<akey>S</akey>how Collection
							</a>
						</li>
						<li> <a href="generate_demand.php" accesskey='d'>
								<akey>D</akey>emand Print
							</a>
						</li>
						<li> <a href="route_demand.php" accesskey='r'>
								<akey>R</akey>oute wise Demand
							</a>
						</li>
						<li> <a href="class_wise_ledger.php"> Class Wise Ledger</a>
						</li>
					</ul>
				</li>
            <?php } ?>
			<?php if ($user_type == "ADMIN" or $user_type == "ACCOUNT") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-truck"></i> <span>Transport Mgmt.</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="add_vehicle.php" accesskey='v'>
								Add <akey>V</akey>ehicle
								<span class="badge badge-success">NEW</span>
							</a>
						</li>
						<li> <a href="add_area.php" accesskey='a'>
								<akey>A</akey>dd Area
							</a>
						</li>
						
						<li><a href="exp_head.php">
								Add Expense
								<span class="badge badge-success">NEW</span>
							</a>
						</li>
						<li> <a href="add_trip.php" accesskey='T'> Add <akey>T</akey>rip </a>
						</li>
						<li> <a href="area_wise_report.php"> Area Wise Report </a>
						</li>
						<li> <a href="vehicle_report.php"> Vehicle Report <span class="badge badge-success">NEW</span> </a>
						</li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN" ) { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-graduation-cap"></i> <span>Exam Mgmt.</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="admit_card">Admit Card </a>
						</li>
						<!--<li> <a href="manage_admit_card"> View Admit Card </a>-->
						<!--</li>-->
						<li> <a href="print_admit_card"> Print Admit Card </a>
						</li>
						<li> <a href="exam_routine">Print Exam Routine</a>
						</li>
						<li><a href="exam_seat_manage">Seating Arrangement </a>
						</li>
						<li> <a href="marks_entry"> Marks Entry </a>
						</li>
						<li> <a href="marks_upload"> Bulk Marks Upload </a>
						</li>
						<li> <a href="consolidated_marks"> Consolidated Marks </a>
						</li>
						<li> <a href="consolidated_new"> Consolidated New </a>
						</li>
						
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == 'Librarian' or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-book"></i> <span>Library Mgmt. </span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="book_cat.php"> Book Category </a>
						</li>
						<li> <a href="book_pub.php"> Book Publisher </a>
						</li>
						<li> <a href="manage_book.php"> Manage Book </a>
						</li>
						<li> <a href="issue_book.php"> Issue A Book </a>
						</li>
						<li> <a href="book_return.php"> Return Book </a>
						</li>
						<li> <a href="return_report.php"> Return Report </a>
						</li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "ACCOUNT" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-inr"></i> <span>Accounts </span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="acc_dash">Account Dashbord </a></li>
						<li> <a href="exp_head.php">Expense Head </a>
						</li>
						<li>
							<a href="manage_account.php"> Manage Transaction</a>
						</li>

						<li>
							<a href="date_wise_report.php"> Date Wise Report</a>
						</li>
						<li>
							<a href="daily_ledger.php"> Daily Ledger</a>
						</li>
						<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
							<li>
								<a href="head_wise_report.php"> Head Wise Report</a>
							</li>
						<?php } ?>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-print"></i> <span>Print Mgmt. </span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
					    <li> <a href="generate_idcard.php"> Identity Card </a>
						</li>
						<li> <a href="create_certificate.php"> Create Certificate </a>
						</li>
						<li> <a href="view_tc.php"> View Issued TC</a>
						</li>
					    <li>
						<a href="generate_report_card"> Print Report Card</a>
						</li>
						<li><a href="admission_card">Admission Slip</a>
						<li> <a href="transport_card.php"> Transport Card</a>
					    </li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-print"></i> <span>Website Mgmt. </span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
					    <li> <a href="add_gallery.php">Add Gallery </a>
						</li>
					 <!--   <li> <a href="manage_gallery.php">Manage Gallery </a>-->
						<!--</li>-->
					    <li> <a href="add_notice.php">Add Notice </a>
						</li>
						<li> <a href="manage_notice.php">Manage Notice </a>
						</li>
						
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-globe"></i> <span>Extra & SMS</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="send_sms.php"> Send SMS </a>
						</li>
						<li> <a href="sms_status.php">SMS Delivery Report </a>
						</li>
						<li> <a href="sms_template.php"> SMS Template </a>
						</li>
						<li> <a href="generate_idcard.php"> Identity Card </a>
						</li>
						<li> <a href="create_certificate.php"> Create Certificate </a>
						</li>
						<li> <a href="employee_status.php"> Employee List</a>
						</li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "DBA" or $user_type == "ADMIN") { ?>
				<li class="treeview">
					<a href="#"> <i class="fa fa-home"></i> <span>Hostel Mgmt. </span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="#"> Create Hostel </a>
						</li>
						<li> <a href="s#">Student Information </a>
						</li>
						<li> <a href="#">In and Out Entry </a>
						</li>
						<li> <a href="#"> Complain Book </a>
						</li>
						<li> <a href="#"> Current Student </a>
						</li>
						<li> <a href="#"> In Out History</a>
						</li>
					</ul>
				</li>
			<?php } ?>
			<?php if ($user_type == "ADMIN" or $user_type == 'Developer') { ?>
				<li class="treeview">
					<a href="add_fee.php"> <i class="fa fa-cog"></i> <span>Settings</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="add_fee.php"> Create Fee </a>
						</li>
						<li> <a href="update_fee.php"> Set Fee Amount </a>
						</li>
						<li> <a href="subject_setting.php"> Manage Subject </a>
						</li>
						<li> <a href="add_user.php"> Manage User </a>
						</li>
						<li> <a href="assign_teacher_subject.php"> Assign Teacher Subjects </a>
						</li>
						<li> <a href="bulk_import.php">Bulk Import </a>
						</li>
						<li>
							<a href="admin_txn.php"> Admin Transaction</a>
						</li>
						<li> <a href="add_role.php"> <span class='badge badge-success'>R</span>Role & Responsibility </a>
						</li>
						<li>
							<a href="admin_account.php"> Transaction Report</a>
						</li>
						<li> <a href="fee_test.php" accesskey='r'>  Recheck </a>
						</li>
						<li>
							<?php // echo $current_session;
							if ($session_list[$db_name] != $current_session) { ?>

						<li> <a href="promote_student.php">Promote Student </a>
						</li>
					<?php } ?>
					</ul>
				</li>
			<?php  } ?>
			<li class="treeview">
					<a href="#" target="_blank"> <i class="fa fa-info"></i> <span>Help Section</span> <span class="pull-right-container"> <i class="fa fa-angle-left pull-right"></i> </span>
					</a>
					<ul class="treeview-menu">
						<li> <a href="help/index.php" target="_">Help</a>
						</li>
					</ul>
			</li>
		</ul>
	</section>
	<div class="sidebar-footer">
		<a href="#" class="link" data-toggle="tooltip" title="" data-original-title="Settings" id='appinfo'><i class="fa fa-cog fa-spin"></i></a>
		<a href="support" class="link" data-toggle="tooltip" title="" data-original-title="Help & Support"><i class="fa fa-life-ring" aria-hidden="true"></i></i></a>
		<a href="#" class="link" data-toggle="tooltip" title="" onclick="logout()"><i class="fa fa-power-off"></i></a>
	</div>
</aside>