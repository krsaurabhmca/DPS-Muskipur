<?php 
require_once("conn.php");
	extract($_POST);
	if(isset($_POST['submit']) and  $_POST['student_class'])
	{
	
		$sql = "select * from student where student_class ='$student_class'  and student_section ='$student_section' order by student_roll";
		$res = mysqli_query($con,$sql);
		while($row =mysqli_fetch_assoc($res))
		{
			$student_list[] = $row['id'];
		}
?>

<style>
body{
	color:#000;
	padding:0px;
	margin:0px;
}

td{font-size:13px;padding:8px;font-family:calibri,arial;font-weight:600;}

.idcard{width:740px;border:solid 0px #ddd; height:260px; background:url('assets/img/idcardpng') no-repeat; text-align:center; float:left; margin:6px; page-break-after:always;position:relative;}
.photo{position:absolute;margin:auto;top:10px;left:10px;margin-right:50px;z-index:0;border:solid 0px #ddd;border-radius:100%;}
.qr{position:absolute;width:55px; height:55px;margin:auto;top:255px;right:18px;z-index:0;border:solid 0px #000;border-radius:5px;}

@media print {
  #printbtn {
    display: none;
  }
  @page {size:portrait}
   .idcard{
        page-break-inside: avoid;
    }
}
</style>



<table border='1' class='idcard' cellspacing='0' rules='all'>
	<thead>
	<tr height='85px'>
		<td colspan='7' align='center'>
			<img src='images/logo.png' height='120px' align='left'>
			<span style='font-size:30px;font-weight:800;font-family:calibri;text-transform:uppercase;color:maroon;'> <?php echo $full_name; ?> </span><br>
			<b>(Affiliated to CBSE, New Delhi upto 10+2) <br>
			<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
			Contact No.: <?php echo $inst_contact; ?><br>
			Email : <?php echo $inst_email; ?>, Website : <?php echo $inst_url; ?>
			<div style='font-size:10px;background:#ddd;color:#131304;border-radius:20px;padding:2px 10px;line-height:12px;margin-top:2px;'>
				<?php echo $exam_name; ?> [ <?php echo $student_class; ?> - <?php echo $student_section; ?> ]
			</div> 
		</td>
	</tr>
	<tr>
		<td> Roll. No. </td>
		<td> Admission No. </td>
		<td> Name </td>
		<td> Father's Name </td>
		<td> Answer Sheet No </td>
		<td> Extra Sheet No </td>
		<td width='120px'> Signature </td>
	</tr>
	</thead>
	<tbody>
		<?php 
		$i=1;
		foreach($student_list as $sid) { ?>
			<tr>
				<td> <?php echo get_data("student",$sid,'student_roll'); ?> </td>
				<td> <?php echo get_data("student",$sid,'student_admission'); ?> </td>
				<td>  <?php echo get_data("student",$sid,'student_name'); ?> </td>
				<td> <?php echo get_data("student",$sid,'student_father'); ?> </td>
				<td> </td>
				<td> </td>
				<td> </td>
			</tr>	
		<?php } ?>
	</tbody>

</table>
	<?php } ?>	