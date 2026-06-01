<?php require_once('required/function.php');
if (!isset($_SESSION['initiated'])) {
  echo "<script> window.location ='https://rmpublicschool.org/pro/required/master_process?task=logout' </script>";
} else {
  $user_id = $_SESSION['user_id'];
  $udata = get_data('user', $user_id)['data'];
  $user_name = $udata['user_name'];
  $user_type = $udata['user_type'];
  session_regenerate_id();
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name=  "apple-mobile-web-app-status-bar" content="#ffbf36">
    <meta name="theme-color" content="#ffbf36">
    <link rel="manifest" href="manifest.json">
    <link rel="icon" href="images/favicon.ico">
    <title><?php echo $inst_name; ?> </title>
    
	<!-- Bootstrap 4.1.3-->
	<link rel="stylesheet" href="assets/vendor_components/bootstrap/css/bootstrap.css">
	
	<!-- Bootstrap-extend-->
	<link rel="stylesheet" href="css/bootstrap-extend.css">
	
	<!-- font awesome -->
	<link rel="stylesheet" href="assets/vendor_components/font-awesome/css/font-awesome.min.css">
	
	<!-- ionicons -->
	<link rel="stylesheet" href="assets/vendor_components/Ionicons/css/ionicons.min.css">
	
	<!-- theme style -->
	<link rel="stylesheet" href="css/master_style.css">
	
	<!-- Minimal-art Admin skins. choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="css/skins/_all-skins.css">
	
	<!-- jvectormap --
	<link rel="stylesheet" href="assets/vendor_components/jvectormap/jquery-jvectormap.css">
	
	<!-- Morris charts --
	<link rel="stylesheet" href="assets/vendor_components/morris.js/morris.css">
    <!-- OfferPlant Custom CSS -->
	<link rel="stylesheet" href="css/op.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<!-- google font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">

     
  </head>

	<!-- Main content -->
	<section class="content">
        	<h3> <?php echo $full_name; ?></h3>
        	<p> Kindly check and update all information of your ward.</p>
		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title"> Verify and update </h3>
                
				<div class="box-tools pull-right">
				    <form method='post'>
					<input type='number' onblur='submit()' name='student_adm' placeholder='Enter Admission No.' style='width:130px'>
					</form>
				</div>
			</div>
			<!-- /.box-header -->

<?php 

if (isset($_POST['student_adm']) and $_POST['student_adm'] != '') {
	$adm = $_POST['student_adm'];

	$res = get_data('student', $adm,null, 'student_admission');
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	
?>
			<div class="box-body">
				<form id='update_frm' action='parent_update_student'>
					<div class="row">

						<div class="col-lg-4">
							<div class="form-group row">

								<label for="example-text-input" class="col-sm-4 col-form-label">Name</label>
								<div class="col-sm-8">
									<input type='hidden' name='id' value='<?php echo $id; ?>' />
									<input class="form-control border-warning" type="text" value='<?php echo $student_name; ?>' name="student_name" required readonly>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Father's Name</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $student_father; ?>' name='student_father' required>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Mother's Name</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $student_mother; ?>' name='student_mother'>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Date of Birth</label>
								<div class="col-sm-8">
									<input class="form-control" type="date" value='<?php echo $date_of_birth; ?>' name='date_of_birth' id='example-date-input'>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Gender</label>
								<div class="col-sm-8">
									<select name='student_sex' class='form-control' required>
										<?php dropdown($gender_list, $student_sex); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Blood Group</label>
								<div class="col-sm-8">
									<select name='student_bloodgroup' class='form-control'>
										<?php dropdown($bloodgroup_list, $student_bloodgroup); ?>
									</select>
								</div>
							</div>
							<!--<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Religion</label>
								<div class="col-sm-8">
									<select name='student_religion' class='form-control'>
										<?php dropdown($religion_list, $student_religion); ?>
									</select>
								</div>
							</div> -->
						</div>

						<div class="col-lg-4">
							<!--<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">State</label>
								<div class="col-sm-8">
									<select name='state_code' class='form-control' onchange='getdistrict(this.value)'>
										<?php dropdown_list('state', 'code', 'name', $state_code); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">District</label>
								<div class="col-sm-8">
									<select name='district_code' class='form-control' id='district_list'>
										<?php dropdown_where('district', 'code', 'name', array('state_code' => $state_code), $district_code); ?>
									</select>
								</div>
							</div> -->
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Permanent Address</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" id='address1' name='student_address1'><?php echo $student_address1; ?></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Present Address
									<div class="checkbox text-sm">
										<input type="checkbox" id="basic_checkbox_1">
										<label for="basic_checkbox_1">Same </label>
									</div>
								</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" id='address2' name='student_address2'><?php echo $student_address2; ?></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Pin Code</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $pin_code; ?>' pattern="[0-9]{6}" name="pin_code" maxlength="6" minlength="6">
								</div>
							</div>
						</div>
						<div class="col-lg-4">

							<div class="form-group row">
								<div class="col-sm-12 text-center">
									<div id='display'>
									    <img src='required/upload/<?php if($student_photo == ""){ echo "no_image.jpg";} else {echo $student_photo;}?>' width='150px' height='160px' id='result'>
									</div>
									<input type='hidden' name='student_photo' id='targetimg' class='form-control' readonly value='<?php echo $student_photo; ?>'> 
									<span id='uploadarea' class='btn btn-secondary'>UPLOAD /CHANGE PHOTO </span>
								</div>
							</div>


							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Mobile No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="tel" value='<?php echo $student_mobile; ?>' pattern="[6789][0-9]{9}" name="student_mobile" required minlength="10" maxlength="10">
								</div>
							</div>
							<!--<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Email Id</label>
								<div class="col-sm-8">
									<input class="form-control" type="email" value='<?php echo $student_email; ?>' name='student_email' >
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Whatsapp No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="tel" pattern="[6789][0-9]{9}" value='<?php echo $student_whatsapp; ?>' name='student_whatsapp' maxlength="10" minlength="10">
								</div>
							</div> -->



						</div>

					</div>
					<!-- /.col -->

		<!--		
					<h3 class="box-title bg-gray p-2">Parents Details</h3>
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Father's Qualification </label>
								<div class="col-sm-6">
									<select name='father_qualification' class='form-control'>
										<?php dropdown($qualification_list, $father_qualification); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Father's Occupation </label>
								<div class="col-sm-6">
									<select name='father_occupation' class='form-control'>
										<?php dropdown($occupation_list, $father_occupation); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Father's Contact No.</label>
								<div class="col-sm-6">
									<input class="form-control" type="text" pattern="[6789][0-9]{9}" name='father_mobile' value='<?php echo $father_mobile; ?>' minlength="10" maxlength="10">
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Mother's Qualification </label>
								<div class="col-sm-6">
									<select name='mother_qualification' class='form-control'>
										<?php dropdown($qualification_list, $mother_qualification); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Mother's Occupation </label>
								<div class="col-sm-6">
									<select name='mother_occupation' class='form-control'>
										<?php dropdown($occupation_list, $mother_occupation); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Mother's Contact No.</label>
								<div class="col-sm-6">
									<input class="form-control" type="tel" pattern="[6789][0-9]{9}" name='mother_mobile' value='<?php echo $mother_mobile; ?>' minlength="10" maxlength="10">
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Family Income(Annual)</label>
								<div class="col-sm-6">
									<!--<select name='family_income' class='form-control' >
					   <?php dropdown($income_list, $family_income); ?>
					</select>
									<input class="form-control" type="text" name='family_income' value='<?php echo $family_income; ?>'>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Caste</label>
								<div class="col-sm-6">
									<select name='student_category' class='form-control'>
										<?php dropdown($caste_list, $student_category); ?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-6 col-form-label">Aadhar No.</label>
								<div class="col-sm-6">
									<input class="form-control" type="text" name='aadhar_no' value='<?php echo $aadhar_no; ?>' maxlength="12" minlength="12">
								</div>
							</div>
						</div>
					</div>
					<!-- /.col -->

		
          
            
		</div>
		<!-- /.box-body -->
		</form>
			<button class="btn btn-success btn-block btn-md" id='update_btn'><i class='fa fa-save'></i> Submit</button>
			
	</section>
	<?php } 

else{
    echo "<h1 class='text-center text-danger'> Invalid Admission No. ! No Student Found </h1>";
}
}?>
</div>

