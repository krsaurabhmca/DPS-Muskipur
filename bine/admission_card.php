<?php require_once('required/header.php');?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
$table_name = 'student';
if(isset($_GET['link']) and $_GET['link']!='')
{
	$data = decode($_GET['link']);
	$student_class = $data['student_class'];
	$student_section = $data['student_section'];
	$res = get_all('student','*',array('status'=>'ACTIVE', 'admission_type'=>'NEW', 'student_section'=>$student_section,'student_class'=>$student_class));
}
else if (isset($_GET['student_class']) and $_GET['student_section']=='') {
	$res = get_all('student','*',array('status'=>'ACTIVE','admission_type'=>'NEW','student_class'=>$student_class));
}
else if( isset($_GET['student_section']) !=''  and $_GET['student_class'] !='') {
	$res = get_all('student','*',array('status'=>'ACTIVE','admission_type'=>'NEW', 'student_section'=>$student_section,'student_class'=>$student_class));
}
else{
	//$res = get_all('student','*',array('status'=>'ACTIVE'));
	$res = get_all('student');
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      New Admission
      </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item">Student</li>
        <li class="breadcrumb-item active">New Student</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-12">
         
         <div class="box">
            <div class="box-header with-border">
			<form action='send_sms.php' method='post'>
              <h3 class="box-title">New Student 
				<input type='hidden' name='mobiles' id='mobiles'>
			  	<input type='submit' class='btn btn-success btn-xs' value='Send SMS'>
			  </h3>
			  </form>
			  <div class="box-tools pull-right">
				<form>
					<select name='student_class' required> 
						<?php dropdown($class_list,$student_class); ?>
					</select>
					<select name='student_section' > 
						<?php dropdown($section_list,$student_section); ?>
					</select>
					<button class='btn btn-orange'> Show </button>
				</form>
				</div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
				<div class="table-responsive">
				  <table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Adm. No.</th>
							<th>Name</th>
							<th>Father's Name</th>
							<th>Class -Section </th>
							<th>Roll</th>
							<th>Mobile No</th>
							<th>Student Type</th>
							<th class='text-right'>Action</th>
						</tr>
					</thead>
					<tbody>
					<?php 
						if($res['count']>0)
						{
						foreach($res['data'] as $row)
						{
						    $id = $row['id']; 
							$link = encode('student_name='.$row['student_name'].'&id='.$row['id']);
						?>
						<tr>
							<td><?php echo $row['student_admission']?></td>
							<td><?php echo $row['student_name']?></td>
							<td><?php echo $row['student_father']?></td>
							<td><?php echo $row['student_class']."-".$row['student_section']?></td>
							<td><?php echo $row['student_roll']; ?></td>
							<td><span class='mobile'><?php echo $row['student_mobile']?></span></td>
							<td><?php echo $row['student_type']?></td>
							<td class='text-right' width=40px'>
							    <?php echo btn_view('student', $id,  $row['student_name']); ?>
								<a class ='fa fa-print btn btn-dark btn-xs' href='admission_card_print.php?link=<?php echo $link;?>' target='_op'></a> 
								
								<?php //echo btn_edit('add_student', $id); ?> 
								<?php //echo btn_delete($table_name, $id); ?> 
							
							</td>
						</tr>
					<?php } } ?>
					</tbody>
					<!--<tfoot>
						<tr>
							<th>Name</th>
							<th>Position</th>
							<th>Office</th>
							<th>Age</th>
							<th>Start date</th>
							<th>Salary</th>
						</tr>
					</tfoot>-->
				  </table>
				</div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
<?php require_once('required/footer2.php'); ?>
