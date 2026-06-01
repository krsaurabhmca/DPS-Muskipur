<?php
require_once('required/function.php');
$data = decode($_GET['link']);

$student_id = $data['id'];
$res = get_data('student',$student_id);

if($res['count']>0)
{
	extract($res['data']);
?>
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
    <div class='outer'>
<table  border='0' align='center' rules ='none' cellpadding='3px' width='330px' > 
	
		<tr>
			<!--<td> <img src='images/logo.png' height='100px'> </td>-->
			
			<td colspan='5' align='center'> 
			<span class='name'> <?php echo $full_name; ?> </span><br>
			<b>(Based on CBSE Pattern) <br>
			<!--Affiliation No. : <?php //echo $aff_no;?>  School Code :  <?php //echo $school_code;?><br>--></b>
			<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
			
			</td>
		</tr>
		
		<tr>
			<td colspan='5' style='background:#cf0056;color:#fff;'> <center><b>ADMISSION CARD </b> </td>
		</tr>
		
	
		<tr>
			<td colspan='2'> Class  </td>
			<td colspan='2'> <?php echo $student_class; ?> </td>
			<td rowspan='5' align='right' width='80px'><img src='required/upload/<?php echo $student_photo;?>' height='100px'> </td>
		</tr>
		<tr>
			<td colspan='2'> Section </td>
			<td colspan='2' > <?php echo $student_section;?> </td>
		</tr>
		<tr>
			<td colspan='2'> Roll No. </td>
			<td colspan='2'> <?php echo $student_roll;?> </td>
			
		</tr>
		<tr>
			<td colspan='2'> Admission No. </td>
			<td colspan='2'> <?php echo $student_admission;?> </td>
		</tr>
		<tr>
			<td colspan='2'> Date of Admission </td>
			<td colspan='2'> <?php echo date('d-M-Y', strtotime($date_of_admission)); ?> </td>
		</tr>
	
		<tr>
			<td colspan='2'> Student Type </td>
			<td colspan='2'> <?php echo $student_type;?></td>
		</tr>
		<tr>
			<td colspan='2'> Bus Stop </td>
			<td colspan='2'> <?php echo get_data('transport_area',$area_id,'area_name')['data'];?></td>
		</tr>
		<tr>
			<td colspan='2'> Trip  </td>
			<td coslpan='3'> <?php echo get_data('trip_details',$trip_id,'trip_name')['data'];?></td>
		</tr>
		<tr>
			<td colspan='2'> Student Name </td>
			<td colspan='3'> <?php echo $student_name;?> </td>
		</tr>
		
	    <tr>
			<td colspan='2'> Father's Name </td>
			<td colspan='3'> <?php echo $student_father;?> </td>
		</tr>
		
	    <tr>
			<td colspan='2'> Mobile No. </td>
			<td colspan='3'> <?php echo $student_mobile ;?> </td>
		</tr>
		<tr>
			<td colspan='2'> Gender </td>
			<td colspan='3'> <?php echo $student_sex;?> </td>
		</tr>
		<td colspan='5'>
		
			<small>
			<b>Instructions :-</b> </br>
			This is Admission Card  is valid for 7 days from Admission. <br>
		    Kindly ask in school office for your Identity card. <br> 
		    <hr>
		    Contact No.: <?php echo $inst_contact; ?><br>
			Email : <?php echo $inst_email; ?><br>
			Website : <?php echo $inst_url; ?>
			</br>
		    
		</td>
	</table>
	</div>
<?php	
}
?>