<div class='modal' id='uploadmodal'>
	<div class='modal-dialog'>
		<div class='modal-content'>
		    <div class="modal-header">
              <h4 class="modal-title">Uplaod Image</h4>
              <button type="button" class="close" data-dismiss="modal" id='btnclose'>&times;</button>
            </div>
			<div class='modal-body'>
			
				<form id='uploadForm' enctype='multipart/form-data'>
					<div class='form-group'>
						<label>Upload Photograph (Max 100 KB)</label>
						<input type='file' name='uploadimg' id='uploadimg' accept='image'>
						<br><small> Only Jpg and Png image upto 100KB. </small>
					</div>
				</form>
				<div id="my_camera"></div>
				<form>
					<input type=button value="Take Photo" onclick="take_snapshot()">
				</form>
			</div>
		</div>
	</div>
</div>
 <!--=========== View Data IN modal ========= -->
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id='view_data'>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalCenterTitle"></h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                       
                    </div>
                </div>
            </div>
    </div>
    
  
  <!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
<!--  <div class="control-sidebar-bg"></div>-->
<!--</div>-->
<!-- ./wrapper -->

	<!-- jQuery 3 -->
<!--	<script src="assets/vendor_components/jquery-3.3.1/jquery-3.3.1.js"></script>-->
	
	<!-- popper -->
