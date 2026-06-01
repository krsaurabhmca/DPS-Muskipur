<?php
require_once('conn.php');
verify_request();
//print_r($_POST);
extract($_POST);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script type="text/javascript" src="towords.js"></script>

<script>
	$(document).ready(function() {
		$("#btn1").click(function() {

			$(".total").filter(function() {
				return $(this).text() == 0;
			}).closest("table").hide(500); //css("color", "red");

		});

	});
</script>

<style>
	body {
		font-family: calibri, arial, time new roman;
		font-size: 10px;
		padding: 0px;
		margin: 0px;

	}

	td {
		font-weight: 300;
	}

	.outer {
		float: left;
		margin: 30px 10px;
	}

	.name {
		display: inline;
		font-size: 24px;
		line-height: 26px;
		font-weight: 500;
	}

	.btn {
		font-size: 15px;
		padding: 3px;
		text-align: center;
		background: #c0e;
		color: #fff;
		border-radius: 10px;
	}

	@media print {
		.btn {
			display: none;
		}

		.outer {
			page-break-inside: avoid;
		}

	}
</style>
<center><input type='button' id='btn1' class='btn' Value=' Click to Remove All Student Have No Dues'></center>
<?php
$all_data = direct_sql("select id from student where student_class='$student_class' and student_section ='$student_section' and status ='ACTIVE'");

$all_student = json_decode($all_data, true);
unset($all_student['count']);
//echo "<pre>";
//print_r($all_data);
//verify_request();
foreach ($all_student as $student) {
	$student_id = $student['id'];
	$r = duesmonthcount($student_id, $fee_month);
	$ct = $r['count'];
	$list = $r['list'];
	$total = 0;
?>
	<title> Demand Bill of <?php echo $student_class . " - " . $student_section; ?> </title>

	<table border='1' align='center' rules='all' cellpadding='5px' height='400px' class='outer' width='360px'>
		<thead>
			<tr>
				<td colspan='5' align='center'>
					<span class='name'><?php echo $full_name; ?></span><br>
					<?php echo $inst_address1; ?>,<?php echo $inst_address2; ?> <br>
					<?php echo $inst_contact; ?> | <?php echo $inst_email; ?> <br>
				</td>
			</tr>

			<tr>
				<td colspan='5' style='text-align:center' height='50px'>
					<!--<b> Demand Slip: <?php echo wordwrap(add_space($list), 60, "\n"); ?></b>-->
					<b> Demand Slip: Up to <?php echo print_r(explode(",",$list)); ?></b>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td> Name </td>
				<td colspan='2'><?php echo strtoupper(get_data('student', $student_id, 'student_name')); ?></td>
				<td> Adm. No.</td>
				<td colspan='2'><?php echo strtoupper(get_data('student', $student_id, 'student_admission')); ?></td>

			</tr>
			<tr>
				<td> Class/Sec. </td>
				<td colspan='2'><?php echo get_data('student', $student_id, 'student_class'); ?>-<?php echo get_data('student', $student_id, 'student_section'); ?> <?php echo " <b>(" . get_data('student', $student_id, 'student_type'); ?>)</b></td>
				<td> Roll No. </td>
				<td><?php echo get_data('student', $student_id, 'student_roll'); ?></td>
			</tr>
			<tr>
				<td colspan='5' height='150px' valign='top'>
					<table width='350px' border='0' rules='none'>
						<?php if (get_data('student_fee', $student_id, 'student_dues') > 0) {
							$total = $total + get_data('student_fee', $student_id, 'student_dues');
						?>
							<tr>

								<td> Previous Dues </td>
								<td align='right'><?php echo get_data('student_fee', $student_id, 'student_dues'); ?></td>
							</tr>

							<?php $all_fee = nmonth_fee($student_id, $fee_month);
							//print_r($all_fee);
							foreach ($all_fee as $key => $value) {
								if ($value <> 0) {
									if ($key <> 'total') {
										echo "<tr><td>" . add_space($key) . "</td><td align='right'>" . intval($value) . "</td></tr>";
									}
								}
							}

							?>

							<!--
				<?php } ?>
				<?php if (get_fee($student_id, 'tuition_fee') > 0) {
					$total = $total + get_fee($student_id, 'tuition_fee') * $ct;
				?>
				<tr>
					
					<td> Tuition Fee </td>
					<td align='right' ><?php echo get_fee($student_id, 'tuition_fee') * $ct; ?></td>
				</tr>
				<?php } ?>
				<?php if (get_data('student', $student_id, 'student_type') == 'TRANSPORT') {
					$area_id = get_data('student', $student_id, 'area_id');
					$t_fare = get_data('transport_area', $area_id, 'area_fee') * $ct;
					$total = $total + $t_fare;
				?>
				<tr>
					
					<td> Transportion Fee :<?php echo get_data('transport_area', get_data('student', $student_id, 'area_id'), 'area_name'); ?></td>
					<td align='right' ><?php echo get_data('transport_area', get_data('student', $student_id, 'area_id'), 'area_fee') * $ct; ?></td>
				</tr>
				<?php } ?>
				
				<?php if (get_data('student', $student_id, 'student_type') == 'HOSTEL') {
					$total = $total + get_fee($student_id, 'hostel_fee') * $ct;
				?>
				<tr>
				<td> Hostel Fee </td>
				
				<td align='right'> <?php echo get_fee($student_id, 'hostel_fee') * $ct; ?></td>
				</tr>
				<?php } ?>
				-->


					</table>
				</td>
			</tr>
			<!--<tr bgcolor='lightyellow'>
					<td colspan='5' ><center> Fee Collection Date : <?php echo date('d-M-Y', strtotime($collection_date)); ?> </center></td>
				</tr>-->
			<tr>
				<td colspan='4' align='right'> Total </td>
				<td align='right' class='total'><?php echo $total; ?></td>
			</tr>


			<tr>
				<td colspan='5' style='text-transform:capitalize;overflow:hidden;font-size:10px;'>
					<script>
						var words = toWords(<?php echo $total; ?>);
						document.write("<b>In Words :</b>" + words + " rupees only");
					</script>
				</td>
			</tr>


			<tr>
				<td colspan='5'>
					<small>
						<b>Instructions :-</b><br>
						Always update your mobile no. to receive notification. <br>
						This receipt is auto generated only for information purpose </br>
				</td>
			</tr>



	</table>

<?php } ?>