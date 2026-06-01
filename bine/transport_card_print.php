<?php
require_once('required/function.php');
//print_r($_REQUEST);
extract($_REQUEST);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script type="text/javascript" src="js/towords.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@500&display=swap');

body{
	font-family: 'Outfit', sans-serif;
	font-size:12px;
	padding:0px;
	margin:0px;
	color:#444;
}
td{
   font-weight:300;
	font-size:13px;  
	padding-left:4px;
}
.outer{
	float:left;
	margin:20px 10px;
	border-radius:10px;
	border:solid 2px #cf0056;
    	
}
.name{
	diplay:inline;
	font-size:24px;
	line-height:28px;
	font-weight:800;
	/*font-family:arial black;*/
	color:#cf0056;
}
.btn{
		font-size:15px;
		padding:3px;
		text-align:center;
		background:#c0e;
		color:#fff;
		border-radius:10px;
	 }
@media print {
        .btn{display:none;}
		.outer{
			page-break-inside: avoid;
		} 
      }
</style>
<?php
//extract($_POST);

/*
foreach($area_id as $area)
{
	$data = get_data('transport_area',$area,null,'area_name')['data'];
	$id[] = $data['id'];
}
$area_id = "'" . implode("','", $id) . "'";
$sql ="select * from student where area_id in($area_id) and status ='ACTIVE'";
$res =direct_sql($sql);
*/
$res = get_all('student','*',array('student_class'=>$student_class,'student_section'=>$student_section, 'student_section'=>$student_section,'finance_type'=>'NORMAL', 'status'=>'ACTIVE','student_type'=>'TRANSPORT'));

if($student_admission <>'')
{
   // $sql = " select * from student where student_class ='$student_class' and student_section ='$student_section' and finance_type='NORMAL' and student_type='TRANSPORT' and status ='ACTIVE' and student_roll in ($student_roll)";
    $sql = " select * from student where finance_type='NORMAL' and student_type='TRANSPORT' and status ='ACTIVE' and student_admission in ($student_admission)";
    $res = direct_sql($sql);
}
else{
    
$res = get_all('student','*',array('student_class'=>$student_class,'student_section'=>$student_section, 'finance_type'=>'NORMAL', 'student_type'=>'TRANSPORT', 'status'=>'ACTIVE'));
}
if($res['count']>0)
{
foreach($res['data'] as $student)
{
$student_id = $student['id'];
    $transport = get_data('transport_area',$student['area_id'] )['data'];
?>
<title> Transport Card of <?php echo $student_class . " - ". $student_section; ?> </title>
<div class='outer'>
<table  border='0' align='center' rules ='none' cellpadding='3px' width='330px' > 
	<thead>
		<tr>
			<td colspan='5' align='center'> 
				<b class='name'><?php echo $full_name; ?></b><br>
				<!--(Affiliated to CBSE, New Delhi upto 10+2) <br>-->
				<!--<b>Affiliation No. : <?php echo $aff_no;?>  School No. :  <?php echo $school_code;?></b><br>-->
				<?php echo $inst_address1; ?>,<?php echo $inst_address2; ?> <br>
				+91 <?php echo $inst_contact; ?> | 
				<!--| <?php echo $inst_email; ?> <br>	<?php echo $inst_url; ?> -->
			</td>
		</tr>
	
	</thead>
	<tbody>
	    <tr>
			<td colspan='5' style='background:#cf0056;color:#fff;'> <center><b>TRANSPORT CARD </b> </td>
		</tr>
		
		<tr>
			<td width='100px'> Adm No.</td> <td colspan='3'><?php echo strtoupper($student['student_admission']); ?></td>
			<td rowspan='4' >
			    <img src='required/upload/<?php echo $student['student_photo']; ?>' width='85px' height='100px' align='right'>
			</td>
		</tr>
		<tr>
			<td> Class & Section </td> <td colspan='3'><?php echo strtoupper($student['student_class']); ?>-<?php echo strtoupper($student['student_section']); ?></td>
			
		</tr>
		<tr>
			<td> Roll No.</td> <td colspan='3'><?php echo strtoupper($student['student_roll']); ?></td>
		</tr>
		
	    <tr>
			<td> Bus Stop </td> <td colspan='3'><?php echo $transport['area_name']; ?></td>
		</tr>
	    <tr>
			<td> Transportion Fee </td> <td colspan='3'><?php echo $transport['area_fee']; ?></td>
		</tr>
	    
	   
	    <tr>
			<td> Issue Date</td> <td colspan='4'><?php echo date('d-M-Y', strtotime($_POST['issue_date'])); ?></td>
		</tr>
	    
		<tr>
			<td> Valid Till</td> <td colspan='4'><?php echo date('d-M-Y', strtotime($_POST['valid_till'])); ?></td>
		</tr>
		
			<tr>
			<td> Name </td> <td colspan='4'><?php echo strtoupper($student['student_name']); ?></td>
		</tr>
		<tr>
			<td> Father's Name </td> <td colspan='4'><?php echo strtoupper($student['student_father']); ?></td>
			
		</tr>
		 <tr>
			<td> Contact No. </td> <td colspan='4'><?php echo $student['student_mobile'] . " ". $student['father_mobile'] . " ". $student['mother_mobile']; ?></td>
		</tr>
	
	    <tr height='60px'>
			<td> Address </td> <td colspan='4'>
			    <?php // echo short($student['student_address1'],65); ?>
			    <?php echo $student['student_address1']; ?></td>
		</tr>
	
		
		<tr>
		<td colspan='5'>
			<small>
			<b>Instructions :-</b> </br>
			You must have this card while using school bus services. <br>
			Kindly Check validity and other details, if any discrepancy report to office. <br> 
			Return this card to office if don't want to use or transport Services.</br>
		</td>
	</tr>
	
	
	
	</table>
</div>
<?php } } ?>
