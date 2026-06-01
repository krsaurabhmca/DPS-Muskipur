<?php 
session_start();
$date = date_default_timezone_set('Asia/Kolkata');
require_once('required/function.php');
//$sel_id =array(1,2,3,5);

// 	if(!is_array($_REQUEST['sel_id']))
// 	{
// 		$sel_id[] = $_REQUEST['sel_id']; 
// 	}
// 	else{
// 		$sel_id = $_REQUEST['sel_id']; 
// 	}
extract($_POST);
$student_type = "'" . implode("','", $student_type) . "'";
$sql = "select student_admission from student where student_class ='$student_class'  and student_section = '$student_section' and student_type in ($student_type) and status='ACTIVE' order by student_roll ";
$res = direct_sql($sql);
foreach ($res['data'] as $row) {
	$student_list[] = $row['student_admission'];
}


foreach ($student_list as $student)
	{
    $student = get_data('student', $student, null, 'student_admission')['data'];
    $sid = $student['id'];
    $gtotal =0;
    $graph =null;
?>
<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow&display=swap" rel="stylesheet">
<style>
	body{
		font-family: 'PT Sans Narrow', sans-serif;
	}
	.h2{
		display:inline;
		font-family:arial black;
		font-size:36px;
		margin:0px;
		padding:0px;
	}
	td{
		padding:2px;
	}
	p{
		font-size:13px;
	}
	.data{
		color:maroon;
		font-family:time new roman;
	}
	.photo{
		border-radius:50%;
	}
.reportcard{
	margin:4px auto;
}
@media print {
  #printbtn {
    display: none;
  }
  .reportcard{
        page-break-inside: avoid;
    }
  @page {size:portrait;}
}
</style>
<table align='center' rules='all' border='1' width='780px' class='reportcard'>
<tr>
	<td align='center' colspan='7'> 
	<table>
		<td>
		<img src='images/cbse.png' height='100px'>
		</td>
		<td align='center' width='500px'>
			<div class='h2'> <?php echo $full_name; ?> </div><br>
			<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
			Managed By:<?php echo $inst_managed_by; ?> <br>
			AFFILIATED TO CBSE, NEW DELHI UPTO 10+2 | AFFILIATION NO. <?php echo $aff_no; ?> <br>
			<?php echo $inst_contact; ?> | <?php echo $inst_email; ?> | <?php echo $inst_url; ?>
		</td>
		<td align='center' > 
		<img src='images/logo.png' height='100px'>
		</td>
	</table>
</tr>
<tr>
	<td colspan='7' align='center' style='background:#525252;color:#fff;height:30px;'>
		<?php echo $exam_name ." EXAMINATION 2022-2023 " . $session_list[$db_name]; ?>
	</td>
</tr>
<tr>
	<td colspan='7' align='center'>
		<b><?php echo get_data('student',$sid,'student_name')['data']; ?> </b>
	</td>
</tr>
<tr>
<td colspan='7'>
	<table rules='none' width='100%' border='0' >
			<td> Roll No. </td>
				<td colspan='3' width='110px'><?php echo get_data('student',$sid,'student_roll')['data']; ?></td> 
			<td rowspan='3' align='center'>	<img src='required/upload/<?php echo get_data('student',$sid,'student_photo')['data']; ?>' width='60' height='60' class='photo'></td>
				<td align='right'>Father's Name : </td><td colspan='3'><?php echo get_data('student',$sid,'student_father')['data']; ?></td>
		</tr>
		<tr>
			<td> Class  </td>
				<td colspan='3'><?php echo $st_class= get_data('student',$sid,'student_class')['data']; ?>-<?php echo get_data('student',$sid,'student_section')['data']; ?></td>
			<td align='right'>Mother's Name : </td>
				<td colspan='3'><?php echo get_data('student',$sid,'student_mother')['data']; ?></td >
		</tr>
		<tr>
			<td> Student ID </td>
				<td colspan='3'><?php echo $admission = get_data('student',$sid,'student_admission')['data']; ?></td> 
			<td align='right'>Date of Birth : </td>
				<td colspan='3'>
				<?php 
				//echo get_data('student',$sid,'date_of_birth');
				if(get_data('student',$sid,'date_of_birth')<>'0000-00-00'){
				echo date('d-M-Y',strtotime(get_data('student',$sid,'date_of_birth')['data']));
				}?></td >
		</tr>
	</table>
	</td>
<tr>
<tr>
	<td colspan='7' align='center' style='background:#525252;color:#fff;height:30px;'>
		SCHOLASTIC AREAS
	</td>
