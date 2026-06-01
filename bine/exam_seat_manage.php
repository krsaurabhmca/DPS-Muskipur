<?php require_once('required/header.php');?>
<?php require_once('required/menu.php');?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> Exam Seat Planning</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item"><a href="#fee">Exam</a></li>
      <li class="breadcrumb-item active">Seat Planning </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Basic Forms -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Select 3 Diffrent Class in each column </h3>
        <div class="box-tools pull-right">
         
        </div>
      </div>
      <!-- /.box-header -->

      <div class="box-body">
					
							<form action ='seat_plan.php' method ='post' target='_blank'>
                            
					        <div class='row'>
									<div class="form-group col-2">
                                            <label> Column 1 </label>
                                            <select class="form-control" name='class_1' required >
                                               <?php dropdown($class_list);?>
                                            </select>
                                    </div>
                                    
                                    <div class="form-group col-2">
                                            <label> Column 2</label>
                                            <select class="form-control" name='class_2'  >
                                               <?php dropdown($class_list);?>
                                            </select>
                                    </div>
                                    
                                    <div class="form-group col-2">
                                            <label> Column 3</label>
                                            <select class="form-control" name='class_3'  >
                                               <?php dropdown($class_list);?>
                                            </select>
                                    </div>
								    <div class="form-group col-3">
										<label>No. of Bench in Room (4,5,5) </label>
										<input class="form-control"  type='text'  name='total_bench' required value='15' >
								</div>
							
							    <div class="form-group col-3">
                                     <label>Click to Generate</label>
									 <input type="submit" class="btn btn-success btn-block" value=' Create Seat Plan' name='submit' >
								</div>	
								
						</div>
						</form>
					
						
        
        <!-- /.row -->
		
        </div>
    </div>
  </section>
</div>
<?php require_once('required/footer2.php'); ?>