<?php require_once('required/header.php');?>
<?php require_once('required/menu.php');
$table = 'admit_card';
$student_class = $_GET['student_class'];
?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Generate Admit card </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#transport">Exam</a></li>
        <li class="breadcrumb-item active">Admit card</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">  
     <!-- Basic Forms -->
      <div class="box box-default">
		<div class="box-header with-border">
          <h3 class="box-title">Generate Admit Card</h3>

		 <div class="box-tools pull-right">
		            <form>
                    <div class="form-group">
						<label>Select Class </label>
						<select name='student_class' required onblur='submit()' onchange='submit()'>
						   <?php dropdown($class_list,$student_class);?>
						</select>
				    </div>
			        </form>
		  </div>
        </div>
				<div class="box-body">
				    <p class='text-danger'> Set Past Date (expired) if Subject not Applicable</p>
				         <div class='row bg-primary mb-2 p-2'>
							    <div class="col-3">
                                            <label>Subject Name </label>
                                </div>
								
								<div class="col-3">
                                            <label>Exam Date</label>
                            	</div>
								<div class="col-3">
                                            <label>Start Time</label>
                                </div>
								<div class="col-3">
                                            <label>End Time</label>
                                </div>
						
						    </div>
				    	<form action ='required/master_process.php?task=update_admit_card' method ='post' id='update_frm'>
				    	    <input class="form-control"  type='hidden' name='student_class' required  value='<?php echo $student_class; ?>' >
				    	    <?php
				    	    if(isset($_GET['student_class']))
				    	    {
				    	    $stu_class = $_GET['student_class'];
				    	    $sub_list = subject_list($stu_class);
				    	    foreach($sub_list as $sub_id){
				    	        
				    	    $sub_name = get_data('subject',$sub_id,'subject_name')['data'];
				    	    
				    	    $find = get_all('admit_card', '*', array('student_class'=>$stu_class, 'subject_id'=> $sub_id))['data'][0];
				    	    
				    	    ?>
				    	    <div class='row m-0'>
							   <div class="form-group col-3">
                                            <input class="form-control"  type='hidden' placeholder='Subject Name' name='subject_id[]' required  value='<?php echo $sub_id; ?>' >
                                        <?php echo $sub_name; ?>
								</div>
								
								<div class="form-group col-3">
                                            <input class="form-control"  type='date' placeholder='Examination Date' name='exam_date[]' value='<?php echo $find['exam_date']; ?>' >
								</div>
									<div class="form-group col-3">
                                            <input class="form-control"  type='time' placeholder='10:00 AM' name='start_time[]'   value='<?php echo $find['start_time']; ?>'>
								</div>
								<div class="form-group col-3">
                                            <input class="form-control"  type='time' placeholder='10:00 AM' name='end_time[]'  value='<?php echo $find['end_time']; ?>'>
								</div>
						
						    </div>
						   <?php  } ?>
						    <div class='row m-0'>
						        <button class='btn btn-success' class='btn_update'> SAVE</button> 
						    </div>
						   <?php } ?>     
						        
						</form>
					
				</div>
		</div>
	</section>
</div>
<?php require_once('required/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
	$("#task").on('change',function()
	{
		var task = $(this).children("option:selected").val();
		console.log(task);
		$("#reminder_frm").attr('action',task);
		
	});
// 	  $('#summernote').summernote({
//         placeholder: 'Enter Details',
//         tabsize: 2,
//         height: 150
//       });
</script>