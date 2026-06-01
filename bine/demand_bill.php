<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script>
	$(document).ready(function() {
		$("#btn1").click(function() {

			$(".total:contains('0.00')").closest("table").hide(500);

		});

	});
</script>

<style>
	body,
	td {
		font-family: calibri, arial, times new roman;
	}

	.name {
		display: inline;
		font-size: 24px;
		line-height: 30px;
	}

	.table {
		border: solid 1px gray;
		margin: 5px;
		float: left;
	}
</style>
<input type='button' id='btn1' Value=' Remove all 0'>
<?php require_once('conn.php');

//print_r($_POST);
extract($_POST);
$demand_month = "";
$query0 = "select * from student where student_class like '%$student_class' order by student_roll";
$res0 = mysqli_query($con, $query0) or die(" Default Error : " . mysqli_error($con));
if (mysqli_num_rows($res0) < 0) {
	die("Sorry No Student Selected");
} else {

	while ($row0 = mysqli_fetch_array($res0)) {
		$student_id = $row0['student_id'];
?>


		<table class='table' width='340px' cellpadding='5px' rules='All' id='<?php echo $student_id; ?>'>
			<thead>
				<tr>
					<td colspan='5' align='center'>
						<span class='name'><?php echo $inst_name; ?></span><br>
						<small><?php echo $inst_address1; ?>,<?php echo $inst_address2; ?></small> <br>
						<?php echo $inst_contact; ?> | <?php echo $inst_email; ?> <br>
					</td>
				</tr>

				<tr>
					<td colspan='2'> Name </td>
					<td colspan='3'><b><?php echo strtoupper(studentinfo($student_id, 'student_name')); ?></b></td>

				</tr>
				<tr>
					<td> Class </td>
					<td colspan='2'><?php echo studentinfo($student_id, 'student_class'); ?></td>
					<td> Roll No. </td>
					<td><?php echo studentinfo($student_id, 'student_roll'); ?></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan='5' height='240px' valign='top'>
						<table width='100%' rules='none' cellspacing='5px' cellpadding='2px'>
							<tr bgcolor='#d3d3d3'>
								<td> No. </td>
								<td>Fee Name </td>
								<td align='right'> Free Amount</td>
							</tr>

							<?php
							$total = studentinfo($student_id, 'student_dues');
							if ($total <> 0) {
								$i = 2;
								echo "<tr><td> 1</td>";
								echo "<td> Back Dues </td>";
								echo "<td align='right'>" . receiptinfo($rid, 'back_dues') . "</td>";
								echo "</tr>";
							} else {
								$i = 1;
							}

							//$total =0;
							$query = "select * from fee_details order by fee_type"; // where fee_status ='ACTIVE' ";
							$res = mysqli_query($con, $query) or die(" Default Error : " . mysqli_error($con));
							while ($row = mysqli_fetch_array($res)) {
								$fee_id = $row['fee_id'];
								$demand_month = $demand_month . " " . $row['fee_name'];
								$colname = remove_space(feeinfo($fee_id, 'fee_name'));
								$fee_type = remove_space(feeinfo($fee_id, 'fee_type'));
								$st = studentfeestatus($student_id, $fee_id);
								//$cmonth =remove_space(date('F'));
								if (in_array($fee_id, $fee_month) and $st == 1) {

									$total = $total + $row['fee_amount'];
									echo "<tr>";
									echo "<td> $i </td>";
									echo "<td>" . $row['fee_name'] . "</td>";
									echo "<td align='right'>" . $row['fee_amount'] . "</td>";

									echo "<tr>";

									$i++;
								}
							}
							?>
			</tbody>

		</table>
		</td>
		</tr>
		<tfoot>
			<tr bgcolor='lightyellow'>
				<td colspan='5'>
					<center> Fee Collection Date : <?php echo date('d-M-Y', strtotime($collection_date)); ?> </center>
				</td>
			</tr>
			<tr bgcolor='#d5d5d5'>
				<td colspan='4'> Total </td>
				<td align='right' class='total'><b><?php echo $total; ?></b></td>
			</tr>
			<tr>
				<td colspan='5'>

					<small>
						<b>Instructions :-</b><br>
						Please Pay Before 10th of every month. </br>
						Always Update your mobile No. to receive notification. <br>
				</td>
			</tr>
			<tr>
				</td>
			</tr>
		</tfoot>
		</table>



	<?php

		if ($checksms) {
			$mobile = studentinfo($student_id, 'student_mobile');
			$name = studentinfo($student_id, 'student_name');
			$student_class = studentinfo($student_id, 'student_class');
			$sms = "Dear Parent Kindly pay Rs. " . $total . " as the tuition fee of" . $name . "class" . $student_class . "of the month " . $demand_month . "\n" . $inst_name . "\n" . $inst_url;
			//send_sms($mobile,$sms);
		}
	}
	?>


<?php
}
?>