<!--	<script src="assets/vendor_components/popper/dist/popper.min.js"></script>-->
	
	<!-- Bootstrap 4.1.3-->
<!--	<script src="assets/vendor_components/bootstrap/js/bootstrap.min.js"></script>	-->
	
	
	<!-- SlimScroll -->
<!--	<script src="assets/vendor_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>-->
	
	<!-- FastClick -->
<!--	<script src="assets/vendor_components/fastclick/lib/fastclick.js"></script>-->
	
	<!-- Minimal-art Admin App -->
<!--	<script src="js/template.js"></script>-->
	
	<!-- This is data table -->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/media/js/jquery.dataTables.min.js"></script>-->
    
    <!-- start - This is for export functionality only -->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/dataTables.buttons.min.js"></script>-->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/buttons.flash.min.js"></script>-->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/ex-js/jszip.min.js"></script>-->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/ex-js/pdfmake.min.js"></script>-->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/ex-js/vfs_fonts.js"></script>-->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/buttons.html5.min.js"></script>-->
<!--    <script src="assets/vendor_plugins/DataTables-1.10.15/extensions/Buttons/js/buttons.print.min.js"></script>-->
    <!-- end - This is for export functionality only -->
	<!-- Minimal-art Admin for Data Table -->
<!--	<script src="js/pages/data-table.js"></script>-->
<!--	<script src="js/jquery.validate.min.js"></script>-->
<!--	<script src="js/bootbox.all.js"></script>-->
<!--	<script src="js/notify.min.js"></script>-->
<!--	<script type="text/javascript" src="cam/webcam.min.js"></script>-->
<!--	<script src="js/shortcut.js"></script>-->
<!--	<script src="js/op.js"></script>-->
<!--</body>-->
<!--</html>-->

<?php require_once('required/footer2.php'); ?>

<script>
	$(document).ready(function() {
		var x = $('#student_type').val();
		if (x == 'TRANSPORT') {
			$('#area_id').css("display", "block");
		} else {
			$('#area_id').css("display", "none");
		}
		$('#student_type').on('change', function() {

			var a = $('#student_type').val();

			if (a == 'TRANSPORT') {
				$('#area_id').css("display", "block");
				$('#hostel_id').css("display", "none");
			}
			else if (a == 'HOSTELER') {
				$('#hostel_id').css("display", "block");
				$('#area_id').css("display", "none");
			}
			else {
				$('#area_id').css("display", "none");
				$('#hostel_id').css("display", "none");
			}
		});

		$("#base_dues").on('blur', function() {
			var bd1 = $("#base_dues").val();
			var bd2 = $("#base_dues2").val();
			if (bd1 != bd2) {
				bootbox.confirm({
					message: "You you really want to Change Base Dues ?",
					buttons: {
						confirm: {
							label: '<i class="fa fa-check"></i> Yes',
							className: 'btn-success'
						},
						cancel: {
							label: '<i class="fa fa-times"></i> No',
							className: 'btn-danger'
						}
					},
					callback: function(result) {
						if (result == false) {
							$("#base_dues").val($("#base_dues2").val());
						}
					}
				});
			}
		});
	});


	$("#student_section, #student_class").on('change', function() {
		var student_class = $("#student_class").val();
		var student_section = $("#student_section").val();
		$.ajax({
			method: 'post',
			url: 'required/master_process.php?task=last_roll',
			data: {
				student_section: student_section,
				student_class: student_class
			},
			success: function(res) {
				$("#student_roll").val(res);
			}
		});
	});
	
	$(document).on('click','#btnclose',function(){
	    	$("#uploadmodal").hide();
	});
</script>