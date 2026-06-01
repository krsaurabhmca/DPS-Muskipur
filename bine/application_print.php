<?php
require_once('required/function.php');
$data = decode($_GET['link']);
$student_id = $data['id'];
$res = get_data('admission', $student_id);


if ($res['count'] > 0) {
	extract($res['data']);

	if(strtotime($created_at)<=strtotime('2023-01-22'))
	{
	    $exam_date = "22 Jan 2023";
	    $phase ='1st Phase';
	}
	else if(strtotime($created_at)>strtotime('2023-01-22') and strtotime($created_at)<=strtotime('2023-02-12'))
	{
	    $exam_date = "12 Feb 2023";
	    $phase ='2nd Phase';
	}
	else if(strtotime($created_at)>strtotime('2023-02-12') and strtotime($created_at)<strtotime('2023-03-12'))
	{
	    $exam_date = "12 March 2023";
	    $phase ='3rd Phase';
	}
	else if(strtotime($created_at)>strtotime('2023-03-12') and strtotime($created_at)<=strtotime('2023-03-26'))
	{
	    $exam_date = "26 March 2023";
	    $phase ='4th Phase';
	}
	else 
	{
	    $exam_date = "Sorry Date Expired";
	    $phase ='No Phase Available ';
	}
?>
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');

		body {
			font-family: 'Roboto', sans-serif;
		}
	
	</style>

	<table rules='rows' width='950px' align='center' border='1' cellpadding='6'>

		<tr>
			<td> <img src='images/logo.png' height='100px'> </td>
			<td colspan='4' align='center'>
				<span style='font-size:28px;font-weight:600;font-family:arial black;text-transform:uppercase;'> <?php echo $full_name; ?> </span><br>
				<b>(Affiliated to CBSE, New Delhi upto 10+2) <br>
					Affiliation No. : <?php echo $aff_no; ?> School Code : <?php echo $school_code; ?></b><br>
				<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
				<!--Contact No.: <?php echo $inst_contact; ?><br>-->
				Email : <?php echo $inst_email; ?>, Website : <?php echo $inst_url; ?>
			</td>
		</tr>

        <tr style='color:#006;text-align:center;padding:6px;border:none'>
			<td colspan='5 '> <h3> Application Acknowledgement (<?php echo $pay_status; ?>) </h3>  </td>
		</tr>
		
		<tr style='background:#222;color:#fff;text-align:center;padding:3px;'>
			<td colspan='5 '> Basic Details  </td>
		
		</tr>
		<tr bgcolor='#f9f9f9'>
			<td > Application No. </td>
			<td > <?php echo $app_no; ?> </td>
			<td  align='right'> Date of Apply </td>
			<td colspan='2'> <?php echo date('d-M-Y H:i A', strtotime($created_at)); ?> </td>
		</tr>
		
		<tr bgcolor='#f9f9f9'>
			<td colspan='2'> Student Name </td>
			<td colspan='2'> <?php echo $student_name; ?> </td>
			<td rowspan='5' align='center' width='140px'><img src='upload/<?php echo $student_photo; ?>' height='160px'> </td>
		</tr>

		<tr>
			<td colspan='2'> Applied for Class </td>
			<td colspan='2'> <?php echo $student_class; ?> </td>
		</tr>
	   	<tr>
			<td colspan='2'> Father's Name </td>
			<td colspan='2'> <?php echo $student_father; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Mother's Name </td>
			<td colspan='2'> <?php echo $student_mother; ?> </td>
		</tr>
		
		<tr>
			<td colspan='2'> Date of Birth </td>
			<td colspan='3'> <?php echo date('d-M-Y', strtotime($student_dob)); ?> </td>
		</tr>
		<!--<tr>-->
		<!--	<td colspan='2'> Gender </td>-->
		<!--	<td colspan='3'> <?php echo $student_sex; ?> </td>-->
		<!--</tr>-->
		<tr style='background:#222;color:#fff;text-align:center;padding:3px;'>
			<th colspan='5' > Contact Details  </th>
		</tr>
		
        <tr>
			<td colspan='2'> Address </td>
			<td colspan='3'> <?php echo $student_address; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Mobile No. </td>
			<td colspan='3'> <?php echo $student_mobile; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Whatsapp </td>
			<td colspan='3'> <?php echo $student_whatsapp; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Email </td>
			<td colspan='3'> <?php echo $student_email; ?> </td>
		</tr>
		
		<tr style='background:#222;color:#fff;text-align:center;padding:3px;'>
			<th colspan='5' > Payment Details  </th>
		</tr>
		
		<tr>
			<td colspan='2'> Payment Status </td>
			<td colspan='3'> <?php echo $pay_status; ?> </td>
		</tr>
        <tr>
			<td colspan='2'> Payment Mode </td>
			<td colspan='3'> <?php echo $pay_mode; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Payment Date & Time </td>
			<td colspan='3'> <?php echo $pay_date; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Amount </td>
			<td colspan='3'> <?php echo $pay_amount; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Payment Txn No. (For Online) </td>
			<td colspan='3'> <?php echo $pay_txn_no; ?> </td>
		</tr>
		
		
		<tr style='background:#222;color:#fff;text-align:center;padding:3px;'>
			<th colspan='5' > Admission Test Details  </th>
		</tr>
		<tr>
			<td colspan='2'> Phase </td>
			<td colspan='3'> <?php echo $phase; ?> </td>
		</tr>
		<tr>
			<td colspan='2'> Date </td>
			<td colspan='3'> <?php echo $exam_date; ?> </td>
		</tr>
		
        <tr>
			<td colspan='2'> Time </td>
			<td colspan='3'> 9:00 AM - 2:00 PM </td>
		</tr>
		
		<tr>
			<td colspan='2'> Venue </td>
			<td colspan='3'> School Campus </td>
		</tr>
	    <tr>
	        
			<td colspan='5'> 
			<i>Terms of Admission </i>
			<small>
			<ol>
			    <li>The school session is from April to March.</li>
                <li>Parents/Guardians seeking admission for their wards shall apply in the month of December with the prescribed registration fee.</li>
                <li>An admission test is held in January each year to ascertain the grade to which a student may be admitted.</li>
                <li>Only such candidates who have had their names registered in December will be eligible to sit for the admission test.</li>
                <li>Admission test is compulsory for all candidates seeking fresh admission to school. </li>
                <li>NO candidate will be admitted unless he/she has appeared in the test and qualified for the class to which admission is sought.</li>
                <li>Parents/Guardians are instructed to fill-in the admission forms with utmost accuracy. No changes will be permitted thereafter, for any reason whatsoever.</li>
                <li>The correct date of birth of the applicant must be supported by a proper certificate and it will not be changed later.</li>
                <li>A transfer certificate will be required from the candidates coming from another school.</li>
                <li>For Further Communication we will connect you on given number via call/ whatsapp.</li>
            </ol>
            </small>
            </td>
		</tr>
		<tr>
			<td colspan='5'> <input type='button' onclick='window.print()' value='Print'>
		</tr>
	</table>
<?php
}
?>