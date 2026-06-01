<?php require_once("required/function.php");	?>
<title> <?php echo $inst_name; ?></title>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

	body {
		color: #000;
		margin: 10px;
		padding: 0px;
		font-family: 'Roboto', sans-serif;
	}

	td {
		font-size: 16px;
		padding: 10px;
		line-height: 30px;
		font-weight: 800;
		padding-left: 10px;
	}

	.header {
		letter-spacing: 2px;
	}

	.idcard {
		width: 900px;
		border: solid 0px #ddd;
		height: 600px;
		background: url('assets/img/idcard_back.png') no-repeat;
		background-size: 345px 202px;
		margin: 4px 15px;
		float: left;
		page-break-before: always;
		position: relative;
	}

	.adm {
		position: absolute;
		width: 70px;
		height: 15px;
		top: 165px;
		left: 10px;
		z-index: 0;
		border: solid 0px #000;
		border-radius: 2px;
		background: #fff;
		text-align: center;
		color: maroon;
		font-weight: 600;
		font-size: 12px;
		opacity: 0.75;
	}

	.session {
		position: absolute;
		width: 45px;
		height: 10px;
		margin: auto;
		top: 32px;
		right: 3px;
		z-index: 0;
		border: solid 0px #000;
		padding: 2px 6px;
	}

	.content {
		margin-left: 100px;
		font-size: 22px;
		text-align: justified;
		line-height: 40px;
	}

	.cc {
		font-family: Old English Text MT;
		font-size: 32px;
		color: maroon;
	}

	.fdata {
		border-bottom: dotted 1px #313140;
		width: 680px;
		float: right;
		text-align: left;
		font-weight: 800;
		color: #222;
		padding-left: 10px;
	}

	@media print {
		#printbtn {
			display: none;
		}

		.idcard {
			page-break-inside: avoid;
		}

		@page {
			size: portrait;
		}
	}
</style>
<?php
extract($_REQUEST);
$res = get_data('student', $student_id);

if ($res['count'] > 0) {
	$student = $res['data'];
	$idata = array('created_at' => date('Y-m-d h:i:s'), 'student_admission' => $student['student_admission'], 'student_name' => $student['student_name'], 'student_class' => $student['student_class'], 'session_year' => $current_session, 'issue_date' => date('Y-m-d'));
	$ires = insert_data('tbl_cc', $idata);
	$student_admission = $student['student_admission'];
?>
	<table border='0' class='idcard' rules='none'>
		<tr height='60px'>
			<td colspan='4' align='center' class='header'>
				<img src='images/logo.png' height='140px' align='left'>
				<span style='font-size:36px;font-weight:800;font-family:calibri;text-transform:uppercase;color:maroon;'> <?php echo $full_name; ?> </span><br>
				<b>(Based on CBSE Pattern) <br>
					<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
					Contact No.: <?php echo $inst_contact; ?><br>
					Email: <?php echo $inst_email; ?>, Website: <?php echo $inst_url; ?>
			</td>
		</tr>
		<tr height='60px' valign='top'>
			<td> Sl. No. : <?php echo get_data('tbl_cc', $student_admission, 'id', 'student_admission')['data']; ?></td>
			<td align='right'> Adm. No. : <?php echo $student['student_admission']; ?> </td>
		</tr>
		<tr height='60px'>
			<td bgcolor='#ddd' align='center' colspan='2' valign='middle' class='cc'> Character Certificate </td>
		</tr>
		<tr>
			<td colspan='2' class='content' valign='top'>

				<span style='float:left'> This is to certify that Mr./Miss</span>
				<div class='fdata' style='width:560'> <?php echo $student['student_name']; ?> of Class <?php echo $student['student_class']; ?> </div>
				<span style='float:left'>Son/Daughter of Mr. </span>
				<div class='fdata' style='width:620px'> <?php echo $student['student_father']; ?> </u> </div><span style='float:left'> has been a bonafied student of this school during the session <?php echo $_GET['session_year']; ?> and was declared pass. He/She bear a good moral character. I wish success in his/her future career. </span>

			</td>
		</tr>
		<tr valign='bottom' height='100px'>
			<td> Date : <?php echo date('d-M-Y'); ?> </td>
			<td align='right'> Principal <br> <span style='padding-right:15px'> Seal </span></td>
		</tr>
	</table>

<?php } ?>