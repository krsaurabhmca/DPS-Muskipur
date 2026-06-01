<?php
require_once('required/function.php');
verify_request();
$data = decode($_GET['link']);
$id = $data['id'];
if (get_data('admission', $id)['count'] > 0) {
	$receipt = get_data('admission', $id)['data'];
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script type="text/javascript" src="js/towords.js"></script>
	<style>
		body {
			font-family: calibri, arial, time new roman;
			font-size: 10px;
			padding: 0px;
			margin: 0px;
		}

		td {
			font-weight: 300;
			font-size: 14px;
			padding: 3px 5px;
		}

		td .head {
			font-size: 11px;
		}

		.name {
			display: inline;
			font-size: 16px;
			line-height: 24px;
			font-weight: 800;
		}

		@media print {
			.btn {
				display: none;
			}
		}
	</style>
	
			<table border='1' align='center' rules='all' cellpadding='5px' width='380px'>
				<thead>
					<tr>
						<td colspan='5' align='center' class='head'>
							<img align='left' src='images/logo.png' height='90px'>
							<span style='font-size:18px;font-weight:600;'> <?php echo $full_name; ?> </span><br>
							(Affiliated to CBSE, New Delhi upto 10+2) <br>
							<b>Affiliation No. : <?php echo $aff_no; ?> School Code : <?php echo $school_code; ?></b><br>
							<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
							<!--Contact No.: <?php echo $inst_contact; ?> -->
							<!--<br> Email : <?php echo $inst_email; ?>, Website : <?php echo $inst_url; ?>-->
							<br> ✉️<?php echo $inst_email; ?> 🌐<?php echo $inst_url; ?>
						</td>
					</tr>
					<tr bgcolor='#f5f5f5'>
						<td colspan='3'> Receipt No. : <b><?php echo $id; ?> </b></td>
						<td align='right' colspan='2'><?php echo date('d-M-Y h:i:s', strtotime($receipt['created_at'])); ?></td>
					</tr>
					<tr>
						<td colspan='5' style='text-align:center'>
							<b> Parent's Copy : Registration Fee Receipt</b>
						</td>
					</tr>
				</thead>
				<tbody>
				    <tr>
						<td> Application No. </td>
						<td colspan='4'><?php echo strtoupper($receipt['app_no']); ?></td>
					</tr>
					<tr>
						<td> Name </td>
						<td colspan='4'><?php echo strtoupper($receipt['student_name']); ?></td>
					</tr>
					<tr>
						<td> Father's Name </td>
						<td colspan='4'><?php echo strtoupper($receipt['student_father']); ?></td>
					</tr>
					<tr>
						<td> Address </td>
						<td colspan='4'><?php echo strtoupper($receipt['student_address']); ?></td>
					</tr>
					<tr>
						<td> Registration for Class </td>
						<td colspan='3'><?php echo $receipt['student_class']; ?></td>
					</tr>
					<tr>
						<td colspan='5' height='140px' valign='top'>
							<table width='100%' border='0' rules='none'>

								<tr>
									<td> Registration Fee </td>
									<td align='right'><?php echo $receipt['pay_amount']; ?></td>
								</tr>

							</table>
						</td>
					</tr>
					<?php if ($receipt['remarks'] != '') { ?>
						<tr>
							<td colspan='5' align='center'>Remarks: <?php echo $receipt['remarks']; ?>
							<?php } ?>
							</td>
						</tr>



						<tr>
							<td colspan='5' style='text-transform:capitalize'>
								 <?php echo amount_in_word($receipt['pay_amount']); ?>
							</td>
						</tr>
						
						<tr>
							<td colspan='5'>
								Note:  This is automatically generated receipt hence signature not required.
							</td>
						</tr>

						<tr>
							<td colspan='3'> Issued by : <?php echo get_data('user', get_data('enquiry', $id, 'created_by')['data'], 'user_name')['data']; ?>
							</td>
							<td colspan='2' align='right'>
								Authorised Signatory
							</td>
						</tr>


			</table>
	
<?php } ?>