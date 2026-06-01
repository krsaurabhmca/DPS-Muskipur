<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['app_no']) and $_GET['app_no'] != '') {
    $res = get_data('admission', $_GET['app_no'],null, 'app_no');
    if($res['count']==1)
    {
      extract($res['data']);  
      $student = insert_row('rmpsorg_2324.student');
      $student_address1 = $student_address;
      $date_of_birth = $student_dob;
      //echo $_SERVER['DOCUMENT_ROOT']."/required/upload/$student_photo";
      copy("upload/$student_photo","required/upload/$student_photo" );
      $id = $student['id'];
    }
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Student Details</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Student</a></li>
			<li class="breadcrumb-item active">Add Student</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Student Details </h3>

				<div class="box-tools pull-right">
					<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form id='update_frm' action='new_adm'>
					<div class="row">

						<div class="col-lg-4">
							<div class="form-group row">

								<label for="example-text-input" class="col-sm-4 col-form-label">Name</label>
								<div class="col-sm-8">
									<input type='hidden' name='id' value='<?php echo $id; ?>' />
									<input class="form-control border-warning" type="text" value='<?php echo $student_name; ?>' name="student_name" required>
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
										<?php dropdown($gender_list, $gender); ?>
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
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Religion</label>
								<div class="col-sm-8">
									<select name='student_religion' class='form-control'>
										<?php dropdown($religion_list, $student_religion); ?>
									</select>
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group row">
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
							</div>
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
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Email Id</label>
								<div class="col-sm-8">
									<input class="form-control" type="email" value='<?php echo $student_email; ?>' name='student_email'>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Whatsapp No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="tel" pattern="[6789][0-9]{9}" value='<?php echo $student_whatsapp; ?>' name='student_whatsapp' maxlength="10" minlength="10">
								</div>
							</div>



						</div>

					</div>
					<!-- /.col -->

					<h3 class="box-title bg-gray p-2">Admission Details</h3>

					<div class="row">

						<div class="col-lg-6">
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Session </label>
								<div class="col-sm-8">
									<select name='student_session' class='form-control' required>
										<?php dropdown($session_list, $student_session); ?>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Date of Admission</label>
								<div class="col-sm-8">
									<input class="form-control" type="date" value='<?php echo $date_of_admission; ?>' name='date_of_admission'>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Admission No.*</label>
								<div class="col-sm-8">
									<input class="form-control border-warning" type="text" value='<?php echo $student_admission; ?>' name='student_admission' required>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Class & Section</label>
								<div class="col-sm-4">
									<select name='student_class' id='student_class' class='form-control' required>
										<?php dropdown($class_list, $student_class); ?>
									</select>
								</div>
								<div class="col-sm-4">
									<select name='student_section' id='student_section' class='form-control' required>
										<?php dropdown($section_list, $student_section); ?>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Class Roll No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="number" name='student_roll' id='student_roll' value='<?php echo $student_roll; ?>'>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">CBSE Reg. No.</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" name='cbse_reg_no' value='<?php echo $cbse_reg_no; ?>'>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Vth Subject (for IX & X)</label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $vth_subject; ?>' name='vth_subject'>
								</div>
							</div>


						</div>

						<div class="col-lg-6">
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Admission Type </label>
								<div class="col-sm-8">
									<select name='admission_type' class='form-control' required>
										<?php dropdown($admission_list, $admission_type); ?>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Finance Type </label>
								<div class="col-sm-8">
									<select name='finance_type' class='form-control' required>
										<?php dropdown($finance_list, $finance_type); ?>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Student Category </label>
								<div class="col-sm-8">
									<select name='student_type' class='form-control' id='student_type' required>
										<?php dropdown($student_type_list, $student_type); ?>
									</select>
								</div>
							</div>

							<div id='area_id'>
								<div class="form-group row">
									<label for="example-text-input" class="col-sm-4 col-form-label">Bus Stop </label>
									<div class="col-sm-8">
										<select name='area_id' class='form-control'>
											<option value=''></option>
											<?php dropdown_list('transport_area', 'id', 'area_name', $area_id); ?>
										</select>
									</div>
								</div>

								<div class="form-group row">
									<label for="example-text-input" class="col-sm-4 col-form-label">Trip No.</label>
									<div class="col-sm-8">
										<select name='trip_id' class='form-control'>
											<?php dropdown_list('trip_details', 'id', 'trip_name', $trip_id); ?>
										</select>
									</div>
								</div>
							</div>
							
							<div id='hostel_id'>
								<div class="form-group row">
									<label for="example-text-input" class="col-sm-4 col-form-label">Select Hostel </label>
									<div class="col-sm-8">
										<select name='hostel_id' class='form-control'>
											<option value=''></option>
											<?php dropdown_list('hostel', 'id', 'hostel_name', $area_id); ?>
										</select>
									</div>
								</div>

								<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Like Non-Veg </label>
    								<div class="col-sm-8">
    									<select name='non_veg' class='form-control'  required>
    										<?php dropdown($allow_status, $non_veg); ?>
    									</select>
    								</div>
							    </div>
							    
							    <div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Room/Bed No.</label>
    								<div class="col-sm-8">
    									<input class="form-control" type="text" id='bed_no' value='<?php echo $bed_no; ?>' name='bed_no'>
    								</div>
							    </div>
							</div>

							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Basic Dues</label>
								<div class="col-sm-8">
									<input class="form-control" type="hidden" id='base_dues2' value='<?php echo $base_dues; ?>'>
									<input class="form-control" type="number" step='any' id='base_dues' value='<?php echo $base_dues; ?>' name='base_dues'>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Status </label>
								<div class="col-sm-8">
									<select name='status' class='form-control' required>
										<?php dropdown($status_list, $status); ?>
									</select>
								</div>
							</div>
						
						</div>
					</div>

					<!-- /.col -->
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
					</select>-->
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

		
           <!-- /.col -->
					<h3 class="box-title bg-gray p-2">Allow Custom Fee (Based on Student ) </h3>
					<div class="row">
					    <?php 
					    $sql = "select * from fee_head where fee_type='STUDENT' and created_by<>0";
					    $res1 =direct_sql($sql);
					    if($res1['count']>0)
					    foreach($res1['data'] as $row) {
					    ?> 
						<div class="col-lg-4">
							<div class="form-group row">
								<div class="col-sm-6">
								    <label for="example-text-input" class="col-form-label"><?php echo $row['fee_name']; ?></label>
								    <select name ="<?php echo $row['col_name']; ?>" class='form-control'>
										<?php 
										dropdown($neet_status_list, $$col_name);
										?> 
									</select>	
								</div>
							</div>
						</div>
                        <?php } ?>
                    </div>
					<!-- /.col -->

			</div>
			<!-- /.row -->
            
		</div>
		<!-- /.box-body -->
		</form>
	</section>
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
					<input type=button value="Take Snapshot" onclick="take_snapshot()">
				</form>
			</div>
		</div>
	</div>
</div>
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