</tr>
<tr>
	<td colspan='7' align='center' height='300px' valign='top'>
		<table rules='all' width='100%'  border='0' bordercolor='#c5c5c5'>
			<tr align='center'>
				<th rowspan='2'> Subject </th>
				<th colspan='6'> <?php echo $exam_name; ?> </th>
			</tr>

			<tr>
				<th> NB (5)</th>
				<th> SEA(5) </th>
				<th> Marks Obtained (40)</th>
				<th> Total </th>
				<th> Grade</th>
			</tr>
           
				<?php
				$sub_list = subject_list($student['student_class']); //.'_subject';
				//$extra_list =extra_list($st_class); //.'_extra';
				//$subject_list = array_diff($sub_list,$extra_list);
				//print_r($extra_list);
				$graph[] =array('Subject','Marks');
				foreach ($sub_list as $subject_id) {
					$garr = array();
					$subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
					$subject_col = get_data('subject', $subject_id, 'subject_column')['data'];
					
					$marks  = get_marks($student['student_admission'], $exam_name, $subject_col);
					
					$total = $marks['pt'] + $marks['nb'] + $marks['se'] + $marks['mo'];
					$gtotal = $gtotal + $total;
					$garr[] =$subject_name; 
				    $garr[] =(int)$total; 
				?>
					<tr align='center'>
						<td align='left'> <?php echo $subject_name; ?> </td>
						<td> <?php echo $marks['nb']; ?> </td>
						<td> <?php echo $marks['se']; ?> </td>
						<td> <?php echo $marks['mo']; ?> </td>
						<td> <?php echo $total; ?> </td>
						<td> <?php echo grade($total*2); ?> </td>
					</tr>
				<?php 
				    $graph[] = $garr;
				} ?>
		</table>
		
	</td>
</tr>

<?php if($st_class =='X'){ ?> 
<tr align='center'>
	<th colspan='2'> Additional Subject </th>
	<th> NB (10)</th>
	<th> SEA(15) </th>
	<th> Marks Obtained (25)</th>
    <th> Total </th>
    <th colspan='2'> Grade </th>
</tr>
<?php } else { ?>
    <tr align='center'>
	<th colspan='2'> Additional Subject </th>
	<th> NB (5)</th>
	<th> SEA(5) </th>
	<th> Marks Obtained (40)</th>
    <th> Total </th>
    <th colspan='2'> Grade </th>
</tr>
    
<?php } ?> 
<?php 
$extra_list = extra_list($student['student_class']); 
foreach($extra_list as $extra_id) { 

	$subject_name = get_data('subject', $extra_id, 'subject_name')['data'];
	$subject_col = get_data('subject', $extra_id, 'subject_column')['data'];
	$extra  = get_marks($student['student_admission'], $exam_name, $subject_col);
					

?>
<tr align='center'>
	<td colspan='2'><?php echo $subject_name; ?></td>
	<td> <?php echo $nb = $extra['nb']; ?>  </td>
	<td> <?php echo $se = $extra['se']; ?>  </td>
	<td> <?php echo $mo = $extra['mo']; ?>  </td>
	<td><?php echo $total = $nb+$se+$mo; ?></td>
	<td colspan='2'> <?php echo grade($total*2); ?> </td>
</tr>
<?php } ?>

<tr height='50px'>
	<td colspan='7'></td>
</tr>

<tr style='background:#525252;color:#fff;height:30px;'>
	<th> <?php echo $exam_name; ?> Total </th>
	<th><?php echo $gtotal; ?></th>
	<th>Percentage </th>
	<th><?php 
//	echo $gper = ($gtotal*2)/count($subject_list); 
	echo $gper = number_format(($gtotal*2)/count($sub_list),2); 
	?></th>
	<th>Grade</th>
	<th colspan='2'><?php echo grade($gper); ?></th>
</tr>


<tr>
	<td colspan='3' align='center' valign='middle'>
		<b>Co-Scholastic Areas </b>
	</td>
	<td rowspan='5' colspan='4' width='60%' height='160px'>
	
	<div id="<?php echo 'graph_'.$sid; ?>" style="width: 450px; height: 150px"></div>
	</td>
</tr>
<?php foreach($co_scholastic_list as $co) {?>
<tr>
	<td colspan='2'><?php echo $co; ?></td>
	<th colspan='1'><?php // echo get_co_scholastic( $student_admission, 'Half Yearly' ,remove_space($co)); ?></th>
	
</tr>
<?php } ?>


<tr align='center'>
	<th colspan='3'>C - FAIR | B - Very Good | A - Outstanding</th>
</tr>
<tr>
	<td colspan='7' align='center' height='60px'>
	8 Point Grading Scale : A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32) <br>
	MO-Marks Obtained | NB - Note Book | SEA - Subject Enrichment Activity | HY - Half Yearly
	</td>
</tr>
<tr>
	<td colspan='5' valign='top' height='50px'>
		
		<b>Remarks: </b>
	
	</td>
	<td colspan='150px' valign='top' align='center'><b>Attendance</b> </td>
</tr>

<tr align='center' valign='bottom' height='80px'>
	<th colspan='4' align='left'>Class Teacher</th>

	<th colspan='3' align='right'>
	<img src='images/sign.png' height='60px'><br>
	<small>Principal </small></th>
</tr>

</table>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($graph); ?>);

        var options = {
          title: 'Performance Report',
          legend: {position: 'top', textStyle: {color: 'green', fontSize: 16}},
		  bar: {groupWidth: "65%"},
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('<?php echo 'graph_'.$sid; ?>'));

        chart.draw(data, options);
      }
    </script>
<?php
}
?>

