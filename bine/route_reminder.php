<?php
require_once('required/function.php');
extract($_POST);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script type="text/javascript" src="js/towords.js"></script>

<style>
	body {
		font-family: calibri, arial, time new roman;
		font-size: 12px;
		padding: 0px;
		margin: 0px;

	}

	td {
		font-weight: 300;
		font-size: 13px;
	}

	.outer {
		float: left;
		margin: 20px 10px;
		height: 450px;
		display: inline;
		font-size: 20px;
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
<?php
extract($_POST);

foreach ($area_id as $area) {
	$data = get_data('transport_area', $area, null, 'area_name')['data'];
	$id[] = $data['id'];
}
$area_id = "'" . implode("','", $id) . "'";
$sql = "select * from student where finance_type ='NORMAL' and area_id in($area_id) and status ='ACTIVE'";
$res = direct_sql($sql);
if ($res['count'] > 0) {
	foreach ($res['data'] as $student) {
		$student_id = $student['id'];
		$prev_dues = 0;
?>
		<title> Demand Bill of <?php echo $student_class . " - " . $student_section; ?> </title>

		<table border='1' align='center' rules='all' cellpadding='5px' class='outer'>
			<thead>
				<tr>
					<td colspan='5' align='center'>
						<span class='name'><?php echo $full_name; ?></span><br>
						<?php echo $inst_address1; ?>,<?php echo $inst_address2; ?> <br>
						<?php echo $inst_contact; ?> | <?php echo $inst_email; ?> <br>
					</td>
				</tr>

				<tr>
					<td colspan='5' style='text-align:center; overflow-wrap: break-word;width:320px;'>
						<b> Demand Slip : <?php echo add_space(implode(', ', $fee_month)) ?></b>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td> Name </td>
					<td colspan='2'><?php echo strtoupper($student['student_name']); ?></td>
					<td> Adm No.</td>
					<td colspan='2'><?php echo strtoupper($student['student_admission']); ?></td>

				</tr>
				<tr>
					<td> Class/Sec. </td>
					<td colspan='2'><?php echo strtoupper($student['student_class']); ?>-<?php echo strtoupper($student['student_section']); ?> <b> (<?php echo strtoupper($student['student_type']); ?>) </b></td>
					<td> Roll No. </td>
					<td><?php echo strtoupper($student['student_roll']); ?></td>
				</tr>
				<tr>
					<td colspan='5' height='150px' valign='top'>
						<table width='320px' border='0' rules='none'>

							<?php if (get_data('student_fee', $student_id, 'current_dues')['data'] != 0) { ?>
								<tr>
									<td> Previous Dues </td>
									<td align='right'>
										<?php $prev_dues = get_data('student_fee', $student_id, 'current_dues')['data'];
										echo intval($prev_dues);
										?>
									</td>
								</tr>
							<?php } ?>

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
						</table>
					</td>
				</tr>
				<tr bgcolor='lightyellow'>
					<td colspan='5'>
						<center> Please Pay Before : <?php echo date('d-M-Y', strtotime($collection_date)); ?> </center>
					</td>
				</tr>
				<tr>
					<td colspan='4' align='right'> Total </td>
					<td align='right' class='total'><?php echo $total = $prev_dues + $all_fee['total']; ?></td>
				</tr>


				<tr>
					<td colspan='5' style='text-transform:capitalize;overflow:hidden;font-size:12px;'>
						<script>
							var words = toWords(<?php echo $total; ?>);
							document.write("<b>In Words :</b>" + words + " rupees only");
						</script>
					</td>
				</tr>


				<tr>
					<td colspan='5'>

						<small>
							<b>Instructions :-</b> </br>
							Always update your mobile no. to receive notification. <br>
							This receipt is auto generated only for information purpose. </br>

					</td>
				</tr>
		</table>

<?php }
} ?>