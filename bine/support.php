<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table = 'book_cat';
if (isset($_GET['link']) and $_GET['link'] != '') {
	$data = decode($_GET['link']);
	$id = $data['id'];
} else {
	$fee = insert_row($table);
	$id = $fee['id'];
}

if ($id != '') {
	$res = get_data($table, $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Support </h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item active">Support </li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title"> We are ready to help you ! </h3>

				<div class="box-tools pull-right">
					<!--<a class='fa fa-plus btn btn-info btn-sm' href='book_cat' title='Add Fee Head'> </a>-->

				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">


				<div class='col-md-4 p-4 bg-warning text-center float-left'>
					<img src='images/tawk.png' height='100px' align='left'>
					<h3>LIVE CHAT </h2>
						9:00 AM - 6:00 PM
					</h3>
					<a href='#' class='btn btn-success btn-sm m-2'> Click on Send Message </a>
				</div>

				<div class='col-md-4 p-4 bg-warning text-center float-left align-middle '>
					<img src='images/anydesk.png' height='100px' align='left' valign='middle'>
					<h3>Remote Login </h2>
						10:00 AM - 5:00 PM
						<a href='https://download.anydesk.com/AnyDesk.exe' class='btn btn-success btn-sm m-2'> Click to Downalod </a>
					</h3>
				</div>

				<div class='col-md-4 p-4 bg-warning text-center float-left align-middle '>
					<img src='images/whatsapp.png' height='100px' align='left' valign='middle'>
					<h3>WhatsApp </h2>
						10:00 AM - 5:00 PM
						<a href='https://wa.me/919431426600?text=Issue+form+<?php echo $inst_name; ?>' class='btn btn-success btn-sm m-2'> Click to open </a>
					</h3>
				</div>

			</div>
		</div>
	</section>
</div>



<?php require_once('required/footer.php'); ?>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
	var Tawk_API = Tawk_API || {},
		Tawk_LoadStart = new Date();
	(function() {
		var s1 = document.createElement("script"),
			s0 = document.getElementsByTagName("script")[0];
		s1.async = true;
		s1.src = 'https://embed.tawk.to/597b6a4f5dfc8255d623f713/1bm6lrj4q';
		s1.charset = 'UTF-8';
		s1.setAttribute('crossorigin', '*');
		s0.parentNode.insertBefore(s1, s0);
	})();
</script>
<!--End of Tawk.to Script-->